<?php /** Giriş öncesi / hata durumları için sade sayfa. */ ?><!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= admin_e($title ?? 'Nextlab Yönetim') ?></title>
<link rel="stylesheet" href="/assets/admin/admin.css">
</head>
<body class="auth-body">
<div class="auth-card">
  <div class="auth-brand"><span class="side-logo">N</span><div><strong>Nextlab</strong><small>Yönetim Paneli</small></div></div>
  <h1 class="auth-title"><?= admin_e($title ?? '') ?></h1>
  <div class="auth-note"><?= $body ?? '' ?></div>
</div>
</body>
</html>
