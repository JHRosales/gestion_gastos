-- =====================================================
-- SCRIPT: Crear Tabla de Transferencias
-- Descripción: Crea la tabla para gestionar transferencias entre cuentas
-- Fecha: [Fecha actual]
-- =====================================================

USE smart_wallet;

-- =====================================================
-- CREAR TABLA TRANSFERENCIAS
-- =====================================================

CREATE TABLE IF NOT EXISTS transferencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    cuenta_origen_id INT NOT NULL,
    cuenta_destino_id INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    descripcion TEXT,
    fecha DATE NOT NULL,
    gasto_id INT NULL,
    ingreso_id INT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (cuenta_origen_id) REFERENCES cuentas(id) ON DELETE CASCADE,
    FOREIGN KEY (cuenta_destino_id) REFERENCES cuentas(id) ON DELETE CASCADE,
    FOREIGN KEY (gasto_id) REFERENCES gastos(id) ON DELETE CASCADE,
    FOREIGN KEY (ingreso_id) REFERENCES ingresos(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id),
    INDEX idx_cuenta_origen (cuenta_origen_id),
    INDEX idx_cuenta_destino (cuenta_destino_id),
    INDEX idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- MODIFICAR TABLA INGRESOS
-- =====================================================

-- Agregar campo es_transferencia a la tabla ingresos
ALTER TABLE ingresos 
ADD COLUMN es_transferencia TINYINT(1) DEFAULT 0 AFTER cuenta_id;

-- Agregar campo transferencia_id a la tabla ingresos
ALTER TABLE ingresos 
ADD COLUMN transferencia_id INT NULL AFTER es_transferencia;

-- Agregar índice para mejorar rendimiento
ALTER TABLE ingresos 
ADD INDEX idx_transferencia (transferencia_id);

-- Agregar foreign key (después de crear la tabla transferencias)
ALTER TABLE ingresos 
ADD CONSTRAINT fk_ingresos_transferencia 
FOREIGN KEY (transferencia_id) REFERENCES transferencias(id) ON DELETE CASCADE;

-- =====================================================
-- MODIFICAR TABLA GASTOS
-- =====================================================

-- Agregar campo es_transferencia a la tabla gastos
ALTER TABLE gastos 
ADD COLUMN es_transferencia TINYINT(1) DEFAULT 0 AFTER cuenta_id;

-- Agregar campo transferencia_id a la tabla gastos
ALTER TABLE gastos 
ADD COLUMN transferencia_id INT NULL AFTER es_transferencia;

-- Agregar índice para mejorar rendimiento
ALTER TABLE gastos 
ADD INDEX idx_transferencia (transferencia_id);

-- Agregar foreign key (después de crear la tabla transferencias)
ALTER TABLE gastos 
ADD CONSTRAINT fk_gastos_transferencia 
FOREIGN KEY (transferencia_id) REFERENCES transferencias(id) ON DELETE CASCADE;

-- =====================================================
-- CREAR CATEGORÍA "Transferencia" SI NO EXISTE
-- =====================================================

-- Insertar categoría "Transferencia" para ingresos (si no existe)
INSERT INTO categorias (nombre, tipo, icono, color)
SELECT 'Transferencia', 'ingreso', 'bi-arrow-left-right', '#17a2b8'
WHERE NOT EXISTS (
    SELECT 1 FROM categorias 
    WHERE nombre = 'Transferencia' AND tipo = 'ingreso'
);

-- Insertar categoría "Transferencia" para gastos (si no existe)
INSERT INTO categorias (nombre, tipo, icono, color)
SELECT 'Transferencia', 'gasto', 'bi-arrow-left-right', '#17a2b8'
WHERE NOT EXISTS (
    SELECT 1 FROM categorias 
    WHERE nombre = 'Transferencia' AND tipo = 'gasto'
);

-- =====================================================
-- VERIFICACIÓN
-- =====================================================

-- Verificar estructura de tablas
-- DESCRIBE transferencias;
-- DESCRIBE ingresos;
-- DESCRIBE gastos;

-- Verificar categorías creadas
-- SELECT * FROM categorias WHERE nombre = 'Transferencia';
