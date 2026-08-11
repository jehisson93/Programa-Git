<?php
$pageTitle = 'Registrar producto';
$pageName = 'nuevoProducto';
require __DIR__ . '/partials/header.php';
?>
<main class="container narrow">
  <section class="card">
    <p class="eyebrow">Productos</p>
    <h1>Registrar producto</h1>
    <p class="subtitle">Ingrese la información básica del producto.</p>
    <form id="formProducto">
      <div class="form-grid two">
        <div><label class="required" for="codigo">Código</label><input id="codigo" name="codigo" required maxlength="30" pattern="[A-Za-z0-9-]+"></div>
        <div><label class="required" for="nombre">Nombre</label><input id="nombre" name="nombre" required maxlength="120"></div>
        <div class="field-full"><label for="descripcion">Descripción</label><textarea id="descripcion" name="descripcion" rows="3" maxlength="255"></textarea></div>
        <div><label class="required" for="id_categoria">Categoría</label><select id="id_categoria" name="id_categoria" required><option value="">Seleccionar</option></select></div>
        <div><label class="required" for="unidad_medida">Unidad de medida</label><input id="unidad_medida" name="unidad_medida" placeholder="Ejemplo: Unidad" required maxlength="30"></div>
        <div><label class="required" for="stock_actual">Stock actual</label><input id="stock_actual" name="stock_actual" type="number" min="0" value="0" required></div>
        <div><label class="required" for="stock_minimo">Stock mínimo</label><input id="stock_minimo" name="stock_minimo" type="number" min="0" value="0" required></div>
        <div><label class="required" for="precio">Precio unitario (COP)</label><input id="precio" name="precio" type="number" min="0" step="0.01" value="0" required></div>
        <div><label class="required" for="estado">Estado</label><select id="estado" name="estado" required><option>Activo</option><option>Inactivo</option></select></div>
      </div>
      <p id="mensajeProducto" class="message" role="status" hidden></p>
      <div class="buttons"><button class="primary" type="submit">Guardar producto</button><a class="btn-link secondary" href="productos.php">Cancelar</a></div>
    </form>
  </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
