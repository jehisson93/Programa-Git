<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/helpers.php';

requireApiAuthentication();

try {
    $connection = Database::getConnection();
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
