<?php
declare(strict_types=1);

/**
 * Nextlab Yönetim Paneli — çekirdek.
 *
 * Yayın modeli: her satırda iki sürüm bulunur.
 *   data_json  = YAYINDAKİ sürüm (NULL ise kayıt henüz yayınlanmamış)
 *   draft_json = panelde düzenlenen çalışma sürümü
 * "Yayına Al" => data_json = draft_json. Public site yalnızca data_json okur.
 */
final class Admin
{
    public PDO $pdo;
    private array $config;
    private ?array $user = null;

    public function __construct(array $config)
    {
        $this->config = $config;
        if (($config['app']['data_mode'] ?? 'file') !== 'mysql' || empty($config['database'])) {
            throw new RuntimeException('Yönetim paneli için config.php içinde data_mode = "mysql" olmalıdır.');
        }
        $db = $config['database'];
        $this->pdo = new PDO(
            'mysql:host=' . $db['host'] . ';port=' . (int)($db['port'] ?? 3306) . ';dbname=' . $db['name'] . ';charset=utf8mb4',
            $db['user'], $db['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );
        $this->startSession();
    }

    // ---------------------------------------------------------------- oturum
    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) return;
        $https = (($_SERVER['HTTPS'] ?? '') !== '' && ($_SERVER['HTTPS'] ?? '') !== 'off');
        session_set_cookie_params([
            'lifetime' => 0, 'path' => '/', 'httponly' => true,
            'secure' => $https, 'samesite' => 'Lax',
        ]);
        session_name('nextlab_admin');
        session_start();

        // Boşta kalan oturumu kapat (8 saat).
        $idleLimit = 8 * 3600;
        if (!empty($_SESSION['admin_id'])) {
            if (isset($_SESSION['seen']) && (time() - (int)$_SESSION['seen']) > $idleLimit) {
                $_SESSION = [];
                session_destroy();
                session_start();
            } else {
                $_SESSION['seen'] = time();
            }
        }
    }

    public function csrfToken(): string
    {
        if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf'];
    }

    public function checkCsrf(): void
    {
        $sent = (string)($_POST['_csrf'] ?? '');
        if (!hash_equals($_SESSION['csrf'] ?? '', $sent)) {
            http_response_code(419);
            exit('Oturum doğrulaması başarısız. Sayfayı yenileyip tekrar deneyin.');
        }
    }

    // ------------------------------------------------------------- kimlik
    public function adminCount(): int
    {
        return (int)$this->pdo->query('SELECT COUNT(*) FROM nextlab_admins')->fetchColumn();
    }

    public function user(): ?array
    {
        if ($this->user !== null) return $this->user;
        $id = (int)($_SESSION['admin_id'] ?? 0);
        if (!$id) return null;
        $st = $this->pdo->prepare('SELECT id, email, name FROM nextlab_admins WHERE id = ?');
        $st->execute([$id]);
        return $this->user = ($st->fetch() ?: null);
    }

    public function requireLogin(): array
    {
        $u = $this->user();
        if (!$u) { header('Location: /admin/giris'); exit; }
        return $u;
    }

    public function createAdmin(string $email, string $name, string $password): void
    {
        $st = $this->pdo->prepare('INSERT INTO nextlab_admins (email, name, password_hash) VALUES (?, ?, ?)');
        $st->execute([$email, $name, password_hash($password, PASSWORD_DEFAULT)]);
    }

