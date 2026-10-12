<?php /* Generated from views/pages/media.ejs; retain original layout. */ ?>
<section class="page-hero">
    <div class="container">
        <h1>Medya</h1>
        <?= view('components/breadcrumb', ['items' => [['label' => 'Medya']]], get_defined_vars()) ?>
    </div>
</section>

<section class="section media-section">
    <div class="container">
        <div class="section-heading">
            <span>BASIN & MEDYA ODASI</span>
            <h2>Nextlab Medya ve Duyurular</h2>
            <p>Marka kimliği materyalleri, resmî duyurular, yüksek çözünürlüklü ürün görselleri ve dijital kataloglar.</p>
        </div>

        <div class="media-grid">
            <article class="media-card">
                <div class="media-card-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#d71920" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                </div>
                <h3>Dijital Ürün Kataloğu</h3>
                <p>Everyday cilt bakım serisi ve Nextlab ortam kokulandırma ürünlerinin güncel teknik özellikleri ve ambalaj detayları.</p>
                <a class="text-link" href="<?= e(url('katalog')) ?>">Kataloğu Görüntüle <span>→</span></a>
            </article>

            <article class="media-card">
                <div class="media-card-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#d71920" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                </div>
                <h3>Logo & Marka Kiti</h3>
                <p>Yüksek çözünürlüklü Nextlab ve Everyday logoları, vektörel amblem dosyaları ve kurumsal renk kodları kılavuzu.</p>
                <a class="text-link" href="<?= e(asset('images/nextlab-logo-hq.png')) ?>" target="_blank" rel="noopener">HQ Logo İndir (PNG) <span>↗</span></a>
            </article>

            <article class="media-card">
                <div class="media-card-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#d71920" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                </div>
                <h3>Sosyal Medya Kanalları</h3>
                <p>Nextlab Cosmetics resmî Instagram ve LinkedIn hesapları üzerinden paylaşılan en yeni ürün lansmanları ve içerikler.</p>
                <div class="media-social-links">
                    <a href="https://www.instagram.com/nextlabcosmetics/" target="_blank" rel="noopener noreferrer" class="social-tag">Instagram ↗</a>
                    <a href="https://www.linkedin.com/company/nextlabcosmetic/" target="_blank" rel="noopener noreferrer" class="social-tag">LinkedIn ↗</a>
                </div>
            </article>
        </div>

        <div class="media-press-box">
            <div>
                <span class="eyebrow dark">BASIN İLETİŞİMİ</span>
                <h3>Basın ve Röportaj Talepleri</h3>
                <p>Basın bültenleri, editoryal çalışmalar ve kurumsal röportaj talepleriniz için iletişim departmanımızla irtibata geçebilirsiniz.</p>
            </div>
            <a class="button" href="<?= e(url('iletisim')) ?>">Basın İletişimi</a>
        </div>
    </div>
</section>
