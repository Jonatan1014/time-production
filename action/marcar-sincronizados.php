<?php
// action/marcar-sincronizados.php
header('Content-Type: application/json');
session_start();

require_once '../includes/Class-usuario.php';
require_once '../includes/Class-sincronizacion.php';

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
    $Sincronizacion_class = new Sincronizacion();
    
    $resultado = $Sincronizacion_class->marcarComoSincronizado(
        $data['registros'],
        $_SESSION['user_id'],
        'Marcado manualmente desde interface web'
    );

    if ($resultado['success']) {
        echo json_encode([
            'success' => true,
            'insertados' => $resultado['insertados'],
            'errores' => $resultado['errores']
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $resultado['message']
        ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar: ' . $e->getMessage()
    ]);
}
