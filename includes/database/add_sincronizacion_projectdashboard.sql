-- =====================================================
-- MIGRACIÓN: Sistema de Sincronización con ProjectDashboard
-- Fecha: 2025-12-12
-- Descripción: Agrega tabla para trackear registros enviados
-- =====================================================

USE horas_produccion_db;

-- Insertar configuración para URL de ProjectDashboard (si existe tabla de configuración)
INSERT INTO configuracion_sistema (clave, valor, tipo, categoria, descripcion) 
VALUES 
    ('projectdashboard_url', '', 'texto', 'integraciones', 'URL webhook del sistema ProjectDashboard'),
    ('projectdashboard_habilitado', '0', 'booleano', 'integraciones', 'Habilitar sincronización con ProjectDashboard'),
    ('projectdashboard_webhook_token', '', 'texto', 'integraciones', 'Token de autenticación para webhook'),
    ('projectdashboard_sincronizacion_automatica', '0', 'booleano', 'integraciones', 'Enviar automáticamente via webhook al aprobar')
ON DUPLICATE KEY UPDATE 
    descripcion = VALUES(descripcion);
