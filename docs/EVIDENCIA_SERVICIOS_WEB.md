# Evidencia GA7-220501096-AA5-EV01

## Servicios implementados

El proyecto incluye dos servicios web relacionados con el acceso al SGI:

- `POST api/registro.php`: registra una cuenta con rol Operador. Requiere una sesión administradora.
- `POST api/auth.php`: comprueba las credenciales y crea la sesión.
- `DELETE api/auth.php`: cierra la sesión actual.

El inicio de sesión acepta tanto los nombres `correo` y `clave`, usados por la interfaz actual, como `usuario` y `contrasena`, definidos en el caso de estudio.

## Preparación de la base de datos

En una instalación nueva, importe `database/schema.sql`. Si la base ya estaba instalada, ejecute una vez `database/migration_auth.sql` para agregar el rol Operador.

## Casos de prueba

1. Inicie sesión con la cuenta administradora.
2. Abra la opción Usuarios y registre una cuenta con un correo y un documento nuevos.
3. Compruebe que la API responde con estado 201 y el mensaje `Registro satisfactorio.`.
4. Repita el registro para comprobar la respuesta 409 por duplicado.
5. Cierre la sesión e ingrese con la cuenta creada.
6. Pruebe una contraseña incorrecta para comprobar la respuesta 401 y el mensaje `Error en la autenticación.`.
7. Revise en MySQL que la columna `clave` contiene un hash y que la cuenta utiliza el rol Operador.

Las solicitudes también pueden ejecutarse desde Postman con encabezado `Content-Type: application/json`.
