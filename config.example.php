<?php
// Copy to config.php on hosting. Never commit credentials.
return [
    'app' => [
        'url' => 'https://www.nextlabcosmetic.com',
        'data_mode' => 'mysql', // 'file' uses the same verified JSON catalog for local preview.
        'debug' => false,
    ],
    'database' => [
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'YOUR_DATABASE',
        'user' => 'YOUR_DATABASE_USER',
        'password' => 'YOUR_DATABASE_PASSWORD',
    ],
];
