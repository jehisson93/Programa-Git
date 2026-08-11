<?php
// El parámetro id de la URL indica qué producto debe recuperar JavaScript.
$pageTitle = 'Modificar producto';
$pageName = 'editarProducto';
require __DIR__ . '/partials/header.php';
?>
<main class="container narrow">
  <section class="card">
    <p class="eyebrow">Productos</p>
    <h1>Modificar producto</h1>
    <?php
    $formId = 'formEditarProducto';
    $messageId = 'mensajeEditar';
    $isEditing = true;
    $submitLabel = 'Actualizar producto';
    require __DIR__ . '/partials/product-form.php';
    ?>
  </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
