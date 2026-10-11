<?php

declare(strict_types=1);

// Registra usuarios desde una sesión administradora y responde siempre en JSON.
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/helpers.php';

requireAdminApiAuthentication();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse(['message' => 'Método no permitido.'], 405);
    }

    $data = readJsonBody();
    $firstNames = textValue($data, 'nombres', 100);
    $lastNames = textValue($data, 'apellidos', 100);
    $document = textValue($data, 'documento', 20);
    $email = strtolower(textValue($data, 'usuario', 120));
    $password = (string) ($data['contrasena'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new InvalidArgumentException('El usuario debe ser un correo válido.');
    }

    if (mb_strlen($password) < 8) {
        throw new InvalidArgumentException(
            'La contraseña debe tener por lo menos ocho caracteres.'
        );
    }

    $connection = Database::getConnection();
    $duplicate = $connection->prepare(
        'SELECT id_usuario FROM usuario
         WHERE correo = :correo OR documento = :documento
         LIMIT 1'
    );
    $duplicate->execute([
        'correo' => $email,
        'documento' => $document,
    ]);

    if ($duplicate->fetch()) {
        jsonResponse(
            ['message' => 'El usuario o documento ya está registrado.'],
            409
        );
    }

    // El rol se consulta por nombre para no depender de un identificador fijo.
    $roleStatement = $connection->prepare(
        "SELECT id_rol FROM rol WHERE nombre = 'Operador' LIMIT 1"
    );
    $roleStatement->execute();
    $roleId = $roleStatement->fetchColumn();

    if ($roleId === false) {
        jsonResponse([
            'message' => 'El rol Operador no está configurado. Ejecute database/migration_auth.sql.',
        ], 503);
    }

    // Solo se almacena el hash; la contraseña original se descarta.
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $statement = $connection->prepare(
        "INSERT INTO usuario
         (id_rol, nombres, apellidos, documento, correo, clave, estado)
         VALUES
         (:id_rol, :nombres, :apellidos, :documento, :correo, :clave, 'Activo')"
    );
    $statement->execute([
        'id_rol' => (int) $roleId,
        'nombres' => $firstNames,
        'apellidos' => $lastNames,
        'documento' => $document,
        'correo' => $email,
        'clave' => $passwordHash,
    ]);

    jsonResponse(['message' => 'Registro satisfactorio.'], 201);
} catch (PDOException $exception) {
    // La restricción UNIQUE también protege contra registros simultáneos repetidos.
    if ($exception->getCode() === '23000') {
        jsonResponse(['message' => 'El usuario o documento ya está registrado.'], 409);
    }

    handleApiException($exception);
} catch (Throwable $exception) {
    handleApiException($exception);
}
