<?php
declare(strict_types=1);

/**
 * Nextlab — tek adımlık web kurulumu.
 *
 * Yüklemeden sonra bu adresi açın: https://alanadiniz.com/kurulum.php
 * Form; veritabanını kurar, config.php'yi yazar, görsel klasörünü açar ve
 * ilk yönetici hesabını oluşturur. Kurulum bittikten sonra kendini kilitler.
 */

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Cache-Control: no-store');
header('X-Robots-Tag: noindex, nofollow');

$root = __DIR__;
$configFile = $root . '/config.php';

function h(mixed $v): string { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

/** SQL dosyasını, tırnak içindeki ';' karakterlerini bozmadan ifadelere böler. */
function split_sql(string $sql): array {
    $lines = preg_split('/\r\n|\r|\n/', $sql) ?: [];
    $clean = [];
    foreach ($lines as $l) { if (!preg_match('/^\s*--/', $l)) $clean[] = $l; }
    $sql = implode("\n", $clean);

    $out = []; $cur = ''; $inStr = false; $esc = false;
    for ($i = 0, $n = strlen($sql); $i < $n; $i++) {
        $ch = $sql[$i];
        $cur .= $ch;
        if ($esc) { $esc = false; continue; }
        if ($ch === '\\') { if ($inStr) $esc = true; continue; }
        if ($ch === "'") { $inStr = !$inStr; continue; }
        if (!$inStr && $ch === ';') { $t = trim($cur); if ($t !== '' && $t !== ';') $out[] = $t; $cur = ''; }
    }
    $t = trim($cur); if ($t !== '') $out[] = $t;
    return $out;
}

// ---------------------------------------------------------------- durum
$installed = false;
if (is_file($configFile)) {
    try {
        $cfg = require $configFile;
        $d = $cfg['database'] ?? null;
        if ($d) {
            $p = new PDO('mysql:host=' . $d['host'] . ';port=' . (int)($d['port'] ?? 3306) . ';dbname=' . $d['name'] . ';charset=utf8mb4',
                $d['user'], $d['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $installed = (int)$p->query('SELECT COUNT(*) FROM nextlab_admins')->fetchColumn() > 0;
        }
    } catch (Throwable) { $installed = false; }
}

$errors = [];
$manualConfig = null;
$guessUrl = (((($_SERVER['HTTPS'] ?? '') !== '' && ($_SERVER['HTTPS'] ?? '') !== 'off') || ($_SERVER['SERVER_PORT'] ?? '') === '443') ? 'https://' : 'http://')
    . ($_SERVER['HTTP_HOST'] ?? 'localhost');

$v = [
    'url' => $_POST['url'] ?? $guessUrl,
    'host' => $_POST['host'] ?? 'localhost',
    'port' => $_POST['port'] ?? '3306',
    'name' => $_POST['name'] ?? '',
    'user' => $_POST['user'] ?? '',
    'pass' => $_POST['pass'] ?? '',
    'aname' => $_POST['aname'] ?? '',
    'amail' => $_POST['amail'] ?? '',
    'apass' => $_POST['apass'] ?? '',
];

if (!$installed && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $url = rtrim(trim((string)$v['url']), '/');
    if (!filter_var($url, FILTER_VALIDATE_URL)) $errors[] = 'Site adresi geçerli değil (örn. https://alanadiniz.com).';
    if (trim((string)$v['name']) === '') $errors[] = 'Veritabanı adı boş olamaz.';
    if (trim((string)$v['user']) === '') $errors[] = 'Veritabanı kullanıcısı boş olamaz.';
    if (!filter_var(trim((string)$v['amail']), FILTER_VALIDATE_EMAIL)) $errors[] = 'Yönetici e-postası geçerli değil.';
    if (strlen((string)$v['apass']) < 10) $errors[] = 'Yönetici parolası en az 10 karakter olmalıdır.';

    $pdo = null;
    if (!$errors) {
        try {
            $pdo = new PDO(
                'mysql:host=' . trim((string)$v['host']) . ';port=' . (int)$v['port'] . ';dbname=' . trim((string)$v['name']) . ';charset=utf8mb4',
                trim((string)$v['user']), (string)$v['pass'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false]
            );
        } catch (Throwable $e) {
            $errors[] = 'Veritabanına bağlanılamadı: ' . $e->getMessage();
        }
    }

    if (!$errors && $pdo) {
        // 1) Şema + başlangıç verisi
        try {
            $sqlFile = $root . '/database.sql';
            if (!is_file($sqlFile)) throw new RuntimeException('database.sql bulunamadı (dosyayı da yüklediğinizden emin olun).');
            $pdo->exec('SET NAMES utf8mb4');
            foreach (split_sql((string)file_get_contents($sqlFile)) as $stmt) $pdo->exec($stmt);
        } catch (Throwable $e) {
            $errors[] = 'Veritabanı kurulurken hata: ' . $e->getMessage();
        }
    }

    if (!$errors && $pdo) {
        // 2) config.php
        $cfgBody = "<?php\n// Nextlab yapılandırması — kurulum sihirbazı tarafından oluşturuldu.\nreturn [\n"
            . "    'app' => [\n        'url' => " . var_export($url, true) . ",\n        'data_mode' => 'mysql',\n        'debug' => false,\n    ],\n"
            . "    'database' => [\n        'host' => " . var_export(trim((string)$v['host']), true) . ",\n        'port' => " . (int)$v['port'] . ",\n"
            . "        'name' => " . var_export(trim((string)$v['name']), true) . ",\n        'user' => " . var_export(trim((string)$v['user']), true) . ",\n"
            . "        'password' => " . var_export((string)$v['pass'], true) . ",\n    ],\n];\n";
        if (@file_put_contents($configFile, $cfgBody) === false) {
            $manualConfig = $cfgBody;
            $errors[] = 'config.php yazılamadı (klasör yazma izni yok). Aşağıdaki içerikle elle oluşturun.';
        } else {
            @chmod($configFile, 0640);
        }
    }

    if (!$errors && $pdo) {
        // 3) Görsel yükleme klasörü
        $up = $root . '/assets/images/uploads';
        if (!is_dir($up)) @mkdir($up, 0775, true);
        if (is_dir($up) && !is_file($up . '/.htaccess')) {
            @file_put_contents($up . '/.htaccess', "php_flag engine off\n<FilesMatch \"\\.(php|phtml|phar|pl|py|cgi|sh|html?)$\">\n  Require all denied\n</FilesMatch>\n");
        }

        // 4) İlk yönetici
        try {
            $has = (int)$pdo->query('SELECT COUNT(*) FROM nextlab_admins')->fetchColumn();
            if ($has === 0) {
                $st = $pdo->prepare('INSERT INTO nextlab_admins (email, name, password_hash) VALUES (?,?,?)');
                $st->execute([
                    trim((string)$v['amail']),
                    trim((string)$v['aname']) ?: 'Yönetici',
                    password_hash((string)$v['apass'], PASSWORD_DEFAULT),
                ]);
            }
        } catch (Throwable $e) {
            $errors[] = 'Yönetici hesabı oluşturulamadı: ' . $e->getMessage();
        }
    }

    if (!$errors) {
        $installed = true;
        $justInstalled = true;
    }
}
?><!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Nextlab · Kurulum</title>
<link rel="stylesheet" href="/assets/admin/admin.css">
<style>
.setup{max-width:620px}
.setup .grp{border:1px solid var(--line);border-radius:12px;padding:16px;margin-bottom:14px;background:#fff}
.setup .grp h2{font-size:14.5px;margin:0 0 12px;font-weight:800}
.setup .two{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.setup pre{background:#0f172a;color:#e2e8f0;padding:14px;border-radius:10px;overflow:auto;font-size:12px;line-height:1.5}
.steps{display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap}
.steps span{background:#fff;border:1px solid var(--line);border-radius:999px;padding:5px 12px;font-size:12.5px;color:var(--muted)}
@media(max-width:560px){.setup .two{grid-template-columns:1fr}}
</style>
</head>
<body class="auth-body">
<div class="auth-card setup">
  <div class="auth-brand"><span class="side-logo">N</span><div><strong>Nextlab</strong><small>Kurulum</small></div></div>

<?php if ($installed && empty($justInstalled)): ?>
  <h1 class="auth-title">Kurulum zaten tamamlanmış</h1>
  <p class="auth-note">Site kurulu ve yönetici hesabı mevcut. Güvenlik için bu sayfa kilitlidir.</p>
  <a class="btn btn-primary full" href="/admin">Yönetim Paneline Git</a>
  <p class="auth-foot"><a href="/">← Siteye dön</a></p>

<?php elseif (!empty($justInstalled)): ?>
  <h1 class="auth-title">Kurulum tamamlandı 🎉</h1>
  <p class="auth-note">Veritabanı kuruldu, ayar dosyası yazıldı ve yönetici hesabınız oluşturuldu.
     Artık tüm içeriği panelden düzenleyip <strong>“Yayına Al”</strong> ile anında yayınlayabilirsiniz.</p>
  <a class="btn btn-primary full" href="/admin">Yönetim Paneline Giriş Yap</a>
  <p class="auth-foot"><a href="/" target="_blank" rel="noopener">Siteyi yeni sekmede aç ↗</a></p>
  <p class="auth-note" style="margin-top:14px">Güvenlik için <code>kurulum.php</code> dosyasını sunucudan silebilirsiniz (zorunlu değil; kurulum sonrası kendini kilitler).</p>

<?php else: ?>
  <h1 class="auth-title">Tek adımda kurulum</h1>
  <div class="steps"><span>1 · Veritabanı</span><span>2 · Yönetici</span><span>3 · Bitti</span></div>
  <p class="auth-note">Natro panelinden oluşturduğunuz <strong>boş MySQL veritabanının</strong> bilgilerini girin.
     Gerisini (tablo kurulumu, ayar dosyası, klasör izni) kurulum kendisi yapar.</p>

  <?php foreach ($errors as $er): ?><div class="flash err"><?= h($er) ?></div><?php endforeach; ?>
  <?php if ($manualConfig): ?>
    <p class="auth-note">Sunucuda <code>config.php</code> adıyla bu içerikle bir dosya oluşturup kurulumu tekrar çalıştırın:</p>
    <pre><?= h($manualConfig) ?></pre>
  <?php endif; ?>

  <form method="post" autocomplete="off">
    <div class="grp">
      <h2>Site</h2>
      <label class="fld"><span>Site adresi</span>
        <input type="url" name="url" value="<?= h($v['url']) ?>" required>
        <small class="help">Otomatik algılandı. Yanlışsa düzeltin (sonda / olmadan).</small>
      </label>
    </div>

    <div class="grp">
      <h2>Veritabanı</h2>
      <div class="two">
        <label class="fld"><span>Sunucu</span><input type="text" name="host" value="<?= h($v['host']) ?>" required>
          <small class="help">Natro'da genelde <code>localhost</code></small></label>
        <label class="fld"><span>Veritabanı adı</span><input type="text" name="name" value="<?= h($v['name']) ?>" required></label>
      </div>
      <div class="two">
        <label class="fld"><span>Port</span><input type="text" name="port" value="<?= h($v['port']) ?>">
          <small class="help">Varsayılan 3306</small></label>
      </div>
      <div class="two">
        <label class="fld"><span>Kullanıcı adı</span><input type="text" name="user" value="<?= h($v['user']) ?>" required></label>
        <label class="fld"><span>Parola</span><input type="password" name="pass" value="<?= h($v['pass']) ?>"></label>
      </div>
    </div>

    <div class="grp">
      <h2>Yönetici hesabı</h2>
      <label class="fld"><span>Ad Soyad</span><input type="text" name="aname" value="<?= h($v['aname']) ?>" required></label>
      <div class="two">
        <label class="fld"><span>E-posta</span><input type="email" name="amail" value="<?= h($v['amail']) ?>" required></label>
        <label class="fld"><span>Parola</span><input type="password" name="apass" minlength="10" required>
          <small class="help">En az 10 karakter</small></label>
      </div>
    </div>

    <button class="btn btn-primary full" type="submit">Kurulumu Tamamla</button>
  </form>
<?php endif; ?>
</div>
</body>
</html>
