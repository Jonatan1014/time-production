<?php
// action/add-dotacion-entrega.php
session_start();
require_once '../includes/Class-usuario.php';
require_once '../includes/Class-dotacion.php';
require_once '../includes/Class-configuracion.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    header("Location: ../login.php");
    exit;
}

if (!$Usuario_class->verificarPermisos('administrador_dotacion')) {
    $_SESSION['error'] = 'No tienes permisos para registrar entregas de dotacion';
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = (int)($_POST['usuario_id'] ?? 0);
    $fecha_entrega = $_POST['fecha_entrega'] ?? date('Y-m-d');
    $observaciones = trim($_POST['observaciones'] ?? '');
    $items_raw = $_POST['items'] ?? [];

    $items = [];
    foreach ($items_raw as $item_id => $cantidad) {
        $item_id = (int)$item_id;
        $cantidad = (int)$cantidad;
        if ($item_id > 0 && $cantidad > 0) {
            $items[$item_id] = $cantidad;
        }
    }

    if ($usuario_id <= 0) {
        $_SESSION['error'] = 'Debes seleccionar un usuario valido.';
        header("Location: ../add-dotacion-entrega.php");
        exit;
    }

    if (empty($items)) {
        $_SESSION['error'] = 'Debes registrar al menos un item de dotacion.';
        header("Location: ../add-dotacion-entrega.php?usuario_id=" . $usuario_id);
        exit;
    }

    $Configuracion_class = new Configuracion();
    $intervalo = $Configuracion_class->obtenerValor('dotacion_intervalo_meses', 4);

    $entregado_por = (int)($_SESSION['user_id'] ?? 0);
    if ($entregado_por <= 0) {
        $_SESSION['error'] = 'No se pudo identificar el usuario que entrega la dotacion.';
        header("Location: ../add-dotacion-entrega.php?usuario_id=" . $usuario_id);
        exit;
    }

    $Dotacion_class = new Dotacion();
    $resultado = $Dotacion_class->registrarEntrega([
        'usuario_id' => $usuario_id,
        'entregado_por' => $entregado_por,
        'fecha_entrega' => $fecha_entrega,
        'observaciones' => $observaciones
    ], $items, $intervalo);

    if ($resultado['success']) {
        $_SESSION['exito'] = $resultado['message'];
        header("Location: ../dotaciones.php");
        exit;
    }

    $_SESSION['error'] = $resultado['message'];
    header("Location: ../add-dotacion-entrega.php?usuario_id=" . $usuario_id);
    exit;
}

$_SESSION['error'] = 'Metodo no permitido';
header("Location: ../dotaciones.php");
exit;
?>
