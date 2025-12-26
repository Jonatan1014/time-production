<?php
session_start();
require_once '../includes/Class-usuario.php';

$Usuario_class = new Usuario();

// Verificar que el usuario esté logueado
if (!$Usuario_class->usuarioLogueado()) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ==================== SUBIR IMAGEN DE PERFIL ====================
if (isset($_FILES['imagen_perfil'])) {
    // Función helper para devolver JSON limpiando cualquier salida previa
    function send_json_and_exit($payload) {
        // Limpiar buffers de salida para evitar contenido no-JSON (warnings, espacios, etc.)
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }

    $file = $_FILES['imagen_perfil'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    // Validar tipo de archivo
    if (!in_array($file['type'], $allowed_types)) {
        send_json_and_exit([
            'success' => false,
            'message' => 'Tipo de archivo no permitido. Solo se permiten JPG, PNG y GIF'
        ]);
    }
    
    // Validar tamaño
    if ($file['size'] > $max_size) {
        send_json_and_exit([
            'success' => false,
            'message' => 'El archivo es demasiado grande. Máximo 5MB'
        ]);
    }
    
    // Crear directorio si no existe (usar carpeta assets/images/users)
    $upload_dir = '../assets/images/users/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Generar nombre único para la imagen
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'user_' . $user_id . '_' . time() . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    // Obtener usuario actual para eliminar imagen anterior
    $usuario_actual = $Usuario_class->obtenerUsuarioPorId($user_id);
    
    // Subir archivo
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
    // Actualizar ruta en base de datos
    $imagen_url = 'assets/images/users/' . $filename;
        
        try {
            $Usuario_class->actualizarImagenPerfil($user_id, $imagen_url);
            // Actualizar también la variable de sesión para que el header muestre la nueva imagen sin recargar sesión
            $_SESSION['user_img'] = $imagen_url;
            $_SESSION['user_imagen'] = $imagen_url;
            
            // Eliminar imagen anterior si existe y no es la default
            if (!empty($usuario_actual['imagen_perfil']) && 
                $usuario_actual['imagen_perfil'] !== 'assets/images/users/user-default.png' &&
                file_exists('../' . $usuario_actual['imagen_perfil'])) {
                unlink('../' . $usuario_actual['imagen_perfil']);
            }
            
            send_json_and_exit([
                'success' => true,
                'message' => 'Foto de perfil actualizada correctamente',
                'imagen_url' => $imagen_url
            ]);
        } catch (Exception $e) {
            // Si falla la actualización, eliminar el archivo subido
            if (file_exists($filepath)) {
                unlink($filepath);
            }
            send_json_and_exit([
                'success' => false,
                'message' => 'Error al actualizar la imagen: ' . $e->getMessage()
            ]);
        }
    } else {
        send_json_and_exit([
            'success' => false,
            'message' => 'Error al subir el archivo'
        ]);
    }
    exit;
}

// ==================== ACTUALIZAR DATOS PERSONALES ====================
if (isset($_POST['actualizar_datos'])) {
    $nombre_completo = trim($_POST['nombre_completo']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $departamento = trim($_POST['departamento']);
    
    // Validaciones básicas
    if (empty($nombre_completo) || empty($username) || empty($email)) {
        $_SESSION['error'] = 'Por favor completa todos los campos obligatorios';
        header("Location: ../mi-perfil.php");
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'El formato del email no es válido';
        header("Location: ../mi-perfil.php");
        exit;
    }
    
    // Verificar si el email ya existe (excepto el usuario actual)
    $email_existe = $Usuario_class->verificarEmailExistente($email, $user_id);
    if ($email_existe) {
        $_SESSION['error'] = 'El email ya está registrado por otro usuario';
        header("Location: ../mi-perfil.php");
        exit;
    }
    
    // Preparar datos para actualizar
    $datos = [
        'nombre_completo' => $nombre_completo,
        'username' => $username,
        'email' => $email,
        'departamento' => $departamento
    ];
    
    try {
        $resultado = $Usuario_class->actualizarDatosPerfil($user_id, $datos);
        
        if ($resultado) {
            // Actualizar sesión con el nuevo nombre
            $_SESSION['user_nombre'] = $nombre_completo;
            $_SESSION['user_username'] = $username;
            $_SESSION['exito'] = 'Datos personales actualizados correctamente';
        } else {
            $_SESSION['error'] = 'No se pudo actualizar la información';
        }
    } catch (Exception $e) {
        $_SESSION['error'] = 'Error al actualizar: ' . $e->getMessage();
    }
    
    header("Location: ../mi-perfil.php");
    exit;
}

// ==================== CAMBIAR CONTRASEÑA ====================
if (isset($_POST['cambiar_password'])) {
    $password_actual = $_POST['password_actual'];
    $password_nueva = $_POST['password_nueva'];
    $password_confirmar = $_POST['password_confirmar'];
    
    // Validaciones
    if (empty($password_actual) || empty($password_nueva) || empty($password_confirmar)) {
        $_SESSION['error'] = 'Por favor completa todos los campos de contraseña';
        header("Location: ../mi-perfil.php");
        exit;
    }
    
    if (strlen($password_nueva) < 8) {
        $_SESSION['error'] = 'La nueva contraseña debe tener al menos 8 caracteres';
        header("Location: ../mi-perfil.php");
        exit;
    }
    
    if ($password_nueva !== $password_confirmar) {
        $_SESSION['error'] = 'Las contraseñas no coinciden';
        header("Location: ../mi-perfil.php");
        exit;
    }
    
    // Obtener usuario actual
    $usuario = $Usuario_class->obtenerUsuarioPorId($user_id);
    
    // Verificar contraseña actual
    if (!password_verify($password_actual, $usuario['password'])) {
        $_SESSION['error'] = 'La contraseña actual es incorrecta';
        header("Location: ../mi-perfil.php");
        exit;
    }
    
    // Actualizar contraseña
    try {
        $password_hash = password_hash($password_nueva, PASSWORD_DEFAULT);
        $resultado = $Usuario_class->actualizarPassword($user_id, $password_hash);
        
        if ($resultado) {
            $_SESSION['exito'] = 'Contraseña actualizada correctamente';
        } else {
            $_SESSION['error'] = 'No se pudo actualizar la contraseña';
        }
    } catch (Exception $e) {
        $_SESSION['error'] = 'Error al cambiar la contraseña: ' . $e->getMessage();
    }
    
    header("Location: ../mi-perfil.php");
    exit;
}

// Si no hay acción válida, redirigir
header("Location: ../mi-perfil.php");
exit;
?>
