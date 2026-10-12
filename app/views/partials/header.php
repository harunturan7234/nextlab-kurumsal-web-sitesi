<?php /* Generated from views/partials/header.ejs; retain original layout. */ ?>
<div class="topbar">
    <div class="container topbar-inner">
        <div class="social-links" aria-label="Sosyal medya">
            <a class="social-icon instagram-icon" href="https://www.instagram.com/nextlabcosmetics/" target="_blank" rel="noopener noreferrer" aria-label="Nextlab Instagram">
                <svg viewBox="0 0 24 24" aria-hidden="true"><defs><linearGradient id="instagram-header" x1="0" y1="1" x2="1" y2="0"><stop offset="0" stop-color="#ffd600"/><stop offset=".45" stop-color="#ff0169"/><stop offset="1" stop-color="#7638fa"/></linearGradient></defs><rect x="2" y="2" width="20" height="20" rx="6" fill="url(#instagram-header)"/><circle cx="12" cy="12" r="4.2" fill="none" stroke="#fff" stroke-width="2"/><circle cx="17.4" cy="6.7" r="1.2" fill="#fff"/></svg>
            </a>
            <a class="social-icon linkedin-icon" href="https://www.linkedin.com/company/nextlabcosmetic/" target="_blank" rel="noopener noreferrer" aria-label="Nextlab LinkedIn">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect width="24" height="24" rx="3" fill="#0a66c2"/><circle cx="6.5" cy="7" r="1.5" fill="#fff"/><path fill="#fff" d="M5.15 9.4h2.7V19h-2.7zm4.4 0h2.58v1.31h.04c.36-.68 1.24-1.66 2.55-1.66 2.73 0 3.23 1.8 3.23 4.13V19h-2.69v-5.16c0-1.23-.02-2.82-1.72-2.82-1.72 0-1.98 1.34-1.98 2.73V19H9.55z"/></svg>
            </a>
        </div>
        <div class="marketplace-links" aria-label="Satış kanalları">
            <a class="marketplace-icon trendyol-market" href="https://www.trendyol.com/magaza/nextlab-saglik-ve-kozmetik-m-1110408?sst=0" target="_blank" rel="noopener noreferrer sponsored" aria-label="Nextlab Trendyol mağazası">
                <img src="<?= e(asset('images/verified/trendyol-icon-80.webp')) ?>" alt="" width="447" height="447" aria-hidden="true">
            </a>
            <a class="marketplace-icon hepsiburada-market" href="https://www.hepsiburada.com/magaza/nextlab-saglik-ve-kozmetik-urunleri" target="_blank" rel="noopener noreferrer sponsored" aria-label="Nextlab Hepsiburada mağazası">
                <img src="<?= e(asset('images/verified/hepsiburada-icon-80.webp')) ?>" alt="" width="447" height="447" aria-hidden="true">
            </a>
        </div>
    </div>
</div>
<header class="site-header">
    <div class="container header-inner">
        <a class="logo" href="<?= e(url('')) ?>" aria-label="Nextlab ana sayfa"><img class="brand-logo-image" src="<?= e(asset('images/verified/nextlab-logo-380.webp')) ?>" alt="NextLab" width="1587" height="712"></a>
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="main-nav"><span></span><span></span><span></span><em>Menü</em></button>
        <nav id="main-nav" class="main-nav" aria-label="Ana menü">
            <a class="<?= e(((isset($page) && ($page === 'home')) ? 'active' : '')) ?>" href="<?= e(url('')) ?>">Anasayfa</a>
            
            <div class="nav-dropdown">
                <a class="<?= e(((isset($page) && ((($page === 'products') ?: ($page === 'category')) ?: ($page === 'product'))) ? 'active' : '')) ?>" href="<?= e(url('urunler')) ?>">Ürünler <span>⌄</span></a>
                <div class="dropdown-menu">
                    <a href="<?= e(url('kategori/temizlik-ve-hijyen')) ?>">Temizlik ve Hijyen</a>
                    <a href="<?= e(url('kategori/kozmetik-ve-kisisel-bakim')) ?>">Kozmetik ve Kişisel Bakım</a>
                    <a href="<?= e(url('urunler')) ?>">Tüm Ürünler</a>
                </div>
            </div>

            <div class="nav-dropdown">
                <a class="<?= e(((isset($page) && ($page === 'distributors')) ? 'active' : '')) ?>" href="<?= e(url('distributorlukler')) ?>">Markalar <span>⌄</span></a>
                <div class="dropdown-menu dropdown-menu-brands">
                    <?php foreach (($distributorBrands ?: []) as $brand): ?>
                    <a href="<?= e(url(('distributorluk/' . ($brand['slug'] ?? null)))) ?>"><?= e(($brand['name'] ?? null)) ?></a>
                    <?php endforeach; ?>
                    <a href="<?= e(url('distributorlukler')) ?>">Markalar ve İş Birlikleri</a>
                </div>
            </div>

            <div class="nav-dropdown">
                <a class="<?= e(((isset($page) && (((($page === 'corporate') ?: ($page === 'about')) ?: ($page === 'faq')) ?: ($page === 'cookie-policy'))) ? 'active' : '')) ?>" href="<?= e(url('kurumsal')) ?>">Kurumsal <span>⌄</span></a>
                <div class="dropdown-menu">
                    <a href="<?= e(url('hakkimizda')) ?>">Hakkımızda</a>
                    <a href="<?= e(url('sss')) ?>">S.S.S</a>
                    <a href="<?= e(url('cerez-politikasi')) ?>">Çerez Politikası</a>
                </div>
            </div>

            <a class="<?= e(((isset($page) && (($page === 'blog') ?: ($page === 'blog-detail'))) ? 'active' : '')) ?>" href="<?= e(url('blog')) ?>">Blog</a>

            <a class="<?= e(((isset($page) && ($page === 'contact')) ? 'active' : '')) ?>" href="<?= e(url('iletisim')) ?>">İletişim</a>
        </nav>
        <a class="header-search" href="<?= e(url('arama')) ?>" aria-label="Ürün ara"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="10" cy="10" r="6.5"/><path d="m15 15 5 5"/></svg><span>Ürün ara</span></a>
    </div>
</header>
