# Evidencia GA7-220501096-AA2-EV01

## 1. Descripción del módulo

Se codificó el módulo de gestión de inventarios de acuerdo con los prototipos y el modelo de datos del proyecto. El módulo permite administrar productos y registrar los cambios en sus existencias.

El prototipo HTML utilizado como punto de partida se conserva dentro de `prototipo-estatico/`. La raíz del proyecto contiene la versión funcional integrada con PHP y MySQL.

La solución utiliza una arquitectura cliente-servidor:

```text
HTML/CSS + JavaScript -> API PHP -> PDO -> MySQL
```

La guía menciona JDBC para proyectos Java. Para esta implementación, y de acuerdo con la orientación del instructor, se utiliza **PDO para MySQL**, que cumple la misma responsabilidad dentro de la tecnología seleccionada: abrir la conexión, preparar sentencias, enviar parámetros, ejecutar operaciones y recuperar resultados.

## 2. Relación con la lista de chequeo

### Conexión con la base de datos

- Archivo: `config/Database.php`.
- Tecnología: PDO con controlador `pdo_mysql`.
- La conexión utiliza UTF-8, excepciones y consultas preparadas reales.

### CRUD

- Crear: `Producto::crear()` ejecuta `INSERT`.
- Consultar: `Producto::listar()` y `Producto::buscarPorId()` ejecutan `SELECT`.
- Actualizar: `Producto::actualizar()` ejecuta `UPDATE`.
- Eliminar: `Producto::eliminar()` ejecuta `DELETE` cuando no existe historial. Cuando hay movimientos o ajustes, cambia el estado a `Inactivo` para conservar la integridad del historial.
- Controlador: `api/productos.php` expone POST, GET, PUT y DELETE.

### Versionamiento

El proyecto incluye un repositorio Git y un archivo `.gitignore`. Para continuar el desarrollo se recomiendan mensajes de commit como:

```text
feat: agregar registro de productos con PDO
feat: implementar consulta y filtros de productos
feat: agregar actualización y eliminación de productos
feat: registrar movimientos mediante transacciones
docs: documentar instalación y pruebas del sistema
```

### Estándares de codificación

- Clases PHP: PascalCase (`Database`, `Producto`, `Inventario`).
- Métodos y variables: camelCase (`getConnection`, `buscarPorId`, `$productId`).
- Tablas y columnas: snake_case (`stock_actual`, `id_producto`).
- Constantes: MAYÚSCULAS (`HOST`, `NAME`, `USER`).
- Código separado por responsabilidades: configuración, modelos, API, interfaz y recursos.

## 3. Seguridad e integridad

- Sentencias preparadas para evitar inyección SQL.
- Contraseña almacenada con `password_hash` y validada con `password_verify`.
- Sesión PHP con cookie `HttpOnly` y regeneración del identificador al ingresar.
- Validaciones tanto en HTML como en PHP.
- Entradas, salidas y ajustes usan transacciones y bloqueo de fila (`FOR UPDATE`).
- No se permite que el stock quede negativo.
- Las claves foráneas conservan la integridad de los datos.

## 4. Pruebas sugeridas para el video o las capturas

1. Mostrar XAMPP con Apache y MySQL activos.
2. Mostrar en phpMyAdmin las seis tablas creadas.
3. Iniciar sesión con el usuario administrador.
4. Registrar un producto nuevo y comprobarlo en phpMyAdmin.
5. Consultarlo mediante el filtro por código.
6. Modificar su nombre, precio o stock mínimo.
7. Registrar una entrada y comprobar el aumento del stock.
8. Registrar una salida y comprobar la disminución del stock.
9. Intentar una salida superior al stock para mostrar la validación.
10. Eliminar un producto sin historial y luego inactivar uno con historial.
11. Generar un reporte y exportarlo a CSV.
12. Mostrar el historial de commits con `git log --oneline`.

## 5. Evidencias visuales recomendadas

- Diagrama de clases y modelo relacional usados como referencia.
- Estructura de carpetas del proyecto.
- Código de `Database.php`.
- Métodos CRUD de `Producto.php`.
- Pruebas funcionales del CRUD.
- Registros correspondientes dentro de MySQL.
- Historial de Git.

## 6. Conclusión propuesta

Se implementó el módulo de gestión de inventarios con PHP, JavaScript y MySQL, respetando los artefactos de diseño definidos previamente. La aplicación realiza las operaciones CRUD mediante PDO, protege la integridad del inventario con transacciones y aplica convenciones uniformes de codificación y versionamiento.
