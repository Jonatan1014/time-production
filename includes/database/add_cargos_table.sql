-- =====================================================
-- MIGRACIÓN: Agregar tabla de cargos
-- Fecha: 18 de diciembre de 2025
-- Descripción: Crea tabla de cargos y asocia a usuarios
-- =====================================================

USE horas_produccion_db;

-- Crear tabla de cargos
CREATE TABLE IF NOT EXISTS cargos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    is_active TINYINT(1) DEFAULT 1 COMMENT '0=inactivo, 1=activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_active (is_active),
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Agregar columna cargo_id a la tabla usuarios
ALTER TABLE usuarios
ADD COLUMN cargo_id INT DEFAULT NULL COMMENT 'ID del cargo del usuario'
AFTER departamento_id;

-- Agregar foreign key
ALTER TABLE usuarios
ADD CONSTRAINT fk_usuario_cargo
FOREIGN KEY (cargo_id) REFERENCES cargos(id) ON DELETE SET NULL;

-- Insertar cargos básicos
INSERT INTO cargos (nombre, descripcion) VALUES
('Dibujante', 'Responsable general de la empresa'),
('Comercial', 'Responsable del área de producción'),
('QAQC', 'Supervisor del equipo de producción'),
('Soldador', 'Trabajador operativo en línea de producción'),
('Calidad', 'Responsable de control de calidad'),
('Contadora', 'Personal administrativo'),
('Ingeniero Mecánico', 'Responsable de logística y distribución'),
('Jefe de Produccion', 'Personal administrativo'),
('Mecanico', 'Personal auxiliar')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- Verificar cambios
SELECT 'Tabla cargos creada y columna cargo_id agregada a usuarios' as mensaje;
SELECT * FROM cargos ORDER BY nombre;