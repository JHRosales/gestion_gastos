-- =====================================================
-- SCRIPT: Crear Tabla de Cuentas
-- Descripción: Crea la tabla para gestionar cuentas de usuario
-- Fecha: 27/10/2025
-- =====================================================

USE smart_wallet;

-- Crear tabla cuentas
CREATE TABLE IF NOT EXISTS cuentas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    saldo_inicial DECIMAL(10,2) DEFAULT 0.00,
    moneda ENUM('PEN', 'USD') DEFAULT 'PEN',
    activa BOOLEAN DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id),
    INDEX idx_activa (activa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar cuentas por defecto para usuarios existentes
-- Nota: Este insert se ejecuta por usuario existente en el sistema
-- Se debe ajustar según los IDs de usuarios reales

-- Para verificar usuarios y crear cuentas por defecto, ejecutar:
/*
INSERT INTO cuentas (usuario_id, nombre, tipo, saldo_inicial, moneda, activa)
SELECT 
    id,
    'Principal',
    'General',
    0.00,
    'PEN',
    1
FROM usuarios
WHERE id NOT IN (SELECT usuario_id FROM cuentas);
*/

-- Verificar creación de tabla
-- SELECT * FROM cuentas LIMIT 1;

