-- =====================================================
-- MIGRACIÓN: Agregar sistema de costos por empleado
-- Fecha: 12 de diciembre de 2025
-- =====================================================

USE horas_produccion_db;

-- Agregar columna de valor_hora_base a la tabla usuarios
ALTER TABLE usuarios 
ADD COLUMN IF NOT EXISTS valor_hora_base DECIMAL(10,2) DEFAULT 0 
COMMENT 'Valor base por hora del empleado' 
AFTER departamento_id;

-- Insertar configuraciones de costos
INSERT INTO configuracion_sistema (clave, valor, tipo, descripcion, categoria) VALUES
('hora_diurna_inicio', '06:00', 'texto', 'Hora de inicio del turno diurno (formato HH:MM)', 'costos'),
('hora_diurna_fin', '18:00', 'texto', 'Hora de fin del turno diurno (formato HH:MM)', 'costos'),
('porcentaje_extra_diurna', '25', 'decimal', 'Porcentaje adicional para horas extras diurnas (%)', 'costos'),
('porcentaje_extra_nocturna', '75', 'decimal', 'Porcentaje adicional para horas extras nocturnas (%)', 'costos'),
('mostrar_costos', '1', 'booleano', 'Mostrar información de costos en reportes', 'costos')
ON DUPLICATE KEY UPDATE valor=VALUES(valor);

-- Verificar cambios
SELECT 'Columna valor_hora_base agregada a usuarios' as mensaje;
SELECT * FROM configuracion_sistema WHERE categoria = 'costos';
