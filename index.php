<?php

declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

$path = trim((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
$basePath = trim((string) parse_url(base_url(), PHP_URL_PATH), '/');
if ($basePath !== '' && str_starts_with($path, $basePath)) {
    $path = trim(substr($path, strlen($basePath)), '/');
}

try {
    if ($path === '') {
        $page = 'home';
        $banners = $repository->banners();
        $categories = $repository->mainCategories();
        $featuredProducts = $repository->featuredProducts(8);
        render('pages/home', compact('page', 'banners', 'categories', 'featuredProducts'));
        exit;
    }

    if ($path === 'hakkimizda') {
        render('pages/about', ['page' => 'about']);
        exit;
    }
    if ($path === 'iletisim') {
        render('pages/contact', ['page' => 'contact']);
        exit;
    }
    if ($path === 'katalog') {
        render('pages/catalog', ['page' => 'catalog', 'categories' => $repository->mainCategories()]);
        exit;
    }
    if ($path === 'urunler') {
        render('pages/category', [
            'page' => 'products', 'category' => ['name' => 'Tüm Ürünler', 'slug' => 'urunler'],
            'categories' => $repository->mainCategories(), 'products' => $repository->allProducts(),
        ]);
        exit;
    }
    if ($path === 'arama') {
        $query = trim((string) ($_GET['q'] ?? ''));
        $queryLength = function_exists('mb_strlen') ? mb_strlen($query) : strlen($query);
        $products = $queryLength >= 2 ? $repository->search($query) : [];
        render('pages/search', compact('query', 'products') + ['page' => 'search']);
        exit;
    }
    if (preg_match('#^kategori/([a-z0-9-]+)$#', $path, $match)) {
        $category = $repository->categoryBySlug($match[1]);
        if (!$category) {
            throw new RuntimeException('not-found');
        }
        render('pages/category', [
            'page' => 'products', 'category' => $category,
            'categories' => $repository->mainCategories(),
            'subCategories' => $repository->subCategories((int) $category['id']),
            'products' => $repository->productsByCategory((int) $category['id']),
        ]);
        exit;
    }
    if (preg_match('#^urun/([a-z0-9-]+)$#', $path, $match)) {
        $product = $repository->productBySlug($match[1]);
        if (!$product) {
            throw new RuntimeException('not-found');
        }
        $related = $repository->relatedProducts((int) $product['category_id'], (int) $product['id'], 4);
        render('pages/product', compact('product', 'related') + ['page' => 'products']);
        exit;
    }

    throw new RuntimeException('not-found');
} catch (Throwable $exception) {
    if ($exception->getMessage() !== 'not-found') {
        error_log($exception->getMessage());
    }
    http_response_code($exception->getMessage() === 'not-found' ? 404 : 500);
    render('pages/404', ['page' => '404']);
}
