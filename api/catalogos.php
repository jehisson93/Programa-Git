<?php

declare(strict_types=1);

// Proporciona las opciones de categorías y productos usadas en los formularios.
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/helpers.php';

requireApiAuthentication();

try {
    $connection = Database::getConnection();
    // No se reciben parámetros del usuario, por eso estas consultas fijas usan query().
    $categories = $connection->query(
        'SELECT id_categoria, nombre FROM categoria ORDER BY nombre'
    )->fetchAll();
    $products = $connection->query(
        "SELECT id_producto, codigo, nombre, stock_actual
         FROM producto WHERE estado = 'Activo' ORDER BY nombre"
    )->fetchAll();
    jsonResponse(['categorias' => $categories, 'productos' => $products]);
} catch (Throwable $exception) {
    handleApiException($exception);
}
