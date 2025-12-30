use horas_produccion_db; 



-- =====================================================
-- TABLA: REGISTROS DE HORAS DE PRODUCCIÓN
-- Almacena los registros detallados de horas de producción
-- Incluye diferentes tipos de horas: regulares, extras diurnas, nocturnas, etc.
-- No requiere validación - se registra directamente
-- =====================================================
CREATE TABLE IF NOT EXISTS registros_horas_produccion (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    orden_produccion_id INT NOT NULL,
    fecha DATE NOT NULL,
    descripcion TEXT COMMENT 'Descripción de las actividades realizadas ese día',
    maquina VARCHAR(20) COMMENT 'Código de la máquina utilizada',
    hr DECIMAL(4,2) DEFAULT 0 COMMENT 'Horas regulares',
    hed DECIMAL(4,2) DEFAULT 0 COMMENT 'Horas extras diurnas',
    hen DECIMAL(4,2) DEFAULT 0 COMMENT 'Horas extras nocturnas',
    hefd DECIMAL(4,2) DEFAULT 0 COMMENT 'Horas extras festivas diurnas',
    hefn DECIMAL(4,2) DEFAULT 0 COMMENT 'Horas extras festivas nocturnas',
    permiso VARCHAR(50) COMMENT 'Tipo de permiso si aplica',
    comida TINYINT(1) DEFAULT 0 COMMENT 'Indicador de comida (1=sí, 0=no)',
    total_horas DECIMAL(4,2) DEFAULT 0 COMMENT 'Total de horas calculadas',
    observaciones TEXT,
    horario VARCHAR(50) DEFAULT '7 am - 5 pm' COMMENT 'Horario de trabajo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (orden_produccion_id) REFERENCES ordenes_produccion(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id),
    INDEX idx_fecha (fecha),
    INDEX idx_orden (orden_produccion_id),
    INDEX idx_usuario_fecha (usuario_id, fecha),
    INDEX idx_maquina (maquina)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




-- Trigger: Actualizar resumen diario después de insertar registro de producción
DELIMITER $$
CREATE TRIGGER after_registro_horas_produccion_insert 
AFTER INSERT ON registros_horas_produccion
FOR EACH ROW
BEGIN
    INSERT INTO resumen_diario_horas (usuario_id, fecha, horas_normales, numero_registros)
    VALUES (NEW.usuario_id, NEW.fecha, NEW.total_horas, 1)
    ON DUPLICATE KEY UPDATE 
        horas_normales = horas_normales + NEW.total_horas,
        numero_registros = numero_registros + 1,
        total_horas = horas_normales + horas_extras;
END;

-- Trigger: Registrar historial al crear registro de horas de producción
DELIMITER $$
CREATE TRIGGER after_registro_horas_produccion_insert_historial
AFTER INSERT ON registros_horas_produccion
FOR EACH ROW
BEGIN
    INSERT INTO historial_cambios (tipo_registro, registro_id, usuario_id, accion, datos_nuevos)
    VALUES (
        'registro_produccion', 
        NEW.id, 
        NEW.usuario_id, 
        'creacion',
        JSON_OBJECT(
            'orden_produccion_id', NEW.orden_produccion_id,
            'fecha', NEW.fecha,
            'descripcion', NEW.descripcion,
            'maquina', NEW.maquina,
            'hr', NEW.hr,
            'hed', NEW.hed,
            'hen', NEW.hen,
            'hefd', NEW.hefd,
            'hefn', NEW.hefn,
            'total_horas', NEW.total_horas,
            'observaciones', NEW.observaciones
        )
    );
END;