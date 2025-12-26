<?php
// action/update-usuarios.php
require_once '../includes/Class-usuario.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['id']) && !empty($_POST['nombre_completo']) && !empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['rol'])) {
        
        $id = intval($_POST['id']);
        $nombre_completo = trim($_POST['nombre_completo']);
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $departamento_id = !empty($_POST['departamento_id']) ? intval($_POST['departamento_id']) : null;
        $cargo_id = !empty($_POST['cargo_id']) ? intval($_POST['cargo_id']) : null;
        $fecha_ingreso = !empty($_POST['fecha_ingreso']) ? $_POST['fecha_ingreso'] : null;
        $valor_hora_base = isset($_POST['valor_hora_base']) ? floatval($_POST['valor_hora_base']) : 0;
        $rol = strtolower(trim($_POST['rol']));
        $is_active = isset($_POST['is_active']) && $_POST['is_active'] == '1' ? 1 : 0;
        $password = !empty($_POST['password']) ? trim($_POST['password']) : null;
        
        $Usuario_class = new Usuario();
        
        // Verificar si cambió el email o username y si ya existe otro usuario con esos datos
        $usuario_actual = $Usuario_class->obtenerUsuarioPorId($id);
        if (!$usuario_actual) {
            $_SESSION['error'] = 'Usuario no encontrado.';
            header("Location: ../usuarios.php");
            exit;
        }
        
        // Verificar si el email cambió y ya existe
        if ($email !== $usuario_actual['email']) {
            $existe_email = $Usuario_class->verificarUsuarioExistente($email, null);
            if ($existe_email) {
                $_SESSION['error'] = 'El correo electrónico ya está registrado por otro usuario.';
                header("Location: ../update-usuarios.php?id=" . $id);
                exit;
            }
        }
        
        // Verificar si el username cambió y ya existe
        if ($username !== $usuario_actual['username']) {
            $existe_username = $Usuario_class->verificarUsuarioExistente(null, $username);
            if ($existe_username) {
                $_SESSION['error'] = 'El nombre de usuario ya está en uso por otro usuario.';
                header("Location: ../update-usuarios.php?id=" . $id);
                exit;
            }
        }
        
        // Validar formato de username
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $_SESSION['error'] = 'El nombre de usuario solo puede contener letras, números y guiones bajos.';
            header("Location: ../update-usuarios.php?id=" . $id);
            exit;
        }
        
        // Validar longitud de contraseña si se proporciona una nueva
        if ($password !== null && strlen($password) < 6) {
            $_SESSION['error'] = 'La contraseña debe tener al menos 6 caracteres.';
            header("Location: ../update-usuarios.php?id=" . $id);
            exit;
        }
        
        $datos = [
            'nombre_completo' => $nombre_completo,
            'username' => $username,
            'email' => $email,
            'departamento_id' => $departamento_id,
            'cargo_id' => $cargo_id,
            'valor_hora_base' => $valor_hora_base,
            'fecha_ingreso' => $fecha_ingreso,
            'rol' => $rol,
            'is_active' => $is_active
        ];

        // Solo incluir password si se proporcionó una nueva
        if ($password !== null) {
            $datos['password'] = $password;
        }
        
        try {
            $resultado = $Usuario_class->actualizarUsuario($id, $datos);

            if ($resultado) {
                // Actualizar datos de sesión si el usuario está editando su propio perfil
                if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $id) {
                    $_SESSION['user_nombre'] = $nombre_completo;
                    $_SESSION['user_username'] = $username;
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_rol'] = $rol;
                    if ($departamento_id !== null) {
                        $_SESSION['user_departamento_id'] = $departamento_id;
                    }
                }
                
                $_SESSION['exito'] = 'Usuario actualizado correctamente';
                header("Location: ../usuarios.php");
                exit;
            } else {
                $_SESSION['error'] = 'Error al actualizar el usuario. No se realizaron cambios.';
                header("Location: ../update-usuarios.php?id=" . $id);
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
            header("Location: ../update-usuarios.php?id=" . $id);
            exit;
        }
    } else {
        $_SESSION['error'] = 'Por favor, completa todos los campos obligatorios (nombre completo, username, email, rol).';
        header("Location: ../update-usuarios.php?id=" . ($_POST['id'] ?? ''));
        exit;
    }
} else {
    $_SESSION['error'] = 'Método no permitido';
    header("Location: ../usuarios.php");
    exit;
}
?>