<?php
// action/aprobar-hora-extra.php - Aprobar solicitud de hora extra
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
    $comentario = $_POST['comentario'] ?? null;
    $aprobador_id = $_SESSION['user_id'];

    if (empty($solicitud_id)) {
        $_SESSION['error'] = "Solicitud no válida";
        header("Location: ../horas-extras.php");
        exit;
    }

    $HoraExtra_class = new HoraExtra();
    $resultado = $HoraExtra_class->aprobarSolicitud($solicitud_id, $aprobador_id, $comentario);

    if ($resultado) {
        $_SESSION['success'] = "Solicitud aprobada correctamente. Las horas han sido registradas";
    } else {
        $_SESSION['error'] = "Error al aprobar la solicitud. Intente nuevamente";
    }
    
    header("Location: ../horas-extras.php");
    exit;
}

header("Location: ../horas-extras.php");
exit;
?>