    /** @return string|null Hata mesajı; başarıda null. */
    public function login(string $email, string $password): ?string
    {
        $st = $this->pdo->prepare('SELECT * FROM nextlab_admins WHERE email = ?');
        $st->execute([$email]);
        $row = $st->fetch();
        if (!$row) return 'E-posta veya parola hatalı.';
        if ($row['locked_until'] && strtotime($row['locked_until']) > time()) {
            return 'Çok fazla hatalı deneme. Lütfen birkaç dakika sonra tekrar deneyin.';
        }
        if (!password_verify($password, $row['password_hash'])) {
            $attempts = (int)$row['failed_attempts'] + 1;
            $lock = $attempts >= 5 ? date('Y-m-d H:i:s', time() + 600) : null;
            $this->pdo->prepare('UPDATE nextlab_admins SET failed_attempts = ?, locked_until = ? WHERE id = ?')
                ->execute([$attempts, $lock, $row['id']]);
            return 'E-posta veya parola hatalı.';
        }
        $this->pdo->prepare('UPDATE nextlab_admins SET failed_attempts = 0, locked_until = NULL, last_login_at = NOW() WHERE id = ?')
            ->execute([$row['id']]);
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int)$row['id'];
        return null;
    }

    /** @return string|null Hata; başarıda null. */
    public function updateProfile(int $id, string $name, string $email): ?string
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return 'Geçerli bir e-posta adresi girin.';
        if (trim($name) === '') return 'Ad boş olamaz.';
        $st = $this->pdo->prepare('SELECT id FROM nextlab_admins WHERE email = ? AND id <> ?');
        $st->execute([$email, $id]);
        if ($st->fetch()) return 'Bu e-posta başka bir hesapta kullanılıyor.';
        $this->pdo->prepare('UPDATE nextlab_admins SET name = ?, email = ? WHERE id = ?')
            ->execute([trim($name), $email, $id]);
        $this->user = null;
        return null;
    }

    /** @return string|null Hata; başarıda null. */
    public function changePassword(int $id, string $current, string $new, string $again): ?string
    {
        if (strlen($new) < 10) return 'Yeni parola en az 10 karakter olmalıdır.';
        if ($new !== $again) return 'Yeni parolalar birbiriyle eşleşmiyor.';
        $st = $this->pdo->prepare('SELECT password_hash FROM nextlab_admins WHERE id = ?');
        $st->execute([$id]);
        $hash = (string)$st->fetchColumn();
        if (!$hash || !password_verify($current, $hash)) return 'Mevcut parolanız hatalı.';
        $this->pdo->prepare('UPDATE nextlab_admins SET password_hash = ? WHERE id = ?')
            ->execute([password_hash($new, PASSWORD_DEFAULT), $id]);
        return null;
    }

    public function listAdmins(): array
    {
        return $this->pdo->query('SELECT id, name, email, last_login_at, created_at FROM nextlab_admins ORDER BY id')->fetchAll();
    }

    /** @return string|null Hata; başarıda null. */
    public function addAdmin(string $name, string $email, string $pass): ?string
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return 'Geçerli bir e-posta adresi girin.';
        if (trim($name) === '') return 'Ad boş olamaz.';
        if (strlen($pass) < 10) return 'Parola en az 10 karakter olmalıdır.';
        $st = $this->pdo->prepare('SELECT id FROM nextlab_admins WHERE email = ?');
        $st->execute([$email]);
        if ($st->fetch()) return 'Bu e-posta zaten kayıtlı.';
        $this->createAdmin($email, trim($name), $pass);
        return null;
    }

    /** @return string|null Hata; başarıda null. */
    public function removeAdmin(int $id, int $currentUserId): ?string
    {
        if ($id === $currentUserId) return 'Kendi hesabınızı silemezsiniz.';
        if ($this->adminCount() <= 1) return 'Son yönetici hesabı silinemez.';
        $this->pdo->prepare('DELETE FROM nextlab_admins WHERE id = ?')->execute([$id]);
        return null;
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    // -------------------------------------------------- varlık tanımları
    /** Panelde yönetilen tüm içerik tipleri ve alanları. */
    public static function entities(): array
    {
        return [
            'urunler' => [
                'label' => 'Ürünler', 'singular' => 'Ürün', 'icon' => 'box', 'public' => 'urun/%s',
                'table' => 'nextlab_products', 'kind' => null,
                'title_field' => 'name',
                'list_columns' => [['name', 'Ürün'], ['brand_name', 'Marka'], ['category_name', 'Kategori']],
                'fields' => [
                    ['key' => 'name', 'label' => 'Ürün Adı', 'type' => 'text', 'required' => true],
                    ['key' => 'slug', 'label' => 'URL (slug)', 'type' => 'slug', 'from' => 'name', 'required' => true, 'help' => 'Adres satırında görünür: /urun/<slug>'],
                    ['key' => 'brand_name', 'label' => 'Marka', 'type' => 'text'],
                    ['key' => 'category_id', 'label' => 'Kategori', 'type' => 'select', 'options' => 'categories', 'required' => true],
                    ['key' => 'sku', 'label' => 'Ürün / Katalog Kodu', 'type' => 'text'],
                    ['key' => 'internal_sku', 'label' => 'Kod dahilî katalog kodudur', 'type' => 'bool'],
                    ['key' => 'image_path', 'label' => 'Ürün Görseli', 'type' => 'image'],
                    ['key' => 'short_description', 'label' => 'Kısa Açıklama', 'type' => 'textarea', 'rows' => 2, 'help' => 'Kartlarda ve meta açıklamada kullanılır (SEO).'],
                    ['key' => 'description', 'label' => 'Açıklama', 'type' => 'textarea', 'rows' => 4],
                    ['key' => 'usage', 'label' => 'Kullanım Bilgisi', 'type' => 'textarea', 'rows' => 3],
                    ['key' => 'package_info', 'label' => 'Ambalaj Bilgisi', 'type' => 'text'],
                    ['key' => 'is_featured', 'label' => 'Ana sayfada öne çıkar', 'type' => 'bool'],
                    ['key' => 'marketplace_url', 'label' => 'Pazaryeri Bağlantısı', 'type' => 'url'],
                    ['key' => 'marketplace_name', 'label' => 'Pazaryeri Buton Metni', 'type' => 'text'],
                    ['key' => 'source_url', 'label' => 'Ürün Bilgi Kaynağı (opsiyonel)', 'type' => 'url'],
                ],
            ],
            'kategoriler' => [
                'label' => 'Kategoriler', 'singular' => 'Kategori', 'icon' => 'folder', 'public' => 'kategori/%s',
                'table' => 'nextlab_categories', 'kind' => null,
                'title_field' => 'name',
                'list_columns' => [['name', 'Kategori'], ['slug', 'URL']],
                'fields' => [
                    ['key' => 'name', 'label' => 'Kategori Adı', 'type' => 'text', 'required' => true],
                    ['key' => 'slug', 'label' => 'URL (slug)', 'type' => 'slug', 'from' => 'name', 'required' => true],
                    ['key' => 'parent_id', 'label' => 'Üst Kategori', 'type' => 'select', 'options' => 'categories', 'allow_empty' => 'Üst kategori yok (ana kategori)'],
                    ['key' => 'description', 'label' => 'Açıklama', 'type' => 'textarea', 'rows' => 2, 'help' => 'Kategori sayfasının meta açıklaması (SEO).'],
                    ['key' => 'image_path', 'label' => 'Kategori Görseli', 'type' => 'image'],
                ],
            ],
            'bloglar' => [
                'label' => 'Blog Yazıları', 'singular' => 'Blog Yazısı', 'icon' => 'doc', 'public' => 'blog/%s',
                'table' => 'nextlab_content', 'kind' => 'blogs',
                'title_field' => 'title',
                'list_columns' => [['title', 'Başlık'], ['category', 'Kategori'], ['publishedAt', 'Tarih']],
                'fields' => [
                    ['key' => 'title', 'label' => 'Başlık', 'type' => 'text', 'required' => true],
                    ['key' => 'slug', 'label' => 'URL (slug)', 'type' => 'slug', 'from' => 'title', 'required' => true],
                    ['key' => 'subtitle', 'label' => 'Alt Başlık', 'type' => 'textarea', 'rows' => 2],
                    ['key' => 'excerpt', 'label' => 'Özet (liste ve SEO)', 'type' => 'textarea', 'rows' => 2],
                    ['key' => 'category', 'label' => 'Kategori Adı', 'type' => 'text'],
                    ['key' => 'categorySlug', 'label' => 'Kategori URL', 'type' => 'slug', 'from' => 'category'],
                    ['key' => 'badge', 'label' => 'Rozet', 'type' => 'text'],
                    ['key' => 'readTime', 'label' => 'Okuma Süresi', 'type' => 'text', 'placeholder' => '3 dk okuma'],
                    ['key' => 'isoDate', 'label' => 'Yayın Tarihi', 'type' => 'date', 'help' => 'Sayfada görünen tarih ve arama motoru tarihi bu alandan üretilir.'],
                    ['key' => 'image_path', 'label' => 'Kapak Görseli', 'type' => 'image'],
                    ['key' => 'image_alt', 'label' => 'Görsel Alt Metni (SEO)', 'type' => 'text'],
                    ['key' => 'author.name', 'label' => 'Yazar Adı', 'type' => 'text'],
                    ['key' => 'author.role', 'label' => 'Yazar Ünvanı', 'type' => 'text'],
                    ['key' => 'author.initials', 'label' => 'Yazar Baş Harfleri', 'type' => 'text'],
                    ['key' => 'highlightQuote', 'label' => 'Öne Çıkan Alıntı', 'type' => 'textarea', 'rows' => 2],
                    ['key' => 'summaryPoints', 'label' => 'Özet Maddeleri', 'type' => 'list', 'help' => 'Her satır bir madde.'],
                    ['key' => 'keywords', 'label' => 'Anahtar Kelimeler', 'type' => 'kvlist', 'help' => 'Her satır: Etiket|/hedef-adres'],
                    ['key' => 'contentHtml', 'label' => 'Yazı İçeriği', 'type' => 'richtext', 'required' => true],
                ],
            ],
            'bannerlar' => [
                'label' => 'Ana Sayfa Banner', 'singular' => 'Banner', 'icon' => 'image',
                'table' => 'nextlab_content', 'kind' => 'banners',
                'title_field' => 'title',
                'list_columns' => [['title', 'Başlık'], ['badge', 'Rozet']],
                'fields' => [
                    ['key' => 'badge', 'label' => 'Üst Etiket', 'type' => 'text'],
                    ['key' => 'title', 'label' => 'Başlık', 'type' => 'text', 'required' => true],
                    ['key' => 'subtitle', 'label' => 'Açıklama', 'type' => 'textarea', 'rows' => 2],
                    ['key' => 'button_text', 'label' => 'Buton Metni', 'type' => 'text'],
                    ['key' => 'button_url', 'label' => 'Buton Adresi', 'type' => 'text', 'placeholder' => 'urunler'],
                    ['key' => 'secondary_text', 'label' => 'İkincil Bağlantı Metni', 'type' => 'text'],
                    ['key' => 'secondary_url', 'label' => 'İkincil Bağlantı Adresi', 'type' => 'url'],
                    ['key' => 'image_path', 'label' => 'Banner Görseli', 'type' => 'image', 'help' => 'Boş bırakılırsa ürün kartları gösterilir.'],
                    ['key' => 'theme', 'label' => 'Tema Anahtarı', 'type' => 'text'],
                    ['key' => 'product_ids', 'label' => 'Gösterilecek Ürün ID\'leri', 'type' => 'idlist', 'help' => 'Virgülle ayırın. Görsel boşsa kullanılır.'],
                ],
            ],
            'sss' => [
                'label' => 'Sıkça Sorulan Sorular', 'singular' => 'Soru', 'icon' => 'help',
                'table' => 'nextlab_content', 'kind' => 'faqs',
                'title_field' => 'question',
                'list_columns' => [['question', 'Soru'], ['category', 'Kategori']],
                'fields' => [
                    ['key' => 'category', 'label' => 'Kategori', 'type' => 'text'],
                    ['key' => 'question', 'label' => 'Soru', 'type' => 'text', 'required' => true],
                    ['key' => 'answer', 'label' => 'Cevap', 'type' => 'textarea', 'rows' => 4, 'required' => true],
                ],
            ],
            'markalar' => [
                'label' => 'Markalar', 'singular' => 'Marka', 'icon' => 'tag', 'public' => 'distributorluk/%s',
                'table' => 'nextlab_content', 'kind' => 'distributorBrands',
                'title_field' => 'name',
                'list_columns' => [['name', 'Marka'], ['category', 'Kategori'], ['origin', 'Menşe']],
                'fields' => [
                    ['key' => 'name', 'label' => 'Marka Adı', 'type' => 'text', 'required' => true],
                    ['key' => 'slug', 'label' => 'URL (slug)', 'type' => 'slug', 'from' => 'name', 'required' => true],
                    ['key' => 'origin', 'label' => 'Menşe', 'type' => 'text'],
                    ['key' => 'category', 'label' => 'Kategori', 'type' => 'text'],
                    ['key' => 'highlight', 'label' => 'Öne Çıkan Özellik', 'type' => 'text'],
                    ['key' => 'description', 'label' => 'Açıklama', 'type' => 'textarea', 'rows' => 3],
                    ['key' => 'badges', 'label' => 'Rozetler', 'type' => 'list'],
                    ['key' => 'key_products', 'label' => 'Öne Çıkan Ürünler', 'type' => 'list'],
                    ['key' => 'verified', 'label' => 'Doğrulanmış marka', 'type' => 'bool'],
                ],
            ],
            'kanallar' => [
                'label' => 'Pazaryeri Mağazaları', 'singular' => 'Pazaryeri', 'icon' => 'cart',
                'table' => 'nextlab_content', 'kind' => 'channels', 'single' => true,
                'title_field' => null,
                'list_columns' => [],
                'fields' => [
                    ['key' => 'trendyol', 'label' => 'Trendyol Mağaza Adresi', 'type' => 'url'],
                    ['key' => 'hepsiburada', 'label' => 'Hepsiburada Mağaza Adresi', 'type' => 'url'],
                ],
            ],
            'ayarlar' => [
                'label' => 'Site Ayarları', 'singular' => 'Ayarlar', 'icon' => 'cog',
                'table' => 'nextlab_content', 'kind' => 'settings', 'single' => true,
                'title_field' => null,
                'list_columns' => [],
                'fields' => [
                    ['key' => 'social.facebook', 'label' => 'Facebook Adresi', 'type' => 'url', 'group' => 'Sosyal Medya'],
                    ['key' => 'social.instagram', 'label' => 'Instagram Adresi', 'type' => 'url', 'group' => 'Sosyal Medya'],
                    ['key' => 'social.linkedin', 'label' => 'LinkedIn Adresi', 'type' => 'url', 'group' => 'Sosyal Medya'],
                    ['key' => 'contact.address', 'label' => 'Adres', 'type' => 'textarea', 'rows' => 2, 'group' => 'İletişim'],
                    ['key' => 'contact.email', 'label' => 'E-posta', 'type' => 'text', 'group' => 'İletişim'],
                    ['key' => 'contact.map_query', 'label' => 'Harita Arama Metni', 'type' => 'text', 'group' => 'İletişim', 'help' => 'Google Haritalar bu metinle konumu bulur.'],
                    ['key' => 'about.eyebrow', 'label' => 'Üst Etiket', 'type' => 'text', 'group' => 'Kurumsal Sayfa'],
                    ['key' => 'about.title', 'label' => 'Başlık', 'type' => 'text', 'group' => 'Kurumsal Sayfa'],
                    ['key' => 'about.paragraphs', 'label' => 'Paragraflar', 'type' => 'list', 'group' => 'Kurumsal Sayfa', 'help' => 'Her satır bir paragraf.'],
                    ['key' => 'seo.site_name', 'label' => 'Site Adı', 'type' => 'text', 'group' => 'SEO'],
                    ['key' => 'seo.default_description', 'label' => 'Varsayılan Meta Açıklama', 'type' => 'textarea', 'rows' => 2, 'group' => 'SEO'],
                ],
            ],
        ];
    }

    public static function entity(string $key): ?array
    {
        return self::entities()[$key] ?? null;
    }

    // ------------------------------------------------------ veri erişimi
    /** Taslak kayıtları listeler (panelde görünen hâli). */
    public function all(array $e): array
    {
        if ($e['kind'] === null) {
            $sql = "SELECT id, slug, data_json, draft_json, updated_at FROM {$e['table']} ORDER BY sort_order, id";
            $rows = $this->pdo->query($sql)->fetchAll();
        } else {
            $st = $this->pdo->prepare("SELECT id, slug, data_json, draft_json, updated_at FROM {$e['table']} WHERE kind = ? ORDER BY sort_order, id");
            $st->execute([$e['kind']]);
            $rows = $st->fetchAll();
        }
        foreach ($rows as &$r) {
            $r['data'] = json_decode($r['draft_json'] ?: ($r['data_json'] ?? '{}'), true) ?: [];
            $r['status'] = self::statusOf($r);
        }
        return $rows;
    }

    /**
     * Kayıt gerçekten bu varlığa mı ait? nextlab_content tablosunu birden çok
     * varlık (blog, SSS, banner, marka, kanal, ayarlar) paylaşır; bu yüzden
     * id ile yapılan HER işlem 'kind' ile sınırlandırılmalıdır.
     * @return array{0:string,1:array}
     */
    private function scope(array $e): array
    {
        return $e['kind'] === null ? ['', []] : [' AND kind = ?', [$e['kind']]];
    }

    public function find(array $e, int $id): ?array
    {
        [$w, $p] = $this->scope($e);
        $st = $this->pdo->prepare("SELECT id, slug, data_json, draft_json, updated_at FROM {$e['table']} WHERE id = ?{$w}");
        $st->execute([$id, ...$p]);
        $r = $st->fetch();
        if (!$r) return null;
        $r['data'] = json_decode($r['draft_json'] ?: ($r['data_json'] ?? '{}'), true) ?: [];
        $r['status'] = self::statusOf($r);
        return $r;
    }

    /** Tekil (single) varlıklar için ilk satırı döndürür, yoksa oluşturur. */
    public function single(array $e): array
    {
        $st = $this->pdo->prepare("SELECT id, slug, data_json, draft_json FROM {$e['table']} WHERE kind = ? LIMIT 1");
        $st->execute([$e['kind']]);
        $r = $st->fetch();
        if (!$r) {
            $ins = $this->pdo->prepare("INSERT INTO {$e['table']} (kind, slug, sort_order, data_json, draft_json) VALUES (?,?,0,NULL,'{}')");
            $ins->execute([$e['kind'], $e['kind']]);
            return $this->single($e);
        }
        $r['data'] = json_decode($r['draft_json'] ?: ($r['data_json'] ?? '{}'), true) ?: [];
        $r['status'] = self::statusOf($r);
        return $r;
    }

    public static function statusOf(array $row): string
    {
        if (($row['data_json'] ?? null) === null) return 'draft';            // hiç yayınlanmamış
        if (($row['draft_json'] ?? '') !== ($row['data_json'] ?? '')) return 'modified'; // yayında + bekleyen değişiklik
        return 'published';
    }

    /** Yayınlanmayı bekleyen değişiklik sayısı. */
    public function pendingCount(): int
    {
        $n = 0;
        foreach (['nextlab_products', 'nextlab_categories', 'nextlab_content'] as $t) {
            $n += (int)$this->pdo->query(
                "SELECT COUNT(*) FROM {$t} WHERE data_json IS NULL OR draft_json <> data_json"
            )->fetchColumn();
        }
        return $n;
    }

    /** Taslağı kaydeder. Yeni kayıtta id döner. */
    public function saveDraft(array $e, ?int $id, array $data, string $slug): int
    {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($id) {
            $data['id'] = $id;
            $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($e['kind'] === null) {
                $extra = $e['table'] === 'nextlab_products' ? ', category_id = ' . (int)($data['category_id'] ?? 0) : '';
                $st = $this->pdo->prepare("UPDATE {$e['table']} SET slug = ?, draft_json = ?{$extra} WHERE id = ?");
                $st->execute([$slug, $json, $id]);
            } else {
                $st = $this->pdo->prepare("UPDATE {$e['table']} SET slug = ?, draft_json = ? WHERE id = ? AND kind = ?");
                $st->execute([$slug, $json, $id, $e['kind']]);
            }
            return $id;
        }
        // yeni kayıt
        if ($e['kind'] === null) {
            $order = (int)$this->pdo->query("SELECT COALESCE(MAX(sort_order),0)+1 FROM {$e['table']}")->fetchColumn();
        } else {
            $st = $this->pdo->prepare("SELECT COALESCE(MAX(sort_order),0)+1 FROM {$e['table']} WHERE kind = ?");
            $st->execute([$e['kind']]);
            $order = (int)$st->fetchColumn();
        }
        if ($e['kind'] === null) {
            $cat = $e['table'] === 'nextlab_products' ? (int)($data['category_id'] ?? 0) : null;
            if ($e['table'] === 'nextlab_products') {
                $st = $this->pdo->prepare("INSERT INTO {$e['table']} (slug, category_id, sort_order, data_json, draft_json) VALUES (?,?,?,NULL,?)");
                $st->execute([$slug, $cat, $order, $json]);
            } else {
                $st = $this->pdo->prepare("INSERT INTO {$e['table']} (slug, sort_order, data_json, draft_json) VALUES (?,?,NULL,?)");
                $st->execute([$slug, $order, $json]);
            }
        } else {
            $st = $this->pdo->prepare("INSERT INTO {$e['table']} (kind, slug, sort_order, data_json, draft_json) VALUES (?,?,?,NULL,?)");
            $st->execute([$e['kind'], $slug, $order, $json]);
        }
        $newId = (int)$this->pdo->lastInsertId();
        // id alanını JSON'a da yaz
        $data['id'] = $newId;
        $this->pdo->prepare("UPDATE {$e['table']} SET draft_json = ? WHERE id = ?")
            ->execute([json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $newId]);
        return $newId;
    }

    public function publish(array $e, int $id): void
    {
        [$w, $p] = $this->scope($e);
        $this->pdo->prepare("UPDATE {$e['table']} SET data_json = draft_json WHERE id = ?{$w}")->execute([$id, ...$p]);
    }

    public function unpublish(array $e, int $id): void
    {
        [$w, $p] = $this->scope($e);
        $this->pdo->prepare("UPDATE {$e['table']} SET data_json = NULL WHERE id = ?{$w}")->execute([$id, ...$p]);
    }

    public function discardDraft(array $e, int $id): void
    {
        [$w, $p] = $this->scope($e);
        $this->pdo->prepare("UPDATE {$e['table']} SET draft_json = data_json WHERE id = ? AND data_json IS NOT NULL{$w}")->execute([$id, ...$p]);
    }

    public function delete(array $e, int $id): void
    {
        // Tekil varlıklar (ayarlar, kanallar) silinemez — site onlara bağımlıdır.
        if (!empty($e['single'])) return;
        [$w, $p] = $this->scope($e);
        $this->pdo->prepare("DELETE FROM {$e['table']} WHERE id = ?{$w}")->execute([$id, ...$p]);
    }

    /** Tüm bekleyen değişiklikleri tek seferde yayına alır. */
    public function publishAll(): int
    {
        $n = 0;
        foreach (['nextlab_products', 'nextlab_categories', 'nextlab_content'] as $t) {
            $st = $this->pdo->prepare("UPDATE {$t} SET data_json = draft_json WHERE data_json IS NULL OR draft_json <> data_json");
            $st->execute();
            $n += $st->rowCount();
        }
        return $n;
    }

    /**
     * Slug değişince eski adresi yenisine bağlar (301).
     * Zincir ve döngü oluşmasını engeller.
     */
    public function recordRedirect(string $from, string $to): void
    {
        if ($from === '' || $to === '' || $from === $to) return;
        try {
            // Eskiden bu adrese işaret edenleri doğrudan yeni hedefe çevir (zincir olmasın).
            $this->pdo->prepare('UPDATE nextlab_redirects SET to_path = ? WHERE to_path = ?')->execute([$to, $from]);
            // Yeni adres bir kaynak olarak duruyorsa kaldır (döngü olmasın).
            $this->pdo->prepare('DELETE FROM nextlab_redirects WHERE from_path = ?')->execute([$to]);
            $this->pdo->prepare('INSERT INTO nextlab_redirects (from_path, to_path) VALUES (?,?)
                                 ON DUPLICATE KEY UPDATE to_path = VALUES(to_path)')->execute([$from, $to]);
        } catch (Throwable) {
            // Yönlendirme tablosu yoksa (eski kurulum) kayıt sessizce atlanır.
        }
    }

    /** Kaydı listede bir sıra yukarı/aşağı taşır. */
    public function move(array $e, int $id, string $dir): void
    {
        $rows = $this->all($e);
        $ids = array_map(fn($r) => (int)$r['id'], $rows);
        $i = array_search($id, $ids, true);
        if ($i === false) return;
        $j = $dir === 'up' ? $i - 1 : $i + 1;
        if ($j < 0 || $j >= count($ids)) return;
        [$ids[$i], $ids[$j]] = [$ids[$j], $ids[$i]];
        $this->reorder($e, $ids);
    }

    public function reorder(array $e, array $idsInOrder): void
    {
        $st = $this->pdo->prepare("UPDATE {$e['table']} SET sort_order = ? WHERE id = ?");
        foreach (array_values($idsInOrder) as $i => $id) $st->execute([$i, (int)$id]);
    }

    // ------------------------------------------------------------ yardımcı
    /** Kategoriye bağlı ürün ve alt kategori sayısı. @return array{products:int,children:int} */
    public function categoryUsage(int $id): array
    {
        $st = $this->pdo->prepare('SELECT COUNT(*) FROM nextlab_products WHERE category_id = ?');
        $st->execute([$id]);
        $products = (int)$st->fetchColumn();

        $children = 0;
        foreach ($this->pdo->query('SELECT draft_json FROM nextlab_categories')->fetchAll() as $r) {
            $d = json_decode($r['draft_json'], true) ?: [];
            if ((int)($d['parent_id'] ?? 0) === $id) $children++;
        }
        return ['products' => $products, 'children' => $children];
    }

    public function categoryOptions(): array
    {
        $rows = $this->pdo->query('SELECT id, draft_json FROM nextlab_categories ORDER BY sort_order, id')->fetchAll();
        $out = [];
        foreach ($rows as $r) {
            $d = json_decode($r['draft_json'], true) ?: [];
            $out[(int)$r['id']] = $d['name'] ?? ('#' . $r['id']);
        }
        return $out;
    }

    /** Kategori id'sinden ad/slug türetir (ürün kaydında tutarlılık için). */
    public function categoryMeta(int $id): array
    {
        $st = $this->pdo->prepare('SELECT draft_json FROM nextlab_categories WHERE id = ?');
        $st->execute([$id]);
        $d = json_decode((string)$st->fetchColumn(), true) ?: [];
        return ['name' => $d['name'] ?? '', 'slug' => $d['slug'] ?? ''];
    }

    public static function slugify(string $v): string
    {
        $v = strtolower(strtr($v, [
            'İ' => 'i', 'I' => 'i', 'ı' => 'i', 'Ş' => 's', 'ş' => 's', 'Ğ' => 'g', 'ğ' => 'g',
            'Ü' => 'u', 'ü' => 'u', 'Ö' => 'o', 'ö' => 'o', 'Ç' => 'c', 'ç' => 'c', 'â' => 'a', 'î' => 'i',
        ]));
        $v = preg_replace('/[^a-z0-9]+/', '-', $v) ?? '';
        return trim($v, '-') ?: 'kayit';
    }

    /** Benzersiz slug üretir. */
    public function uniqueSlug(array $e, string $slug, ?int $exceptId): string
    {
        $base = self::slugify($slug);
        $try = $base; $n = 2;
        while (true) {
            if ($e['kind'] === null) {
                $st = $this->pdo->prepare("SELECT id FROM {$e['table']} WHERE slug = ? AND id <> ?");
                $st->execute([$try, $exceptId ?? 0]);
            } else {
                $st = $this->pdo->prepare("SELECT id FROM {$e['table']} WHERE kind = ? AND slug = ? AND id <> ?");
                $st->execute([$e['kind'], $try, $exceptId ?? 0]);
            }
            if (!$st->fetch()) return $try;
            $try = $base . '-' . $n++;
        }
    }

    // ------------------------------------------------- iç içe alan erişimi
    public static function get(array $data, string $path, mixed $default = ''): mixed
    {
        $v = $data;
        foreach (explode('.', $path) as $k) {
            if (!is_array($v) || !array_key_exists($k, $v)) return $default;
            $v = $v[$k];
        }
        return $v;
    }

    public static function set(array &$data, string $path, mixed $value): void
    {
        $keys = explode('.', $path);
        $ref = &$data;
        foreach ($keys as $i => $k) {
            if ($i === count($keys) - 1) { $ref[$k] = $value; return; }
            if (!isset($ref[$k]) || !is_array($ref[$k])) $ref[$k] = [];
            $ref = &$ref[$k];
        }
    }

    // ------------------------------------------------------------- yedek
    /** Tüm içeriği (yayın + taslak) tek bir diziye çıkarır. */
    public function exportAll(): array
    {
        $dump = ['version' => 1, 'created_at' => date('c'), 'tables' => []];
        foreach (['nextlab_categories', 'nextlab_products', 'nextlab_content'] as $t) {
            $cols = $t === 'nextlab_products'
                ? 'id, slug, category_id, sort_order, data_json, draft_json'
                : ($t === 'nextlab_content'
                    ? 'id, kind, slug, sort_order, data_json, draft_json'
                    : 'id, slug, sort_order, data_json, draft_json');
            $dump['tables'][$t] = $this->pdo->query("SELECT {$cols} FROM {$t} ORDER BY id")->fetchAll();
        }
        return $dump;
    }

    /**
     * Yedeği geri yükler. Yöneticiler ve yönlendirmeler korunur.
     * @return string|null Hata; başarıda null.
     */
    public function importAll(array $dump): ?string
    {
        if (($dump['version'] ?? 0) !== 1 || !is_array($dump['tables'] ?? null)) {
            return 'Dosya tanınmadı. Lütfen bu panelden indirilmiş bir yedek dosyası seçin.';
        }
        $allowed = ['nextlab_categories', 'nextlab_products', 'nextlab_content'];
        foreach ($allowed as $t) {
            if (!isset($dump['tables'][$t]) || !is_array($dump['tables'][$t])) return "Yedek eksik: {$t}.";
        }
        // Boş yedekle içeriği silmeyi engelle.
        if (!$dump['tables']['nextlab_products'] && !$dump['tables']['nextlab_content']) {
            return 'Yedek boş görünüyor; güvenlik için geri yükleme yapılmadı.';
        }

        $this->pdo->beginTransaction();
        try {
            foreach ($allowed as $t) {
                $this->pdo->exec("DELETE FROM {$t}");
                foreach ($dump['tables'][$t] as $row) {
                    if (!is_array($row)) continue;
                    $cols = array_keys($row);
                    $ph = implode(',', array_fill(0, count($cols), '?'));
                    $sql = "INSERT INTO {$t} (`" . implode('`,`', $cols) . "`) VALUES ({$ph})";
                    $this->pdo->prepare($sql)->execute(array_values($row));
                }
            }
            $this->pdo->commit();
            return null;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            return 'Geri yükleme başarısız: ' . $e->getMessage();
        }
    }

    // ------------------------------------------------------------- görsel
    /** @return array{0:?string,1:?string,2:?string} [yol, hata, uyarı] */
    public function uploadImage(array $file): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return [null, null, null];
        if (($file['error'] ?? 1) !== UPLOAD_ERR_OK) return [null, 'Yükleme başarısız (kod ' . $file['error'] . ').', null];
        if (($file['size'] ?? 0) > 6 * 1024 * 1024) return [null, 'Dosya 6 MB sınırını aşıyor.', null];
        $info = @getimagesize($file['tmp_name']);
        if (!$info) return [null, 'Geçerli bir görsel dosyası değil.', null];
        $ext = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp', IMAGETYPE_GIF => 'gif'][$info[2]] ?? null;
        if (!$ext) return [null, 'Yalnızca JPG, PNG, WEBP veya GIF yükleyebilirsiniz.', null];
        $dir = dirname(__DIR__, 2) . '/assets/images/uploads';
        if (!is_dir($dir) && !@mkdir($dir, 0775, true)) return [null, 'Yükleme klasörü oluşturulamadı.', null];
        $name = date('Ymd') . '-' . bin2hex(random_bytes(6)) . '.' . $ext;
        if (!@move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) return [null, 'Dosya kaydedilemedi (klasör yazma izni?).', null];
        // Sunucuda görsel küçültme (GD/Imagick) olmayabilir; çok büyük görseller
        // sayfayı yavaşlatır. Kullanıcıyı bilgilendir.
        $warn = null;
        if ($info[0] > 2000) {
            $warn = 'Görsel ' . $info[0] . ' piksel genişliğinde. Sitenin hızlı açılması için '
                  . 'yüklemeden önce genişliği 1600 piksel altına düşürmeniz önerilir.';
        }
        return ['images/uploads/' . $name, null, $warn];
    }

    /** Yüklenen bir görseli siler. Yalnızca uploads klasörü içinde çalışır. */
    public function deleteUpload(string $name): bool
    {
        if (!preg_match('/^[A-Za-z0-9._-]+\.(jpe?g|png|webp|gif)$/i', $name)) return false;
        $dir = dirname(__DIR__, 2) . '/assets/images/uploads';
        $real = realpath($dir . '/' . $name);
        if (!$real || !str_starts_with($real, realpath($dir) . DIRECTORY_SEPARATOR)) return false;
        return @unlink($real);
    }

    /** Yüklenmiş görselleri listeler. */
    public function mediaLibrary(): array
    {
        $dir = dirname(__DIR__, 2) . '/assets/images/uploads';
        if (!is_dir($dir)) return [];
        $files = array_values(array_filter(scandir($dir) ?: [], fn($f) => preg_match('/\.(jpe?g|png|webp|gif)$/i', $f)));
        usort($files, fn($a, $b) => strcmp($b, $a));
        return array_map(fn($f) => 'images/uploads/' . $f, $files);
    }
}
