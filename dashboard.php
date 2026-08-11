<?php
// Variables consumidas por partials/header.php y assets/app.js.
$pageTitle = 'Menú principal';
$pageName = 'dashboard';
require __DIR__ . '/partials/header.php';
?>
<main class="container wide">
  <!-- El panel solo contiene accesos; las operaciones viven en módulos separados. -->
  <section class="hero-card">
    <div>
      <p class="eyebrow">Panel principal</p>
      <h1>Control de inventario</h1>
      <p>Administre productos, entradas, salidas y ajustes desde un solo lugar.</p>
    </div>
    <a class="btn-link primary" href="nuevo-producto.php">Registrar producto</a>
  </section>
  <section class="menu-grid" aria-label="Módulos disponibles">
    <a class="menu-item badge-blue" href="productos.php"><strong>Productos</strong><span>Consultar, modificar y eliminar productos.</span></a>
    <a class="menu-item badge-green" href="nuevo-producto.php"><strong>Nuevo producto</strong><span>Registrar un artículo en la base de datos.</span></a>
    <a class="menu-item badge-purple" href="movimiento.php"><strong>Movimientos</strong><span>Registrar entradas y salidas de existencias.</span></a>
    <a class="menu-item badge-yellow" href="ajuste.php"><strong>Ajustes</strong><span>Corregir diferencias del inventario físico.</span></a>
    <a class="menu-item badge-red" href="reportes.php"><strong>Reportes</strong><span>Consultar el inventario y su historial.</span></a>
  </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
