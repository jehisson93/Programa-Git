<?php

declare(strict_types=1);

// Controla el inicio y cierre de sesión de la aplicación.
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/helpers.php';

startSecureSession();

try {
    $method = $_SERVER['REQUEST_METHOD'];
    // JavaScript envía DELETE cuando el usuario presiona el botón Salir.
    if ($method === 'DELETE') {
        session_unset();
        session_destroy();
        jsonResponse(['message' => 'Sesión cerrada correctamente.']);
    }

    if ($method !== 'POST') {
        jsonResponse(['message' => 'Método no permitido.'], 405);
    }

    $data = readJsonBody();
    // Se aceptan ambos contratos para conservar la interfaz actual y el caso académico.
    $emailKey = array_key_exists('usuario', $data) ? 'usuario' : 'correo';
    $email = strtolower(textValue($data, $emailKey, 120));
    $password = (string) ($data['contrasena'] ?? $data['clave'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new InvalidArgumentException('El usuario debe ser un correo válido.');
    }

    if ($password === '') {
        throw new InvalidArgumentException('La contraseña es obligatoria.');
    }

    // Se busca solamente un usuario activo con el correo indicado.
    $statement = Database::getConnection()->prepare(
        "SELECT id_usuario, id_rol, nombres, apellidos, correo, clave
         FROM usuario WHERE correo = :correo AND estado = 'Activo' LIMIT 1"
    );
    $statement->execute(['correo' => $email]);
    $user = $statement->fetch();

    // password_verify compara la clave escrita con el hash almacenado.
    if (!$user || !password_verify($password, $user['clave'])) {
        jsonResponse(['message' => 'Error en la autenticación.'], 401);
    }

    // Cambiar el identificador después de ingresar reduce el riesgo de robo de sesión.
    session_regenerate_id(true);
    $_SESSION['usuario_id'] = (int) $user['id_usuario'];
    $_SESSION['usuario_rol'] = (int) $user['id_rol'];
    $_SESSION['usuario_nombre'] = trim($user['nombres'] . ' ' . $user['apellidos']);
    $_SESSION['usuario_correo'] = $user['correo'];
    unset($user['clave']);
    jsonResponse(['message' => 'Autenticación satisfactoria.', 'usuario' => $user]);
} catch (Throwable $exception) {
    handleApiException($exception);
}
