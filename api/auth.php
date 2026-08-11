<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/helpers.php';

startSecureSession();

try {
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method === 'DELETE') {
        session_unset();
        session_destroy();
        jsonResponse(['message' => 'Sesión cerrada correctamente.']);
    }

    if ($method !== 'POST') {
        jsonResponse(['message' => 'Método no permitido.'], 405);
    }

    $data = readJsonBody();
    $email = textValue($data, 'correo', 120);
    $password = (string) ($data['clave'] ?? '');
    $statement = Database::getConnection()->prepare(
        "SELECT id_usuario, nombres, apellidos, correo, clave
         FROM usuario WHERE correo = :correo AND estado = 'Activo' LIMIT 1"
    );
    $statement->execute(['correo' => $email]);
    $user = $statement->fetch();

    if (!$user || !password_verify($password, $user['clave'])) {
        jsonResponse(['message' => 'Correo o contraseña incorrectos.'], 401);
    }

    session_regenerate_id(true);
    $_SESSION['usuario_id'] = (int) $user['id_usuario'];
    $_SESSION['usuario_nombre'] = trim($user['nombres'] . ' ' . $user['apellidos']);
    $_SESSION['usuario_correo'] = $user['correo'];
    unset($user['clave']);
    jsonResponse(['message' => 'Inicio de sesión correcto.', 'usuario' => $user]);
} catch (Throwable $exception) {
    handleApiException($exception);
}
