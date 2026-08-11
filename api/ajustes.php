<?php

declare(strict_types=1);

// Punto de entrada para consultar o registrar correcciones de existencias.
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Inventario.php';

requireApiAuthentication();

try {
    $model = new Inventario(Database::getConnection());
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        jsonResponse(['ajustes' => $model->listar('ajustes')]);
    }
    // El modelo valida que el ajuste no deje un stock negativo.
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $model->crearAjuste(readJsonBody(), (int) $_SESSION['usuario_id']);
        jsonResponse(['message' => 'Ajuste registrado correctamente.', 'id_ajuste' => $id], 201);
    }
    jsonResponse(['message' => 'Método no permitido.'], 405);
} catch (Throwable $exception) {
    handleApiException($exception);
}
