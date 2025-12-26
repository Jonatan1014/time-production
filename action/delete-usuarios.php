<?php
// action/delete-usuarios.php
require_once '../includes/Class-usuario.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
    $id = $_POST['id'];
    $Usuario_class = new Usuario();
    
    try {
        $resultado = $Usuario_class->eliminarUsuario($id);
        
        if ($resultado) {
            $_SESSION['exito'] = 'Usuario eliminado correctamente';
        } else {
            $_SESSION['error'] = 'Error al eliminar el usuario.';
        }
    } catch (Exception $e) {
        $_SESSION['error'] = 'Error: ' . $e->getMessage();
    }
} else {
    $_SESSION['error'] = 'ID de usuario no proporcionado.';
}

header("Location: ../usuarios.php");
exit;
?>