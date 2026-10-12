<?php
declare(strict_types=1);

require_once __DIR__ . '/CatalogRepository.php';
date_default_timezone_set('Europe/Istanbul');
$root = dirname(__DIR__);
$config = is_file($root . '/config.php') ? require $root . '/config.php' : [
    'app' => ['url' => 'https://www.nextlabcosmetic.com', 'data_mode' => 'file', 'debug' => false],
];
$appUrl = rtrim($config['app']['url'] ?? 'https://www.nextlabcosmetic.com', '/');
if (!filter_var($appUrl, FILTER_VALIDATE_URL) || !in_array(parse_url($appUrl, PHP_URL_SCHEME), ['http', 'https'], true)) throw new RuntimeException('Invalid app URL.');
$repo = new CatalogRepository($config);
$media = json_decode(file_get_contents($root . '/data/media.json'), true, 512, JSON_THROW_ON_ERROR);

function e(mixed $value): string {
    if (is_bool($value)) $value = $value ? 'true' : 'false';
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function url(?string $path = ''): string { return '/' . ltrim($path ?? '', '/'); }
function asset(?string $path = ''): string { return '/assets/' . ltrim($path ?? '', '/'); }
function fullUrl(?string $path = ''): string { global $appUrl; return $appUrl . url($path); }
function mediaSrcset(?string $source): string {
    global $media;
    return implode(', ', array_map(fn($v) => asset($v['file']) . ' ' . $v['width'] . 'w', $media[$source]['variants'] ?? []));
}
function imageTag(?string $source, string $alt, array $options = []): string {
    global $media;
    if (!$source) return '<span class="asset-pending"><span aria-hidden="true">＋</span><span>Ürün fotoğrafı<br>hazırlanıyor</span></span>';
    $m = $media[$source] ?? null;
    $dimensions = $m ? ' width="' . $m['width'] . '" height="' . $m['height'] . '"' : '';
    $responsive = $m ? ' srcset="' . e(mediaSrcset($source)) . '" sizes="' . e($options['sizes'] ?? '100vw') . '"' : '';
    $priority = $options['priority'] ?? false;
    return '<img src="' . e(asset($source)) . '" alt="' . e($alt) . '"' . $dimensions . $responsive . ' decoding="async" loading="' . ($priority ? 'eager' : 'lazy') . '"' . ($priority ? ' fetchpriority="high"' : '') . '>';
}
function bannerProducts(array $banner): array {
    global $repo;
    return array_values(array_filter(array_map(fn($id) => $repo->productById($id), $banner['product_ids'] ?? [])));
}
function view(string $template, array $data = [], array $scope = []): string {
    if (!preg_match('~^(pages|partials|components)/[a-z0-9-]+$~D', $template) && $template !== 'layout') throw new RuntimeException('Invalid template.');
    $file = __DIR__ . '/views/' . $template . '.php';
    // Protect the renderer's own variables from page/partial variables.
    extract(array_merge($scope, $data), EXTR_SKIP);
    ob_start();
    try { require $file; return ob_get_clean(); }
    catch (Throwable $error) { ob_end_clean(); throw $error; }
}
function renderPage(string $view, array $data = [], int $status = 200): void {
    global $repo, $appUrl;
    $currentPath = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/', '/');
    $scope = array_merge([
        'currentPath' => $currentPath, 'baseUrl' => $appUrl, 'title' => '',
        'metaDescription' => '', 'canonicalUrl' => '', 'ogType' => 'website',
        'ogImage' => '', 'structuredData' => null, 'page' => '',
        'distributorBrands' => $repo->distributorBrands(), 'channels' => $repo->channels(),
    ], $data);
    $scope['content'] = view('pages/' . $view, $scope);
    http_response_code($status);
    header('Content-Type: text/html; charset=utf-8');
    echo view('layout', $scope);
}
