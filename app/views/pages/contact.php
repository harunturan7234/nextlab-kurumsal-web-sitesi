<?php /* Generated from views/pages/contact.ejs; retain original layout. */ ?>
<section class="page-hero">
    <div class="container">
        <h1>İletişim</h1>
        <?= view('components/breadcrumb', ['items' => [['label' => 'İletişim']]], get_defined_vars()) ?>
    </div>
</section>

<section class="section">
    <div class="container contact-layout">
        <div class="contact-info">
            <span class="eyebrow dark">BİZE ULAŞIN</span>
            <h2>Ürünlerimiz hakkında konuşalım.</h2>
            <p>Kurumsal talepleriniz, ürün bilgileri, distribütörlük ve toptan tedarik iş ortaklığı sorularınız için bizimle doğrudan iletişime geçebilirsiniz.</p>
            
            <div class="contact-card">
                <span>Merkez Ofis</span>
                <b>Üniversite Sokak No: 7/B, Işık Plaza<br>Altunizade, Üsküdar / İstanbul</b>
            </div>
            <div class="contact-card">
                <span>E-posta</span>
                <a href="mailto:info@nextlabcosmetic.com">info@nextlabcosmetic.com ↗</a>
            </div>
            <div class="contact-card">
                <span>Ürün ve Kurumsal Talepler</span>
                <b>Ürün bilgisi ve tedarik seçenekleri</b>
            </div>
            <div class="contact-card">
                <span>LinkedIn</span>
                <a href="https://www.linkedin.com/company/nextlabcosmetic/" target="_blank" rel="noopener noreferrer">Nextlab Cosmetic ↗</a>
            </div>
            <div class="contact-card">
                <span>Instagram</span>
                <a href="https://www.instagram.com/nextlabcosmetics/" target="_blank" rel="noopener noreferrer">@nextlabcosmetics ↗</a>
            </div>
        </div>

        <form class="contact-form" action="mailto:info@nextlabcosmetic.com" method="post" enctype="text/plain">
            <h2>İletişim Formu</h2>
            <div class="field-row">
                <label>Ad Soyad*<input name="name" required maxlength="100" placeholder="Adınız ve Soyadınız"></label>
                <label>Telefon<input name="phone" type="tel" maxlength="30" placeholder="0 (5XX) XXX XX XX"></label>
            </div>
            <label>E-posta*<input name="email" type="email" required maxlength="190" placeholder="ornek@sirket.com"></label>
            <label>Konu / Talep Türü
                <input name="subject" maxlength="120" placeholder="Ürün Bilgisi, Distribütörlük, B2B Tedarik veya Genel Talep">
            </label>
            <label>Mesajınız*<textarea name="message" rows="5" required maxlength="2000" placeholder="Mesajınızı ve sorularınızı buraya yazınız..."></textarea></label>
            <button class="button" type="submit">Mesajı Gönder</button>
            <small>Bu form cihazınızdaki e-posta uygulamasını açar. Mesajınızı açılan uygulamada göndererek tamamlayın.</small>
        </form>
    </div>
</section>

<?php
$nextlabAddress = 'Üniversite Sokak No: 7/B, Işık Plaza, Altunizade, Üsküdar / İstanbul';
$nextlabMapQ    = 'NextLab Sağlık ve Kozmetik, Altunizade, Üsküdar, İstanbul';
$nextlabMapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($nextlabMapQ);
$nextlabMapEmbed = 'https://www.openstreetmap.org/export/embed.html?bbox=29.0423,41.0184,29.0543,41.0254&layer=mapnik&marker=41.0219,29.0483';
?>
<section class="location-section" aria-label="NextLab konumu">
    <div class="container">
        <div class="location-block">
            <div class="location-title">
                <svg class="location-pin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                <h2>Konumumuz</h2>
            </div>
            <p class="location-address"><?= e($nextlabAddress) ?></p>
            <a class="location-map" href="<?= e($nextlabMapsUrl) ?>" target="_blank" rel="noopener noreferrer" aria-label="Google Haritalar'da konumumuzu açın">
                <iframe title="NextLab konum haritası" src="<?= e($nextlabMapEmbed) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" tabindex="-1"></iframe>
                <span class="location-map-badge">Google Haritalar'da Aç <svg class="location-ext" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg></span>
            </a>
        </div>
    </div>
</section>

