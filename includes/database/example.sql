-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: systemautomatic.xyz
-- Tiempo de generación: 29-04-2026 a las 04:38:17
-- Versión del servidor: 11.8.6-MariaDB-ubu2404
-- Versión de PHP: 8.2.27

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
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1 COMMENT '0=inactivo, 1=activo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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
  `id` int(11) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `valor` text NOT NULL,
  `tipo` enum('texto','numero','decimal','booleano','json') DEFAULT 'texto',
  `descripcion` text DEFAULT NULL,
  `categoria` varchar(50) DEFAULT 'general',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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
(28, 'projectdashboard_sincronizacion_automatica', '0', 'booleano', 'Enviar automáticamente via webhook al aprobar', 'integraciones', '2025-12-13 05:01:44', '2025-12-13 05:01:44'),
(29, 'factor_extra_diurna', '1.25', 'decimal', 'Factor multiplicador para horas extras diurnas (HED: 1.25x)', 'costos', '2026-01-08 05:23:47', '2026-01-08 05:23:47'),
(30, 'factor_extra_nocturna', '1.35', 'decimal', 'Factor multiplicador para horas extras nocturnas (HEN: 1.35x)', 'costos', '2026-01-08 05:23:47', '2026-01-08 05:23:47'),
(31, 'factor_fin_semana_diurno', '2.1', 'decimal', 'Factor multiplicador para horas extras fin de semana diurnas (HEFD: 2.1x)', 'costos', '2026-01-08 05:23:47', '2026-01-08 05:23:47'),
(32, 'factor_fin_semana_nocturno', '2.5', 'decimal', 'Factor multiplicador para horas extras fin de semana nocturnas (HEFN: 2.5x)', 'costos', '2026-01-08 05:23:47', '2026-01-08 05:23:47'),
(33, 'factor_festivo_diurno', '2.1', 'decimal', 'Factor multiplicador para horas extras días festivos diurnas (HEFD: 2.1x)', 'costos', '2026-01-08 05:23:47', '2026-01-08 05:23:47'),
(34, 'factor_festivo_nocturno', '2.5', 'decimal', 'Factor multiplicador para horas extras días festivos nocturnas (HEFN: 2.5x)', 'costos', '2026-01-08 05:23:47', '2026-01-08 05:23:47'),
(35, 'recargo_nocturno', '1.35', 'decimal', 'Recargo adicional para horas nocturnas (1.35x)', 'costos', '2026-01-08 05:23:47', '2026-01-08 05:23:47'),
(36, 'festivos_pais', 'CO', 'texto', 'Código ISO del país para consultar días festivos', 'integraciones', '2026-01-08 05:23:47', '2026-01-08 05:23:47'),
(37, 'festivos_api_url', 'https://date.nager.at/api/v3/PublicHolidays', 'texto', 'URL base de la API de días festivos', 'integraciones', '2026-01-08 05:23:47', '2026-01-08 05:23:47'),
(38, 'festivos_consulta_automatica', '1', 'booleano', 'Consultar automáticamente días festivos para calcular recargos', 'integraciones', '2026-01-08 05:23:47', '2026-01-08 05:23:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamentos`
--

CREATE TABLE `departamentos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `codigo` varchar(20) DEFAULT NULL,
  `responsable_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `departamentos`
--

INSERT INTO `departamentos` (`id`, `nombre`, `descripcion`, `codigo`, `responsable_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Ingenieria', 'Departamento encargado de procesos de  calculos', 'ING', NULL, 1, '2025-11-29 15:31:39', '2025-11-29 15:31:39'),
(2, 'Calidad', 'Departamento encargado de procesos de buen funcionamiento', 'CAL', NULL, 1, '2025-11-29 15:31:39', '2025-11-29 15:31:39'),
(3, 'Cotizacion', 'Departamento encargado de procesos de  cotización y ventas', 'COT', NULL, 1, '2025-11-29 15:31:39', '2025-11-29 15:31:39'),
(4, 'Licitaciones', 'Departamento encargado de procesos de  licitar proyectos', 'LIC', NULL, 1, '2025-11-29 15:31:39', '2025-11-29 15:31:39'),
(5, 'Recursos Humanos', '', 'RECHUM', 1, 1, '2026-01-08 05:27:52', '2026-01-08 05:27:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `festivos_cache`
--

CREATE TABLE `festivos_cache` (
  `id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `tipo` enum('festivo','puente') DEFAULT 'festivo',
  `pais` varchar(5) NOT NULL,
  `anio` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `festivos_cache`
--

INSERT INTO `festivos_cache` (`id`, `fecha`, `nombre`, `tipo`, `pais`, `anio`, `created_at`, `updated_at`) VALUES
(1, '2026-01-01', 'Año Nuevo', 'festivo', 'CO', 2026, '2026-01-08 05:25:06', '2026-01-08 05:25:06'),
(2, '2026-01-12', 'Día de los Reyes Magos', 'festivo', 'CO', 2026, '2026-01-08 05:25:06', '2026-01-08 05:25:06'),
(3, '2026-03-23', 'Día de San José', 'festivo', 'CO', 2026, '2026-01-08 05:25:06', '2026-01-08 05:25:06'),
(4, '2026-04-02', 'Jueves Santo', 'festivo', 'CO', 2026, '2026-01-08 05:25:06', '2026-01-08 05:25:06'),
(5, '2026-04-03', 'Viernes Santo', 'festivo', 'CO', 2026, '2026-01-08 05:25:06', '2026-01-08 05:25:06'),
(6, '2026-05-01', 'Primero de Mayo', 'festivo', 'CO', 2026, '2026-01-08 05:25:06', '2026-01-08 05:25:06'),
(7, '2026-05-18', 'Ascensión del señor', 'festivo', 'CO', 2026, '2026-01-08 05:25:06', '2026-01-08 05:25:06'),
(8, '2026-06-08', 'Corpus Christi', 'festivo', 'CO', 2026, '2026-01-08 05:25:06', '2026-01-08 05:25:06'),
(9, '2026-06-15', 'Sagrado Corazón', 'festivo', 'CO', 2026, '2026-01-08 05:25:06', '2026-01-08 05:25:06'),
(10, '2026-06-29', 'San Pedro y San Pablo', 'festivo', 'CO', 2026, '2026-01-08 05:25:06', '2026-01-08 05:25:06'),
(11, '2026-07-20', 'Declaracion de la Independencia de Colombia', 'festivo', 'CO', 2026, '2026-01-08 05:25:06', '2026-01-08 05:25:06'),
(12, '2026-08-07', 'Batalla de Boyacá', 'festivo', 'CO', 2026, '2026-01-08 05:25:06', '2026-01-08 05:25:06'),
(13, '2026-08-17', 'La Asunción', 'festivo', 'CO', 2026, '2026-01-08 05:25:06', '2026-01-08 05:25:06'),
(14, '2026-10-12', 'Día de la Raza', 'festivo', 'CO', 2026, '2026-01-08 05:25:06', '2026-01-08 05:25:06'),
(15, '2026-11-02', 'Dia de los Santos', 'festivo', 'CO', 2026, '2026-01-08 05:25:06', '2026-01-08 05:25:06'),
(16, '2026-11-16', 'Independencia de Cartagena', 'festivo', 'CO', 2026, '2026-01-08 05:25:06', '2026-01-08 05:25:06'),
(17, '2026-12-08', 'La Inmaculada Concepción', 'festivo', 'CO', 2026, '2026-01-08 05:25:06', '2026-01-08 05:25:06'),
(18, '2026-12-25', 'Navidad', 'festivo', 'CO', 2026, '2026-01-08 05:25:06', '2026-01-08 05:25:06'),
(19, '2025-01-01', 'Año Nuevo', 'festivo', 'CO', 2025, '2026-01-08 05:29:29', '2026-01-08 05:29:29'),
(20, '2025-01-06', 'Día de los Reyes Magos', 'festivo', 'CO', 2025, '2026-01-08 05:29:29', '2026-01-08 05:29:29'),
(21, '2025-03-24', 'Día de San José', 'festivo', 'CO', 2025, '2026-01-08 05:29:29', '2026-01-08 05:29:29'),
(22, '2025-04-17', 'Jueves Santo', 'festivo', 'CO', 2025, '2026-01-08 05:29:29', '2026-01-08 05:29:29'),
(23, '2025-04-18', 'Viernes Santo', 'festivo', 'CO', 2025, '2026-01-08 05:29:29', '2026-01-08 05:29:29'),
(24, '2025-05-01', 'Primero de Mayo', 'festivo', 'CO', 2025, '2026-01-08 05:29:29', '2026-01-08 05:29:29'),
(25, '2025-06-02', 'Ascensión del señor', 'festivo', 'CO', 2025, '2026-01-08 05:29:29', '2026-01-08 05:29:29'),
(26, '2025-06-23', 'Corpus Christi', 'festivo', 'CO', 2025, '2026-01-08 05:29:29', '2026-01-08 05:29:29'),
(27, '2025-06-30', 'San Pedro y San Pablo', 'festivo', 'CO', 2025, '2026-01-08 05:29:29', '2026-01-08 05:29:29'),
(29, '2025-07-20', 'Declaracion de la Independencia de Colombia', 'festivo', 'CO', 2025, '2026-01-08 05:29:29', '2026-01-08 05:29:29'),
(30, '2025-08-07', 'Batalla de Boyacá', 'festivo', 'CO', 2025, '2026-01-08 05:29:29', '2026-01-08 05:29:29'),
(31, '2025-08-18', 'La Asunción', 'festivo', 'CO', 2025, '2026-01-08 05:29:29', '2026-01-08 05:29:29'),
(32, '2025-10-13', 'Día de la Raza', 'festivo', 'CO', 2025, '2026-01-08 05:29:29', '2026-01-08 05:29:29'),
(33, '2025-11-03', 'Dia de los Santos', 'festivo', 'CO', 2025, '2026-01-08 05:29:29', '2026-01-08 05:29:29'),
(34, '2025-11-17', 'Independencia de Cartagena', 'festivo', 'CO', 2025, '2026-01-08 05:29:29', '2026-01-08 05:29:29'),
(35, '2025-12-08', 'La Inmaculada Concepción', 'festivo', 'CO', 2025, '2026-01-08 05:29:29', '2026-01-08 05:29:29'),
(36, '2025-12-25', 'Navidad', 'festivo', 'CO', 2025, '2026-01-08 05:29:29', '2026-01-08 05:29:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_cambios`
--

CREATE TABLE `historial_cambios` (
  `id` int(11) NOT NULL,
  `tipo_registro` enum('registro_normal','hora_extra','orden_produccion','registro_produccion') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `registro_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `accion` enum('creacion','modificacion','eliminacion','validacion','aprobacion','rechazo') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `datos_anteriores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `datos_nuevos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `motivo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Volcado de datos para la tabla `historial_cambios`
--

INSERT INTO `historial_cambios` (`id`, `tipo_registro`, `registro_id`, `usuario_id`, `accion`, `datos_anteriores`, `datos_nuevos`, `motivo`, `ip_address`, `created_at`) VALUES
(1, 'registro_produccion', 1, 7, 'creacion', NULL, '{\"hr\": 1.00, \"hed\": 0.00, \"hen\": 0.00, \"hefd\": 0.00, \"hefn\": 0.00, \"fecha\": \"2026-01-01\", \"maquina\": \"MAQ-001\", \"descripcion\": \"\", \"total_horas\": 1.00, \"observaciones\": \"\", \"orden_produccion_id\": 11}', NULL, NULL, '2026-01-08 05:22:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios_laborales`
--

CREATE TABLE `horarios_laborales` (
  `id` int(11) NOT NULL,
  `dia_semana` enum('lunes','martes','miercoles','jueves','viernes','sabado','domingo') NOT NULL,
  `hora_inicio_manana` time NOT NULL,
  `hora_fin_manana` time NOT NULL,
  `hora_inicio_tarde` time DEFAULT NULL,
  `hora_fin_tarde` time DEFAULT NULL,
  `horas_totales` decimal(4,2) NOT NULL,
  `es_laborable` tinyint(1) DEFAULT 1,
  `descripcion` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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
  `id` int(11) NOT NULL,
  `codigo_op` varchar(50) NOT NULL,
  `nombre_producto` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `cliente` varchar(100) DEFAULT NULL,
  `cantidad_objetivo` int(11) DEFAULT 0,
  `unidad_medida` varchar(20) DEFAULT 'unidades',
  `fecha_inicio` date NOT NULL,
  `fecha_fin_estimada` date DEFAULT NULL,
  `fecha_fin_real` date DEFAULT NULL,
  `estado` enum('activa','en_proceso','completada','cancelada') DEFAULT 'activa',
  `prioridad` enum('baja','media','alta','urgente') DEFAULT 'media',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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
(12, '7029', 'FABRICACION DE BOTAS DE GAS ', 'FABRICACION DE BOTAS DE GAS ', 'ECOPETROL', 3, 'unidades', '2025-01-01', '2026-01-30', NULL, 'activa', 'media', '2025-12-27 14:42:03', '2025-12-27 14:42:03'),
(13, 'OPI-359', 'ASME', 'Todo lo relacionado a ASME y preparación para el Joint Review: Agendamiento de auditoría, revisión de plataformas, revisión de dossieres de equipos ASME para el Joint Review', 'Talleres Unidos LDTA', 1, 'unidades', '2026-01-05', '2026-07-30', NULL, 'activa', 'alta', '2026-01-09 15:23:10', '2026-01-09 15:23:10'),
(14, '50027', 'REENTUBE TOTAL A DOS AEROENFRIADORES  AE 270 -A Y 270 -B', 'REENTUBE TOTAL A DOS AEROENFRIADORES  AE 270 -A Y 270 -B', 'MONTAJES TECNICOS ZAMBRANO & VARGAS S.A.S.', 2, 'unidades', '2026-01-07', '2026-05-30', NULL, 'activa', 'alta', '2026-03-30 19:49:17', '2026-03-30 19:49:17'),
(15, '50049', 'FABRICACION AEROENFRIADOR AE-601A', 'FABRICACION DE AEROENFRIADOR  TUEBRIA ALETEDA EN ACERO AL CARBON', 'MONTAJES TECNICOS ZAMBRANO & VARGAS S.A.S.', 1, 'unidades', '2026-03-13', '2026-05-15', NULL, 'activa', 'urgente', '2026-04-09 21:40:42', '2026-04-09 21:40:42'),
(16, '50050', 'FABRICACION AERO ENFRIADOR AE-602A', 'FABRICACION AEROENFRIADOR TUBRIA ALETEADA EN ACERO AL CARBON ', 'MONTAJES TECNICOS ZAMBRANO & VARGAS S.A.S.', 1, 'unidades', '2026-03-13', '2026-05-15', NULL, 'activa', 'urgente', '2026-04-09 21:42:02', '2026-04-09 21:42:02'),
(17, '50051', 'FABRICACION AERO ENFRIADOR AE-603A', 'FABRIACION AEROENFRIADOR TUBERIA ALETEADA EN ACERO AL CARBON ', 'MONTAJES TECNICOS ZAMBRANO & VARGAS S.A.S.', 1, 'unidades', '2026-03-13', '2026-05-15', NULL, 'activa', 'urgente', '2026-04-09 21:43:18', '2026-04-09 21:43:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registros_horas`
--

CREATE TABLE `registros_horas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `orden_produccion_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `horas_trabajadas` decimal(4,2) NOT NULL COMMENT 'Número de horas trabajadas (0.5 a 8.5)',
  `descripcion_trabajo` text NOT NULL,
  `observaciones` text DEFAULT NULL,
  `estado` enum('registrado','validado','rechazado') DEFAULT 'registrado',
  `editado` tinyint(1) DEFAULT 0 COMMENT 'Indica si el registro ya fue editado (0=no, 1=sí)',
  `validado_por` int(11) DEFAULT NULL,
  `fecha_validacion` timestamp NULL DEFAULT NULL,
  `comentario_validacion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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
(336, 5, 10, '2026-01-05', 0.50, 'Ajustar cotización MONTAJES TECNICOS - Forma de pago', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-05 21:09:51', '2026-01-05 21:09:51'),
(337, 3, 4, '2026-01-05', 2.00, 'Recepción del Material ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 14:52:04', '2026-01-09 14:52:04'),
(338, 3, 3, '2026-01-05', 1.00, 'Reunión con el gerente y producción para el Plan de acción', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 14:52:04', '2026-01-09 14:52:04'),
(339, 3, 3, '2026-01-05', 4.00, 'Revisión de planos y planeación de END', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 14:52:04', '2026-01-09 14:52:04'),
(340, 4, 5, '2026-01-05', 2.00, 'Recepción de materiales, revisión de mtrs, organización de dossier, revisión de planos para puntos de espera', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 14:53:28', '2026-01-09 14:53:28'),
(341, 4, 6, '2026-01-05', 2.00, 'Recepción de materiales, revisión de mtrs, organización de dossier, revisión de planos para puntos de espera', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 14:53:28', '2026-01-09 14:53:28'),
(342, 4, 3, '2026-01-05', 2.00, 'Recepción de materiales, revisión de mtrs, organización de dossier, revisión de planos para puntos de espera', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 14:53:28', '2026-01-09 14:53:28'),
(343, 4, 4, '2026-01-05', 1.00, 'Revisión de material monel ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 14:53:28', '2026-01-09 14:53:28'),
(344, 3, 3, '2026-01-06', 4.00, 'Revisión de planos, (Juntas soldadas). Revisión de estrategias de producción, inspección visual de biseles', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 14:57:44', '2026-01-09 14:57:44'),
(345, 3, 13, '2026-01-06', 4.00, 'Construcción de matriz documental para verificación de la documentación disponible (Dossieres de los equipos estampados en los ultimos años) para el Joint Review', NULL, 'registrado', 1, NULL, NULL, NULL, '2026-01-09 14:57:44', '2026-01-09 15:32:56'),
(346, 4, 3, '2026-01-06', 2.00, 'Inspección biselado láminas para cajones, medición de longitudes, inspección de ángulos en bridas, evidencia fotográfica para inspector asme', NULL, 'registrado', 1, NULL, NULL, NULL, '2026-01-09 14:57:50', '2026-01-09 15:19:18'),
(347, 4, 5, '2026-01-06', 2.00, 'Inspección biselado láminas para cajones, medición de longitudes, inspección de ángulos en bridas, evidencia fotográfica para inspector asme', NULL, 'registrado', 1, NULL, NULL, NULL, '2026-01-09 14:57:50', '2026-01-09 15:18:50'),
(348, 4, 6, '2026-01-06', 2.00, 'Inspección biselado láminas para cajones, medición de longitudes, inspección de ángulos en bridas, evidencia fotográfica para inspector asme', NULL, 'registrado', 1, NULL, NULL, NULL, '2026-01-09 14:57:50', '2026-01-09 15:18:19'),
(349, 4, 4, '2026-01-06', 2.00, 'Recepción de materiales monel, revisión he inspeccion', NULL, 'registrado', 1, NULL, NULL, NULL, '2026-01-09 14:57:50', '2026-01-09 15:17:41'),
(350, 3, 13, '2026-01-07', 5.00, 'Revisión de documentación disponible (Dossieres de los equipos estampados en los ultimos años) para el Joint Review', NULL, 'registrado', 1, NULL, NULL, NULL, '2026-01-09 15:01:31', '2026-01-09 15:33:36'),
(351, 3, 3, '2026-01-07', 3.00, 'Cotización de nuevo proveedor para realizar RT', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 15:01:31', '2026-01-09 15:01:31'),
(352, 2, 3, '2025-12-31', 2.00, 'Gestión en planta y documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 15:08:38', '2026-01-09 15:08:38'),
(353, 2, 6, '2025-12-31', 2.00, 'Gestión en planta y documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 15:08:38', '2026-01-09 15:08:38'),
(354, 2, 5, '2025-12-31', 2.00, 'Gestión en planta y documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 15:08:38', '2026-01-09 15:08:38'),
(355, 3, 3, '2026-01-08', 2.00, 'Cotización de nuevo proveedor para realizar RT', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 15:09:20', '2026-01-09 15:09:20'),
(356, 3, 3, '2026-01-08', 1.50, 'Visita de posibles proveedores para realizar RT', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 15:09:20', '2026-01-09 15:09:20'),
(357, 3, 13, '2026-01-08', 2.00, 'Revisión de documentación disponible (Dossieres de los equipos estampados en los ultimos años) para el Joint Review', NULL, 'registrado', 1, NULL, NULL, NULL, '2026-01-09 15:09:20', '2026-01-09 15:32:14'),
(358, 3, 3, '2026-01-08', 2.50, 'Inspección de armado de cajones, avance de maquinado de bridas en el CNC, planeación posible entrega de puntos de espera', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 15:09:20', '2026-01-09 15:09:20'),
(359, 4, 3, '2026-01-07', 2.00, 'Inspección, marcado de láminas  ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 15:10:22', '2026-01-09 15:10:22'),
(360, 4, 5, '2026-01-07', 2.00, 'Inspección, marcado de láminas  ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 15:10:22', '2026-01-09 15:10:22'),
(361, 4, 6, '2026-01-07', 2.00, 'Inspección, marcado de láminas  ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 15:10:22', '2026-01-09 15:10:22'),
(362, 4, 13, '2026-01-07', 2.00, 'Revisión documental de los dossieres  asme para auditoria', NULL, 'registrado', 1, NULL, NULL, NULL, '2026-01-09 15:10:22', '2026-01-09 15:33:07'),
(363, 4, 13, '2026-01-08', 4.00, 'Organización de dissieres asme en físico y digital', NULL, 'registrado', 1, NULL, NULL, NULL, '2026-01-09 15:12:23', '2026-01-09 15:34:13'),
(364, 4, 6, '2026-01-08', 4.00, 'Visita proveedores de radiografías ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 15:12:23', '2026-01-09 15:12:23'),
(365, 2, 3, '2026-01-05', 2.00, 'Gestión en planta y documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 15:13:17', '2026-01-09 15:13:17'),
(366, 2, 6, '2026-01-05', 2.00, 'Gestión en planta y documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 15:13:17', '2026-01-09 15:13:17'),
(367, 2, 5, '2026-01-05', 2.00, 'Gestión en planta y documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 15:13:17', '2026-01-09 15:13:17'),
(368, 4, 3, '2026-01-09', 2.00, 'ensayos no destructivos líquidos penetrantes a biseles ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 15:14:52', '2026-01-09 15:14:52'),
(369, 4, 5, '2026-01-09', 2.00, 'ensayos no destructivos líquidos penetrantes a biseles ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 15:14:52', '2026-01-09 15:14:52'),
(370, 4, 6, '2026-01-09', 2.00, 'ensayos no destructivos líquidos penetrantes a biseles ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 15:14:52', '2026-01-09 15:14:52'),
(371, 4, 4, '2026-01-09', 2.00, 'Inspección soldada de brida con purga', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 15:14:52', '2026-01-09 15:14:52'),
(372, 2, 13, '2026-01-05', 1.00, 'Solicitud de agendamiento de auditoría', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 15:24:06', '2026-01-09 15:24:06'),
(373, 2, 3, '2026-01-06', 2.00, 'Gestión en planta y documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 18:51:12', '2026-01-09 18:51:12'),
(374, 2, 6, '2026-01-06', 2.00, 'Gestión en planta y documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 18:51:12', '2026-01-09 18:51:12'),
(375, 2, 5, '2026-01-06', 2.00, 'Gestión en planta y documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 18:51:12', '2026-01-09 18:51:12'),
(376, 2, 13, '2026-01-06', 2.00, 'Gestión en planta y documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 18:51:12', '2026-01-09 18:51:12'),
(377, 2, 3, '2026-01-07', 2.00, 'Gestión en planta y documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 18:52:02', '2026-01-09 18:52:02'),
(378, 2, 6, '2026-01-07', 2.00, 'Gestión en planta y documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 18:52:02', '2026-01-09 18:52:02'),
(379, 2, 5, '2026-01-07', 2.00, 'Gestión en planta y documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 18:52:02', '2026-01-09 18:52:02'),
(380, 2, 13, '2026-01-07', 2.00, 'Gestión en planta y documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 18:52:02', '2026-01-09 18:52:02'),
(381, 2, 3, '2026-01-08', 2.00, 'Gestión en planta y documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 18:53:43', '2026-01-09 18:53:43'),
(382, 2, 6, '2026-01-08', 2.00, 'Gestión en planta y documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 18:53:43', '2026-01-09 18:53:43'),
(383, 2, 5, '2026-01-08', 2.00, 'Gestión en planta y documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 18:53:43', '2026-01-09 18:53:43'),
(384, 2, 13, '2026-01-08', 2.00, 'Gestión en planta y documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 18:53:43', '2026-01-09 18:53:43'),
(385, 2, 5, '2026-01-09', 2.00, 'Gestión en planta y documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 18:54:32', '2026-01-09 18:54:32'),
(386, 2, 6, '2026-01-09', 2.00, 'Gestión en planta y documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 18:54:32', '2026-01-09 18:54:32'),
(387, 2, 3, '2026-01-09', 2.00, 'Gestión en planta y documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 18:54:32', '2026-01-09 18:54:32'),
(388, 2, 13, '2026-01-09', 2.00, 'Gestión en planta y documental', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-09 18:54:32', '2026-01-09 18:54:32'),
(389, 4, 6, '2026-01-10', 2.00, 'Ensayos no destructivos por líquidos penetrantes', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-13 21:56:38', '2026-01-13 21:56:38'),
(390, 4, 5, '2026-01-10', 2.00, 'Ensayos no destructivos por líquidos penetrantes', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-13 21:56:38', '2026-01-13 21:56:38'),
(391, 4, 3, '2026-01-10', 1.00, 'Ensayos no destructivos por líquidos penetrantes', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-13 21:56:38', '2026-01-13 21:56:38'),
(392, 4, 3, '2026-01-13', 2.00, 'Ensayos no destructivos por líquidos penetrantes, realizacion formato de tintas', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-13 22:00:16', '2026-01-13 22:00:16'),
(393, 4, 5, '2026-01-13', 2.00, 'Ensayos no destructivos por líquidos penetrantes, realizacion formato de tintas', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-13 22:00:17', '2026-01-13 22:00:17'),
(394, 4, 6, '2026-01-13', 2.00, 'Ensayos no destructivos por líquidos penetrantes, realizacion formato de tintas', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-13 22:00:17', '2026-01-13 22:00:17'),
(395, 4, 13, '2026-01-13', 2.00, 'Organización dossieres ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-13 22:00:17', '2026-01-13 22:00:17'),
(396, 4, 3, '2026-01-14', 4.00, 'Ensayos no destructivos por líquidos penetrantes, actualización formato, entrega líquidos, inspección de uniones soldadas ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-14 21:26:26', '2026-01-14 21:26:26'),
(397, 4, 6, '2026-01-14', 2.00, 'Ensayos no destructivos por líquidos penetrantes', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-14 21:26:26', '2026-01-14 21:26:26'),
(398, 4, 13, '2026-01-14', 2.00, 'Organización dossieres ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-14 21:26:26', '2026-01-14 21:26:26'),
(399, 4, 6, '2026-01-15', 2.00, 'Ensayos no destructivos por líquidos penetrantes, formato de tintas ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-19 13:24:57', '2026-01-19 13:24:57'),
(400, 4, 5, '2026-01-15', 2.00, 'Ensayos no destructivos por líquidos penetrantes, formato de tintas ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-19 13:24:57', '2026-01-19 13:24:57'),
(401, 4, 3, '2026-01-15', 2.00, 'Inspección armado y soldadura ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-19 13:24:57', '2026-01-19 13:24:57'),
(402, 4, 13, '2026-01-15', 2.00, 'Revisión dossieres', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-19 13:24:57', '2026-01-19 13:24:57'),
(403, 4, 3, '2026-01-16', 4.00, 'Inspector ASME', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-19 13:27:28', '2026-01-19 13:27:28'),
(404, 4, 10, '2026-01-16', 4.00, 'Inspector ASME', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-19 13:27:28', '2026-01-19 13:27:28'),
(405, 4, 3, '2026-01-17', 2.00, 'Registro de radiografías y reparación   ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-19 13:38:22', '2026-01-19 13:38:22'),
(406, 4, 5, '2026-01-17', 1.00, 'Ensayo no destructivo por líquidos penetrantes ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-19 13:38:22', '2026-01-19 13:38:22'),
(407, 4, 4, '2026-01-17', 2.00, 'Realización de inspección para programa de radiografía ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-19 13:38:22', '2026-01-19 13:38:22'),
(408, 3, 3, '2026-01-16', 8.00, 'Revisión de documento con Inspector ASME.  Liberación de Hold Points (5/6), liberación de materiales, liberación de procedimeintos.', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-19 13:49:24', '2026-01-19 13:49:24'),
(409, 3, 3, '2026-01-17', 5.00, 'Revisión de Dossieres, revisón de peliculas de soldadura, programación de soldadura', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-19 13:57:49', '2026-01-19 13:57:49'),
(410, 4, 3, '2026-01-19', 4.00, 'Inspección, toma de medidas a laterales', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-19 20:58:08', '2026-01-19 20:58:08'),
(411, 4, 6, '2026-01-19', 2.00, 'Inspección, verificación de certificado de tubería. ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-19 20:58:08', '2026-01-19 20:58:08'),
(412, 4, 5, '2026-01-19', 1.00, 'Revisión de placas radiográficas. ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-19 20:58:08', '2026-01-19 20:58:08'),
(413, 4, 3, '2026-01-20', 2.00, 'wps, wpq, pqr calificación de soldadores ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-20 21:56:25', '2026-01-20 21:56:25'),
(414, 4, 5, '2026-01-20', 2.00, 'wps, wpq, pqr calificación de soldadores ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-20 21:56:25', '2026-01-20 21:56:25'),
(415, 4, 6, '2026-01-20', 2.00, 'wps, wpq, pqr calificación de soldadores ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-20 21:56:25', '2026-01-20 21:56:25'),
(416, 4, 6, '2026-01-20', 2.00, 'Ensayo no destructivo por líquidos penetrantes ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-20 21:56:25', '2026-01-20 21:56:25'),
(417, 2, 3, '2026-01-10', 1.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 13:55:17', '2026-01-21 13:55:17'),
(418, 2, 5, '2026-01-10', 1.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 13:55:17', '2026-01-21 13:55:17'),
(419, 2, 6, '2026-01-10', 1.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 13:55:17', '2026-01-21 13:55:17'),
(420, 2, 13, '2026-01-10', 2.00, 'Gestión solicitud de renovación de estampe', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 13:55:17', '2026-01-21 13:55:17'),
(421, 2, 3, '2026-01-12', 2.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 13:56:44', '2026-01-21 13:56:44'),
(422, 2, 5, '2026-01-12', 2.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 13:56:44', '2026-01-21 13:56:44'),
(423, 2, 6, '2026-01-12', 2.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 13:56:44', '2026-01-21 13:56:44'),
(424, 2, 13, '2026-01-12', 1.00, 'Gestión de solicitud de renovación de estampe', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 13:56:44', '2026-01-21 13:56:44'),
(429, 2, 3, '2026-01-14', 2.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 14:03:16', '2026-01-21 14:03:16'),
(430, 2, 5, '2026-01-14', 2.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 14:03:16', '2026-01-21 14:03:16'),
(431, 2, 6, '2026-01-14', 2.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 14:03:16', '2026-01-21 14:03:16'),
(432, 2, 13, '2026-01-14', 2.00, 'Gestión solicitud de renovación de estampe', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 14:03:16', '2026-01-21 14:03:16'),
(433, 2, 3, '2026-01-15', 2.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 14:04:50', '2026-01-21 14:04:50'),
(434, 2, 5, '2026-01-15', 2.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 14:04:50', '2026-01-21 14:04:50'),
(435, 2, 6, '2026-01-15', 2.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 14:04:50', '2026-01-21 14:04:50'),
(436, 2, 13, '2026-01-15', 2.00, 'Gestión solicitud de renovación de estampe', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 14:04:50', '2026-01-21 14:04:50'),
(437, 2, 3, '2026-01-16', 2.00, 'Atención visita Inspector Autorizado ASME', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 14:06:35', '2026-01-21 14:06:35'),
(438, 2, 5, '2026-01-16', 3.00, 'Atención visita Inspector Autorizado ASME', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 14:06:35', '2026-01-21 14:06:35'),
(439, 2, 6, '2026-01-16', 3.00, 'Atención visita Inspector Autorizado ASME', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 14:06:35', '2026-01-21 14:06:35'),
(440, 2, 3, '2026-01-17', 1.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 14:07:38', '2026-01-21 14:07:38'),
(441, 2, 5, '2026-01-17', 1.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 14:07:38', '2026-01-21 14:07:38'),
(442, 2, 6, '2026-01-17', 1.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 14:07:38', '2026-01-21 14:07:38'),
(443, 2, 13, '2026-01-17', 2.00, 'Gestión solicitud de renovación de estampe', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 14:07:38', '2026-01-21 14:07:38'),
(444, 2, 3, '2026-01-19', 2.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 14:08:29', '2026-01-21 14:08:29'),
(445, 2, 5, '2026-01-19', 2.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 14:08:29', '2026-01-21 14:08:29'),
(446, 2, 6, '2026-01-19', 2.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 14:08:29', '2026-01-21 14:08:29'),
(447, 2, 13, '2026-01-19', 1.00, 'Gestión solicitud de renovación de estampe', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 14:08:29', '2026-01-21 14:08:29'),
(448, 2, 3, '2026-01-20', 2.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 14:10:09', '2026-01-21 14:10:09'),
(449, 2, 5, '2026-01-20', 2.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 14:10:09', '2026-01-21 14:10:09'),
(450, 2, 6, '2026-01-20', 2.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 14:10:09', '2026-01-21 14:10:09'),
(451, 2, 2, '2026-01-20', 2.00, 'Cierre documental para recibo del equipo', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-21 14:10:09', '2026-01-21 14:10:09'),
(452, 4, 5, '2026-01-21', 1.00, 'wps, pqr, wpq', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-22 22:00:52', '2026-01-22 22:00:52'),
(453, 4, 3, '2026-01-21', 1.00, 'wps, pqr, wpq', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-22 22:00:52', '2026-01-22 22:00:52'),
(454, 4, 6, '2026-01-21', 1.00, 'wps, pqr, wpq', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-22 22:00:52', '2026-01-22 22:00:52'),
(455, 4, 6, '2026-01-21', 2.00, 'calificacion junta de esquina', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-22 22:00:52', '2026-01-22 22:00:52'),
(456, 4, 5, '2026-01-21', 1.00, 'Ensayos no destructivos por líquidos penetrantes', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-22 22:00:52', '2026-01-22 22:00:52'),
(457, 4, 6, '2026-01-21', 1.00, 'Ensayos no destructivos por líquidos penetrantes', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-22 22:00:52', '2026-01-22 22:00:52'),
(458, 4, 3, '2026-01-21', 1.00, 'Ensayos no destructivos por líquidos penetrantes', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-22 22:00:52', '2026-01-22 22:00:52'),
(459, 4, 6, '2026-01-22', 2.00, 'Revisión de placas radiograficas ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-22 22:02:45', '2026-01-22 22:02:45'),
(460, 4, 4, '2026-01-22', 2.00, 'Revisión documental ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-22 22:02:45', '2026-01-22 22:02:45'),
(461, 4, 3, '2026-01-22', 2.00, 'Ensayos no destructivos por líquidos penetrantes', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-22 22:02:45', '2026-01-22 22:02:45'),
(462, 4, 6, '2026-01-22', 2.00, 'Ensayos no destructivos por líquidos penetrantes', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-22 22:02:45', '2026-01-22 22:02:45'),
(463, 4, 3, '2026-01-23', 2.00, 'Lijado de probetas de calificacion para prueba de macroataque', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-24 16:30:58', '2026-01-24 16:30:58'),
(464, 4, 5, '2026-01-23', 2.00, 'Lijado de probeta de calificación para prueba de macroataque  ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-27 19:11:27', '2026-01-27 19:11:27'),
(465, 4, 6, '2026-01-23', 2.00, 'Lijado de probeta de calificación para prueba de macroataque  ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-27 19:11:27', '2026-01-27 19:11:27'),
(466, 4, 4, '2026-01-23', 2.00, 'revision dossier ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-27 19:11:27', '2026-01-27 19:11:27'),
(467, 4, 5, '2026-01-24', 1.00, 'Ensayo no destructivo por líquidos penetrantes ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-27 19:13:11', '2026-01-27 19:13:11'),
(468, 4, 6, '2026-01-24', 1.00, 'Ensayo no destructivo por líquidos penetrantes ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-27 19:13:11', '2026-01-27 19:13:11'),
(469, 4, 3, '2026-01-24', 1.00, 'Ensayo no destructivo por líquidos penetrantes ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-27 19:13:12', '2026-01-27 19:13:12'),
(470, 4, 13, '2026-01-24', 2.00, 'Revisión de dossieres ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-27 19:13:12', '2026-01-27 19:13:12'),
(471, 4, 3, '2026-01-26', 2.00, 'Revisión de documentación armado dossieres', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-27 19:18:50', '2026-01-27 19:18:50'),
(472, 4, 5, '2026-01-26', 2.00, 'Revisión de documentación armado dossieres', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-27 19:18:50', '2026-01-27 19:18:50'),
(473, 4, 6, '2026-01-26', 2.00, 'Revisión de documentación armado dossieres', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-27 19:18:50', '2026-01-27 19:18:50'),
(474, 4, 4, '2026-01-26', 1.00, 'Revisión documentación ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-27 19:18:50', '2026-01-27 19:18:50'),
(475, 4, 6, '2026-01-27', 2.00, 'Ensayos no destructivos por líquidos penetrantes ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-27 21:53:00', '2026-01-27 21:53:00'),
(476, 4, 5, '2026-01-27', 2.00, 'Preparación dossier inspector asme  ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-27 21:53:00', '2026-01-27 21:53:00'),
(477, 4, 6, '2026-01-27', 2.00, 'Preparación dossier inspector asme  ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-27 21:53:00', '2026-01-27 21:53:00'),
(478, 4, 3, '2026-01-27', 2.00, 'Preparación dossier inspector asme  ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-01-27 21:53:00', '2026-01-27 21:53:00'),
(479, 3, 3, '2026-02-02', 7.00, 'Revisión de soldadura, Dossier, radiografias y documentos a presentar para visita del IA', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-11 15:16:48', '2026-02-11 15:16:48'),
(480, 3, 3, '2026-02-03', 8.00, 'Revisión de documentos de Petro testing (nuevo proveedor de RT) Revisión de soldadura de cabezales, revisión radiografías, revisión de documentos', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-11 15:18:26', '2026-02-11 15:18:26'),
(481, 3, 3, '2026-02-04', 8.00, 'Revisión de documentos de Petro testing (nuevo proveedor de RT) Revisión de soldadura de cabezales, revisión radiografías, revisión de documentos', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-11 15:33:25', '2026-02-11 15:33:25'),
(482, 3, 3, '2026-02-05', 8.00, 'Reunión con petrotesting para retoma de placas y corrección de informes, inspección de soldadura de cabezales', NULL, 'registrado', 1, NULL, NULL, NULL, '2026-02-11 15:59:04', '2026-02-11 18:14:39'),
(483, 3, 3, '2026-02-06', 8.00, 'corrección de informes, inspección de soldadura de cabezales, welding map ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-11 18:06:08', '2026-02-11 18:06:08'),
(484, 3, 3, '2026-02-07', 5.00, 'Seguimiento e inspección de soldadura de cabezales, construcción de formato liberación dimensional ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-11 18:08:32', '2026-02-11 18:08:32'),
(485, 3, 3, '2026-02-09', 7.00, 'Inspección de soldadura, inspección de placas e informes de radiografías, recepción de tubing ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-11 18:13:04', '2026-02-11 18:13:04'),
(486, 3, 3, '2026-02-10', 8.00, 'Revisión de placas e informes de RT junto al Inspector ASME, revisión y liberación de soldadura junto al inspector ASME, revisión de documentación proveedor de radiografía, liberación de cabezales para alivio térmico ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-11 18:22:26', '2026-02-11 18:22:26'),
(487, 2, 3, '2026-01-21', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:14:18', '2026-02-16 14:14:18'),
(488, 2, 5, '2026-01-21', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:14:18', '2026-02-16 14:14:18'),
(489, 2, 6, '2026-01-21', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:14:18', '2026-02-16 14:14:18'),
(490, 2, 3, '2026-01-22', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:14:55', '2026-02-16 14:14:55'),
(491, 2, 5, '2026-01-22', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:14:55', '2026-02-16 14:14:55'),
(492, 2, 6, '2026-01-22', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:14:55', '2026-02-16 14:14:55'),
(493, 2, 3, '2026-01-23', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:17:30', '2026-02-16 14:17:30'),
(494, 2, 5, '2026-01-23', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:17:30', '2026-02-16 14:17:30'),
(495, 2, 6, '2026-01-23', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:17:30', '2026-02-16 14:17:30'),
(496, 2, 3, '2026-01-24', 1.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:18:16', '2026-02-16 14:18:16'),
(497, 2, 5, '2026-01-24', 1.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:18:16', '2026-02-16 14:18:16'),
(498, 2, 6, '2026-01-24', 1.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:18:16', '2026-02-16 14:18:16'),
(499, 2, 3, '2026-01-26', 2.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:19:09', '2026-02-16 14:19:09'),
(500, 2, 5, '2026-01-26', 2.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:19:09', '2026-02-16 14:19:09'),
(501, 2, 6, '2026-01-26', 2.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:19:09', '2026-02-16 14:19:09'),
(502, 2, 3, '2026-01-27', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:19:35', '2026-02-16 14:19:35'),
(503, 2, 5, '2026-01-27', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:19:35', '2026-02-16 14:19:35'),
(504, 2, 6, '2026-01-27', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:19:35', '2026-02-16 14:19:35'),
(505, 2, 3, '2026-01-28', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:20:21', '2026-02-16 14:20:21'),
(506, 2, 5, '2026-01-28', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:20:21', '2026-02-16 14:20:21'),
(507, 2, 6, '2026-01-28', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:20:21', '2026-02-16 14:20:21'),
(508, 2, 3, '2026-01-29', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:21:09', '2026-02-16 14:21:09'),
(509, 2, 5, '2026-01-29', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:21:09', '2026-02-16 14:21:09'),
(510, 2, 6, '2026-01-29', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:21:09', '2026-02-16 14:21:09'),
(511, 2, 3, '2026-01-30', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:21:47', '2026-02-16 14:21:47'),
(512, 2, 5, '2026-01-30', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:21:47', '2026-02-16 14:21:47'),
(513, 2, 6, '2026-01-30', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:21:47', '2026-02-16 14:21:47'),
(514, 2, 3, '2026-01-31', 1.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:22:42', '2026-02-16 14:22:42'),
(515, 2, 5, '2026-01-31', 1.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:22:42', '2026-02-16 14:22:42'),
(516, 2, 6, '2026-01-31', 1.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:22:42', '2026-02-16 14:22:42'),
(517, 2, 3, '2026-02-02', 2.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:23:12', '2026-02-16 14:23:12'),
(518, 2, 5, '2026-02-02', 2.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:23:12', '2026-02-16 14:23:12'),
(519, 2, 6, '2026-02-02', 2.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:23:12', '2026-02-16 14:23:12'),
(520, 2, 3, '2026-02-03', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:24:53', '2026-02-16 14:24:53'),
(521, 2, 5, '2026-02-03', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:24:53', '2026-02-16 14:24:53'),
(522, 2, 6, '2026-02-03', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:24:53', '2026-02-16 14:24:53'),
(523, 2, 3, '2026-02-04', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:25:30', '2026-02-16 14:25:30'),
(524, 2, 5, '2026-02-04', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:25:30', '2026-02-16 14:25:30'),
(525, 2, 6, '2026-02-04', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:25:30', '2026-02-16 14:25:30'),
(526, 2, 3, '2026-02-05', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:26:17', '2026-02-16 14:26:17'),
(527, 2, 5, '2026-02-05', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:26:17', '2026-02-16 14:26:17'),
(528, 2, 6, '2026-02-05', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:26:17', '2026-02-16 14:26:17'),
(529, 2, 3, '2026-02-06', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:27:02', '2026-02-16 14:27:02'),
(530, 2, 5, '2026-02-06', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:27:03', '2026-02-16 14:27:03'),
(531, 2, 6, '2026-02-06', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:27:03', '2026-02-16 14:27:03'),
(532, 2, 3, '2026-02-07', 1.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:27:29', '2026-02-16 14:27:29'),
(533, 2, 5, '2026-02-07', 1.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:27:29', '2026-02-16 14:27:29'),
(534, 2, 6, '2026-02-07', 1.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:27:29', '2026-02-16 14:27:29'),
(535, 2, 3, '2026-02-09', 2.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:27:59', '2026-02-16 14:27:59'),
(536, 2, 5, '2026-02-09', 2.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:27:59', '2026-02-16 14:27:59'),
(537, 2, 6, '2026-02-09', 2.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:27:59', '2026-02-16 14:27:59'),
(538, 2, 3, '2026-02-10', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:28:34', '2026-02-16 14:28:34'),
(539, 2, 5, '2026-02-10', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:28:34', '2026-02-16 14:28:34'),
(540, 2, 6, '2026-02-10', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:28:34', '2026-02-16 14:28:34'),
(541, 2, 3, '2026-02-11', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:29:05', '2026-02-16 14:29:05'),
(542, 2, 5, '2026-02-11', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:29:05', '2026-02-16 14:29:05'),
(543, 2, 6, '2026-02-11', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:29:05', '2026-02-16 14:29:05'),
(544, 2, 3, '2026-02-12', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:29:40', '2026-02-16 14:29:40'),
(545, 2, 5, '2026-02-12', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:29:40', '2026-02-16 14:29:40'),
(546, 2, 6, '2026-02-12', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:29:40', '2026-02-16 14:29:40'),
(547, 2, 3, '2026-02-13', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:30:17', '2026-02-16 14:30:17'),
(548, 2, 5, '2026-02-13', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:30:18', '2026-02-16 14:30:18'),
(549, 2, 6, '2026-02-13', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:30:18', '2026-02-16 14:30:18'),
(550, 2, 3, '2026-02-14', 1.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:30:46', '2026-02-16 14:30:46'),
(551, 2, 5, '2026-02-14', 1.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:30:46', '2026-02-16 14:30:46'),
(552, 2, 6, '2026-02-14', 1.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-16 14:30:46', '2026-02-16 14:30:46'),
(553, 4, 3, '2026-02-14', 5.00, 'Inspector asme ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-02-26 13:02:06', '2026-02-26 13:02:06'),
(554, 4, 3, '2026-03-02', 7.00, 'medicion de expansion y entubado ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-10 15:35:05', '2026-03-10 15:35:05'),
(555, 4, 3, '2026-03-03', 4.00, 'medicion de expansion ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-10 15:39:23', '2026-03-10 15:39:23'),
(556, 4, 5, '2026-03-03', 4.00, 'entubado, medicion de expansion ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-10 15:39:23', '2026-03-10 15:39:23'),
(557, 4, 6, '2026-03-04', 8.00, 'entube y medicion de expansion', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-10 15:41:54', '2026-03-10 15:41:54'),
(558, 4, 3, '2026-03-05', 8.00, 'prueba hidrostatica', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-10 15:42:57', '2026-03-10 15:42:57'),
(559, 4, 6, '2026-03-06', 8.00, 'Prueba hidrostatica ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-10 15:47:08', '2026-03-10 15:47:08'),
(560, 9, 13, '2026-03-03', 8.00, 'Actualización sistema ASME', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-10 15:47:57', '2026-03-10 15:47:57'),
(561, 9, 13, '2026-03-04', 8.00, 'Actualización sistema ASME', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-10 15:48:39', '2026-03-10 15:48:39'),
(562, 4, 3, '2026-03-07', 1.00, 'dossier', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-10 15:49:37', '2026-03-10 15:49:37'),
(563, 4, 5, '2026-03-07', 2.00, 'dossier', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-10 15:49:37', '2026-03-10 15:49:37'),
(564, 4, 6, '2026-03-07', 2.00, 'dossier', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-10 15:49:37', '2026-03-10 15:49:37'),
(565, 9, 13, '2026-03-05', 8.00, 'Acompañamiento visita inspector ASME, revisión de sistema ASME para actualización ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-10 15:50:29', '2026-03-10 15:50:29'),
(566, 9, 13, '2026-03-07', 5.00, 'Coordinar estampe de aeroenfriadores de ecopetrol, verificar pendientes de visita inspector ASME ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-10 15:51:57', '2026-03-10 15:51:57'),
(567, 4, 3, '2026-03-09', 2.00, 'envio aeroenfriadores', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-10 15:54:09', '2026-03-10 15:54:09'),
(568, 4, 5, '2026-03-09', 2.00, 'envio aeroenfriadores', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-10 15:54:09', '2026-03-10 15:54:09'),
(569, 4, 4, '2026-03-09', 3.00, 'organizacion dossier', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-10 15:54:09', '2026-03-10 15:54:09'),
(570, 4, 4, '2026-03-10', 4.00, 'inspeccion de boquillas ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-10 21:41:57', '2026-03-10 21:41:57'),
(571, 4, 13, '2026-03-10', 4.00, 'levantamiento plano aeroenfriado reentube', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-10 21:41:57', '2026-03-10 21:41:57'),
(572, 4, 4, '2026-03-11', 2.00, 'inspeccion ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-11 21:32:12', '2026-03-11 21:32:12');
INSERT INTO `registros_horas` (`id`, `usuario_id`, `orden_produccion_id`, `fecha`, `horas_trabajadas`, `descripcion_trabajo`, `observaciones`, `estado`, `editado`, `validado_por`, `fecha_validacion`, `comentario_validacion`, `created_at`, `updated_at`) VALUES
(573, 4, 13, '2026-03-11', 6.00, 'Organizacion documental ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-11 21:32:12', '2026-03-11 21:32:12'),
(574, 3, 3, '2026-03-11', 8.00, 'Informes de NDT líquidos penetrantes (PT)', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-12 12:23:42', '2026-03-12 12:23:42'),
(575, 3, 3, '2026-03-10', 8.00, 'Construcción de Dossier \r\nReunión con virtual con Ecopetrol, de cierre de proyecto\r\nInformes de NDT líquidos penetrantes (PT)', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-12 12:30:24', '2026-03-12 12:30:24'),
(576, 3, 3, '2026-03-09', 7.00, 'Construcción de Dossier ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-12 12:31:22', '2026-03-12 12:31:22'),
(577, 3, 3, '2026-03-07', 5.00, 'Alistamiento de Dossier ASME', NULL, 'registrado', 1, NULL, NULL, NULL, '2026-03-12 12:45:41', '2026-03-12 12:54:22'),
(578, 3, 3, '2026-03-06', 8.00, 'Visita de Inspector ASME, para liberación y estampe de equipos ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-12 12:46:07', '2026-03-12 12:46:07'),
(579, 3, 3, '2026-03-05', 8.00, 'Visita de Inspector ASME, para liberación y estampe de equipos', NULL, 'registrado', 1, NULL, NULL, NULL, '2026-03-12 12:47:02', '2026-03-12 12:53:39'),
(580, 3, 3, '2026-03-04', 8.00, 'Alistamiento de Dossier Ecopetrol\r\nReunión con Ecopetrol, para revisar avance de Dossier', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-12 12:48:59', '2026-03-12 12:48:59'),
(581, 3, 3, '2026-03-03', 8.00, 'Alistamiento de Dossier de construcción y Dossier ASME ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-12 12:49:50', '2026-03-12 12:49:50'),
(582, 3, 3, '2026-03-02', 7.00, 'Alistamiento de Dossier de construcción y Dossier ASME ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-12 12:50:19', '2026-03-12 12:50:19'),
(583, 2, 3, '2026-02-16', 2.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:14:57', '2026-03-13 13:14:57'),
(584, 2, 5, '2026-02-16', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:14:57', '2026-03-13 13:14:57'),
(585, 2, 6, '2026-02-16', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:14:57', '2026-03-13 13:14:57'),
(586, 2, 3, '2026-02-17', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:15:51', '2026-03-13 13:15:51'),
(587, 2, 6, '2026-02-17', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:15:51', '2026-03-13 13:15:51'),
(588, 2, 5, '2026-02-17', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:15:51', '2026-03-13 13:15:51'),
(589, 2, 3, '2026-02-18', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:16:24', '2026-03-13 13:16:24'),
(590, 2, 6, '2026-02-18', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:16:24', '2026-03-13 13:16:24'),
(591, 2, 5, '2026-02-18', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:16:24', '2026-03-13 13:16:24'),
(592, 2, 3, '2026-02-19', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:17:14', '2026-03-13 13:17:14'),
(593, 2, 5, '2026-02-19', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:17:14', '2026-03-13 13:17:14'),
(594, 2, 6, '2026-02-19', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:17:14', '2026-03-13 13:17:14'),
(595, 2, 3, '2026-02-20', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:17:49', '2026-03-13 13:17:49'),
(596, 2, 5, '2026-02-20', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:17:49', '2026-03-13 13:17:49'),
(597, 2, 6, '2026-02-20', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:17:49', '2026-03-13 13:17:49'),
(598, 2, 3, '2026-02-21', 1.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:18:29', '2026-03-13 13:18:29'),
(599, 2, 5, '2026-02-21', 1.50, 'Gestión documental y gestión en planta ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:18:29', '2026-03-13 13:18:29'),
(600, 2, 6, '2026-02-21', 1.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:18:29', '2026-03-13 13:18:29'),
(601, 2, 3, '2026-02-23', 2.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:19:21', '2026-03-13 13:19:21'),
(602, 2, 5, '2026-02-23', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:19:21', '2026-03-13 13:19:21'),
(603, 2, 6, '2026-02-23', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:19:21', '2026-03-13 13:19:21'),
(604, 2, 3, '2026-02-24', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:20:02', '2026-03-13 13:20:02'),
(605, 2, 5, '2026-02-24', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:20:02', '2026-03-13 13:20:02'),
(606, 2, 6, '2026-02-24', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:20:02', '2026-03-13 13:20:02'),
(607, 2, 3, '2026-02-25', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:21:26', '2026-03-13 13:21:26'),
(608, 2, 6, '2026-02-25', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:21:26', '2026-03-13 13:21:26'),
(609, 2, 5, '2026-02-25', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:21:26', '2026-03-13 13:21:26'),
(610, 2, 3, '2026-02-26', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:22:07', '2026-03-13 13:22:07'),
(611, 2, 6, '2026-02-26', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:22:07', '2026-03-13 13:22:07'),
(612, 2, 5, '2026-02-26', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:22:07', '2026-03-13 13:22:07'),
(613, 2, 3, '2026-02-27', 2.50, 'Gestión documental y gestión en planta, visita inspector ASME', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:23:07', '2026-03-13 13:23:07'),
(614, 2, 6, '2026-02-27', 2.50, 'Gestión documental y gestión en planta, visita inspector ASME', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:23:07', '2026-03-13 13:23:07'),
(615, 2, 5, '2026-02-27', 2.50, 'Gestión documental y gestión en planta, visita inspector ASME', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:23:07', '2026-03-13 13:23:07'),
(616, 2, 3, '2026-02-28', 1.50, 'Gestión documental y gestión en planta, visita inspector ASME', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:24:00', '2026-03-13 13:24:00'),
(617, 2, 5, '2026-02-28', 1.50, 'Gestión documental y gestión en planta, visita inspector ASME', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:24:00', '2026-03-13 13:24:00'),
(618, 2, 6, '2026-02-28', 1.50, 'Gestión documental y gestión en planta, visita inspector ASME', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:24:00', '2026-03-13 13:24:00'),
(619, 2, 3, '2026-03-05', 2.50, 'Gestión documental y gestión en planta, visita inspector ASME', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:25:33', '2026-03-13 13:25:33'),
(620, 2, 6, '2026-03-05', 2.50, 'Gestión documental y gestión en planta, visita inspector ASME', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:25:33', '2026-03-13 13:25:33'),
(621, 2, 5, '2026-03-05', 2.50, 'Gestión documental y gestión en planta, visita inspector ASME', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:25:33', '2026-03-13 13:25:33'),
(622, 2, 3, '2026-03-06', 2.50, 'Gestión documental y gestión en planta, visita inspector ASME', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:26:01', '2026-03-13 13:26:01'),
(623, 2, 6, '2026-03-06', 2.50, 'Gestión documental y gestión en planta, visita inspector ASME', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:26:01', '2026-03-13 13:26:01'),
(624, 2, 5, '2026-03-06', 2.50, 'Gestión documental y gestión en planta, visita inspector ASME', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:26:01', '2026-03-13 13:26:01'),
(625, 2, 3, '2026-03-02', 2.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:26:46', '2026-03-13 13:26:46'),
(626, 2, 5, '2026-03-02', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:26:46', '2026-03-13 13:26:46'),
(627, 2, 6, '2026-03-02', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:26:46', '2026-03-13 13:26:46'),
(628, 2, 3, '2026-03-03', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:27:16', '2026-03-13 13:27:16'),
(629, 2, 6, '2026-03-03', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:27:16', '2026-03-13 13:27:16'),
(630, 2, 5, '2026-03-03', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:27:16', '2026-03-13 13:27:16'),
(631, 2, 3, '2026-03-04', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:27:58', '2026-03-13 13:27:58'),
(632, 2, 6, '2026-03-04', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:27:58', '2026-03-13 13:27:58'),
(633, 2, 5, '2026-03-04', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:27:58', '2026-03-13 13:27:58'),
(634, 2, 3, '2026-03-07', 1.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:28:39', '2026-03-13 13:28:39'),
(635, 2, 5, '2026-03-07', 1.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:28:39', '2026-03-13 13:28:39'),
(636, 2, 6, '2026-03-07', 1.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:28:39', '2026-03-13 13:28:39'),
(637, 2, 3, '2026-03-09', 2.00, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:29:19', '2026-03-13 13:29:19'),
(638, 2, 5, '2026-03-09', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:29:19', '2026-03-13 13:29:19'),
(639, 2, 6, '2026-03-09', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:29:19', '2026-03-13 13:29:19'),
(640, 2, 6, '2026-03-10', 2.50, 'Gestión documental y gestión en planta', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:30:23', '2026-03-13 13:30:23'),
(641, 2, 5, '2026-03-10', 2.50, 'Gestión documental ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:30:23', '2026-03-13 13:30:23'),
(642, 2, 5, '2026-03-10', 2.50, 'Gestión documental ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:30:23', '2026-03-13 13:30:23'),
(643, 2, 3, '2026-03-11', 2.50, 'Gestión documental ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:31:01', '2026-03-13 13:31:01'),
(644, 2, 6, '2026-03-11', 2.50, 'Gestión documental ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:31:01', '2026-03-13 13:31:01'),
(645, 2, 5, '2026-03-11', 2.50, 'Gestión documental ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:31:01', '2026-03-13 13:31:01'),
(646, 2, 5, '2026-03-12', 2.50, 'Gestión documental ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:31:31', '2026-03-13 13:31:31'),
(647, 2, 6, '2026-03-12', 2.50, 'Gestión documental ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:31:31', '2026-03-13 13:31:31'),
(648, 2, 3, '2026-03-12', 2.50, 'Gestión documental ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:31:31', '2026-03-13 13:31:31'),
(649, 9, 13, '2026-03-09', 7.00, 'Actualización sistema de calidad ASME 2025', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:55:12', '2026-03-13 13:55:12'),
(650, 9, 13, '2026-03-10', 8.00, 'Actualización sistema de calidad ASME 2025', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:55:52', '2026-03-13 13:55:52'),
(651, 9, 13, '2026-03-11', 8.00, 'Actualización sistema de calidad ASME 2025', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:56:09', '2026-03-13 13:56:09'),
(652, 9, 13, '2026-03-12', 8.00, 'Actualización sistema de calidad ASME 2025', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 13:56:29', '2026-03-13 13:56:29'),
(653, 3, 3, '2026-03-12', 4.00, 'Completamiento del Dossier de Fabricación ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 14:27:39', '2026-03-13 14:27:39'),
(654, 3, 3, '2026-03-12', 4.00, 'Revisión documentos de petrotesting y solicitud de correcciones ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 14:27:39', '2026-03-13 14:27:39'),
(655, 4, 13, '2026-03-13', 6.00, 'actualizacion de formatos', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 21:58:16', '2026-03-13 21:58:16'),
(656, 4, 4, '2026-03-13', 2.00, 'Inspeccion ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 21:58:16', '2026-03-13 21:58:16'),
(657, 2, 3, '2026-03-13', 2.50, 'Revisión documental para dossier', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 22:11:39', '2026-03-13 22:11:39'),
(658, 2, 5, '2026-03-13', 2.50, 'Revisión documental para dossier', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 22:11:39', '2026-03-13 22:11:39'),
(659, 2, 6, '2026-03-13', 2.50, 'Revisión documental para dossier', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-13 22:11:39', '2026-03-13 22:11:39'),
(660, 4, 13, '2026-03-14', 4.00, 'organización documentación de dossieres', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-16 12:43:29', '2026-03-16 12:43:29'),
(661, 4, 4, '2026-03-14', 1.00, 'Inspección ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-16 12:43:29', '2026-03-16 12:43:29'),
(662, 3, 3, '2026-03-13', 8.00, 'Revisión y actualización del Dossier de fabricación', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-16 12:44:41', '2026-03-16 12:44:41'),
(663, 3, 3, '2026-03-14', 5.00, 'Resolución de comentarios de Ingeniería al Dossier de Fabricación ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-16 12:48:32', '2026-03-16 12:48:32'),
(664, 2, 3, '2026-03-14', 1.50, 'Revisión documental para el dossier', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-16 12:55:12', '2026-03-16 12:55:12'),
(665, 2, 5, '2026-03-14', 1.50, 'Revisión documental para el dossier', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-16 12:55:12', '2026-03-16 12:55:12'),
(666, 2, 6, '2026-03-14', 1.50, 'Revisión documental para el dossier', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-16 12:55:12', '2026-03-16 12:55:12'),
(667, 3, 4, '2026-03-16', 4.00, 'Sub subsanando pendientes de listas de chequeo', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-17 18:13:16', '2026-03-17 18:13:16'),
(668, 3, 3, '2026-03-16', 3.00, 'Comentarios del Dossier y Formato de Dureza antes y después de alivio termico', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-17 18:13:16', '2026-03-17 18:13:16'),
(669, 9, 13, '2026-03-13', 8.00, 'Actualización sistema ASME 2026', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-30 19:31:13', '2026-03-30 19:31:13'),
(670, 9, 13, '2026-03-14', 5.00, 'Actualización sistema asme 2026', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-30 19:32:29', '2026-03-30 19:32:29'),
(671, 9, 14, '2026-03-18', 8.00, 'Inspección de roscas de tapones para informe preliminar del cliente y revisión de informe ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-30 19:57:48', '2026-03-30 19:57:48'),
(672, 9, 14, '2026-03-16', 7.00, 'inspección de roscas para aeroenfriadores ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-30 19:59:39', '2026-03-30 19:59:39'),
(673, 9, 13, '2026-03-19', 2.00, 'actualización sistema asme 2026', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-30 20:03:11', '2026-03-30 20:03:11'),
(674, 9, 4, '2026-03-19', 2.00, 'Inspecciones y documentación reactor', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-30 20:03:11', '2026-03-30 20:03:11'),
(675, 9, 14, '2026-03-19', 4.00, 'inspecciones en aeroenfriadores ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-30 20:03:11', '2026-03-30 20:03:11'),
(676, 9, 4, '2026-03-20', 4.00, 'inspecciones y documentación reactor ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-30 20:03:54', '2026-03-30 20:03:54'),
(677, 9, 13, '2026-03-21', 2.00, 'Reunión de socialización de sistema con el equipo de trabajo (ingeniería- calidad)', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-30 20:05:52', '2026-03-30 20:05:52'),
(678, 9, 4, '2026-03-21', 3.00, 'Revisión de registros de radiografías ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-30 20:05:52', '2026-03-30 20:05:52'),
(679, 9, 4, '2026-03-24', 8.00, 'Revisión de documentación de reactor', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-30 20:23:08', '2026-03-30 20:23:08'),
(680, 9, 4, '2026-03-25', 8.00, 'Revisión de documentación para reactor ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-30 20:23:44', '2026-03-30 20:23:44'),
(681, 9, 4, '2026-03-26', 8.00, 'Revisión de documentos y preparación de calificación de procedimiento de soldadura de sello ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-30 20:25:55', '2026-03-30 20:25:55'),
(682, 9, 4, '2026-03-27', 8.00, 'revisión de documentos, inspecciones de soldadura y preparación de calificación de procedimiento de soldadura de sello ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-30 20:27:36', '2026-03-30 20:27:36'),
(683, 9, 4, '2026-03-28', 5.00, 'realizar prueba con edilson de calificacion de procedimiento ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-30 20:28:11', '2026-03-30 20:28:11'),
(684, 4, 4, '2026-03-17', 2.00, 'Ensayo no destructivo de liquidos penetrantes ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 12:48:46', '2026-03-31 12:48:46'),
(685, 4, 14, '2026-03-17', 6.00, 'inspeccion y verificación de tapones', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 12:48:46', '2026-03-31 12:48:46'),
(686, 4, 14, '2026-03-18', 8.00, 'inspección y verificación de tapones', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 12:49:32', '2026-03-31 12:49:32'),
(687, 4, 14, '2026-03-19', 8.00, 'paso de machuelo por las roscas de los cabezales', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 12:50:30', '2026-03-31 12:50:30'),
(688, 4, 14, '2026-03-20', 8.00, 'paso de machuelo por las roscas de los cabezales', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 12:51:00', '2026-03-31 12:51:00'),
(689, 4, 14, '2026-03-21', 5.00, 'realización layout ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 12:51:42', '2026-03-31 12:51:42'),
(690, 4, 4, '2026-03-23', 7.00, 'inspección verificacion corte laminas Monel', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 12:53:13', '2026-03-31 12:53:13'),
(691, 4, 4, '2026-03-24', 4.00, 'Aplicación liquidos penetrantes parte Monel', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 12:55:28', '2026-03-31 12:55:28'),
(692, 4, 14, '2026-03-24', 4.00, 'inspeccion de roscas de mayor diametro en los cabezales', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 12:55:29', '2026-03-31 12:55:29'),
(693, 4, 14, '2026-03-25', 8.00, 'Marcado de roscas mayor diametro en los cabezales', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 12:56:11', '2026-03-31 12:56:11'),
(694, 4, 4, '2026-03-26', 4.00, 'Aplicación tintas Monel', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 12:58:44', '2026-03-31 12:58:44'),
(695, 4, 14, '2026-03-26', 4.00, 'Inspección retirado de arandelas de los cabezales', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 12:58:44', '2026-03-31 12:58:44'),
(696, 4, 4, '2026-03-27', 4.00, 'Inspección de los componentes del reactor', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 13:00:26', '2026-03-31 13:00:26'),
(697, 4, 14, '2026-03-27', 4.00, 'Inspección', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 13:00:26', '2026-03-31 13:00:26'),
(698, 4, 6, '2026-03-28', 2.00, 'Armado dossier fisico', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 13:01:45', '2026-03-31 13:01:45'),
(699, 4, 5, '2026-03-28', 2.00, 'armado dossier fisico', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 13:01:45', '2026-03-31 13:01:45'),
(700, 4, 3, '2026-03-28', 1.00, 'armado dossier fisico', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 13:01:45', '2026-03-31 13:01:45'),
(701, 4, 5, '2026-03-30', 5.00, 'Armado dossier fisico', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 13:03:06', '2026-03-31 13:03:06'),
(702, 4, 4, '2026-03-30', 2.00, 'Aplicación de tintas penetrantes Monel', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 13:03:06', '2026-03-31 13:03:06'),
(703, 3, 3, '2026-03-31', 4.00, 'Conformación de Análisis de Riesgo para cambio de manómetros en Bodega GRB', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 19:17:43', '2026-03-31 19:17:43'),
(704, 3, 3, '2026-03-30', 4.00, 'Conformación de Procedimiento de cambio de manómetros en bodega GRB', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 19:19:37', '2026-03-31 19:19:37'),
(705, 3, 10, '2026-03-30', 3.00, 'Conformación del Informe Anual (2025) para junta de socios', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 19:19:37', '2026-03-31 19:19:37'),
(706, 3, 4, '2026-03-28', 5.00, 'Reunión del Equipo de Ingeniería con el Equipo de calidad para la revisión y distribución de pendientes del reactor\r\nFirma de lista de chequeo del reactor', NULL, 'registrado', 1, NULL, NULL, NULL, '2026-03-31 19:21:21', '2026-03-31 19:31:26'),
(707, 3, 10, '2026-03-27', 8.00, 'Recibo e inducción para el puesto de HSEQ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 19:24:09', '2026-03-31 19:24:09'),
(708, 3, 10, '2026-03-26', 8.00, 'Recibo e inducción para el puesto de HSEQ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 19:25:05', '2026-03-31 19:25:05'),
(709, 3, 10, '2026-03-25', 8.00, 'Recibo e inducción para el puesto de HSEQ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 19:25:20', '2026-03-31 19:25:20'),
(710, 3, 10, '2026-03-24', 8.00, 'Recibo e inducción para el puesto de HSEQ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 19:25:38', '2026-03-31 19:25:38'),
(711, 3, 10, '2026-03-23', 7.00, 'Recibo e inducción para el puesto de HSEQ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 19:25:54', '2026-03-31 19:25:54'),
(712, 3, 3, '2026-03-17', 8.00, 'Conformación de Dossier Digital Ecopetrol', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 19:28:40', '2026-03-31 19:28:40'),
(713, 3, 10, '2026-03-18', 8.00, 'Conformación de Dossier Digital Ecopetrol', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 19:29:12', '2026-03-31 19:29:12'),
(714, 3, 3, '2026-03-19', 8.00, 'Conformación de Dossier Digital Ecopetrol', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 19:29:44', '2026-03-31 19:29:44'),
(715, 3, 3, '2026-03-20', 8.00, 'Conformación de Dossier Digital Ecopetrol', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 19:30:00', '2026-03-31 19:30:00'),
(716, 3, 3, '2026-03-21', 5.00, 'Conformación de Dossier Digital Ecopetrol', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-03-31 19:32:24', '2026-03-31 19:32:24'),
(717, 4, 5, '2026-03-31', 4.00, 'Dossier fisico', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-01 12:39:32', '2026-04-01 12:39:32'),
(718, 4, 14, '2026-03-31', 4.00, 'Inspección tuberia aleteada, y corte de laminas', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-01 12:39:32', '2026-04-01 12:39:32'),
(719, 3, 14, '2026-03-31', 4.00, 'Seguimiento de las actividades de producción. Inspección de EPPs ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-01 15:09:11', '2026-04-01 15:09:11'),
(720, 4, 3, '2026-04-01', 3.00, 'Dossier fisico', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-01 21:53:08', '2026-04-01 21:53:08'),
(721, 4, 6, '2026-04-01', 3.00, 'dossier fisico', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-01 21:53:08', '2026-04-01 21:53:08'),
(722, 4, 4, '2026-04-01', 2.00, 'medicion de espesores componente interno', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-01 21:53:08', '2026-04-01 21:53:08'),
(723, 4, 14, '2026-04-06', 7.00, 'Inspección corte de laminas, inspección extracción de casquillos de cabezales reentube ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-08 15:06:32', '2026-04-08 15:06:32'),
(724, 4, 14, '2026-04-07', 8.00, 'extraccion de casquillos cabezal flotante, paso de rima y medición agujeros', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-08 15:08:43', '2026-04-08 15:08:43'),
(725, 4, 14, '2026-04-08', 6.00, 'inspección corte biselado de laminas busqueda de laminas con codigo de trazabilidad ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-09 12:36:14', '2026-04-09 12:36:14'),
(726, 4, 4, '2026-04-08', 2.00, 'inspección soldadura monel', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-09 12:36:14', '2026-04-09 12:36:14'),
(727, 9, 14, '2026-03-30', 4.00, 'revisión de estado de la tubería ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-09 20:16:01', '2026-04-09 20:16:01'),
(728, 9, 4, '2026-03-30', 3.00, 'validación de certificados de materiales ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-09 20:16:01', '2026-04-09 20:16:01'),
(729, 9, 4, '2026-03-31', 8.00, 'Revisión de documentación de reactor ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-09 20:22:15', '2026-04-09 20:22:15'),
(730, 9, 4, '2026-04-01', 8.00, 'Revisión documental de reactor, revisión de soldaduras ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-09 20:27:09', '2026-04-09 20:27:09'),
(731, 9, 14, '2026-04-06', 5.00, 'Inspección y metrología de agujeros de cabezal ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-09 20:47:08', '2026-04-09 20:47:08'),
(732, 4, 4, '2026-04-09', 6.00, 'inspección cono de monel y aplicación de tintas penetrantes', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-09 21:57:04', '2026-04-09 21:57:04'),
(733, 4, 14, '2026-04-09', 2.00, 'inspección corte laminas', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-09 21:57:04', '2026-04-09 21:57:04'),
(734, 4, 15, '2026-04-10', 8.00, 'inspección de corte y biselado laminas ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-13 20:25:30', '2026-04-13 20:25:30'),
(735, 4, 16, '2026-04-11', 5.00, 'Inspecció corte y biselado de laminas', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-13 20:26:08', '2026-04-13 20:26:08'),
(736, 4, 15, '2026-04-13', 7.00, 'inspección armado de cabezales', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-13 20:26:40', '2026-04-13 20:26:40'),
(737, 9, 15, '2026-04-06', 2.00, 'Inspección de tubería, cantidades, medición de longitud, diámetros y calibres.', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-14 18:31:22', '2026-04-14 18:31:22'),
(738, 9, 15, '2026-04-14', 2.50, 'Inspecciones de soldadura ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-15 20:09:52', '2026-04-15 20:09:52'),
(739, 9, 16, '2026-04-14', 2.50, 'Inspecciones de soldadura ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-15 20:09:52', '2026-04-15 20:09:52'),
(740, 9, 4, '2026-04-14', 3.00, 'Preparación de documentos para presentar al inspector ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-15 20:09:52', '2026-04-15 20:09:52'),
(741, 9, 14, '2026-04-15', 4.00, 'Inspecciones de soldadura, líquidos penetrantes a soldaduras ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-15 20:11:17', '2026-04-15 20:11:17'),
(742, 9, 4, '2026-04-15', 2.50, 'Preparación de documentación para presentar al inspector ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-15 20:11:17', '2026-04-15 20:11:17'),
(743, 9, 15, '2026-04-13', 2.00, 'Inspecciones de soldadura, revision de documentos, recepción de materiales ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-15 20:17:46', '2026-04-15 20:17:46'),
(744, 9, 16, '2026-04-13', 2.00, 'Inspecciones de soldadura, revision de documentos, recepción de materiales ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-15 20:17:46', '2026-04-15 20:17:46'),
(745, 9, 17, '2026-04-13', 3.00, 'Inspecciones de soldadura, revision de documentos, recepción de materiales ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-15 20:17:46', '2026-04-15 20:17:46'),
(746, 9, 15, '2026-04-07', 3.00, 'Inspecciones y revisión documental ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-16 15:31:21', '2026-04-16 15:31:21'),
(747, 9, 16, '2026-04-07', 3.00, 'Inspecciones y revisión documental ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-16 15:31:21', '2026-04-16 15:31:21'),
(748, 9, 17, '2026-04-07', 2.00, 'Inspecciones y revisión documental ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-16 15:31:21', '2026-04-16 15:31:21'),
(749, 4, 15, '2026-04-14', 3.00, 'inspección aplicación de soldadura', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-16 15:33:45', '2026-04-16 15:33:45'),
(750, 4, 16, '2026-04-14', 3.00, 'inspección aplicación de soldadura', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-16 15:33:45', '2026-04-16 15:33:45'),
(751, 4, 17, '2026-04-14', 2.00, 'inspección aplicación de soldadura', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-16 15:33:45', '2026-04-16 15:33:45'),
(752, 4, 15, '2026-04-15', 4.00, 'inspección aplicación pase de raiz y aplicación liquidos penetrantes', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-16 15:35:08', '2026-04-16 15:35:08'),
(753, 4, 16, '2026-04-15', 2.00, 'inspección aplicación pase de raiz', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-16 15:35:08', '2026-04-16 15:35:08'),
(754, 4, 17, '2026-04-15', 2.00, 'inspección aplicación pase de raiz', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-16 15:35:08', '2026-04-16 15:35:08'),
(755, 9, 14, '2026-04-08', 8.00, 'Inspección de cabezales', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-16 15:35:27', '2026-04-16 15:35:27'),
(756, 9, 13, '2026-04-09', 8.00, 'Revisión de documentación petrotesting de acuerdo a las indicaciones dadas por los inspectores ASME', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-16 19:02:37', '2026-04-16 19:02:37'),
(757, 9, 13, '2026-04-10', 8.00, 'Revisión de documentación petrotesting de acuerdo a las indicaciones dadas por los inspectores ASME', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-16 19:04:46', '2026-04-16 19:04:46'),
(758, 9, 9, '2026-04-11', 5.00, 'Actualización de haces de tubos para parada, de acuerdo a especificaciones de material de tubería y diámetros para realizar requerimientos de la herramienta a utilizar ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-16 19:11:47', '2026-04-16 19:11:47'),
(759, 4, 4, '2026-04-17', 8.00, 'Inspector ASME', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-20 14:24:30', '2026-04-20 14:24:30'),
(760, 4, 4, '2026-04-18', 5.00, 'Inspector asme', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-20 14:26:40', '2026-04-20 14:26:40'),
(761, 9, 16, '2026-04-16', 2.00, 'líquidos penetrantes cabezales', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-20 18:46:27', '2026-04-20 18:46:27'),
(762, 9, 15, '2026-04-16', 2.00, 'líquidos penetrantes cabezales', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-20 18:46:27', '2026-04-20 18:46:27'),
(763, 9, 17, '2026-04-16', 2.00, 'líquidos penetrantes cabezales', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-20 18:46:27', '2026-04-20 18:46:27'),
(764, 9, 4, '2026-04-16', 2.00, 'documentación ASME ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-20 18:46:27', '2026-04-20 18:46:27'),
(765, 9, 4, '2026-04-17', 8.00, 'Atender visita inspector ASME, presentar radiografías, inspecciones de soldaduras ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-20 18:47:49', '2026-04-20 18:47:49'),
(766, 9, 4, '2026-04-18', 5.00, 'Visita inspector ASME, revisión de materiales ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-20 19:18:36', '2026-04-20 19:18:36'),
(767, 4, 15, '2026-04-20', 2.00, 'Aplicación liquidos penetrantes', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-21 13:01:25', '2026-04-21 13:01:25'),
(768, 4, 16, '2026-04-20', 2.00, 'aplicación liquidos penetrantes', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-21 13:01:25', '2026-04-21 13:01:25'),
(769, 4, 17, '2026-04-20', 2.00, 'aplicación liquidos penetrantes', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-21 13:01:25', '2026-04-21 13:01:25'),
(770, 4, 4, '2026-04-20', 1.00, 'revision documentación', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-21 13:01:25', '2026-04-21 13:01:25'),
(771, 4, 15, '2026-04-21', 3.00, 'Inspección soldadura, aplicación liquidos penetrantes', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-25 14:04:23', '2026-04-25 14:04:23'),
(772, 4, 16, '2026-04-21', 3.00, 'Inspección soldadura, aplicación liquidos penetrantes', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-25 14:04:23', '2026-04-25 14:04:23'),
(773, 4, 17, '2026-04-21', 2.00, 'Inspección soldadura, aplicación liquidos penetrantes', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-25 14:04:23', '2026-04-25 14:04:23'),
(774, 4, 17, '2026-04-22', 6.00, 'Inspección soldadura, aplicación liquidos penetrantes, dossier', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-25 14:05:35', '2026-04-25 14:05:35'),
(775, 4, 16, '2026-04-22', 1.00, 'Inspección soldadura', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-25 14:05:35', '2026-04-25 14:05:35'),
(776, 4, 15, '2026-04-22', 1.00, 'Inspección soldadura', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-25 14:05:35', '2026-04-25 14:05:35'),
(777, 4, 16, '2026-04-23', 6.00, 'Inspección soldadura, dossier', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-25 14:09:11', '2026-04-25 14:09:11'),
(778, 4, 15, '2026-04-23', 2.00, 'dossier', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-25 14:09:11', '2026-04-25 14:09:11'),
(779, 4, 16, '2026-04-24', 4.00, 'Inspección soldadura', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-25 14:11:25', '2026-04-25 14:11:25'),
(780, 4, 4, '2026-04-24', 2.00, 'planificación de radiografias', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-25 14:11:25', '2026-04-25 14:11:25'),
(781, 4, 16, '2026-04-24', 2.00, 'Inspección soldadura', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-25 14:11:25', '2026-04-25 14:11:25'),
(782, 4, 4, '2026-04-25', 2.00, 'revisión de radiografias', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-27 14:16:21', '2026-04-27 14:16:21'),
(783, 4, 17, '2026-04-25', 3.00, 'aplicación de liquidos penetrantes', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-27 14:16:21', '2026-04-27 14:16:21'),
(784, 9, 15, '2026-04-20', 6.00, 'Inspecciones de soldadura a cabezales de aeroenfriadores ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-27 20:09:38', '2026-04-27 20:09:38'),
(785, 9, 16, '2026-04-21', 3.00, 'Inspecciones de soldadura a cabezales', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-27 20:11:04', '2026-04-27 20:11:04'),
(786, 9, 17, '2026-04-21', 3.00, 'inspecciones de soldadura a cabezales ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-27 20:11:04', '2026-04-27 20:11:04'),
(787, 9, 15, '2026-04-21', 2.00, 'Inspecciones de soldadura a cabezales ', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-27 20:11:04', '2026-04-27 20:11:04'),
(788, 4, 17, '2026-04-27', 4.00, 'liberación para alivio termico aplicación de tintas penetrantes antes de alivio', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-27 20:49:15', '2026-04-27 20:49:15'),
(789, 4, 16, '2026-04-27', 3.00, 'aplicación de liquidos penetrantes antes de alivio, liberación de soldaduras', NULL, 'registrado', 0, NULL, NULL, NULL, '2026-04-27 20:49:15', '2026-04-27 20:49:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registros_horas_produccion`
--

CREATE TABLE `registros_horas_produccion` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `orden_produccion_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `descripcion` text DEFAULT NULL COMMENT 'Descripción de las actividades realizadas ese día',
  `maquina` varchar(20) DEFAULT NULL COMMENT 'Código de la máquina utilizada',
  `hr` decimal(4,2) DEFAULT 0.00 COMMENT 'Horas regulares',
  `hed` decimal(4,2) DEFAULT 0.00 COMMENT 'Horas extras diurnas',
  `hen` decimal(4,2) DEFAULT 0.00 COMMENT 'Horas extras nocturnas',
  `hefd` decimal(4,2) DEFAULT 0.00 COMMENT 'Horas extras festivas diurnas',
  `hefn` decimal(4,2) DEFAULT 0.00 COMMENT 'Horas extras festivas nocturnas',
  `permiso` varchar(50) DEFAULT NULL COMMENT 'Tipo de permiso si aplica',
  `comida` tinyint(1) DEFAULT 0 COMMENT 'Indicador de comida (1=sí, 0=no)',
  `total_horas` decimal(4,2) DEFAULT 0.00 COMMENT 'Total de horas calculadas',
  `observaciones` text DEFAULT NULL,
  `horario` varchar(50) DEFAULT '7 am - 5 pm' COMMENT 'Horario de trabajo',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `registros_horas_produccion`
--

INSERT INTO `registros_horas_produccion` (`id`, `usuario_id`, `orden_produccion_id`, `fecha`, `descripcion`, `maquina`, `hr`, `hed`, `hen`, `hefd`, `hefn`, `permiso`, `comida`, `total_horas`, `observaciones`, `horario`, `created_at`, `updated_at`) VALUES
(1, 7, 11, '2026-01-01', '', 'MAQ-001', 1.00, 0.00, 0.00, 0.00, 0.00, '', 1, 1.00, '', '7 am - 5 pm', '2026-01-08 05:22:20', '2026-01-08 05:22:20');

--
-- Disparadores `registros_horas_produccion`
--
DELIMITER $$
CREATE TRIGGER `after_registro_horas_produccion_insert_historial` AFTER INSERT ON `registros_horas_produccion` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resumen_diario_horas`
--

CREATE TABLE `resumen_diario_horas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `horas_normales` decimal(4,2) DEFAULT 0.00,
  `horas_extras` decimal(4,2) DEFAULT 0.00,
  `total_horas` decimal(4,2) DEFAULT 0.00,
  `numero_registros` int(11) DEFAULT 0,
  `ordenes_trabajadas` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sincronizacion_projectdashboard`
--

CREATE TABLE `sincronizacion_projectdashboard` (
  `id` int(11) NOT NULL,
  `tipo_registro` enum('horas_normales','horas_extras') NOT NULL,
  `registro_id` int(11) NOT NULL COMMENT 'ID del registro en registros_horas o solicitudes_horas_extras',
  `usuario_id` int(11) NOT NULL,
  `orden_produccion_id` int(11) NOT NULL,
  `fecha_registro` date NOT NULL,
  `horas_ordinarias` decimal(4,2) DEFAULT 0.00,
  `horas_extras` decimal(4,2) DEFAULT 0.00,
  `total_pagado` decimal(10,2) DEFAULT 0.00,
  `fecha_sincronizacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `sincronizado_por` int(11) NOT NULL,
  `respuesta_api` text DEFAULT NULL COMMENT 'Respuesta del sistema externo si aplica'
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
(192, 'horas_normales', 15, 2, 1, '2025-12-01', 3.50, 0.00, 26250.00, '2025-12-30 21:51:20', 1, 'Enviado via webhook - HTTP 200'),
(193, 'horas_normales', 475, 4, 6, '2026-01-27', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(194, 'horas_normales', 477, 4, 6, '2026-01-27', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(195, 'horas_normales', 476, 4, 5, '2026-01-27', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(196, 'horas_normales', 478, 4, 3, '2026-01-27', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(197, 'horas_normales', 471, 4, 3, '2026-01-26', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(198, 'horas_normales', 472, 4, 5, '2026-01-26', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(199, 'horas_normales', 473, 4, 6, '2026-01-26', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(200, 'horas_normales', 474, 4, 4, '2026-01-26', 1.00, 0.00, 7500.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(201, 'horas_normales', 467, 4, 5, '2026-01-24', 1.00, 0.00, 7500.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(202, 'horas_normales', 468, 4, 6, '2026-01-24', 1.00, 0.00, 7500.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(203, 'horas_normales', 469, 4, 3, '2026-01-24', 1.00, 0.00, 7500.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(204, 'horas_normales', 463, 4, 3, '2026-01-23', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(205, 'horas_normales', 464, 4, 5, '2026-01-23', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(206, 'horas_normales', 465, 4, 6, '2026-01-23', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(207, 'horas_normales', 466, 4, 4, '2026-01-23', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(208, 'horas_normales', 459, 4, 6, '2026-01-22', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(209, 'horas_normales', 462, 4, 6, '2026-01-22', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(210, 'horas_normales', 460, 4, 4, '2026-01-22', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(211, 'horas_normales', 461, 4, 3, '2026-01-22', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(212, 'horas_normales', 452, 4, 5, '2026-01-21', 1.00, 0.00, 7500.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(213, 'horas_normales', 456, 4, 5, '2026-01-21', 1.00, 0.00, 7500.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(214, 'horas_normales', 453, 4, 3, '2026-01-21', 1.00, 0.00, 7500.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(215, 'horas_normales', 458, 4, 3, '2026-01-21', 1.00, 0.00, 7500.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(216, 'horas_normales', 454, 4, 6, '2026-01-21', 1.00, 0.00, 7500.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(217, 'horas_normales', 455, 4, 6, '2026-01-21', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(218, 'horas_normales', 457, 4, 6, '2026-01-21', 1.00, 0.00, 7500.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(219, 'horas_normales', 413, 4, 3, '2026-01-20', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(220, 'horas_normales', 414, 4, 5, '2026-01-20', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(221, 'horas_normales', 415, 4, 6, '2026-01-20', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(222, 'horas_normales', 416, 4, 6, '2026-01-20', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(223, 'horas_normales', 448, 2, 3, '2026-01-20', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(224, 'horas_normales', 449, 2, 5, '2026-01-20', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(225, 'horas_normales', 450, 2, 6, '2026-01-20', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(226, 'horas_normales', 451, 2, 2, '2026-01-20', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(227, 'horas_normales', 410, 4, 3, '2026-01-19', 4.00, 0.00, 30000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(228, 'horas_normales', 411, 4, 6, '2026-01-19', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(229, 'horas_normales', 412, 4, 5, '2026-01-19', 1.00, 0.00, 7500.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(230, 'horas_normales', 444, 2, 3, '2026-01-19', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(231, 'horas_normales', 445, 2, 5, '2026-01-19', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(232, 'horas_normales', 446, 2, 6, '2026-01-19', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(233, 'horas_extras', 19, 4, 6, '2026-01-18', 0.00, 1.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(234, 'horas_normales', 405, 4, 3, '2026-01-17', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(235, 'horas_normales', 406, 4, 5, '2026-01-17', 1.00, 0.00, 7500.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(236, 'horas_normales', 407, 4, 4, '2026-01-17', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(237, 'horas_normales', 409, 3, 3, '2026-01-17', 5.00, 0.00, 37500.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(238, 'horas_normales', 440, 2, 3, '2026-01-17', 1.00, 0.00, 7500.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(239, 'horas_normales', 441, 2, 5, '2026-01-17', 1.00, 0.00, 7500.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(240, 'horas_normales', 442, 2, 6, '2026-01-17', 1.00, 0.00, 7500.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(241, 'horas_normales', 403, 4, 3, '2026-01-16', 4.00, 0.00, 30000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(242, 'horas_normales', 408, 3, 3, '2026-01-16', 8.00, 0.00, 60000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(243, 'horas_extras', 17, 3, 3, '2026-01-16', 0.00, 1.00, 9375.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(244, 'horas_extras', 18, 3, 3, '2026-01-16', 0.00, 5.00, 46875.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(245, 'horas_normales', 437, 2, 3, '2026-01-16', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(246, 'horas_normales', 438, 2, 5, '2026-01-16', 3.00, 0.00, 22500.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(247, 'horas_normales', 439, 2, 6, '2026-01-16', 3.00, 0.00, 22500.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(248, 'horas_extras', 13, 4, 6, '2026-01-16', 0.00, 1.00, 9375.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(249, 'horas_extras', 14, 4, 6, '2026-01-16', 0.00, 1.00, 9375.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(250, 'horas_extras', 15, 4, 4, '2026-01-16', 0.00, 3.00, 30375.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(251, 'horas_extras', 16, 4, 5, '2026-01-16', 0.00, 1.00, 10125.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(252, 'horas_normales', 399, 4, 6, '2026-01-15', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(253, 'horas_normales', 400, 4, 5, '2026-01-15', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(254, 'horas_normales', 401, 4, 3, '2026-01-15', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(255, 'horas_normales', 433, 2, 3, '2026-01-15', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(256, 'horas_normales', 434, 2, 5, '2026-01-15', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(257, 'horas_normales', 435, 2, 6, '2026-01-15', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(258, 'horas_normales', 396, 4, 3, '2026-01-14', 4.00, 0.00, 30000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(259, 'horas_normales', 397, 4, 6, '2026-01-14', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(260, 'horas_normales', 429, 2, 3, '2026-01-14', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(261, 'horas_normales', 430, 2, 5, '2026-01-14', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(262, 'horas_normales', 431, 2, 6, '2026-01-14', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(263, 'horas_normales', 392, 4, 3, '2026-01-13', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(264, 'horas_normales', 393, 4, 5, '2026-01-13', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(265, 'horas_normales', 394, 4, 6, '2026-01-13', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(266, 'horas_normales', 421, 2, 3, '2026-01-12', 2.00, 0.00, 26250.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(267, 'horas_normales', 422, 2, 5, '2026-01-12', 2.00, 0.00, 26250.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(268, 'horas_normales', 423, 2, 6, '2026-01-12', 2.00, 0.00, 26250.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(269, 'horas_normales', 389, 4, 6, '2026-01-10', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(270, 'horas_normales', 390, 4, 5, '2026-01-10', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(271, 'horas_normales', 391, 4, 3, '2026-01-10', 1.00, 0.00, 7500.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(272, 'horas_normales', 417, 2, 3, '2026-01-10', 1.00, 0.00, 7500.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(273, 'horas_normales', 418, 2, 5, '2026-01-10', 1.00, 0.00, 7500.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(274, 'horas_normales', 419, 2, 6, '2026-01-10', 1.00, 0.00, 7500.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(275, 'horas_normales', 368, 4, 3, '2026-01-09', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(276, 'horas_normales', 369, 4, 5, '2026-01-09', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(277, 'horas_normales', 370, 4, 6, '2026-01-09', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(278, 'horas_normales', 371, 4, 4, '2026-01-09', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(279, 'horas_normales', 385, 2, 5, '2026-01-09', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(280, 'horas_normales', 386, 2, 6, '2026-01-09', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(281, 'horas_normales', 387, 2, 3, '2026-01-09', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(282, 'horas_normales', 364, 4, 6, '2026-01-08', 4.00, 0.00, 30000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(283, 'horas_normales', 355, 3, 3, '2026-01-08', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(284, 'horas_normales', 356, 3, 3, '2026-01-08', 1.50, 0.00, 11250.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(285, 'horas_normales', 358, 3, 3, '2026-01-08', 2.50, 0.00, 18750.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(286, 'horas_normales', 381, 2, 3, '2026-01-08', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(287, 'horas_normales', 382, 2, 6, '2026-01-08', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(288, 'horas_normales', 383, 2, 5, '2026-01-08', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(289, 'horas_normales', 359, 4, 3, '2026-01-07', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(290, 'horas_normales', 360, 4, 5, '2026-01-07', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(291, 'horas_normales', 361, 4, 6, '2026-01-07', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(292, 'horas_normales', 351, 3, 3, '2026-01-07', 3.00, 0.00, 22500.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(293, 'horas_normales', 377, 2, 3, '2026-01-07', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(294, 'horas_normales', 378, 2, 6, '2026-01-07', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(295, 'horas_normales', 379, 2, 5, '2026-01-07', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(296, 'horas_normales', 346, 4, 3, '2026-01-06', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(297, 'horas_normales', 347, 4, 5, '2026-01-06', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(298, 'horas_normales', 348, 4, 6, '2026-01-06', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(299, 'horas_normales', 349, 4, 4, '2026-01-06', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(300, 'horas_normales', 344, 3, 3, '2026-01-06', 4.00, 0.00, 30000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(301, 'horas_normales', 373, 2, 3, '2026-01-06', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(302, 'horas_normales', 374, 2, 6, '2026-01-06', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(303, 'horas_normales', 375, 2, 5, '2026-01-06', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(304, 'horas_normales', 340, 4, 5, '2026-01-05', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(305, 'horas_normales', 341, 4, 6, '2026-01-05', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(306, 'horas_normales', 342, 4, 3, '2026-01-05', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(307, 'horas_normales', 343, 4, 4, '2026-01-05', 1.00, 0.00, 7500.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(308, 'horas_normales', 337, 3, 4, '2026-01-05', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(309, 'horas_normales', 338, 3, 3, '2026-01-05', 1.00, 0.00, 7500.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(310, 'horas_normales', 339, 3, 3, '2026-01-05', 4.00, 0.00, 30000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(311, 'horas_normales', 365, 2, 3, '2026-01-05', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(312, 'horas_normales', 366, 2, 6, '2026-01-05', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(313, 'horas_normales', 367, 2, 5, '2026-01-05', 2.00, 0.00, 15000.00, '2026-02-07 13:25:51', 1, 'Enviado via webhook - HTTP 200'),
(314, 'horas_normales', 514, 2, 3, '2026-01-31', 1.50, 0.00, 11250.00, '2026-03-10 15:39:29', 8, 'Enviado via webhook - HTTP 200'),
(315, 'horas_normales', 515, 2, 5, '2026-01-31', 1.50, 0.00, 11250.00, '2026-03-10 15:39:29', 8, 'Enviado via webhook - HTTP 200'),
(316, 'horas_normales', 516, 2, 6, '2026-01-31', 1.50, 0.00, 11250.00, '2026-03-10 15:39:29', 8, 'Enviado via webhook - HTTP 200'),
(317, 'horas_normales', 511, 2, 3, '2026-01-30', 2.50, 0.00, 18750.00, '2026-03-10 15:39:29', 8, 'Enviado via webhook - HTTP 200'),
(318, 'horas_normales', 512, 2, 5, '2026-01-30', 2.50, 0.00, 18750.00, '2026-03-10 15:39:29', 8, 'Enviado via webhook - HTTP 200'),
(319, 'horas_normales', 513, 2, 6, '2026-01-30', 2.50, 0.00, 18750.00, '2026-03-10 15:39:29', 8, 'Enviado via webhook - HTTP 200'),
(320, 'horas_normales', 508, 2, 3, '2026-01-29', 2.50, 0.00, 18750.00, '2026-03-10 15:39:29', 8, 'Enviado via webhook - HTTP 200'),
(321, 'horas_normales', 509, 2, 5, '2026-01-29', 2.50, 0.00, 18750.00, '2026-03-10 15:39:29', 8, 'Enviado via webhook - HTTP 200'),
(322, 'horas_normales', 510, 2, 6, '2026-01-29', 2.50, 0.00, 18750.00, '2026-03-10 15:39:29', 8, 'Enviado via webhook - HTTP 200'),
(323, 'horas_normales', 505, 2, 3, '2026-01-28', 2.50, 0.00, 18750.00, '2026-03-10 15:39:29', 8, 'Enviado via webhook - HTTP 200'),
(324, 'horas_normales', 506, 2, 5, '2026-01-28', 2.50, 0.00, 18750.00, '2026-03-10 15:39:29', 8, 'Enviado via webhook - HTTP 200'),
(325, 'horas_normales', 507, 2, 6, '2026-01-28', 2.50, 0.00, 18750.00, '2026-03-10 15:39:29', 8, 'Enviado via webhook - HTTP 200'),
(326, 'horas_normales', 502, 2, 3, '2026-01-27', 2.50, 0.00, 18750.00, '2026-03-10 15:39:30', 8, 'Enviado via webhook - HTTP 200'),
(327, 'horas_normales', 503, 2, 5, '2026-01-27', 2.50, 0.00, 18750.00, '2026-03-10 15:39:30', 8, 'Enviado via webhook - HTTP 200'),
(328, 'horas_normales', 504, 2, 6, '2026-01-27', 2.50, 0.00, 18750.00, '2026-03-10 15:39:30', 8, 'Enviado via webhook - HTTP 200'),
(329, 'horas_normales', 499, 2, 3, '2026-01-26', 2.00, 0.00, 15000.00, '2026-03-10 15:39:30', 8, 'Enviado via webhook - HTTP 200'),
(330, 'horas_normales', 500, 2, 5, '2026-01-26', 2.00, 0.00, 15000.00, '2026-03-10 15:39:30', 8, 'Enviado via webhook - HTTP 200'),
(331, 'horas_normales', 501, 2, 6, '2026-01-26', 2.00, 0.00, 15000.00, '2026-03-10 15:39:30', 8, 'Enviado via webhook - HTTP 200'),
(332, 'horas_normales', 496, 2, 3, '2026-01-24', 1.50, 0.00, 11250.00, '2026-03-10 15:39:30', 8, 'Enviado via webhook - HTTP 200'),
(333, 'horas_normales', 497, 2, 5, '2026-01-24', 1.50, 0.00, 11250.00, '2026-03-10 15:39:30', 8, 'Enviado via webhook - HTTP 200'),
(334, 'horas_normales', 498, 2, 6, '2026-01-24', 1.50, 0.00, 11250.00, '2026-03-10 15:39:30', 8, 'Enviado via webhook - HTTP 200'),
(335, 'horas_normales', 493, 2, 3, '2026-01-23', 2.50, 0.00, 18750.00, '2026-03-10 15:39:30', 8, 'Enviado via webhook - HTTP 200'),
(336, 'horas_normales', 494, 2, 5, '2026-01-23', 2.50, 0.00, 18750.00, '2026-03-10 15:39:30', 8, 'Enviado via webhook - HTTP 200'),
(337, 'horas_normales', 495, 2, 6, '2026-01-23', 2.50, 0.00, 18750.00, '2026-03-10 15:39:30', 8, 'Enviado via webhook - HTTP 200'),
(338, 'horas_normales', 490, 2, 3, '2026-01-22', 2.50, 0.00, 18750.00, '2026-03-10 15:39:30', 8, 'Enviado via webhook - HTTP 200'),
(339, 'horas_normales', 491, 2, 5, '2026-01-22', 2.50, 0.00, 18750.00, '2026-03-10 15:39:30', 8, 'Enviado via webhook - HTTP 200'),
(340, 'horas_normales', 492, 2, 6, '2026-01-22', 2.50, 0.00, 18750.00, '2026-03-10 15:39:30', 8, 'Enviado via webhook - HTTP 200'),
(341, 'horas_normales', 487, 2, 3, '2026-01-21', 2.50, 0.00, 18750.00, '2026-03-10 15:39:30', 8, 'Enviado via webhook - HTTP 200'),
(342, 'horas_normales', 488, 2, 5, '2026-01-21', 2.50, 0.00, 18750.00, '2026-03-10 15:39:30', 8, 'Enviado via webhook - HTTP 200'),
(343, 'horas_normales', 489, 2, 6, '2026-01-21', 2.50, 0.00, 18750.00, '2026-03-10 15:39:30', 8, 'Enviado via webhook - HTTP 200'),
(344, 'horas_extras', 30, 2, 5, '2026-01-17', 0.00, 1.00, 9375.00, '2026-03-10 15:39:30', 8, 'Enviado via webhook - HTTP 200'),
(345, 'horas_extras', 26, 2, 3, '2026-01-16', 0.00, 1.00, 9375.00, '2026-03-10 15:39:30', 8, 'Enviado via webhook - HTTP 200'),
(346, 'horas_extras', 32, 2, 3, '2026-01-16', 0.00, 1.33, 13466.25, '2026-03-10 15:39:30', 8, 'Enviado via webhook - HTTP 200'),
(347, 'horas_extras', 31, 2, 6, '2026-01-16', 0.00, 1.00, 9375.00, '2026-03-10 15:39:30', 8, 'Enviado via webhook - HTTP 200'),
(348, 'horas_extras', 34, 2, 6, '2026-01-16', 0.00, 1.33, 13466.25, '2026-03-10 15:39:30', 8, 'Enviado via webhook - HTTP 200'),
(349, 'horas_extras', 33, 2, 5, '2026-01-16', 0.00, 1.33, 13466.25, '2026-03-10 15:39:30', 8, 'Enviado via webhook - HTTP 200'),
(350, 'horas_extras', 37, 4, 3, '2026-02-27', 0.00, 5.50, 51562.50, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(351, 'horas_extras', 39, 4, 3, '2026-02-27', 0.00, 1.00, 9375.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(352, 'horas_extras', 40, 4, 3, '2026-02-27', 0.00, 5.50, 51562.50, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(353, 'horas_normales', 553, 4, 3, '2026-02-14', 5.00, 0.00, 37500.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(354, 'horas_extras', 38, 4, 3, '2026-02-14', 0.00, 5.00, 46875.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(355, 'horas_normales', 550, 2, 3, '2026-02-14', 1.50, 0.00, 11250.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(356, 'horas_normales', 551, 2, 5, '2026-02-14', 1.50, 0.00, 11250.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(357, 'horas_normales', 552, 2, 6, '2026-02-14', 1.50, 0.00, 11250.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(358, 'horas_normales', 547, 2, 3, '2026-02-13', 2.50, 0.00, 18750.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(359, 'horas_normales', 548, 2, 5, '2026-02-13', 2.50, 0.00, 18750.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(360, 'horas_normales', 549, 2, 6, '2026-02-13', 2.50, 0.00, 18750.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(361, 'horas_normales', 544, 2, 3, '2026-02-12', 2.50, 0.00, 18750.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(362, 'horas_normales', 545, 2, 5, '2026-02-12', 2.50, 0.00, 18750.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(363, 'horas_normales', 546, 2, 6, '2026-02-12', 2.50, 0.00, 18750.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(364, 'horas_normales', 541, 2, 3, '2026-02-11', 2.50, 0.00, 18750.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(365, 'horas_normales', 542, 2, 5, '2026-02-11', 2.50, 0.00, 18750.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(366, 'horas_normales', 543, 2, 6, '2026-02-11', 2.50, 0.00, 18750.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(367, 'horas_normales', 486, 3, 3, '2026-02-10', 8.00, 0.00, 60000.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(368, 'horas_extras', 23, 3, 3, '2026-02-10', 0.00, 1.00, 9375.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(369, 'horas_extras', 24, 3, 3, '2026-02-10', 0.00, 2.75, 25781.25, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(370, 'horas_normales', 538, 2, 3, '2026-02-10', 2.50, 0.00, 18750.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(371, 'horas_normales', 539, 2, 5, '2026-02-10', 2.50, 0.00, 18750.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(372, 'horas_extras', 27, 2, 5, '2026-02-10', 0.00, 1.00, 9375.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(373, 'horas_normales', 540, 2, 6, '2026-02-10', 2.50, 0.00, 18750.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(374, 'horas_normales', 485, 3, 3, '2026-02-09', 7.00, 0.00, 52500.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(375, 'horas_normales', 535, 2, 3, '2026-02-09', 2.00, 0.00, 15000.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(376, 'horas_normales', 536, 2, 5, '2026-02-09', 2.00, 0.00, 15000.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(377, 'horas_normales', 537, 2, 6, '2026-02-09', 2.00, 0.00, 15000.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(378, 'horas_normales', 484, 3, 3, '2026-02-07', 5.00, 0.00, 37500.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(379, 'horas_normales', 532, 2, 3, '2026-02-07', 1.50, 0.00, 11250.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(380, 'horas_normales', 533, 2, 5, '2026-02-07', 1.50, 0.00, 11250.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(381, 'horas_normales', 534, 2, 6, '2026-02-07', 1.50, 0.00, 11250.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(382, 'horas_normales', 483, 3, 3, '2026-02-06', 8.00, 0.00, 60000.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(383, 'horas_normales', 529, 2, 3, '2026-02-06', 2.50, 0.00, 18750.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(384, 'horas_normales', 530, 2, 5, '2026-02-06', 2.50, 0.00, 18750.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(385, 'horas_normales', 531, 2, 6, '2026-02-06', 2.50, 0.00, 18750.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200');
INSERT INTO `sincronizacion_projectdashboard` (`id`, `tipo_registro`, `registro_id`, `usuario_id`, `orden_produccion_id`, `fecha_registro`, `horas_ordinarias`, `horas_extras`, `total_pagado`, `fecha_sincronizacion`, `sincronizado_por`, `respuesta_api`) VALUES
(386, 'horas_normales', 482, 3, 3, '2026-02-05', 8.00, 0.00, 60000.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(387, 'horas_normales', 526, 2, 3, '2026-02-05', 2.50, 0.00, 18750.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(388, 'horas_normales', 527, 2, 5, '2026-02-05', 2.50, 0.00, 18750.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(389, 'horas_normales', 528, 2, 6, '2026-02-05', 2.50, 0.00, 18750.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(390, 'horas_normales', 481, 3, 3, '2026-02-04', 8.00, 0.00, 60000.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(391, 'horas_extras', 22, 3, 3, '2026-02-04', 0.00, 1.00, 9375.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(392, 'horas_extras', 25, 3, 3, '2026-02-04', 0.00, 4.00, 37500.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(393, 'horas_normales', 523, 2, 3, '2026-02-04', 2.50, 0.00, 18750.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(394, 'horas_normales', 524, 2, 5, '2026-02-04', 2.50, 0.00, 18750.00, '2026-03-10 15:40:51', 8, 'Enviado via webhook - HTTP 200'),
(395, 'horas_normales', 525, 2, 6, '2026-02-04', 2.50, 0.00, 18750.00, '2026-03-10 15:40:52', 8, 'Enviado via webhook - HTTP 200'),
(396, 'horas_extras', 28, 2, 6, '2026-02-04', 0.00, 1.00, 9375.00, '2026-03-10 15:40:52', 8, 'Enviado via webhook - HTTP 200'),
(397, 'horas_normales', 480, 3, 3, '2026-02-03', 8.00, 0.00, 60000.00, '2026-03-10 15:40:52', 8, 'Enviado via webhook - HTTP 200'),
(398, 'horas_extras', 20, 3, 3, '2026-02-03', 0.00, 1.00, 9375.00, '2026-03-10 15:40:52', 8, 'Enviado via webhook - HTTP 200'),
(399, 'horas_extras', 21, 3, 3, '2026-02-03', 0.00, 7.00, 65625.00, '2026-03-10 15:40:52', 8, 'Enviado via webhook - HTTP 200'),
(400, 'horas_normales', 520, 2, 3, '2026-02-03', 2.50, 0.00, 18750.00, '2026-03-10 15:40:52', 8, 'Enviado via webhook - HTTP 200'),
(401, 'horas_extras', 29, 2, 3, '2026-02-03', 0.00, 1.00, 9375.00, '2026-03-10 15:40:52', 8, 'Enviado via webhook - HTTP 200'),
(402, 'horas_normales', 521, 2, 5, '2026-02-03', 2.50, 0.00, 18750.00, '2026-03-10 15:40:52', 8, 'Enviado via webhook - HTTP 200'),
(403, 'horas_normales', 522, 2, 6, '2026-02-03', 2.50, 0.00, 18750.00, '2026-03-10 15:40:52', 8, 'Enviado via webhook - HTTP 200'),
(404, 'horas_normales', 479, 3, 3, '2026-02-02', 7.00, 0.00, 52500.00, '2026-03-10 15:40:52', 8, 'Enviado via webhook - HTTP 200'),
(405, 'horas_normales', 517, 2, 3, '2026-02-02', 2.00, 0.00, 15000.00, '2026-03-10 15:40:52', 8, 'Enviado via webhook - HTTP 200'),
(406, 'horas_normales', 518, 2, 5, '2026-02-02', 2.00, 0.00, 15000.00, '2026-03-10 15:40:52', 8, 'Enviado via webhook - HTTP 200'),
(407, 'horas_normales', 519, 2, 6, '2026-02-02', 2.00, 0.00, 15000.00, '2026-03-10 15:40:52', 8, 'Enviado via webhook - HTTP 200'),
(408, 'horas_normales', 656, 4, 4, '2026-03-13', 2.00, 0.00, 15000.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(409, 'horas_normales', 657, 2, 3, '2026-03-13', 2.50, 0.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(410, 'horas_normales', 658, 2, 5, '2026-03-13', 2.50, 0.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(411, 'horas_normales', 659, 2, 6, '2026-03-13', 2.50, 0.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(412, 'horas_normales', 653, 3, 3, '2026-03-12', 4.00, 0.00, 30000.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(413, 'horas_normales', 654, 3, 3, '2026-03-12', 4.00, 0.00, 30000.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(414, 'horas_normales', 646, 2, 5, '2026-03-12', 2.50, 0.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(415, 'horas_normales', 647, 2, 6, '2026-03-12', 2.50, 0.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(416, 'horas_normales', 648, 2, 3, '2026-03-12', 2.50, 0.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(417, 'horas_normales', 572, 4, 4, '2026-03-11', 2.00, 0.00, 15000.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(418, 'horas_normales', 574, 3, 3, '2026-03-11', 8.00, 0.00, 60000.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(419, 'horas_normales', 643, 2, 3, '2026-03-11', 2.50, 0.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(420, 'horas_normales', 644, 2, 6, '2026-03-11', 2.50, 0.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(421, 'horas_normales', 645, 2, 5, '2026-03-11', 2.50, 0.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(422, 'horas_normales', 570, 4, 4, '2026-03-10', 4.00, 0.00, 30000.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(423, 'horas_normales', 575, 3, 3, '2026-03-10', 8.00, 0.00, 60000.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(424, 'horas_normales', 640, 2, 6, '2026-03-10', 2.50, 0.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(425, 'horas_normales', 641, 2, 5, '2026-03-10', 2.50, 0.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(426, 'horas_normales', 642, 2, 5, '2026-03-10', 2.50, 0.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(427, 'horas_normales', 567, 4, 3, '2026-03-09', 2.00, 0.00, 15000.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(428, 'horas_normales', 568, 4, 5, '2026-03-09', 2.00, 0.00, 15000.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(429, 'horas_normales', 569, 4, 4, '2026-03-09', 3.00, 0.00, 22500.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(430, 'horas_normales', 576, 3, 3, '2026-03-09', 7.00, 0.00, 52500.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(431, 'horas_normales', 637, 2, 3, '2026-03-09', 2.00, 0.00, 15000.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(432, 'horas_normales', 638, 2, 5, '2026-03-09', 2.50, 0.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(433, 'horas_normales', 639, 2, 6, '2026-03-09', 2.50, 0.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(434, 'horas_normales', 562, 4, 3, '2026-03-07', 1.00, 0.00, 7500.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(435, 'horas_normales', 563, 4, 5, '2026-03-07', 2.00, 0.00, 15000.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(436, 'horas_normales', 564, 4, 6, '2026-03-07', 2.00, 0.00, 15000.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(437, 'horas_extras', 48, 4, 6, '2026-03-07', 0.00, 4.75, 44531.25, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(438, 'horas_normales', 577, 3, 3, '2026-03-07', 5.00, 0.00, 37500.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(439, 'horas_normales', 634, 2, 3, '2026-03-07', 1.50, 0.00, 11250.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(440, 'horas_normales', 635, 2, 5, '2026-03-07', 1.50, 0.00, 11250.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(441, 'horas_normales', 636, 2, 6, '2026-03-07', 1.50, 0.00, 11250.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(442, 'horas_normales', 559, 4, 6, '2026-03-06', 8.00, 0.00, 60000.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(443, 'horas_extras', 47, 4, 6, '2026-03-06', 0.00, 1.50, 14062.50, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(444, 'horas_normales', 578, 3, 3, '2026-03-06', 8.00, 0.00, 60000.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(445, 'horas_extras', 50, 3, 3, '2026-03-06', 0.00, 1.00, 9375.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(446, 'horas_extras', 51, 3, 3, '2026-03-06', 0.00, 5.50, 51562.50, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(447, 'horas_normales', 622, 2, 3, '2026-03-06', 2.50, 0.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(448, 'horas_normales', 623, 2, 6, '2026-03-06', 2.50, 0.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(449, 'horas_extras', 56, 2, 6, '2026-03-06', 0.00, 3.50, 35437.50, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(450, 'horas_normales', 624, 2, 5, '2026-03-06', 2.50, 0.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(451, 'horas_extras', 54, 2, 5, '2026-03-06', 0.00, 1.00, 9375.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(452, 'horas_extras', 55, 2, 5, '2026-03-06', 0.00, 2.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(453, 'horas_normales', 558, 4, 3, '2026-03-05', 8.00, 0.00, 60000.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(454, 'horas_extras', 45, 4, 3, '2026-03-05', 0.00, 1.00, 9375.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(455, 'horas_extras', 46, 4, 3, '2026-03-05', 0.00, 2.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(456, 'horas_normales', 579, 3, 3, '2026-03-05', 8.00, 0.00, 60000.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(457, 'horas_extras', 49, 3, 3, '2026-03-05', 0.00, 2.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(458, 'horas_normales', 619, 2, 3, '2026-03-05', 2.50, 0.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(459, 'horas_extras', 52, 2, 3, '2026-03-05', 0.00, 1.00, 9375.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(460, 'horas_extras', 53, 2, 3, '2026-03-05', 0.00, 2.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(461, 'horas_normales', 620, 2, 6, '2026-03-05', 2.50, 0.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(462, 'horas_normales', 621, 2, 5, '2026-03-05', 2.50, 0.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(463, 'horas_normales', 557, 4, 6, '2026-03-04', 8.00, 0.00, 60000.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(464, 'horas_normales', 580, 3, 3, '2026-03-04', 8.00, 0.00, 60000.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(465, 'horas_normales', 631, 2, 3, '2026-03-04', 2.50, 0.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(466, 'horas_normales', 632, 2, 6, '2026-03-04', 2.50, 0.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(467, 'horas_normales', 633, 2, 5, '2026-03-04', 2.50, 0.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(468, 'horas_normales', 555, 4, 3, '2026-03-03', 4.00, 0.00, 30000.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(469, 'horas_normales', 556, 4, 5, '2026-03-03', 4.00, 0.00, 30000.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(470, 'horas_normales', 581, 3, 3, '2026-03-03', 8.00, 0.00, 60000.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(471, 'horas_normales', 628, 2, 3, '2026-03-03', 2.50, 0.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(472, 'horas_normales', 629, 2, 6, '2026-03-03', 2.50, 0.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(473, 'horas_normales', 630, 2, 5, '2026-03-03', 2.50, 0.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(474, 'horas_normales', 554, 4, 3, '2026-03-02', 7.00, 0.00, 52500.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(475, 'horas_extras', 43, 4, 3, '2026-03-02', 0.00, 2.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(476, 'horas_normales', 582, 3, 3, '2026-03-02', 7.00, 0.00, 52500.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(477, 'horas_normales', 625, 2, 3, '2026-03-02', 2.00, 0.00, 15000.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(478, 'horas_normales', 626, 2, 5, '2026-03-02', 2.50, 0.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(479, 'horas_normales', 627, 2, 6, '2026-03-02', 2.50, 0.00, 18750.00, '2026-03-14 14:46:59', 1, 'Enviado via webhook - HTTP 200'),
(480, 'horas_normales', 667, 3, 4, '2026-03-16', 4.00, 0.00, 30000.00, '2026-03-17 20:23:13', 1, 'Enviado via webhook - HTTP 200'),
(481, 'horas_normales', 668, 3, 3, '2026-03-16', 3.00, 0.00, 22500.00, '2026-03-17 20:23:13', 1, 'Enviado via webhook - HTTP 200'),
(482, 'horas_normales', 661, 4, 4, '2026-03-14', 1.00, 0.00, 7500.00, '2026-03-17 20:23:13', 1, 'Enviado via webhook - HTTP 200'),
(483, 'horas_normales', 663, 3, 3, '2026-03-14', 5.00, 0.00, 37500.00, '2026-03-17 20:23:13', 1, 'Enviado via webhook - HTTP 200'),
(484, 'horas_normales', 664, 2, 3, '2026-03-14', 1.50, 0.00, 11250.00, '2026-03-17 20:23:13', 1, 'Enviado via webhook - HTTP 200'),
(485, 'horas_normales', 665, 2, 5, '2026-03-14', 1.50, 0.00, 11250.00, '2026-03-17 20:23:13', 1, 'Enviado via webhook - HTTP 200'),
(486, 'horas_normales', 666, 2, 6, '2026-03-14', 1.50, 0.00, 11250.00, '2026-03-17 20:23:13', 1, 'Enviado via webhook - HTTP 200'),
(487, 'horas_normales', 662, 3, 3, '2026-03-13', 8.00, 0.00, 60000.00, '2026-03-17 20:23:13', 1, 'Enviado via webhook - HTTP 200'),
(488, 'horas_normales', 616, 2, 3, '2026-02-28', 1.50, 0.00, 11250.00, '2026-03-21 12:40:22', 10, 'Enviado via webhook - HTTP 200'),
(489, 'horas_normales', 617, 2, 5, '2026-02-28', 1.50, 0.00, 11250.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(490, 'horas_normales', 618, 2, 6, '2026-02-28', 1.50, 0.00, 11250.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(491, 'horas_extras', 41, 4, 5, '2026-02-28', 0.00, 5.50, 51562.50, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(492, 'horas_extras', 42, 4, 5, '2026-02-28', 0.00, 3.50, 35437.50, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(493, 'horas_normales', 613, 2, 3, '2026-02-27', 2.50, 0.00, 18750.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(494, 'horas_normales', 614, 2, 6, '2026-02-27', 2.50, 0.00, 18750.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(495, 'horas_normales', 615, 2, 5, '2026-02-27', 2.50, 0.00, 18750.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(496, 'horas_normales', 610, 2, 3, '2026-02-26', 2.50, 0.00, 18750.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(497, 'horas_normales', 611, 2, 6, '2026-02-26', 2.50, 0.00, 18750.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(498, 'horas_normales', 612, 2, 5, '2026-02-26', 2.50, 0.00, 18750.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(499, 'horas_normales', 607, 2, 3, '2026-02-25', 2.50, 0.00, 18750.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(500, 'horas_normales', 608, 2, 6, '2026-02-25', 2.50, 0.00, 18750.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(501, 'horas_normales', 609, 2, 5, '2026-02-25', 2.50, 0.00, 18750.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(502, 'horas_normales', 604, 2, 3, '2026-02-24', 2.50, 0.00, 18750.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(503, 'horas_normales', 605, 2, 5, '2026-02-24', 2.50, 0.00, 18750.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(504, 'horas_normales', 606, 2, 6, '2026-02-24', 2.50, 0.00, 18750.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(505, 'horas_normales', 601, 2, 3, '2026-02-23', 2.00, 0.00, 15000.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(506, 'horas_normales', 602, 2, 5, '2026-02-23', 2.50, 0.00, 18750.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(507, 'horas_normales', 603, 2, 6, '2026-02-23', 2.50, 0.00, 18750.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(508, 'horas_normales', 598, 2, 3, '2026-02-21', 1.50, 0.00, 11250.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(509, 'horas_normales', 599, 2, 5, '2026-02-21', 1.50, 0.00, 11250.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(510, 'horas_normales', 600, 2, 6, '2026-02-21', 1.50, 0.00, 11250.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(511, 'horas_normales', 595, 2, 3, '2026-02-20', 2.50, 0.00, 18750.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(512, 'horas_normales', 596, 2, 5, '2026-02-20', 2.50, 0.00, 18750.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(513, 'horas_normales', 597, 2, 6, '2026-02-20', 2.50, 0.00, 18750.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(514, 'horas_normales', 592, 2, 3, '2026-02-19', 2.50, 0.00, 18750.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(515, 'horas_normales', 593, 2, 5, '2026-02-19', 2.50, 0.00, 18750.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(516, 'horas_normales', 594, 2, 6, '2026-02-19', 2.50, 0.00, 18750.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(517, 'horas_normales', 589, 2, 3, '2026-02-18', 2.50, 0.00, 18750.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(518, 'horas_normales', 590, 2, 6, '2026-02-18', 2.50, 0.00, 18750.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(519, 'horas_normales', 591, 2, 5, '2026-02-18', 2.50, 0.00, 18750.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(520, 'horas_normales', 586, 2, 3, '2026-02-17', 2.50, 0.00, 18750.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(521, 'horas_normales', 587, 2, 6, '2026-02-17', 2.50, 0.00, 18750.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(522, 'horas_normales', 588, 2, 5, '2026-02-17', 2.50, 0.00, 18750.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(523, 'horas_normales', 583, 2, 3, '2026-02-16', 2.00, 0.00, 15000.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(524, 'horas_normales', 584, 2, 5, '2026-02-16', 2.50, 0.00, 18750.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(525, 'horas_normales', 585, 2, 6, '2026-02-16', 2.50, 0.00, 18750.00, '2026-03-21 12:40:23', 10, 'Enviado via webhook - HTTP 200'),
(526, 'horas_normales', 701, 4, 5, '2026-03-30', 5.00, 0.00, 37500.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(527, 'horas_normales', 702, 4, 4, '2026-03-30', 2.00, 0.00, 15000.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(528, 'horas_normales', 704, 3, 3, '2026-03-30', 4.00, 0.00, 30000.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(529, 'horas_normales', 698, 4, 6, '2026-03-28', 2.00, 0.00, 15000.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(530, 'horas_normales', 699, 4, 5, '2026-03-28', 2.00, 0.00, 15000.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(531, 'horas_normales', 700, 4, 3, '2026-03-28', 1.00, 0.00, 7500.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(532, 'horas_normales', 706, 3, 4, '2026-03-28', 5.00, 0.00, 37500.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(533, 'horas_normales', 683, 9, 4, '2026-03-28', 5.00, 0.00, 64375.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(534, 'horas_normales', 696, 4, 4, '2026-03-27', 4.00, 0.00, 30000.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(535, 'horas_normales', 697, 4, 14, '2026-03-27', 4.00, 0.00, 30000.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(536, 'horas_normales', 682, 9, 4, '2026-03-27', 8.00, 0.00, 103000.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(537, 'horas_normales', 694, 4, 4, '2026-03-26', 4.00, 0.00, 30000.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(538, 'horas_normales', 695, 4, 14, '2026-03-26', 4.00, 0.00, 30000.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(539, 'horas_normales', 681, 9, 4, '2026-03-26', 8.00, 0.00, 103000.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(540, 'horas_normales', 693, 4, 14, '2026-03-25', 8.00, 0.00, 60000.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(541, 'horas_normales', 680, 9, 4, '2026-03-25', 8.00, 0.00, 103000.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(542, 'horas_normales', 691, 4, 4, '2026-03-24', 4.00, 0.00, 30000.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(543, 'horas_normales', 692, 4, 14, '2026-03-24', 4.00, 0.00, 30000.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(544, 'horas_normales', 679, 9, 4, '2026-03-24', 8.00, 0.00, 103000.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(545, 'horas_normales', 690, 4, 4, '2026-03-23', 7.00, 0.00, 91875.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(546, 'horas_normales', 689, 4, 14, '2026-03-21', 5.00, 0.00, 37500.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(547, 'horas_normales', 716, 3, 3, '2026-03-21', 5.00, 0.00, 37500.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(548, 'horas_normales', 678, 9, 4, '2026-03-21', 3.00, 0.00, 38625.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(549, 'horas_normales', 688, 4, 14, '2026-03-20', 8.00, 0.00, 60000.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(550, 'horas_normales', 715, 3, 3, '2026-03-20', 8.00, 0.00, 60000.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(551, 'horas_normales', 676, 9, 4, '2026-03-20', 4.00, 0.00, 51500.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(552, 'horas_normales', 687, 4, 14, '2026-03-19', 8.00, 0.00, 60000.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(553, 'horas_normales', 714, 3, 3, '2026-03-19', 8.00, 0.00, 60000.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(554, 'horas_normales', 674, 9, 4, '2026-03-19', 2.00, 0.00, 25750.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(555, 'horas_normales', 675, 9, 14, '2026-03-19', 4.00, 0.00, 51500.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(556, 'horas_normales', 686, 4, 14, '2026-03-18', 8.00, 0.00, 60000.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(557, 'horas_normales', 671, 9, 14, '2026-03-18', 8.00, 0.00, 103000.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(558, 'horas_normales', 684, 4, 4, '2026-03-17', 2.00, 0.00, 15000.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(559, 'horas_normales', 685, 4, 14, '2026-03-17', 6.00, 0.00, 45000.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(560, 'horas_normales', 712, 3, 3, '2026-03-17', 8.00, 0.00, 60000.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(561, 'horas_normales', 672, 9, 14, '2026-03-16', 7.00, 0.00, 90125.00, '2026-04-06 19:05:17', 1, 'Enviado via webhook - HTTP 200'),
(562, 'horas_normales', 717, 4, 5, '2026-03-31', 4.00, 0.00, 30000.00, '2026-04-09 19:08:04', 10, 'Enviado via webhook - HTTP 200'),
(563, 'horas_normales', 718, 4, 14, '2026-03-31', 4.00, 0.00, 30000.00, '2026-04-09 19:08:04', 10, 'Enviado via webhook - HTTP 200'),
(564, 'horas_normales', 703, 3, 3, '2026-03-31', 4.00, 0.00, 30000.00, '2026-04-09 19:08:04', 10, 'Enviado via webhook - HTTP 200'),
(565, 'horas_normales', 719, 3, 14, '2026-03-31', 4.00, 0.00, 30000.00, '2026-04-09 19:08:04', 10, 'Enviado via webhook - HTTP 200'),
(566, 'horas_normales', 725, 4, 14, '2026-04-08', 6.00, 0.00, 45000.00, '2026-04-09 19:09:14', 10, 'Enviado via webhook - HTTP 200'),
(567, 'horas_normales', 726, 4, 4, '2026-04-08', 2.00, 0.00, 15000.00, '2026-04-09 19:09:14', 10, 'Enviado via webhook - HTTP 200'),
(568, 'horas_normales', 724, 4, 14, '2026-04-07', 8.00, 0.00, 60000.00, '2026-04-09 19:09:14', 10, 'Enviado via webhook - HTTP 200'),
(569, 'horas_normales', 723, 4, 14, '2026-04-06', 7.00, 0.00, 52500.00, '2026-04-09 19:09:14', 10, 'Enviado via webhook - HTTP 200'),
(570, 'horas_normales', 720, 4, 3, '2026-04-01', 3.00, 0.00, 22500.00, '2026-04-09 19:09:14', 10, 'Enviado via webhook - HTTP 200'),
(571, 'horas_normales', 721, 4, 6, '2026-04-01', 3.00, 0.00, 22500.00, '2026-04-09 19:09:14', 10, 'Enviado via webhook - HTTP 200'),
(572, 'horas_normales', 722, 4, 4, '2026-04-01', 2.00, 0.00, 15000.00, '2026-04-09 19:09:14', 10, 'Enviado via webhook - HTTP 200'),
(573, 'horas_normales', 767, 4, 15, '2026-04-20', 2.00, 0.00, 15000.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(574, 'horas_normales', 768, 4, 16, '2026-04-20', 2.00, 0.00, 15000.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(575, 'horas_normales', 769, 4, 17, '2026-04-20', 2.00, 0.00, 15000.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(576, 'horas_normales', 770, 4, 4, '2026-04-20', 1.00, 0.00, 7500.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(577, 'horas_normales', 760, 4, 4, '2026-04-18', 5.00, 0.00, 37500.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(578, 'horas_normales', 766, 9, 4, '2026-04-18', 5.00, 0.00, 64375.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(579, 'horas_normales', 759, 4, 4, '2026-04-17', 8.00, 0.00, 60000.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(580, 'horas_normales', 765, 9, 4, '2026-04-17', 8.00, 0.00, 103000.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(581, 'horas_normales', 761, 9, 16, '2026-04-16', 2.00, 0.00, 25750.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(582, 'horas_normales', 762, 9, 15, '2026-04-16', 2.00, 0.00, 25750.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(583, 'horas_normales', 763, 9, 17, '2026-04-16', 2.00, 0.00, 25750.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(584, 'horas_normales', 764, 9, 4, '2026-04-16', 2.00, 0.00, 25750.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(585, 'horas_normales', 752, 4, 15, '2026-04-15', 4.00, 0.00, 30000.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(586, 'horas_normales', 753, 4, 16, '2026-04-15', 2.00, 0.00, 15000.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(587, 'horas_normales', 754, 4, 17, '2026-04-15', 2.00, 0.00, 15000.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(588, 'horas_normales', 741, 9, 14, '2026-04-15', 4.00, 0.00, 51500.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(589, 'horas_normales', 742, 9, 4, '2026-04-15', 2.50, 0.00, 32187.50, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(590, 'horas_normales', 749, 4, 15, '2026-04-14', 3.00, 0.00, 22500.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(591, 'horas_normales', 750, 4, 16, '2026-04-14', 3.00, 0.00, 22500.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(592, 'horas_normales', 751, 4, 17, '2026-04-14', 2.00, 0.00, 15000.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(593, 'horas_normales', 738, 9, 15, '2026-04-14', 2.50, 0.00, 32187.50, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(594, 'horas_normales', 739, 9, 16, '2026-04-14', 2.50, 0.00, 32187.50, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(595, 'horas_normales', 740, 9, 4, '2026-04-14', 3.00, 0.00, 38625.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(596, 'horas_normales', 736, 4, 15, '2026-04-13', 7.00, 0.00, 52500.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(597, 'horas_normales', 743, 9, 15, '2026-04-13', 2.00, 0.00, 25750.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(598, 'horas_normales', 744, 9, 16, '2026-04-13', 2.00, 0.00, 25750.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(599, 'horas_normales', 745, 9, 17, '2026-04-13', 3.00, 0.00, 38625.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(600, 'horas_normales', 735, 4, 16, '2026-04-11', 5.00, 0.00, 37500.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(601, 'horas_normales', 734, 4, 15, '2026-04-10', 8.00, 0.00, 60000.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(602, 'horas_normales', 732, 4, 4, '2026-04-09', 6.00, 0.00, 45000.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(603, 'horas_normales', 733, 4, 14, '2026-04-09', 2.00, 0.00, 15000.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(604, 'horas_normales', 755, 9, 14, '2026-04-08', 8.00, 0.00, 103000.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(605, 'horas_normales', 746, 9, 15, '2026-04-07', 3.00, 0.00, 38625.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(606, 'horas_normales', 747, 9, 16, '2026-04-07', 3.00, 0.00, 38625.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(607, 'horas_normales', 748, 9, 17, '2026-04-07', 2.00, 0.00, 25750.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(608, 'horas_normales', 731, 9, 14, '2026-04-06', 5.00, 0.00, 64375.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(609, 'horas_normales', 737, 9, 15, '2026-04-06', 2.00, 0.00, 25750.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(610, 'horas_normales', 730, 9, 4, '2026-04-01', 8.00, 0.00, 103000.00, '2026-04-23 15:37:49', 10, 'Enviado via webhook - HTTP 200'),
(611, 'horas_normales', 729, 9, 4, '2026-03-31', 8.00, 0.00, 103000.00, '2026-04-23 15:38:27', 10, 'Enviado via webhook - HTTP 200'),
(612, 'horas_normales', 727, 9, 14, '2026-03-30', 4.00, 0.00, 51500.00, '2026-04-23 15:38:27', 10, 'Enviado via webhook - HTTP 200'),
(613, 'horas_normales', 728, 9, 4, '2026-03-30', 3.00, 0.00, 38625.00, '2026-04-23 15:38:27', 10, 'Enviado via webhook - HTTP 200');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_horas_extras`
--

CREATE TABLE `solicitudes_horas_extras` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `orden_produccion_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL COMMENT 'Hora de inicio de las horas extras',
  `hora_fin` time NOT NULL COMMENT 'Hora de finalización de las horas extras',
  `total_horas_extras` decimal(4,2) NOT NULL COMMENT 'Total de horas extras calculadas',
  `descripcion_trabajo` text NOT NULL,
  `estado` enum('pendiente','aprobada','rechazada','cancelada') DEFAULT 'pendiente',
  `aprobado_por` int(11) DEFAULT NULL,
  `fecha_solicitud` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_respuesta` timestamp NULL DEFAULT NULL,
  `comentario_aprobacion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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
(12, 4, 4, '2025-12-29', '17:00:00', '18:00:00', 1.00, '', 'aprobada', 2, '2025-12-29 14:36:12', '2025-12-29 18:01:49', '', '2025-12-29 14:36:12', '2025-12-29 18:01:49'),
(13, 4, 6, '2026-01-16', '12:00:00', '13:00:00', 1.00, 'Inspector asme ', 'aprobada', 2, '2026-01-19 13:29:07', '2026-01-21 18:31:01', '', '2026-01-19 13:29:07', '2026-01-21 18:31:01'),
(14, 4, 6, '2026-01-16', '17:00:00', '18:00:00', 1.00, 'Inspector asme', 'aprobada', 2, '2026-01-19 13:30:59', '2026-01-21 18:31:29', '', '2026-01-19 13:30:59', '2026-01-21 18:31:29'),
(15, 4, 4, '2026-01-16', '19:00:00', '22:00:00', 3.00, 'Inspector asme ', 'aprobada', 2, '2026-01-19 13:31:48', '2026-01-21 18:31:56', '', '2026-01-19 13:31:48', '2026-01-21 18:31:56'),
(16, 4, 5, '2026-01-16', '18:00:00', '19:00:00', 1.00, 'Inspector asme ', 'aprobada', 2, '2026-01-19 13:34:49', '2026-01-21 18:32:25', '', '2026-01-19 13:34:49', '2026-01-21 18:32:25'),
(17, 3, 3, '2026-01-16', '11:00:00', '12:00:00', 1.00, 'Revisión de documento con Inspector ASME.  liberación de materiales.', 'aprobada', 2, '2026-01-19 13:50:36', '2026-01-21 18:32:56', '', '2026-01-19 13:50:36', '2026-01-21 18:32:56'),
(18, 3, 3, '2026-01-16', '17:00:00', '22:00:00', 5.00, '', 'aprobada', 2, '2026-01-19 13:51:14', '2026-01-21 18:34:37', 'Horas destinadas a revisión documental con el inspector autorizado ASME en referencia a los aeroenfriadores E-156 (OP-50.008), E-157 (OP-50.010), E-158 (OP-50.011)', '2026-01-19 13:51:14', '2026-01-21 18:34:37'),
(19, 4, 6, '2026-01-18', '10:20:00', '11:20:00', 1.00, 'Ensayo no destructivo por liquidos penetrantes', 'aprobada', 2, '2026-01-19 20:59:35', '2026-01-21 18:34:53', '', '2026-01-19 20:59:35', '2026-01-21 18:34:53'),
(20, 3, 3, '2026-02-03', '11:00:00', '12:00:00', 1.00, 'Revisión de documentos de Petro testing (nuevo proveedor de RT) Revisión de soldadura de cabezales, revisión radiografías, revisión de documentos', 'aprobada', 2, '2026-02-11 15:29:43', '2026-02-16 14:35:28', '', '2026-02-11 15:29:43', '2026-02-16 14:35:28'),
(21, 3, 3, '2026-02-03', '17:00:00', '00:00:00', 7.00, 'Revisión de documentos de Petro testing (nuevo proveedor de RT) Revisión de soldadura de cabezales, revisión radiografías, revisión de documentos', 'aprobada', 2, '2026-02-11 15:30:44', '2026-02-16 14:35:22', '', '2026-02-11 15:30:44', '2026-02-16 14:35:22'),
(22, 3, 3, '2026-02-04', '11:00:00', '12:00:00', 1.00, 'Revisión de soldadura de cabezales, revisión radiografías, revisión de documentos, WPS, PQR, WPQ', 'aprobada', 2, '2026-02-11 15:34:44', '2026-02-16 14:35:07', '', '2026-02-11 15:34:44', '2026-02-16 14:35:07'),
(23, 3, 3, '2026-02-10', '11:00:00', '12:00:00', 1.00, 'Revisión de placas e informes de RT junto al Inspector ASME, revisión y liberación de soldadura junto al inspector ASME, revisión de documentación proveedor de radiografía, liberación de cabezales para alivio térmico ', 'aprobada', 2, '2026-02-11 18:23:14', '2026-02-16 14:34:44', '', '2026-02-11 18:23:14', '2026-02-16 14:34:44'),
(24, 3, 3, '2026-02-10', '17:00:00', '19:45:00', 2.75, 'Revisión de placas e informes de RT junto al Inspector ASME, revisión y liberación de soldadura junto al inspector ASME, revisión de documentación proveedor de radiografía, liberación de cabezales para alivio térmico ', 'aprobada', 2, '2026-02-11 18:38:02', '2026-02-16 14:34:25', '', '2026-02-11 18:38:02', '2026-02-16 14:34:25'),
(25, 3, 3, '2026-02-04', '17:00:00', '21:00:00', 4.00, 'Revisión de placas e informes de RT junto al Inspector ASME, revisión y liberación de soldadura junto al inspector ASME', 'aprobada', 2, '2026-02-11 18:39:18', '2026-02-16 14:35:01', '', '2026-02-11 18:39:18', '2026-02-16 14:35:01'),
(26, 2, 3, '2026-01-16', '12:00:00', '13:00:00', 1.00, 'Gestión documental y gestión en planta', 'aprobada', 2, '2026-02-16 15:11:14', '2026-02-16 15:21:41', '', '2026-02-16 15:11:14', '2026-02-16 15:21:41'),
(27, 2, 5, '2026-02-10', '12:00:00', '13:00:00', 1.00, 'Gestión documental y gestión en planta', 'aprobada', 2, '2026-02-16 15:12:08', '2026-02-16 15:21:44', '', '2026-02-16 15:12:08', '2026-02-16 15:21:44'),
(28, 2, 6, '2026-02-04', '12:00:00', '13:00:00', 1.00, '', 'aprobada', 2, '2026-02-16 15:12:41', '2026-02-16 15:21:46', '', '2026-02-16 15:12:41', '2026-02-16 15:21:46'),
(29, 2, 3, '2026-02-03', '12:00:00', '13:00:00', 1.00, 'Gestión documental y gestión en planta', 'aprobada', 2, '2026-02-16 15:14:00', '2026-02-16 15:21:48', '', '2026-02-16 15:14:00', '2026-02-16 15:21:48'),
(30, 2, 5, '2026-01-17', '16:00:00', '17:00:00', 1.00, 'Líquidos penetrantes', 'aprobada', 2, '2026-02-16 15:17:34', '2026-02-16 15:21:27', '', '2026-02-16 15:17:34', '2026-02-16 15:21:27'),
(31, 2, 6, '2026-01-16', '17:00:00', '18:00:00', 1.00, 'Gestión documental y gestión en planta', 'aprobada', 2, '2026-02-16 15:18:34', '2026-02-16 15:21:37', '', '2026-02-16 15:18:34', '2026-02-16 15:21:37'),
(32, 2, 3, '2026-01-16', '18:00:00', '19:20:00', 1.33, 'Gestión documental y gestión en planta', 'aprobada', 2, '2026-02-16 15:20:14', '2026-02-16 15:21:34', '', '2026-02-16 15:20:14', '2026-02-16 15:21:34'),
(33, 2, 5, '2026-01-16', '19:20:00', '20:40:00', 1.33, 'Gestión documental y gestión en planta', 'aprobada', 2, '2026-02-16 15:20:50', '2026-02-16 15:21:32', '', '2026-02-16 15:20:50', '2026-02-16 15:21:32'),
(34, 2, 6, '2026-01-16', '20:40:00', '22:00:00', 1.33, 'Gestión documental y gestión en planta', 'aprobada', 2, '2026-02-16 15:21:18', '2026-02-16 15:21:29', '', '2026-02-16 15:21:18', '2026-02-16 15:21:29'),
(35, 4, 3, '2026-03-02', '12:00:00', '18:00:00', 6.00, 'Inspector ASME', 'rechazada', 2, '2026-03-02 15:09:29', '2026-03-02 15:31:11', 'Hora de almuerzo', '2026-03-02 15:09:29', '2026-03-02 15:31:11'),
(36, 4, 3, '2026-03-27', '11:00:00', '13:00:00', 2.00, 'Prueba hidrostatica', 'rechazada', 2, '2026-03-02 15:13:58', '2026-03-02 15:31:04', 'Hora de almuerzo', '2026-03-02 15:13:58', '2026-03-02 15:31:04'),
(37, 4, 3, '2026-02-27', '17:00:00', '22:30:00', 5.50, 'Prueba hidrostatica, documentación asme ', 'aprobada', 2, '2026-03-02 15:15:11', '2026-03-02 15:33:37', '', '2026-03-02 15:15:11', '2026-03-02 15:33:37'),
(38, 4, 3, '2026-02-14', '13:00:00', '18:00:00', 5.00, 'Inspector ASME ', 'aprobada', 2, '2026-03-02 15:50:55', '2026-03-03 14:51:41', '', '2026-03-02 15:50:55', '2026-03-03 14:51:41'),
(39, 4, 3, '2026-02-27', '12:00:00', '13:00:00', 1.00, 'Prueba hidrostatica ', 'aprobada', 2, '2026-03-02 15:51:57', '2026-03-03 14:52:07', '', '2026-03-02 15:51:57', '2026-03-03 14:52:07'),
(40, 4, 3, '2026-02-27', '17:00:00', '22:30:00', 5.50, 'revisión documental ASME ', 'aprobada', 2, '2026-03-02 15:52:52', '2026-03-03 14:52:27', '', '2026-03-02 15:52:52', '2026-03-03 14:52:27'),
(41, 4, 5, '2026-02-28', '12:30:00', '18:00:00', 5.50, 'entubado y mapeo ', 'aprobada', 2, '2026-03-02 15:56:21', '2026-03-12 21:07:52', '', '2026-03-02 15:56:21', '2026-03-12 21:07:52'),
(42, 4, 5, '2026-02-28', '18:00:00', '21:30:00', 3.50, 'revisión documental ASME ', 'aprobada', 2, '2026-03-02 15:57:05', '2026-03-12 21:08:00', '', '2026-03-02 15:57:05', '2026-03-12 21:08:00'),
(43, 4, 3, '2026-03-02', '16:00:00', '18:00:00', 2.00, 'entubado ', 'aprobada', 2, '2026-03-10 15:36:21', '2026-03-12 20:55:11', 'Trabajo de entubado en planta', '2026-03-10 15:36:21', '2026-03-12 20:55:11'),
(44, 4, 5, '2026-03-10', '11:00:00', '13:00:00', 2.00, 'entube- autorizado por Jose Mendoza', 'rechazada', 2, '2026-03-10 15:40:25', '2026-03-12 21:04:50', 'Revisar fecha', '2026-03-10 15:40:25', '2026-03-12 21:04:50'),
(45, 4, 3, '2026-03-05', '12:00:00', '13:00:00', 1.00, 'prueba hidrostatica ', 'aprobada', 2, '2026-03-10 15:43:54', '2026-03-12 20:58:19', '', '2026-03-10 15:43:54', '2026-03-12 20:58:19'),
(46, 4, 3, '2026-03-05', '17:00:00', '19:00:00', 2.00, 'inspector ASME', 'aprobada', 2, '2026-03-10 15:45:06', '2026-03-12 20:56:59', '', '2026-03-10 15:45:06', '2026-03-12 20:56:59'),
(47, 4, 6, '2026-03-06', '17:00:00', '18:30:00', 1.50, 'Inspector ASME', 'aprobada', 2, '2026-03-10 15:48:15', '2026-03-12 21:01:43', '', '2026-03-10 15:48:15', '2026-03-12 21:01:43'),
(48, 4, 6, '2026-03-07', '12:00:00', '16:45:00', 4.75, 'documentacion de dossieres', 'aprobada', 2, '2026-03-10 15:50:54', '2026-03-12 21:03:47', 'de 12 a 5 pm, laboró 4 horas restando la hora del almuerzo', '2026-03-10 15:50:54', '2026-03-12 21:03:47'),
(49, 3, 3, '2026-03-05', '17:00:00', '19:00:00', 2.00, 'Visita del Inspector ASME', 'aprobada', 2, '2026-03-12 12:55:36', '2026-03-12 20:56:48', '', '2026-03-12 12:55:36', '2026-03-12 20:56:48'),
(50, 3, 3, '2026-03-06', '11:00:00', '12:00:00', 1.00, 'Alistamiento de Dossier para revisión ASME', 'aprobada', 2, '2026-03-12 12:58:04', '2026-03-12 21:00:29', '', '2026-03-12 12:58:04', '2026-03-12 21:00:29'),
(51, 3, 3, '2026-03-06', '17:00:00', '22:30:00', 5.50, 'Visita del Inspector ASME', 'aprobada', 2, '2026-03-12 13:07:14', '2026-03-12 21:01:20', '', '2026-03-12 13:07:14', '2026-03-12 21:01:20'),
(52, 2, 3, '2026-03-05', '12:00:00', '13:00:00', 1.00, 'Gestión documental durante visita inspector ASME', 'aprobada', 2, '2026-03-13 13:56:06', '2026-03-13 14:00:10', '', '2026-03-13 13:56:06', '2026-03-13 14:00:10'),
(53, 2, 3, '2026-03-05', '17:00:00', '19:00:00', 2.00, 'Gestión documental durante visita inspector ASME', 'aprobada', 2, '2026-03-13 13:56:34', '2026-03-13 14:00:07', '', '2026-03-13 13:56:34', '2026-03-13 14:00:07'),
(54, 2, 5, '2026-03-06', '12:00:00', '13:00:00', 1.00, 'Gestión documental durante visita inspector ASME', 'aprobada', 2, '2026-03-13 13:57:21', '2026-03-13 14:00:04', '', '2026-03-13 13:57:21', '2026-03-13 14:00:04'),
(55, 2, 5, '2026-03-06', '17:00:00', '19:00:00', 2.00, 'Gestión documental durante visita inspector ASME', 'aprobada', 2, '2026-03-13 13:58:47', '2026-03-13 14:00:01', '', '2026-03-13 13:58:47', '2026-03-13 14:00:01'),
(56, 2, 6, '2026-03-06', '19:00:00', '22:30:00', 3.50, 'Gestión documental durante visita inspector ASME', 'aprobada', 2, '2026-03-13 13:59:19', '2026-03-13 13:59:58', '', '2026-03-13 13:59:19', '2026-03-13 13:59:58'),
(57, 4, 4, '2026-04-17', '12:00:00', '13:00:00', 1.00, 'Inspector asme', 'pendiente', NULL, '2026-04-20 14:25:15', NULL, NULL, '2026-04-20 14:25:15', '2026-04-20 14:25:15'),
(58, 4, 4, '2026-04-17', '17:00:00', '20:00:00', 3.00, 'inspector asme', 'pendiente', NULL, '2026-04-20 14:25:48', NULL, NULL, '2026-04-20 14:25:48', '2026-04-20 14:25:48'),
(59, 4, 4, '2026-04-18', '12:00:00', '13:40:00', 1.67, 'Inspector asme', 'pendiente', NULL, '2026-04-20 14:27:36', NULL, NULL, '2026-04-20 14:27:36', '2026-04-20 14:27:36'),
(60, 9, 4, '2026-04-17', '17:00:00', '19:30:00', 2.50, 'Visita inspector ASME, revisión de materiales reactor', 'pendiente', NULL, '2026-04-20 19:16:16', NULL, NULL, '2026-04-20 19:16:16', '2026-04-20 19:16:16'),
(61, 9, 4, '2026-04-18', '12:30:00', '13:30:00', 1.00, 'Visita inspector ASME revisión de materiales ', 'pendiente', NULL, '2026-04-20 19:17:43', NULL, NULL, '2026-04-20 19:17:43', '2026-04-20 19:17:43'),
(62, 4, 17, '2026-04-25', '12:00:00', '15:00:00', 3.00, 'Aplicación de liquidos penetrantes, inspección y liberación de cabezales', 'pendiente', NULL, '2026-04-27 14:17:54', NULL, NULL, '2026-04-27 14:17:54', '2026-04-27 14:17:54'),
(63, 4, 16, '2026-04-26', '07:00:00', '15:00:00', 8.00, 'Inspección aplicación de soldaduras, aplicación de liquidos penetrantes, inspección de dobles tuberia en U, liberación de soldaduras', 'pendiente', NULL, '2026-04-27 14:20:09', NULL, NULL, '2026-04-27 14:20:09', '2026-04-27 14:20:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre_completo` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('trabajador','administrador') DEFAULT 'trabajador',
  `is_active` tinyint(1) DEFAULT 1,
  `fecha_ingreso` date DEFAULT NULL,
  `departamento_id` int(11) DEFAULT NULL,
  `cargo_id` int(11) DEFAULT NULL COMMENT 'ID del cargo del usuario',
  `valor_hora_base` decimal(10,2) DEFAULT 0.00 COMMENT 'Valor base por hora del empleado',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre_completo`, `username`, `email`, `password`, `rol`, `is_active`, `fecha_ingreso`, `departamento_id`, `cargo_id`, `valor_hora_base`, `created_at`, `updated_at`) VALUES
(1, 'Inyira Arciniegas', 'inyira', 'recursoshumanos@talleresunidosltda.com', '$2y$10$8XQBPtGQO5EUg1AIFD3C3.g5TKYPM/l1ITpLTfUvGtnJ3yhaWR0w6', 'administrador', 1, '2025-11-29', 5, NULL, 0.00, '2025-11-29 15:31:39', '2026-02-28 15:26:45'),
(2, 'Valeria Jaraba Bonilla', 'Valeria', 'ingenieria@talleresunidosltda.com', '$2y$10$lhZCaIp2SILBgPhkRzQBTehPSS0yWbR1GPXnL5N6irDioevAX5ePK', 'administrador', 1, '2025-11-29', 1, 7, 7500.00, '2025-11-29 15:31:39', '2025-12-19 15:19:13'),
(3, 'Jhonatan Sampayo', 'QCC', 'ingenieria2@talleresunidosltda.com', '$2y$10$DNo2IFH7Q4ze4mCH5Kim/.f2JEWjlR1ds4GkQbyIyM4QpEoPEIUeO', 'trabajador', 1, NULL, 2, 3, 7500.00, '2025-11-29 16:48:04', '2025-12-19 15:15:19'),
(4, 'Jesús Suaréz', 'jsuarez', 'jesussuarez071125@gmail.com', '$2y$10$pBVgGyH5PCpxfkqF6n7OJ.7om12PLCgK2tiXAYOwE/H1PmUP9oCre', 'trabajador', 1, NULL, 2, 3, 7500.00, '2025-11-29 16:51:58', '2025-12-19 15:14:59'),
(5, 'Jinella Plata', 'jplata', 'comercial2@talleresunidosltda.com', '$2y$10$747IMs5Yf5RDUzN1I9CeGeBw1VpVP36tMtqyavzOgNxxhd54PxA8W', 'trabajador', 1, NULL, 4, 2, 7500.00, '2025-12-01 15:18:52', '2025-12-19 15:16:54'),
(6, 'Dilan Botello', 'dbotello', 'diedubora0316@gmail.com', '$2y$10$8c3qbaxAwtnfeFK2TIzsx.lWIfFeRANP/ra5761HEI2eXANurMfSa', 'trabajador', 0, NULL, 1, 1, 7500.00, '2025-12-01 20:58:59', '2026-04-16 21:44:11'),
(7, 'Enaldo Jaraba', 'ejaraba', 'ventas@talleresunidosltda.com', '$2y$10$Thrp3A.Tqc4aGdE3i/TiC.hwd5JRfPlJOzbB9SatEpWnMAvrjueem', 'trabajador', 1, NULL, 3, NULL, 10833.00, '2025-12-02 14:00:35', '2025-12-15 15:00:28'),
(8, 'Jonatan Cantillo', 'jcantillo', 'jcantillo6@udi.edu.co', '$2y$10$hTHoSgyfYXuT/6FsGsDD8ukjC/tz8ulkSD06iGBEs4kEINhJCmHpq', 'administrador', 1, NULL, 1, 11, 0.00, '2025-12-16 20:20:54', '2025-12-19 15:18:41'),
(9, 'Merly Sulgey Llanes Salas', 'MERLYLLANES_2026', 'calidad@talleresunidosltda.com', '$2y$10$lxhMbEioRYwSnJPt/oHGHuJhV66jwzMdHrN/UYPAutuqsGj2WEXlS', 'trabajador', 1, '2026-03-01', 2, 3, 12875.00, '2026-03-10 15:21:28', '2026-04-16 21:45:19'),
(10, 'Jose Federico Haad Atuesta', 'FedeHaad', 'fhaad0920@hotmail.com', '$2y$10$Mn5J4bRZaZGwwA72rU9ro.qiOGtWNsDNCUGjf.IQLMJc7yE2QOGj.', 'administrador', 1, NULL, 5, NULL, 0.00, '2026-03-10 15:43:38', '2026-03-10 15:48:36');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_horas_extras_pendientes`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_horas_extras_pendientes` (
`id` int(11)
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
,`dias_pendiente` int(8)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_horas_por_orden`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_horas_por_orden` (
`orden_id` int(11)
,`codigo_op` varchar(50)
,`nombre_producto` varchar(150)
,`estado` enum('activa','en_proceso','completada','cancelada')
,`trabajadores_asignados` bigint(21)
,`total_horas_invertidas` decimal(26,2)
,`numero_registros` bigint(21)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_resumen_mensual`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_resumen_mensual` (
`usuario_id` int(11)
,`nombre_completo` varchar(100)
,`departamento` varchar(100)
,`anio` int(5)
,`mes` int(3)
,`dias_trabajados` bigint(21)
,`total_horas_normales` decimal(26,2)
,`total_horas_extras` decimal(26,2)
,`total_horas` decimal(27,2)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_horas_extras_pendientes`
--
DROP TABLE IF EXISTS `vista_horas_extras_pendientes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_horas_extras_pendientes`  AS SELECT `he`.`id` AS `id`, `he`.`fecha` AS `fecha`, `u`.`nombre_completo` AS `trabajador`, `d`.`nombre` AS `departamento`, `op`.`codigo_op` AS `codigo_op`, `op`.`nombre_producto` AS `nombre_producto`, `he`.`hora_inicio` AS `hora_inicio`, `he`.`hora_fin` AS `hora_fin`, `he`.`total_horas_extras` AS `total_horas_extras`, `he`.`descripcion_trabajo` AS `descripcion_trabajo`, `he`.`fecha_solicitud` AS `fecha_solicitud`, to_days(current_timestamp()) - to_days(`he`.`fecha_solicitud`) AS `dias_pendiente` FROM (((`solicitudes_horas_extras` `he` join `usuarios` `u` on(`he`.`usuario_id` = `u`.`id`)) left join `departamentos` `d` on(`u`.`departamento_id` = `d`.`id`)) join `ordenes_produccion` `op` on(`he`.`orden_produccion_id` = `op`.`id`)) WHERE `he`.`estado` = 'pendiente' ORDER BY `he`.`fecha_solicitud` ASC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_horas_por_orden`
--
DROP TABLE IF EXISTS `vista_horas_por_orden`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_horas_por_orden`  AS SELECT `op`.`id` AS `orden_id`, `op`.`codigo_op` AS `codigo_op`, `op`.`nombre_producto` AS `nombre_producto`, `op`.`estado` AS `estado`, count(distinct `r`.`usuario_id`) AS `trabajadores_asignados`, sum(`r`.`horas_trabajadas`) AS `total_horas_invertidas`, count(`r`.`id`) AS `numero_registros` FROM (`ordenes_produccion` `op` left join `registros_horas` `r` on(`op`.`id` = `r`.`orden_produccion_id`)) GROUP BY `op`.`id` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_resumen_mensual`
--
DROP TABLE IF EXISTS `vista_resumen_mensual`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_resumen_mensual`  AS SELECT `u`.`id` AS `usuario_id`, `u`.`nombre_completo` AS `nombre_completo`, `d`.`nombre` AS `departamento`, year(`r`.`fecha`) AS `anio`, month(`r`.`fecha`) AS `mes`, count(distinct `r`.`fecha`) AS `dias_trabajados`, sum(`r`.`horas_trabajadas`) AS `total_horas_normales`, coalesce(sum(`he`.`total_horas_extras`),0) AS `total_horas_extras`, sum(`r`.`horas_trabajadas`) + coalesce(sum(`he`.`total_horas_extras`),0) AS `total_horas` FROM (((`usuarios` `u` left join `departamentos` `d` on(`u`.`departamento_id` = `d`.`id`)) left join `registros_horas` `r` on(`u`.`id` = `r`.`usuario_id`)) left join `solicitudes_horas_extras` `he` on(`u`.`id` = `he`.`usuario_id` and `he`.`estado` = 'aprobada' and year(`he`.`fecha`) = year(`r`.`fecha`) and month(`he`.`fecha`) = month(`r`.`fecha`))) WHERE `u`.`rol` = 'trabajador' GROUP BY `u`.`id`, year(`r`.`fecha`), month(`r`.`fecha`) ;

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
-- Indices de la tabla `festivos_cache`
--
ALTER TABLE `festivos_cache`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_festivo` (`pais`,`anio`,`fecha`),
  ADD KEY `idx_pais_anio` (`pais`,`anio`),
  ADD KEY `idx_fecha` (`fecha`);

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
-- Indices de la tabla `registros_horas_produccion`
--
ALTER TABLE `registros_horas_produccion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_fecha` (`fecha`),
  ADD KEY `idx_orden` (`orden_produccion_id`),
  ADD KEY `idx_usuario_fecha` (`usuario_id`,`fecha`),
  ADD KEY `idx_maquina` (`maquina`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `configuracion_sistema`
--
ALTER TABLE `configuracion_sistema`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `festivos_cache`
--
ALTER TABLE `festivos_cache`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de la tabla `historial_cambios`
--
ALTER TABLE `historial_cambios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `horarios_laborales`
--
ALTER TABLE `horarios_laborales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `ordenes_produccion`
--
ALTER TABLE `ordenes_produccion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `registros_horas`
--
ALTER TABLE `registros_horas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=790;

--
-- AUTO_INCREMENT de la tabla `registros_horas_produccion`
--
ALTER TABLE `registros_horas_produccion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `resumen_diario_horas`
--
ALTER TABLE `resumen_diario_horas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sincronizacion_projectdashboard`
--
ALTER TABLE `sincronizacion_projectdashboard`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=614;

--
-- AUTO_INCREMENT de la tabla `solicitudes_horas_extras`
--
ALTER TABLE `solicitudes_horas_extras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
-- Filtros para la tabla `registros_horas_produccion`
--
ALTER TABLE `registros_horas_produccion`
  ADD CONSTRAINT `registros_horas_produccion_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `registros_horas_produccion_ibfk_2` FOREIGN KEY (`orden_produccion_id`) REFERENCES `ordenes_produccion` (`id`) ON DELETE CASCADE;

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
