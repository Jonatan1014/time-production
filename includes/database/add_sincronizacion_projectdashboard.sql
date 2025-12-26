-- =====================================================
-- MIGRACIÓN: Sistema de Sincronización con ProjectDashboard
-- Fecha: 2025-12-12
-- Descripción: Agrega tabla para trackear registros enviados
-- =====================================================

USE horas_produccion_db;

-- Tabla para registrar sincronizaciones con ProjectDashboard
CREATE TABLE IF NOT EXISTS sincronizacion_projectdashboard (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tipo_registro ENUM('horas_normales', 'horas_extras') NOT NULL,
    registro_id INT NOT NULL COMMENT 'ID del registro en registros_horas o solicitudes_horas_extras',
    usuario_id INT NOT NULL,
    orden_produccion_id INT NOT NULL,
    fecha_registro DATE NOT NULL,
    horas_ordinarias DECIMAL(4,2) DEFAULT 0,
    horas_extras DECIMAL(4,2) DEFAULT 0,
    total_pagado DECIMAL(10,2) DEFAULT 0,
    fecha_sincronizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    sincronizado_por INT NOT NULL,
    respuesta_api TEXT COMMENT 'Respuesta del sistema externo si aplica',
    INDEX idx_tipo_registro (tipo_registro, registro_id),
    INDEX idx_usuario (usuario_id),
    INDEX idx_fecha (fecha_registro),
    INDEX idx_sincronizacion (fecha_sincronizacion),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (orden_produccion_id) REFERENCES ordenes_produccion(id) ON DELETE CASCADE,
    FOREIGN KEY (sincronizado_por) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índice compuesto único para evitar duplicados
ALTER TABLE sincronizacion_projectdashboard 
ADD UNIQUE KEY unique_sincronizacion (tipo_registro, registro_id);

-- Insertar configuración para URL de ProjectDashboard (si existe tabla de configuración)
INSERT INTO configuracion_sistema (clave, valor, tipo, categoria, descripcion) 
VALUES 
    ('projectdashboard_url', '', 'texto', 'integraciones', 'URL webhook del sistema ProjectDashboard'),
    ('projectdashboard_habilitado', '0', 'booleano', 'integraciones', 'Habilitar sincronización con ProjectDashboard'),
    ('projectdashboard_webhook_token', '', 'texto', 'integraciones', 'Token de autenticación para webhook'),
    ('projectdashboard_sincronizacion_automatica', '0', 'booleano', 'integraciones', 'Enviar automáticamente via webhook al aprobar')
ON DUPLICATE KEY UPDATE 
    descripcion = VALUES(descripcion);
