USE horas_produccion_db;

-- =====================================================
-- INSERTAR DATOS DE EJEMPLO
-- =====================================================

-- 1. USUARIOS
-- Insertar usuario administrador por defecto
-- Nota: departamento_id se asignará después de insertar departamentos
INSERT INTO usuarios (nombre_completo, username, email, password, rol, is_active, fecha_ingreso, departamento_id) 
VALUES ('Inyira Arciniegas', 'inyira', 'recursoshumanos@talleresunidosltda.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador', 1, '2025-11-29', 
    (SELECT id FROM departamentos WHERE codigo = 'ADMIN' LIMIT 1))
ON DUPLICATE KEY UPDATE username = username;
-- Contraseña por defecto: password
INSERT INTO usuarios (nombre_completo, username, email, password, rol, is_active, fecha_ingreso, departamento_id) 
VALUES ('Valeria Jaraba', 'valeria', 'ingenieria@talleresunidosltda.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador', 1, '2025-11-29', 
    (SELECT id FROM departamentos WHERE codigo = 'ADMIN' LIMIT 1))
ON DUPLICATE KEY UPDATE username = username;
-- Contraseña por defecto: password


-- 2. DEPARTAMENTOS
-- Insertar departamentos de la empresa
INSERT INTO departamentos (nombre, descripcion, codigo, is_active) VALUES
('Ingenieria', 'Departamento encargado de procesos de  calculos', 'ING', 1),
('Calidad', 'Departamento encargado de procesos de buen funcionamiento', 'CAL', 1),
('Cotizacion', 'Departamento encargado de procesos de  cotización y ventas', 'COT', 1),
('Licitaciones', 'Departamento encargado de procesos de  licitar proyectos', 'LIC', 1)
ON DUPLICATE KEY UPDATE nombre = nombre;

-- 3. HORARIOS LABORALES
-- Insertar horarios laborales según especificaciones
INSERT INTO horarios_laborales (dia_semana, hora_inicio_manana, hora_fin_manana, hora_inicio_tarde, hora_fin_tarde, horas_totales, es_laborable, descripcion) VALUES
('lunes', '07:00:00', '11:00:00', '13:00:00', '16:00:00', 7.00, 1, 'Lunes: 7am-11am y 1pm-4pm'),
('martes', '07:00:00', '11:00:00', '13:00:00', '17:00:00', 8.00, 1, 'Martes: 7am-11am y 1pm-5pm'),
('miercoles', '07:00:00', '11:00:00', '13:00:00', '17:00:00', 8.00, 1, 'Miércoles: 7am-11am y 1pm-5pm'),
('jueves', '07:00:00', '11:00:00', '13:00:00', '17:00:00', 8.00, 1, 'Jueves: 7am-11am y 1pm-5pm'),
('viernes', '07:00:00', '11:00:00', '13:00:00', '17:00:00', 8.00, 1, 'Viernes: 7am-11am y 1pm-5pm'),
('sabado', '07:00:00', '12:00:00', NULL, NULL, 5.00, 1, 'Sábado: 7am-12pm'),
('domingo', '00:00:00', '00:00:00', NULL, NULL, 0.00, 0, 'Domingo: No laborable')
ON DUPLICATE KEY UPDATE dia_semana = dia_semana;

-- 4. CONFIGURACIÓN DEL SISTEMA
-- Insertar configuraciones del sistema (sistema simplificado de horas)
INSERT INTO configuracion_sistema (clave, valor, tipo, descripcion, categoria) VALUES
('sistema_nombre', 'Sistema de Registro de Horas de Producción', 'texto', 'Nombre del sistema', 'general'),
('horas_extras_requieren_aprobacion', '1', 'booleano', 'Las horas extras requieren aprobación de administrador', 'horas'),
('horas_maximas_por_dia', '8.5', 'decimal', 'Número máximo de horas que se pueden registrar en un día normal (valor configurable)', 'horarios'),
('horas_minimas_por_registro', '0.5', 'decimal', 'Número mínimo de horas que se pueden registrar en un solo registro', 'horarios'),
('horas_maximas_extras', '4.0', 'decimal', 'Número máximo de horas extras que se pueden solicitar por día', 'horarios'),
('incremento_horas', '0.5', 'decimal', 'Incremento permitido para registro de horas (múltiplos de este valor: 0.5, 1.0, 1.5, etc.)', 'horarios'),
('dias_edicion_permitidos', '7', 'numero', 'Días permitidos para editar un registro', 'horas'),
('notificaciones_email_activas', '1', 'booleano', 'Activar notificaciones por email', 'notificaciones'),
('zona_horaria', 'America/Bogota', 'texto', 'Zona horaria del sistema', 'general')
ON DUPLICATE KEY UPDATE clave = clave;

-- 5. ÓRDENES DE PRODUCCIÓN
-- Insertar órdenes de producción de intercambiadores de calor para Ecopetrol
INSERT INTO ordenes_produccion (codigo_op, nombre_producto, descripcion, cliente, cantidad_objetivo, unidad_medida, fecha_inicio, fecha_fin_estimada, estado, prioridad) VALUES
('50003', 'HAZ DE TUBOS E-150C', 'TAPA CABEZAL FLOTANTE CON BACKING RING', 'ECOPETROL GRB.', 1, 'unidades', '2025-06-05', '2025-12-31', 'en_proceso', 'alta'),
('50007', 'HAZ DE TUBOS E-12', 'CANAL INTEGRADO MATERIALES HF', 'ECOPETROL CARTAGENA.', 1, 'unidades', '2025-08-14', '2025-12-31', 'en_proceso', 'alta'),
('50008', 'AERO ENFRIADOR E-156', 'FABRICACION DE AERO ENFRIADOR E-156', 'ECOPETROL GRB.', 1, 'unidades', '2025-08-15', '2025-12-31', 'activa', 'media'),
('50009', 'REACTOR ACIDO NITRICO', 'FABRICACION DE REACTOR ACIDO NITRICO ME01-01-51', 'JK HOLDING.', 1, 'unidades', '2024-12-19', '2025-12-31', 'activa', 'media'),
('50010', 'AERO ENFRIADOR E-157', 'FABRICACION DE AER ENFRIADOR E-157', 'ECOPETROL GRB.', 1, 'unidades', '2025-10-10', '2025-12-31', 'activa', 'media'),
('50011', 'AERO ENFRIADOR E-158', 'FABRICACION DE AERO ENFRIADOR E-158', 'ECOPETROL GRB.', 1, 'unidades', '2025-10-10', '2025-12-31', 'activa', 'media')
ON DUPLICATE KEY UPDATE codigo_op = codigo_op;

