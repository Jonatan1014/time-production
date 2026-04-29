<?php
// action/add-dotacion-item.php
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
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    if ($nombre === '') {
        $_SESSION['error'] = 'Por favor, ingresa el nombre del item.';
        header("Location: ../add-dotacion-item.php");
        exit;
    }

    $Dotacion_class = new Dotacion();

    if ($Dotacion_class->itemExiste($nombre)) {
        $_SESSION['error'] = 'Ya existe un item con ese nombre.';
        header("Location: ../add-dotacion-item.php");
        exit;
    }

    $resultado = $Dotacion_class->crearItem([
        'nombre' => $nombre,
        'descripcion' => $descripcion
    ]);

    if ($resultado['success']) {
        $_SESSION['exito'] = 'Item de dotacion creado exitosamente.';
        header("Location: ../dotaciones-items.php");
        exit;
    }

    $_SESSION['error'] = $resultado['message'];
    header("Location: ../add-dotacion-item.php");
    exit;
}

$_SESSION['error'] = 'Metodo no permitido';
header("Location: ../dotaciones-items.php");
exit;
?>
