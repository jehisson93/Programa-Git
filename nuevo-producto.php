<?php
// Configura el título y el inicializador JavaScript que usará esta pantalla.
$pageTitle = 'Registrar producto';
$pageName = 'nuevoProducto';
require __DIR__ . '/partials/header.php';
?>
<main class="container narrow">
  <section class="card">
    <p class="eyebrow">Productos</p>
    <h1>Registrar producto</h1>
    <p class="subtitle">Ingrese la información básica del producto.</p>
    <?php
    // El mismo formulario se utiliza al crear y editar para evitar código duplicado.
    $formId = 'formProducto';
    $messageId = 'mensajeProducto';
    $isEditing = false;
    $submitLabel = 'Guardar producto';
    require __DIR__ . '/partials/product-form.php';
    ?>
  </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
