<?php
/** @var Admin $admin */ /** @var array $files */
$pageTitle = 'Görseller';
$activeKey = 'medya';
ob_start();
?>
<section class="panel">
  <h3>Yeni görsel yükle</h3>
  <form method="post" action="/admin/medya" enctype="multipart/form-data" class="upload-row">
    <input type="hidden" name="_csrf" value="<?= admin_e($admin->csrfToken()) ?>">
    <input type="file" name="file" accept="image/jpeg,image/png,image/webp,image/gif" required>
    <button class="btn btn-primary" type="submit">Yükle</button>
  </form>
  <small class="help">JPG, PNG, WEBP veya GIF · en fazla 6 MB. Yüklenen görselleri ürün, blog, kategori ve banner formlarında “Kitaplıktan seç” ile kullanabilirsiniz.</small>
</section>

<section class="panel">
  <h3>Kitaplık <span class="muted">(<?= count($files) ?>)</span></h3>
  <?php if (!$files): ?>
    <p class="muted">Henüz görsel yüklenmedi.</p>
  <?php else: ?>
    <div class="media-grid">
      <?php foreach ($files as $f): ?>
        <figure class="media-item">
          <img src="/assets/<?= admin_e($f) ?>" alt="" loading="lazy">
          <figcaption>
            <code><?= admin_e(basename($f)) ?></code>
            <button type="button" class="btn btn-sm copy" data-copy="<?= admin_e($f) ?>">Yolu kopyala</button>
          </figcaption>
        </figure>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
