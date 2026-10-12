<?php /* Generated from views/pages/distributors.ejs; retain original layout. */ ?>
<section class="page-hero">
    <div class="container">
        <h1>Markalar ve İş Birlikleri</h1>
        <?= view('components/breadcrumb', ['items' => [['label' => 'Markalar ve İş Birlikleri']]], get_defined_vars()) ?>
    </div>
</section>

<section class="section distributors-page">
    <div class="container">
        <div class="distributors-header-row">
            <div>
                <span class="eyebrow dark">MARKA REHBERİ</span>
                <h2>Markalar ve Kurumsal Tedarik</h2>
                <p>Ceymop temizlik ekipmanlarını kataloğumuzdan inceleyebilirsiniz. Diğer markalar bilgi amaçlı listelenir; bu liste yetkili distribütörlük beyanı değildir. Güncel tedarik ve iş birliği bilgisi için bize ulaşın.</p>
            </div>
            <div class="distributors-stat-pill">
                <strong><?= e(count($brands ?? [])) ?></strong>
                <span>Marka Rehberi</span>
            </div>
        </div>

        <div class="distributor-grid">
            <?php foreach (($brands ?: []) as $brand): ?>
            <article class="distributor-card" id="<?= e(($brand['slug'] ?? null)) ?>">
                <div class="dist-card-top">
                    <div class="dist-brand-meta">
                        <span class="dist-origin-badge">
                            <span class="flag-dot"></span> <?= e(($brand['origin'] ?? null)) ?>
                        </span>
                        <span class="dist-cat-badge"><?= e(($brand['category'] ?? null)) ?></span>
                    </div>
                    <h3 class="dist-brand-name"><?= e(($brand['name'] ?? null)) ?></h3>
                    <p class="dist-highlight"><?= e(($brand['highlight'] ?? null)) ?></p>
                </div>
                <div class="dist-card-body">
                    <p class="dist-desc"><?= e(($brand['description'] ?? null)) ?></p>
                    <div class="dist-badges-list">
                        <?php foreach (($brand['badges'] ?? null) as $b): ?>
                        <span class="dist-tag"><?= e($b) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div class="dist-hero-products">
                        <small>Öne Çıkan Ürünler:</small>
                        <ul>
                            <?php foreach (($brand['key_products'] ?? null) as $p): ?>
                            <li><?= e($p) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="dist-card-footer">
                    <a class="button dist-btn" href="<?= e(url(('distributorluk/' . ($brand['slug'] ?? null)))) ?>">Markayı Keşfet →</a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <div class="distributor-partnership-box">
            <div class="partner-copy">
                <span class="eyebrow dark" style="color: #fca5a5;">B2B & BAYİLİK</span>
                <h3>Distribütörlük ve Toptan Tedarik Talepleri</h3>
                <p>Ürün grubu ve ihtiyaç miktarınızı paylaşarak güncel tedarik seçeneklerini kurumsal ekibimizden öğrenebilirsiniz.</p>
            </div>
            <div class="partner-action">
                <a class="button light" href="<?= e(url('iletisim')) ?>">Bayilik Başvurusu Yap</a>
            </div>
        </div>
    </div>
</section>
