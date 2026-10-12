<?php /* Generated from views/pages/home.ejs; retain original layout. */ ?>
<section class="hero-showcase" data-slider aria-roledescription="karusel" aria-label="Nextlab ürün dünyası">
    <div class="hero-slides-wrapper">
        <?php foreach ($banners as $index => $banner): ?>
        <article class="hero-slide hero-<?= e(($banner['theme'] ?? null)) ?> <?= e((($index === 0) ? 'is-active' : '')) ?>" data-slide="<?= e($index) ?>" aria-hidden="<?= e(($index !== 0)) ?>" <?= e((($index !== 0) ? 'inert' : '')) ?>>
            <div class="container hero-composition">
                <div class="hero-copy">
                    <span class="hero-eyebrow"><span></span><?= e(($banner['badge'] ?? null)) ?></span>
                    <?php if (($index === 0)): ?><h1><?= e(($banner['title'] ?? null)) ?></h1><?php else: ?><h2><?= e(($banner['title'] ?? null)) ?></h2><?php endif; ?>
                    <p><?= e(($banner['subtitle'] ?? null)) ?></p>
                    <div class="hero-actions">
                        <a class="button" href="<?= e(url(($banner['button_url'] ?? null))) ?>"><?= e(($banner['button_text'] ?? null)) ?> <span aria-hidden="true">↗</span></a>
                        <?php if (($banner['secondary_url'] ?? null)): ?><a class="hero-secondary" href="<?= e(($banner['secondary_url'] ?? null)) ?>" target="_blank" rel="noopener noreferrer"><?= e(($banner['secondary_text'] ?? null)) ?></a><?php endif; ?>
                    </div>
                    <span class="hero-counter">0<?= e(($index + 1)) ?> <span>/ 03</span></span>
                </div>
                <?php if (($banner['image_path'] ?? null)): ?>
                <div class="hero-photo">
                    <picture>
                        <source media="(max-width: 680px)" srcset="<?= e(asset('images/verified/temizlik-mobil-480.webp')) ?> 480w, <?= e(asset('images/verified/temizlik-mobil-768.webp')) ?> 768w" sizes="100vw">
                        <img src="<?= e(asset(($banner['image_path'] ?? null))) ?>" srcset="<?= e(mediaSrcset(($banner['image_path'] ?? null))) ?>" sizes="(max-width: 680px) 100vw, 56vw" width="1920" height="650" alt="Profesyonel temizlik ekipmanları" loading="lazy">
                    </picture>
                    <span class="photo-credit">Profesyonel temizlik ekipmanları</span>
                </div>
                <?php else: ?>
                <div class="hero-product-composition">
                    <?php foreach (bannerProducts($banner) as $item): ?>
                    <a class="hero-product" href="<?= e(url(('urun/' . ($item['slug'] ?? null)))) ?>">
                        <?= imageTag(($item['image_path'] ?? null), ($item['name'] ?? null), ['sizes' => '(max-width: 680px) 30vw, 18vw', 'priority' => ($index === 0)]) ?>
                        <span><b><?= e(($item['brand_name'] ?? null)) ?></b><?= e(preg_replace('~^Everyday ~u', '', ($item['name'] ?? null))) ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
    <div class="slider-controls">
        <button class="hero-nav-arrow hero-prev" type="button" aria-label="Önceki slayt">←</button>
        <div class="slider-dots" role="group" aria-label="Slayt seçimi">
            <?php foreach ($banners as $index => $banner): ?>
            <button class="<?= e((($index === 0) ? 'active' : '')) ?>" type="button" aria-current="<?= e((($index === 0) ? 'true' : 'false')) ?>" aria-label="<?= e(($index + 1)) ?>. slayt: <?= e(($banner['title'] ?? null)) ?>"><span></span></button>
            <?php endforeach; ?>
        </div>
        <button class="hero-nav-arrow hero-next" type="button" aria-label="Sonraki slayt">→</button>
        <button class="slider-pause" type="button" aria-pressed="false" aria-label="Slaytları duraklat">Duraklat</button>
    </div>
</section>
<section class="section categories-section">
    <div class="container">
        <div class="section-heading"><span>ÜRÜN AİLEMİZ</span><h2>Kategoriler</h2><p>İhtiyacınıza uygun ürünleri, kendi marka kimlikleriyle keşfedin.</p></div>
        <div class="category-grid">
            <?php foreach ($categories as $category): ?>
                <?= view('components/category-card', ['category' => $category], get_defined_vars()) ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<section class="section featured-section">
    <div class="container">
        <div class="section-heading row-heading"><div><span>ÖNE ÇIKANLAR</span><h2>Seçili Ürünler</h2></div><a class="outline-button" href="<?= e(url('urunler')) ?>">Tüm Ürünler</a></div>
        <div class="product-grid">
            <?php foreach ($featuredProducts as $product): ?>
                <?= view('components/product-card', ['product' => $product], get_defined_vars()) ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<section class="brand-family section">
    <div class="container brand-family-grid">
        <div><span class="eyebrow dark">ÜRÜN AİLELERİ</span><h2>İki odak, yalın bir ürün yaklaşımı</h2><p>Everyday günlük cilt ve kişisel bakım ürünlerini; NextLab ise ortam kokulandırma çözümlerini bir araya getirir.</p></div>
        <div class="brand-tile everyday"><strong>everyday</strong><span>Günlük bakım ürünleri</span><a href="<?= e(url('kategori/kozmetik-ve-kisisel-bakim')) ?>">Ürünleri keşfet →</a></div>
        <div class="brand-tile nextlab"><strong>NEXTLAB</strong><span>Ortam kokulandırma</span><a href="<?= e(url('kategori/ortam-kokulandirma')) ?>">Koleksiyonu incele →</a></div>
    </div>
