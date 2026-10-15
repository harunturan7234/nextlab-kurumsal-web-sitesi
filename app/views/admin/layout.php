<?php
/** @var Admin $admin */
/** @var array $user */
/** @var string $content */
$pending = $admin->pendingCount();
$entities = Admin::entities();
$currentKey = $e['key'] ?? ($activeKey ?? '');
$flashes = $_SESSION['flash'] ?? [];
unset($_SESSION['flash']);
$icons = [
    'box' => '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>',
    'folder' => '<path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>',
    'doc' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
    'image' => '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>',
    'help' => '<circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/>',
    'tag' => '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>',
    'cart' => '<circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>',
    'cog' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.6a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>',
    'home' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
    'user' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
    'save' => '<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>',
];
$ico = fn(string $n) => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($icons[$n] ?? $icons['doc']) . '</svg>';
?><!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= admin_e($pageTitle ?? 'Yönetim') ?> · Nextlab Yönetim</title>
<link rel="stylesheet" href="/assets/admin/admin.css?v=<?= @filemtime(dirname(__DIR__, 3) . '/assets/admin/admin.css') ?: 1 ?>">
</head>
<body>
<a class="skip" href="#ana">İçeriğe geç</a>
<div class="shell">
  <aside class="side">
    <div class="side-brand">
      <span class="side-logo">N</span>
      <div><strong>Nextlab</strong><small>Yönetim Paneli</small></div>
    </div>
    <nav class="side-nav" aria-label="Bölümler">
      <a href="/admin" class="<?= $currentKey === '' && ($activeKey ?? '') === '' ? 'on' : '' ?>"><?= $ico('home') ?><span>Pano</span></a>
      <?php foreach ($entities as $k => $ent): ?>
        <a href="/admin/<?= admin_e($k) ?>" class="<?= $currentKey === $k ? 'on' : '' ?>"><?= $ico($ent['icon']) ?><span><?= admin_e($ent['label']) ?></span></a>
      <?php endforeach; ?>
      <a href="/admin/medya" class="<?= ($activeKey ?? '') === 'medya' ? 'on' : '' ?>"><?= $ico('image') ?><span>Görseller</span></a>
      <a href="/admin/yedek" class="<?= ($activeKey ?? '') === 'yedek' ? 'on' : '' ?>"><?= $ico('save') ?><span>Yedek</span></a>
      <a href="/admin/hesap" class="<?= ($activeKey ?? '') === 'hesap' ? 'on' : '' ?>"><?= $ico('user') ?><span>Hesabım</span></a>
    </nav>
    <div class="side-foot">
      <a href="/" target="_blank" rel="noopener">Siteyi görüntüle ↗</a>
      <form method="post" action="/admin/cikis"><input type="hidden" name="_csrf" value="<?= admin_e($admin->csrfToken()) ?>"><button class="linkbtn" type="submit">Çıkış yap</button></form>
    </div>
  </aside>

  <div class="main">
    <header class="top">
      <div class="top-l">
        <button class="burger" type="button" aria-label="Menü" aria-expanded="false"><span></span><span></span><span></span></button>
        <h1><?= admin_e($pageTitle ?? 'Pano') ?></h1>
      </div>
      <div class="top-r">
        <?php if ($pending > 0): ?>
          <span class="pending" title="Yayınlanmayı bekleyen değişiklik"><?= (int)$pending ?> bekleyen</span>
          <form method="post" action="/admin/yayinla-tumu" class="inline" data-confirm="Tüm bekleyen değişiklikler siteye yansıtılacak. Onaylıyor musunuz?">
            <input type="hidden" name="_csrf" value="<?= admin_e($admin->csrfToken()) ?>">
            <input type="hidden" name="_back" value="<?= admin_e($_SERVER['REQUEST_URI'] ?? '/admin') ?>">
            <button class="btn btn-pub" type="submit">Tümünü Yayına Al</button>
          </form>
        <?php else: ?>
          <span class="live">● Site güncel</span>
        <?php endif; ?>
        <a class="who" href="/admin/hesap" title="Hesap ayarları"><?= admin_e($user['name'] ?? '') ?></a>
      </div>
    </header>

    <?php if ($flashes): ?>
      <div class="flashes">
        <?php foreach ($flashes as $f): ?>
          <div class="flash <?= $f['type'] === 'err' ? 'err' : 'ok' ?>"><?= $f['msg'] ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <main id="ana" class="content"><?= $content ?></main>
  </div>
</div>
<script src="/assets/admin/admin.js?v=<?= @filemtime(dirname(__DIR__, 3) . '/assets/admin/admin.js') ?: 1 ?>" defer></script>
</body>
</html>
