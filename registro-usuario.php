<?php

// Solo un administrador autenticado puede abrir el formulario de registro.
require_once __DIR__ . '/config/helpers.php';
requireAdminPageAuthentication();

$pageTitle = 'Registrar usuario';
$pageName = 'registroUsuario';
require __DIR__ . '/partials/header.php';
?>
<main class="container narrow">
  <section class="card" aria-labelledby="tituloRegistro">
    <p class="eyebrow">Administración de acceso</p>
    <h1 id="tituloRegistro">Registrar usuario</h1>
    <p class="subtitle">Cree una cuenta de operador para ingresar al sistema de inventarios.</p>
    <form id="formRegistroUsuario">
      <div class="form-grid two">
        <div>
          <label class="required" for="nombres">Nombres</label>
          <input id="nombres" name="nombres" type="text" maxlength="100" autocomplete="given-name" required>
        </div>
        <div>
          <label class="required" for="apellidos">Apellidos</label>
          <input id="apellidos" name="apellidos" type="text" maxlength="100" autocomplete="family-name" required>
        </div>
        <div>
          <label class="required" for="documento">Documento</label>
          <input id="documento" name="documento" type="text" maxlength="20" inputmode="numeric" required>
        </div>
        <div>
          <label class="required" for="usuario">Correo electrónico</label>
          <input id="usuario" name="usuario" type="email" maxlength="120" autocomplete="username" required>
        </div>
        <div>
          <label class="required" for="contrasena">Contraseña</label>
          <input id="contrasena" name="contrasena" type="password" minlength="8" autocomplete="new-password" required>
          <p class="help">Debe contener por lo menos ocho caracteres.</p>
        </div>
        <div>
          <label class="required" for="confirmar_contrasena">Confirmar contraseña</label>
          <input id="confirmar_contrasena" name="confirmar_contrasena" type="password" minlength="8" autocomplete="new-password" required>
        </div>
      </div>
      <p id="mensajeRegistroUsuario" class="message" role="alert" hidden></p>
      <div class="buttons">
        <button class="btn waves-effect waves-light primary" type="submit">Registrar usuario <i class="material-icons right" aria-hidden="true">person_add</i></button>
        <button class="btn waves-effect waves-light dark" type="reset">Limpiar</button>
      </div>
    </form>
  </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
