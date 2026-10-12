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
            <span class="eyebrow dark">NEXTLAB KURUMSAL</span>
            <h2>Azla çok etki, bilimle gerçek çözüm.</h2>
            <p>Nextlab Kozmetik olarak felsefemiz, cilt sağlığına yönelik çözüm odaklı ürünler geliştirmek üzerine kuruludur. 12 yılı aşkın ilaç ve kozmetik tecrübemizle bilimsel araştırma ve klinik veriler ışığında formüller oluşturuyor; tüketici ihtiyaçlarını merkeze alan sade ama etkili içeriklerle sürdürülebilir güzelliği savunuyoruz.</p>
            <p>Kendi markamız Everyday ile günlük kullanıma uygun; paraben, alkol, parfüm ve renklendirici içermeyen ürünler sunuyor, formüllerimizin pH dengesi ve aktif stabilitesiyle maksimum etkiyi hedefliyoruz. Hayvanlar üzerinde test yapmıyor, doğaya saygılı bir üretim anlayışı benimsiyoruz.</p>
            <p>“Az içerikle çok fayda” prensibiyle yalın, anlaşılır ve sonuç odaklı çözümler geliştiriyoruz. Aynı zamanda tüketiciyi eğiten, bilinçli cilt bakımı alışkanlıklarını destekleyen bir bilgi platformu olmayı hedefliyoruz. Çünkü bize göre sağlıklı cilt bir ayrıcalık değil, ulaşılabilir bir haktır.</p>
            <div class="values">
                <div><b>Bilim</b><span>Araştırma ve klinik veri ışığında formüller</span></div>
                <div><b>Sadelik</b><span>Az içerikle çok fayda; yalın ve etkili</span></div>
                <div><b>Sorumluluk</b><span>Hayvan deneyi yok, doğaya saygılı üretim</span></div>
            </div>
        </div>
    </div>
</section>
