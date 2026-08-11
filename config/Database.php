<?php

declare(strict_types=1);

final class Database
{
    private const HOST = '127.0.0.1';
    private const PORT = '3306';
    private const NAME = 'sgi_inventario';
    private const USER = 'root';
    private const PASSWORD = '';

    private static ?PDO $connection = null;

    private function __construct()
    {
    }

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                self::HOST,
                self::PORT,
                self::NAME
            );

            self::$connection = new PDO($dsn, self::USER, self::PASSWORD, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        return self::$connection;
    }
}
