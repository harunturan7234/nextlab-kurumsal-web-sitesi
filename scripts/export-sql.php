<?php
declare(strict_types=1);

/**
 * Generates database.sql from the canonical JSON data files.
 *
 * The production catalog (CatalogRepository in "mysql" mode) stores one JSON
 * document per row in three tables. Keeping this exporter means the JSON files
 * stay the single source of truth and the SQL can always be regenerated — and
 * a future admin panel can write the same tables without a schema redesign.
 *
 * Usage:  php scripts/export-sql.php > database.sql
 */

$root = dirname(__DIR__);
$catalog = json_decode(file_get_contents($root . '/data/catalog.json'), true, 512, JSON_THROW_ON_ERROR);
$blogs = json_decode(file_get_contents($root . '/data/blogs.json'), true, 512, JSON_THROW_ON_ERROR);

$json = static fn(array $v): string =>
    json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

/** Escape a string for a single-quoted MySQL literal. */
$q = static function (string $s): string {
    return "'" . strtr($s, [
        '\\' => '\\\\', "'" => "\\'", "\n" => '\\n', "\r" => '\\r',
        "\x00" => '\\0', "\x1a" => '\\Z',
    ]) . "'";
};

$slugify = static function (string $s): string {
    $s = CatalogNormalize($s);
    $s = preg_replace('/[^a-z0-9]+/', '-', $s) ?? '';
    return trim($s, '-') ?: 'item';
};
function CatalogNormalize(string $value): string {
    return strtolower(strtr($value, [
        'İ'=>'i','I'=>'i','ı'=>'i','Ş'=>'s','ş'=>'s','Ğ'=>'g','ğ'=>'g',
        'Ü'=>'u','ü'=>'u','Ö'=>'o','ö'=>'o','Ç'=>'c','ç'=>'c',
    ]));
}

$out = [];
$out[] = "-- Nextlab kurumsal katalog veritabanı";
$out[] = "-- Otomatik üretildi: scripts/export-sql.php — kaynak: data/catalog.json + data/blogs.json";
$out[] = "-- MySQL 5.7+ / MariaDB 10.3+ · utf8mb4";
$out[] = "-- İçe aktarma: phpMyAdmin > İçe Aktar, ya da  mysql -u KULLANICI -p VERITABANI < database.sql";
$out[] = "SET NAMES utf8mb4;";
$out[] = "SET time_zone = '+00:00';";
$out[] = "SET sql_mode = 'STRICT_ALL_TABLES';";
$out[] = "";

// --- Schema -----------------------------------------------------------------
// data_json  = YAYINDAKİ sürüm (NULL ise kayıt henüz yayınlanmamış taslaktır)
// draft_json = PANELDE düzenlenen çalışma sürümü. "Yayına Al" => data_json = draft_json
$out[] = "DROP TABLE IF EXISTS `nextlab_products`;";
$out[] = "DROP TABLE IF EXISTS `nextlab_categories`;";
$out[] = "DROP TABLE IF EXISTS `nextlab_content`;";
$out[] = "DROP TABLE IF EXISTS `nextlab_admins`;";
$out[] = "";
$out[] = "CREATE TABLE `nextlab_categories` (";
$out[] = "  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,";
$out[] = "  `slug` VARCHAR(160) NOT NULL,";
$out[] = "  `sort_order` INT NOT NULL DEFAULT 0,";
$out[] = "  `data_json` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,";
$out[] = "  `draft_json` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,";
$out[] = "  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,";
$out[] = "  PRIMARY KEY (`id`),";
$out[] = "  UNIQUE KEY `uq_category_slug` (`slug`)";
$out[] = ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
$out[] = "";
$out[] = "CREATE TABLE `nextlab_products` (";
$out[] = "  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,";
$out[] = "  `slug` VARCHAR(190) NOT NULL,";
$out[] = "  `category_id` INT UNSIGNED NOT NULL DEFAULT 0,";
$out[] = "  `sort_order` INT NOT NULL DEFAULT 0,";
$out[] = "  `data_json` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,";
$out[] = "  `draft_json` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,";
$out[] = "  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,";
$out[] = "  PRIMARY KEY (`id`),";
$out[] = "  UNIQUE KEY `uq_product_slug` (`slug`),";
$out[] = "  KEY `idx_product_category` (`category_id`)";
$out[] = ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
$out[] = "";
$out[] = "CREATE TABLE `nextlab_content` (";
$out[] = "  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,";
$out[] = "  `kind` VARCHAR(40) NOT NULL,";
$out[] = "  `slug` VARCHAR(190) NOT NULL,";
$out[] = "  `sort_order` INT NOT NULL DEFAULT 0,";
$out[] = "  `data_json` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,";
$out[] = "  `draft_json` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,";
$out[] = "  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,";
$out[] = "  PRIMARY KEY (`id`),";
$out[] = "  UNIQUE KEY `uq_content_kind_slug` (`kind`, `slug`),";
$out[] = "  KEY `idx_content_kind` (`kind`, `sort_order`)";
$out[] = ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
$out[] = "";
$out[] = "-- Yönetim paneli kullanıcıları. İlk kullanıcı /admin adresindeki kurulum ekranından oluşturulur.";
$out[] = "CREATE TABLE `nextlab_admins` (";
$out[] = "  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,";
$out[] = "  `email` VARCHAR(190) NOT NULL,";
$out[] = "  `name` VARCHAR(120) NOT NULL,";
$out[] = "  `password_hash` VARCHAR(255) NOT NULL,";
$out[] = "  `failed_attempts` INT NOT NULL DEFAULT 0,";
$out[] = "  `locked_until` DATETIME NULL,";
$out[] = "  `last_login_at` DATETIME NULL,";
$out[] = "  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,";
$out[] = "  PRIMARY KEY (`id`),";
$out[] = "  UNIQUE KEY `uq_admin_email` (`email`)";
$out[] = ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
$out[] = "";

