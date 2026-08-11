<?php
$pageTitle = 'Registrar ajuste';
$pageName = 'ajuste';
require __DIR__ . '/partials/header.php';
?>
<main class="container narrow">
  <section class="card">
    <p class="eyebrow">Inventario</p>
    <h1>Registrar ajuste</h1>
    <p class="subtitle">Use un número positivo para sumar o negativo para descontar.</p>
    <form id="formAjuste">
      <label class="required" for="ajusteProducto">Producto</label>
      <select id="ajusteProducto" name="id_producto" required><option value="">Seleccionar</option></select>
      <div class="form-grid two">
        <div><label class="required" for="cantidadAjuste">Cantidad ajustada (+/-)</label><input id="cantidadAjuste" name="cantidad_ajustada" type="number" required></div>
        <div><label class="required" for="motivoAjuste">Motivo</label><input id="motivoAjuste" name="motivo" required maxlength="100"></div>
        <div class="field-full"><label for="observacionAjuste">Observación</label><textarea id="observacionAjuste" name="observacion" rows="3" maxlength="255"></textarea></div>
      </div>
      <p id="mensajeAjuste" class="message" role="status" hidden></p>
      <div class="buttons"><button class="primary" type="submit">Guardar ajuste</button><button class="secondary" type="reset">Limpiar</button></div>
    </form>
  </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
