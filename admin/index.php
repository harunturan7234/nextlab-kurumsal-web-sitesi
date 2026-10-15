<?php
declare(strict_types=1);

/** Nextlab Yönetim Paneli — ön denetleyici. */

header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');
header('X-Frame-Options: DENY');
header('Cache-Control: no-store, no-cache, must-revalidate');

$root = dirname(__DIR__);
require_once $root . '/app/Admin/Admin.php';

/** Basit HTML temizleyici — panel içeriği için savunma katmanı. */
function admin_clean_html(string $html): string
{
    $html = preg_replace('#<\s*(script|style|iframe|object|embed|form)\b[^>]*>.*?<\s*/\s*\1\s*>#is', '', $html) ?? $html;
    $html = preg_replace('#<\s*(script|style|iframe|object|embed|form)\b[^>]*/?>#is', '', $html) ?? $html;
    $html = preg_replace('#\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)#i', '', $html) ?? $html;
    $html = preg_replace('#(href|src)\s*=\s*("|\')\s*javascript:[^"\']*\2#i', '$1="#"', $html) ?? $html;
    return trim($html);
}

function admin_e(mixed $v): string
{
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function admin_redirect(string $to): never
{
    header('Location: ' . $to);
    exit;
}

function admin_flash(string $type, string $msg): void
{
    $_SESSION['flash'][] = ['type' => $type, 'msg' => $msg];
}

function admin_view(string $tpl, array $vars = []): void
{
    extract($vars, EXTR_SKIP);
    require dirname(__DIR__) . '/app/views/admin/' . $tpl . '.php';
}

// Beklenmeyen hatalarda beyaz ekran yerine anlaşılır sayfa göster.
set_exception_handler(function (Throwable $err): void {
    error_log('Nextlab admin error: ' . $err->getMessage());
    if (!headers_sent()) { http_response_code(500); header('Content-Type: text/html; charset=utf-8'); }
    admin_view('bare', [
        'title' => 'İşlem tamamlanamadı',
        'body'  => '<p>Beklenmeyen bir hata oluştu ve işlem tamamlanamadı. Lütfen tekrar deneyin.</p>'
                 . '<p><a class="btn btn-primary full" href="/admin">Panoya dön</a></p>',
    ]);
});

// --------------------------------------------------------------- yapılandırma
if (!is_file($root . '/config.php')) {
    http_response_code(503);
    admin_view('bare', ['title' => 'Yapılandırma gerekli', 'body' =>
        '<p><strong>config.php bulunamadı.</strong></p><p>Yönetim panelini kullanmak için sunucuda <code>config.example.php</code> dosyasını <code>config.php</code> adıyla kopyalayın; veritabanı bilgilerinizi girin ve <code>data_mode</code> değerini <code>mysql</code> yapın.</p>']);
    exit;
}
$config = require $root . '/config.php';

try {
    $admin = new Admin($config);
} catch (Throwable $err) {
    http_response_code(503);
    admin_view('bare', ['title' => 'Bağlantı hatası', 'body' =>
        '<p><strong>Veritabanına bağlanılamadı.</strong></p><p>' . admin_e($err->getMessage()) . '</p><p>config.php içindeki veritabanı bilgilerini ve <code>data_mode = "mysql"</code> ayarını kontrol edin.</p>']);
    exit;
}

// ------------------------------------------------------------------ yönlendirme
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/admin', PHP_URL_PATH) ?? '/admin');
$path = '/' . trim(preg_replace('#^/admin#', '', $path) ?? '', '/');
$seg = $path === '/' ? [] : explode('/', trim($path, '/'));
$isPost = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
$entities = Admin::entities();

// --- ilk kurulum (hiç yönetici yoksa) ---
if ($admin->adminCount() === 0) {
    if ($isPost && ($seg[0] ?? '') === 'kurulum') {
        $admin->checkCsrf();
        $email = trim((string)($_POST['email'] ?? ''));
        $name  = trim((string)($_POST['name'] ?? '')) ?: 'Yönetici';
        $pass  = (string)($_POST['password'] ?? '');
        $err = null;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $err = 'Geçerli bir e-posta adresi girin.';
        elseif (strlen($pass) < 10) $err = 'Parola en az 10 karakter olmalıdır.';
        if ($err) { admin_view('setup', ['admin' => $admin, 'error' => $err]); exit; }
        $admin->createAdmin($email, $name, $pass);
        $admin->login($email, $pass);
        admin_flash('ok', 'Hoş geldiniz! Yönetici hesabınız oluşturuldu.');
        admin_redirect('/admin');
    }
    admin_view('setup', ['admin' => $admin, 'error' => null]);
    exit;
}

// --- giriş / çıkış ---
if (($seg[0] ?? '') === 'giris') {
    if ($admin->user()) admin_redirect('/admin');
    $error = null;
    if ($isPost) {
        $admin->checkCsrf();
        $error = $admin->login(trim((string)($_POST['email'] ?? '')), (string)($_POST['password'] ?? ''));
        if ($error === null) admin_redirect('/admin');
    }
    admin_view('login', ['admin' => $admin, 'error' => $error, 'demo' => admin_demo_credentials($root)]);
    exit;
}
if (($seg[0] ?? '') === 'cikis') {
    if ($isPost) $admin->checkCsrf();   // zorla çıkış (CSRF) engeli
    $admin->logout();
    admin_redirect('/admin/giris');
}

$user = $admin->requireLogin();

// --- tümünü yayına al ---
if ($isPost && ($seg[0] ?? '') === 'yayinla-tumu') {
    $admin->checkCsrf();
    $n = $admin->publishAll();
    admin_flash('ok', $n > 0 ? "{$n} değişiklik yayına alındı. Site güncel." : 'Yayınlanacak değişiklik yoktu.');
    admin_redirect($_POST['_back'] ?? '/admin');
}

// --- yedek al / geri yükle ---
if (($seg[0] ?? '') === 'yedek') {
    if (($seg[1] ?? '') === 'indir') {
        $json = json_encode($admin->exportAll(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="nextlab-yedek-' . date('Y-m-d-Hi') . '.json"');
        header('Content-Length: ' . strlen((string)$json));
        echo $json;
        exit;
    }
    if ($isPost) {
        $admin->checkCsrf();
        $f = $_FILES['backup'] ?? null;
        if (!$f || ($f['error'] ?? 1) !== UPLOAD_ERR_OK) {
            admin_flash('err', 'Dosya yüklenemedi.');
        } else {
            $raw = (string)file_get_contents($f['tmp_name']);
            $dump = json_decode($raw, true);
            $err = is_array($dump) ? $admin->importAll($dump) : 'Dosya geçerli bir JSON değil.';
            admin_flash($err ? 'err' : 'ok', $err ?: 'Yedek geri yüklendi. Sitenin güncel hâli için değişiklikleri yayına almayı unutmayın.');
        }
        admin_redirect('/admin/yedek');
    }
    admin_view('backup', ['admin' => $admin, 'user' => $user]);
    exit;
}

// --- hesap yönetimi ---
if (($seg[0] ?? '') === 'hesap') {
    if ($isPost) {
        $admin->checkCsrf();
        $what = (string)($_POST['what'] ?? '');
        if ($what === 'profil') {
            $err = $admin->updateProfile((int)$user['id'], (string)($_POST['name'] ?? ''), trim((string)($_POST['email'] ?? '')));
            admin_flash($err ? 'err' : 'ok', $err ?: 'Hesap bilgileriniz güncellendi.');
        } elseif ($what === 'parola') {
            $err = $admin->changePassword((int)$user['id'], (string)($_POST['current'] ?? ''), (string)($_POST['new'] ?? ''), (string)($_POST['again'] ?? ''));
            admin_flash($err ? 'err' : 'ok', $err ?: 'Parolanız değiştirildi.');
        } elseif ($what === 'ekle') {
            $err = $admin->addAdmin((string)($_POST['name'] ?? ''), trim((string)($_POST['email'] ?? '')), (string)($_POST['password'] ?? ''));
            admin_flash($err ? 'err' : 'ok', $err ?: 'Yeni yönetici eklendi.');
        } elseif ($what === 'sil') {
            $err = $admin->removeAdmin((int)($_POST['id'] ?? 0), (int)$user['id']);
            admin_flash($err ? 'err' : 'ok', $err ?: 'Yönetici silindi.');
        }
        admin_redirect('/admin/hesap');
    }
    admin_view('account', ['admin' => $admin, 'user' => $admin->user(), 'admins' => $admin->listAdmins()]);
    exit;
}

// --- medya kitaplığı ---
if (($seg[0] ?? '') === 'medya') {
    if ($isPost) {
        $admin->checkCsrf();
        if (($seg[1] ?? '') === 'sil') {
            $ok = $admin->deleteUpload(basename((string)($_POST['file'] ?? '')));
            admin_flash($ok ? 'ok' : 'err', $ok ? 'Görsel silindi.' : 'Görsel silinemedi.');
            admin_redirect('/admin/medya');
        }
        [$p, $err, $warn] = $admin->uploadImage($_FILES['file'] ?? []);
        admin_flash($err ? 'err' : 'ok', $err ?: 'Görsel yüklendi.');
        if ($warn) admin_flash('err', admin_e($warn));
        admin_redirect('/admin/medya');
    }
    admin_view('media', ['admin' => $admin, 'user' => $user, 'files' => $admin->mediaLibrary()]);
    exit;
}

// --- panoya dön ---
if (!isset($seg[0]) || $seg[0] === '') {
    admin_view('dashboard', ['admin' => $admin, 'user' => $user, 'entities' => $entities]);
    exit;
}

// --- varlık işlemleri ---
$key = $seg[0];
$e = Admin::entity($key);
if (!$e) { http_response_code(404); admin_view('bare', ['title' => 'Sayfa bulunamadı', 'body' => '<p>Aradığınız bölüm yok. <a href="/admin">Panoya dön</a>.</p>']); exit; }
$e['key'] = $key;

$action = $seg[1] ?? '';

// Tekil varlıklar (ayarlar, kanallar) doğrudan form gösterir.
if (!empty($e['single'])) {
    $row = $admin->single($e);
    if ($isPost) {
        $admin->checkCsrf();
        $data = $row['data'];
        foreach ($e['fields'] as $f) Admin::set($data, $f['key'], admin_field_value($f, $admin));
        $admin->saveDraft($e, (int)$row['id'], $data, (string)$row['slug']);
        if (($_POST['_publish'] ?? '') === '1') { $admin->publish($e, (int)$row['id']); admin_flash('ok', 'Kaydedildi ve yayına alındı.'); }
        else admin_flash('ok', 'Taslak kaydedildi. Yayınlamak için “Yayına Al” deyin.');
        admin_redirect('/admin/' . $key);
    }
    admin_view('form', ['admin' => $admin, 'user' => $user, 'e' => $e, 'row' => $row, 'data' => $row['data'], 'isNew' => false]);
    exit;
}

// POST işlemleri
if ($isPost) {
    $admin->checkCsrf();
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'sil') {
        // Kullanımda olan kategori silinirse ürünler sahipsiz kalır — engelle.
        if ($e['table'] === 'nextlab_categories') {
            $used = $admin->categoryUsage($id);
            if ($used['products'] > 0 || $used['children'] > 0) {
                $parts = [];
                if ($used['products'] > 0) $parts[] = $used['products'] . ' ürün';
                if ($used['children'] > 0) $parts[] = $used['children'] . ' alt kategori';
                admin_flash('err', 'Bu kategori silinemez: ' . admin_e(implode(' ve ', $parts))
                    . ' buna bağlı. Önce onları başka bir kategoriye taşıyın.');
                admin_redirect('/admin/' . $key);
            }
        }
        $admin->delete($e, $id);
        admin_flash('ok', $e['singular'] . ' silindi.');
        admin_redirect('/admin/' . $key);
    }
    if ($action === 'yayinla') {
        $admin->publish($e, $id);
        admin_flash('ok', $e['singular'] . ' yayına alındı.');
        admin_redirect($_POST['_back'] ?? '/admin/' . $key);
    }
    if ($action === 'yayindan-kaldir') {
        $admin->unpublish($e, $id);
        admin_flash('ok', $e['singular'] . ' yayından kaldırıldı.');
        admin_redirect('/admin/' . $key);
    }
    if ($action === 'taslagi-sil') {
        $admin->discardDraft($e, $id);
        admin_flash('ok', 'Yayınlanmamış değişiklikler geri alındı.');
        admin_redirect('/admin/' . $key);
    }
    if ($action === 'tasi') {
        $admin->move($e, $id, (string)($_POST['dir'] ?? 'up') === 'down' ? 'down' : 'up');
        admin_redirect('/admin/' . $key);
    }
    if ($action === 'sirala') {
        $admin->reorder($e, array_map('intval', (array)($_POST['order'] ?? [])));
        admin_flash('ok', 'Sıralama güncellendi.');
        admin_redirect('/admin/' . $key);
    }
    if ($action === 'kaydet') {
        $existing = $id ? $admin->find($e, $id) : null;
        $data = $existing['data'] ?? [];
        foreach ($e['fields'] as $f) Admin::set($data, $f['key'], admin_field_value($f, $admin));

        // slug türet / benzersizleştir
        $slugField = null;
        foreach ($e['fields'] as $f) if (($f['type'] ?? '') === 'slug' && $f['key'] === 'slug') $slugField = $f;
        $titleKey = $e['title_field'] ?? 'name';
        $slugSource = (string)(Admin::get($data, 'slug', '') ?: Admin::get($data, $titleKey, ''));
        $slug = $admin->uniqueSlug($e, $slugSource, $id ?: null);
        if ($slugField) Admin::set($data, 'slug', $slug);

        // blogda görünen tarihi ISO tarihten türet (iki alanın tutarsızlaşmasını önler)
        if (($e['kind'] ?? null) === 'blogs') {
            $iso = (string) Admin::get($data, 'isoDate', '');
            $ts = $iso !== '' ? strtotime($iso) : false;
            if ($ts !== false) {
                $ay = [1 => 'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran',
                       'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
                Admin::set($data, 'publishedAt', date('j', $ts) . ' ' . $ay[(int)date('n', $ts)] . ' ' . date('Y', $ts));
            }
        }

        // ürünlerde kategori adı/slug'ını senkron tut
        if ($e['table'] === 'nextlab_products') {
            $meta = $admin->categoryMeta((int)(Admin::get($data, 'category_id', 0)));
            $data['category_name'] = $meta['name'];
            $data['category_slug'] = $meta['slug'];
        }
        $data['updated_at'] = date('Y-m-d');

        // Zorunlu alanlar sunucu tarafında da doğrulanır (tarayıcı kontrolü atlanabilir).
        $invalid = [];
        foreach ($e['fields'] as $f) {
            if (empty($f['required'])) continue;
            $val = Admin::get($data, $f['key'], '');
            if ($val === '' || $val === null || $val === []) $invalid[] = $f['label'];
        }
        if ($invalid) {
            admin_flash('err', 'Şu alanlar zorunludur: <strong>' . admin_e(implode(', ', $invalid)) . '</strong>');
            $row = $id ? $admin->find($e, $id) : null;
            admin_view('form', ['admin' => $admin, 'user' => $user, 'e' => $e, 'row' => $row, 'data' => $data, 'isNew' => !$id]);
            exit;
        }

        // Adres (slug) değiştiyse eski bağlantı ölmesin: kalıcı yönlendirme kaydet.
        if ($existing && !empty($e['public'])) {
            $oldSlug = (string)($existing['slug'] ?? '');
            if ($oldSlug !== '' && $oldSlug !== $slug) {
                $admin->recordRedirect('/' . sprintf($e['public'], $oldSlug), '/' . sprintf($e['public'], $slug));
            }
        }

        $newId = $admin->saveDraft($e, $id ?: null, $data, $slug);
        if (($_POST['_publish'] ?? '') === '1') {
            $admin->publish($e, $newId);
            admin_flash('ok', $e['singular'] . ' kaydedildi ve <strong>yayına alındı</strong>.');
        } else {
            admin_flash('ok', $e['singular'] . ' taslağa kaydedildi. Yayınlamak için “Yayına Al” deyin.');
        }
        admin_redirect('/admin/' . $key);
    }
    admin_redirect('/admin/' . $key);
}

// GET: yeni / düzenle / liste
if ($action === 'yeni') {
    admin_view('form', ['admin' => $admin, 'user' => $user, 'e' => $e, 'row' => null, 'data' => [], 'isNew' => true]);
    exit;
}
if ($action !== '' && ctype_digit($action)) {
    $row = $admin->find($e, (int)$action);
    if (!$row) { http_response_code(404); admin_redirect('/admin/' . $key); }
    admin_view('form', ['admin' => $admin, 'user' => $user, 'e' => $e, 'row' => $row, 'data' => $row['data'], 'isNew' => false]);
    exit;
}
admin_view('list', ['admin' => $admin, 'user' => $user, 'e' => $e, 'rows' => $admin->all($e)]);


// --------------------------------------------------------- alan değer okuma
function admin_field_value(array $f, Admin $admin): mixed
{
    $name = str_replace('.', '__', $f['key']);
    $type = $f['type'] ?? 'text';
    $raw = $_POST[$name] ?? null;

    switch ($type) {
        case 'bool':
            return !empty($raw) ? 1 : 0;
        case 'list':
            $lines = preg_split('/\r\n|\r|\n/', (string)$raw) ?: [];
            return array_values(array_filter(array_map('trim', $lines), fn($s) => $s !== ''));
        case 'kvlist':
            $out = [];
            foreach (preg_split('/\r\n|\r|\n/', (string)$raw) ?: [] as $line) {
                $line = trim($line);
                if ($line === '') continue;
                $parts = array_map('trim', explode('|', $line, 2));
                $out[] = ['label' => $parts[0], 'url' => $parts[1] ?? ''];
            }
            return $out;
        case 'idlist':
            $ids = array_filter(array_map('trim', explode(',', (string)$raw)), fn($s) => $s !== '');
            return array_values(array_map('intval', $ids));
        case 'select':
            $v = trim((string)$raw);
            return $v === '' ? null : (int)$v;
        case 'richtext':
            return admin_clean_html((string)$raw);
        case 'image':
            // Yeni dosya yüklendiyse onu kullan, yoksa seçili/eski yolu koru.
            $file = $_FILES[$name] ?? null;
            if ($file && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                [$p, $err, $warn] = $admin->uploadImage($file);
                if ($err) admin_flash('err', $f['label'] . ': ' . $err);
                if ($warn) admin_flash('err', admin_e($f['label'] . ': ' . $warn));
                if ($p) return $p;
            }
            return trim((string)$raw);
        default:
            return trim((string)$raw);
    }
}

/**
 * Deneme giriş bilgileri — YALNIZCA yerel geliştirmede gösterilir.
 * Canlı alan adında (Natro) asla döndürmez; parola kimseye görünmez.
 */
function admin_demo_credentials(string $root): ?array
{
    $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
    $hostOnly = explode(':', $host)[0];
    $isLocal = in_array($hostOnly, ['127.0.0.1', 'localhost', '::1', '[::1]'], true)
        || str_ends_with($hostOnly, '.test') || str_ends_with($hostOnly, '.localhost');
    if (!$isLocal) return null;

    $file = $root . '/.local-admin.txt';
    if (!is_file($file)) return null;
    $txt = (string)file_get_contents($file);
    $mail = $pass = null;
    if (preg_match('/^E-posta\s*:\s*(.+)$/mu', $txt, $m)) $mail = trim($m[1]);
    if (preg_match('/^Parola\s*:\s*(.+)$/mu', $txt, $m)) $pass = trim($m[1]);
    return ($mail && $pass) ? ['email' => $mail, 'password' => $pass] : null;
}
