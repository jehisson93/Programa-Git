<?php
// Registra entradas y salidas y delega la actualización segura del stock al modelo.
$pageTitle = 'Registrar movimiento';
$pageName = 'movimiento';
require __DIR__ . '/partials/header.php';
?>
<main class="container narrow">
  <section class="card">
    <p class="eyebrow">Inventario</p>
    <h1>Registrar movimiento</h1>
    <p class="subtitle">Una entrada suma existencias y una salida las descuenta.</p>
    <!-- El responsable no se pide aquí: se obtiene de la sesión iniciada. -->
    <form id="formMovimiento">
      <label class="required" for="movimientoProducto">Producto</label>
      <select id="movimientoProducto" name="id_producto" required><option value="">Seleccionar</option></select>
      <div class="form-grid two">
        <div><label class="required" for="tipoMovimiento">Tipo</label><select id="tipoMovimiento" name="tipo_movimiento" required><option value="">Seleccionar</option><option>Entrada</option><option>Salida</option></select></div>
        <div><label class="required" for="cantidadMovimiento">Cantidad</label><input id="cantidadMovimiento" name="cantidad" type="number" min="1" required></div>
        <div class="field-full"><label class="required" for="motivoMovimiento">Motivo</label><input id="motivoMovimiento" name="motivo" required maxlength="100"></div>
        <div class="field-full"><label for="observacionMovimiento">Observación</label><textarea id="observacionMovimiento" name="observacion" rows="3" maxlength="255"></textarea></div>
      </div>
      <p id="mensajeMovimiento" class="message" role="status" hidden></p>
      <div class="buttons"><button class="primary" type="submit">Guardar movimiento</button><button class="secondary" type="reset">Limpiar</button></div>
    </form>
  </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
