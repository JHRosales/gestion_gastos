-- =====================================================
-- SCRIPT: Modificar Tablas Ingresos y Gastos
-- Descripción: Agrega campo cuenta_id a ingresos y gastos
-- Fecha: 27/10/2025
-- =====================================================

USE smart_wallet;

-- Agregar campo cuenta_id a la tabla ingresos
ALTER TABLE ingresos 
ADD COLUMN cuenta_id INT NULL AFTER usuario_id;

-- Agregar campo cuenta_id a la tabla gastos
ALTER TABLE gastos 
ADD COLUMN cuenta_id INT NULL AFTER usuario_id;

-- Agregar índices para mejorar rendimiento
ALTER TABLE ingresos 
ADD INDEX idx_cuenta (cuenta_id);

ALTER TABLE gastos 
ADD INDEX idx_cuenta (cuenta_id);

-- Agregar foreign keys (después de poblar los datos)
-- IMPORTANTE: Ejecutar esto DESPUÉS de asignar cuentas a registros existentes
/*
ALTER TABLE ingresos 
ADD CONSTRAINT fk_ingresos_cuenta 
FOREIGN KEY (cuenta_id) REFERENCES cuentas(id) ON DELETE SET NULL;

ALTER TABLE gastos 
ADD CONSTRAINT fk_gastos_cuenta 
FOREIGN KEY (cuenta_id) REFERENCES cuentas(id) ON DELETE SET NULL;
*/

-- =====================================================
-- MIGRACIÓN DE DATOS EXISTENTES
-- =====================================================

-- Crear cuenta por defecto para usuarios existentes
INSERT INTO cuentas (usuario_id, nombre, tipo, saldo_inicial, moneda, activa)
SELECT 
    id,
    'Principal',
    'General',
    0.00,
    'PEN',
    1
FROM usuarios
WHERE id NOT IN (SELECT usuario_id FROM cuentas WHERE nombre = 'Principal');

-- Asignar cuenta principal a ingresos existentes sin cuenta_id
UPDATE ingresos i
INNER JOIN cuentas c ON c.usuario_id = i.usuario_id AND c.nombre = 'Principal'
SET i.cuenta_id = c.id
WHERE i.cuenta_id IS NULL;

-- Asignar cuenta principal a gastos existentes sin cuenta_id
UPDATE gastos g
INNER JOIN cuentas c ON c.usuario_id = g.usuario_id AND c.nombre = 'Principal'
SET g.cuenta_id = c.id
WHERE g.cuenta_id IS NULL;

-- Ahora sí, agregar las foreign keys
ALTER TABLE ingresos 
ADD CONSTRAINT fk_ingresos_cuenta 
FOREIGN KEY (cuenta_id) REFERENCES cuentas(id) ON DELETE SET NULL;

ALTER TABLE gastos 
ADD CONSTRAINT fk_gastos_cuenta 
FOREIGN KEY (cuenta_id) REFERENCES cuentas(id) ON DELETE SET NULL;

-- =====================================================
-- VERIFICACIÓN
-- =====================================================

-- Verificar que todos los ingresos tienen cuenta_id
-- SELECT COUNT(*) as ingresos_sin_cuenta FROM ingresos WHERE cuenta_id IS NULL;

-- Verificar que todos los gastos tienen cuenta_id
-- SELECT COUNT(*) as gastos_sin_cuenta FROM gastos WHERE cuenta_id IS NULL;

-- Ver estructura de tablas
-- DESCRIBE ingresos;
-- DESCRIBE gastos;
-- DESCRIBE cuentas;

