<?php
// action/cambiar-estado-usuario.php
session_start();
require_once '../includes/Class-usuario.php';
require_once '../includes/conn-db.php';

// Verificar permisos de administrador
$Usuario_class = new Usuario();
if (!$Usuario_class->usuarioLogueado() || !$Usuario_class->verificarPermisos('Administrador')) {
    $_SESSION['error'] = 'No tienes permisos para realizar esta acción';
    header("Location: ../usuarios.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['id']) && isset($_POST['estado'])) {
        
        $id = (int)$_POST['id'];
        $nuevo_estado = (int)$_POST['estado'];
        
        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            // Verificar que el usuario existe
            $stmt = $conn->prepare("SELECT nombre_completo FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$usuario) {
                $_SESSION['error'] = 'Usuario no encontrado.';
                header("Location: ../usuarios.php");
                exit;
            }
            
            // No permitir desactivar el propio usuario
            if ($id == $_SESSION['user_id']) {
                $_SESSION['error'] = 'No puedes desactivar tu propia cuenta.';
                header("Location: ../usuarios.php");
                exit;
            }
            
            // Actualizar el estado
            $stmt = $conn->prepare("UPDATE usuarios SET is_active = ? WHERE id = ?");
            $resultado = $stmt->execute([$nuevo_estado, $id]);
            
            if ($resultado) {
                $accion = $nuevo_estado == 1 ? 'activado' : 'desactivado';
                $_SESSION['exito'] = "Usuario {$usuario['nombre_completo']} {$accion} correctamente.";
            } else {
                $_SESSION['error'] = 'Error al cambiar el estado del usuario.';
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error del sistema: ' . $e->getMessage();
        }
        
    } else {
        $_SESSION['error'] = 'Datos incompletos.';
    }
} else {
    $_SESSION['error'] = 'Método no permitido';
}

header("Location: ../usuarios.php");
exit;
?>
