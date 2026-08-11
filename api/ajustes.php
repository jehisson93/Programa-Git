<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Inventario.php';

requireApiAuthentication();

try {
    $model = new Inventario(Database::getConnection());
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        jsonResponse(['ajustes' => $model->listar('ajustes')]);
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $model->crearAjuste(readJsonBody(), (int) $_SESSION['usuario_id']);
        jsonResponse(['message' => 'Ajuste registrado correctamente.', 'id_ajuste' => $id], 201);
    }
    jsonResponse(['message' => 'Método no permitido.'], 405);
} catch (Throwable $exception) {
    handleApiException($exception);
}
