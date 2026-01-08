<?php
// cron-festivos.php
// Script para cron job externo - actualiza festivos automáticamente
// 
// CONFIGURACIÓN CRON (ejecutar mensualmente o anualmente):
// ========================================================
// 
// Opción 1: Cron local (recomendado)
//   0 0 1 1 * /usr/bin/php /Applications/XAMPP/xamppfiles/htdocs/time-production/cron-festivos.php
//   (Se ejecuta el 1 de enero a las 00:00)
//
// Opción 2: Cron mensual
//   0 0 1 * * /usr/bin/php /Applications/XAMPP/xamppfiles/htdocs/time-production/cron-festivos.php
//   (Se ejecuta el día 1 de cada mes a las 00:00)
//
// Opción 3: Llamada HTTP externa (con token de seguridad)
//   curl -X GET "https://tudominio.com/time-production/cron-festivos.php?token=TU_TOKEN_SECRETO"
//
// Opción 4: Con año específico
//   curl -X GET "https://tudominio.com/time-production/cron-festivos.php?token=TU_TOKEN_SECRETO&anio=2027"
//

// ===== CONFIGURACIÓN DE SEGURIDAD =====
// IMPORTANTE: Cambiar este token en producción
define('CRON_TOKEN', 'festivos_secure_token_2026');

// Detectar modo de ejecución
$es_cli = (php_sapi_name() === 'cli');
$es_web = !$es_cli;

// Si es web, verificar token
if ($es_web) {
    header('Content-Type: application/json; charset=utf-8');
    
    $token = isset($_REQUEST['token']) ? $_REQUEST['token'] : '';
    if ($token !== CRON_TOKEN) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'error' => 'Token de acceso inválido',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit(1);
    }
}

// Incluir dependencias
require_once __DIR__ . '/includes/Class-festivos.php';

// Función para logging
function log_message($mensaje, $es_error = false) {
    global $es_cli;
    $prefix = date('[Y-m-d H:i:s]');
    
    if ($es_error) {
        error_log("$prefix [ERROR] $mensaje");
    }
    
    if ($es_cli) {
        echo "$prefix " . ($es_error ? "[ERROR] " : "[INFO] ") . "$mensaje\n";
    }
}

// Función para respuesta final
function finalizar($success, $mensaje, $datos = []) {
    global $es_cli, $es_web;
    
    $response = [
        'success' => $success,
        'mensaje' => $mensaje,
        'timestamp' => date('Y-m-d H:i:s'),
        'datos' => $datos
    ];
    
    if ($es_cli) {
        log_message($mensaje, !$success);
        exit($success ? 0 : 1);
    }
    
    if ($es_web) {
        http_response_code($success ? 200 : 500);
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}

// ===== INICIO DEL PROCESO =====
log_message("Iniciando actualización de festivos...");

try {
    $festivos = new Festivos();
    
    // Obtener parámetros
    $anio_actual = date('Y');
    $anio_siguiente = $anio_actual + 1;
    
    // Si se especifica un año, solo procesar ese
    $anio_especifico = isset($_REQUEST['anio']) ? intval($_REQUEST['anio']) : null;
    $forzar = isset($_REQUEST['forzar']) && ($_REQUEST['forzar'] === '1' || $_REQUEST['forzar'] === 'true');
    
    $anios_a_procesar = $anio_especifico 
        ? [$anio_especifico] 
        : [$anio_actual, $anio_siguiente];
    
    $resultados = [];
    $total_festivos = 0;
    $errores = 0;
    
    foreach ($anios_a_procesar as $anio) {
        log_message("Procesando año $anio...");
        
        $resultado = $festivos->consultarYGuardarFestivos($anio, null, $forzar);
        $resultados[$anio] = $resultado;
        
        if ($resultado['success']) {
            $count = count($resultado['festivos']);
            $total_festivos += $count;
            $desde = isset($resultado['desde_cache']) && $resultado['desde_cache'] ? '(desde cache)' : '(desde API)';
            log_message("  ✓ Año $anio: $count festivos registrados $desde");
        } else {
            $errores++;
            log_message("  ✗ Año $anio: " . $resultado['mensaje'], true);
        }
    }
    
    // Resumen final
    $mensaje_final = $errores === 0 
        ? "Actualización completada: $total_festivos festivos para " . count($anios_a_procesar) . " año(s)"
        : "Actualización parcial: $errores error(es) de " . count($anios_a_procesar) . " año(s)";
    
    finalizar($errores === 0, $mensaje_final, [
        'anios_procesados' => $anios_a_procesar,
        'total_festivos' => $total_festivos,
        'errores' => $errores,
        'resultados' => $resultados
    ]);
    
} catch (Exception $e) {
    log_message("Error fatal: " . $e->getMessage(), true);
    finalizar(false, "Error fatal: " . $e->getMessage());
}
?>
