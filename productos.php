<?php
$pageTitle = 'Productos';
$pageName = 'productos';
require __DIR__ . '/partials/header.php';
?>
<main class="container wide">
  <section class="card">
    <div class="section-heading">
      <div><p class="eyebrow">CRUD de productos</p><h1>Consultar productos</h1></div>
      <a class="btn-link primary" href="nuevo-producto.php">Nuevo producto</a>
    </div>
    <form id="formFiltros" class="filters">
      <div class="form-grid three">
        <div><label for="buscarProducto">Nombre o código</label><input id="buscarProducto" type="search" placeholder="Ejemplo: PROD001"></div>
        <div><label for="filtroCategoria">Categoría</label><select id="filtroCategoria"><option value="">Todas</option></select></div>
        <div><label for="filtroEstado">Estado</label><select id="filtroEstado"><option value="">Todos</option><option>Activo</option><option>Inactivo</option></select></div>
      </div>
      <div class="buttons"><button class="primary" type="submit">Consultar</button><button id="btnLimpiarFiltros" class="secondary" type="button">Limpiar</button></div>
    </form>
    <p id="mensajeProductos" class="message" role="status" hidden></p>
    <p id="cantidadResultados" class="results-count" aria-live="polite"></p>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Código</th><th>Nombre</th><th>Categoría</th><th>Stock</th><th>Mínimo</th><th>Precio</th><th>Estado</th><th>Acciones</th></tr></thead>
        <tbody id="tablaProductos"><tr><td colspan="8" class="empty">Cargando productos…</td></tr></tbody>
      </table>
    </div>
  </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
