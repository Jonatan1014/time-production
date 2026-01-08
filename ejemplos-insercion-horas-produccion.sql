-- =====================================================
-- EJEMPLOS DE INSERCIÓN PARA HORAS DE PRODUCCIÓN
-- =====================================================
-- Este archivo contiene ejemplos de inserción de datos
-- para la tabla registros_horas_produccion
-- =====================================================

USE horas_produccion_db;

-- =====================================================
-- EJEMPLO 1: Registro básico de 8 horas regulares
-- =====================================================
INSERT INTO registros_horas_produccion (
    usuario_id, orden_produccion_id, fecha, descripcion, maquina,
    hr, hed, hen, hefd, hefn,
    permiso, comida, total_horas, observaciones, horario
) VALUES (
    3, -- ID del trabajador
    1, -- ID de la orden de producción
    '2025-12-29', -- Fecha del trabajo
    'Ensamblaje de componentes electrónicos y soldadura de circuitos en línea de producción principal', -- Descripción de actividades
    'MAQ-001', -- Código de la máquina
    8.0, -- Horas regulares
    0, -- Horas extras diurnas
    0, -- Horas extras nocturnas
    0, -- Horas extras festivas diurnas
    0, -- Horas extras festivas nocturnas
    '', -- Permiso (vacío = sin permiso)
    1, -- Comida (1 = tuvo comida, 0 = no)
    8.0, -- Total de horas
    'Trabajo en línea de producción principal', -- Observaciones
    '7 am - 5 pm' -- Horario
);

