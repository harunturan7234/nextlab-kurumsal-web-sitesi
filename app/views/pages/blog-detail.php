<?php /* Generated from views/pages/blog-detail.ejs; retain original layout. */ ?>
<article class="blog-detail-article" itemscope itemtype="https://schema.org/BlogPosting">
    <!-- Breadcrumb -->
    <nav class="container blog-breadcrumb" aria-label="Sayfa yolu">
        <a href="<?= e(url('')) ?>">Anasayfa</a>
        <span class="bc-sep">/</span>
        <a href="<?= e(url('blog')) ?>">Blog</a>
        <span class="bc-sep">/</span>
        <span class="bc-current" aria-current="page"><?= e(($article['title'] ?? null)) ?></span>
    </nav>

    <!-- Article Header -->
    <header class="blog-detail-header">
        <div class="container blog-header-inner">
            <div class="article-badge-row">
                <span class="detail-badge"><?= e(($article['badge'] ?? null)) ?></span>
                <span class="detail-readtime"><?= e(($article['readTime'] ?? null)) ?></span>
            </div>

            <h1 class="detail-title" itemprop="headline"><?= e(($article['title'] ?? null)) ?></h1>
            <p class="detail-subtitle" itemprop="description"><?= e(($article['subtitle'] ?? null)) ?></p>

            <div class="detail-author-bar">
                <div class="author-meta-left" itemprop="author" itemscope itemtype="https://schema.org/Person">
                    <span class="author-big-avatar" aria-hidden="true"><?= e((($article['author'] ?? null)['initials'] ?? null)) ?></span>
                    <div>
                        <strong itemprop="name"><?= e((($article['author'] ?? null)['name'] ?? null)) ?></strong>
                        <span><?= e((($article['author'] ?? null)['role'] ?? null)) ?></span>
                    </div>
                </div>
                <div class="article-date-meta">
                    <span class="meta-label">Yayın Tarihi:</span>
                    <time itemprop="datePublished" datetime="<?= e(($article['isoDate'] ?? null)) ?>"><?= e(($article['publishedAt'] ?? null)) ?></time>
                </div>
            </div>

        </div>
    </header>

    <!-- Main Visual Cover Image -->
    <?php if (($article['image_path'] ?? null)): ?>
    <div class="container blog-cover-container">
        <div class="detail-cover-figure">
            <?= imageTag(($article['image_path'] ?? null), ($article['image_alt'] ?? null), ['sizes' => '(max-width: 680px) 92vw, 720px', 'priority' => true]) ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Article Content Grid -->
    <div class="container detail-content-layout">
        <!-- Main Article Content Body -->
        <div class="detail-main-prose" itemprop="articleBody">
            <?php if (($article['highlightQuote'] ?? null)): ?>
            <blockquote class="editorial-pullquote">
                <p>“<?= e(($article['highlightQuote'] ?? null)) ?>”</p>
                <cite>— <?= e((($article['author'] ?? null)['name'] ?? null)) ?></cite>
            </blockquote>
            <?php endif; ?>

            <?php if ((($article['summaryPoints'] ?? null) && (count(($article['summaryPoints'] ?? null) ?? []) > 0))): ?>
            <div class="summary-keypoints-box">
                <div class="keypoints-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Rehber Özeti &amp; Öne Çıkan Bulgular
                </div>
                <ul>
                    <?php foreach (($article['summaryPoints'] ?? null) as $pt): ?>
                    <li><?= e($pt) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <!-- Raw Editorial Body with Embedded Keyword Hyperlinks -->
            <div class="prose-body-content">
                <?= ($article['contentHtml'] ?? null) ?>
            </div>

            <!-- Bottom Interactive Keywords Block -->
            <div class="article-bottom-tags-section">
                <span class="bot-tag-title">Yazıda Geçen Konular &amp; Anahtar Kelimeler:</span>
                <div class="bot-tags-wrap">
                    <?php foreach ((($article['keywords'] ?? null) ?: []) as $kw): ?>
                    <a href="<?= e(url(($kw['url'] ?? null))) ?>" class="bot-tag-link">
                        <?= e(($kw['label'] ?? null)) ?> <span>↗</span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Author Card -->
            <div class="author-full-card">
                <span class="author-large-avatar"><?= e((($article['author'] ?? null)['initials'] ?? null)) ?></span>
                <div class="author-full-info">
                    <h4><?= e((($article['author'] ?? null)['name'] ?? null)) ?></h4>
                    <p class="author-role-title"><?= e((($article['author'] ?? null)['role'] ?? null)) ?></p>
                    <p class="author-bio">Nextlab ürün kataloğundaki ürün gruplarını ve satış kanallarını tanıtan kurumsal içerikler.</p>
                </div>
            </div>
        </div>

        <!-- Sidebar (Sticky Brand Showcase & Quick Nav) -->
        <aside class="detail-sidebar" aria-label="İlgili markalar ve içerikler">
            <!-- Brand Links Card -->
            <div class="sidebar-card sidebar-distributor-card">
                <span class="card-eyebrow">ÜRÜN GRUPLARI</span><h3>Kataloğu Keşfedin</h3><div class="sidebar-brand-links"><a class="sb-brand-row" href="<?= e(url('kategori/temizlik-ve-hijyen')) ?>">Temizlik ve Hijyen ↗</a><a class="sb-brand-row" href="<?= e(url('kategori/kozmetik-ve-kisisel-bakim')) ?>">Kozmetik ve Kişisel Bakım ↗</a><a class="sb-brand-row" href="<?= e(url('kategori/ortam-kokulandirma')) ?>">Ortam Kokulandırma ↗</a></div>
            </div>

            <!-- FAQ / Support Shortcut -->
            <div class="sidebar-card sidebar-help-card">
                <span class="card-eyebrow">DESTEK</span>
                <h3>Sıkça Sorulan Sorular</h3>
                <p>Ürünler, kullanım bilgisi ve mağazalarımız hakkındaki sorularınızı inceleyin.</p>
                <a href="<?= e(url('sss')) ?>" class="outline-button full-btn">S.S.S Sayfasına Git</a>
            </div>

            <!-- Contact / Consultation -->
            <div class="sidebar-card sidebar-consult-card">
                <span class="card-eyebrow">KURUMSAL TALEP</span>
                <h3>B2B Tedarik &amp; Bayilik</h3>
                <p>Eczane, klinik ve toptan ürün tedariği için kurumsal satış ekibimizle görüşün.</p>
                <a href="<?= e(url('iletisim')) ?>" class="primary-button full-btn">İletişim Formu →</a>
            </div>
        </aside>
    </div>

    <!-- Related Articles -->
    <?php if (($relatedArticles && (count($relatedArticles ?? []) > 0))): ?>
    <section class="section related-blogs-section">
        <div class="container">
            <div class="section-heading">
                <span>DİĞER REHBERLER</span>
                <h2>İlginizi Çekebilecek Yazılar</h2>
            </div>
            <div class="blog-grid-cards">
                <?php foreach ($relatedArticles as $rel): ?>
                <article class="blog-card">
                    <div class="card-media-wrap">
                        <a href="<?= e(url(('blog/' . ($rel['slug'] ?? null)))) ?>">
                            <?php if (($rel['image_path'] ?? null)): ?><?= imageTag(($rel['image_path'] ?? null), ($rel['image_alt'] ?? null), ['sizes' => '(max-width: 680px) 92vw, 360px']) ?><?php else: ?><span class="blog-editorial-cover"><small>NEXTLAB REHBERİ</small><strong><?= e(($rel['category'] ?? null)) ?></strong></span><?php endif; ?>
                        </a>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">
                            <a href="<?= e(url(('blog/' . ($rel['slug'] ?? null)))) ?>"><?= e(($rel['title'] ?? null)) ?></a>
                        </h3>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
</article>
