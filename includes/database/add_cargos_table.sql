-- =====================================================
-- MIGRACIÓN: Agregar tabla de cargos
-- Fecha: 18 de diciembre de 2025
-- Descripción: Crea tabla de cargos y asocia a usuarios
-- =====================================================

USE horas_produccion_db;

-- Verificar cambios
SELECT 'Tabla cargos creada y columna cargo_id agregada a usuarios' as mensaje;
SELECT * FROM cargos ORDER BY nombre;