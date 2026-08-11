<?php

declare(strict_types=1);

// Controlador REST: traduce métodos HTTP en operaciones del modelo Producto.
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Producto.php';

requireApiAuthentication();

try {
    $model = new Producto(Database::getConnection());
    $method = $_SERVER['REQUEST_METHOD'];
    $id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;

    // GET con id consulta un único producto; GET sin id devuelve una lista.
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

    // POST representa la C de CRUD: crear.
    if ($method === 'POST') {
        $newId = $model->crear(readJsonBody());
        jsonResponse(['message' => 'Producto registrado correctamente.', 'id_producto' => $newId], 201);
    }

    // PUT representa la U de CRUD: actualizar un registro completo.
    if ($method === 'PUT' && $id) {
        $model->actualizar((int) $id, readJsonBody());
        jsonResponse(['message' => 'Producto actualizado correctamente.']);
    }

    // DELETE representa la D de CRUD: eliminar o inactivar.
    if ($method === 'DELETE' && $id) {
        $model->eliminar((int) $id);
        jsonResponse(['message' => 'Producto eliminado o inactivado correctamente.']);
    }

    jsonResponse(['message' => 'Método o identificador no válido.'], 405);
} catch (Throwable $exception) {
    handleApiException($exception);
}
