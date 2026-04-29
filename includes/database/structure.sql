-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: systemautomatic.xyz
-- Tiempo de generación: 29-04-2026 a las 04:38:38
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dotacion_items`
--

CREATE TABLE `dotacion_items` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dotacion_entregas`
--

CREATE TABLE `dotacion_entregas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `entregado_por` int(11) NOT NULL,
  `fecha_entrega` date NOT NULL,
  `proxima_entrega` date NOT NULL,
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dotacion_entrega_items`
--

CREATE TABLE `dotacion_entrega_items` (
  `id` int(11) NOT NULL,
  `entrega_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `rol` enum('trabajador','administrador','produccion','administrador_dotacion') DEFAULT 'trabajador',
  `is_active` tinyint(1) DEFAULT 1,
  `fecha_ingreso` date DEFAULT NULL,
  `departamento_id` int(11) DEFAULT NULL,
  `cargo_id` int(11) DEFAULT NULL COMMENT 'ID del cargo del usuario',
  `valor_hora_base` decimal(10,2) DEFAULT 0.00 COMMENT 'Valor base por hora del empleado',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Indices de la tabla `dotacion_entrega_items`
--
ALTER TABLE `dotacion_entrega_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_entrega` (`entrega_id`),
  ADD KEY `idx_item` (`item_id`),
  ADD KEY `idx_entrega_item` (`entrega_id`,`item_id`);

--
-- Indices de la tabla `dotacion_entregas`
--
ALTER TABLE `dotacion_entregas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_entregado_por` (`entregado_por`),
  ADD KEY `idx_fecha_entrega` (`fecha_entrega`),
  ADD KEY `idx_proxima_entrega` (`proxima_entrega`),
  ADD KEY `idx_usuario_proxima` (`usuario_id`,`proxima_entrega`);

--
-- Indices de la tabla `dotacion_items`
--
ALTER TABLE `dotacion_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_nombre` (`nombre`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `configuracion_sistema`
--
ALTER TABLE `configuracion_sistema`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `dotacion_entrega_items`
--
ALTER TABLE `dotacion_entrega_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `dotacion_entregas`
--
ALTER TABLE `dotacion_entregas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `dotacion_items`
--
ALTER TABLE `dotacion_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `festivos_cache`
--
ALTER TABLE `festivos_cache`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historial_cambios`
--
ALTER TABLE `historial_cambios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `horarios_laborales`
--
ALTER TABLE `horarios_laborales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ordenes_produccion`
--
ALTER TABLE `ordenes_produccion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `registros_horas`
--
ALTER TABLE `registros_horas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `registros_horas_produccion`
--
ALTER TABLE `registros_horas_produccion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `resumen_diario_horas`
--
ALTER TABLE `resumen_diario_horas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sincronizacion_projectdashboard`
--
ALTER TABLE `sincronizacion_projectdashboard`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `solicitudes_horas_extras`
--
ALTER TABLE `solicitudes_horas_extras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `departamentos`
--
ALTER TABLE `departamentos`
  ADD CONSTRAINT `fk_departamento_responsable` FOREIGN KEY (`responsable_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `dotacion_entrega_items`
--
ALTER TABLE `dotacion_entrega_items`
  ADD CONSTRAINT `dotacion_entrega_items_ibfk_1` FOREIGN KEY (`entrega_id`) REFERENCES `dotacion_entregas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dotacion_entrega_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `dotacion_items` (`id`) ON DELETE RESTRICT;

--
-- Filtros para la tabla `dotacion_entregas`
--
ALTER TABLE `dotacion_entregas`
  ADD CONSTRAINT `dotacion_entregas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dotacion_entregas_ibfk_2` FOREIGN KEY (`entregado_por`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT;

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
