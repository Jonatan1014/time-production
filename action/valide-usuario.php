<?php
// action/valide-usuario.php
session_start();
require_once '../includes/Class-usuario.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['emailaddress']) && !empty($_POST['password'])) {
        
        $email = $_POST['emailaddress'];
        $password = $_POST['password'];
        $recuerdame = isset($_POST['recuerdame']) ? true : false;
        
        $Usuario_class = new Usuario();
        
        try {
            // Validar credenciales
            $usuario = $Usuario_class->validarCredenciales($email, $password);
            
            if ($usuario) {
                // Iniciar sesión con los datos del usuario
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['user_nombre'] = $usuario['nombre_completo'];
                $_SESSION['user_username'] = $usuario['username'];
                $_SESSION['user_email'] = $usuario['email'];
                $_SESSION['user_rol'] = $usuario['rol'];
                $_SESSION['user_departamento'] = $usuario['departamento'] ?? '';
                $_SESSION['user_imagen'] = 'assets/images/users/user-default.png'; // Imagen por defecto
                
                // Opcional: Crear un token de sesión para mayor seguridad
                $_SESSION['user_token'] = bin2hex(random_bytes(32));
                
                // Manejar "Recuérdame"
                if ($recuerdame) {
                    // Crear cookie con token (opcional, para sesión persistente)
                    $token = bin2hex(random_bytes(32));
                    setcookie('remember_token', $token, time() + (86400 * 30), "/"); // 30 días
                    // Aquí normalmente guardarías el token en la base de datos
                }
                
                // Redirigir al dashboard
                header("Location: ../index.php");
                exit;
                
            } else {
                $_SESSION['error'] = 'Credenciales incorrectas. Por favor, verifica tu email y contraseña.';
                header("Location: ../login.php");
                exit;
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al procesar el login: ' . $e->getMessage();
            header("Location: ../login.php");
            exit;
        }
        
    } else {
        $_SESSION['error'] = 'Por favor, ingresa tu email y contraseña.';
        header("Location: ../login.php");
        exit;
    }
    
} else {
    $_SESSION['error'] = 'Método no permitido';
    header("Location: ../login.php");
    exit;
}
?>