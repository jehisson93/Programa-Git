<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar sesión - SGI</title>
  <link rel="stylesheet" href="assets/styles.css">
</head>
<body data-page="login">
  <!-- Esta página es pública; las demás comprueban una sesión PHP activa. -->
  <main class="container centered">
    <section class="card auth-card" aria-labelledby="tituloLogin">
      <p class="eyebrow">Sistema de Gestión de Inventarios</p>
      <h1 id="tituloLogin">Iniciar sesión</h1>
      <p class="subtitle">Ingrese sus credenciales para administrar el inventario.</p>
      <!-- JavaScript envía este formulario a api/auth.php sin recargar la página. -->
      <form id="formLogin">
        <label class="required" for="correo">Correo electrónico</label>
        <input id="correo" name="correo" type="email" autocomplete="username" required value="admin@sgi.local">
        <label class="required" for="clave">Contraseña</label>
        <input id="clave" name="clave" type="password" autocomplete="current-password" required>
        <p id="mensajeLogin" class="message" role="alert" hidden></p>
        <div class="buttons"><button class="primary full-button" type="submit">Ingresar</button></div>
      </form>
      <p class="demo-note"><strong>Acceso inicial:</strong> admin@sgi.local / Admin123*</p>
    </section>
  </main>
  <script src="assets/app.js"></script>
</body>
</html>
