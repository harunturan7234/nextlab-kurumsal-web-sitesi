<?php
/** @var Admin $admin */ /** @var array $e */ /** @var array $rows */
$pageTitle = $e['label'];
$badge = [
    'published' => ['Yayında', 'ok'],
    'modified'  => ['Değişiklik var', 'warn'],
    'draft'     => ['Taslak', 'muted'],
];
ob_start();
?>
<div class="list-head">
  <div class="search-wrap">
    <input type="search" id="q" placeholder="<?= admin_e($e['label']) ?> içinde ara…" aria-label="Ara">
  </div>
  <a class="btn btn-primary" href="/admin/<?= admin_e($e['key']) ?>/yeni">+ Yeni <?= admin_e($e['singular']) ?></a>
</div>

<?php if (!$rows): ?>
  <div class="empty">
    <p>Henüz kayıt yok.</p>
    <a class="btn btn-primary" href="/admin/<?= admin_e($e['key']) ?>/yeni">İlk <?= admin_e($e['singular']) ?>u ekleyin</a>
  </div>
<?php else: ?>
<div class="table-wrap">
<table class="tbl">
  <thead>
    <tr>
      <th class="col-ord" title="Sıra">Sıra</th>
      <?php foreach ($e['list_columns'] as [$col, $label]): ?><th><?= admin_e($label) ?></th><?php endforeach; ?>
      <th>Durum</th>
      <th class="ta-r">İşlem</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($rows as $rowIndex => $r): ?>
    <?php [$bl, $bc] = $badge[$r['status']]; ?>
    <tr data-search="<?= admin_e(strtolower(implode(' ', array_map(fn($c) => (string)Admin::get($r['data'], $c[0], ''), $e['list_columns'])))) ?>">
      <td class="col-ord">
        <div class="ordbtns">
          <form method="post" action="/admin/<?= admin_e($e['key']) ?>/tasi" class="inline">
            <input type="hidden" name="_csrf" value="<?= admin_e($admin->csrfToken()) ?>">
            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><input type="hidden" name="dir" value="up">
            <button type="submit" title="Yukarı taşı" aria-label="Yukarı taşı" <?= $rowIndex === 0 ? 'disabled' : '' ?>>↑</button>
          </form>
          <form method="post" action="/admin/<?= admin_e($e['key']) ?>/tasi" class="inline">
            <input type="hidden" name="_csrf" value="<?= admin_e($admin->csrfToken()) ?>">
            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><input type="hidden" name="dir" value="down">
            <button type="submit" title="Aşağı taşı" aria-label="Aşağı taşı" <?= $rowIndex === count($rows) - 1 ? 'disabled' : '' ?>>↓</button>
          </form>
        </div>
      </td>
      <?php foreach ($e['list_columns'] as $i => [$col, $label]): ?>
        <td>
          <?php if ($i === 0): ?>
            <a class="row-title" href="/admin/<?= admin_e($e['key']) ?>/<?= (int)$r['id'] ?>"><?= admin_e(Admin::get($r['data'], $col, '(adsız)')) ?></a>
          <?php else: ?>
            <span class="muted"><?= admin_e(Admin::get($r['data'], $col, '—')) ?></span>
          <?php endif; ?>
        </td>
      <?php endforeach; ?>
      <td><span class="badge <?= $bc ?>"><?= $bl ?></span></td>
      <td class="ta-r actions">
        <a class="btn btn-sm" href="/admin/<?= admin_e($e['key']) ?>/<?= (int)$r['id'] ?>">Düzenle</a>
        <?php if (!empty($e['public']) && $r['status'] !== 'draft' && ($rs = (string)Admin::get($r['data'], 'slug', '')) !== ''): ?>
          <a class="btn btn-sm" href="/<?= admin_e(sprintf($e['public'], $rs)) ?>" target="_blank" rel="noopener" title="Sayfayı yeni sekmede aç">Gör ↗</a>
        <?php endif; ?>
        <?php if ($r['status'] !== 'published'): ?>
          <form method="post" action="/admin/<?= admin_e($e['key']) ?>/yayinla" class="inline">
            <input type="hidden" name="_csrf" value="<?= admin_e($admin->csrfToken()) ?>">
            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
            <input type="hidden" name="_back" value="/admin/<?= admin_e($e['key']) ?>">
            <button class="btn btn-sm btn-pub" type="submit">Yayına Al</button>
          </form>
        <?php endif; ?>
        <details class="more">
          <summary aria-label="Diğer işlemler">⋯</summary>
          <div class="more-menu">
            <?php if ($r['status'] === 'modified'): ?>
              <form method="post" action="/admin/<?= admin_e($e['key']) ?>/taslagi-sil" data-confirm="Yayınlanmamış değişiklikler geri alınacak. Onaylıyor musunuz?">
                <input type="hidden" name="_csrf" value="<?= admin_e($admin->csrfToken()) ?>">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button type="submit">Değişiklikleri geri al</button>
              </form>
            <?php endif; ?>
            <?php if ($r['status'] !== 'draft'): ?>
              <form method="post" action="/admin/<?= admin_e($e['key']) ?>/yayindan-kaldir" data-confirm="Kayıt siteden kaldırılacak (silinmez). Onaylıyor musunuz?">
                <input type="hidden" name="_csrf" value="<?= admin_e($admin->csrfToken()) ?>">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button type="submit">Yayından kaldır</button>
              </form>
            <?php endif; ?>
            <form method="post" action="/admin/<?= admin_e($e['key']) ?>/sil" data-confirm="Bu kayıt kalıcı olarak silinecek. Onaylıyor musunuz?">
              <input type="hidden" name="_csrf" value="<?= admin_e($admin->csrfToken()) ?>">
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <button type="submit" class="danger">Kalıcı olarak sil</button>
            </form>
          </div>
        </details>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div>
<p class="hint"><strong>Sıralama</strong> oklarla değiştirilir ve <strong>anında</strong> siteye yansır (yayınlamaya gerek yoktur). İçerik düzenlemeleri ise “Yayına Al” denene kadar sitede görünmez.<br> Durum “Değişiklik var” ise site hâlâ eski hâli gösterir; “Yayına Al” deyince güncellenir.</p>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
