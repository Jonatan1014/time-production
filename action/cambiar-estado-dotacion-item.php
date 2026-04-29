<?php
// action/cambiar-estado-dotacion-item.php
session_start();
require_once '../includes/Class-usuario.php';
require_once '../includes/Class-dotacion.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado() || !$Usuario_class->verificarPermisos('administrador_dotacion')) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $estado = (int)($_POST['estado'] ?? -1);

    if ($id <= 0 || ($estado !== 0 && $estado !== 1)) {
        echo json_encode(['success' => false, 'message' => 'Parametros invalidos']);
        exit;
    }

    $Dotacion_class = new Dotacion();
    $resultado = $Dotacion_class->cambiarEstadoItem($id, $estado);

    echo json_encode($resultado);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Metodo no permitido']);
exit;
?>
