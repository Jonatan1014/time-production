
-- Crear base de datos
CREATE DATABASE IF NOT EXISTS horas_produccion_db;
USE horas_produccion_db;

-- =====================================================
-- TABLA: DEPARTAMENTOS
-- Almacena los departamentos de la empresa
-- =====================================================
CREATE TABLE IF NOT EXISTS departamentos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    codigo VARCHAR(20) UNIQUE,
    responsable_id INT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre),
    INDEX idx_codigo (codigo),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: USUARIOS
-- Almacena información de los trabajadores y administradores
-- =====================================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre_completo VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('trabajador', 'administrador') DEFAULT 'trabajador',
    is_active TINYINT(1) DEFAULT 1,
    fecha_ingreso DATE NULL,
    departamento_id INT NULL,
    valor_hora_base DECIMAL(10,2) DEFAULT 0 COMMENT 'Valor base por hora del empleado',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (departamento_id) REFERENCES departamentos(id) ON DELETE SET NULL,
    INDEX idx_username (username),
    INDEX idx_rol (rol),
    INDEX idx_active (is_active),
    INDEX idx_departamento (departamento_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Agregar foreign key para responsable del departamento después de crear usuarios
ALTER TABLE departamentos 
ADD CONSTRAINT fk_departamento_responsable 
FOREIGN KEY (responsable_id) REFERENCES usuarios(id) ON DELETE SET NULL;

-- =====================================================
-- TABLA: ORDENES DE PRODUCCIÓN
-- Almacena las órdenes de producción disponibles
-- =====================================================
CREATE TABLE IF NOT EXISTS ordenes_produccion (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo_op VARCHAR(50) NOT NULL UNIQUE,
    nombre_producto VARCHAR(150) NOT NULL,
    descripcion TEXT,
    cliente VARCHAR(100),
    cantidad_objetivo INT DEFAULT 0,
    unidad_medida VARCHAR(20) DEFAULT 'unidades',
    fecha_inicio DATE NOT NULL,
    fecha_fin_estimada DATE,
    fecha_fin_real DATE NULL,
    estado ENUM('activa', 'en_proceso', 'completada', 'cancelada') DEFAULT 'activa',
    prioridad ENUM('baja', 'media', 'alta', 'urgente') DEFAULT 'media',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_codigo (codigo_op),
    INDEX idx_estado (estado),
    INDEX idx_fecha_inicio (fecha_inicio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: HORARIOS LABORALES
-- Define los horarios de trabajo por día de la semana
-- =====================================================
CREATE TABLE IF NOT EXISTS horarios_laborales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    dia_semana ENUM('lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo') NOT NULL UNIQUE,
    hora_inicio_manana TIME NOT NULL,
    hora_fin_manana TIME NOT NULL,
    hora_inicio_tarde TIME NULL,
    hora_fin_tarde TIME NULL,
    horas_totales DECIMAL(4,2) NOT NULL,
    es_laborable TINYINT(1) DEFAULT 1,
    descripcion VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: REGISTROS DE HORAS NORMALES
-- Almacena los registros diarios de horas trabajadas
-- Sistema simplificado: solo se registra el número de horas (0.5 a 8.5)
-- =====================================================
CREATE TABLE IF NOT EXISTS registros_horas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    orden_produccion_id INT NOT NULL,
    fecha DATE NOT NULL,
    horas_trabajadas DECIMAL(4,2) NOT NULL COMMENT 'Número de horas trabajadas (0.5 a 8.5)',
    descripcion_trabajo TEXT NOT NULL,
    observaciones TEXT,
    estado ENUM('registrado', 'validado', 'rechazado') DEFAULT 'registrado',
    validado_por INT NULL,
    fecha_validacion TIMESTAMP NULL,
    comentario_validacion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (orden_produccion_id) REFERENCES ordenes_produccion(id) ON DELETE CASCADE,
    FOREIGN KEY (validado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_usuario (usuario_id),
    INDEX idx_fecha (fecha),
    INDEX idx_orden (orden_produccion_id),
    INDEX idx_estado (estado),
    INDEX idx_usuario_fecha (usuario_id, fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE registros_horas 
ADD COLUMN editado TINYINT(1) DEFAULT 0 
COMMENT 'Indica si el registro ya fue editado (0=no, 1=sí)' 
AFTER estado;

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

-- =====================================================
-- TABLA: SOLICITUDES DE HORAS EXTRAS
-- Almacena las solicitudes de horas extras que requieren aprobación
-- =====================================================
CREATE TABLE IF NOT EXISTS solicitudes_horas_extras (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    orden_produccion_id INT NOT NULL,
    fecha DATE NOT NULL,
    hora_inicio TIME NOT NULL COMMENT 'Hora de inicio de las horas extras',
    hora_fin TIME NOT NULL COMMENT 'Hora de finalización de las horas extras',
    total_horas_extras DECIMAL(4,2) NOT NULL COMMENT 'Total de horas extras calculadas',
    descripcion_trabajo TEXT NOT NULL,
    estado ENUM('pendiente', 'aprobada', 'rechazada', 'cancelada') DEFAULT 'pendiente',
    aprobado_por INT NULL,
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_respuesta TIMESTAMP NULL,
    comentario_aprobacion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (orden_produccion_id) REFERENCES ordenes_produccion(id) ON DELETE CASCADE,
    FOREIGN KEY (aprobado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_usuario (usuario_id),
    INDEX idx_fecha (fecha),
    INDEX idx_estado (estado),
    INDEX idx_aprobador (aprobado_por)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: RESUMEN DIARIO DE HORAS
-- Vista consolidada de horas por usuario y fecha
-- =====================================================
CREATE TABLE IF NOT EXISTS resumen_diario_horas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    fecha DATE NOT NULL,
    horas_normales DECIMAL(4,2) DEFAULT 0,
    horas_extras DECIMAL(4,2) DEFAULT 0,
    total_horas DECIMAL(4,2) DEFAULT 0,
    numero_registros INT DEFAULT 0,
    ordenes_trabajadas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_usuario_fecha (usuario_id, fecha),
    INDEX idx_fecha (fecha),
    INDEX idx_usuario_fecha (usuario_id, fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: HISTORIAL DE CAMBIOS
-- Auditoría de cambios en registros de horas
-- =====================================================
CREATE TABLE IF NOT EXISTS historial_cambios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tipo_registro ENUM('registro_normal', 'hora_extra', 'orden_produccion', 'registro_produccion') NOT NULL,
    registro_id INT NOT NULL,
    usuario_id INT NOT NULL,
    accion ENUM('creacion', 'modificacion', 'eliminacion', 'validacion', 'aprobacion', 'rechazo') NOT NULL,
    datos_anteriores JSON,
    datos_nuevos JSON,
    motivo TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_tipo_registro (tipo_registro, registro_id),
    INDEX idx_fecha (created_at),
    INDEX idx_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: CACHE DE DÍAS FESTIVOS
-- Almacena los días festivos obtenidos desde la API
-- =====================================================
CREATE TABLE IF NOT EXISTS festivos_cache (
    id INT PRIMARY KEY AUTO_INCREMENT,
    fecha DATE NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    tipo ENUM('festivo', 'puente') DEFAULT 'festivo',
    pais VARCHAR(5) NOT NULL,
    anio INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_festivo (pais, anio, fecha),
    INDEX idx_pais_anio (pais, anio),
    INDEX idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


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


-- =====================================================
-- TABLA: CONFIGURACIÓN DEL SISTEMA
-- Parámetros configurables del sistema
-- =====================================================
CREATE TABLE IF NOT EXISTS configuracion_sistema (
    id INT PRIMARY KEY AUTO_INCREMENT,
    clave VARCHAR(100) NOT NULL UNIQUE,
    valor TEXT NOT NULL,
    tipo ENUM('texto', 'numero', 'decimal', 'booleano', 'json') DEFAULT 'texto',
    descripcion TEXT,
    categoria VARCHAR(50) DEFAULT 'general',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar configuración para URL de ProjectDashboard (si existe tabla de configuración)
INSERT INTO configuracion_sistema (clave, valor, tipo, categoria, descripcion) 
VALUES 
    ('projectdashboard_url', '', 'texto', 'integraciones', 'URL webhook del sistema ProjectDashboard'),
    ('projectdashboard_habilitado', '0', 'booleano', 'integraciones', 'Habilitar sincronización con ProjectDashboard'),
    ('projectdashboard_webhook_token', '', 'texto', 'integraciones', 'Token de autenticación para webhook'),
    ('projectdashboard_sincronizacion_automatica', '0', 'booleano', 'integraciones', 'Enviar automáticamente via webhook al aprobar')
ON DUPLICATE KEY UPDATE 
    descripcion = VALUES(descripcion);

-- =====================================================
-- DATOS INICIALES: CONFIGURACIONES DE COSTOS
-- Basado en legislación laboral colombiana
-- =====================================================
INSERT INTO configuracion_sistema (clave, valor, tipo, descripcion, categoria) VALUES
-- Horarios de turnos
('hora_diurna_inicio', '06:00', 'texto', 'Hora de inicio del turno diurno (formato HH:MM)', 'costos'),
('hora_diurna_fin', '21:00', 'texto', 'Hora de fin del turno diurno (formato HH:MM)', 'costos'),

-- Horas extras ordinarias (lunes a sábado)
('factor_extra_diurna', '1.25', 'decimal', 'Hora extra diurna ordinaria (1.25x)', 'costos'),
('factor_extra_nocturna', '1.75', 'decimal', 'Hora extra nocturna ordinaria (1.75x)', 'costos'),

-- Recargos nocturnos
('recargo_nocturno_ordinario', '0.35', 'decimal', 'Recargo por hora nocturna ordinaria (0.35x adicional)', 'costos'),
('recargo_nocturno_dominical', '2.1', 'decimal', 'Recargo nocturno dominical o festivo (2.1x)', 'costos'),

-- Horas extras dominicales y festivas
('factor_dominical_diurno', '2.0', 'decimal', 'Hora extra diurna dominical o festiva (2.0x)', 'costos'),
('factor_dominical_nocturno', '2.5', 'decimal', 'Hora extra nocturna dominical o festiva (2.5x)', 'costos'),

-- Trabajo dominical y festivo
('factor_dominical', '1.75', 'decimal', 'Factor día/hora dominical o festivo (1.75x)', 'costos'),

-- Configuración general de costos
('mostrar_costos', '1', 'booleano', 'Mostrar información de costos en reportes', 'costos'),

-- Configuración de festivos
('festivos_pais', 'CO', 'texto', 'Código ISO del país para consultar días festivos', 'integraciones'),
('festivos_api_url', 'https://date.nager.at/api/v3/PublicHolidays', 'texto', 'URL base de la API de días festivos', 'integraciones'),
('festivos_consulta_automatica', '1', 'booleano', 'Consultar automáticamente días festivos para calcular recargos', 'integraciones')
ON DUPLICATE KEY UPDATE valor=VALUES(valor), descripcion=VALUES(descripcion);



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


-- Agregar columna de valor_hora_base a la tabla usuarios
ALTER TABLE usuarios 
ADD COLUMN IF NOT EXISTS valor_hora_base DECIMAL(10,2) DEFAULT 0 
COMMENT 'Valor base por hora del empleado' 
AFTER departamento_id;



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

ALTER TABLE sincronizacion_projectdashboard MODIFY COLUMN tipo_registro ENUM('horas_normales', 'horas_extras', 'horas_produccion') NOT NULL;

-- =====================================================
-- TABLA: CACHE DE DÍAS FESTIVOS
-- Almacena los días festivos obtenidos desde la API
-- =====================================================
CREATE TABLE IF NOT EXISTS festivos_cache (
    id INT PRIMARY KEY AUTO_INCREMENT,
    fecha DATE NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    tipo ENUM('festivo', 'puente') DEFAULT 'festivo',
    pais VARCHAR(5) NOT NULL,
    anio INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_festivo (pais, anio, fecha),
    INDEX idx_pais_anio (pais, anio),
    INDEX idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =====================================================
-- VISTAS ÚTILES
-- =====================================================

-- Vista: Resumen de horas por usuario y mes
CREATE OR REPLACE VIEW vista_resumen_mensual AS
SELECT 
    u.id as usuario_id,
    u.nombre_completo,
    d.nombre as departamento,
    YEAR(r.fecha) as anio,
    MONTH(r.fecha) as mes,
    COUNT(DISTINCT r.fecha) as dias_trabajados,
    SUM(r.horas_trabajadas) as total_horas_normales,
    COALESCE(SUM(he.total_horas_extras), 0) as total_horas_extras,
    SUM(r.horas_trabajadas) + COALESCE(SUM(he.total_horas_extras), 0) as total_horas
FROM usuarios u
LEFT JOIN departamentos d ON u.departamento_id = d.id
LEFT JOIN registros_horas r ON u.id = r.usuario_id
LEFT JOIN solicitudes_horas_extras he ON u.id = he.usuario_id 
    AND he.estado = 'aprobada' 
    AND YEAR(he.fecha) = YEAR(r.fecha) 
    AND MONTH(he.fecha) = MONTH(r.fecha)
WHERE u.rol = 'trabajador'
GROUP BY u.id, YEAR(r.fecha), MONTH(r.fecha);

-- Vista: Horas por orden de producción
CREATE OR REPLACE VIEW vista_horas_por_orden AS
SELECT 
    op.id as orden_id,
    op.codigo_op,
    op.nombre_producto,
    op.estado,
    COUNT(DISTINCT r.usuario_id) as trabajadores_asignados,
    SUM(r.horas_trabajadas) as total_horas_invertidas,
    COUNT(r.id) as numero_registros
FROM ordenes_produccion op
LEFT JOIN registros_horas r ON op.id = r.orden_produccion_id
GROUP BY op.id;

-- Vista: Solicitudes de horas extras pendientes
CREATE OR REPLACE VIEW vista_horas_extras_pendientes AS
SELECT 
    he.id,
    he.fecha,
    u.nombre_completo as trabajador,
    d.nombre as departamento,
    op.codigo_op,
    op.nombre_producto,
    he.hora_inicio,
    he.hora_fin,
    he.total_horas_extras,
    he.descripcion_trabajo,
    he.fecha_solicitud,
    DATEDIFF(NOW(), he.fecha_solicitud) as dias_pendiente
FROM solicitudes_horas_extras he
INNER JOIN usuarios u ON he.usuario_id = u.id
LEFT JOIN departamentos d ON u.departamento_id = d.id
INNER JOIN ordenes_produccion op ON he.orden_produccion_id = op.id
WHERE he.estado = 'pendiente'
ORDER BY he.fecha_solicitud ASC;

-- =====================================================
-- TRIGGERS
-- =====================================================

-- Trigger: Actualizar resumen diario después de insertar registro
DELIMITER $$
CREATE TRIGGER after_registro_horas_insert 
AFTER INSERT ON registros_horas
FOR EACH ROW
BEGIN
    INSERT INTO resumen_diario_horas (usuario_id, fecha, horas_normales, numero_registros)
    VALUES (NEW.usuario_id, NEW.fecha, NEW.horas_trabajadas, 1)
    ON DUPLICATE KEY UPDATE 
        horas_normales = horas_normales + NEW.horas_trabajadas,
        numero_registros = numero_registros + 1,
        total_horas = horas_normales + horas_extras;
END;

-- Trigger: Actualizar resumen diario después de aprobar horas extras
DELIMITER $$
CREATE TRIGGER after_horas_extras_aprobadas
AFTER UPDATE ON solicitudes_horas_extras
FOR EACH ROW
BEGIN
    IF NEW.estado = 'aprobada' AND OLD.estado != 'aprobada' THEN
        INSERT INTO resumen_diario_horas (usuario_id, fecha, horas_extras)
        VALUES (NEW.usuario_id, NEW.fecha, NEW.total_horas_extras)
        ON DUPLICATE KEY UPDATE 
            horas_extras = horas_extras + NEW.total_horas_extras,
            total_horas = horas_normales + horas_extras;
    END IF;
END;

-- Trigger: Registrar historial al crear registro de horas
DELIMITER $$
CREATE TRIGGER after_registro_horas_insert_historial
AFTER INSERT ON registros_horas
FOR EACH ROW
BEGIN
    INSERT INTO historial_cambios (tipo_registro, registro_id, usuario_id, accion, datos_nuevos)
    VALUES (
        'registro_normal', 
        NEW.id, 
        NEW.usuario_id, 
        'creacion',
        JSON_OBJECT(
            'orden_produccion_id', NEW.orden_produccion_id,
            'fecha', NEW.fecha,
            'horas_trabajadas', NEW.horas_trabajadas,
            'descripcion_trabajo', NEW.descripcion_trabajo
        )
    );
END;

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

DELIMITER ;

-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS
-- =====================================================

-- Procedimiento: Obtener resumen semanal de un usuario
DELIMITER $$
CREATE PROCEDURE sp_resumen_semanal_usuario(
    IN p_usuario_id INT,
    IN p_fecha_inicio DATE,
    IN p_fecha_fin DATE
)
BEGIN
    SELECT 
        DATE(r.fecha) as fecha,
        DAYNAME(r.fecha) as dia_semana,
        SUM(r.horas_trabajadas) as horas_normales,
        COALESCE(SUM(he.total_horas_extras), 0) as horas_extras,
        SUM(r.horas_trabajadas) + COALESCE(SUM(he.total_horas_extras), 0) as total_horas,
        COUNT(DISTINCT r.orden_produccion_id) as ordenes_trabajadas
    FROM registros_horas r
    LEFT JOIN solicitudes_horas_extras he ON r.usuario_id = he.usuario_id 
        AND r.fecha = he.fecha 
        AND he.estado = 'aprobada'
    WHERE r.usuario_id = p_usuario_id
        AND r.fecha BETWEEN p_fecha_inicio AND p_fecha_fin
    GROUP BY DATE(r.fecha)
    ORDER BY r.fecha;
END;

-- Procedimiento: Aprobar solicitud de horas extras
DELIMITER $$
CREATE PROCEDURE sp_aprobar_horas_extras(
    IN p_solicitud_id INT,
    IN p_aprobador_id INT,
    IN p_comentario TEXT
)
BEGIN
    UPDATE solicitudes_horas_extras
    SET 
        estado = 'aprobada',
        aprobado_por = p_aprobador_id,
        fecha_respuesta = NOW(),
        comentario_aprobacion = p_comentario
    WHERE id = p_solicitud_id;
    
    SELECT 'Solicitud aprobada exitosamente' as mensaje;
END;

-- Procedimiento: Rechazar solicitud de horas extras
DELIMITER $$
CREATE PROCEDURE sp_rechazar_horas_extras(
    IN p_solicitud_id INT,
    IN p_aprobador_id INT,
    IN p_comentario TEXT
)
BEGIN
    UPDATE solicitudes_horas_extras
    SET 
        estado = 'rechazada',
        aprobado_por = p_aprobador_id,
        fecha_respuesta = NOW(),
        comentario_aprobacion = p_comentario
    WHERE id = p_solicitud_id;
    
    SELECT 'Solicitud rechazada' as mensaje;
END;

DELIMITER ;

-- =====================================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- =====================================================

-- Índices compuestos para consultas frecuentes
CREATE INDEX idx_registro_usuario_fecha_estado ON registros_horas(usuario_id, fecha, estado);
CREATE INDEX idx_solicitud_estado_fecha ON solicitudes_horas_extras(estado, fecha);
CREATE INDEX idx_orden_estado_fecha ON ordenes_produccion(estado, fecha_inicio);

-- =====================================================
-- FIN DE LA ESTRUCTURA DE BASE DE DATOS
-- =====================================================
