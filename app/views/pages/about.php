<?php /* Generated from views/pages/about.ejs; retain original layout. */ ?>
<section class="page-hero">
    <div class="container">
        <h1>Kurumsal</h1>
        <?= view('components/breadcrumb', ['items' => [['label' => 'Kurumsal']]], get_defined_vars()) ?>
    </div>
</section>

<section class="section">
    <div class="container content-split">
        <div class="content-visual about-visual">
            <img src="<?= e(asset('images/about-nextlab.jpg')) ?>" alt="NextLab yeni nesil cilt bakımı ürünleri" width="1000" height="563" loading="lazy" decoding="async">
        </div>
        <div class="prose">
            <span class="eyebrow dark"><?= e(setting('about.eyebrow', 'NEXTLAB KURUMSAL')) ?></span>
            <h2><?= e(setting('about.title', 'Azla çok etki, bilimle gerçek çözüm.')) ?></h2>
            <?php foreach ((array) setting('about.paragraphs', []) as $aboutParagraph): ?>
            <p><?= e($aboutParagraph) ?></p>
            <?php endforeach; ?>
        </div>
    </div>
</section>
