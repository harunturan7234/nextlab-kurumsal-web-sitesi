<?php /* Generated from views/pages/blog.ejs; retain original layout. */ ?>
<section class="page-hero">
    <div class="container">
        <h1>Blog</h1>
        <?= view('components/breadcrumb', ['items' => [['label' => 'Blog']]], get_defined_vars()) ?>
    </div>
</section>

<section class="section blog-listing-section">
    <div class="container">
        <?php if (((isset($categories) && $categories) && (count($categories ?? []) > 0))): ?>
        <!-- Blog Kategori Filtreleri -->
        <div class="blog-category-filter" role="tablist" aria-label="Blog kategorileri">
            <?php foreach ($categories as $cat): ?><?php $isActive = (($currentCategory === ($cat['slug'] ?? null)) ?: ((!$currentCategory) && (($cat['slug'] ?? null) === 'all'))); ?>
            <a 
                href="<?= e(url(((($cat['slug'] ?? null) === 'all') ? 'blog' : ('blog?kategori=' . ($cat['slug'] ?? null))))) ?>" 
                class="blog-filter-btn <?= e(($isActive ? 'is-active' : '')) ?>"
                role="tab"
                aria-selected="<?= e(($isActive ? 'true' : 'false')) ?>"
            >
                <?= e(($cat['name'] ?? null)) ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php if (((!$blogs) ?: (count($blogs ?? []) === 0))): ?>
            <div class="blog-empty-state">
                <p>Seçilen kategoride henüz yazı bulunmamaktadır.</p>
                <a href="<?= e(url('blog')) ?>" class="primary-button">Tüm Yazıları Görüntüle</a>
            </div>
        <?php else: ?>
            
            <!-- Clean, Compact 3-Column Editorial Grid -->
            <div class="blog-grid-cards">
                <?php foreach ($blogs as $article): ?>
                <article class="blog-card" aria-label="<?= e(($article['title'] ?? null)) ?>">
                    <div class="card-media-wrap">
                        <a href="<?= e(url(('blog/' . ($article['slug'] ?? null)))) ?>" aria-label="<?= e(($article['title'] ?? null)) ?> yazısını oku">
                            <?php if (($article['image_path'] ?? null)): ?><?= imageTag(($article['image_path'] ?? null), ($article['image_alt'] ?? null), ['sizes' => '(max-width: 680px) 92vw, 360px']) ?><?php else: ?><span class="blog-editorial-cover"><small>NEXTLAB REHBERİ</small><strong><?= e(($article['category'] ?? null)) ?></strong></span><?php endif; ?>
                        </a>
                    </div>

                    <div class="card-body">
                        <h2 class="card-title">
                            <a href="<?= e(url(('blog/' . ($article['slug'] ?? null)))) ?>"><?= e(($article['title'] ?? null)) ?></a>
                        </h2>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>
    </div>
</section>

<section class="cta">
    <div class="container">
        <div>
            <span>BİLİMSEL FORMÜLASYON &amp; KURUMSAL TEDARİK</span>
            <h2>Eczane, klinik ve B2B toptan tedarik talepleriniz için uzman ekibimizle iletişime geçin.</h2>
        </div>
        <a class="button light" href="<?= e(url('iletisim')) ?>">Kurumsal İletişim →</a>
    </div>
</section>