// --- Categories -------------------------------------------------------------
$out[] = "-- Kategoriler (" . count($catalog['categories']) . ")";
$i = 0;
foreach ($catalog['categories'] as $c) {
    $out[] = sprintf(
        "INSERT INTO `nextlab_categories` (`id`,`slug`,`sort_order`,`data_json`,`draft_json`) VALUES (%d,%s,%d,%s,%s);",
        (int)$c['id'], $q($c['slug']), $i++, $q($json($c)), $q($json($c))
    );
}
$out[] = "";

// --- Products ---------------------------------------------------------------
$out[] = "-- Ürünler (" . count($catalog['products']) . ")";
$i = 0;
foreach ($catalog['products'] as $p) {
    $out[] = sprintf(
        "INSERT INTO `nextlab_products` (`id`,`slug`,`category_id`,`sort_order`,`data_json`,`draft_json`) VALUES (%d,%s,%d,%d,%s,%s);",
        (int)$p['id'], $q($p['slug']), (int)($p['category_id'] ?? 0), $i++, $q($json($p)), $q($json($p))
    );
}
$out[] = "";

// --- Content (banners, faqs, distributorBrands, blogs, channels) ------------
$contentRows = [];
foreach ($catalog['banners'] as $n => $b) {
    $contentRows[] = ['banners', 'banner-' . (int)($b['id'] ?? $n + 1), $n, $b];
}
foreach ($catalog['faqs'] as $n => $f) {
    $contentRows[] = ['faqs', 'faq-' . (int)($f['id'] ?? $n + 1), $n, $f];
}
foreach ($catalog['distributorBrands'] as $n => $b) {
    $contentRows[] = ['distributorBrands', $b['slug'] ?? ('brand-' . ($n + 1)), $n, $b];
}
foreach ($blogs as $n => $b) {
    $contentRows[] = ['blogs', $b['slug'] ?? ('blog-' . ($n + 1)), $n, $b];
}
// channels is consumed as a single row ($items[0]) by the repository.
$contentRows[] = ['channels', 'channels', 0, $catalog['channels']];

// Site geneli ayarlar — panelden düzenlenir, header/footer/kurumsal/iletişim okur.
$settings = json_decode(file_get_contents($root . '/data/settings.json'), true, 512, JSON_THROW_ON_ERROR);
$contentRows[] = ['settings', 'settings', 0, $settings];

$out[] = "-- İçerik: banner, SSS, markalar, blog, kanallar, ayarlar (" . count($contentRows) . " satır)";
foreach ($contentRows as [$kind, $slug, $order, $payload]) {
    $out[] = sprintf(
        "INSERT INTO `nextlab_content` (`kind`,`slug`,`sort_order`,`data_json`,`draft_json`) VALUES (%s,%s,%d,%s,%s);",
        $q($kind), $q((string)$slug), (int)$order, $q($json($payload)), $q($json($payload))
    );
}
$out[] = "";

fwrite(STDOUT, implode("\n", $out) . "\n");
