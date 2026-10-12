<?php /* Generated from views/components/category-card.ejs; retain original layout. */ ?>
<article class="category-card">
    <a class="category-image" href="<?= e(url(('kategori/' . ($category['slug'] ?? null)))) ?>"><?= imageTag(($category['image_path'] ?? null), ($category['name'] ?? null), ['sizes' => '(max-width: 680px) 92vw, 46vw']) ?><span class="category-image-label"><?= e(((($category['id'] ?? null) === 1) ? 'TEMİZLİK' : 'EVERYDAY')) ?></span></a>
    <div><h3><a href="<?= e(url(('kategori/' . ($category['slug'] ?? null)))) ?>"><?= e(($category['name'] ?? null)) ?> <span aria-hidden="true">↗</span></a></h3><p><?= e(($category['description'] ?? null)) ?></p></div>
</article>
