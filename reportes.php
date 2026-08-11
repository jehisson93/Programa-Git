<?php
// Reúne consultas de inventario, movimientos y ajustes sin modificar datos.
$pageTitle = 'Reportes';
$pageName = 'reportes';
require __DIR__ . '/partials/header.php';
?>
<main class="container wide">
  <section class="card">
    <div class="section-heading">
      <div><p class="eyebrow">Consultas</p><h1>Reportes del inventario</h1></div>
      <button id="btnImprimir" class="secondary" type="button">Imprimir / Guardar PDF</button>
    </div>
    <div class="form-grid two filters">
      <div><label for="tipoReporte">Tipo de reporte</label><select id="tipoReporte"><option value="inventario">Inventario actual</option><option value="movimientos">Movimientos</option><option value="ajustes">Ajustes</option></select></div>
      <div class="buttons align-end"><button id="btnGenerarReporte" class="primary" type="button">Generar reporte</button><button id="btnExportarCsv" class="secondary" type="button">Exportar CSV</button></div>
    </div>
    <p id="mensajeReporte" class="message" role="status" hidden></p>
    <!-- app.js genera aquí una tabla que también puede imprimirse o exportarse. -->
    <section id="vistaReporte" class="preview-box" aria-live="polite">Seleccione un reporte.</section>
  </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
