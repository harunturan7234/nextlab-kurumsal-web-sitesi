<article class="category-card">
    <a class="category-image" href="<?= e(url('kategori/' . $category['slug'])) ?>"><img src="<?= e(asset($category['image_path'])) ?>" alt="<?= e($category['name']) ?>" width="700" height="520"></a>
    <div><h3><a href="<?= e(url('kategori/' . $category['slug'])) ?>"><?= e($category['name']) ?></a></h3><p><?= e($category['description'] ?? '') ?></p></div>
</article>
