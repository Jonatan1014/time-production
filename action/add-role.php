<?php
// action/add-role.php
session_start();
require_once '../includes/conn-db.php';
require_once '../includes/Class-usuario.php';

// Verificar permisos de administrador
$Usuario_class = new Usuario();
if (!$Usuario_class->usuarioLogueado() || !$Usuario_class->verificarPermisos('Administrador')) {
    $_SESSION['error'] = 'No tienes permisos para realizar esta acción';
    header('Location: ../roles.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Método no permitido';
    header('Location: ../roles.php');
    exit;
}

$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');

if (empty($nombre)) {
    $_SESSION['error'] = 'El nombre del rol es obligatorio';
    header('Location: ../roles.php');
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Verificar que no exista un rol con el mismo nombre
    $stmt = $conn->prepare("SELECT id FROM roles WHERE nombre = ?");
    $stmt->execute([$nombre]);
    
    if ($stmt->fetch()) {
        $_SESSION['error'] = 'Ya existe un rol con ese nombre';
        header('Location: ../roles.php');
        exit;
    }
    
    // Crear el nuevo rol
    $stmt = $conn->prepare("INSERT INTO roles (nombre, descripcion, estado) VALUES (?, ?, 1)");
    $ok = $stmt->execute([$nombre, $descripcion]);
    
    if ($ok) {
        $_SESSION['exito'] = "Rol '{$nombre}' creado correctamente";
    } else {
        $_SESSION['error'] = 'Error al crear el rol';
    }
} catch (Exception $e) {
    $_SESSION['error'] = 'Error del sistema: ' . $e->getMessage();
}

header('Location: ../roles.php');
exit;
?>