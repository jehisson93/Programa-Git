# Evidencia GA7-220501096-AA5-EV01

## Servicios web implementados

Para el proyecto SGI desarrollé una API REST en PHP que atiende las operaciones principales del sistema de gestión de inventarios. Los servicios intercambian información en formato JSON y utilizan los métodos HTTP `GET`, `POST`, `PUT` y `DELETE` según la acción solicitada.

La autenticación se mantiene mediante una sesión segura. Con excepción del inicio de sesión, todos los servicios requieren que el usuario se haya autenticado previamente. El registro de nuevas cuentas tiene una restricción adicional: solo puede ejecutarlo un usuario con rol Administrador.

La ruta base depende de la ubicación configurada para el proyecto en el servidor web. En los ejemplos se utiliza `http://localhost/PROYECTO%20SGI/api/`. Las solicitudes que envían información deben incluir el encabezado `Content-Type: application/json`.

## Resumen de los servicios

| Recurso | Método | Ruta | Función | Autorización |
|---|---|---|---|---|
| Autenticación | `POST` | `api/auth.php` | Iniciar sesión | Pública |
| Autenticación | `DELETE` | `api/auth.php` | Cerrar la sesión | Sesión actual |
| Usuarios | `POST` | `api/registro.php` | Registrar un usuario Operador | Administrador |
| Usuarios | `GET` | `api/usuarios.php` | Listar las cuentas registradas | Administrador |
| Usuarios | `PATCH` | `api/usuarios.php?id={id}` | Activar o desactivar una cuenta | Administrador |
| Productos | `GET` | `api/productos.php` | Listar o buscar productos | Usuario autenticado |
| Productos | `POST` | `api/productos.php` | Crear un producto | Usuario autenticado |
| Productos | `PUT` | `api/productos.php?id={id}` | Actualizar un producto | Usuario autenticado |
| Productos | `DELETE` | `api/productos.php?id={id}` | Eliminar o inactivar un producto | Usuario autenticado |
| Movimientos | `GET` | `api/movimientos.php` | Consultar entradas y salidas | Usuario autenticado |
| Movimientos | `POST` | `api/movimientos.php` | Registrar una entrada o salida | Usuario autenticado |
| Ajustes | `GET` | `api/ajustes.php` | Consultar ajustes de existencias | Usuario autenticado |
| Ajustes | `POST` | `api/ajustes.php` | Registrar un ajuste | Usuario autenticado |
| Catálogos | `GET` | `api/catalogos.php` | Consultar categorías y productos activos | Usuario autenticado |

## 1. Servicio de autenticación

### Iniciar sesión

- **Método:** `POST`
- **Ruta:** `api/auth.php`
- **Propósito:** validar las credenciales de un usuario activo y crear la sesión utilizada por los demás servicios.
- **Campos obligatorios:** correo y contraseña.

El servicio acepta los campos `correo` y `clave`, utilizados por la interfaz del SGI. También admite `usuario` y `contrasena` para conservar el contrato planteado en el caso académico.

Solicitud:

```json
{
  "correo": "admin@sgi.local",
  "clave": "Admin123*"
}
```

Respuesta satisfactoria (`200 OK`):

```json
{
  "message": "Autenticación satisfactoria.",
  "usuario": {
    "id_usuario": 1,
    "id_rol": 1,
    "nombres": "Administrador",
    "apellidos": "SGI",
    "correo": "admin@sgi.local"
  }
}
```

Se valida que el usuario sea un correo válido, que la contraseña no esté vacía y que la cuenta exista y esté activa. La contraseña se compara con el hash almacenado y, después del ingreso, se regenera el identificador de sesión. Si las credenciales no coinciden, se responde `401 Unauthorized` con el mensaje `Error en la autenticación.`

### Cerrar sesión

- **Método:** `DELETE`
- **Ruta:** `api/auth.php`
- **Propósito:** eliminar los datos de la sesión actual.

Respuesta (`200 OK`):

```json
{
  "message": "Sesión cerrada correctamente."
}
```

## 2. Servicio de registro de usuarios

- **Método:** `POST`
- **Ruta:** `api/registro.php`
- **Propósito:** permitir que un administrador registre una cuenta con rol Operador.
- **Autorización:** sesión administradora.

Solicitud:

```json
{
  "nombres": "Laura",
  "apellidos": "Gómez Pérez",
  "documento": "1020304050",
  "usuario": "laura.gomez@sgi.local",
  "contrasena": "Operador123*"
}
```

Respuesta (`201 Created`):

```json
{
  "message": "Registro satisfactorio."
}
```

