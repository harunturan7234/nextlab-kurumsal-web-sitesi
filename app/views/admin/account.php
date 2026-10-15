<?php
/** @var Admin $admin */ /** @var array $user */ /** @var array $admins */
$pageTitle = 'Hesabım';
$activeKey = 'hesap';
ob_start();
?>
<div class="acct-grid">
  <section class="panel">
    <h3>Hesap bilgileriniz</h3>
    <form method="post" action="/admin/hesap">
      <input type="hidden" name="_csrf" value="<?= admin_e($admin->csrfToken()) ?>">
      <input type="hidden" name="what" value="profil">
      <label class="fld"><span>Ad Soyad</span>
        <input type="text" name="name" value="<?= admin_e($user['name'] ?? '') ?>" required></label>
      <label class="fld"><span>E-posta</span>
        <input type="email" name="email" value="<?= admin_e($user['email'] ?? '') ?>" required>
        <small class="help">Panele bu adresle giriş yaparsınız.</small></label>
      <button class="btn btn-primary" type="submit">Bilgileri Güncelle</button>
    </form>
  </section>

  <section class="panel">
    <h3>Parola değiştir</h3>
    <form method="post" action="/admin/hesap" autocomplete="off">
      <input type="hidden" name="_csrf" value="<?= admin_e($admin->csrfToken()) ?>">
      <input type="hidden" name="what" value="parola">
      <label class="fld"><span>Mevcut parolanız</span>
        <input type="password" name="current" required autocomplete="current-password"></label>
      <label class="fld"><span>Yeni parola</span>
        <input type="password" name="new" minlength="10" required autocomplete="new-password">
        <small class="help">En az 10 karakter.</small></label>
      <label class="fld"><span>Yeni parola (tekrar)</span>
        <input type="password" name="again" minlength="10" required autocomplete="new-password"></label>
      <button class="btn btn-primary" type="submit">Parolayı Değiştir</button>
    </form>
  </section>
</div>

<section class="panel">
  <h3>Yöneticiler <span class="muted">(<?= count($admins) ?>)</span></h3>
  <div class="table-wrap">
    <table class="tbl">
      <thead><tr><th>Ad</th><th>E-posta</th><th>Son giriş</th><th class="ta-r">İşlem</th></tr></thead>
      <tbody>
      <?php foreach ($admins as $a): ?>
        <tr>
          <td>
            <strong><?= admin_e($a['name']) ?></strong>
            <?php if ((int)$a['id'] === (int)$user['id']): ?><span class="badge ok">siz</span><?php endif; ?>
          </td>
          <td class="muted"><?= admin_e($a['email']) ?></td>
          <td class="muted"><?= admin_e($a['last_login_at'] ? date('d.m.Y H:i', strtotime($a['last_login_at'])) : 'hiç') ?></td>
          <td class="ta-r">
            <?php if ((int)$a['id'] !== (int)$user['id'] && count($admins) > 1): ?>
              <form method="post" action="/admin/hesap" class="inline" data-confirm="Bu yönetici hesabı kalıcı olarak silinecek. Onaylıyor musunuz?">
                <input type="hidden" name="_csrf" value="<?= admin_e($admin->csrfToken()) ?>">
                <input type="hidden" name="what" value="sil">
                <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                <button class="btn btn-sm" type="submit">Sil</button>
              </form>
            <?php else: ?><span class="muted">—</span><?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<section class="panel">
  <h3>Yeni yönetici ekle</h3>
  <form method="post" action="/admin/hesap" autocomplete="off" class="acct-add">
    <input type="hidden" name="_csrf" value="<?= admin_e($admin->csrfToken()) ?>">
    <input type="hidden" name="what" value="ekle">
    <label class="fld"><span>Ad Soyad</span><input type="text" name="name" required></label>
    <label class="fld"><span>E-posta</span><input type="email" name="email" required></label>
    <label class="fld"><span>Parola</span><input type="password" name="password" minlength="10" required autocomplete="new-password"></label>
    <button class="btn btn-primary" type="submit">Yönetici Ekle</button>
  </form>
  <small class="help">Eklenen kişi panelin tamamına erişebilir. Yalnızca güvendiğiniz kişileri ekleyin.</small>
</section>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
