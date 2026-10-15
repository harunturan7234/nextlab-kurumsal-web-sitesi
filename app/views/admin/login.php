<?php /** @var Admin $admin */ ?><!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Giriş · Nextlab Yönetim</title>
<link rel="stylesheet" href="/assets/admin/admin.css">
</head>
<body class="auth-body">
<form class="auth-card" method="post" action="/admin/giris" autocomplete="on">
  <div class="auth-brand"><span class="side-logo">N</span><div><strong>Nextlab</strong><small>Yönetim Paneli</small></div></div>
  <h1 class="auth-title">Giriş yapın</h1>
  <?php if (!empty($error)): ?><div class="flash err"><?= admin_e($error) ?></div><?php endif; ?>
  <input type="hidden" name="_csrf" value="<?= admin_e($admin->csrfToken()) ?>">
  <label class="fld">
    <span>E-posta</span>
    <input type="email" name="email" required autocomplete="username" autofocus>
  </label>
  <label class="fld">
    <span>Parola</span>
    <input type="password" name="password" required autocomplete="current-password">
  </label>
  <button class="btn btn-primary full" type="submit">Giriş Yap</button>
  <p class="auth-foot"><a href="/">← Siteye dön</a></p>
</form>
</body>
</html>
