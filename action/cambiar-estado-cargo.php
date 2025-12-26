<?php
// action/cambiar-estado-cargo.php
require_once '../includes/Class-cargos.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['id']) && isset($_POST['estado'])) {

        $id = intval($_POST['id']);
        $estado = intval($_POST['estado']);

        if ($estado !== 0 && $estado !== 1) {
            echo json_encode(['success' => false, 'message' => 'Estado inválido']);
            exit;
        }

        $Cargos_class = new Cargos();
        $resultado = $Cargos_class->cambiarEstadoCargo($id, $estado);

        echo json_encode($resultado);
        exit;

    } else {
        echo json_encode(['success' => false, 'message' => 'Parámetros incompletos']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}
?>