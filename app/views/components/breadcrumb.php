<?php /* Generated from views/components/breadcrumb.ejs; retain original layout. */ ?>
<nav class="breadcrumb" aria-label="Sayfa yolu"><a href="<?= e(url('')) ?>">Anasayfa</a><span>/</span><?php foreach ($items as $index => $item): ?><?php if ((($item['url'] ?? null) && ($index !== (count($items ?? []) - 1)))): ?><a href="<?= e(($item['url'] ?? null)) ?>"><?= e(($item['label'] ?? null)) ?></a><?php else: ?><span aria-current="page"><?= e(($item['label'] ?? null)) ?></span><?php endif; ?><?php if (($index !== (count($items ?? []) - 1))): ?><span>/</span><?php endif; ?><?php endforeach; ?></nav>
<?php
$bcCrumbs = [['name' => 'Anasayfa', 'url' => fullUrl('')]];
$bcCurrent = fullUrl(ltrim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/', '/'));
foreach ($items as $bcItem) {
    $bcCrumbs[] = ['name' => $bcItem['label'] ?? '', 'url' => !empty($bcItem['url']) ? fullUrl($bcItem['url']) : $bcCurrent];
}
?>
<script type="application/ld+json"><?= json_encode(['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => array_map(fn($c, $i) => ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $c['name'], 'item' => $c['url']], $bcCrumbs, array_keys($bcCrumbs))], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
