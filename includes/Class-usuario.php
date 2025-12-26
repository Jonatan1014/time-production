<?php
// includes/Class-usuario.php
require_once 'conn-db.php';

class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Verificar si el usuario ya existe por email o username
    public function verificarUsuarioExistente($email, $username) {
        $query = "SELECT id FROM " . $this->table_name . " 
                  WHERE email = ? OR username = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(1, $email);
        $stmt->bindParam(2, $username);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    // Crear nuevo usuario
    public function crearUsuario($datos) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nombre_completo, username, email, password, rol, departamento_id, cargo_id, valor_hora_base, fecha_ingreso, is_active) 
                  VALUES (:nombre_completo, :username, :email, :password, :rol, :departamento_id, :cargo_id, :valor_hora_base, :fecha_ingreso, :is_active)";

        $stmt = $this->conn->prepare($query);

        // Asignar variables para bindParam
        $nombre_completo = $datos['nombre_completo'];
        $username = $datos['username'];
        $email = $datos['email'];
        $password_hash = password_hash($datos['password'], PASSWORD_DEFAULT);
        $rol = $datos['rol'];
        $departamento_id = !empty($datos['departamento_id']) ? $datos['departamento_id'] : null;
        $cargo_id = !empty($datos['cargo_id']) ? $datos['cargo_id'] : null;
        $valor_hora_base = $datos['valor_hora_base'] ?? 0;
        $fecha_ingreso = $datos['fecha_ingreso'] ?? null;
        $is_active = $datos['is_active'] ?? 1;

        $stmt->bindParam(':nombre_completo', $nombre_completo);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':rol', $rol);
        $stmt->bindParam(':departamento_id', $departamento_id, PDO::PARAM_INT);
        $stmt->bindParam(':cargo_id', $cargo_id, PDO::PARAM_INT);
        $stmt->bindParam(':valor_hora_base', $valor_hora_base);
        $stmt->bindParam(':fecha_ingreso', $fecha_ingreso);
        $stmt->bindParam(':is_active', $is_active);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Obtener todos los usuarios (incluye activos e inactivos)
    public function obtenerUsuarios($filtros = []) {
        $query = "SELECT u.*, d.nombre as departamento, c.nombre as cargo 
                  FROM " . $this->table_name . " u
                  LEFT JOIN departamentos d ON u.departamento_id = d.id
                  LEFT JOIN cargos c ON u.cargo_id = c.id
                  WHERE 1=1";
        
        if (isset($filtros['rol'])) {
            $query .= " AND u.rol = :rol";
        }
        
        if (isset($filtros['departamento_id'])) {
            $query .= " AND u.departamento_id = :departamento_id";
        }
        
        $query .= " ORDER BY u.created_at DESC";

        $stmt = $this->conn->prepare($query);
        
        if (isset($filtros['rol'])) {
            $stmt->bindParam(':rol', $filtros['rol']);
        }
        
        if (isset($filtros['departamento_id'])) {
            $stmt->bindParam(':departamento_id', $filtros['departamento_id']);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener usuario por ID
    public function obtenerUsuarioPorId($id) {
        $query = "SELECT u.*, d.nombre as departamento, c.nombre as cargo
                  FROM " . $this->table_name . " u
                  LEFT JOIN departamentos d ON u.departamento_id = d.id
                  LEFT JOIN cargos c ON u.cargo_id = c.id
                  WHERE u.id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);

        // Asignar variable para bindParam
        $user_id = $id;

        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar usuario
    public function actualizarUsuario($id, $datos) {
        $query = "UPDATE " . $this->table_name . " 
                  SET nombre_completo = :nombre_completo, username = :username, 
                      email = :email, rol = :rol, departamento_id = :departamento_id, 
                      cargo_id = :cargo_id, valor_hora_base = :valor_hora_base, 
                      fecha_ingreso = :fecha_ingreso, is_active = :is_active" . 
                  (isset($datos['password']) && !empty($datos['password']) ? ", password = :password" : "") . 
                  " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Asignar variables para bindParam
        $user_id = $id;
        $nombre_completo = $datos['nombre_completo'];
        $username = $datos['username'];
        $email = $datos['email'];
        $rol = $datos['rol'];
        $departamento_id = !empty($datos['departamento_id']) ? $datos['departamento_id'] : null;
        $cargo_id = !empty($datos['cargo_id']) ? $datos['cargo_id'] : null;
        $valor_hora_base = $datos['valor_hora_base'] ?? 0;
        $fecha_ingreso = $datos['fecha_ingreso'] ?? null;
        $is_active = $datos['is_active'] ?? 1;

        $stmt->bindParam(':id', $user_id);
        $stmt->bindParam(':nombre_completo', $nombre_completo);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':rol', $rol);
        $stmt->bindParam(':departamento_id', $departamento_id, PDO::PARAM_INT);
        $stmt->bindParam(':cargo_id', $cargo_id, PDO::PARAM_INT);
        $stmt->bindParam(':valor_hora_base', $valor_hora_base);
        $stmt->bindParam(':fecha_ingreso', $fecha_ingreso);
        $stmt->bindParam(':is_active', $is_active);

        if (isset($datos['password']) && !empty($datos['password'])) {
            $password_hash = password_hash($datos['password'], PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $password_hash);
        }

        return $stmt->execute();
    }

    // Eliminar usuario (cambiar is_active a 0)
    public function eliminarUsuario($id) {
        $query = "UPDATE " . $this->table_name . " SET is_active = 0 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        // Asignar variable para bindParam
        $user_id = $id;
        
        $stmt->bindParam(1, $user_id);
        return $stmt->execute();
    }


    // Añadir esta función a la clase Usuario (en includes/Class-usuario.php)

    // Validar credenciales para login - Versión mejorada
    public function validarCredenciales($email, $password) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE email = ? AND is_active = 1 LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        
        // Asignar variable para bindParam
        $user_email = $email;
        
        $stmt->bindParam(1, $user_email);
        $stmt->execute();
        
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario && password_verify($password, $usuario['password'])) {
            return $usuario;
        }
        return false;
    }

    // Obtener información completa del usuario por email
    public function obtenerUsuarioPorEmail($email) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        
        // Asignar variable para bindParam
        $user_email = $email;
        
        $stmt->bindParam(1, $user_email);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // En includes/Class-usuario.php o en un nuevo archivo includes/auth.php

    public function usuarioLogueado() {
        return isset($_SESSION['user_id']) && isset($_SESSION['user_token']);
    }

    public function verificarPermisos($rol_requerido) {
        if (!$this->usuarioLogueado()) {
            return false;
        }
        
        $rol_usuario = strtolower($_SESSION['user_rol'] ?? '');
        $rol_requerido = strtolower($rol_requerido);
        
        return $rol_usuario === $rol_requerido || 
               $rol_usuario === 'administrador' || 
               $rol_usuario === 'root';
    }

    // Verificar si el usuario actual es administrador
    public function esAdministrador() {
        if (!$this->usuarioLogueado()) {
            return false;
        }
        
        $rol_usuario = strtolower($_SESSION['user_rol'] ?? '');
        return $rol_usuario === 'administrador' || $rol_usuario === 'root';
    }

    // Obtener roles disponibles (ENUM de la tabla usuarios)
    public function obtenerRoles() {
        // Roles definidos por el ENUM en la base de datos
        return [
            ['nombre' => 'administrador', 'descripcion' => 'Administrador del sistema'],
            ['nombre' => 'trabajador', 'descripcion' => 'Trabajador de producción']
        ];
    }

    // Verificar si un email ya existe (excepto para un usuario específico)
    public function verificarEmailExistente($email, $id_usuario_excluir = null) {
        if ($id_usuario_excluir) {
            $query = "SELECT COUNT(*) as total FROM usuarios WHERE email = :email AND id != :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':id', $id_usuario_excluir);
        } else {
            $query = "SELECT COUNT(*) as total FROM usuarios WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'] > 0;
    }

    // Actualizar contraseña
    public function actualizarPassword($id_usuario, $password_hash) {
        $query = "UPDATE usuarios SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':id', $id_usuario);
        
        return $stmt->execute();
    }

    // Actualizar datos de perfil
    public function actualizarDatosPerfil($id_usuario, $datos) {
        $query = "UPDATE usuarios 
                  SET nombre_completo = :nombre_completo,
                      username = :username,
                      email = :email,
                      departamento_id = :departamento_id
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $departamento_id = !empty($datos['departamento_id']) ? $datos['departamento_id'] : null;
        
        $stmt->bindParam(':nombre_completo', $datos['nombre_completo']);
        $stmt->bindParam(':username', $datos['username']);
        $stmt->bindParam(':email', $datos['email']);
        $stmt->bindParam(':departamento_id', $departamento_id, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id_usuario);
        
        return $stmt->execute();
    }

    // Actualizar imagen de perfil
    public function actualizarImagenPerfil($id_usuario, $imagen_url) {
        $query = "UPDATE usuarios SET imagen_perfil = :imagen_perfil WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':imagen_perfil', $imagen_url);
        $stmt->bindParam(':id', $id_usuario);
        
        return $stmt->execute();
    }

    // Obtener departamentos activos
    public function obtenerDepartamentosActivos() {
        $query = "SELECT id, nombre, codigo, descripcion 
                  FROM departamentos 
                  WHERE is_active = 1 
                  ORDER BY nombre ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener todos los departamentos (incluyendo inactivos)
    public function obtenerTodosDepartamentos() {
        $query = "SELECT id, nombre, codigo, descripcion, is_active 
                  FROM departamentos 
                  ORDER BY nombre ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>