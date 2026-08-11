<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Inventario.php';

requireApiAuthentication();

try {
    $model = new Inventario(Database::getConnection());
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        jsonResponse(['movimientos' => $model->listar('movimientos')]);
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $model->crearMovimiento(readJsonBody(), (int) $_SESSION['usuario_id']);
        jsonResponse(['message' => 'Movimiento registrado correctamente.', 'id_movimiento' => $id], 201);
    }
    jsonResponse(['message' => 'Método no permitido.'], 405);
} catch (Throwable $exception) {
    handleApiException($exception);
}
