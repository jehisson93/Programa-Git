# Guía para entender el código

Esta guía explica el orden recomendado para estudiar el proyecto. No es
necesario comprender todos los archivos al mismo tiempo.

## 1. Recorrido de una solicitud

Cuando se registra un producto ocurre lo siguiente:

```text
nuevo-producto.php
  -> assets/app.js lee el formulario
  -> api/productos.php recibe POST
  -> models/Producto.php valida y ejecuta INSERT
  -> config/Database.php abre la conexión PDO
  -> MySQL guarda el registro
  -> la API responde JSON
  -> app.js muestra el mensaje al usuario
```

## 2. Páginas y componentes

- `index.php`: formulario público de inicio de sesión.
- `dashboard.php`: menú principal después de ingresar.
- `productos.php`: consulta y eliminación de productos.
- `nuevo-producto.php`: creación de productos.
- `editar-producto.php`: actualización de productos.
- `movimiento.php`: entradas y salidas.
- `ajuste.php`: correcciones del stock.
- `reportes.php`: consultas, CSV e impresión.
- `partials/header.php`: encabezado y control de sesión compartidos.
- `partials/footer.php`: pie y carga de JavaScript compartidos.
- `partials/product-form.php`: campos usados tanto al crear como al editar.

## 3. Backend PHP

`config/Database.php` crea una sola conexión PDO. `config/helpers.php` contiene
funciones comunes de sesiones, respuestas JSON y validación. Los archivos de
`api/` reciben solicitudes HTTP y deciden qué método del modelo ejecutar. Los
archivos de `models/` contienen SQL y reglas de negocio.

Separar estas responsabilidades evita colocar consultas SQL dentro del HTML.

## 4. CRUD de productos

| Acción | Método HTTP | Método PHP | SQL |
|---|---|---|---|
| Crear | POST | `Producto::crear()` | INSERT |
| Consultar | GET | `Producto::listar()` | SELECT |
| Actualizar | PUT | `Producto::actualizar()` | UPDATE |
| Eliminar | DELETE | `Producto::eliminar()` | DELETE o UPDATE |

Si un producto tiene historial se inactiva en lugar de borrarse. Esta decisión
mantiene disponibles los movimientos y ajustes anteriores.

## 5. JavaScript

`assets/app.js` usa `fetch()` para comunicarse con las API. La función
`apiRequest()` centraliza solicitudes y errores. Al final del archivo, el valor
`data-page` de cada página decide qué inicializador ejecutar; así no se ejecuta
código de reportes en la pantalla de productos, por ejemplo.

## 6. Base de datos

`database/schema.sql` crea tablas, relaciones, índices y datos iniciales. Las
claves foráneas evitan registros sin relación válida y los índices aceleran las
búsquedas. La contraseña inicial se guarda como hash.

## 7. Transacciones

Una entrada, salida o ajuste debe guardar el historial y actualizar el stock.
Si alguna parte falla, `rollBack()` deshace todo. Si ambas funcionan, `commit()`
confirma todo. `FOR UPDATE` evita cambios simultáneos sobre el mismo producto.

## 8. Orden recomendado de estudio

1. Abra `nuevo-producto.php` y ubique el formulario compartido.
2. Busque `initializeNewProduct()` en `assets/app.js`.
3. Revise el bloque POST de `api/productos.php`.
4. Revise `Producto::crear()`.
5. Revise `Database::getConnection()`.
6. Compare los nombres utilizados con la tabla `producto` de `schema.sql`.

Repita el recorrido con consultar, actualizar y eliminar. Esta técnica permite
entender el sistema por operaciones completas en lugar de memorizar archivos.
