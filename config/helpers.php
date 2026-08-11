<?php

declare(strict_types=1);

function startSecureSession(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_set_cookie_params([
            'httponly' => true,
            'samesite' => 'Lax',
            'secure' => isset($_SERVER['HTTPS']),
        ]);
        session_start();
    }
}

function jsonResponse(array $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function readJsonBody(): array
{
    $content = file_get_contents('php://input');
    $data = json_decode($content ?: '{}', true);

    if (!is_array($data)) {
        jsonResponse(['message' => 'El cuerpo de la solicitud no es válido.'], 400);
    }

    return $data;
}

function requireApiAuthentication(): void
{
    startSecureSession();
    if (empty($_SESSION['usuario_id'])) {
        jsonResponse(['message' => 'Debe iniciar sesión.'], 401);
    }
}

function requirePageAuthentication(): void
{
    startSecureSession();
    if (empty($_SESSION['usuario_id'])) {
        header('Location: index.php');
        exit;
    }
}

function textValue(array $data, string $key, int $maxLength, bool $required = true): string
{
    $value = trim((string) ($data[$key] ?? ''));
    if ($required && $value === '') {
        throw new InvalidArgumentException("El campo {$key} es obligatorio.");
    }
    if (mb_strlen($value) > $maxLength) {
        throw new InvalidArgumentException("El campo {$key} supera {$maxLength} caracteres.");
    }
    return $value;
}

function integerValue(array $data, string $key, int $minimum = 0): int
{
    $value = filter_var($data[$key] ?? null, FILTER_VALIDATE_INT);
    if ($value === false || $value < $minimum) {
        throw new InvalidArgumentException("El campo {$key} debe ser un entero mayor o igual a {$minimum}.");
    }
    return $value;
}

function handleApiException(Throwable $exception): never
{
    if ($exception instanceof InvalidArgumentException) {
        jsonResponse(['message' => $exception->getMessage()], 422);
    }

    error_log($exception->getMessage());
    jsonResponse(['message' => 'Ocurrió un error al procesar la solicitud.'], 500);
}
