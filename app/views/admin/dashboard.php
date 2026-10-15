<?php
/** @var Admin $admin */ /** @var array $entities */
$pageTitle = 'Pano';
$activeKey = '';
$counts = [];
foreach ($entities as $k => $ent) {
    if (!empty($ent['single'])) { $counts[$k] = null; continue; }
    $counts[$k] = count($admin->all($ent));
}
$pending = $admin->pendingCount();
ob_start();
?>
<section class="hero-card">
  <div>
    <h2>Merhaba <?= admin_e(explode(' ', (string)$user['name'])[0]) ?> 👋</h2>
    <p>Sitenin tüm içeriğini buradan yönetebilirsiniz. Yaptığınız değişiklikler önce <strong>taslak</strong> olarak kaydedilir; <strong>“Yayına Al”</strong> dediğinizde anında siteye yansır.</p>
  </div>
  <?php if ($pending > 0): ?>
    <form method="post" action="/admin/yayinla-tumu" data-confirm="Tüm bekleyen değişiklikler siteye yansıtılacak. Onaylıyor musunuz?">
      <input type="hidden" name="_csrf" value="<?= admin_e($admin->csrfToken()) ?>">
      <input type="hidden" name="_back" value="/admin">
      <button class="btn btn-pub lg" type="submit"><?= (int)$pending ?> değişikliği yayına al</button>
    </form>
  <?php else: ?>
    <span class="live big">● Site güncel — bekleyen değişiklik yok</span>
  <?php endif; ?>
</section>

<div class="grid-cards">
  <?php foreach ($entities as $k => $ent): ?>
    <a class="card" href="/admin/<?= admin_e($k) ?>">
      <span class="card-label"><?= admin_e($ent['label']) ?></span>
      <span class="card-num"><?= $counts[$k] === null ? '—' : (int)$counts[$k] ?></span>
      <span class="card-go">Yönet →</span>
    </a>
  <?php endforeach; ?>
</div>

<section class="panel">
  <h3>Nasıl çalışır?</h3>
  <ol class="howto">
    <li><strong>Düzenleyin.</strong> İstediğiniz bölüme girip içeriği değiştirin veya yeni kayıt ekleyin.</li>
    <li><strong>Kaydedin.</strong> “Taslağa Kaydet” değişikliği saklar ama siteyi <em>değiştirmez</em>.</li>
    <li><strong>Yayına alın.</strong> “Kaydet ve Yayına Al” ya da üstteki “Tümünü Yayına Al” ile değişiklik <em>anında</em> sitede görünür.</li>
  </ol>
</section>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
