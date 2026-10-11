USE sgi_inventario;

-- Agrega el rol asignado por el servicio de registro sin modificar los usuarios existentes.
INSERT INTO rol (nombre, descripcion) VALUES
  ('Operador', 'Acceso operativo al sistema de inventarios')
ON DUPLICATE KEY UPDATE
  nombre = VALUES(nombre),
  descripcion = VALUES(descripcion);
