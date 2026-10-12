<?php /* Generated from views/components/product-card.ejs; retain original layout. */ ?>
<article class="product-card">
    <a class="product-image" href="<?= e(url(('urun/' . ($product['slug'] ?? null)))) ?>" aria-label="<?= e(($product['name'] ?? null)) ?>">
        <?= imageTag(($product['image_path'] ?? null), ($product['name'] ?? null), ['sizes' => '(max-width: 680px) 44vw, (max-width: 980px) 30vw, 280px']) ?>
        <?php if (($product['package_info'] ?? null)): ?>
            <span class="product-badge-pill"><?= e(($product['package_info'] ?? null)) ?></span>
        <?php endif; ?>
    </a>
    <div class="product-card-body">
        <div class="product-meta-row">
            <span class="product-brand"><?= e((($product['brand_name'] ?? null) ?: 'NextLab')) ?></span>
            <span class="product-code"><?= e(($product['category_name'] ?? null)) ?></span>
        </div>
        <h3 class="product-title">
            <a href="<?= e(url(('urun/' . ($product['slug'] ?? null)))) ?>"><?= e(($product['name'] ?? null)) ?></a>
        </h3>
        <?php if (($product['short_description'] ?? null)): ?>
            <p class="product-desc"><?= e(($product['short_description'] ?? null)) ?></p>
        <?php endif; ?>
        <div class="product-card-footer">
            <a class="product-btn" href="<?= e(url(('urun/' . ($product['slug'] ?? null)))) ?>">
                <span>Ürünü İncele</span>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </a>
        </div>
    </div>
</article>