Se comprueba que todos los campos estén presentes, que el usuario sea un correo válido, que la contraseña tenga al menos ocho caracteres y que el correo y el documento no estén repetidos. La contraseña se guarda mediante un hash. El servicio puede responder `401` si no hay sesión, `403` si el usuario no es administrador, `409` si existe un duplicado, `422` si los datos no son válidos o `503` si el rol Operador no está configurado.

### Gestión básica de cuentas

La vista `usuarios.php` permite que el administrador consulte las cuentas y cambie su estado entre `Activo` e `Inactivo`. Para conservar la trazabilidad no se eliminan usuarios. El servicio también impide que el administrador desactive la cuenta con la que mantiene abierta la sesión.

- **Listar:** `GET api/usuarios.php`.
- **Cambiar estado:** `PATCH api/usuarios.php?id={id}`.

```json
{
  "estado": "Inactivo"
}
```

## 3. Servicio de productos

Este recurso implementa las operaciones CRUD del catálogo de productos.

### Listar o consultar productos

- **Método:** `GET`
- **Ruta para listar:** `api/productos.php`
- **Filtros opcionales:** `busqueda`, `categoria` y `estado`.
- **Ruta para consultar uno:** `api/productos.php?id={id}`.

Ejemplo con filtros: `api/productos.php?busqueda=laptop&categoria=1&estado=Activo`.

Respuesta de listado (`200 OK`):

```json
{
  "productos": [
    {
      "id_producto": 1,
      "id_categoria": 1,
      "codigo": "PROD001",
      "nombre": "Laptop Dell XPS 15",
      "descripcion": "Equipo portátil de alto rendimiento",
      "unidad_medida": "Unidad",
      "stock_minimo": 5,
      "stock_actual": 8,
      "precio": "5500000.00",
      "estado": "Activo",
      "categoria": "Electrónica"
    }
  ]
}
```

La consulta por identificador retorna la propiedad `producto`. Si el registro no existe, responde `404 Not Found`.

### Crear un producto

- **Método:** `POST`
- **Ruta:** `api/productos.php`

Solicitud:

```json
{
  "id_categoria": 2,
  "codigo": "PAP-004",
  "nombre": "Caja de marcadores",
  "descripcion": "Marcadores permanentes de varios colores",
  "unidad_medida": "Caja",
  "stock_minimo": 5,
  "stock_actual": 20,
  "precio": 18000,
  "estado": "Activo"
}
```

Respuesta (`201 Created`):

```json
{
  "message": "Producto registrado correctamente.",
  "id_producto": 4
}
```

### Actualizar un producto

- **Método:** `PUT`
- **Ruta:** `api/productos.php?id={id}`

La actualización recibe `id_categoria`, `nombre`, `descripcion`, `unidad_medida`, `stock_minimo`, `stock_actual`, `precio` y `estado`. El código no se modifica mediante esta operación.

```json
{
  "message": "Producto actualizado correctamente."
}
```

### Eliminar o inactivar un producto

- **Método:** `DELETE`
- **Ruta:** `api/productos.php?id={id}`

Si el producto tiene movimientos o ajustes asociados, cambio su estado a `Inactivo` para conservar la trazabilidad. Si no tiene historial, se elimina físicamente.

```json
{
  "message": "Producto eliminado o inactivado correctamente."
}
```

Las validaciones comprueban la categoría, los campos obligatorios, el código único, el formato alfanumérico del código, los valores no negativos de stock y precio, los estados `Activo` o `Inactivo` y la existencia del producto cuando se actualiza o elimina.

## 4. Servicio de movimientos de inventario

### Consultar movimientos

- **Método:** `GET`
- **Ruta:** `api/movimientos.php`
- **Propósito:** obtener el historial de entradas y salidas desde el registro más reciente.

```json
{
  "movimientos": [
    {
      "fecha_hora": "2026-10-14 10:30:00",
      "codigo": "PROD001",
      "producto": "Laptop Dell XPS 15",
      "tipo_movimiento": "Entrada",
      "cantidad": 2,
      "usuario": "Administrador",
      "motivo": "Compra a proveedor"
    }
  ]
}
```

### Registrar un movimiento

- **Método:** `POST`
- **Ruta:** `api/movimientos.php`

Solicitud:

```json
{
  "id_producto": 1,
  "tipo_movimiento": "Salida",
  "cantidad": 2,
  "motivo": "Entrega al área administrativa",
  "observacion": "Solicitud interna 015"
}
```

Respuesta (`201 Created`):

```json
{
  "message": "Movimiento registrado correctamente.",
  "id_movimiento": 1
}
```

El producto debe existir y estar activo, la cantidad debe ser un entero positivo y el tipo debe ser `Entrada` o `Salida`. Una salida no puede dejar existencias negativas. El responsable se toma de la sesión y no de un campo manipulable. Además, el movimiento y la actualización del stock se ejecutan en una transacción para que ambos cambios se confirmen o se reviertan juntos.

