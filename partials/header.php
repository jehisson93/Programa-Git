<?php
// Este encabezado se reutiliza en todas las páginas que requieren autenticación.
require_once __DIR__ . '/../config/helpers.php';
requirePageAuthentication();
// Cada página puede sobrescribir estas variables antes de incluir el encabezado.
$pageTitle = $pageTitle ?? 'Sistema de Gestión de Inventarios';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?> - SGI</title>
  <!-- Materialize aporta componentes y utilidades responsivas al módulo web. -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <link rel="stylesheet" href="assets/styles.css">
</head>
<body data-page="<?= htmlspecialchars($pageName ?? '') ?>">
  <!-- data-page permite que app.js ejecute solo el código necesario en esta vista. -->
  <header class="app-header">
    <a class="brand-link" href="dashboard.php"><i class="material-icons" aria-hidden="true">inventory_2</i> SGI</a>
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
