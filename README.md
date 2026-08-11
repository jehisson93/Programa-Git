# Sistema de Gestión de Inventarios - SGI

Proyecto académico desarrollado con PHP, MySQL, JavaScript, HTML y CSS para la evidencia **GA7-220501096-AA2-EV01 - Codificación de módulos del software**.

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

## Instalación en XAMPP

1. Copie la carpeta `prototipo_html DEFINITIVO` dentro de `C:\xampp\htdocs\`.
2. Abra el panel de XAMPP e inicie Apache y MySQL.
3. Entre a `http://localhost/phpmyadmin`.
4. Seleccione la pestaña **Importar** e importe `database/schema.sql`.
5. Abra `http://localhost/prototipo_html%20DEFINITIVO/`.

Credenciales iniciales:

- Correo: `admin@sgi.local`
- Contraseña: `Admin123*`

La conexión predeterminada se encuentra en `config/Database.php` y corresponde a la instalación normal de XAMPP: usuario `root`, sin contraseña y base de datos `sgi_inventario`.

## Estructura

```text
api/         Controladores HTTP y respuestas JSON
assets/      JavaScript y estilos de la interfaz
config/      Conexión PDO, sesión y funciones compartidas
database/    Script de creación y datos iniciales
docs/        Documento de apoyo para la evidencia
models/      Acceso a datos y reglas del inventario
partials/    Encabezado y pie reutilizables
prototipo-estatico/ Copia del prototipo HTML anterior
*.php        Pantallas del sistema
```

## Estándar de codificación

- PHP sigue PSR-12: clases en `PascalCase`, métodos y variables en `camelCase`.
- Tablas y columnas MySQL utilizan `snake_case`.
- JavaScript utiliza `camelCase` y constantes descriptivas.
- Las consultas utilizan PDO y sentencias preparadas.
- La lógica de base de datos está separada de las pantallas.

Consulte `docs/EVIDENCIA.md` para el guion de presentación y la relación con la lista de chequeo.
