<?php

declare(strict_types=1);
require __DIR__ . '/app/bootstrap.php';
header('Content-Type: application/xml; charset=UTF-8');
$urls = [['loc' => base_url(), 'date' => date('Y-m-d')]];
foreach (['hakkimizda', 'iletisim', 'katalog', 'urunler'] as $route) {
    $urls[] = ['loc' => url($route), 'date' => date('Y-m-d')];
}
foreach ($repository->mainCategories() as $category) {
    $urls[] = ['loc' => url('kategori/' . $category['slug']), 'date' => $category['updated_at'] ?? date('Y-m-d')];
}
foreach ($repository->allProducts() as $product) {
    $urls[] = ['loc' => url('urun/' . $product['slug']), 'date' => $product['updated_at'] ?? date('Y-m-d')];
}
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($urls as $item): ?>
  <url><loc><?= e($item['loc']) ?></loc><lastmod><?= e(substr((string) $item['date'], 0, 10)) ?></lastmod></url>
<?php endforeach; ?>
</urlset>
