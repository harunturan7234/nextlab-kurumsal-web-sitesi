<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= e($metaDescription ?? 'Nextlab temizlik, hijyen, kozmetik ve kişisel bakım ürünleri.') ?>">
    <meta name="theme-color" content="#173f61">
    <title><?= e(isset($title) ? $title . ' | Nextlab' : 'Nextlab | Temizlik ve Kişisel Bakım Ürünleri') ?></title>
    <link rel="canonical" href="<?= e(url(current_path())) ?>">
    <meta property="og:type" content="<?= e($ogType ?? 'website') ?>">
    <meta property="og:site_name" content="Nextlab Cosmetic">
    <meta property="og:title" content="<?= e(isset($title) ? $title . ' | Nextlab' : 'Nextlab Cosmetic') ?>">
    <meta property="og:description" content="<?= e($metaDescription ?? 'Nextlab ve Everyday ürünlerini keşfedin.') ?>">
    <meta property="og:url" content="<?= e(url(current_path())) ?>">
    <meta property="og:image" content="<?= e($ogImage ?? asset('images/social-share.svg')) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
    <script defer src="<?= e(asset('js/app.js')) ?>"></script>
    <script type="application/ld+json"><?= json_encode($structuredData ?? ['@context'=>'https://schema.org','@type'=>'Organization','name'=>'Nextlab Cosmetic','url'=>base_url(),'sameAs'=>['https://www.instagram.com/nextlabcosmetics/','https://www.linkedin.com/company/nextlabcosmetic/']], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) ?></script>
</head>
<body>
<?php require __DIR__ . '/partials/header.php'; ?>
<main><?= $content ?></main>
<?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
