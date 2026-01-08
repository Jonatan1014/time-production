<?php
// action/consultar-festivos.php
// Endpoint para consultar festivos de un año específico
// Usado para consultas AJAX desde el panel de configuración

header('Content-Type: application/json; charset=utf-8');

session_start();
require_once __DIR__ . '/../includes/Class-usuario.php';
require_once __DIR__ . '/../includes/Class-festivos.php';

// Verificar sesión
$Usuario_class = new Usuario();
if (!$Usuario_class->usuarioLogueado()) {
    echo json_encode(['success' => false, 'mensaje' => 'Sesión no válida']);
    exit;
}

try {
    $festivos_class = new Festivos();
    
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'obtener';
    $anio = isset($_REQUEST['anio']) ? intval($_REQUEST['anio']) : date('Y');
    $pais = isset($_REQUEST['pais']) ? $_REQUEST['pais'] : null;
    
    switch ($action) {
        case 'obtener':
            // Obtener festivos de cache sin consultar API
            $pais = $pais ?: $festivos_class->obtenerConfiguracion('festivos_pais') ?: 'CO';
            $festivos = $festivos_class->obtenerFestivosCache($pais, $anio);
            
            echo json_encode([
                'success' => true,
                'anio' => $anio,
                'pais' => $pais,
                'total' => count($festivos),
                'festivos' => $festivos
            ], JSON_UNESCAPED_UNICODE);
            break;
            
        case 'consultar':
            // Consultar API y guardar (solo admin)
            $es_admin = isset($_SESSION['user_rol']) && 
                        ($_SESSION['user_rol'] === 'administrador' || $_SESSION['user_rol'] === 'Administrador');
            
            if (!$es_admin) {
                echo json_encode(['success' => false, 'mensaje' => 'No tiene permisos para esta acción']);
                exit;
            }
            
            $forzar = isset($_REQUEST['forzar']) && $_REQUEST['forzar'] === '1';
            $resultado = $festivos_class->consultarYGuardarFestivos($anio, $pais, $forzar);
            
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
            break;
            
        case 'anios':
            // Obtener años disponibles
            $anios = $festivos_class->obtenerAniosDisponibles($pais);
            
            echo json_encode([
                'success' => true,
                'anios' => $anios
            ], JSON_UNESCAPED_UNICODE);
            break;
            
        case 'eliminar':
            // Eliminar festivos de un año (solo admin)
            $es_admin = isset($_SESSION['user_rol']) && 
                        ($_SESSION['user_rol'] === 'administrador' || $_SESSION['user_rol'] === 'Administrador');
            
            if (!$es_admin) {
                echo json_encode(['success' => false, 'mensaje' => 'No tiene permisos para esta acción']);
                exit;
            }
            
            $resultado = $festivos_class->eliminarFestivosAnio($anio, $pais);
            
            echo json_encode([
                'success' => $resultado,
                'mensaje' => $resultado 
                    ? "Festivos del año $anio eliminados correctamente" 
                    : "Error al eliminar festivos del año $anio"
            ], JSON_UNESCAPED_UNICODE);
            break;
            
        case 'verificar':
            // Verificar si una fecha es festiva
            $fecha = isset($_REQUEST['fecha']) ? $_REQUEST['fecha'] : date('Y-m-d');
            $es_festivo = $festivos_class->esFestivo($fecha, $pais);
            
            echo json_encode([
                'success' => true,
                'fecha' => $fecha,
                'es_festivo' => $es_festivo
            ], JSON_UNESCAPED_UNICODE);
            break;
            
        default:
            echo json_encode(['success' => false, 'mensaje' => 'Acción no válida']);
    }
    
} catch (Exception $e) {
    error_log("Error en consultar-festivos.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'mensaje' => 'Error interno: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