</section>
<section class="approach section">
    <div class="container content-split">
        <div class="approach-visual"><span>LESS</span><b>IS MORE</b></div>
        <div class="prose"><span class="eyebrow dark">MARKA YAKLAŞIMI</span><h2>Az içerikle, anlaşılır bakım.</h2><p>Nextlab Cosmetic, cilt sağlığına yönelik çözüm odaklı ürünler geliştirmeyi; sade ve anlaşılır içerik iletişimini merkeze alır.</p><p>Everyday ürün ailesi günlük kullanıma odaklanırken, marka bilinçli cilt bakımı alışkanlıklarını destekleyen açık bir iletişim hedefler.</p><a class="text-link" href="<?= e(url('hakkimizda')) ?>">Nextlab’ı tanıyın →</a></div>
    </div>
</section>
<?php if (((isset($latestBlogs) && $latestBlogs) && (count($latestBlogs ?? []) > 0))): ?>
<section class="section home-blog-section" aria-label="Güncel blog yazıları">
    <div class="container">
        <div class="section-heading row-heading">
            <div>
                <span>NEXTLAB REHBERİ</span>
                <h2>Ürünler &amp; Yaşam Alanları</h2>
            </div>
            <a class="outline-button" href="<?= e(url('blog')) ?>">Tüm Blog Yazıları (6) →</a>
        </div>
        <div class="blog-grid-cards">
            <?php foreach ($latestBlogs as $article): ?>
            <article class="blog-card" aria-label="<?= e(($article['title'] ?? null)) ?>">
                <div class="card-media-wrap">
                    <a href="<?= e(url(('blog/' . ($article['slug'] ?? null)))) ?>" aria-label="<?= e(($article['title'] ?? null)) ?> yazısını oku">
                        <?php if (($article['image_path'] ?? null)): ?><?= imageTag(($article['image_path'] ?? null), ($article['image_alt'] ?? null), ['sizes' => '(max-width: 680px) 92vw, 400px']) ?><?php else: ?><span class="blog-editorial-cover"><small>NEXTLAB REHBERİ</small><strong><?= e(($article['category'] ?? null)) ?></strong></span><?php endif; ?>" 
                            width="800" 
                            height="500" 
                            loading="lazy"
                            class="card-cover-img"
                        >
                    </a>
                </div>
                <div class="card-body">
                    <h3 class="card-title">
                        <a href="<?= e(url(('blog/' . ($article['slug'] ?? null)))) ?>"><?= e(($article['title'] ?? null)) ?></a>
                    </h3>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
<section class="faq-section" id="sss" aria-label="Sıkça Sorulan Sorular">
    <div class="container">
        <div class="faq-header">
            <h2 class="faq-title">Sıkça Sorulan Sorular</h2>
        </div>

        <?php $allFaqs = ($faqs ?: []); ?><?php $mid = ((int) ceil((count($allFaqs ?? []) / 2))); ?><?php $leftCol = array_slice($allFaqs, 0, $mid); ?><?php $rightCol = array_slice($allFaqs, $mid); ?>

        <div class="faq-grid-2col" data-faq-accordion>
            <div class="faq-col">
                <?php foreach ($leftCol as $idx => $faq): ?>
                <details class="faq-card-item" <?= e((($idx === 0) ? 'open' : '')) ?>>
                    <summary class="faq-card-summary">
                        <span class="faq-plus-icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#d71920" stroke-width="3.4" stroke-linecap="round">
                                <line x1="12" y1="4" x2="12" y2="20"></line>
                                <line x1="4" y1="12" x2="20" y2="12"></line>
                            </svg>
                        </span>
                        <h3 class="faq-card-title"><?= e(($faq['question'] ?? null)) ?></h3>
                    </summary>
                    <div class="faq-card-body">
                        <p class="faq-card-text"><?= e(($faq['answer'] ?? null)) ?></p>
                    </div>
                </details>
                <?php endforeach; ?>
            </div>
            <div class="faq-col">
                <?php foreach ($rightCol as $faq): ?>
                <details class="faq-card-item">
                    <summary class="faq-card-summary">
                        <span class="faq-plus-icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#d71920" stroke-width="3.4" stroke-linecap="round">
                                <line x1="12" y1="4" x2="12" y2="20"></line>
                                <line x1="4" y1="12" x2="20" y2="12"></line>
                            </svg>
                        </span>
                        <h3 class="faq-card-title"><?= e(($faq['question'] ?? null)) ?></h3>
                    </summary>
                    <div class="faq-card-body">
                        <p class="faq-card-text"><?= e(($faq['answer'] ?? null)) ?></p>
                    </div>
                </details>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<section class="location-section" aria-label="Nextlab konumu"><div class="container location-card"><div><span class="eyebrow dark">İSTANBUL • ÜSKÜDAR</span><h2>İhtiyaçlarınızı birlikte konuşalım.</h2><p>Üniversite Sokak No: 7/B, Işık Plaza<br>Altunizade, Üsküdar / İstanbul</p></div><a class="outline-button" href="https://www.google.com/maps/search/?api=1&amp;query=Nextlab+Universite+Sokak+7%2FB+Altunizade+Uskudar" target="_blank" rel="noopener noreferrer">Haritada Aç ↗</a></div></section>
