<?php /* Generated from views/layout.ejs; retain original layout. */ ?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= e(($metaDescription ?: 'Nextlab temizlik, hijyen, kozmetik ve kişisel bakım ürünleri.')) ?>">
    <meta name="theme-color" content="#d71920">
    <title><?= e(($title ? ('' . $title . ' | Nextlab') : 'Nextlab | Temizlik ve Kişisel Bakım Ürünleri')) ?></title>
    <link rel="canonical" href="<?= e(($canonicalUrl ?: fullUrl($currentPath))) ?>">
    <meta property="og:type" content="<?= e(($ogType ?: 'website')) ?>">
    <meta property="og:site_name" content="Nextlab Cosmetic">
    <meta property="og:title" content="<?= e(($title ? ('' . $title . ' | Nextlab') : 'Nextlab Cosmetic')) ?>">
    <meta property="og:description" content="<?= e(($metaDescription ?: 'Nextlab ve Everyday ürünlerini keşfedin.')) ?>">
    <meta property="og:url" content="<?= e(fullUrl($currentPath)) ?>">
    <meta property="og:image" content="<?= e(($ogImage ?: fullUrl('assets/images/verified/nextlab-logo-1200.webp'))) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta property="og:locale" content="tr_TR">
    <link rel="stylesheet" href="<?= e(assetV('css/style.css')) ?>">
    <link rel="stylesheet" href="<?= e(assetV('css/professional.css')) ?>">
    <?php if ((($page === 'search') ?: ($page === '404'))): ?><meta name="robots" content="noindex,follow"><?php endif; ?>
    <script defer src="<?= e(assetV('js/app.js')) ?>"></script>
    <script type="application/ld+json"><?= json_encode(($structuredData ?: ['@context' => 'https://schema.org', '@type' => 'Organization', 'name' => 'Nextlab Cosmetic', 'url' => fullUrl(''), 'logo' => fullUrl('assets/images/verified/nextlab-logo-1200.webp'), 'address' => ['@type' => 'PostalAddress', 'streetAddress' => 'Üniversite Sokak No: 7/B, Işık Plaza, Altunizade', 'addressLocality' => 'Üsküdar', 'addressRegion' => 'İstanbul', 'addressCountry' => 'TR'], 'sameAs' => ['https://www.facebook.com/nextlabcosmetics', 'https://www.instagram.com/nextlabcosmetics/', 'https://www.linkedin.com/company/nextlabcosmetic/']]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
</head>
<body>
<a class="skip-link" href="#main-content">İçeriğe geç</a>
<?= view('partials/header', [], get_defined_vars()) ?>
<main id="main-content"><?= $content ?></main>
<?= view('partials/footer', [], get_defined_vars()) ?>
<?= view('partials/chatbot', [], get_defined_vars()) ?>
</body>
</html>
