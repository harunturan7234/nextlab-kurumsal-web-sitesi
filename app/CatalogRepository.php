<?php
declare(strict_types=1);

final class CatalogRepository
{
    private array $data;
    private array $blogs;
    private array $settings = [];

    public function __construct(array $config)
    {
        $root = dirname(__DIR__);
        if (($config['app']['data_mode'] ?? 'file') === 'mysql') {
            $db = $config['database'];
            $pdo = new PDO(
                'mysql:host=' . $db['host'] . ';port=' . (int)($db['port'] ?? 3306) . ';dbname=' . $db['name'] . ';charset=utf8mb4',
                $db['user'], $db['password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
            );
            // Yalnızca YAYINDAKİ sürüm okunur; yayınlanmamış taslaklar siteye sızmaz.
            $decode = static fn(array $rows): array => array_map(static fn(array $r): array => json_decode($r['data_json'], true, 512, JSON_THROW_ON_ERROR), $rows);
            $this->data = [
                'categories' => $decode($pdo->query('SELECT data_json FROM nextlab_categories WHERE data_json IS NOT NULL ORDER BY sort_order, id')->fetchAll()),
                'products' => $decode($pdo->query('SELECT data_json FROM nextlab_products WHERE data_json IS NOT NULL ORDER BY sort_order, id')->fetchAll()),
            ];
            $query = $pdo->prepare('SELECT data_json FROM nextlab_content WHERE kind = :kind AND data_json IS NOT NULL ORDER BY sort_order, slug');
            foreach (['banners', 'faqs', 'distributorBrands', 'blogs', 'channels', 'settings'] as $kind) {
                $query->execute(['kind' => $kind]);
                $items = $decode($query->fetchAll());
                if ($kind === 'blogs') $this->blogs = $items;
                elseif ($kind === 'settings') $this->settings = $items[0] ?? [];
                else $this->data[$kind] = $kind === 'channels' ? ($items[0] ?? []) : $items;
            }
            if (!$this->data['products'] || !$this->data['categories']) throw new RuntimeException('Catalog database is empty. Import database.sql.');
        } else {
            $this->data = json_decode(file_get_contents($root . '/data/catalog.json'), true, 512, JSON_THROW_ON_ERROR);
            $this->blogs = json_decode(file_get_contents($root . '/data/blogs.json'), true, 512, JSON_THROW_ON_ERROR);
            $settingsFile = $root . '/data/settings.json';
            if (is_file($settingsFile)) {
                $this->settings = json_decode(file_get_contents($settingsFile), true, 512, JSON_THROW_ON_ERROR);
            }
        }
    }

    /** Site geneli ayarlar (sosyal medya, iletişim, kurumsal metin, SEO). */
    public function settings(): array { return $this->settings; }
    public function setting(string $path, mixed $default = null): mixed {
        $value = $this->settings;
        foreach (explode('.', $path) as $key) {
            if (!is_array($value) || !array_key_exists($key, $value)) return $default;
            $value = $value[$key];
        }
        return $value === '' || $value === [] ? $default : $value;
    }

    public function categories(): array { return $this->data['categories']; }
    public function mainCategories(): array { return array_values(array_filter($this->categories(), fn($c) => $c['parent_id'] === null)); }
    public function categoryBySlug(string $slug): ?array { return $this->find($this->categories(), 'slug', $slug); }
    public function subCategories(int $id): array { return array_values(array_filter($this->categories(), fn($c) => $c['parent_id'] === $id)); }
    public function allProducts(): array {
        $items = $this->data['products'];
        usort($items, fn($a,$b) => ((int)!empty($b['image_path'])) <=> ((int)!empty($a['image_path'])));
        return $items;
    }
    public function productBySlug(string $slug): ?array { return $this->find($this->allProducts(), 'slug', $slug); }
    public function productById(int $id): ?array { return $this->find($this->allProducts(), 'id', $id); }
    public function productsByCategory(int $id): array {
        $ids = [$id, ...array_column($this->subCategories($id), 'id')];
        return array_values(array_filter($this->allProducts(), fn($p) => in_array($p['category_id'], $ids, true)));
    }
    public function featuredProducts(int $limit = 8): array { return array_slice(array_values(array_filter($this->allProducts(), fn($p) => (bool)$p['is_featured'])), 0, $limit); }
    public function relatedProducts(int $category, int $exclude, int $limit = 4): array { return array_slice(array_values(array_filter($this->productsByCategory($category), fn($p) => $p['id'] !== $exclude)), 0, $limit); }
    public function banners(): array { return $this->data['banners']; }
    public function faqs(): array { return $this->data['faqs']; }
    public function channels(): array { return $this->data['channels']; }
    public function distributorBrands(): array { return $this->data['distributorBrands']; }
    public function distributorBrandBySlug(string $slug): ?array { return $this->find($this->distributorBrands(), 'slug', $slug); }
    public function blogs(): array { return $this->blogs; }
    public function blogBySlug(string $slug): ?array { return $this->find($this->blogs, 'slug', $slug) ?? $this->find($this->blogs, 'legacy_slug', $slug); }
    public function blogsByCategory(string $slug): array { return $slug === 'all' ? $this->blogs : array_values(array_filter($this->blogs, fn($b) => $b['categorySlug'] === $slug)); }
    public function blogCategories(): array {
        $categories = ['all' => ['slug' => 'all', 'name' => 'Tüm Yazılar']];
        foreach ($this->blogs as $b) $categories[$b['categorySlug']] = ['slug' => $b['categorySlug'], 'name' => $b['category']];
        return array_values($categories);
    }
    public function relatedBlogs(int $id): array { return array_slice(array_values(array_filter($this->blogs, fn($b) => $b['id'] !== $id)), 0, 3); }
    public function search(string $query): array {
        $needle = self::normalize($query);
        return array_values(array_filter($this->allProducts(), fn($p) => str_contains(self::normalize($p['name'] . ' ' . $p['sku'] . ' ' . $p['brand_name'] . ' ' . $p['description']), $needle)));
    }
    public static function normalize(string $value): string {
        // Works on shared hosting without mbstring.
        return strtolower(strtr($value, ['İ'=>'i','I'=>'i','ı'=>'i','Ş'=>'s','ş'=>'s','Ğ'=>'g','ğ'=>'g','Ü'=>'u','ü'=>'u','Ö'=>'o','ö'=>'o','Ç'=>'c','ç'=>'c']));
    }
    private function find(array $items, string $key, mixed $value): ?array {
        foreach ($items as $item) if (($item[$key] ?? null) === $value) return $item;
        return null;
    }
}
