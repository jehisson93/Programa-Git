  <!-- Pie y JavaScript compartidos para evitar repetirlos en cada página. -->
  <footer class="site-footer">Sistema de Gestión de Inventarios · Proyecto formativo ADSO</footer>
  <!-- El JavaScript de Materialize habilita componentes como notificaciones y selectores. -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
  <!-- La versión basada en la fecha del archivo evita ejecutar JavaScript antiguo desde la caché. -->
  <script src="assets/app.js?v=<?= rawurlencode((string) filemtime(__DIR__ . '/../assets/app.js')) ?>"></script>
</body>
</html>
