<?php /* Generated from views/pages/product.ejs; retain original layout. */ ?>
<section class="page-hero"><div class="container"><h1><?= e(($product['name'] ?? null)) ?></h1><?= view('components/breadcrumb', ['items' => [['label' => 'Ürünler', 'url' => url('urunler')], ['label' => ($product['category_name'] ?? null), 'url' => url(('kategori/' . ($product['category_slug'] ?? null)))], ['label' => ($product['name'] ?? null)]]], get_defined_vars()) ?></div></section>
<section class="section"><div class="container product-detail">
    <div class="detail-gallery"><div class="detail-image"><?= imageTag(($product['image_path'] ?? null), ($product['name'] ?? null), ['sizes' => '(max-width: 680px) 92vw, 46vw', 'priority' => true]) ?></div>
        <?php if ((!($product['image_path'] ?? null))): ?><p class="image-note">Bu ürünün fotoğrafı henüz eklenmedi.</p><?php endif; ?>
    </div>
    <div class="detail-copy"><span class="product-brand"><?= e(($product['brand_name'] ?? null)) ?></span><h2>Ürün hakkında</h2>
        <div class="detail-meta"><p><b>Kategori:</b> <a href="<?= e(url(('kategori/' . ($product['category_slug'] ?? null)))) ?>"><?= e(($product['category_name'] ?? null)) ?></a></p><p><b><?= e((($product['internal_sku'] ?? null) ? 'Katalog kodu' : 'Ürün kodu')) ?>:</b> <?= e(($product['sku'] ?? null)) ?></p>
        <?php if (($product['package_info'] ?? null)): ?><p><b>Ambalaj:</b> <?= e(($product['package_info'] ?? null)) ?></p><?php endif; ?></div>
        <p class="lead"><?= e(($product['description'] ?? null)) ?></p>
        <div class="usage-box"><h3>Kullanım Bilgisi</h3><p><?= e(($product['usage'] ?? null)) ?></p></div>
        <?php if (($product['marketplace_url'] ?? null)): ?>
        <div class="product-marketplaces"><a class="button" href="<?= e(($product['marketplace_url'] ?? null)) ?>" target="_blank" rel="noopener noreferrer"><?= e(($product['marketplace_name'] ?? null)) ?> ↗</a>
        <a class="marketplace-text-link" href="<?= e(($channels['trendyol'] ?? null)) ?>" target="_blank" rel="noopener noreferrer"><img src="<?= e(asset('images/verified/trendyol-icon-40.webp')) ?>" alt="" width="24" height="24">Trendyol mağazamız ↗</a>
        <a class="marketplace-text-link" href="<?= e(($channels['hepsiburada'] ?? null)) ?>" target="_blank" rel="noopener noreferrer"><img src="<?= e(asset('images/verified/hepsiburada-icon-40.webp')) ?>" alt="" width="24" height="24">Hepsiburada mağazamız ↗</a></div>
        <?php else: ?><a class="button" href="<?= e(url('iletisim')) ?>">Ürün Hakkında Bilgi Alın ↗</a><?php endif; ?>
        <?php if (($product['source_url'] ?? null)): ?><p class="product-source"><a href="<?= e(($product['source_url'] ?? null)) ?>" target="_blank" rel="noopener noreferrer">Ürün bilgi kaynağı ↗</a></p><?php endif; ?>
    </div>
</div></section>
<?php if (($related && (count($related ?? []) > 0))): ?><section class="section related-section"><div class="container"><div class="section-heading"><span>AYNI KATEGORİDEN</span><h2>Diğer Ürünler</h2></div><div class="product-grid"><?php foreach ($related as $product): ?><?= view('components/product-card', ['product' => $product], get_defined_vars()) ?><?php endforeach; ?></div></div></section><?php endif; ?>
