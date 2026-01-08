-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: systemautomatic.xyz:3307
-- Tiempo de generación: 07-01-2026 a las 13:47:55
-- Versión del servidor: 9.5.0
-- Versión de PHP: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `horas_produccion_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargos`
--

CREATE TABLE `cargos` (
  `id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT '1' COMMENT '0=inactivo, 1=activo',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cargos`
--

INSERT INTO `cargos` (`id`, `nombre`, `descripcion`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Dibujante', 'Responsable general de la empresa', 1, '2025-12-18 21:12:53', '2025-12-18 21:12:53'),
(2, 'Comercial', 'Responsable del área de producción', 1, '2025-12-18 21:12:53', '2025-12-18 21:12:53'),
(3, 'QAQC', 'Supervisor del equipo de producción', 1, '2025-12-18 21:12:53', '2025-12-18 21:12:53'),
(4, 'Soldador', 'Trabajador operativo en línea de producción', 1, '2025-12-18 21:12:53', '2025-12-18 21:12:53'),
(5, 'Calidad', 'Responsable de control de calidad', 1, '2025-12-18 21:12:53', '2025-12-18 21:12:53'),
(6, 'Contadora', 'Personal administrativo', 1, '2025-12-18 21:12:53', '2025-12-18 21:12:53'),
(7, 'Ingeniero Mecánico', 'Responsable de logística y distribución', 1, '2025-12-18 21:12:53', '2025-12-18 21:12:53'),
(8, 'Jefe de Produccion', 'Personal administrativo', 1, '2025-12-18 21:12:53', '2025-12-18 21:12:53'),
(9, 'Mecanico', 'Personal auxiliar', 1, '2025-12-18 21:12:53', '2025-12-18 21:12:53'),
(10, 'Mercadeo y Rectificación', NULL, 1, '2025-12-19 15:12:58', '2025-12-19 15:13:55'),
(11, 'Ingeniero de Sistemas y Tecnologia', NULL, 1, '2025-12-19 15:18:12', '2025-12-19 15:18:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_sistema`
--

CREATE TABLE `configuracion_sistema` (
  `id` int NOT NULL,
  `clave` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('texto','numero','decimal','booleano','json') COLLATE utf8mb4_unicode_ci DEFAULT 'texto',
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `categoria` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'general',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuracion_sistema`
--

INSERT INTO `configuracion_sistema` (`id`, `clave`, `valor`, `tipo`, `descripcion`, `categoria`, `created_at`, `updated_at`) VALUES
(1, 'sistema_nombre', 'Sistema de Registro de Horas de Producción', 'texto', 'Nombre del sistema', 'general', '2025-11-29 15:31:39', '2025-11-29 15:31:39'),
(2, 'horas_extras_requieren_aprobacion', '1', 'booleano', 'Las horas extras requieren aprobación de administrador', 'horas', '2025-11-29 15:31:39', '2025-11-29 15:31:39'),
(3, 'horas_maximas_por_dia', '8.5', 'decimal', 'Número máximo de horas que se pueden registrar en un día normal (valor configurable)', 'horarios', '2025-11-29 15:31:39', '2025-11-29 15:31:39'),
(4, 'horas_minimas_por_registro', '0.5', 'decimal', 'Número mínimo de horas que se pueden registrar en un solo registro', 'horarios', '2025-11-29 15:31:39', '2025-11-29 15:31:39'),
(5, 'horas_maximas_extras', '4.0', 'decimal', 'Número máximo de horas extras que se pueden solicitar por día', 'horarios', '2025-11-29 15:31:39', '2025-11-29 15:31:39'),
(6, 'incremento_horas', '0.5', 'decimal', 'Incremento permitido para registro de horas (múltiplos de este valor: 0.5, 1.0, 1.5, etc.)', 'horarios', '2025-11-29 15:31:39', '2025-11-29 15:31:39'),
(7, 'dias_edicion_permitidos', '7', 'numero', 'Días permitidos para editar un registro', 'horas', '2025-11-29 15:31:39', '2025-11-29 15:31:39'),
(8, 'notificaciones_email_activas', '1', 'booleano', 'Activar notificaciones por email', 'notificaciones', '2025-11-29 15:31:39', '2025-11-29 15:31:39'),
(9, 'zona_horaria', 'America/Bogota', 'texto', 'Zona horaria del sistema', 'general', '2025-11-29 15:31:39', '2025-11-29 15:31:39'),
(10, 'hora_diurna_inicio', '06:00', 'texto', 'Hora de inicio del turno diurno (formato HH:MM)', 'costos', '2025-12-13 05:01:12', '2025-12-13 05:01:12'),
(11, 'hora_diurna_fin', '18:00', 'texto', 'Hora de fin del turno diurno (formato HH:MM)', 'costos', '2025-12-13 05:01:12', '2025-12-13 05:01:12'),
(12, 'porcentaje_extra_diurna', '25', 'decimal', 'Porcentaje adicional para horas extras diurnas (%)', 'costos', '2025-12-13 05:01:12', '2025-12-13 05:01:12'),
(13, 'porcentaje_extra_nocturna', '75', 'decimal', 'Porcentaje adicional para horas extras nocturnas (%)', 'costos', '2025-12-13 05:01:12', '2025-12-13 05:01:12'),
(14, 'mostrar_costos', '1', 'booleano', 'Mostrar información de costos en reportes', 'costos', '2025-12-13 05:01:12', '2025-12-13 05:01:12'),
(25, 'projectdashboard_url', 'https://n8n.systemautomatic.xyz/webhook/time-production', 'texto', 'URL webhook del sistema ProjectDashboard', 'integraciones', '2025-12-13 05:01:44', '2025-12-13 05:24:35'),
(26, 'projectdashboard_habilitado', '1', 'booleano', 'Habilitar sincronización con ProjectDashboard', 'integraciones', '2025-12-13 05:01:44', '2025-12-13 05:24:35'),
(27, 'projectdashboard_webhook_token', '', 'texto', 'Token de autenticación para webhook', 'integraciones', '2025-12-13 05:01:44', '2025-12-13 05:01:44'),
(28, 'projectdashboard_sincronizacion_automatica', '0', 'booleano', 'Enviar automáticamente via webhook al aprobar', 'integraciones', '2025-12-13 05:01:44', '2025-12-13 05:01:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamentos`
--

CREATE TABLE `departamentos` (
  `id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `codigo` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `responsable_id` int DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `departamentos`
--

INSERT INTO `departamentos` (`id`, `nombre`, `descripcion`, `codigo`, `responsable_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Ingenieria', 'Departamento encargado de procesos de  calculos', 'ING', NULL, 1, '2025-11-29 15:31:39', '2025-11-29 15:31:39'),
(2, 'Calidad', 'Departamento encargado de procesos de buen funcionamiento', 'CAL', NULL, 1, '2025-11-29 15:31:39', '2025-11-29 15:31:39'),
(3, 'Cotizacion', 'Departamento encargado de procesos de  cotización y ventas', 'COT', NULL, 1, '2025-11-29 15:31:39', '2025-11-29 15:31:39'),
(4, 'Licitaciones', 'Departamento encargado de procesos de  licitar proyectos', 'LIC', NULL, 1, '2025-11-29 15:31:39', '2025-11-29 15:31:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_cambios`
--

CREATE TABLE `historial_cambios` (
  `id` int NOT NULL,
  `tipo_registro` enum('registro_normal','hora_extra','orden_produccion','registro_produccion') COLLATE utf8mb4_unicode_ci NOT NULL,
  `registro_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `accion` enum('creacion','modificacion','eliminacion','validacion','aprobacion','rechazo') COLLATE utf8mb4_unicode_ci NOT NULL,
  `datos_anteriores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `datos_nuevos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `motivo` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios_laborales`
--

CREATE TABLE `horarios_laborales` (
  `id` int NOT NULL,
  `dia_semana` enum('lunes','martes','miercoles','jueves','viernes','sabado','domingo') COLLATE utf8mb4_unicode_ci NOT NULL,
  `hora_inicio_manana` time NOT NULL,
  `hora_fin_manana` time NOT NULL,
  `hora_inicio_tarde` time DEFAULT NULL,
  `hora_fin_tarde` time DEFAULT NULL,
  `horas_totales` decimal(4,2) NOT NULL,
  `es_laborable` tinyint(1) DEFAULT '1',
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `horarios_laborales`
--

INSERT INTO `horarios_laborales` (`id`, `dia_semana`, `hora_inicio_manana`, `hora_fin_manana`, `hora_inicio_tarde`, `hora_fin_tarde`, `horas_totales`, `es_laborable`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'lunes', '07:00:00', '11:00:00', '13:00:00', '16:00:00', 7.00, 1, 'Lunes: 7am-11am y 1pm-4pm', '2025-11-29 15:31:39', '2025-11-29 15:31:39'),
(2, 'martes', '07:00:00', '11:00:00', '13:00:00', '17:00:00', 8.00, 1, 'Martes: 7am-11am y 1pm-5pm', '2025-11-29 15:31:39', '2025-11-29 15:31:39'),
(3, 'miercoles', '07:00:00', '11:00:00', '13:00:00', '17:00:00', 8.00, 1, 'Miércoles: 7am-11am y 1pm-5pm', '2025-11-29 15:31:39', '2025-11-29 15:31:39'),
(4, 'jueves', '07:00:00', '11:00:00', '13:00:00', '17:00:00', 8.00, 1, 'Jueves: 7am-11am y 1pm-5pm', '2025-11-29 15:31:39', '2025-11-29 15:31:39'),
(5, 'viernes', '07:00:00', '11:00:00', '13:00:00', '17:00:00', 8.00, 1, 'Viernes: 7am-11am y 1pm-5pm', '2025-11-29 15:31:39', '2025-11-29 15:31:39'),
(6, 'sabado', '07:00:00', '12:00:00', NULL, NULL, 5.00, 1, 'Sábado: 7am-12pm', '2025-11-29 15:31:39', '2025-11-29 15:31:39'),
(7, 'domingo', '00:00:00', '00:00:00', NULL, NULL, 0.00, 0, 'Domingo: No laborable', '2025-11-29 15:31:39', '2025-11-29 15:31:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenes_produccion`
--

CREATE TABLE `ordenes_produccion` (
  `id` int NOT NULL,
  `codigo_op` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_producto` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `cliente` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cantidad_objetivo` int DEFAULT '0',
  `unidad_medida` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'unidades',
  `fecha_inicio` date NOT NULL,
  `fecha_fin_estimada` date DEFAULT NULL,
  `fecha_fin_real` date DEFAULT NULL,
  `estado` enum('activa','en_proceso','completada','cancelada') COLLATE utf8mb4_unicode_ci DEFAULT 'activa',
  `prioridad` enum('baja','media','alta','urgente') COLLATE utf8mb4_unicode_ci DEFAULT 'media',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ordenes_produccion`
--

INSERT INTO `ordenes_produccion` (`id`, `codigo_op`, `nombre_producto`, `descripcion`, `cliente`, `cantidad_objetivo`, `unidad_medida`, `fecha_inicio`, `fecha_fin_estimada`, `fecha_fin_real`, `estado`, `prioridad`, `created_at`, `updated_at`) VALUES
(1, '50003', 'HAZ DE TUBOS E-150C', 'TAPA CABEZAL FLOTANTE CON BACKING RING', 'ECOPETROL GRB.', 1, 'unidades', '2025-06-05', '2025-12-31', NULL, 'en_proceso', 'alta', '2025-11-29 15:31:39', '2025-11-29 15:31:39'),
(2, '50007', 'HAZ DE TUBOS E-12', 'CANAL INTEGRADO MATERIALES HF', 'ECOPETROL CARTAGENA.', 1, 'unidades', '2025-08-14', '2025-12-31', NULL, 'en_proceso', 'alta', '2025-11-29 15:31:39', '2025-11-29 15:31:39'),
(3, '50008', 'AERO ENFRIADOR E-156', 'FABRICACION DE AERO ENFRIADOR E-156', 'ECOPETROL GRB.', 1, 'unidades', '2025-08-15', '2025-12-31', NULL, 'activa', 'media', '2025-11-29 15:31:39', '2025-11-29 15:31:39'),
(4, '50009', 'REACTOR ACIDO NITRICO', 'FABRICACION DE REACTOR ACIDO NITRICO ME01-01-51', 'JK HOLDING.', 1, 'unidades', '2024-12-19', '2025-12-31', NULL, 'activa', 'media', '2025-11-29 15:31:39', '2025-11-29 15:31:39'),
(5, '50010', 'AERO ENFRIADOR E-157', 'FABRICACION DE AER ENFRIADOR E-157', 'ECOPETROL GRB.', 1, 'unidades', '2025-10-10', '2025-12-31', NULL, 'activa', 'media', '2025-11-29 15:31:39', '2025-11-29 15:31:39'),
(6, '50011', 'AERO ENFRIADOR E-158', 'FABRICACION DE AERO ENFRIADOR E-158', 'ECOPETROL GRB.', 1, 'unidades', '2025-10-10', '2025-12-31', NULL, 'activa', 'media', '2025-11-29 15:31:39', '2025-11-29 15:31:39'),
(7, '6956', 'FABRICACION AERO ENFRIADOR AE-7451 (CHICHIMENE)', '', 'ECOPETROL', 1, 'unidades', '2024-11-19', '2025-12-15', NULL, 'activa', 'alta', '2025-11-29 16:21:06', '2025-11-29 16:21:06'),
(8, '50013', 'REENTUBE PARCIAL HAZ DE TUBOS E-1112', 'REENTUBE PARCIAL DE 141 TUBOS, RETIRO Y APLICACION DE SOLDADURA DE SELLO ', 'CONSORCIO TABARCA', 1, 'unidades', '2025-12-02', '2025-12-06', NULL, 'activa', 'alta', '2025-12-02 14:48:10', '2025-12-02 14:48:10'),
(9, '358', 'COTIZACIONES', 'REALIZAR COTIZACIONES ', 'TALLERES UNIDO LTDA', 1, 'unidades', '2025-12-01', '2025-12-31', NULL, 'activa', 'alta', '2025-12-02 20:00:16', '2025-12-02 20:00:16'),
(10, '357', 'GESTION DOCUMENTAL Y ATENCION A CLIENTES', 'REALIZAR DOCUMENTOS, CONTACTAR CLIENTES ETC', 'TALLERES UNIDO LTDA', 1, 'unidades', '2025-12-01', '2025-12-31', NULL, 'activa', 'media', '2025-12-03 14:36:01', '2025-12-03 14:36:01'),
(11, '50014', 'REENTUBE TOTAL HAZ DE TUBOS E-1217', 'REENTUBE TOTAL HAZ DE TUBOS E-1217 CON SOLDADURA DE SELLO EN 590 TUBOS DE 3/4 BW 14 LONGITUD DE 16 FT', 'ECOPETROL', 1, 'unidades', '2025-12-10', '2025-12-15', NULL, 'activa', 'alta', '2025-12-11 21:05:32', '2025-12-11 21:05:32'),
(12, '7029', 'FABRICACION DE BOTAS DE GAS ', 'FABRICACION DE BOTAS DE GAS ', 'ECOPETROL', 3, 'unidades', '2025-01-01', '2026-01-30', NULL, 'activa', 'media', '2025-12-27 14:42:03', '2025-12-27 14:42:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registros_horas`
--

CREATE TABLE `registros_horas` (
  `id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `orden_produccion_id` int NOT NULL,
  `fecha` date NOT NULL,
  `horas_trabajadas` decimal(4,2) NOT NULL COMMENT 'Número de horas trabajadas (0.5 a 8.5)',
  `descripcion_trabajo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `estado` enum('registrado','validado','rechazado') COLLATE utf8mb4_unicode_ci DEFAULT 'registrado',
  `editado` tinyint(1) DEFAULT '0' COMMENT 'Indica si el registro ya fue editado (0=no, 1=sí)',
  `validado_por` int DEFAULT NULL,
  `fecha_validacion` timestamp NULL DEFAULT NULL,
  `comentario_validacion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `registros_horas`
--

INSERT INTO `registros_horas` (`id`, `usuario_id`, `orden_produccion_id`, `fecha`, `horas_trabajadas`, `descripcion_trabajo`, `observaciones`, `estado`, `editado`, `validado_por`, `fecha_validacion`, `comentario_validacion`, `created_at`, `updated_at`) VALUES
(1, 4, 2, '2025-12-01', 4.00, 'Revisión dossier, pintura ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-01 20:57:56', '2025-12-01 20:57:56'),
(2, 4, 4, '2025-12-01', 3.00, 'Revisión de entubado al haz ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-01 20:57:56', '2025-12-01 20:57:56'),
(3, 3, 2, '2025-12-01', 7.00, 'Elaboración, revisión y compilación del Dossier de Construcción para el equipo 044-E-12.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-01 20:59:11', '2025-12-01 20:59:11'),
(4, 6, 8, '2025-12-01', 7.00, 'Planos ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-02 20:00:11', '2025-12-02 20:00:11'),
(5, 6, 8, '2025-12-02', 8.00, 'Planos y solido ( cabezal)', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-02 20:01:06', '2025-12-02 20:01:06'),
(6, 7, 9, '2025-12-01', 7.00, 'Realizar Cotización de vasija o recipiente para tratamiento de lodos de 9.000 mm de Longitud X 2.216 mm de Ancho X 2.400 mm de altura, Cotizar Materiales , Cotizar Tiempos , Elaboración de Cotización en el SCP y Gestionar Firma de Autorización de la cotización con Don Arturo.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-02 20:02:22', '2025-12-02 20:02:22'),
(7, 4, 8, '2025-12-02', 6.00, 'wps, wpq, realización informe de avance, atención visita Tabarca', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 13:08:31', '2025-12-03 13:08:31'),
(8, 4, 2, '2025-12-02', 1.00, 'Realización dossier ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 13:08:31', '2025-12-03 13:08:31'),
(9, 4, 4, '2025-12-02', 1.00, 'reunion gamma rite', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 13:08:31', '2025-12-03 13:08:31'),
(10, 3, 8, '2025-12-02', 2.00, 'Inspección Inicial, acompañamiento a visitas del cliente, inspección al retiro de tubería y gestión del informe diario ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 13:08:51', '2025-12-03 13:08:51'),
(11, 3, 3, '2025-12-02', 1.50, 'Liberacion de MTRs para compra de Bridas y Couplings', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 13:08:51', '2025-12-03 13:08:51'),
(12, 3, 4, '2025-12-02', 1.00, 'Reunión con Gamma Rite para tomas de placa de la Junta Expansiva (C15 y C16)', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 13:08:51', '2025-12-03 13:08:51'),
(13, 3, 2, '2025-12-02', 3.50, 'Revisión y construcción del Dossier de Fabricación ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 13:08:51', '2025-12-03 13:08:51'),
(14, 2, 2, '2025-12-01', 3.50, 'Gestión en planta y documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 13:17:22', '2025-12-03 13:17:22'),
(15, 2, 1, '2025-12-01', 3.50, 'Gestión documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 13:18:10', '2025-12-03 13:18:10'),
(16, 2, 2, '2025-12-02', 1.00, 'Reunión', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 13:21:16', '2025-12-03 13:21:16'),
(17, 2, 1, '2025-12-02', 1.00, 'Reunión', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 13:21:34', '2025-12-03 13:21:34'),
(18, 2, 3, '2025-12-02', 0.50, 'Reunión', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 13:26:06', '2025-12-03 13:26:06'),
(19, 2, 5, '2025-12-02', 0.50, 'Reunión', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 13:26:48', '2025-12-03 13:26:48'),
(20, 2, 6, '2025-12-02', 0.50, 'Reunión', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 13:26:48', '2025-12-03 13:26:48'),
(21, 2, 2, '2025-12-02', 4.50, 'Gestión en planta y documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 13:27:42', '2025-12-03 13:27:42'),
(22, 4, 8, '2025-12-03', 0.67, 'Elaboración de informe y envío ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 13:36:22', '2025-12-03 13:36:22'),
(23, 5, 1, '2025-11-18', 0.50, 'Envío de corres a Laura de Geodis y Ecopetrol solicitando información del recibido a satisfacción del E-150C para lograr facturar.', NULL, 'registrado', 1, NULL, NULL, NULL, '2025-12-03 19:23:07', '2025-12-15 13:39:08'),
(24, 5, 2, '2025-11-18', 1.00, 'Solicitud cotización pintura detectora de ácido', NULL, 'registrado', 1, NULL, NULL, NULL, '2025-12-03 19:23:07', '2025-12-15 13:38:37'),
(25, 5, 10, '2025-11-18', 1.00, 'Diligenciar formulario \"Inteligencia de mercado - Servicio de material compuesto\"', NULL, 'registrado', 1, NULL, NULL, NULL, '2025-12-03 19:23:07', '2025-12-15 13:37:45'),
(26, 5, 10, '2025-11-18', 1.00, 'Diligenciar en CRM nueva solicitud de Ecopetrol y cotizaciones', NULL, 'registrado', 1, NULL, NULL, NULL, '2025-12-03 19:23:07', '2025-12-15 13:37:18'),
(27, 5, 10, '2025-11-18', 0.50, 'Diligenciar en CRM nueva solicitud de cotización de CEINFER', NULL, 'registrado', 1, NULL, NULL, NULL, '2025-12-03 19:23:07', '2025-12-15 13:36:52'),
(28, 5, 10, '2025-11-18', 1.00, 'Registrar en CRM cotizaciones de INSURCOL de acuerdo a cada alternativa presentada', NULL, 'registrado', 1, NULL, NULL, NULL, '2025-12-03 19:23:07', '2025-12-15 13:36:29'),
(29, 5, 10, '2025-11-18', 2.00, 'Realizar oficios de ofrecimiento de servicios', NULL, 'registrado', 1, NULL, NULL, NULL, '2025-12-03 19:23:07', '2025-12-15 13:35:51'),
(30, 5, 10, '2025-11-18', 0.50, 'Solicitar cotizacion de instrumentación a Instrucont para el proyectos de las botas de gas de CASABE', NULL, 'registrado', 1, NULL, NULL, NULL, '2025-12-03 19:23:07', '2025-12-15 13:34:33'),
(31, 5, 10, '2025-11-18', 0.50, 'Solicitar cotizacion de instrumentación a Granada para el proyectos de las botas de gas de CASABE', NULL, 'registrado', 1, NULL, NULL, NULL, '2025-12-03 19:23:07', '2025-12-15 13:33:45'),
(32, 5, 10, '2025-11-19', 2.00, 'Registrar desviaciones y/o aclaraciones en el formato de Observaciones pra el proceso \"Sondeo de Mercado - Botas de Gas CASA 2,3 Y 5)', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 19:31:19', '2025-12-03 19:31:19'),
(33, 5, 10, '2025-11-19', 1.00, 'Revisar fotografías y clasificarlas para el Nuevo Brochure que se quiere presentar.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 19:31:19', '2025-12-03 19:31:19'),
(34, 5, 10, '2025-11-19', 2.00, 'Diligenciar formato de Excel \"REGISTRO USUARIOS\" para la gestión de usuarios y contraseñas que se manejan en la empresa', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 19:37:43', '2025-12-03 19:37:43'),
(35, 5, 10, '2025-11-19', 2.00, 'Cambio de estado de los negocios en el CRM', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 19:37:43', '2025-12-03 19:37:43'),
(36, 7, 9, '2025-12-02', 8.00, 'Trabajé en el análisis de la Información suministrada por el cliente PSIG Para cotizar la fabricación de recipiente con Estampe ASME .', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 19:42:25', '2025-12-03 19:42:25'),
(37, 5, 10, '2025-11-20', 0.50, 'Registrar en CRM nueva solicitud de Ecopetrol', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 19:43:19', '2025-12-03 19:43:19'),
(38, 5, 10, '2025-11-20', 0.50, 'Seguimiento a la solicitud de cotización de instrumentación para enviar observaciones al cliente', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 19:43:19', '2025-12-03 19:43:19'),
(39, 5, 10, '2025-11-20', 2.00, 'Revisión SUPLOS, plataforma cliente PETROPERÚ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 19:43:19', '2025-12-03 19:43:19'),
(40, 5, 10, '2025-11-20', 2.00, 'Envío de observaciones botas de gas CASABE 2,3, y 5.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 19:43:19', '2025-12-03 19:43:19'),
(41, 5, 10, '2025-11-20', 0.50, 'Envio de observaciones a solicitud de cotización de CEINFER', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 19:43:19', '2025-12-03 19:43:19'),
(42, 5, 10, '2025-11-21', 2.00, 'Revisión proceso SECOP II - Ecopetrol S.A', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 20:15:57', '2025-12-03 20:15:57'),
(43, 5, 10, '2025-11-21', 1.00, 'Diligenciar formulario cámara de comercio', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 20:15:57', '2025-12-03 20:15:57'),
(44, 5, 10, '2025-11-22', 2.00, 'Preparar oferta técnica Botas de gas CASABE 2,3 y 5', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 20:17:46', '2025-12-03 20:17:46'),
(45, 5, 10, '2025-11-22', 0.50, 'Registrar en CRM nueva solicitud de cotización bajo la union temporal XEC THERMAL', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 20:17:46', '2025-12-03 20:17:46'),
(46, 5, 10, '2025-11-22', 0.50, 'Seguimiento pintura E-12 SIGMATHERM 230 PPG', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 20:17:46', '2025-12-03 20:17:46'),
(47, 5, 10, '2025-11-24', 1.00, 'Revisar y firmar acuerdo de confidencialidad Baker Hughes', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 20:21:42', '2025-12-03 20:21:42'),
(48, 5, 10, '2025-11-24', 2.00, 'Continuar la preparacion de la oferta tecnica de las botas de gas CASABE 2, 3 y 5', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 20:21:42', '2025-12-03 20:21:42'),
(49, 5, 10, '2025-11-24', 0.50, 'Registrar en CRM nueva solicitud de cotización de TABARCA - Reentube E-1153', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 20:21:42', '2025-12-03 20:21:42'),
(50, 5, 10, '2025-11-24', 0.50, 'Solicitar RUT a TWM SAS', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 20:21:42', '2025-12-03 20:21:42'),
(51, 6, 8, '2025-12-03', 8.00, 'solido y piezas en solword', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 21:51:35', '2025-12-03 21:51:35'),
(52, 4, 2, '2025-12-03', 4.00, 'Terminación dossier', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 22:01:30', '2025-12-03 22:01:30'),
(53, 4, 4, '2025-12-03', 0.50, 'Inspección, programación radiografía', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 22:01:30', '2025-12-03 22:01:30'),
(54, 4, 8, '2025-12-03', 2.50, 'Inspección, medición, elaboración informe', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-03 22:01:30', '2025-12-03 22:01:30'),
(55, 3, 8, '2025-12-03', 2.00, 'Acompañamiento a visita de Tabarca y gestión de informe ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-04 21:16:05', '2025-12-04 21:16:05'),
(56, 3, 2, '2025-12-03', 6.00, 'Construcción y liberación del Dossier de Fabricación', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-04 21:16:05', '2025-12-04 21:16:05'),
(57, 3, 8, '2025-12-04', 2.00, 'Acompañamiento y gestión de informe ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-04 21:41:44', '2025-12-04 21:41:44'),
(58, 3, 2, '2025-12-04', 6.00, 'Liberación de Dossier de Fabricación ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-04 21:41:44', '2025-12-04 21:41:44'),
(59, 2, 8, '2025-12-03', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-04 21:45:36', '2025-12-04 21:45:36'),
(60, 2, 2, '2025-12-03', 2.00, 'Gestión documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-04 21:45:36', '2025-12-04 21:45:36'),
(61, 2, 1, '2025-12-03', 3.00, 'Gestión documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-04 21:45:36', '2025-12-04 21:45:36'),
(62, 2, 1, '2025-12-03', 1.00, 'Gestión documental y revisión del dossier', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-04 21:46:18', '2025-12-04 21:46:18'),
(63, 2, 1, '2025-12-04', 2.00, 'Gestión documental y revisión del dossier', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-04 21:46:59', '2025-12-04 21:46:59'),
(64, 2, 2, '2025-12-04', 4.00, 'Gestión documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-04 21:47:22', '2025-12-04 21:47:22'),
(65, 2, 8, '2025-12-04', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-04 21:47:38', '2025-12-04 21:47:38'),
(66, 7, 2, '2025-12-03', 3.00, 'Participe de las Reuniones en las que se discutía con nuestro aliado Español PROYSER Acerca del proyecto Haz de Tubos E-12.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-04 22:00:16', '2025-12-04 22:00:16'),
(67, 7, 9, '2025-12-03', 2.50, 'Realice un Apoyo a la Sección de rectificación en torno a unos trabajos que se están realizando a la empresa SETIP , Cuya Orden de Producción es 6827.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-04 22:00:16', '2025-12-04 22:00:16'),
(68, 7, 9, '2025-12-03', 2.50, 'Análisis de la Información Recibida por parte  de la Empresa PSIG , Para la realización de una Cotización  ó Proyecto ASME.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-04 22:00:16', '2025-12-04 22:00:16'),
(69, 4, 8, '2025-12-04', 8.00, 'Mapeo cabezales, mapeo tubería dentro de cabezales, elaboración de informes sobre medición para cliente', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-05 15:46:27', '2025-12-05 15:46:27'),
(70, 5, 10, '2025-11-25', 1.00, 'Preparar cotización TABARCA E-1153', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-05 15:52:31', '2025-12-05 15:52:31'),
(71, 5, 9, '2025-11-25', 0.50, 'Envío de cotización TABARCA E-1153', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-05 15:52:31', '2025-12-05 15:52:31'),
(72, 5, 10, '2025-11-25', 0.50, 'Dar respuesta al cliente PSIG para concretar visita a sus instalaciones.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-05 15:52:31', '2025-12-05 15:52:31'),
(73, 5, 10, '2025-11-25', 1.00, 'Preparar cotización TABARCA fabricación de patrones', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-05 15:52:31', '2025-12-05 15:52:31'),
(74, 5, 9, '2025-11-25', 0.50, 'Envío de catización TABARCA Fabricación de patrones', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-05 15:52:31', '2025-12-05 15:52:31'),
(75, 5, 10, '2025-11-25', 2.00, 'Revisión de informe de Evaluación del Sistema de Gestión de la Producción – Método 5S', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-05 15:52:31', '2025-12-05 15:52:31'),
(76, 7, 9, '2025-12-04', 1.00, 'Atender Visita del Mecánico de la Empresa SETIP para seguimiento a reparación de Partes de Motor Cummins 4BT.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-05 15:58:38', '2025-12-05 15:58:38'),
(77, 7, 9, '2025-12-04', 3.50, 'Realizar Estudio de Factibilidad para la Alteración con Estampe R de Equipo Scrubber fabricado con Estampe ASME U ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-05 16:00:37', '2025-12-05 16:00:37'),
(78, 7, 9, '2025-12-04', 3.50, 'Realizar Estudio de Factibilidad para Fabricación con Estampe ASME U de Scrubber identificado con el TAG MBF - 3.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-05 16:02:09', '2025-12-05 16:02:09'),
(79, 5, 10, '2025-12-04', 1.00, 'Reunion Proyser E-002 (UT XEC THERMAL)', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-06 13:28:56', '2025-12-06 13:28:56'),
(80, 5, 10, '2025-12-04', 1.50, 'Presentar oferta técnica y comercial de la UT (E-002)', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-06 13:28:56', '2025-12-06 13:28:56'),
(81, 5, 10, '2025-12-04', 0.50, 'Diligenciar en CRM nueva solicitu de cotización para el cliente PETROSANTANDER', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-06 13:28:56', '2025-12-06 13:28:56'),
(82, 5, 10, '2025-12-04', 0.50, 'Diligenciar en CRM nueva solicitud de cotización del cliente PEDLEX', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-06 13:28:56', '2025-12-06 13:28:56'),
(83, 5, 10, '2025-12-04', 0.50, 'Diligenciar en CRM nueva solicitud de cotización para el cliente Ecopetrol \"Cilindros GLP\"', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-06 13:28:56', '2025-12-06 13:28:56'),
(84, 5, 10, '2025-12-04', 0.50, 'Descargue de documentación en SAP ARIBA de nuevo proceso de Ecopetrol \"Cilindros GLP\"', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-06 13:28:56', '2025-12-06 13:28:56'),
(85, 5, 2, '2025-12-04', 1.50, 'Realizar en Geoflow Aprobación, confirmacion y creación de embarque de la OC 986600 (E-12)', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-06 13:28:56', '2025-12-06 13:28:56'),
(86, 5, 2, '2025-12-04', 0.50, 'Enviar registro fotográfico a Angy Caballero de Geodis de E-12', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-06 13:28:56', '2025-12-06 13:28:56'),
(87, 5, 10, '2025-12-04', 0.50, 'Envío de acuerdo de confidencialidad firmado a la camara de comercio', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-06 13:28:56', '2025-12-06 13:28:56'),
(88, 5, 2, '2025-12-04', 1.00, 'Verificar que prueba de adherencia le aplica al E-12 ya que se le aplicó una capa de Interbond para darle color gris', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-06 13:28:56', '2025-12-06 13:28:56'),
(89, 5, 1, '2025-12-05', 1.00, 'Confirmar y expedir en Ariba reemplazo de la OC E-150C', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-06 13:33:55', '2025-12-06 13:33:55'),
(90, 5, 10, '2025-12-05', 0.50, 'Compartir nuevamente acuerdo de confidencialidad firmado a Dailing, en formato PDF', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-06 13:33:55', '2025-12-06 13:33:55'),
(91, 5, 10, '2025-12-05', 2.00, 'Preparar y enviar observaciones frente al proceso de PETROSANTANDER - TANQUE BUTANO-PROPANO', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-06 13:33:55', '2025-12-06 13:33:55'),
(92, 5, 2, '2025-12-05', 1.50, 'Preparar documentación para despacho del E-12', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-06 13:33:55', '2025-12-06 13:33:55'),
(93, 5, 2, '2025-12-05', 0.50, 'Compartir a Alcomex, registro del material E-12 ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-06 13:33:55', '2025-12-06 13:33:55'),
(94, 5, 10, '2025-12-05', 1.00, 'Revisar costos de importacion con All Trans para la entrega del E-002 en Refinería de Cartagena.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-06 13:33:55', '2025-12-06 13:33:55'),
(95, 5, 10, '2025-12-05', 0.50, 'Revisar con Comercio Exterior la partida arancelaria del E-002', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-06 13:33:55', '2025-12-06 13:33:55'),
(96, 5, 2, '2025-12-05', 0.50, 'Revisar con Jonathan Sampayo (Calidad) resultados de las pruebas de pintura para presentar informe.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-06 13:33:55', '2025-12-06 13:33:55'),
(97, 5, 2, '2025-12-02', 2.00, 'Redactar correo de pintura E-12 (COLOR DEL EPOXICO NOVOLACA)', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-06 14:40:11', '2025-12-06 14:40:11'),
(98, 5, 2, '2025-12-02', 0.50, 'Geoflow E-12 (Notificar a Geodis que no aparece la orden de compra)', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-06 14:40:11', '2025-12-06 14:40:11'),
(99, 5, 10, '2025-12-02', 1.00, 'Modificación cotizaciones E-1112 (TABARCA)', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-06 14:40:11', '2025-12-06 14:40:11'),
(100, 5, 9, '2025-12-02', 0.50, 'Envío de cotizaciones actualizadas del E-1112 (TABARCA)', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-06 14:40:11', '2025-12-06 14:40:11'),
(101, 5, 10, '2025-12-02', 0.50, 'Compartir respuesta de observaciones a instrucont y notificar que la nuevo fecha de entrega de la propuesta es el 10 de diciembre. (BOTAS DE GAS CASABE 2,3 y 5)', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-06 14:40:11', '2025-12-06 14:40:11'),
(102, 5, 2, '2025-12-02', 1.00, 'Redactar correo a Geodis de las dudas que se tienen frente a la entrega del equipo E-12 en Refineria de Cartagena', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-06 14:40:11', '2025-12-06 14:40:11'),
(103, 5, 10, '2025-12-02', 0.50, 'Revisión acuerdo de confidencialidad Alemanes', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-06 14:40:11', '2025-12-06 14:40:11'),
(104, 7, 9, '2025-12-05', 4.00, 'Realizar Estudio de Factibilidad para Re Rating de Botella Fabricada con ESTAMPE U identificada con TAG MBL - 6-', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-09 19:13:09', '2025-12-09 19:13:09'),
(105, 7, 9, '2025-12-05', 4.00, 'Realizar Estudio de Factibilidad para el Re Rating con Estampe R a equipo AFTER COOLER Identificada con TAG HAL - 1.200.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-09 19:16:16', '2025-12-09 19:16:16'),
(106, 3, 8, '2025-12-05', 2.00, 'Acompañamiento a visita y gestión para la confirmación del certificado de expansión y nuevo alcance', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-10 12:57:51', '2025-12-10 12:57:51'),
(107, 3, 2, '2025-12-05', 6.00, 'Liberación de Pintura (ensayo de pull of y espesores)\r\nConstrucción del Dossier de Construcción para Ecopetrol.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-10 12:57:51', '2025-12-10 12:57:51'),
(108, 3, 2, '2025-12-06', 4.00, 'Liberación de Pintura (Construcción de informe de Liberación de pintura y diligenciamiento de formato de perfil de anclaje)\r\nConstrucción del Dossier de Construcción para Ecopetrol.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-10 13:03:01', '2025-12-10 13:03:01'),
(109, 3, 8, '2025-12-06', 1.00, 'Acompañamiento a visita de Tabarca y Ecopetrol, para determinar el nuevo alcance del equipo ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-10 13:03:01', '2025-12-10 13:03:01'),
(110, 6, 8, '2025-12-04', 8.00, 'emsamblado soliword', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-10 13:13:23', '2025-12-10 13:13:23'),
(111, 6, 8, '2025-12-05', 8.00, 'diseño de planos ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-10 13:13:50', '2025-12-10 13:13:50'),
(112, 6, 8, '2025-12-06', 5.00, 'educion de planos y coreeecion', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-10 13:14:24', '2025-12-10 13:14:24'),
(113, 6, 8, '2025-12-09', 8.00, 'plano final', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-10 13:14:45', '2025-12-10 13:14:45'),
(114, 3, 8, '2025-12-09', 2.00, 'Acompañamiento de visita de Tabarca, para seguimiento y solicitud de entrega del equipo el 10/12/2025\r\nGestión documental (Informes Fin de semana) y aprobación del % de expansión ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-10 13:43:37', '2025-12-10 13:43:37'),
(115, 3, 2, '2025-12-09', 4.00, 'Construcción y consolidación del Dossier ASME \r\nGeneración y envió al cliente de nuevos enlaces de descarga para el Dossier de Ecopetrol', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-10 13:43:37', '2025-12-10 13:43:37'),
(116, 3, 4, '2025-12-09', 1.00, 'Recibo de Informes de radiografía de las juntas expansivas (C15 Y C16)\r\nProgramación de radiografías para el cierre del programada de inspección', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-10 13:43:37', '2025-12-10 13:43:37'),
(117, 3, 3, '2025-12-09', 1.00, 'Reunión virtual con GIIE (Empresa de END) para cotización de las radiografías del proyecto. ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-10 13:43:37', '2025-12-10 13:43:37'),
(118, 2, 2, '2025-12-05', 3.00, 'Revisión documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-10 14:28:05', '2025-12-10 14:28:05'),
(119, 2, 1, '2025-12-05', 3.00, 'Revisión documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-10 14:28:05', '2025-12-10 14:28:05'),
(120, 2, 8, '2025-12-05', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-10 14:28:05', '2025-12-10 14:28:05'),
(121, 2, 2, '2025-12-06', 3.00, 'Revisión documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-10 14:29:44', '2025-12-10 14:29:44'),
(122, 2, 8, '2025-12-06', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-10 14:29:44', '2025-12-10 14:29:44'),
(123, 2, 3, '2025-12-10', 2.00, 'Revisión de ingeniería', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-10 14:31:01', '2025-12-10 14:31:01'),
(124, 2, 5, '2025-12-10', 2.00, 'Revisión de ingeniería', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-10 14:31:01', '2025-12-10 14:31:01'),
(125, 2, 6, '2025-12-10', 2.00, 'Revisión de ingeniería', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-10 14:31:01', '2025-12-10 14:31:01'),
(126, 2, 8, '2025-12-10', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-10 14:31:01', '2025-12-10 14:31:01'),
(127, 2, 3, '2025-12-09', 2.00, 'Revisión de ingeniería', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-10 14:32:30', '2025-12-10 14:32:30'),
(128, 2, 5, '2025-12-09', 2.00, 'Revisión de ingeniería', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-10 14:32:30', '2025-12-10 14:32:30'),
(129, 2, 6, '2025-12-09', 2.00, 'Revisión de ingeniería', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-10 14:32:30', '2025-12-10 14:32:30'),
(130, 2, 8, '2025-12-09', 2.00, 'Gestión en planta / Líquidos penetrantes', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-10 14:32:30', '2025-12-10 14:32:30'),
(131, 4, 8, '2025-12-05', 5.00, 'Inspección, medición, elaboración de informes', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-11 14:54:14', '2025-12-11 14:54:14'),
(132, 4, 4, '2025-12-05', 2.00, 'Inspección programación radiografías', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-11 14:54:14', '2025-12-11 14:54:14'),
(133, 4, 2, '2025-12-05', 1.00, 'dossier', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-11 14:54:14', '2025-12-11 14:54:14'),
(134, 4, 8, '2025-12-06', 5.00, 'Medición cabezales, visita Tabarca- Ecopetrol, ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-11 14:55:54', '2025-12-11 14:55:54'),
(135, 4, 8, '2025-12-09', 7.00, 'Inspección, elaboración de informe', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-11 14:57:25', '2025-12-11 14:57:25'),
(136, 4, 4, '2025-12-09', 1.00, 'Inspección programa de radiografía', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-11 14:57:25', '2025-12-11 14:57:25'),
(137, 4, 8, '2025-12-10', 6.00, 'Alistamiento y entrega, mapeo de tubos, líquidos ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-11 14:59:23', '2025-12-11 14:59:23'),
(138, 4, 4, '2025-12-10', 2.00, 'Programación de limpieza, inspección y programación de radiografías', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-11 14:59:23', '2025-12-11 14:59:23'),
(139, 4, 8, '2025-12-06', 0.67, 'Medición y levantamiento de druns suministrados para corte y aplicación de soldadura ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-11 18:00:38', '2025-12-11 18:00:38'),
(140, 5, 10, '2025-12-03', 1.00, 'Reunión Proyser', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-15 13:48:32', '2025-12-15 13:48:32'),
(141, 5, 10, '2025-12-03', 2.00, 'Realizar oferta técnica TMW ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-15 13:48:32', '2025-12-15 13:48:32'),
(142, 5, 9, '2025-12-03', 0.50, 'Envío de oferta a TWM ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-15 13:48:32', '2025-12-15 13:48:32'),
(143, 5, 2, '2025-12-03', 1.00, 'Realizar pre alerta a Angy Caballero', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-15 13:48:32', '2025-12-15 13:48:32'),
(144, 5, 10, '2025-12-03', 1.50, 'Realizar oferta Haces Ecopetrol Cartagena - Juan Pablo Villalba ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-15 13:48:32', '2025-12-15 13:48:32'),
(145, 5, 9, '2025-12-03', 0.50, 'Envío oferta Haces Cartagena', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-15 13:48:32', '2025-12-15 13:48:32'),
(146, 5, 10, '2025-12-06', 1.00, 'Enviar correo a All trans con información para la cotización de los costos de nacionalización E-002 (UT XEC THERMAL)', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-15 14:21:28', '2025-12-15 14:21:28'),
(147, 5, 2, '2025-12-06', 0.50, 'Envío registro fotográfico a Angy Caballero del E-12 sobre camión', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-15 14:21:28', '2025-12-15 14:21:28'),
(148, 5, 2, '2025-12-06', 0.50, 'Envío registro fotográfico a Alcomex del E-12 sobre camión', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-15 14:21:28', '2025-12-15 14:21:28'),
(149, 5, 10, '2025-12-06', 2.00, 'Oferta técnica Botas de gas CASABE', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-15 14:21:28', '2025-12-15 14:21:28'),
(150, 5, 10, '2025-12-06', 0.50, 'Registrar en CRM negocios', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-15 14:21:28', '2025-12-15 14:21:28'),
(151, 5, 10, '2025-12-09', 2.00, 'Prepara oferta Botas de gas CASABE', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-15 14:29:40', '2025-12-15 14:29:40'),
(152, 5, 10, '2025-12-09', 1.00, 'Revisar costo nacionalización E-002', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-15 14:29:40', '2025-12-15 14:29:40'),
(153, 5, 10, '2025-12-09', 1.00, 'Preparar oferta tecnica de Petrosantander - Tanque propano', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-15 14:29:40', '2025-12-15 14:29:40'),
(154, 5, 2, '2025-12-09', 2.00, 'Conversación con Angy Caballero respecto a la entrega del E-12 (Inconvenientes en la entrega)', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-15 14:29:40', '2025-12-15 14:29:40'),
(155, 5, 10, '2025-12-10', 0.50, 'Realizar en el factory OP del E-1217', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-15 15:29:43', '2025-12-15 15:29:43'),
(156, 5, 10, '2025-12-10', 2.50, 'Terminar oferta tecnica botas de gas CASABE', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-15 15:29:43', '2025-12-15 15:29:43'),
(157, 5, 10, '2025-12-10', 1.50, 'Preparar oferta con el valor de la intrumentación del proveedor INSTRUCONT - Proceso botas de gas CASABE', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-15 15:29:43', '2025-12-15 15:29:43'),
(158, 5, 10, '2025-12-10', 1.00, 'Terminar oferta técnica petrosantander - Tanque propano butano', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-15 15:29:43', '2025-12-15 15:29:43'),
(159, 5, 10, '2025-12-10', 1.00, 'Actualizar oferta técnica E-002', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-15 15:29:43', '2025-12-15 15:29:43'),
(160, 5, 9, '2025-12-10', 0.50, 'Envío oferta actualizada E-002', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-15 15:29:43', '2025-12-15 15:29:43'),
(161, 5, 9, '2025-12-10', 1.00, 'Enviar oferta Botas de gas CASABE', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-15 15:29:43', '2025-12-15 15:29:43'),
(162, 5, 10, '2025-12-11', 6.00, 'conversación con All trans, Proyser, solicitar reunión a Maria Elena para hablar temas tributarios, aduaneros y cambiarios, exponer situación a Maria Elena. ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-15 15:43:15', '2025-12-15 15:43:15'),
(163, 5, 11, '2025-12-11', 0.50, 'Modificar cotización en Factory', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-15 15:43:15', '2025-12-15 15:43:15'),
(164, 6, 8, '2025-12-10', 8.00, 'ajuste de planos', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-16 14:52:48', '2025-12-16 14:52:48'),
(165, 6, 8, '2025-12-11', 8.00, 'ajuste de solido', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-16 14:53:18', '2025-12-16 14:53:18'),
(166, 6, 8, '2025-12-12', 8.00, 'ajuste de plano y tablas', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-16 14:53:45', '2025-12-16 14:53:45'),
(167, 6, 8, '2025-12-13', 5.00, 'planos finales', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-16 14:54:19', '2025-12-16 14:54:19'),
(168, 6, 11, '2025-12-15', 7.00, 'emsamble ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-16 14:56:12', '2025-12-16 14:56:12'),
(169, 6, 11, '2025-12-13', 1.00, 'tubos benturi', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-17 21:52:44', '2025-12-17 21:52:44'),
(170, 6, 11, '2025-12-15', 1.50, 'tintas y  mapeo', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-17 21:52:49', '2025-12-17 21:52:49'),
(171, 2, 8, '2025-12-17', 5.00, 'Alistamiento y visita en campo (refinería de Bcaeja)', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-17 21:56:11', '2025-12-17 21:56:11'),
(172, 2, 11, '2025-12-11', 4.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-17 21:59:46', '2025-12-17 21:59:46'),
(173, 2, 8, '2025-12-11', 4.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-17 21:59:46', '2025-12-17 21:59:46'),
(174, 5, 10, '2025-12-12', 1.00, 'Compartir a Maria Elena documento \"Publicación de Documentos Oferente SECOP\" del proceso botas de gas CASABE', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-19 19:21:10', '2025-12-19 19:21:10'),
(175, 5, 10, '2025-12-12', 3.00, 'Diligenciar formato \"Evaluación técnica\" para el proyectos Botas de gas CASABE', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-19 19:21:10', '2025-12-19 19:21:10'),
(176, 5, 10, '2025-12-12', 1.00, 'Diligenciar en CRM cotización botas de gas CASABE', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-19 19:21:10', '2025-12-19 19:21:10'),
(177, 5, 10, '2025-12-12', 0.50, 'Solicitar a Instrucont diligenciar Formato de \"Evaluación técnica\"', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-19 19:21:10', '2025-12-19 19:21:10'),
(178, 5, 10, '2025-12-12', 0.50, 'Compartir al equipo nuevo proceso de cotización de ASTILLEROS FINLANDES', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-19 19:21:10', '2025-12-19 19:21:10'),
(179, 5, 10, '2025-12-18', 1.00, 'Terminar de diligenciar formato de \"Evaluación técnica\"', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-19 19:59:26', '2025-12-19 19:59:26'),
(180, 5, 10, '2025-12-18', 1.00, 'Enviar a Maria Elena formato completamente diligenciado', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-19 19:59:26', '2025-12-19 19:59:26'),
(181, 5, 10, '2025-12-18', 0.50, 'Crear OP Factory \"Eyectores\" del cliente Tabarca', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-19 19:59:26', '2025-12-19 19:59:26'),
(182, 5, 10, '2025-12-18', 0.50, 'Crear OP Factory \"Recuperación canal\" del cliente Tabarca', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-19 19:59:26', '2025-12-19 19:59:26'),
(183, 5, 10, '2025-12-18', 0.50, 'Construir oferta técnica comercial correspondiente a la solicitud de Astillero Finlandés', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-19 19:59:26', '2025-12-19 19:59:26'),
(184, 5, 10, '2025-12-18', 1.00, 'Diligenciaer en el CRM nuevos negocios', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-19 19:59:26', '2025-12-19 19:59:26'),
(185, 5, 10, '2025-12-18', 2.00, 'Actualizar negocios y/o cotizaciones en el CRM', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-19 19:59:26', '2025-12-19 19:59:26'),
(186, 5, 10, '2025-12-18', 1.50, 'Revisión de informe ejecutivo nuevo proceso ANDE TAMBORES ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-19 19:59:26', '2025-12-19 19:59:26'),
(187, 5, 10, '2025-12-19', 4.00, 'Terminar oferta Astillero finlandes', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-19 20:23:07', '2025-12-19 20:23:07'),
(188, 5, 10, '2025-12-19', 1.00, 'Diligenciar encuesta sondeo de mercado Suministro de Trampas', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-19 20:23:07', '2025-12-19 20:23:07'),
(189, 5, 10, '2025-12-19', 0.50, 'Diligenciar en CRM nuevo proceso \"ANDE TAMBORES\"', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-19 20:23:07', '2025-12-19 20:23:07'),
(190, 5, 2, '2025-12-19', 0.50, 'Compartir al equipo hallazgos del E-12', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-19 20:23:07', '2025-12-19 20:23:07'),
(191, 5, 4, '2025-12-19', 0.50, 'Compartir al ingeniero Nelson formato de acta de entrega', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-19 20:23:07', '2025-12-19 20:23:07'),
(192, 5, 9, '2025-12-19', 1.00, 'Enviar cotización a PSIG ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-19 20:23:07', '2025-12-19 20:23:07'),
(193, 5, 10, '2025-12-19', 0.50, 'Solicitar RUT a DISMET', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-19 20:23:07', '2025-12-19 20:23:07'),
(194, 3, 3, '2025-12-26', 2.00, 'Se presentó, aprobó y firmó las listas de chequeo de los Aero-enfriadores ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 13:49:07', '2025-12-27 13:49:07'),
(195, 3, 4, '2025-12-26', 6.00, 'Se revisó los comentarios de Ingeniería y se revisaron materiales ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 13:56:37', '2025-12-27 13:56:37'),
(196, 6, 11, '2025-12-16', 8.00, 'Edición y ensamblaje de planos', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 13:56:59', '2025-12-27 13:56:59'),
(197, 6, 8, '2025-12-17', 8.00, 'Modificación de planos ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 13:57:29', '2025-12-27 13:57:29'),
(198, 6, 11, '2025-12-18', 8.00, 'Detallado de planos', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 13:58:04', '2025-12-27 13:58:04'),
(199, 6, 8, '2025-12-19', 8.00, 'Edición de planos ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 13:58:29', '2025-12-27 13:58:29'),
(200, 6, 8, '2025-12-20', 4.00, 'Planos y modificaciones ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 13:59:09', '2025-12-27 13:59:09'),
(201, 6, 11, '2025-12-20', 1.00, 'Tintas ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 13:59:09', '2025-12-27 13:59:09'),
(202, 6, 8, '2025-12-22', 7.00, 'Modificaciones de planos ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 13:59:41', '2025-12-27 13:59:41'),
(203, 6, 11, '2025-12-23', 8.00, 'Modificaciones y detallado de planos', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:00:16', '2025-12-27 14:00:16'),
(204, 6, 5, '2025-12-24', 4.00, 'Edicion de planos y solido', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:00:59', '2025-12-27 14:00:59'),
(205, 6, 4, '2025-12-26', 8.00, 'Planos y solido', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:01:36', '2025-12-27 14:01:36'),
(206, 6, 4, '2025-12-27', 5.00, 'Edicion de solido', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:02:03', '2025-12-27 14:02:03'),
(207, 7, 9, '2025-12-06', 5.00, 'Iniciar la Realización del Estudio de Factibilidad para La Alteración de Scrubber de la Empresa P.S.I.G', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:02:27', '2025-12-27 14:02:27'),
(208, 3, 10, '2025-12-24', 5.00, 'Construcción y revisión de Informes de Paradas', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:08:48', '2025-12-27 14:08:48'),
(209, 7, 9, '2025-12-09', 4.00, 'Realizar Solicitud de cotización de Materiales a proveedores , Realizar solicitud de cotización de servicio de Prueba de Espesores a Proveedores para estudio de factibilidad de la Alteración certificada con Estampe ASME del Scrubber identificado con TAG MBF-2 de La empresa P.S.I.G.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:09:05', '2025-12-27 14:09:05'),
(210, 7, 9, '2025-12-09', 4.00, 'Seguimiento a Trabajos a realizar en la sección de Rectificación. ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:09:05', '2025-12-27 14:09:05'),
(211, 3, 10, '2025-12-23', 8.00, 'Construcción y revisión de Informes de Paradas', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:11:18', '2025-12-27 14:11:18'),
(212, 7, 9, '2025-12-10', 4.00, 'Continuar con la Realización del Estudio de factibilidad para la Alteración Certificada con  Estampe R del Scrubber MBF-2.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:12:14', '2025-12-27 14:12:14'),
(213, 7, 9, '2025-12-10', 4.00, 'Seguimiento a Actividades y trabajos a desarrollar en la Sección de Rectificación.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:12:14', '2025-12-27 14:12:14'),
(214, 3, 7, '2025-12-15', 7.00, 'Inspección del equipo en patio, retiro de llantas y plástico', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:15:27', '2025-12-27 14:15:27'),
(215, 3, 7, '2025-12-16', 8.00, 'Reunión con personal de Ecopetrol y personal de izaje para determinar el alcance y la manera correcta de hacer la maniobra ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:18:16', '2025-12-27 14:18:16'),
(216, 7, 9, '2025-12-11', 4.00, 'Continuar con la Realización del Estudio de Factibilidad para la Alteración de Scrubber identificado con el TAG MBF-2 de la Empresa P.S.I.G.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:18:33', '2025-12-27 14:18:33'),
(217, 7, 9, '2025-12-11', 4.00, 'Seguimiento a Trabajos a Realizar en la sección de Rectificación de Motores.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:18:33', '2025-12-27 14:18:33'),
(218, 7, 9, '2025-12-12', 4.00, 'Continuar con la Realización del estudio de factibilidad de la Alteración con Estampe R del Scrubber identificado con el TAG MBF - 2.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:20:49', '2025-12-27 14:20:49'),
(219, 7, 9, '2025-12-12', 4.00, 'Seguimiento a trabajos por Desarrollar en la Sección de Rectificación.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:20:49', '2025-12-27 14:20:49'),
(220, 3, 7, '2025-12-17', 8.00, 'Revisión de las áreas (patio y planta de Ecopetrol) para ultimar detalles de maniobra ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:24:06', '2025-12-27 14:24:06'),
(221, 7, 10, '2025-12-13', 5.00, 'Seguimiento a Ordenes de Producción rectificación Números 6828 y 6834 del cliente Sociedad Portuaria la  Gloria.  ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:24:36', '2025-12-27 14:24:36'),
(222, 3, 7, '2025-12-18', 8.00, 'Izaje de equipo en patio, movilización a planta, desmontaje de equipo anterior e instalación de equipo nuevo', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:25:27', '2025-12-27 14:25:27'),
(223, 7, 9, '2025-12-15', 4.00, 'Revisión y Ajuste de Estudio de Factibilidad para la Fabricación con Estampe Asme U de Scrubber identificado con TAG MBF-3.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:30:48', '2025-12-27 14:30:48'),
(224, 7, 10, '2025-12-15', 3.00, 'Atención a Clientes de Rectificación.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:30:48', '2025-12-27 14:30:48'),
(225, 3, 10, '2025-12-22', 7.00, 'Construcción y revisión de Informes de Paradas', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:32:19', '2025-12-27 14:32:19'),
(226, 7, 9, '2025-12-16', 4.00, 'Revisión y Ajuste de Estudio de Factibilidad para la Fabricación con Estampe Asme U de Scrubber identificado con TAG MBF-3.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:33:44', '2025-12-27 14:33:44'),
(227, 7, 10, '2025-12-16', 4.00, 'Atención clientes de Rectificación.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:33:44', '2025-12-27 14:33:44'),
(228, 7, 9, '2025-12-17', 6.00, 'Análisis de Información para la Realización de estudio de factibilidad de la Modificación de recipiente identificado con el TAG MBL-6 del cliente P.S.I.G.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:40:11', '2025-12-27 14:40:11'),
(229, 7, 10, '2025-12-17', 2.00, 'Seguimiento a Trabajos por realizar para Clientes de la Sección de Rectificación.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:40:11', '2025-12-27 14:40:11'),
(230, 7, 9, '2025-12-18', 6.00, 'Análisis de la Información para la Realización de estudio de factibilidad para la Modificacion de Equipo identificado con el TAG HAL 1.200 del cliente P.S.I.G.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:45:09', '2025-12-27 14:45:09'),
(231, 7, 10, '2025-12-18', 2.00, 'Gestión documental y atención de clientes de la Sección de Rectificación.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:45:09', '2025-12-27 14:45:09'),
(232, 7, 9, '2025-12-19', 2.00, 'Estructurar Cotización del cliente ALMACO.\r\n', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:53:29', '2025-12-27 14:53:29'),
(233, 5, 10, '2025-12-20', 0.50, 'Registrar en el CRM a la empresa MONTAJES TECNICOS', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:56:45', '2025-12-27 14:56:45'),
(234, 5, 10, '2025-12-20', 2.50, 'Reunión equipo comercial', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:56:46', '2025-12-27 14:56:46'),
(235, 5, 10, '2025-12-20', 1.50, 'Revisar informe ejecutivo - solicitud de cotizacion Reentube de Aeroenfriadores', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:56:46', '2025-12-27 14:56:46'),
(236, 5, 10, '2025-12-20', 0.50, 'Registro de negocios en CRM', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 14:56:46', '2025-12-27 14:56:46'),
(237, 3, 12, '2025-12-19', 8.00, 'Movilización a Puerto Gaitan', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 15:07:39', '2025-12-27 15:07:39'),
(238, 3, 12, '2025-12-20', 5.00, 'Movilización a Caño sur, inspección visual junto a Horacio e inspector de Tecnitanques del estado de las 3 Botas de Gas ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 15:09:38', '2025-12-27 15:09:38'),
(239, 5, 10, '2025-12-22', 1.00, 'Envío de correo comercial a INSURCOL', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 15:10:26', '2025-12-27 15:10:26'),
(240, 5, 10, '2025-12-22', 1.00, 'Enviar correo de alineacion comercial a PETROSANTANDER', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 15:10:26', '2025-12-27 15:10:26'),
(241, 5, 10, '2025-12-22', 1.00, 'Revisar proceso DISMET', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 15:10:26', '2025-12-27 15:10:26'),
(242, 5, 10, '2025-12-22', 1.00, 'Documentar preguntar del proceso \"Reentube de Aeroenfriadores\" y enviarlas a DIEGO de MONTAJES TECNICOS ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 15:10:26', '2025-12-27 15:10:26'),
(243, 5, 10, '2025-12-22', 2.00, 'Construir oferta técnica KO DRUM', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 15:10:26', '2025-12-27 15:10:26'),
(244, 5, 2, '2025-12-22', 0.50, 'Conversación con Angy caballero respecto a las respuestas de los hallazgos  en el E-12', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 15:10:26', '2025-12-27 15:10:26'),
(245, 5, 10, '2025-12-22', 0.50, 'Reunión comercial con Cesar Urueña', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 15:10:26', '2025-12-27 15:10:26'),
(246, 5, 3, '2025-12-23', 0.50, 'Revisar pintura de aeroenfriadores', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 15:31:25', '2025-12-27 15:31:25'),
(247, 5, 5, '2025-12-23', 0.50, 'Solicitar cotización de pintura a Aplika para Aeroenfriadores', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 15:31:25', '2025-12-27 15:31:25'),
(248, 5, 10, '2025-12-23', 2.00, 'Ajustar cotización PSIG', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 15:31:25', '2025-12-27 15:31:25'),
(249, 5, 10, '2025-12-23', 1.00, 'Oferta técnica KO DRUM', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 15:31:25', '2025-12-27 15:31:25'),
(250, 5, 10, '2025-12-23', 1.00, 'Diligenciar formato REFERENCIACION DE PRECIOS', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 15:31:25', '2025-12-27 15:31:25'),
(251, 5, 2, '2025-12-23', 1.00, 'Conversación con Angy caballero de Geosi, con respecto al E-12 ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 15:31:25', '2025-12-27 15:31:25'),
(252, 5, 10, '2025-12-24', 1.00, 'Ajustar oferta PSIG', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 15:32:36', '2025-12-27 15:32:36'),
(253, 5, 10, '2025-12-24', 3.00, 'Oferta técnica KO DRUM - Ecopetrol', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 15:32:36', '2025-12-27 15:32:36'),
(254, 5, 10, '2025-12-26', 0.50, 'Llamada de seguimiento a cotización del cliente PSIG', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 16:36:58', '2025-12-27 16:36:58'),
(255, 5, 10, '2025-12-26', 0.50, 'Correo de seguimiento a la reunión con Maria Elena (UT) ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-27 16:36:58', '2025-12-27 16:36:58'),
(256, 4, 11, '2025-12-15', 7.00, 'Se realiza inspección visual, ensayos no destructivos mediante líquidos penetrantes, medición dimensional de los agujeros en los cabezales fijo y flotante, verificación dimensional del conformado del haz tubular y elaboración y envío del informe técnico correspondiente', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 13:15:02', '2025-12-29 13:15:02'),
(257, 4, 10, '2025-12-16', 8.00, 'Se realizó inspección visual a las compuertas de quemador, se ejecutaron ensayos no destructivos mediante líquidos penetrantes, se revisa la integridad de cada uno de los quemadores y se elaboró el informe de entrega correspondiente', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 13:18:38', '2025-12-29 13:18:38'),
(258, 4, 8, '2025-12-17', 8.00, 'Se realizó el traslado a la refinería con el fin de brindar las explicaciones técnicas correspondientes al proceso de expansión del haz tubular, se lleva el registro técnico y documental del canal a reconstruir, el cual se encuentra en las instalaciones del taller', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 13:23:09', '2025-12-29 13:23:09'),
(259, 4, 10, '2025-12-18', 8.00, 'Se realizó el registro técnico y la inspección visual de los trabajos ejecutados en el canal, así como la aplicación de ensayos no destructivos mediante líquidos penetrantes', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 13:26:05', '2025-12-29 13:26:05'),
(260, 4, 10, '2025-12-19', 8.00, 'Se realizó la gestión, revisión y validación de los procedimientos de soldadura (WPS) y de las calificaciones de soldadores (WPQ) aplicables a la soldadura bimetálica del canal', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 13:34:26', '2025-12-29 13:34:26'),
(261, 4, 10, '2025-12-20', 4.00, 'Elaboración de informe final del canal, ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 13:36:33', '2025-12-29 13:36:33'),
(262, 4, 4, '2025-12-20', 1.00, 'Elaboración ensayos no destructivos por líquidos penetrantes ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 13:36:33', '2025-12-29 13:36:33'),
(263, 4, 10, '2025-12-22', 7.00, 'Revisión de informes correspondientes a paradas', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 13:38:08', '2025-12-29 13:38:08'),
(264, 4, 4, '2025-12-23', 8.00, 'Revisión de elementos soldados y preparación para visita del inspector  ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 13:39:53', '2025-12-29 13:39:53'),
(265, 4, 4, '2025-12-24', 5.00, 'Alistamiento dossier para visita de inspector ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 13:41:07', '2025-12-29 13:41:07'),
(266, 4, 4, '2025-12-26', 8.00, 'visita inspector', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 13:41:54', '2025-12-29 13:41:54'),
(267, 4, 4, '2025-12-27', 5.00, 'Revisión de pendientes luego de la visita del inspector ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 13:42:56', '2025-12-29 13:42:56'),
(268, 3, 4, '2025-12-27', 5.00, 'Revisión de Materiales y comentarios del Inspector ASME', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 13:46:05', '2025-12-29 13:46:05'),
(269, 5, 10, '2025-12-29', 0.50, 'Correo de recordatorio a Proyser para dar respuesta a las observaciones emitidas por el Ecopetrol para el proceso del equipo E-002, bajo la unión temporal.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 15:17:38', '2025-12-29 15:17:38');
INSERT INTO `registros_horas` (`id`, `usuario_id`, `orden_produccion_id`, `fecha`, `horas_trabajadas`, `descripcion_trabajo`, `observaciones`, `estado`, `editado`, `validado_por`, `fecha_validacion`, `comentario_validacion`, `created_at`, `updated_at`) VALUES
(270, 5, 10, '2025-12-29', 3.50, 'Terminar oferta tecnica KO DRUM', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 15:17:38', '2025-12-29 15:17:38'),
(271, 4, 4, '2025-12-26', 1.00, '', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 18:01:41', '2025-12-29 18:01:41'),
(272, 4, 4, '2025-12-29', 1.00, '', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 18:01:49', '2025-12-29 18:01:49'),
(273, 3, 12, '2025-12-20', 2.50, 'Movilización de puerto Gaitan a Caño Sur', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 18:11:21', '2025-12-29 18:11:21'),
(274, 3, 12, '2025-12-20', 8.00, 'Inspección visual de las Botas de Gas, movilización de Caño Sur a Villavicencio \r\nHora de llegada a Villavicencio 11:00 pm', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 18:11:28', '2025-12-29 18:11:28'),
(275, 3, 4, '2025-12-26', 1.00, 'Revisión de materiales con Inspector ASME', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 18:12:11', '2025-12-29 18:12:11'),
(276, 7, 10, '2025-12-26', 5.00, 'Revisión de Trabajos elaborados en la sección de rectificación pendientes por entregar y realización de gestión con clientes para que pasen a recoger los trabajos.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 18:26:56', '2025-12-29 18:26:56'),
(277, 7, 9, '2025-12-26', 3.00, 'Lectura y revisión  del resumen ejecutivo de análisis de documentos de contratación para fabricación de equipos industriales bajo normatividad Asme.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 18:26:56', '2025-12-29 18:26:56'),
(278, 7, 9, '2025-12-27', 5.00, 'Gestionar con los Directivos de la empresa P.S.I.G la viabilidad de la puesta de la Orden de compra para el diagnostico y revisión de espesores de los equipos que se cotizaron.', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 18:31:14', '2025-12-29 18:31:14'),
(279, 3, 12, '2025-12-20', 3.00, 'Movilización a Villavicencio', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 18:55:16', '2025-12-29 18:55:16'),
(280, 3, 12, '2025-12-21', 6.00, 'Movilización de Villavicencio a Barrancabermeja y descargue de guayas y diferenciales en el taller', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 18:55:19', '2025-12-29 18:55:19'),
(281, 3, 12, '2025-12-21', 8.00, 'Movilización de Villavicencio a Barrancabermeja ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 18:55:24', '2025-12-29 18:55:24'),
(282, 2, 4, '2025-12-12', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 19:40:12', '2025-12-29 19:40:12'),
(283, 2, 3, '2025-12-12', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 19:40:12', '2025-12-29 19:40:12'),
(284, 2, 6, '2025-12-12', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 19:40:12', '2025-12-29 19:40:12'),
(285, 2, 5, '2025-12-12', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 19:40:12', '2025-12-29 19:40:12'),
(286, 2, 4, '2025-12-15', 3.50, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 19:41:31', '2025-12-29 19:41:31'),
(287, 2, 8, '2025-12-15', 3.50, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 19:41:31', '2025-12-29 19:41:31'),
(288, 2, 4, '2025-12-16', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 19:42:09', '2025-12-29 19:42:09'),
(289, 2, 3, '2025-12-16', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 19:42:09', '2025-12-29 19:42:09'),
(290, 2, 6, '2025-12-16', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 19:42:09', '2025-12-29 19:42:09'),
(291, 2, 5, '2025-12-16', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 19:42:09', '2025-12-29 19:42:09'),
(292, 2, 3, '2025-12-17', 1.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 19:42:34', '2025-12-29 19:42:34'),
(293, 2, 6, '2025-12-17', 1.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 19:42:34', '2025-12-29 19:42:34'),
(294, 2, 5, '2025-12-17', 1.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 19:42:34', '2025-12-29 19:42:34'),
(295, 2, 4, '2025-12-18', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 19:54:32', '2025-12-29 19:54:32'),
(296, 2, 3, '2025-12-18', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 19:54:32', '2025-12-29 19:54:32'),
(297, 2, 6, '2025-12-18', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 19:54:32', '2025-12-29 19:54:32'),
(298, 2, 5, '2025-12-18', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 19:54:32', '2025-12-29 19:54:32'),
(299, 6, 6, '2025-12-29', 7.00, 'Ensamble y ajuste de 3d aero enfriadores  ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 19:58:27', '2025-12-29 19:58:27'),
(300, 2, 4, '2025-12-19', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 19:58:58', '2025-12-29 19:58:58'),
(301, 2, 3, '2025-12-19', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 19:58:58', '2025-12-29 19:58:58'),
(302, 2, 6, '2025-12-19', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 19:58:58', '2025-12-29 19:58:58'),
(303, 2, 5, '2025-12-19', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 19:58:58', '2025-12-29 19:58:58'),
(304, 2, 8, '2025-12-20', 2.50, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 19:59:40', '2025-12-29 19:59:40'),
(305, 2, 11, '2025-12-20', 2.50, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 19:59:40', '2025-12-29 19:59:40'),
(306, 2, 4, '2025-12-22', 1.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 20:00:21', '2025-12-29 20:00:21'),
(307, 2, 3, '2025-12-22', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 20:00:21', '2025-12-29 20:00:21'),
(308, 2, 6, '2025-12-22', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 20:00:21', '2025-12-29 20:00:21'),
(309, 2, 5, '2025-12-22', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 20:00:21', '2025-12-29 20:00:21'),
(310, 2, 4, '2025-12-23', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 20:00:50', '2025-12-29 20:00:50'),
(311, 2, 3, '2025-12-23', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 20:00:50', '2025-12-29 20:00:50'),
(312, 2, 6, '2025-12-23', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 20:00:50', '2025-12-29 20:00:50'),
(313, 2, 5, '2025-12-23', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 20:00:50', '2025-12-29 20:00:50'),
(314, 2, 11, '2025-12-24', 2.50, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 20:02:17', '2025-12-29 20:02:17'),
(315, 2, 8, '2025-12-24', 2.50, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 20:02:17', '2025-12-29 20:02:17'),
(316, 2, 4, '2025-12-26', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 20:03:14', '2025-12-29 20:03:14'),
(317, 2, 3, '2025-12-26', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 20:03:14', '2025-12-29 20:03:14'),
(318, 2, 6, '2025-12-26', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 20:03:14', '2025-12-29 20:03:14'),
(319, 2, 5, '2025-12-26', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 20:03:14', '2025-12-29 20:03:14'),
(320, 2, 4, '2025-12-29', 1.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 20:03:56', '2025-12-29 20:03:56'),
(321, 2, 3, '2025-12-29', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 20:03:56', '2025-12-29 20:03:56'),
(322, 2, 6, '2025-12-29', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 20:03:56', '2025-12-29 20:03:56'),
(323, 2, 5, '2025-12-29', 2.00, 'Gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-29 20:03:56', '2025-12-29 20:03:56'),
(324, 5, 10, '2025-12-29', 0.50, 'Revisar respuestas MTZ - Reentube de Aeroenfriadores', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-30 13:18:46', '2025-12-30 13:18:46'),
(325, 5, 10, '2025-12-29', 1.00, 'Redactar correo para enviar oferta \"Reentube Aeroenfriadores\" MTZ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-30 13:18:46', '2025-12-30 13:18:46'),
(326, 5, 10, '2025-12-29', 1.00, 'Enviar hojas de datos de instrumentación a Maria E. para el proceso Botas de Gas CASABE', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-30 13:18:46', '2025-12-30 13:18:46'),
(327, 5, 9, '2025-12-30', 0.50, 'Enviar cotización de Filtros - Instrucont', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-30 20:39:49', '2025-12-30 20:39:49'),
(328, 5, 10, '2025-12-30', 0.50, 'Crear cliente (MTZ LTDA) en el SCP ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-30 20:39:49', '2025-12-30 20:39:49'),
(329, 5, 10, '2025-12-30', 1.50, 'Crear cotización a nombre de MTZ en el SCP', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-30 20:39:49', '2025-12-30 20:39:49'),
(330, 5, 9, '2025-12-30', 0.50, 'Enviar cotización MTZ LTDA', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-30 20:39:49', '2025-12-30 20:39:49'),
(331, 5, 10, '2025-12-30', 2.00, 'Oficio respuesta a solicitud de aclaraciones E-002, proceso bajo la Unión Temporal XEC THERMAL. ', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-30 20:39:49', '2025-12-30 20:39:49'),
(332, 5, 10, '2025-12-30', 0.50, 'Envío de oficio de respuesta a solicitud de aclaración E-002 (Unión Temporal XEC THERMAL)', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-30 20:39:49', '2025-12-30 20:39:49'),
(333, 5, 10, '2025-12-30', 1.00, 'Registrar negocios y cotizaciones en el CRM', NULL, 'registrado', 0, NULL, NULL, NULL, '2025-12-30 20:39:49', '2025-12-30 20:39:49'),
(334, 5, 10, '2026-01-05', 0.50, 'Llamada telefónica con Diego de Montajes Tecnicos, conversar forma de pago de la cotización #7118', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-05 21:09:51', '2026-01-05 21:09:51'),
(335, 5, 10, '2026-01-05', 0.50, 'Correo de seguimiento a respuestas de observaciones de proceso \"COMPRA DE KODRUM PARA PROYECTO MANEJO DE VENTEOS MITO 1 RES 40066, PERTENECIENTE A LA GERENCIA DE OPERACIONES DE DESARROLLO Y PRODUCCIÓN ORIENTE - GOR\"', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-05 21:09:51', '2026-01-05 21:09:51'),
(336, 5, 10, '2026-01-05', 0.50, 'Ajustar cotización MONTAJES TECNICOS - Forma de pago', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-05 21:09:51', '2026-01-05 21:09:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resumen_diario_horas`
--

CREATE TABLE `resumen_diario_horas` (
  `id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `fecha` date NOT NULL,
  `horas_normales` decimal(4,2) DEFAULT '0.00',
  `horas_extras` decimal(4,2) DEFAULT '0.00',
  `total_horas` decimal(4,2) DEFAULT '0.00',
  `numero_registros` int DEFAULT '0',
  `ordenes_trabajadas` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sincronizacion_projectdashboard`
--

CREATE TABLE `sincronizacion_projectdashboard` (
  `id` int NOT NULL,
  `tipo_registro` enum('horas_normales','horas_extras') COLLATE utf8mb4_unicode_ci NOT NULL,
  `registro_id` int NOT NULL COMMENT 'ID del registro en registros_horas o solicitudes_horas_extras',
  `usuario_id` int NOT NULL,
  `orden_produccion_id` int NOT NULL,
  `fecha_registro` date NOT NULL,
  `horas_ordinarias` decimal(4,2) DEFAULT '0.00',
  `horas_extras` decimal(4,2) DEFAULT '0.00',
  `total_pagado` decimal(10,2) DEFAULT '0.00',
  `fecha_sincronizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sincronizado_por` int NOT NULL,
  `respuesta_api` text COLLATE utf8mb4_unicode_ci COMMENT 'Respuesta del sistema externo si aplica'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sincronizacion_projectdashboard`
--

INSERT INTO `sincronizacion_projectdashboard` (`id`, `tipo_registro`, `registro_id`, `usuario_id`, `orden_produccion_id`, `fecha_registro`, `horas_ordinarias`, `horas_extras`, `total_pagado`, `fecha_sincronizacion`, `sincronizado_por`, `respuesta_api`) VALUES
(1, 'horas_normales', 299, 6, 6, '2025-12-29', 7.00, 0.00, 52500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(2, 'horas_normales', 272, 4, 4, '2025-12-29', 1.00, 0.00, 7500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(3, 'horas_extras', 12, 4, 4, '2025-12-29', 0.00, 1.00, 9375.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(4, 'horas_normales', 320, 2, 4, '2025-12-29', 1.00, 0.00, 7500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(5, 'horas_normales', 321, 2, 3, '2025-12-29', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(6, 'horas_normales', 322, 2, 6, '2025-12-29', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(7, 'horas_normales', 323, 2, 5, '2025-12-29', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(8, 'horas_normales', 206, 6, 4, '2025-12-27', 5.00, 0.00, 37500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(9, 'horas_normales', 267, 4, 4, '2025-12-27', 5.00, 0.00, 37500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(10, 'horas_normales', 268, 3, 4, '2025-12-27', 5.00, 0.00, 37500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(11, 'horas_normales', 205, 6, 4, '2025-12-26', 8.00, 0.00, 60000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(12, 'horas_normales', 266, 4, 4, '2025-12-26', 8.00, 0.00, 60000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(13, 'horas_normales', 271, 4, 4, '2025-12-26', 1.00, 0.00, 7500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(14, 'horas_extras', 11, 4, 4, '2025-12-26', 0.00, 1.00, 9375.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(15, 'horas_normales', 194, 3, 3, '2025-12-26', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(16, 'horas_normales', 195, 3, 4, '2025-12-26', 6.00, 0.00, 45000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(17, 'horas_normales', 275, 3, 4, '2025-12-26', 1.00, 0.00, 7500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(18, 'horas_extras', 5, 3, 4, '2025-12-26', 0.00, 1.00, 9375.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(19, 'horas_normales', 316, 2, 4, '2025-12-26', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(20, 'horas_normales', 317, 2, 3, '2025-12-26', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(21, 'horas_normales', 318, 2, 6, '2025-12-26', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(22, 'horas_normales', 319, 2, 5, '2025-12-26', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(23, 'horas_normales', 204, 6, 5, '2025-12-24', 4.00, 0.00, 30000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(24, 'horas_normales', 265, 4, 4, '2025-12-24', 5.00, 0.00, 37500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(25, 'horas_normales', 314, 2, 11, '2025-12-24', 2.50, 0.00, 18750.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(26, 'horas_normales', 315, 2, 8, '2025-12-24', 2.50, 0.00, 18750.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(27, 'horas_normales', 203, 6, 11, '2025-12-23', 8.00, 0.00, 60000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(28, 'horas_normales', 264, 4, 4, '2025-12-23', 8.00, 0.00, 60000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(29, 'horas_normales', 247, 5, 5, '2025-12-23', 0.50, 0.00, 3750.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(30, 'horas_normales', 246, 5, 3, '2025-12-23', 0.50, 0.00, 3750.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(31, 'horas_normales', 251, 5, 2, '2025-12-23', 1.00, 0.00, 7500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(32, 'horas_normales', 310, 2, 4, '2025-12-23', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(33, 'horas_normales', 311, 2, 3, '2025-12-23', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(34, 'horas_normales', 312, 2, 6, '2025-12-23', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(35, 'horas_normales', 313, 2, 5, '2025-12-23', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(36, 'horas_normales', 202, 6, 8, '2025-12-22', 7.00, 0.00, 52500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(37, 'horas_normales', 244, 5, 2, '2025-12-22', 0.50, 0.00, 3750.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(38, 'horas_normales', 306, 2, 4, '2025-12-22', 1.00, 0.00, 7500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(39, 'horas_normales', 307, 2, 3, '2025-12-22', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(40, 'horas_normales', 308, 2, 6, '2025-12-22', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(41, 'horas_normales', 309, 2, 5, '2025-12-22', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(42, 'horas_normales', 280, 3, 12, '2025-12-21', 6.00, 0.00, 45000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(43, 'horas_normales', 281, 3, 12, '2025-12-21', 8.00, 0.00, 60000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(44, 'horas_extras', 9, 3, 12, '2025-12-21', 0.00, 8.00, 75000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(45, 'horas_extras', 10, 3, 12, '2025-12-21', 0.00, 6.00, 56250.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(46, 'horas_normales', 201, 6, 11, '2025-12-20', 1.00, 0.00, 7500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(47, 'horas_normales', 200, 6, 8, '2025-12-20', 4.00, 0.00, 30000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(48, 'horas_normales', 262, 4, 4, '2025-12-20', 1.00, 0.00, 7500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(49, 'horas_normales', 238, 3, 12, '2025-12-20', 5.00, 0.00, 37500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(50, 'horas_normales', 273, 3, 12, '2025-12-20', 2.50, 0.00, 18750.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(51, 'horas_normales', 274, 3, 12, '2025-12-20', 8.00, 0.00, 60000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(52, 'horas_normales', 279, 3, 12, '2025-12-20', 3.00, 0.00, 22500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(53, 'horas_extras', 6, 3, 12, '2025-12-20', 0.00, 2.50, 32812.50, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(54, 'horas_extras', 7, 3, 12, '2025-12-20', 0.00, 8.00, 75000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(55, 'horas_extras', 8, 3, 12, '2025-12-20', 0.00, 3.00, 39375.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(56, 'horas_normales', 304, 2, 8, '2025-12-20', 2.50, 0.00, 18750.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(57, 'horas_normales', 305, 2, 11, '2025-12-20', 2.50, 0.00, 18750.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(58, 'horas_normales', 199, 6, 8, '2025-12-19', 8.00, 0.00, 60000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(59, 'horas_normales', 237, 3, 12, '2025-12-19', 8.00, 0.00, 60000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(60, 'horas_normales', 190, 5, 2, '2025-12-19', 0.50, 0.00, 3750.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(61, 'horas_normales', 191, 5, 4, '2025-12-19', 0.50, 0.00, 3750.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(62, 'horas_normales', 300, 2, 4, '2025-12-19', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(63, 'horas_normales', 301, 2, 3, '2025-12-19', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(64, 'horas_normales', 302, 2, 6, '2025-12-19', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(65, 'horas_normales', 303, 2, 5, '2025-12-19', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(66, 'horas_normales', 198, 6, 11, '2025-12-18', 8.00, 0.00, 60000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(67, 'horas_normales', 222, 3, 7, '2025-12-18', 8.00, 0.00, 60000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(68, 'horas_normales', 295, 2, 4, '2025-12-18', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(69, 'horas_normales', 296, 2, 3, '2025-12-18', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(70, 'horas_normales', 297, 2, 6, '2025-12-18', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(71, 'horas_normales', 298, 2, 5, '2025-12-18', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(72, 'horas_normales', 197, 6, 8, '2025-12-17', 8.00, 0.00, 60000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(73, 'horas_normales', 258, 4, 8, '2025-12-17', 8.00, 0.00, 60000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(74, 'horas_normales', 220, 3, 7, '2025-12-17', 8.00, 0.00, 60000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(75, 'horas_normales', 171, 2, 8, '2025-12-17', 5.00, 0.00, 37500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(76, 'horas_normales', 292, 2, 3, '2025-12-17', 1.00, 0.00, 7500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(77, 'horas_normales', 293, 2, 6, '2025-12-17', 1.00, 0.00, 7500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(78, 'horas_normales', 294, 2, 5, '2025-12-17', 1.00, 0.00, 7500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(79, 'horas_normales', 196, 6, 11, '2025-12-16', 8.00, 0.00, 60000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(80, 'horas_normales', 215, 3, 7, '2025-12-16', 8.00, 0.00, 60000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(81, 'horas_normales', 288, 2, 4, '2025-12-16', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(82, 'horas_normales', 289, 2, 3, '2025-12-16', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(83, 'horas_normales', 290, 2, 6, '2025-12-16', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(84, 'horas_normales', 291, 2, 5, '2025-12-16', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(85, 'horas_normales', 168, 6, 11, '2025-12-15', 7.00, 0.00, 52500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(86, 'horas_normales', 170, 6, 11, '2025-12-15', 1.50, 0.00, 11250.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(87, 'horas_extras', 4, 6, 11, '2025-12-15', 0.00, 1.50, 14062.50, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(88, 'horas_normales', 256, 4, 11, '2025-12-15', 7.00, 0.00, 52500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(89, 'horas_normales', 214, 3, 7, '2025-12-15', 7.00, 0.00, 52500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(90, 'horas_normales', 286, 2, 4, '2025-12-15', 3.50, 0.00, 26250.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(91, 'horas_normales', 287, 2, 8, '2025-12-15', 3.50, 0.00, 26250.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(92, 'horas_normales', 167, 6, 8, '2025-12-13', 5.00, 0.00, 37500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(93, 'horas_normales', 169, 6, 11, '2025-12-13', 1.00, 0.00, 7500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(94, 'horas_extras', 3, 6, 11, '2025-12-13', 0.00, 1.00, 9375.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(95, 'horas_normales', 166, 6, 8, '2025-12-12', 8.00, 0.00, 60000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(96, 'horas_normales', 282, 2, 4, '2025-12-12', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(97, 'horas_normales', 283, 2, 3, '2025-12-12', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(98, 'horas_normales', 284, 2, 6, '2025-12-12', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(99, 'horas_normales', 285, 2, 5, '2025-12-12', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(100, 'horas_normales', 165, 6, 8, '2025-12-11', 8.00, 0.00, 60000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(101, 'horas_normales', 163, 5, 11, '2025-12-11', 0.50, 0.00, 3750.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(102, 'horas_normales', 172, 2, 11, '2025-12-11', 4.00, 0.00, 30000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(103, 'horas_normales', 173, 2, 8, '2025-12-11', 4.00, 0.00, 30000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(104, 'horas_normales', 164, 6, 8, '2025-12-10', 8.00, 0.00, 60000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(105, 'horas_normales', 137, 4, 8, '2025-12-10', 6.00, 0.00, 45000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(106, 'horas_normales', 138, 4, 4, '2025-12-10', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(107, 'horas_normales', 123, 2, 3, '2025-12-10', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(108, 'horas_normales', 124, 2, 5, '2025-12-10', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(109, 'horas_normales', 125, 2, 6, '2025-12-10', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(110, 'horas_normales', 126, 2, 8, '2025-12-10', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(111, 'horas_normales', 113, 6, 8, '2025-12-09', 8.00, 0.00, 60000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(112, 'horas_normales', 135, 4, 8, '2025-12-09', 7.00, 0.00, 52500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(113, 'horas_normales', 136, 4, 4, '2025-12-09', 1.00, 0.00, 7500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(114, 'horas_normales', 114, 3, 8, '2025-12-09', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(115, 'horas_normales', 115, 3, 2, '2025-12-09', 4.00, 0.00, 30000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(116, 'horas_normales', 116, 3, 4, '2025-12-09', 1.00, 0.00, 7500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(117, 'horas_normales', 117, 3, 3, '2025-12-09', 1.00, 0.00, 7500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(118, 'horas_normales', 154, 5, 2, '2025-12-09', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(119, 'horas_normales', 127, 2, 3, '2025-12-09', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(120, 'horas_normales', 128, 2, 5, '2025-12-09', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(121, 'horas_normales', 129, 2, 6, '2025-12-09', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(122, 'horas_normales', 130, 2, 8, '2025-12-09', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(123, 'horas_normales', 112, 6, 8, '2025-12-06', 5.00, 0.00, 37500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(124, 'horas_normales', 134, 4, 8, '2025-12-06', 5.00, 0.00, 37500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(125, 'horas_normales', 139, 4, 8, '2025-12-06', 0.67, 0.00, 5025.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(126, 'horas_extras', 2, 4, 8, '2025-12-06', 0.00, 0.67, 6281.25, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(127, 'horas_normales', 108, 3, 2, '2025-12-06', 4.00, 0.00, 30000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(128, 'horas_normales', 109, 3, 8, '2025-12-06', 1.00, 0.00, 7500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(129, 'horas_normales', 147, 5, 2, '2025-12-06', 0.50, 0.00, 3750.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(130, 'horas_normales', 148, 5, 2, '2025-12-06', 0.50, 0.00, 3750.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(131, 'horas_normales', 121, 2, 2, '2025-12-06', 3.00, 0.00, 22500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(132, 'horas_normales', 122, 2, 8, '2025-12-06', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(133, 'horas_normales', 111, 6, 8, '2025-12-05', 8.00, 0.00, 60000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(134, 'horas_normales', 131, 4, 8, '2025-12-05', 5.00, 0.00, 37500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(135, 'horas_normales', 132, 4, 4, '2025-12-05', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(136, 'horas_normales', 133, 4, 2, '2025-12-05', 1.00, 0.00, 7500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(137, 'horas_normales', 106, 3, 8, '2025-12-05', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(138, 'horas_normales', 107, 3, 2, '2025-12-05', 6.00, 0.00, 45000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(139, 'horas_normales', 89, 5, 1, '2025-12-05', 1.00, 0.00, 7500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(140, 'horas_normales', 92, 5, 2, '2025-12-05', 1.50, 0.00, 11250.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(141, 'horas_normales', 93, 5, 2, '2025-12-05', 0.50, 0.00, 3750.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(142, 'horas_normales', 96, 5, 2, '2025-12-05', 0.50, 0.00, 3750.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(143, 'horas_normales', 118, 2, 2, '2025-12-05', 3.00, 0.00, 22500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(144, 'horas_normales', 119, 2, 1, '2025-12-05', 3.00, 0.00, 22500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(145, 'horas_normales', 120, 2, 8, '2025-12-05', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(146, 'horas_normales', 110, 6, 8, '2025-12-04', 8.00, 0.00, 60000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(147, 'horas_normales', 69, 4, 8, '2025-12-04', 8.00, 0.00, 60000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(148, 'horas_normales', 57, 3, 8, '2025-12-04', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(149, 'horas_normales', 58, 3, 2, '2025-12-04', 6.00, 0.00, 45000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(150, 'horas_normales', 85, 5, 2, '2025-12-04', 1.50, 0.00, 11250.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(151, 'horas_normales', 86, 5, 2, '2025-12-04', 0.50, 0.00, 3750.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(152, 'horas_normales', 88, 5, 2, '2025-12-04', 1.00, 0.00, 7500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(153, 'horas_normales', 63, 2, 1, '2025-12-04', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(154, 'horas_normales', 64, 2, 2, '2025-12-04', 4.00, 0.00, 30000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(155, 'horas_normales', 65, 2, 8, '2025-12-04', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(156, 'horas_normales', 51, 6, 8, '2025-12-03', 8.00, 0.00, 60000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(157, 'horas_normales', 66, 7, 2, '2025-12-03', 3.00, 0.00, 32499.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(158, 'horas_normales', 22, 4, 8, '2025-12-03', 0.67, 0.00, 5025.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(159, 'horas_normales', 54, 4, 8, '2025-12-03', 2.50, 0.00, 18750.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(160, 'horas_extras', 1, 4, 8, '2025-12-03', 0.00, 0.67, 6281.25, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(161, 'horas_normales', 52, 4, 2, '2025-12-03', 4.00, 0.00, 30000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(162, 'horas_normales', 53, 4, 4, '2025-12-03', 0.50, 0.00, 3750.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(163, 'horas_normales', 55, 3, 8, '2025-12-03', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(164, 'horas_normales', 56, 3, 2, '2025-12-03', 6.00, 0.00, 45000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(165, 'horas_normales', 143, 5, 2, '2025-12-03', 1.00, 0.00, 7500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(166, 'horas_normales', 59, 2, 8, '2025-12-03', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(167, 'horas_normales', 60, 2, 2, '2025-12-03', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(168, 'horas_normales', 61, 2, 1, '2025-12-03', 3.00, 0.00, 22500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(169, 'horas_normales', 62, 2, 1, '2025-12-03', 1.00, 0.00, 7500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(170, 'horas_normales', 5, 6, 8, '2025-12-02', 8.00, 0.00, 60000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(171, 'horas_normales', 7, 4, 8, '2025-12-02', 6.00, 0.00, 45000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(172, 'horas_normales', 8, 4, 2, '2025-12-02', 1.00, 0.00, 7500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(173, 'horas_normales', 9, 4, 4, '2025-12-02', 1.00, 0.00, 7500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(174, 'horas_normales', 10, 3, 8, '2025-12-02', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(175, 'horas_normales', 11, 3, 3, '2025-12-02', 1.50, 0.00, 11250.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(176, 'horas_normales', 12, 3, 4, '2025-12-02', 1.00, 0.00, 7500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(177, 'horas_normales', 13, 3, 2, '2025-12-02', 3.50, 0.00, 26250.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(178, 'horas_normales', 97, 5, 2, '2025-12-02', 2.00, 0.00, 15000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(179, 'horas_normales', 98, 5, 2, '2025-12-02', 0.50, 0.00, 3750.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(180, 'horas_normales', 102, 5, 2, '2025-12-02', 1.00, 0.00, 7500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(181, 'horas_normales', 16, 2, 2, '2025-12-02', 1.00, 0.00, 7500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(182, 'horas_normales', 21, 2, 2, '2025-12-02', 4.50, 0.00, 33750.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(183, 'horas_normales', 17, 2, 1, '2025-12-02', 1.00, 0.00, 7500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(184, 'horas_normales', 18, 2, 3, '2025-12-02', 0.50, 0.00, 3750.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(185, 'horas_normales', 19, 2, 5, '2025-12-02', 0.50, 0.00, 3750.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(186, 'horas_normales', 20, 2, 6, '2025-12-02', 0.50, 0.00, 3750.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(187, 'horas_normales', 4, 6, 8, '2025-12-01', 7.00, 0.00, 52500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(188, 'horas_normales', 1, 4, 2, '2025-12-01', 4.00, 0.00, 30000.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(189, 'horas_normales', 2, 4, 4, '2025-12-01', 3.00, 0.00, 22500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(190, 'horas_normales', 3, 3, 2, '2025-12-01', 7.00, 0.00, 52500.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(191, 'horas_normales', 14, 2, 2, '2025-12-01', 3.50, 0.00, 26250.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(192, 'horas_normales', 15, 2, 1, '2025-12-01', 3.50, 0.00, 26250.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_horas_extras`
--

CREATE TABLE `solicitudes_horas_extras` (
  `id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `orden_produccion_id` int NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL COMMENT 'Hora de inicio de las horas extras',
  `hora_fin` time NOT NULL COMMENT 'Hora de finalización de las horas extras',
  `total_horas_extras` decimal(4,2) NOT NULL COMMENT 'Total de horas extras calculadas',
  `descripcion_trabajo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('pendiente','aprobada','rechazada','cancelada') COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente',
  `aprobado_por` int DEFAULT NULL,
  `fecha_solicitud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_respuesta` timestamp NULL DEFAULT NULL,
  `comentario_aprobacion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `solicitudes_horas_extras`
--

INSERT INTO `solicitudes_horas_extras` (`id`, `usuario_id`, `orden_produccion_id`, `fecha`, `hora_inicio`, `hora_fin`, `total_horas_extras`, `descripcion_trabajo`, `estado`, `aprobado_por`, `fecha_solicitud`, `fecha_respuesta`, `comentario_aprobacion`, `created_at`, `updated_at`) VALUES
(1, 4, 8, '2025-12-03', '17:00:00', '17:40:00', 0.67, 'Elaboración de informe y envío ', 'aprobada', 2, '2025-12-03 13:11:09', '2025-12-03 13:36:22', 'Se aprueba con la indicación de que los próximos informes de avance deben realizarse dentro del horario laboral ', '2025-12-03 13:11:09', '2025-12-03 13:36:22'),
(2, 4, 8, '2025-12-06', '12:00:00', '12:40:00', 0.67, 'Medición y levantamiento de druns suministrados para corte y aplicación de soldadura ', 'aprobada', 2, '2025-12-11 15:01:03', '2025-12-11 18:00:38', '', '2025-12-11 15:01:03', '2025-12-11 18:00:38'),
(3, 6, 11, '2025-12-13', '12:00:00', '13:00:00', 1.00, 'tubos benturi', 'aprobada', 2, '2025-12-16 14:55:05', '2025-12-17 21:52:44', 'Se aprueba con la observación de que en la hora se trabajó a los tubos venturi que no tienen OP', '2025-12-16 14:55:05', '2025-12-17 21:52:44'),
(4, 6, 11, '2025-12-15', '16:00:00', '17:30:00', 1.50, 'tintas y  mapeo', 'aprobada', 2, '2025-12-16 14:56:56', '2025-12-17 21:52:49', '', '2025-12-16 14:56:56', '2025-12-17 21:52:49'),
(5, 3, 4, '2025-12-26', '17:00:00', '18:00:00', 1.00, 'Revisión de materiales con Inspector ASME', 'aprobada', 2, '2025-12-27 13:58:08', '2025-12-29 18:12:11', '', '2025-12-27 13:58:08', '2025-12-29 18:12:11'),
(6, 3, 12, '2025-12-20', '04:30:00', '07:00:00', 2.50, 'Movilización de puerto Gaitan a Caño Sur', 'aprobada', 2, '2025-12-27 15:11:29', '2025-12-29 18:11:21', '', '2025-12-27 15:11:29', '2025-12-29 18:11:21'),
(7, 3, 12, '2025-12-20', '12:00:00', '20:00:00', 8.00, 'Inspección visual de las Botas de Gas, movilización de Caño Sur a Villavicencio \r\nHora de llegada a Villavicencio 11:00 pm', 'aprobada', 2, '2025-12-27 15:43:33', '2025-12-29 18:11:28', '', '2025-12-27 15:43:33', '2025-12-29 18:11:28'),
(8, 3, 12, '2025-12-20', '20:00:00', '23:00:00', 3.00, 'Movilización a Villavicencio', 'aprobada', 2, '2025-12-27 15:45:10', '2025-12-29 18:55:16', '', '2025-12-27 15:45:10', '2025-12-29 18:55:16'),
(9, 3, 12, '2025-12-21', '07:00:00', '15:00:00', 8.00, 'Movilización de Villavicencio a Barrancabermeja ', 'aprobada', 2, '2025-12-27 15:46:37', '2025-12-29 18:55:24', '', '2025-12-27 15:46:37', '2025-12-29 18:55:24'),
(10, 3, 12, '2025-12-21', '15:00:00', '21:00:00', 6.00, 'Movilización de Villavicencio a Barrancabermeja y descargue de guayas y diferenciales en el taller', 'aprobada', 2, '2025-12-27 15:47:54', '2025-12-29 18:55:19', '', '2025-12-27 15:47:54', '2025-12-29 18:55:19'),
(11, 4, 4, '2025-12-26', '11:00:00', '12:00:00', 1.00, '', 'aprobada', 2, '2025-12-29 14:35:39', '2025-12-29 18:01:41', '', '2025-12-29 14:35:39', '2025-12-29 18:01:41'),
(12, 4, 4, '2025-12-29', '17:00:00', '18:00:00', 1.00, '', 'aprobada', 2, '2025-12-29 14:36:12', '2025-12-29 18:01:49', '', '2025-12-29 14:36:12', '2025-12-29 18:01:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `nombre_completo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` enum('trabajador','administrador') COLLATE utf8mb4_unicode_ci DEFAULT 'trabajador',
  `is_active` tinyint(1) DEFAULT '1',
  `fecha_ingreso` date DEFAULT NULL,
  `departamento_id` int DEFAULT NULL,
  `cargo_id` int DEFAULT NULL COMMENT 'ID del cargo del usuario',
  `valor_hora_base` decimal(10,2) DEFAULT '0.00' COMMENT 'Valor base por hora del empleado',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre_completo`, `username`, `email`, `password`, `rol`, `is_active`, `fecha_ingreso`, `departamento_id`, `cargo_id`, `valor_hora_base`, `created_at`, `updated_at`) VALUES
(1, 'Inyira Arciniegas', 'inyira', 'recursoshumanos@talleresunidosltda.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador', 1, '2025-11-29', NULL, NULL, 0.00, '2025-11-29 15:31:39', '2025-11-29 15:31:39'),
(2, 'Valeria Jaraba Bonilla', 'Valeria', 'ingenieria@talleresunidosltda.com', '$2y$10$lhZCaIp2SILBgPhkRzQBTehPSS0yWbR1GPXnL5N6irDioevAX5ePK', 'administrador', 1, '2025-11-29', 1, 7, 7500.00, '2025-11-29 15:31:39', '2025-12-19 15:19:13'),
(3, 'Jhonatan Sampayo', 'QCC', 'ingenieria2@talleresunidosltda.com', '$2y$10$DNo2IFH7Q4ze4mCH5Kim/.f2JEWjlR1ds4GkQbyIyM4QpEoPEIUeO', 'trabajador', 1, NULL, 2, 3, 7500.00, '2025-11-29 16:48:04', '2025-12-19 15:15:19'),
(4, 'Jesús Suaréz', 'jsuarez', 'jesussuarez071125@gmail.com', '$2y$10$pBVgGyH5PCpxfkqF6n7OJ.7om12PLCgK2tiXAYOwE/H1PmUP9oCre', 'trabajador', 1, NULL, 2, 3, 7500.00, '2025-11-29 16:51:58', '2025-12-19 15:14:59'),
(5, 'Jinella Plata', 'jplata', 'comercial2@talleresunidosltda.com', '$2y$10$747IMs5Yf5RDUzN1I9CeGeBw1VpVP36tMtqyavzOgNxxhd54PxA8W', 'trabajador', 1, NULL, 4, 2, 7500.00, '2025-12-01 15:18:52', '2025-12-19 15:16:54'),
(6, 'Dilan Botello', 'dbotello', 'diedubora0316@gmail.com', '$2y$10$8c3qbaxAwtnfeFK2TIzsx.lWIfFeRANP/ra5761HEI2eXANurMfSa', 'trabajador', 1, NULL, 1, 1, 7500.00, '2025-12-01 20:58:59', '2025-12-19 15:12:04'),
(7, 'Enaldo Jaraba', 'ejaraba', 'ventas@talleresunidosltda.com', '$2y$10$Thrp3A.Tqc4aGdE3i/TiC.hwd5JRfPlJOzbB9SatEpWnMAvrjueem', 'trabajador', 1, NULL, 3, NULL, 10833.00, '2025-12-02 14:00:35', '2025-12-15 15:00:28'),
(8, 'Jonatan Cantillo', 'jcantillo', 'jcantillo6@udi.edu.co', '$2y$10$hTHoSgyfYXuT/6FsGsDD8ukjC/tz8ulkSD06iGBEs4kEINhJCmHpq', 'administrador', 1, NULL, 1, 11, 0.00, '2025-12-16 20:20:54', '2025-12-19 15:18:41');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_horas_extras_pendientes`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_horas_extras_pendientes` (
`id` int
,`fecha` date
,`trabajador` varchar(100)
,`departamento` varchar(100)
,`codigo_op` varchar(50)
,`nombre_producto` varchar(150)
,`hora_inicio` time
,`hora_fin` time
,`total_horas_extras` decimal(4,2)
,`descripcion_trabajo` text
,`fecha_solicitud` timestamp
,`dias_pendiente` int
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_horas_por_orden`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_horas_por_orden` (
`orden_id` int
,`codigo_op` varchar(50)
,`nombre_producto` varchar(150)
,`estado` enum('activa','en_proceso','completada','cancelada')
,`trabajadores_asignados` bigint
,`total_horas_invertidas` decimal(26,2)
,`numero_registros` bigint
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_resumen_mensual`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_resumen_mensual` (
`usuario_id` int
,`nombre_completo` varchar(100)
,`departamento` varchar(100)
,`anio` year
,`mes` int
,`dias_trabajados` bigint
,`total_horas_normales` decimal(26,2)
,`total_horas_extras` decimal(26,2)
,`total_horas` decimal(27,2)
);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cargos`
--
ALTER TABLE `cargos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_nombre` (`nombre`);

--
-- Indices de la tabla `configuracion_sistema`
--
ALTER TABLE `configuracion_sistema`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clave` (`clave`);

--
-- Indices de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `idx_nombre` (`nombre`),
  ADD KEY `idx_codigo` (`codigo`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `fk_departamento_responsable` (`responsable_id`);

--
-- Indices de la tabla `historial_cambios`
--
ALTER TABLE `historial_cambios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tipo_registro` (`tipo_registro`,`registro_id`),
  ADD KEY `idx_fecha` (`created_at`),
  ADD KEY `idx_usuario` (`usuario_id`);

--
-- Indices de la tabla `horarios_laborales`
--
ALTER TABLE `horarios_laborales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dia_semana` (`dia_semana`);

--
-- Indices de la tabla `ordenes_produccion`
--
ALTER TABLE `ordenes_produccion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_op` (`codigo_op`),
  ADD KEY `idx_codigo` (`codigo_op`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_fecha_inicio` (`fecha_inicio`),
  ADD KEY `idx_orden_estado_fecha` (`estado`,`fecha_inicio`);

--
-- Indices de la tabla `registros_horas`
--
ALTER TABLE `registros_horas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `validado_por` (`validado_por`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_fecha` (`fecha`),
  ADD KEY `idx_orden` (`orden_produccion_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_usuario_fecha` (`usuario_id`,`fecha`),
  ADD KEY `idx_registro_usuario_fecha_estado` (`usuario_id`,`fecha`,`estado`);

--
-- Indices de la tabla `resumen_diario_horas`
--
ALTER TABLE `resumen_diario_horas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_usuario_fecha` (`usuario_id`,`fecha`),
  ADD KEY `idx_fecha` (`fecha`),
  ADD KEY `idx_usuario_fecha` (`usuario_id`,`fecha`);

--
-- Indices de la tabla `sincronizacion_projectdashboard`
--
ALTER TABLE `sincronizacion_projectdashboard`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_sincronizacion` (`tipo_registro`,`registro_id`),
  ADD KEY `idx_tipo_registro` (`tipo_registro`,`registro_id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_fecha` (`fecha_registro`),
  ADD KEY `idx_sincronizacion` (`fecha_sincronizacion`),
  ADD KEY `orden_produccion_id` (`orden_produccion_id`),
  ADD KEY `sincronizado_por` (`sincronizado_por`);

--
-- Indices de la tabla `solicitudes_horas_extras`
--
ALTER TABLE `solicitudes_horas_extras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orden_produccion_id` (`orden_produccion_id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_fecha` (`fecha`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_aprobador` (`aprobado_por`),
  ADD KEY `idx_solicitud_estado_fecha` (`estado`,`fecha`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_rol` (`rol`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_departamento` (`departamento_id`),
  ADD KEY `fk_usuario_cargo` (`cargo_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cargos`
--
ALTER TABLE `cargos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `configuracion_sistema`
--
ALTER TABLE `configuracion_sistema`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `historial_cambios`
--
ALTER TABLE `historial_cambios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `horarios_laborales`
--
ALTER TABLE `horarios_laborales`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `ordenes_produccion`
--
ALTER TABLE `ordenes_produccion`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `registros_horas`
--
ALTER TABLE `registros_horas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=337;

--
-- AUTO_INCREMENT de la tabla `resumen_diario_horas`
--
ALTER TABLE `resumen_diario_horas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sincronizacion_projectdashboard`
--
ALTER TABLE `sincronizacion_projectdashboard`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=193;

--
-- AUTO_INCREMENT de la tabla `solicitudes_horas_extras`
--
ALTER TABLE `solicitudes_horas_extras`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_horas_extras_pendientes`
--
DROP TABLE IF EXISTS `vista_horas_extras_pendientes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_horas_extras_pendientes`  AS SELECT `he`.`id` AS `id`, `he`.`fecha` AS `fecha`, `u`.`nombre_completo` AS `trabajador`, `d`.`nombre` AS `departamento`, `op`.`codigo_op` AS `codigo_op`, `op`.`nombre_producto` AS `nombre_producto`, `he`.`hora_inicio` AS `hora_inicio`, `he`.`hora_fin` AS `hora_fin`, `he`.`total_horas_extras` AS `total_horas_extras`, `he`.`descripcion_trabajo` AS `descripcion_trabajo`, `he`.`fecha_solicitud` AS `fecha_solicitud`, (to_days(now()) - to_days(`he`.`fecha_solicitud`)) AS `dias_pendiente` FROM (((`solicitudes_horas_extras` `he` join `usuarios` `u` on((`he`.`usuario_id` = `u`.`id`))) left join `departamentos` `d` on((`u`.`departamento_id` = `d`.`id`))) join `ordenes_produccion` `op` on((`he`.`orden_produccion_id` = `op`.`id`))) WHERE (`he`.`estado` = 'pendiente') ORDER BY `he`.`fecha_solicitud` ASC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_horas_por_orden`
--
DROP TABLE IF EXISTS `vista_horas_por_orden`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_horas_por_orden`  AS SELECT `op`.`id` AS `orden_id`, `op`.`codigo_op` AS `codigo_op`, `op`.`nombre_producto` AS `nombre_producto`, `op`.`estado` AS `estado`, count(distinct `r`.`usuario_id`) AS `trabajadores_asignados`, sum(`r`.`horas_trabajadas`) AS `total_horas_invertidas`, count(`r`.`id`) AS `numero_registros` FROM (`ordenes_produccion` `op` left join `registros_horas` `r` on((`op`.`id` = `r`.`orden_produccion_id`))) GROUP BY `op`.`id` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_resumen_mensual`
--
DROP TABLE IF EXISTS `vista_resumen_mensual`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_resumen_mensual`  AS SELECT `u`.`id` AS `usuario_id`, `u`.`nombre_completo` AS `nombre_completo`, `d`.`nombre` AS `departamento`, year(`r`.`fecha`) AS `anio`, month(`r`.`fecha`) AS `mes`, count(distinct `r`.`fecha`) AS `dias_trabajados`, sum(`r`.`horas_trabajadas`) AS `total_horas_normales`, coalesce(sum(`he`.`total_horas_extras`),0) AS `total_horas_extras`, (sum(`r`.`horas_trabajadas`) + coalesce(sum(`he`.`total_horas_extras`),0)) AS `total_horas` FROM (((`usuarios` `u` left join `departamentos` `d` on((`u`.`departamento_id` = `d`.`id`))) left join `registros_horas` `r` on((`u`.`id` = `r`.`usuario_id`))) left join `solicitudes_horas_extras` `he` on(((`u`.`id` = `he`.`usuario_id`) and (`he`.`estado` = 'aprobada') and (year(`he`.`fecha`) = year(`r`.`fecha`)) and (month(`he`.`fecha`) = month(`r`.`fecha`))))) WHERE (`u`.`rol` = 'trabajador') GROUP BY `u`.`id`, year(`r`.`fecha`), month(`r`.`fecha`) ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `departamentos`
--
ALTER TABLE `departamentos`
  ADD CONSTRAINT `fk_departamento_responsable` FOREIGN KEY (`responsable_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `historial_cambios`
--
ALTER TABLE `historial_cambios`
  ADD CONSTRAINT `historial_cambios_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `registros_horas`
--
ALTER TABLE `registros_horas`
  ADD CONSTRAINT `registros_horas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `registros_horas_ibfk_2` FOREIGN KEY (`orden_produccion_id`) REFERENCES `ordenes_produccion` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `registros_horas_ibfk_3` FOREIGN KEY (`validado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `resumen_diario_horas`
--
ALTER TABLE `resumen_diario_horas`
  ADD CONSTRAINT `resumen_diario_horas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sincronizacion_projectdashboard`
--
ALTER TABLE `sincronizacion_projectdashboard`
  ADD CONSTRAINT `sincronizacion_projectdashboard_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sincronizacion_projectdashboard_ibfk_2` FOREIGN KEY (`orden_produccion_id`) REFERENCES `ordenes_produccion` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sincronizacion_projectdashboard_ibfk_3` FOREIGN KEY (`sincronizado_por`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `solicitudes_horas_extras`
--
ALTER TABLE `solicitudes_horas_extras`
  ADD CONSTRAINT `solicitudes_horas_extras_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `solicitudes_horas_extras_ibfk_2` FOREIGN KEY (`orden_produccion_id`) REFERENCES `ordenes_produccion` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `solicitudes_horas_extras_ibfk_3` FOREIGN KEY (`aprobado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_cargo` FOREIGN KEY (`cargo_id`) REFERENCES `cargos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
