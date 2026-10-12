<?php
// PHP built-in development server. Production uses Apache .htaccess.
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/');
if (str_starts_with($path, '/assets/')) {
    $file = realpath(__DIR__ . $path);
    $assetRoot = realpath(__DIR__ . '/assets') . DIRECTORY_SEPARATOR;
    if ($file && is_file($file) && str_starts_with($file, $assetRoot) && preg_match('/\.(css|js|png|jpe?g|webp|avif|svg|ico)$/iD', $file)) return false;
    http_response_code(404); exit('Not found');
}
if (preg_match('~^/(app|data|lib|services|views|source-assets|scripts|qa|node_modules|var|\.git|\.aistudio|\.github)(/|$)~i', $path) || preg_match('~(^|/)[.]|\.(sql|json|lock|md|ejs|cjs|log|env|bak)$~iD', $path) || (preg_match('/\.(php|js|html)$/iD', $path) && !in_array($path, ['/index.php','/index.html'],true))) {
    http_response_code(403); exit('Forbidden');
}
require __DIR__ . '/index.php';