## 5. Servicio de ajustes de inventario

### Consultar ajustes

- **Método:** `GET`
- **Ruta:** `api/ajustes.php`

```json
{
  "ajustes": [
    {
      "fecha_hora": "2026-10-14 11:00:00",
      "codigo": "PROD002",
      "producto": "Resma de papel A4",
      "cantidad": -1,
      "usuario": "Administrador",
      "motivo": "Deterioro"
    }
  ]
}
```

### Registrar un ajuste

- **Método:** `POST`
- **Ruta:** `api/ajustes.php`

Solicitud:

```json
{
  "id_producto": 2,
  "cantidad_ajustada": -1,
  "motivo": "Producto deteriorado",
  "observacion": "Novedad encontrada durante el conteo físico"
}
```

Respuesta (`201 Created`):

```json
{
  "message": "Ajuste registrado correctamente.",
  "id_ajuste": 1
}
```

El producto debe existir y estar activo. La cantidad ajustada debe ser un entero diferente de cero: un valor positivo aumenta el stock y uno negativo lo disminuye. El resultado no puede dejar existencias negativas. El motivo es obligatorio, la observación es opcional y el responsable se obtiene de la sesión. El ajuste y el cambio del stock se procesan en una misma transacción.

## 6. Servicio de catálogos

- **Método:** `GET`
- **Ruta:** `api/catalogos.php`
- **Propósito:** entregar las categorías y los productos activos utilizados en los formularios.

```json
{
  "categorias": [
    {
      "id_categoria": 1,
      "nombre": "Electrónica"
    }
  ],
  "productos": [
    {
      "id_producto": 1,
      "codigo": "PROD001",
      "nombre": "Laptop Dell XPS 15",
      "stock_actual": 8
    }
  ]
}
```

Este servicio no recibe datos enviados por el usuario y solo devuelve productos cuyo estado sea `Activo`.

## Respuestas generales

| Estado HTTP | Significado dentro del SGI |
|---|---|
| `200 OK` | La consulta, actualización, eliminación o autenticación fue correcta. |
| `201 Created` | Se creó un usuario, producto, movimiento o ajuste. |
| `400 Bad Request` | El cuerpo no contiene un JSON válido. |
| `401 Unauthorized` | No hay sesión o las credenciales son incorrectas. |
| `403 Forbidden` | El usuario no tiene el rol requerido. |
| `404 Not Found` | No se encontró el producto consultado. |
| `405 Method Not Allowed` | El método o identificador no es válido para la ruta. |
| `409 Conflict` | Existe un registro duplicado. |
| `422 Unprocessable Entity` | Los datos incumplen una validación del negocio. |
| `500 Internal Server Error` | Ocurrió un error técnico durante el procesamiento. |
| `503 Service Unavailable` | Falta una configuración necesaria, como el rol Operador. |

Todas las respuestas usan JSON. La propiedad `message` comunica el resultado o la causa controlada del error.

## Preparación de la base de datos

En una instalación nueva se debe importar `database/schema.sql`. Si la base ya estaba instalada antes de incorporar la autenticación por roles, se ejecuta una vez `database/migration_auth.sql` para agregar el rol Operador.

## Casos de prueba sugeridos

1. Iniciar sesión con la cuenta administradora y probar también una contraseña incorrecta.
2. Registrar un Operador y repetir el registro para comprobar el conflicto por duplicado.
3. Crear, consultar, actualizar y eliminar o inactivar un producto.
4. Intentar crear otro producto con el mismo código.
5. Consultar productos aplicando filtros de texto, categoría y estado.
6. Registrar una entrada y verificar que el stock aumente.
7. Registrar una salida válida y comprobar que el stock disminuya.
8. Intentar una salida superior al stock y confirmar que no se alteren las existencias.
9. Registrar ajustes positivos y negativos y verificar el resultado.
10. Intentar un ajuste que deje el stock negativo y comprobar que se revierta.
11. Consultar movimientos, ajustes y catálogos.
12. Cerrar la sesión y confirmar que los servicios protegidos respondan `401`.

Las pruebas pueden ejecutarse desde Postman con `Content-Type: application/json`. Como la autenticación funciona mediante sesión, Postman debe conservar la cookie generada al iniciar sesión para enviarla en las solicitudes posteriores.

## Relación con los criterios de evaluación

Con estos servicios cubro las necesidades principales del proyecto formativo: administración de productos, control de entradas y salidas, corrección de existencias, consulta de información auxiliar y control de acceso. La selección de métodos HTTP organiza la API con un enfoque REST, mientras que las validaciones del servidor protegen la integridad de la información. Finalmente, el proyecto se administra con Git, lo que permite conservar el historial de cambios y aplicar versionamiento durante el desarrollo.
