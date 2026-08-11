CREATE DATABASE IF NOT EXISTS sgi_inventario
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE sgi_inventario;

CREATE TABLE IF NOT EXISTS rol (
  id_rol INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL UNIQUE,
  descripcion VARCHAR(150) NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS usuario (
  id_usuario INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_rol INT UNSIGNED NOT NULL,
  nombres VARCHAR(100) NOT NULL,
  apellidos VARCHAR(100) NOT NULL,
  documento VARCHAR(20) NOT NULL UNIQUE,
  correo VARCHAR(120) NOT NULL UNIQUE,
  clave VARCHAR(255) NOT NULL,
  estado ENUM('Activo', 'Inactivo') NOT NULL DEFAULT 'Activo',
  CONSTRAINT fk_usuario_rol FOREIGN KEY (id_rol) REFERENCES rol(id_rol)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categoria (
  id_categoria INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL UNIQUE,
  descripcion VARCHAR(150) NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS producto (
  id_producto INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_categoria INT UNSIGNED NOT NULL,
  codigo VARCHAR(30) NOT NULL UNIQUE,
  nombre VARCHAR(120) NOT NULL,
  descripcion VARCHAR(255) NULL,
  unidad_medida VARCHAR(30) NOT NULL,
  stock_minimo INT UNSIGNED NOT NULL DEFAULT 0,
  stock_actual INT UNSIGNED NOT NULL DEFAULT 0,
  precio DECIMAL(12,2) UNSIGNED NOT NULL DEFAULT 0,
  estado ENUM('Activo', 'Inactivo') NOT NULL DEFAULT 'Activo',
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_producto_nombre (nombre),
  INDEX idx_producto_estado (estado),
  CONSTRAINT fk_producto_categoria FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS movimiento (
  id_movimiento INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_producto INT UNSIGNED NOT NULL,
  id_usuario INT UNSIGNED NOT NULL,
  tipo_movimiento ENUM('Entrada', 'Salida') NOT NULL,
  fecha_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  cantidad INT UNSIGNED NOT NULL,
  motivo VARCHAR(100) NOT NULL,
  observacion VARCHAR(255) NULL,
  INDEX idx_movimiento_fecha (fecha_hora),
  CONSTRAINT fk_movimiento_producto FOREIGN KEY (id_producto) REFERENCES producto(id_producto)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_movimiento_usuario FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ajuste (
  id_ajuste INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_producto INT UNSIGNED NOT NULL,
  id_usuario INT UNSIGNED NOT NULL,
  fecha_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  cantidad_ajustada INT NOT NULL,
  motivo VARCHAR(100) NOT NULL,
  observacion VARCHAR(255) NULL,
  INDEX idx_ajuste_fecha (fecha_hora),
  CONSTRAINT fk_ajuste_producto FOREIGN KEY (id_producto) REFERENCES producto(id_producto)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_ajuste_usuario FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT INTO rol (id_rol, nombre, descripcion) VALUES
  (1, 'Administrador', 'Acceso completo al sistema')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

INSERT INTO categoria (id_categoria, nombre, descripcion) VALUES
  (1, 'Electrónica', 'Equipos y accesorios electrónicos'),
  (2, 'Papelería', 'Suministros de oficina'),
  (3, 'Mobiliario', 'Muebles y elementos de oficina'),
  (4, 'Otros', 'Productos sin categoría específica')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

INSERT INTO usuario
  (id_usuario, id_rol, nombres, apellidos, documento, correo, clave, estado)
VALUES
  (1, 1, 'Administrador', 'SGI', '1000000000', 'admin@sgi.local',
   '$2y$10$nzZYHwH8qzoDGKSUKx6R9OGQehqXDI5Gj5dO1Cfd.IrQTlHCwaWHi', 'Activo')
ON DUPLICATE KEY UPDATE correo = VALUES(correo), clave = VALUES(clave), estado = VALUES(estado);

INSERT INTO producto
  (id_producto, id_categoria, codigo, nombre, descripcion, unidad_medida,
   stock_minimo, stock_actual, precio, estado)
VALUES
  (1, 1, 'PROD001', 'Laptop Dell XPS 15', 'Equipo portátil de alto rendimiento', 'Unidad', 5, 8, 5500000, 'Activo'),
  (2, 2, 'PROD002', 'Resma de papel A4', 'Papel blanco tamaño A4', 'Paquete', 10, 3, 23000, 'Activo'),
  (3, 3, 'PROD003', 'Silla ergonómica', 'Silla ajustable para oficina', 'Unidad', 5, 12, 850000, 'Activo')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);
