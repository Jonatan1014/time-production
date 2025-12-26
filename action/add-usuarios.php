<?php
// action/add-usuarios.php
session_start();
require_once '../includes/Class-usuario.php';
require_once '../includes/conn-db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar campos requeridos
    if (empty($_POST['nombres']) || empty($_POST['apellidos']) || empty($_POST['username']) || 
        empty($_POST['email']) || empty($_POST['password']) || empty($_POST['rol'])) {
        $_SESSION['error'] = 'Por favor, completa todos los campos obligatorios marcados con *.';
        header("Location: ../add-usuarios.php");
        exit;
    }
    
    try {
        $Usuario_class = new Usuario();
        
        // Verificar si el email o username ya existen
        if ($Usuario_class->verificarUsuarioExistente($_POST['email'], $_POST['username'])) {
            $_SESSION['error'] = 'El correo electrónico o nombre de usuario ya están registrados.';
            header("Location: ../add-usuarios.php");
            exit;
        }
        
        // Preparar datos del usuario
        $nombre_completo = trim($_POST['nombres']) . ' ' . trim($_POST['apellidos']);
        
        $datos = [
            'nombre_completo' => $nombre_completo,
            'username' => trim($_POST['username']),
            'email' => trim($_POST['email']),
            'password' => $_POST['password'],
            'departamento_id' => !empty($_POST['departamento_id']) ? intval($_POST['departamento_id']) : null,
            'fecha_ingreso' => !empty($_POST['fecha_ingreso']) ? $_POST['fecha_ingreso'] : null,
            'valor_hora_base' => isset($_POST['valor_hora_base']) ? floatval($_POST['valor_hora_base']) : 0,
            'rol' => $_POST['rol'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        
        // Registrar el usuario
        $nuevoId = $Usuario_class->crearUsuario($datos);

        if ($nuevoId !== false) {
            $_SESSION['exito'] = 'Usuario creado correctamente. Usuario: ' . $_POST['username'];
            header("Location: ../usuarios.php");
            exit;
        } else {
            $_SESSION['error'] = 'Error al crear el usuario. Inténtalo de nuevo.';
            header("Location: ../add-usuarios.php");
            exit;
        }
        
    } catch (Exception $e) {
        $_SESSION['error'] = 'Error del sistema: ' . $e->getMessage();
        header("Location: ../add-usuarios.php");
        exit;
    }
    
} else {
    $_SESSION['error'] = 'Método no permitido';
    header("Location: ../add-usuarios.php");
    exit;
}
?>