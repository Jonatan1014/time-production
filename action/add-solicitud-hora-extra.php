<?php
// action/add-solicitud-hora-extra.php - Procesar solicitud de hora extra
session_start();
require_once '../includes/Class-usuario.php';
require_once '../includes/Class-horas-extras.php';
require_once '../includes/Class-configuracion.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['user_id'];
    $orden_produccion_id = $_POST['orden_produccion_id'] ?? '';
    $fecha = $_POST['fecha'] ?? '';
    $hora_inicio = $_POST['hora_inicio'] ?? '';
    $hora_fin = $_POST['hora_fin'] ?? '';
    $descripcion_trabajo = $_POST['descripcion_trabajo'] ?? '';

    // Validaciones
    $errores = [];

    if (empty($orden_produccion_id)) {
        $errores[] = "Debe seleccionar una orden de producción";
    }

    if (empty($fecha)) {
        $errores[] = "La fecha es requerida";
    }

    if (empty($hora_inicio)) {
        $errores[] = "La hora de inicio es requerida";
    }

    if (empty($hora_fin)) {
        $errores[] = "La hora de finalización es requerida";
    }

    // Validar formato de horas
    if (!empty($hora_inicio) && !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $hora_inicio)) {
        $errores[] = "El formato de hora de inicio es inválido";
    }

    if (!empty($hora_fin) && !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $hora_fin)) {
        $errores[] = "El formato de hora de fin es inválido";
    }

    // Calcular total de horas extras
    if (!empty($hora_inicio) && !empty($hora_fin)) {
        $inicio = new DateTime($hora_inicio);
        $fin = new DateTime($hora_fin);
        $diferencia = $inicio->diff($fin);
        $total_horas = $diferencia->h + ($diferencia->i / 60);
        
        // Si la hora de fin es menor que la de inicio, asumimos que cruza la medianoche
        if ($fin < $inicio) {
            $total_horas = (24 - $inicio->format('H') - $inicio->format('i')/60) + ($fin->format('H') + $fin->format('i')/60);
        }
        
        $total_horas = round($total_horas, 2);
        
        // Validar rango de horas
        if ($total_horas < 0.5 || $total_horas > 8) {
            $errores[] = "Las horas extras deben estar entre 0.5 y 8 horas. Total calculado: " . number_format($total_horas, 2) . " horas";
        }
    }

    // Si hay errores, redirigir con mensaje
    if (!empty($errores)) {
        $_SESSION['error'] = implode('<br>', $errores);
        header("Location: ../solicitar-horas-extras.php");
        exit;
    }

    // Crear la solicitud
    $HoraExtra_class = new HoraExtra();
    
    $datos = [
        'usuario_id' => $usuario_id,
        'orden_produccion_id' => $orden_produccion_id,
        'fecha' => $fecha,
        'hora_inicio' => $hora_inicio,
        'hora_fin' => $hora_fin,
        'descripcion_trabajo' => $descripcion_trabajo,
        'estado' => 'pendiente'
    ];

    try {
        $resultado = $HoraExtra_class->crearSolicitud($datos);

        if ($resultado) {
            $_SESSION['success'] = "Solicitud de horas extras enviada correctamente. Total: " . number_format($total_horas, 2) . " horas. Pendiente de aprobación";
            header("Location: ../horas-extras.php");
        } else {
            $_SESSION['error'] = "Error al enviar la solicitud. Intente nuevamente";
            header("Location: ../solicitar-horas-extras.php");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: ../solicitar-horas-extras.php");
    }
    exit;
}

header("Location: ../solicitar-horas-extras.php");
exit;
?>
