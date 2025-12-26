<?php
// includes/Class-departamentos.php
require_once 'conn-db.php';

class Departamento {
    private $conn;
    private $table_name = "departamentos";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Obtener todos los departamentos
    public function obtenerTodos($incluir_inactivos = false) {
        $query = "SELECT d.*, 
                         u.nombre_completo as responsable_nombre,
                         (SELECT COUNT(*) FROM usuarios WHERE departamento_id = d.id) as total_usuarios
                  FROM " . $this->table_name . " d
                  LEFT JOIN usuarios u ON d.responsable_id = u.id";
        
        if (!$incluir_inactivos) {
            $query .= " WHERE d.is_active = 1";
        }
        
        $query .= " ORDER BY d.nombre ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener departamento por ID
    public function obtenerPorId($id) {
        $query = "SELECT d.*, 
                         u.nombre_completo as responsable_nombre,
                         (SELECT COUNT(*) FROM usuarios WHERE departamento_id = d.id) as total_usuarios
                  FROM " . $this->table_name . " d
                  LEFT JOIN usuarios u ON d.responsable_id = u.id
                  WHERE d.id = ?
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Verificar si el código ya existe
    public function verificarCodigoExistente($codigo, $excluir_id = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE codigo = ?";
        
        if ($excluir_id) {
            $query .= " AND id != ?";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $codigo);
        
        if ($excluir_id) {
            $stmt->bindParam(2, $excluir_id, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    // Verificar si el nombre ya existe
    public function verificarNombreExistente($nombre, $excluir_id = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE nombre = ?";
        
        if ($excluir_id) {
            $query .= " AND id != ?";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $nombre);
        
        if ($excluir_id) {
            $stmt->bindParam(2, $excluir_id, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    // Crear nuevo departamento
    public function crear($datos) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nombre, descripcion, codigo, responsable_id, is_active) 
                  VALUES (:nombre, :descripcion, :codigo, :responsable_id, :is_active)";

        $stmt = $this->conn->prepare($query);

        // Asignar variables
        $nombre = $datos['nombre'];
        $descripcion = $datos['descripcion'] ?? null;
        $codigo = !empty($datos['codigo']) ? $datos['codigo'] : null;
        $responsable_id = !empty($datos['responsable_id']) ? $datos['responsable_id'] : null;
        $is_active = $datos['is_active'] ?? 1;

        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':responsable_id', $responsable_id, PDO::PARAM_INT);
        $stmt->bindParam(':is_active', $is_active, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }

    // Actualizar departamento
    public function actualizar($id, $datos) {
        $query = "UPDATE " . $this->table_name . " 
                  SET nombre = :nombre,
                      descripcion = :descripcion,
                      codigo = :codigo,
                      responsable_id = :responsable_id,
                      is_active = :is_active,
                      updated_at = CURRENT_TIMESTAMP
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Asignar variables
        $nombre = $datos['nombre'];
        $descripcion = $datos['descripcion'] ?? null;
        $codigo = !empty($datos['codigo']) ? $datos['codigo'] : null;
        $responsable_id = !empty($datos['responsable_id']) ? $datos['responsable_id'] : null;
        $is_active = $datos['is_active'] ?? 1;

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':responsable_id', $responsable_id, PDO::PARAM_INT);
        $stmt->bindParam(':is_active', $is_active, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Eliminar departamento (soft delete)
    public function eliminar($id) {
        // Verificar si tiene usuarios asignados
        $depto = $this->obtenerPorId($id);
        if ($depto && $depto['total_usuarios'] > 0) {
            return ['success' => false, 'message' => 'No se puede eliminar el departamento porque tiene ' . $depto['total_usuarios'] . ' usuario(s) asignado(s)'];
        }

        $query = "UPDATE " . $this->table_name . " 
                  SET is_active = 0, updated_at = CURRENT_TIMESTAMP 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Departamento desactivado correctamente'];
        }
        
        return ['success' => false, 'message' => 'Error al desactivar el departamento'];
    }

    // Activar departamento
    public function activar($id) {
        $query = "UPDATE " . $this->table_name . " 
                  SET is_active = 1, updated_at = CURRENT_TIMESTAMP 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    // Obtener estadísticas del departamento
    public function obtenerEstadisticas($id) {
        $query = "SELECT 
                    d.nombre,
                    d.codigo,
                    COUNT(DISTINCT u.id) as total_usuarios,
                    COUNT(DISTINCT CASE WHEN u.is_active = 1 THEN u.id END) as usuarios_activos,
                    COUNT(DISTINCT CASE WHEN u.rol = 'trabajador' THEN u.id END) as trabajadores,
                    COUNT(DISTINCT CASE WHEN u.rol = 'administrador' THEN u.id END) as administradores
                  FROM " . $this->table_name . " d
                  LEFT JOIN usuarios u ON d.id = u.departamento_id
                  WHERE d.id = ?
                  GROUP BY d.id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
