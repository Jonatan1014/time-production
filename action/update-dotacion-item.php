<?php
// action/update-dotacion-item.php
session_start();
require_once '../includes/Class-usuario.php';
require_once '../includes/Class-dotacion.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    header("Location: ../login.php");
    exit;
}

if (!$Usuario_class->verificarPermisos('administrador_dotacion')) {
    $_SESSION['error'] = 'No tienes permisos para realizar esta accion';
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    if ($id <= 0 || $nombre === '') {
        $_SESSION['error'] = 'Datos incompletos para actualizar el item.';
        header("Location: ../dotaciones-items.php");
        exit;
    }

    $Dotacion_class = new Dotacion();

    if ($Dotacion_class->itemExiste($nombre, $id)) {
        $_SESSION['error'] = 'Ya existe un item con ese nombre.';
        header("Location: ../update-dotacion-item.php?id=" . $id);
        exit;
    }

    $resultado = $Dotacion_class->actualizarItem($id, [
        'nombre' => $nombre,
        'descripcion' => $descripcion
    ]);

    if ($resultado['success']) {
        $_SESSION['exito'] = 'Item de dotacion actualizado correctamente.';
        header("Location: ../dotaciones-items.php");
        exit;
    }

    $_SESSION['error'] = $resultado['message'];
    header("Location: ../update-dotacion-item.php?id=" . $id);
    exit;
}

$_SESSION['error'] = 'Metodo no permitido';
header("Location: ../dotaciones-items.php");
exit;
?>
