<?php
/** @var Admin $admin */
$pageTitle = 'Yedek';
$activeKey = 'yedek';
ob_start();
?>
<section class="panel">
  <h3>Yedek al</h3>
  <p class="muted">Tüm ürün, kategori, blog, banner, S.S.S, marka ve ayar içeriğini
     (yayındaki ve taslak hâlleriyle) tek bir dosyaya indirir. Büyük bir değişiklik
     yapmadan önce yedek almanız önerilir.</p>
  <a class="btn btn-primary" href="/admin/yedek/indir">Yedeği İndir (.json)</a>
</section>

<section class="panel">
  <h3>Yedekten geri yükle</h3>
  <div class="flash err" style="margin-bottom:14px">
    <strong>Dikkat:</strong> Geri yükleme mevcut tüm içeriğin yerine yedektekini koyar.
    Bu işlem geri alınamaz. Yöneticiler, parolalar ve yönlendirmeler etkilenmez.
  </div>
  <form method="post" action="/admin/yedek" enctype="multipart/form-data" class="upload-row"
        data-confirm="Mevcut TÜM içerik silinip yedektekiyle değiştirilecek. Bu işlem geri alınamaz. Devam edilsin mi?">
    <input type="hidden" name="_csrf" value="<?= admin_e($admin->csrfToken()) ?>">
    <input type="file" name="backup" accept="application/json,.json" required>
    <button class="btn" type="submit">Geri Yükle</button>
  </form>
  <small class="help">Yalnızca bu panelden indirilmiş yedek dosyalarını kullanın.</small>
</section>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
