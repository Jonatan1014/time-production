<?php
// action/update-role.php
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
    if (!empty($_POST['id']) && !empty($_POST['nombre'])) {
        
        $id = (int)$_POST['id'];
        $nombre = trim($_POST['nombre']);
        $descripcion = !empty($_POST['descripcion']) ? trim($_POST['descripcion']) : null;
        
        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            // Verificar que el rol existe
            $stmt = $conn->prepare("SELECT id FROM roles WHERE id = ?");
            $stmt->execute([$id]);
            
            if (!$stmt->fetch()) {
                $_SESSION['error'] = 'Rol no encontrado.';
                header("Location: ../roles.php");
                exit;
            }
            
            // Verificar que no exista otro rol con el mismo nombre
            $stmt = $conn->prepare("SELECT id FROM roles WHERE nombre = ? AND id != ?");
            $stmt->execute([$nombre, $id]);
            
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Ya existe un rol con ese nombre.';
                header("Location: ../roles.php");
                exit;
            }
            
            // Actualizar el rol
            $stmt = $conn->prepare("UPDATE roles SET nombre = ?, descripcion = ? WHERE id = ?");
            $resultado = $stmt->execute([$nombre, $descripcion, $id]);
            
            if ($resultado) {
                $_SESSION['exito'] = "Rol '{$nombre}' actualizado correctamente.";
            } else {
                $_SESSION['error'] = 'Error al actualizar el rol.';
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
