<?php

declare(strict_types=1);

/**
 * Administra una única conexión PDO con MySQL.
 *
 * Centralizar la conexión evita repetir usuario, contraseña y opciones en
 * cada consulta. El patrón empleado se conoce como Singleton sencillo.
 */
final class Database
{
    // Valores predeterminados de una instalación local de XAMPP.
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
        // La conexión solo se construye la primera vez que alguien la solicita.
        if (self::$connection === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                self::HOST,
                self::PORT,
                self::NAME
            );

            self::$connection = new PDO($dsn, self::USER, self::PASSWORD, [
                // Convierte errores de MySQL en excepciones que podemos controlar.
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                // Devuelve cada fila como un arreglo asociativo: ['nombre' => '...'].
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Obliga a MySQL a preparar realmente las consultas parametrizadas.
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        return self::$connection;
    }
}
