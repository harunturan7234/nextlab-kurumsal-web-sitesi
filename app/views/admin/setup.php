<?php /** @var Admin $admin */ ?><!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>İlk Kurulum · Nextlab Yönetim</title>
<link rel="stylesheet" href="/assets/admin/admin.css">
</head>
<body class="auth-body">
<form class="auth-card" method="post" action="/admin/kurulum" autocomplete="off">
  <div class="auth-brand"><span class="side-logo">N</span><div><strong>Nextlab</strong><small>Yönetim Paneli</small></div></div>
  <h1 class="auth-title">İlk yönetici hesabını oluşturun</h1>
  <p class="auth-note">Bu ekran yalnızca hiç yönetici yokken görünür. Hesabı oluşturduktan sonra erişim parolayla korunur.</p>
  <?php if (!empty($error)): ?><div class="flash err"><?= admin_e($error) ?></div><?php endif; ?>
  <input type="hidden" name="_csrf" value="<?= admin_e($admin->csrfToken()) ?>">
  <label class="fld"><span>Ad Soyad</span><input type="text" name="name" required></label>
  <label class="fld"><span>E-posta</span><input type="email" name="email" required></label>
  <label class="fld"><span>Parola</span><input type="password" name="password" required minlength="10" autocomplete="new-password">
    <small class="help">En az 10 karakter. Tahmin edilmesi zor bir parola seçin.</small>
  </label>
  <button class="btn btn-primary full" type="submit">Hesabı Oluştur ve Giriş Yap</button>
</form>
</body>
</html>
