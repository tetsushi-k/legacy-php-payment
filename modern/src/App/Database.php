<?php

declare(strict_types=1);

namespace App;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (!self::$pdo instanceof \PDO) {
            $host = getenv('DB_HOST') ?: 'localhost';
            $name = getenv('DB_NAME') ?: 'legacy_payment';
            $user = getenv('DB_USER') ?: 'legacy_user';
            $pass = getenv('DB_PASS') ?: 'legacy_password';

            $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";

            try {
                self::$pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                throw new \RuntimeException('DB接続失敗: ' . $e->getMessage(), 0, $e);
            }
        }

        return self::$pdo;
    }
}
