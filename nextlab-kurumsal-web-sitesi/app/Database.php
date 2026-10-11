<?php

declare(strict_types=1);

final class Database
{
    public static function connect(array $database): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $database['host'], (int) ($database['port'] ?? 3306),
            $database['name'], $database['charset'] ?? 'utf8mb4'
        );
        return new PDO($dsn, $database['username'], $database['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
}
