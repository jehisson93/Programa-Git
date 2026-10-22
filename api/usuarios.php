<?php

declare(strict_types=1);

// Permite que el administrador consulte y active o desactive usuarios.
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/helpers.php';

requireAdminApiAuthentication();

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $connection = Database::getConnection();

    if ($method === 'GET') {
        $statement = $connection->query(
            "SELECT u.id_usuario, u.nombres, u.apellidos, u.documento,
                    u.correo, u.estado, r.nombre AS rol
             FROM usuario u
             INNER JOIN rol r ON r.id_rol = u.id_rol
             ORDER BY u.nombres, u.apellidos"
        );
        $users = $statement->fetchAll();

        foreach ($users as &$user) {
            $user['es_usuario_actual'] = (int) $user['id_usuario'] === (int) $_SESSION['usuario_id'];
        }
        unset($user);

        jsonResponse(['usuarios' => $users]);
    }

    if ($method !== 'PATCH') {
        jsonResponse(['message' => 'Método no permitido.'], 405);
    }

    $userId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$userId) {
        throw new InvalidArgumentException('Debe indicar un usuario válido.');
    }

    $data = readJsonBody();
    $status = textValue($data, 'estado', 10);
    if (!in_array($status, ['Activo', 'Inactivo'], true)) {
        throw new InvalidArgumentException('El estado indicado no es válido.');
    }

    // La sesión administradora actual no puede bloquearse a sí misma.
    if ((int) $userId === (int) $_SESSION['usuario_id'] && $status === 'Inactivo') {
        throw new InvalidArgumentException('No puede desactivar su propia cuenta.');
    }

    $statement = $connection->prepare(
        'UPDATE usuario SET estado = :estado WHERE id_usuario = :id_usuario'
    );
    $statement->execute([
        'estado' => $status,
        'id_usuario' => (int) $userId,
    ]);

    if ($statement->rowCount() === 0) {
        $exists = $connection->prepare(
            'SELECT id_usuario FROM usuario WHERE id_usuario = :id_usuario'
        );
        $exists->execute(['id_usuario' => (int) $userId]);
        if (!$exists->fetch()) {
            jsonResponse(['message' => 'El usuario no existe.'], 404);
        }
    }

    jsonResponse(['message' => "Usuario {$status} correctamente."]);
} catch (Throwable $exception) {
    handleApiException($exception);
}
