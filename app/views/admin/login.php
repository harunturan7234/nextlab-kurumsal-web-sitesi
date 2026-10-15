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

  <?php if (!empty($demo)): ?>
    <div class="demo-box">
      <div class="demo-head">
        <strong>Deneme bilgileri</strong>
        <span class="demo-tag">yalnızca yerel</span>
      </div>
      <dl class="demo-list">
        <dt>E-posta</dt><dd><code><?= admin_e($demo['email']) ?></code></dd>
        <dt>Parola</dt><dd><code><?= admin_e($demo['password']) ?></code></dd>
      </dl>
      <button type="button" class="btn btn-sm full" id="demoFill">Bilgileri doldur</button>
      <small class="demo-note">Bu kutu yalnızca 127.0.0.1 / localhost üzerinde görünür.
        Canlı sitede <strong>asla</strong> gösterilmez.</small>
    </div>
    <script>
      document.getElementById('demoFill').addEventListener('click', function () {
        var f = this.closest('form');
        f.querySelector('[name=email]').value = <?= json_encode($demo['email'], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
        f.querySelector('[name=password]').value = <?= json_encode($demo['password'], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
      });
    </script>
  <?php endif; ?>

  <p class="auth-foot"><a href="/">← Siteye dön</a></p>
</form>
</body>
</html>
