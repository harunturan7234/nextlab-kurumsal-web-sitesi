<article class="product-card">
    <a class="product-image" href="<?= e(url('urun/' . $product['slug'])) ?>"><img src="<?= e(asset($product['image_path'])) ?>" alt="<?= e($product['name']) ?>" width="600" height="600" loading="lazy"></a>
    <div class="product-card-body"><span class="product-code"><?= e($product['sku']) ?></span><h3><a href="<?= e(url('urun/' . $product['slug'])) ?>"><?= e($product['name']) ?></a></h3><p><?= e($product['short_description'] ?? '') ?></p><a class="text-link" href="<?= e(url('urun/' . $product['slug'])) ?>">Ürünü İncele <span>→</span></a></div>
</article>
