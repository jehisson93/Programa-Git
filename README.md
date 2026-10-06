# Sistema de Gestión de Inventarios - SGI

Proyecto académico desarrollado con PHP, MySQL, JavaScript, HTML, CSS y el framework de interfaz Materialize para la evidencia **GA7-220501096-AA3-EV01 - Codificación de módulos del software stand-alone, web y móvil**. La solución se presenta en la modalidad web.

## Funcionalidades

- Inicio de sesión con contraseña cifrada.
- Registro, consulta, actualización y eliminación/inactivación de productos.
- Filtros por texto, categoría y estado.
- Registro de entradas y salidas de inventario.
- Ajustes positivos y negativos.
- Actualización transaccional del stock.
- Reportes de inventario, movimientos y ajustes.
- Exportación CSV e impresión en PDF desde el navegador.

## Requisitos

- XAMPP con Apache, PHP 8.0 o superior y MySQL.
- Navegador web moderno.
- Git para consultar o ampliar el historial de versiones.
- Conexión a internet para cargar Materialize 1.0.0 y los iconos de Google desde CDN.

## Instalación en XAMPP

1. Copie la carpeta `PROYECTO SGI` dentro de `C:\xampp\htdocs\`.
2. Abra el panel de XAMPP e inicie Apache y MySQL.
3. Entre a `http://localhost/phpmyadmin`.
4. Seleccione la pestaña **Importar** e importe `database/schema.sql`.
5. Abra `http://localhost/PROYECTO%20SGI/`.

Credenciales iniciales:

- Correo: `admin@sgi.local`
- Contraseña: `Admin123*`

La conexión predeterminada se encuentra en `config/Database.php` y corresponde a la instalación normal de XAMPP: usuario `root`, sin contraseña y base de datos `sgi_inventario`.

## Contenido del script de base de datos

`database/schema.sql` crea exactamente seis tablas: `rol`, `usuario`,
`categoria`, `producto`, `movimiento` y `ajuste`. También registra un rol
administrador, cuatro categorías, el usuario inicial y tres productos de
ejemplo con los códigos `PROD001`, `PROD002` y `PROD003`.

Los códigos utilizados en las pruebas de la evidencia, como `EV02-589462`, se
registraron desde la interfaz durante la ejecución de los casos de prueba. No
forman parte de los datos iniciales del script. El esquema tampoco incluye
tablas para notificaciones, filtros guardados o administración avanzada de
usuarios, porque esas funciones no están implementadas en esta versión.

## Estructura

```text
api/         Controladores HTTP y respuestas JSON
assets/      JavaScript y estilos de la interfaz
config/      Conexión PDO, sesión y funciones compartidas
database/    Script de creación y datos iniciales
docs/        Documento de apoyo para la evidencia
models/      Acceso a datos y reglas del inventario
partials/    Encabezado y pie reutilizables
*.php        Pantallas del sistema
```

La aplicación mantiene una sola versión activa. Los antiguos archivos HTML no
se duplican dentro del proyecto porque las páginas PHP ya contienen el HTML de
la interfaz y, además, permiten trabajar con sesiones y MySQL.

## Estándar de codificación

- PHP sigue PSR-12: clases en `PascalCase`, métodos y variables en `camelCase`.
- Tablas y columnas MySQL utilizan `snake_case`.
- JavaScript utiliza `camelCase` y constantes descriptivas.
- Materialize 1.0.0 aporta botones, iconos, tablas responsivas, efectos visuales y notificaciones Toast.
- Las consultas utilizan PDO y sentencias preparadas.
- La lógica de base de datos está separada de las pantallas.
- El formulario de productos se reutiliza en creación y edición mediante
  `partials/product-form.php`.
- El código contiene comentarios por bloques y decisiones importantes para
  facilitar su estudio sin repetir explicaciones obvias en cada línea.

Consulte `docs/EVIDENCIA.md` para el guion de presentación y
`docs/GUIA_CODIGO.md` para estudiar el recorrido del programa.
