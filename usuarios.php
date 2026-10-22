<?php

// Esta vista contiene funciones exclusivas para el administrador.
require_once __DIR__ . '/config/helpers.php';
requireAdminPageAuthentication();

$pageTitle = 'Gestión de usuarios';
$pageName = 'usuarios';
require __DIR__ . '/partials/header.php';
?>
<main class="container wide">
  <section class="card" aria-labelledby="tituloUsuarios">
    <div class="section-heading">
      <div>
        <p class="eyebrow">Administración de acceso</p>
        <h1 id="tituloUsuarios">Usuarios del sistema</h1>
        <p class="subtitle left-text">Consulte las cuentas y controle quién puede ingresar al SGI.</p>
      </div>
      <a class="btn waves-effect waves-light primary" href="registro-usuario.php">
        Registrar usuario <i class="material-icons right" aria-hidden="true">person_add</i>
      </a>
    </div>

    <p id="mensajeUsuarios" class="message" role="alert" hidden></p>
    <p id="cantidadUsuarios" class="results-count" aria-live="polite"></p>

    <div class="table-wrap">
      <table aria-label="Usuarios registrados">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Documento</th>
            <th>Correo</th>
            <th>Rol</th>
            <th>Estado</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody id="tablaUsuarios">
          <tr><td colspan="6" class="empty">Cargando usuarios...</td></tr>
        </tbody>
      </table>
    </div>
  </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
