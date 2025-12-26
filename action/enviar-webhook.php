<?php
// action/enviar-webhook.php
header('Content-Type: application/json');
session_start();

require_once '../includes/Class-usuario.php';
require_once '../includes/Class-sincronizacion.php';
require_once '../includes/Class-webhook.php';

$Usuario_class = new Usuario();

// Verificar autenticación y permisos
if (!$Usuario_class->usuarioLogueado()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

if (!$Usuario_class->verificarPermisos('Administrador')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Sin permisos']);
    exit;
}

// Obtener datos JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['registros']) || !is_array($data['registros']) || empty($data['registros'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No se recibieron registros']);
    exit;
}

try {
    $Webhook_class = new WebhookProjectDashboard();
    $Sincronizacion_class = new Sincronizacion();
    
    // Enviar datos via webhook
    $resultado_envio = $Webhook_class->enviarDatos($data['registros']);
    
    if ($resultado_envio['success']) {
        // Marcar como sincronizado si el envío fue exitoso
        $resultado_sync = $Sincronizacion_class->marcarComoSincronizado(
            $data['registros'],
            $_SESSION['user_id'],
            'Enviado via webhook - HTTP ' . $resultado_envio['http_code']
        );
        
        echo json_encode([
            'success' => true,
            'message' => 'Datos enviados y marcados como sincronizados',
            'enviados' => $resultado_envio['registros_enviados'],
            'sincronizados' => $resultado_sync['insertados'],
            'http_code' => $resultado_envio['http_code']
        ]);
    } else {
        // Error al enviar
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $resultado_envio['message'],
            'http_code' => $resultado_envio['http_code'] ?? 0
        ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar: ' . $e->getMessage()
    ]);
}
