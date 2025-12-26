<?php
// action/cambiar-estado-rol.php
session_start();
require_once '../includes/conn-db.php';
require_once '../includes/Class-usuario.php';

// Verificar permisos de administrador
$Usuario_class = new Usuario();
if (!$Usuario_class->usuarioLogueado() || !$Usuario_class->verificarPermisos('Administrador')) {
    $_SESSION['error'] = 'No tienes permisos para realizar esta acción';
    header("Location: ../roles.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['id']) && isset($_POST['estado'])) {
        
        $id = (int)$_POST['id'];
        $nuevo_estado = (int)$_POST['estado'];
        
        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            // Verificar que el rol existe
            $stmt = $conn->prepare("SELECT nombre FROM roles WHERE id = ?");
            $stmt->execute([$id]);
            $rol = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$rol) {
                $_SESSION['error'] = 'Rol no encontrado.';
                header("Location: ../roles.php");
                exit;
            }
            
            // Verificar que no tenga usuarios asignados si se va a desactivar
            if ($nuevo_estado == 0) {
                $stmt = $conn->prepare("SELECT COUNT(*) as total FROM usuarios WHERE rol = ?");
                $stmt->execute([$rol['nombre']]);
                $usuarios = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($usuarios['total'] > 0) {
                    $_SESSION['error'] = "No se puede desactivar el rol '{$rol['nombre']}' porque tiene {$usuarios['total']} usuario(s) asignado(s).";
                    header("Location: ../roles.php");
                    exit;
                }
            }
            
            // Actualizar el estado
            $stmt = $conn->prepare("UPDATE roles SET estado = ? WHERE id = ?");
            $resultado = $stmt->execute([$nuevo_estado, $id]);
            
            if ($resultado) {
                $accion = $nuevo_estado == 1 ? 'activado' : 'desactivado';
                $_SESSION['exito'] = "Rol '{$rol['nombre']}' {$accion} correctamente.";
            } else {
                $_SESSION['error'] = 'Error al cambiar el estado del rol.';
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

header("Location: ../roles.php");
exit;
?>
