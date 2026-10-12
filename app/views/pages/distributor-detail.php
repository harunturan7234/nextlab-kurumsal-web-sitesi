<?php /* Generated from views/pages/distributor-detail.ejs; retain original layout. */ ?>
<section class="page-hero">
    <div class="container">
        <h1><?= e(($brand['name'] ?? null)) ?></h1>
        <?= view('components/breadcrumb', ['items' => [['label' => 'Markalar ve İş Birlikleri', 'url' => 'distributorlukler'], ['label' => ($brand['name'] ?? null)]]], get_defined_vars()) ?>
    </div>
</section>

<section class="section brand-detail-section">
    <div class="container">
        <div class="brand-detail-layout">
            <div class="brand-detail-main">
                <div class="brand-intro-card">
                    <div class="brand-origin-pill">
                        <span class="flag-dot"></span> <?= e(($brand['origin'] ?? null)) ?> &bull; <?= e(($brand['category'] ?? null)) ?>
                    </div>
                    <h2><?= e(($brand['name'] ?? null)) ?> Marka Bilgileri</h2>
                    <p class="brand-highlight-lead"><?= e(($brand['highlight'] ?? null)) ?></p>
                    <p class="brand-full-desc"><?= e(($brand['description'] ?? null)) ?></p>

                    <p class="image-note">Marka rehberi kaydı, yetkili distribütörlük beyanı değildir. Güncel iş birliği ve tedarik bilgileri için bize ulaşın.</p>
                </div>

                <div class="brand-products-block">
                    <span class="eyebrow dark">ÖNE ÇIKAN SERİLER</span>
                    <h3><?= e(($brand['name'] ?? null)) ?> Popüler Ürün Gamı</h3>
                    <div class="brand-hero-products-grid">
                        <?php foreach (($brand['key_products'] ?? null) as $i => $prod): ?>
                        <div class="prod-showcase-tile">
                            <div class="prod-num">0<?= e(($i + 1)) ?></div>
                            <h4><?= e($prod) ?></h4>
                            <p>Ürün grubunu ve teknik ayrıntıları kataloğumuzdan inceleyebilirsiniz.</p>
                            <span class="prod-tag"><?= e((($brand['badges'] ?? null)[($i % count(($brand['badges'] ?? null) ?? []))] ?? null)) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <aside class="brand-detail-sidebar">
                <div class="b2b-contact-card">
                    <h3>Kurumsal Tedarik</h3>
                    <p><?= e(($brand['name'] ?? null)) ?> ürünlerini eczanenizde, kliniğinizde veya mağazanızda satışa sunmak için toptan sipariş ve bayi fiyat listesi talep edebilirsiniz.</p>

                    <a class="button w-full" href="<?= e(url('iletisim')) ?>">Toptan / Bayilik İletişimi</a>
                </div>

                <div class="other-brands-card">
                    <h4>Diğer Markalar</h4>
                    <ul class="other-brands-list">
                        <?php foreach (array_slice(array_values(array_filter(($allBrands ?: []), fn($b) => (($b['slug'] ?? null) !== ($brand['slug'] ?? null)))), 0, 6) as $other): ?>
                        <li>
                            <a href="<?= e(url(('distributorluk/' . ($other['slug'] ?? null)))) ?>">
                                <strong><?= e(($other['name'] ?? null)) ?></strong>
                                <span><?= e(($other['category'] ?? null)) ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <a class="text-link" href="<?= e(url('distributorlukler')) ?>" style="margin-top: 14px; display: inline-block;">Tüm Markaları Gör →</a>
                </div>
            </aside>
        </div>
    </div>
</section>
