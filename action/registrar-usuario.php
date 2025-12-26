<?php
// action/registrar-usuario.php
session_start();
require_once '../includes/Class-usuario.php';
require_once '../includes/conn-db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Validar que todos los campos requeridos estén presentes
    $campos_requeridos = ['nombres', 'apellidos', 'email', 'username', 'password', 'password_confirm'];
    foreach ($campos_requeridos as $campo) {
        if (empty($_POST[$campo])) {
            $_SESSION['error'] = 'Todos los campos marcados con * son obligatorios.';
            header("Location: ../registro.php");
            exit;
        }
    }
    
    // Validar que las contraseñas coincidan
    if ($_POST['password'] !== $_POST['password_confirm']) {
        $_SESSION['error'] = 'Las contraseñas no coinciden.';
        header("Location: ../registro.php");
        exit;
    }
    
    // Validar longitud mínima de la contraseña
    if (strlen($_POST['password']) < 6) {
        $_SESSION['error'] = 'La contraseña debe tener al menos 6 caracteres.';
        header("Location: ../registro.php");
        exit;
    }
    
    // Validar términos y condiciones
    if (!isset($_POST['terminos'])) {
        $_SESSION['error'] = 'Debes aceptar los términos y condiciones.';
        header("Location: ../registro.php");
        exit;
    }
    
    try {
        $Usuario_class = new Usuario();
        
        // Verificar si el email o username ya existen
        if ($Usuario_class->verificarUsuarioExistente($_POST['email'], $_POST['username'])) {
            $_SESSION['error'] = 'El correo electrónico o nombre de usuario ya están registrados. Por favor, usa otros datos.';
            header("Location: ../registro.php");
            exit;
        }
        
        // Preparar datos del usuario
        $nombre_completo = trim($_POST['nombres']) . ' ' . trim($_POST['apellidos']);
        
        $datos_usuario = [
            'nombre_completo' => $nombre_completo,
            'username' => trim($_POST['username']),
            'email' => trim($_POST['email']),
            'password' => $_POST['password'], // Se hasheará en la clase
            'departamento_id' => !empty($_POST['departamento_id']) ? intval($_POST['departamento_id']) : null,
            'rol' => 'trabajador', // Por defecto, los nuevos registros son trabajadores
            'is_active' => 0 // Estado inactivo hasta que un administrador lo active
        ];
        
        // Crear el usuario
        $resultado = $Usuario_class->crearUsuario($datos_usuario);
        
        if ($resultado !== false) {
            $_SESSION['exito'] = 'Registro exitoso. Tu cuenta está pendiente de aprobación por un administrador. Te notificaremos cuando esté activa.';
            header("Location: ../login.php");
            exit;
        } else {
            $_SESSION['error'] = 'Error al crear el usuario. Inténtalo de nuevo.';
            header("Location: ../registro.php");
            exit;
        }
        
    } catch (Exception $e) {
        $_SESSION['error'] = 'Error del sistema: ' . $e->getMessage();
        header("Location: ../registro.php");
        exit;
    }
    
} else {
    $_SESSION['error'] = 'Método no permitido';
    header("Location: ../registro.php");
    exit;
}
?>
