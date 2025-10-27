CREATE DATABASE IF NOT EXISTS smart_wallet;
USE smart_wallet;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) UNIQUE NOT NULL,
    contrasena VARCHAR(255) NOT NULL
);

CREATE TABLE ingresos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    categoria VARCHAR(50),
    descripcion TEXT,
    fecha DATE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE gastos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    categoria VARCHAR(50),
    descripcion TEXT,
    fecha DATE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE metas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nombre_meta VARCHAR(100),
    categoria VARCHAR(50) NOT NULL,
    monto_objetivo DECIMAL(10,2),
    monto_actual DECIMAL(10,2) DEFAULT 0,
    fecha_inicio DATE,
    fecha_fin DATE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabla de categorías para ingresos y gastos
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    tipo ENUM('ingreso','gasto') NOT NULL,
    icono VARCHAR(50) NOT NULL,
    color VARCHAR(20) NOT NULL DEFAULT '#CCCCCC'
);

-- Ejemplo de inserción de categorías iniciales con iconos de Bootstrap
INSERT INTO categorias (nombre, tipo, icono, color) VALUES
('Sueldo', 'ingreso', 'bi-cash-stack', '#36A2EB'),
('Venta', 'ingreso', 'bi-bag-check', '#FF6384'),
('Inversión', 'ingreso', 'bi-graph-up', '#FFCE56'),
('Otro', 'ingreso', 'bi-plus-circle', '#4BC0C0'),
('Alimentación', 'gasto', 'bi-egg-fried', '#9966FF'),
('Transporte', 'gasto', 'bi-truck', '#FF9F40'),
('Servicios', 'gasto', 'bi-lightning-charge', '#B2FF66'),
('Entretenimiento', 'gasto', 'bi-controller', '#FF66B2'),
('Salud', 'gasto', 'bi-heart-pulse', '#66FFB2'),
('Otro', 'gasto', 'bi-dash-circle', '#B266FF');