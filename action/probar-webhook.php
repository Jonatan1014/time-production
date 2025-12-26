<?php
// action/probar-webhook.php
header('Content-Type: application/json');
session_start();

require_once '../includes/Class-usuario.php';
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

try {
    $Webhook_class = new WebhookProjectDashboard();
    $resultado = $Webhook_class->probarConexion();
    
    echo json_encode($resultado);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
