-- =====================================================
-- MIGRACIÓN: Agregar sistema de costos por empleado
-- Fecha: 12 de diciembre de 2025
-- =====================================================

USE horas_produccion_db;


-- Verificar cambios
SELECT 'Columna valor_hora_base agregada a usuarios' as mensaje;
SELECT * FROM configuracion_sistema WHERE categoria = 'costos';
