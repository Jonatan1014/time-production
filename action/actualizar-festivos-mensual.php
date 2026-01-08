<?php
// action/actualizar-festivos-mensual.php
// Script para actualizar festivos de manera manual o automática vía cron
// 
// USO CRON (ejecutar mensualmente el día 1):
//   0 0 1 * * /usr/bin/php /path/to/time-production/action/actualizar-festivos-mensual.php
//
// USO WEB (llamada AJAX desde configuración):
//   POST/GET con parámetros opcionales: anio, pais, forzar
//
// USO EXTERNO (cron job remoto):
//   curl -X POST "https://tudominio.com/time-production/action/actualizar-festivos-mensual.php?token=TU_TOKEN_SECRETO&anio=2026"

// Detectar si se ejecuta desde CLI o web
$es_cli = (php_sapi_name() === 'cli');

// Configurar headers si es web
if (!$es_cli) {
    header('Content-Type: application/json; charset=utf-8');
}

// Token de seguridad para llamadas externas (cambiar en producción)
define('CRON_SECRET_TOKEN', 'festivos_cron_2026_secret');

require_once __DIR__ . '/../includes/Class-festivos.php';

// Función para responder
function responder($success, $mensaje, $datos = []) {
    global $es_cli;
    
    $response = [
        'success' => $success,
        'mensaje' => $mensaje,
        'timestamp' => date('Y-m-d H:i:s'),
        'datos' => $datos
    ];
    
    if ($es_cli) {
        echo ($success ? "[OK] " : "[ERROR] ") . $mensaje . "\n";
        if (!empty($datos)) {
            print_r($datos);
        }
        exit($success ? 0 : 1);
    } else {
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// Verificar token si es llamada externa con token
if (!$es_cli && isset($_REQUEST['token'])) {
    if ($_REQUEST['token'] !== CRON_SECRET_TOKEN) {
        responder(false, 'Token de seguridad inválido');
    }
}

// Verificar sesión si es llamada web sin token (desde panel de admin)
if (!$es_cli && !isset($_REQUEST['token'])) {
    session_start();
    require_once __DIR__ . '/../includes/Class-usuario.php';
    $Usuario_class = new Usuario();
    
    if (!$Usuario_class->usuarioLogueado()) {
        responder(false, 'Sesión no válida');
    }
    
    // Verificar que sea administrador
    $es_admin = isset($_SESSION['user_rol']) && 
                ($_SESSION['user_rol'] === 'administrador' || $_SESSION['user_rol'] === 'Administrador');
    
    if (!$es_admin) {
        responder(false, 'No tiene permisos para esta acción');
    }
}

try {
    $festivos = new Festivos();
    
    // Obtener parámetros
    $anio = isset($_REQUEST['anio']) ? intval($_REQUEST['anio']) : date('Y');
    $pais = isset($_REQUEST['pais']) ? $_REQUEST['pais'] : null;
    $forzar = isset($_REQUEST['forzar']) && ($_REQUEST['forzar'] === '1' || $_REQUEST['forzar'] === 'true');
    $anio_siguiente = isset($_REQUEST['anio_siguiente']) && ($_REQUEST['anio_siguiente'] === '1' || $_REQUEST['anio_siguiente'] === 'true');
    
    // Si está habilitado, también consultar el año siguiente
    $resultados = [];
    
    // Consultar año solicitado
    $resultado = $festivos->consultarYGuardarFestivos($anio, $pais, $forzar);
    $resultados[$anio] = $resultado;
    
    // Si es cron o se solicita, también el año siguiente
    if ($es_cli || $anio_siguiente) {
        $anio_sig = $anio + 1;
        $resultado_sig = $festivos->consultarYGuardarFestivos($anio_sig, $pais, $forzar);
        $resultados[$anio_sig] = $resultado_sig;
    }
    
    // Preparar resumen
    $total_festivos = 0;
    $mensajes = [];
    $todos_exitosos = true;
    
    foreach ($resultados as $a => $res) {
        if ($res['success']) {
            $total_festivos += count($res['festivos']);
            $mensajes[] = "Año $a: " . count($res['festivos']) . " festivos";
        } else {
            $todos_exitosos = false;
            $mensajes[] = "Año $a: Error - " . $res['mensaje'];
        }
    }
    
    $mensaje_final = $todos_exitosos 
        ? "Festivos actualizados correctamente. " . implode(', ', $mensajes)
        : "Algunos años tuvieron errores. " . implode(', ', $mensajes);
    
    responder($todos_exitosos, $mensaje_final, [
        'resultados' => $resultados,
        'total_festivos' => $total_festivos,
        'anios_procesados' => array_keys($resultados)
    ]);
    
} catch (Exception $e) {
    error_log("Error en actualizar-festivos-mensual.php: " . $e->getMessage());
    responder(false, 'Error interno: ' . $e->getMessage());
}
?>