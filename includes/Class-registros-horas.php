<?php
// includes/Class-registros-horas.php
require_once 'conn-db.php';

class RegistroHoras {
    private $conn;
    private $table_name = "registros_horas";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Crear nuevo registro de horas
    public function crearRegistro($datos) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (usuario_id, orden_produccion_id, fecha, horas_trabajadas, descripcion_trabajo, estado) 
                  VALUES (:usuario_id, :orden_produccion_id, :fecha, :horas_trabajadas, :descripcion_trabajo, :estado)";

        $stmt = $this->conn->prepare($query);

        $usuario_id = $datos['usuario_id'];
        $orden_produccion_id = $datos['orden_produccion_id'];
        $fecha = $datos['fecha'];
        $horas_trabajadas = $datos['horas_trabajadas'];
        $descripcion_trabajo = $datos['descripcion_trabajo'];
        $estado = $datos['estado'] ?? 'registrado';

        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':orden_produccion_id', $orden_produccion_id);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':horas_trabajadas', $horas_trabajadas);
        $stmt->bindParam(':descripcion_trabajo', $descripcion_trabajo);
        $stmt->bindParam(':estado', $estado);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Obtener registros de horas por usuario
    public function obtenerRegistrosPorUsuario($usuario_id, $fecha_inicio = null, $fecha_fin = null) {
        $query = "SELECT rh.*, op.codigo_op, op.nombre_producto, op.cliente,
                         u.nombre_completo as usuario_nombre
                  FROM " . $this->table_name . " rh
                  INNER JOIN ordenes_produccion op ON rh.orden_produccion_id = op.id
                  INNER JOIN usuarios u ON rh.usuario_id = u.id
                  WHERE rh.usuario_id = :usuario_id";
        
        if ($fecha_inicio && $fecha_fin) {
            $query .= " AND rh.fecha BETWEEN :fecha_inicio AND :fecha_fin";
        }
        
        $query .= " ORDER BY rh.fecha DESC, rh.id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        
        if ($fecha_inicio && $fecha_fin) {
            $stmt->bindParam(':fecha_inicio', $fecha_inicio);
            $stmt->bindParam(':fecha_fin', $fecha_fin);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener todos los registros de horas con filtros
    public function obtenerTodosRegistros($filtros = []) {
        $query = "SELECT rh.*, op.codigo_op, op.nombre_producto, op.cliente,
                         u.nombre_completo as usuario_nombre, u.username, u.departamento_id,
                         d.nombre as departamento, d.codigo as departamento_codigo,
                         0 as es_hora_extra
                  FROM " . $this->table_name . " rh
                  INNER JOIN ordenes_produccion op ON rh.orden_produccion_id = op.id
                  INNER JOIN usuarios u ON rh.usuario_id = u.id
                  LEFT JOIN departamentos d ON u.departamento_id = d.id
                  WHERE 1=1";
        
        if (isset($filtros['usuario_id'])) {
            $query .= " AND rh.usuario_id = :usuario_id";
        }
        
        if (isset($filtros['fecha_inicio']) && isset($filtros['fecha_fin'])) {
            $query .= " AND rh.fecha BETWEEN :fecha_inicio AND :fecha_fin";
        }
        
        if (isset($filtros['orden_produccion_id'])) {
            $query .= " AND rh.orden_produccion_id = :orden_produccion_id";
        }
        
        if (isset($filtros['estado'])) {
            $query .= " AND rh.estado = :estado";
        }
        
        $query .= " ORDER BY rh.fecha DESC, rh.id DESC";

        $stmt = $this->conn->prepare($query);
        
        if (isset($filtros['usuario_id'])) {
            $stmt->bindParam(':usuario_id', $filtros['usuario_id']);
        }
        
        if (isset($filtros['fecha_inicio']) && isset($filtros['fecha_fin'])) {
            $stmt->bindParam(':fecha_inicio', $filtros['fecha_inicio']);
            $stmt->bindParam(':fecha_fin', $filtros['fecha_fin']);
        }
        
        if (isset($filtros['orden_produccion_id'])) {
            $stmt->bindParam(':orden_produccion_id', $filtros['orden_produccion_id']);
        }
        
        if (isset($filtros['estado'])) {
            $stmt->bindParam(':estado', $filtros['estado']);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener registro por ID
    public function obtenerRegistroPorId($id) {
        $query = "SELECT rh.*, op.codigo_op, op.nombre_producto, op.cliente,
                         u.nombre_completo as usuario_nombre
                  FROM " . $this->table_name . " rh
                  INNER JOIN ordenes_produccion op ON rh.orden_produccion_id = op.id
                  INNER JOIN usuarios u ON rh.usuario_id = u.id
                  WHERE rh.id = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar registro de horas
    public function actualizarRegistro($id, $datos) {
        $query = "UPDATE " . $this->table_name . " 
                  SET orden_produccion_id = :orden_produccion_id,
                      fecha = :fecha,
                      horas_trabajadas = :horas_trabajadas,
                      descripcion_trabajo = :descripcion_trabajo,
                      estado = :estado
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $registro_id = $id;
        $orden_produccion_id = $datos['orden_produccion_id'];
        $fecha = $datos['fecha'];
        $horas_trabajadas = $datos['horas_trabajadas'];
        $descripcion_trabajo = $datos['descripcion_trabajo'];
        $estado = $datos['estado'];

        $stmt->bindParam(':id', $registro_id);
        $stmt->bindParam(':orden_produccion_id', $orden_produccion_id);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':horas_trabajadas', $horas_trabajadas);
        $stmt->bindParam(':descripcion_trabajo', $descripcion_trabajo);
        $stmt->bindParam(':estado', $estado);

        return $stmt->execute();
    }

    // Actualizar registro una vez (permitir solo una edición)
    public function actualizarRegistroUnaVez($id, $datos) {
        // Primero verificar que no haya sido editado
        $query_check = "SELECT editado, estado FROM " . $this->table_name . " WHERE id = :id";
        $stmt_check = $this->conn->prepare($query_check);
        $stmt_check->bindParam(':id', $id);
        $stmt_check->execute();
        $registro = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if (!$registro) {
            return false;
        }

        // Verificar que no haya sido editado previamente
        if (isset($registro['editado']) && $registro['editado'] == 1) {
            return false;
        }

        // Verificar que esté en estado registrado
        if ($registro['estado'] !== 'registrado') {
            return false;
        }

        // Actualizar el registro y marcar como editado
        $query = "UPDATE " . $this->table_name . " 
                  SET fecha = :fecha,
                      orden_produccion_id = :orden_produccion_id,
                      horas_trabajadas = :horas_trabajadas,
                      descripcion_trabajo = :descripcion_trabajo,
                      editado = 1,
                      updated_at = NOW()
                  WHERE id = :id AND editado = 0 AND estado = 'registrado'";

        $stmt = $this->conn->prepare($query);

        $registro_id = $id;
        $fecha = $datos['fecha'];
        $orden_produccion_id = $datos['orden_produccion_id'];
        $horas_trabajadas = $datos['horas_trabajadas'];
        $descripcion_trabajo = $datos['descripcion_trabajo'];

        $stmt->bindParam(':id', $registro_id);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':orden_produccion_id', $orden_produccion_id);
        $stmt->bindParam(':horas_trabajadas', $horas_trabajadas);
        $stmt->bindParam(':descripcion_trabajo', $descripcion_trabajo);

        return $stmt->execute();
    }

    // Eliminar registro
    public function eliminarRegistro($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }

    // Obtener total de horas trabajadas por usuario en un período
    public function obtenerTotalHorasPorUsuario($usuario_id, $fecha_inicio, $fecha_fin) {
        $query = "SELECT 
                      COALESCE(SUM(horas_trabajadas), 0) as total_horas,
                      COUNT(*) as total_registros
                  FROM " . $this->table_name . "
                  WHERE usuario_id = :usuario_id
                    AND fecha BETWEEN :fecha_inicio AND :fecha_fin
                    AND estado = 'registrado'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':fecha_fin', $fecha_fin);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener resumen de horas por orden de producción
    public function obtenerResumenPorOrden($orden_produccion_id) {
        $query = "SELECT 
                      u.id as usuario_id,
                      u.nombre_completo,
                      COUNT(*) as total_registros,
                      SUM(rh.horas_trabajadas) as total_horas,
                      MIN(rh.fecha) as fecha_inicio,
                      MAX(rh.fecha) as fecha_fin
                  FROM " . $this->table_name . " rh
                  INNER JOIN usuarios u ON rh.usuario_id = u.id
                  WHERE rh.orden_produccion_id = :orden_produccion_id
                    AND rh.estado = 'registrado'
                  GROUP BY u.id, u.nombre_completo
                  ORDER BY total_horas DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':orden_produccion_id', $orden_produccion_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Validar solapamiento de horarios
    // Obtener total de horas registradas en un día para un usuario
    public function obtenerTotalHorasDia($usuario_id, $fecha, $excluir_id = null) {
        $query = "SELECT COALESCE(SUM(horas_trabajadas), 0) as total 
                  FROM " . $this->table_name . " 
                  WHERE usuario_id = :usuario_id 
                    AND fecha = :fecha 
                    AND estado != 'rechazado'";
        
        if ($excluir_id) {
            $query .= " AND id != :excluir_id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':fecha', $fecha);
        
        if ($excluir_id) {
            $stmt->bindParam(':excluir_id', $excluir_id);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return floatval($row['total']);
    }

    // Obtener registros por orden de producción
    public function obtenerRegistrosPorOrden($orden_id) {
        $query = "SELECT rh.*, 
                         u.nombre_completo as usuario_nombre, u.username
                  FROM " . $this->table_name . " rh
                  INNER JOIN usuarios u ON rh.usuario_id = u.id
                  WHERE rh.orden_produccion_id = :orden_id
                  ORDER BY rh.fecha DESC, rh.id DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':orden_id', $orden_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener estadísticas generales
    public function obtenerEstadisticasGenerales($fecha_inicio = null, $fecha_fin = null) {
        $query = "SELECT 
                      COUNT(DISTINCT usuario_id) as total_usuarios,
                      COUNT(*) as total_registros,
                      SUM(horas_trabajadas) as total_horas,
                      AVG(horas_trabajadas) as promedio_horas,
                      COUNT(DISTINCT orden_produccion_id) as total_ordenes
                  FROM " . $this->table_name . "
                  WHERE estado = 'registrado'";
        
        if ($fecha_inicio && $fecha_fin) {
            $query .= " AND fecha BETWEEN :fecha_inicio AND :fecha_fin";
        }

        $stmt = $this->conn->prepare($query);
        
        if ($fecha_inicio && $fecha_fin) {
            $stmt->bindParam(':fecha_inicio', $fecha_inicio);
            $stmt->bindParam(':fecha_fin', $fecha_fin);
        }
        
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
