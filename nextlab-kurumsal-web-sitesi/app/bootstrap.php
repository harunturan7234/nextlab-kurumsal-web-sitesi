<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/CatalogRepository.php';

$configFile = dirname(__DIR__) . '/config.php';
$config = is_file($configFile) ? require $configFile : require dirname(__DIR__) . '/config.example.php';
if (!is_file($configFile)) {
    $config['app']['demo_mode'] = true;
    $config['app']['debug'] = true;
    $config['app']['url'] = '';
}

date_default_timezone_set($config['app']['timezone'] ?? 'Europe/Istanbul');
ini_set('display_errors', ($config['app']['debug'] ?? false) ? '1' : '0');
error_reporting(E_ALL);

$pdo = null;
if (!($config['app']['demo_mode'] ?? false)) {
    try {
        $pdo = Database::connect($config['database']);
    } catch (Throwable $exception) {
        error_log('Database connection failed: ' . $exception->getMessage());
    }
}
$repository = new CatalogRepository($pdo, require __DIR__ . '/data/demo.php');
