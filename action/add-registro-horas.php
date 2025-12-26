<?php
// action/add-registro-horas.php - Procesar registro de horas
session_start();
require_once '../includes/Class-usuario.php';
require_once '../includes/Class-registros-horas.php';
require_once '../includes/Class-configuracion.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar que el usuario esté en sesión
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        $_SESSION['error'] = "Sesión inválida. Por favor, inicie sesión nuevamente.";
        header("Location: ../login.php");
        exit;
    }
    
    $usuario_id = $_SESSION['user_id'];
    
    // Verificar que el usuario existe en la base de datos
    try {
        require_once '../includes/conn-db.php';
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "SELECT id FROM usuarios WHERE id = ? AND is_active = 1";
        $stmt = $conn->prepare($query);
        $stmt->execute([$usuario_id]);
        
        if ($stmt->rowCount() === 0) {
            $_SESSION['error'] = "Usuario no válido o inactivo. Por favor, contacte al administrador.";
            header("Location: ../login.php");
            exit;
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error al verificar usuario: " . $e->getMessage();
        header("Location: ../registrar-horas.php");
        exit;
    }
    
    // Verificar si es un registro múltiple o individual
    $fecha = $_POST['fecha'] ?? '';
    
    if (isset($_POST['registros']) && is_array($_POST['registros'])) {
        // Procesamiento múltiple
        $registros = $_POST['registros'];
        $errores = [];
        $registros_exitosos = 0;
        $total_horas_registradas = 0;
        
        // Validar fecha
        if (empty($fecha)) {
            $_SESSION['error'] = "La fecha es requerida";
            header("Location: ../registrar-horas.php");
            exit;
        }
        
        if (strtotime($fecha) > strtotime(date('Y-m-d'))) {
            $_SESSION['error'] = "No puede registrar horas de fechas futuras";
            header("Location: ../registrar-horas.php");
            exit;
        }
        
        // Instanciar clases necesarias
        $RegistroHoras_class = new RegistroHoras();
        $Configuracion = new Configuracion();
        
        // Obtener total actual del día
        $total_dia_actual = $RegistroHoras_class->obtenerTotalHorasDia($usuario_id, $fecha);
        
        // Obtener horas máximas según el día de la semana
        $horario_dia = $Configuracion->obtenerHorasLaboralesDia($fecha);
        $horas_maximas = $horario_dia['horas_totales'];
        
        // Verificar que sea día laborable
        if (!$horario_dia['es_laborable']) {
            $_SESSION['error'] = "No se pueden registrar horas para " . $horario_dia['dia_semana'] . " - no es un día laborable según el horario configurado";
            header("Location: ../registrar-horas.php");
            exit;
        }
        
        // Validar cada registro
        foreach ($registros as $index => $registro) {
            $num_registro = $index + 1;
            $horas_trabajadas = $registro['horas_trabajadas'] ?? '';
            $orden_produccion_id = $registro['orden_produccion_id'] ?? '';
            $descripcion_trabajo = $registro['descripcion_trabajo'] ?? '';
            
            // Validar orden de producción
            if (empty($orden_produccion_id)) {
                $errores[] = "Registro #$num_registro: Debe seleccionar una orden de producción";
                continue;
            }
            
            // Validaciones individuales
            if (empty($horas_trabajadas)) {
                $errores[] = "Registro #$num_registro: Las horas trabajadas son requeridas";
                continue;
            }
            
            $horas_trabajadas = floatval($horas_trabajadas);
            
            // Validar horas usando configuración (sin pasar fecha aquí, ya validamos el límite general después)
            try {
                // Validación básica sin límite de día específico
                $config = $Configuracion->obtenerConfigHoras();
                $horas_min = $config['horas_minimas_por_registro'] ?? 0.5;
                $incremento = $config['incremento_horas'] ?? 0.5;
                
                if ($horas_trabajadas < $horas_min) {
                    $errores[] = "Registro #$num_registro: Debe registrar al menos " . number_format($horas_min, 1) . " horas";
                    continue;
                }
                
                if (fmod($horas_trabajadas, $incremento) != 0) {
                    $errores[] = "Registro #$num_registro: Las horas deben ser múltiplos de " . number_format($incremento, 1);
                    continue;
                }
                
                // Validar que no exceda el máximo del día
                if ($horas_trabajadas > $horas_maximas) {
                    $errores[] = "Registro #$num_registro: No puede registrar más de " . number_format($horas_maximas, 1) . " horas para " . $horario_dia['dia_semana'];
                    continue;
                }
            } catch (Exception $e) {
                // Validación por defecto
                if ($horas_trabajadas < 0.5 || $horas_trabajadas > $horas_maximas || fmod($horas_trabajadas, 0.5) != 0) {
                    $errores[] = "Registro #$num_registro: Horas inválidas (debe ser entre 0.5 y " . number_format($horas_maximas, 1) . ", múltiplos de 0.5)";
                    continue;
                }
            }
            
            if (strlen($descripcion_trabajo) < 5) {
                $errores[] = "Registro #$num_registro: La descripción debe tener al menos 5 caracteres";
                continue;
            }
            
            // Acumular horas para validación total
            $total_horas_registradas += $horas_trabajadas;
        }
        
        // Validar que no se exceda el límite diario
        if (($total_dia_actual + $total_horas_registradas) > $horas_maximas) {
            $errores[] = "El total de horas (" . number_format($total_horas_registradas, 1) . " hrs) más las ya registradas (" . number_format($total_dia_actual, 1) . " hrs) superaría las " . number_format($horas_maximas, 1) . " horas permitidas para " . $horario_dia['dia_semana'];
        }
        
        // Si hay errores, detener el proceso
        if (!empty($errores)) {
            $_SESSION['error'] = implode('<br>', $errores);
            header("Location: ../registrar-horas.php");
            exit;
        }
        
        // Procesar cada registro
        foreach ($registros as $index => $registro) {
            $datos = [
                'usuario_id' => $usuario_id,
                'orden_produccion_id' => $registro['orden_produccion_id'],
                'fecha' => $fecha,
                'horas_trabajadas' => floatval($registro['horas_trabajadas']),
                'descripcion_trabajo' => $registro['descripcion_trabajo'],
                'estado' => 'registrado'
            ];
            
            try {
                $resultado = $RegistroHoras_class->crearRegistro($datos);
                if ($resultado) {
                    $registros_exitosos++;
                }
            } catch (Exception $e) {
                $errores[] = "Error en registro #" . ($index + 1) . ": " . $e->getMessage();
            }
        }
        
        // Mensaje final
        if ($registros_exitosos > 0) {
            $_SESSION['success'] = "¡Registros exitosos! Se registraron $registros_exitosos registros con un total de " . number_format($total_horas_registradas, 1) . " horas para el día " . date('d/m/Y', strtotime($fecha));
            if (!empty($errores)) {
                $_SESSION['error'] = "Algunos registros tuvieron problemas:<br>" . implode('<br>', $errores);
            }
            header("Location: ../registrar-horas.php");
        } else {
            $_SESSION['error'] = "No se pudo registrar ninguna hora.<br>" . implode('<br>', $errores);
            header("Location: ../registrar-horas.php");
        }
        exit;
    }
    
    // Procesamiento individual (mantener compatibilidad con formularios antiguos)
    $orden_produccion_id = $_POST['orden_produccion_id'] ?? '';
    $horas_trabajadas = $_POST['horas_trabajadas'] ?? '';
    $descripcion_trabajo = $_POST['descripcion_trabajo'] ?? '';

    // Validaciones
    $errores = [];

    if (empty($orden_produccion_id)) {
        $errores[] = "Debe seleccionar una orden de producción";
    }

    if (empty($fecha)) {
        $errores[] = "La fecha es requerida";
    } else if (strtotime($fecha) > strtotime(date('Y-m-d'))) {
        $errores[] = "No puede registrar horas de fechas futuras";
    }

    if (empty($horas_trabajadas)) {
        $errores[] = "Las horas trabajadas son requeridas";
    } else {
        $horas_trabajadas = floatval($horas_trabajadas);
        
        // Validar horas usando la configuración del sistema con fecha específica
        try {
            $Configuracion = new Configuracion();
            
            // Obtener horario del día
            $horario_dia = $Configuracion->obtenerHorasLaboralesDia($fecha);
            
            // Verificar que sea día laborable
            if (!$horario_dia['es_laborable']) {
                $errores[] = "No se pueden registrar horas para " . $horario_dia['dia_semana'] . " - no es un día laborable";
            }
            
            $config = $Configuracion->obtenerConfigHoras();
            $horas_min = $config['horas_minimas_por_registro'] ?? 0.5;
            $incremento = $config['incremento_horas'] ?? 0.5;
            $horas_max = $horario_dia['horas_totales'];
            
            if ($horas_trabajadas < $horas_min) {
                $errores[] = "Debe registrar al menos " . number_format($horas_min, 1) . " horas";
            }
            
            if ($horas_trabajadas > $horas_max) {
                $errores[] = "No puede registrar más de " . number_format($horas_max, 1) . " horas para " . $horario_dia['dia_semana'];
            }
            
            if (fmod($horas_trabajadas, $incremento) != 0) {
                $errores[] = "Las horas deben ser múltiplos de " . number_format($incremento, 1);
            }
        } catch (Exception $e) {
            // Si falla la configuración, usar valores por defecto
            if ($horas_trabajadas < 0.5) {
                $errores[] = "Debe registrar al menos 0.5 horas";
            }
            if ($horas_trabajadas > 8.5) {
                $errores[] = "No puede registrar más de 8.5 horas normales por día";
            }
            if (fmod($horas_trabajadas, 0.5) != 0) {
                $errores[] = "Las horas deben ser múltiplos de 0.5 (ejemplo: 0.5, 1.0, 1.5, 2.0...)";
            }
        }
    }

    if (strlen($descripcion_trabajo) < 5) {
        $errores[] = "La descripción debe tener al menos 5 caracteres";
    }

    // Verificar total de horas del día
    if (empty($errores)) {
        try {
            $RegistroHoras_class = new RegistroHoras();
            $Configuracion = new Configuracion();
            
            $total_dia = $RegistroHoras_class->obtenerTotalHorasDia($usuario_id, $fecha);
            $horario_dia = $Configuracion->obtenerHorasLaboralesDia($fecha);
            $horas_maximas = $horario_dia['horas_totales'];
            
            if (($total_dia + $horas_trabajadas) > $horas_maximas) {
                $errores[] = "Con este registro superaría las " . number_format($horas_maximas, 1) . " horas permitidas para " . $horario_dia['dia_semana'] . ". Ya tiene registradas " . number_format($total_dia, 1) . " horas";
            }
        } catch (Exception $e) {
            // Si falla, usar valor por defecto de 8.0 horas máximas
            $RegistroHoras_class = new RegistroHoras();
            $total_dia = $RegistroHoras_class->obtenerTotalHorasDia($usuario_id, $fecha);
            $horas_maximas = 8.0;
            
            if (($total_dia + $horas_trabajadas) > $horas_maximas) {
                $errores[] = "Con este registro superaría las " . number_format($horas_maximas, 1) . " horas permitidas por día. Ya tiene registradas " . number_format($total_dia, 1) . " horas";
            }
        }
    }

    // Si hay errores, redirigir con mensaje
    if (!empty($errores)) {
        $_SESSION['error'] = implode('<br>', $errores);
        header("Location: ../registrar-horas.php");
        exit;
    }

    // Crear el registro
    $datos = [
        'usuario_id' => $usuario_id,
        'orden_produccion_id' => $orden_produccion_id,
        'fecha' => $fecha,
        'horas_trabajadas' => $horas_trabajadas,
        'descripcion_trabajo' => $descripcion_trabajo,
        'estado' => 'registrado'
    ];

    try {
        $resultado = $RegistroHoras_class->crearRegistro($datos);

        if ($resultado) {
            $_SESSION['success'] = "¡Horas registradas correctamente! Se registraron " . number_format($horas_trabajadas, 1) . " horas para el día " . date('d/m/Y', strtotime($fecha));
            header("Location: ../mis-horas.php");
        } else {
            $_SESSION['error'] = "No se pudieron registrar las horas. Por favor, intente nuevamente.";
            header("Location: ../registrar-horas.php");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error al registrar las horas: " . $e->getMessage();
        header("Location: ../registrar-horas.php");
    }
    exit;
}

header("Location: ../registrar-horas.php");
exit;
?>
