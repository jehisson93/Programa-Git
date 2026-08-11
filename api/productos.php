<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Producto.php';

requireApiAuthentication();

try {
    $model = new Producto(Database::getConnection());
    $method = $_SERVER['REQUEST_METHOD'];
    $id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;

    if ($method === 'GET' && $id) {
        $product = $model->buscarPorId((int) $id);
        if ($product === null) {
            jsonResponse(['message' => 'Producto no encontrado.'], 404);
        }
        jsonResponse(['producto' => $product]);
    }

    if ($method === 'GET') {
        $search = trim((string) ($_GET['busqueda'] ?? ''));
        $category = isset($_GET['categoria']) && $_GET['categoria'] !== ''
            ? (int) $_GET['categoria']
            : null;
        $state = trim((string) ($_GET['estado'] ?? ''));
        jsonResponse(['productos' => $model->listar($search, $category, $state)]);
    }

    if ($method === 'POST') {
        $newId = $model->crear(readJsonBody());
        jsonResponse(['message' => 'Producto registrado correctamente.', 'id_producto' => $newId], 201);
    }

    if ($method === 'PUT' && $id) {
        $model->actualizar((int) $id, readJsonBody());
        jsonResponse(['message' => 'Producto actualizado correctamente.']);
    }

    if ($method === 'DELETE' && $id) {
        $model->eliminar((int) $id);
        jsonResponse(['message' => 'Producto eliminado o inactivado correctamente.']);
    }

    jsonResponse(['message' => 'Método o identificador no válido.'], 405);
} catch (Throwable $exception) {
    handleApiException($exception);
}
