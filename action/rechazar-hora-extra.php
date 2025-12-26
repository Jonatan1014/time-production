<?php
// action/rechazar-hora-extra.php - Rechazar solicitud de hora extra
session_start();
require_once '../includes/Class-usuario.php';
require_once '../includes/Class-horas-extras.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    header("Location: ../login.php");
    exit;
}

// Verificar que sea administrador
$es_admin = isset($_SESSION['user_rol']) && ($_SESSION['user_rol'] === 'administrador' || $_SESSION['user_rol'] === 'Administrador');

if (!$es_admin) {
    $_SESSION['error'] = "No tiene permisos para realizar esta acción";
    header("Location: ../horas-extras.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $solicitud_id = $_POST['solicitud_id'] ?? '';
    $comentario = $_POST['comentario'] ?? '';
    $rechazador_id = $_SESSION['user_id'];

    if (empty($solicitud_id)) {
        $_SESSION['error'] = "Solicitud no válida";
        header("Location: ../horas-extras.php");
        exit;
    }

    if (empty($comentario)) {
        $_SESSION['error'] = "Debe proporcionar un motivo para el rechazo";
        header("Location: ../horas-extras.php");
        exit;
    }

    $HoraExtra_class = new HoraExtra();
    $resultado = $HoraExtra_class->rechazarSolicitud($solicitud_id, $rechazador_id, $comentario);

    if ($resultado) {
        $_SESSION['success'] = "Solicitud rechazada correctamente";
    } else {
        $_SESSION['error'] = "Error al rechazar la solicitud. Intente nuevamente";
    }
    
    header("Location: ../horas-extras.php");
    exit;
}

header("Location: ../horas-extras.php");
exit;
?>
