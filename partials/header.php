<?php
require_once __DIR__ . '/../config/helpers.php';
requirePageAuthentication();
$pageTitle = $pageTitle ?? 'Sistema de Gestión de Inventarios';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?> - SGI</title>
  <link rel="stylesheet" href="assets/styles.css">
</head>
<body data-page="<?= htmlspecialchars($pageName ?? '') ?>">
  <header class="app-header">
    <a class="brand-link" href="dashboard.php">SGI</a>
    <nav aria-label="Navegación principal">
      <a href="productos.php">Productos</a>
      <a href="movimiento.php">Movimientos</a>
      <a href="ajuste.php">Ajustes</a>
      <a href="reportes.php">Reportes</a>
    </nav>
    <div class="user-area">
      <span><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario') ?></span>
      <button id="btnCerrarSesion" class="link-button" type="button">Salir</button>
    </div>
  </header>
