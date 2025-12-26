<?php
// includes/Class-horas-extras.php
require_once 'conn-db.php';

class HoraExtra {
    private $conn;
    private $table_name = "solicitudes_horas_extras";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Crear nueva solicitud de horas extras
    public function crearSolicitud($datos) {
        // Calcular total de horas extras desde hora_inicio y hora_fin
        $hora_inicio = new DateTime($datos['hora_inicio']);
        $hora_fin = new DateTime($datos['hora_fin']);
        $diferencia = $hora_inicio->diff($hora_fin);
        $total_horas = $diferencia->h + ($diferencia->i / 60);
        
        // Si la hora de fin es menor que la de inicio, asumimos que cruza la medianoche
        if ($hora_fin < $hora_inicio) {
            $total_horas = (24 - $hora_inicio->format('H') - $hora_inicio->format('i')/60) + ($hora_fin->format('H') + $hora_fin->format('i')/60);
        }
        
        // Redondear a 2 decimales
        $total_horas = round($total_horas, 2);
        
        $query = "INSERT INTO " . $this->table_name . " 
                  (usuario_id, orden_produccion_id, fecha, hora_inicio, hora_fin, total_horas_extras, descripcion_trabajo, estado) 
                  VALUES (:usuario_id, :orden_produccion_id, :fecha, :hora_inicio, :hora_fin, :total_horas_extras, :descripcion_trabajo, :estado)";

        $stmt = $this->conn->prepare($query);

        $usuario_id = $datos['usuario_id'];
        $orden_produccion_id = $datos['orden_produccion_id'];
        $fecha = $datos['fecha'];
        $hora_inicio = $datos['hora_inicio'];
        $hora_fin = $datos['hora_fin'];
        $descripcion_trabajo = $datos['descripcion_trabajo'];
        $estado = $datos['estado'] ?? 'pendiente';

        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':orden_produccion_id', $orden_produccion_id);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':hora_inicio', $hora_inicio);
        $stmt->bindParam(':hora_fin', $hora_fin);
        $stmt->bindParam(':total_horas_extras', $total_horas);
        $stmt->bindParam(':descripcion_trabajo', $descripcion_trabajo);
        $stmt->bindParam(':estado', $estado);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Obtener solicitudes de horas extras por usuario
    public function obtenerSolicitudesPorUsuario($usuario_id, $estado = null) {
        $query = "SELECT she.*, op.codigo_op, op.nombre_producto, op.cliente,
                         u.nombre_completo as usuario_nombre,
                         ua.nombre_completo as aprobador_nombre
                  FROM " . $this->table_name . " she
                  INNER JOIN ordenes_produccion op ON she.orden_produccion_id = op.id
                  INNER JOIN usuarios u ON she.usuario_id = u.id
                  LEFT JOIN usuarios ua ON she.aprobado_por = ua.id
                  WHERE she.usuario_id = :usuario_id";
        
        if ($estado) {
            $query .= " AND she.estado = :estado";
        }
        
        $query .= " ORDER BY she.fecha DESC, she.id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        
        if ($estado) {
            $stmt->bindParam(':estado', $estado);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener todas las solicitudes (para administradores)
    public function obtenerTodasSolicitudes($filtros = []) {
        $query = "SELECT she.*, op.codigo_op, op.nombre_producto, op.cliente,
                         u.nombre_completo as usuario_nombre, u.username,
                         d.nombre as departamento,
                         ua.nombre_completo as aprobador_nombre
                  FROM " . $this->table_name . " she
                  INNER JOIN ordenes_produccion op ON she.orden_produccion_id = op.id
                  INNER JOIN usuarios u ON she.usuario_id = u.id
                  LEFT JOIN departamentos d ON u.departamento_id = d.id
                  LEFT JOIN usuarios ua ON she.aprobado_por = ua.id
                  WHERE 1=1";
        
        if (isset($filtros['estado'])) {
            // Si es un array de estados, usar IN
            if (is_array($filtros['estado'])) {
                $placeholders = str_repeat('?,', count($filtros['estado']) - 1) . '?';
                $query .= " AND she.estado IN ($placeholders)";
            } else {
                $query .= " AND she.estado = :estado";
            }
        }
        
        if (isset($filtros['fecha_inicio']) && isset($filtros['fecha_fin'])) {
            $query .= " AND she.fecha BETWEEN :fecha_inicio AND :fecha_fin";
        }
        
        if (isset($filtros['usuario_id'])) {
            $query .= " AND she.usuario_id = :usuario_id";
        }
        
        if (isset($filtros['orden_produccion_id'])) {
            $query .= " AND she.orden_produccion_id = :orden_produccion_id";
        }
        
        $query .= " ORDER BY 
                    CASE she.estado 
                        WHEN 'pendiente' THEN 1 
                        WHEN 'aprobada' THEN 2 
                        WHEN 'rechazada' THEN 3 
                    END,
                    she.fecha DESC,
                    she.created_at DESC";

        $stmt = $this->conn->prepare($query);
        
        if (isset($filtros['estado'])) {
            if (is_array($filtros['estado'])) {
                // Bind de cada estado en el array
                foreach ($filtros['estado'] as $key => $estado) {
                    $stmt->bindValue(($key + 1), $estado);
                }
            } else {
                $stmt->bindParam(':estado', $filtros['estado']);
            }
        }
        
        if (isset($filtros['fecha_inicio']) && isset($filtros['fecha_fin'])) {
            $stmt->bindParam(':fecha_inicio', $filtros['fecha_inicio']);
            $stmt->bindParam(':fecha_fin', $filtros['fecha_fin']);
        }
        
        if (isset($filtros['usuario_id'])) {
            $stmt->bindParam(':usuario_id', $filtros['usuario_id']);
        }
        
        if (isset($filtros['orden_produccion_id'])) {
            $stmt->bindParam(':orden_produccion_id', $filtros['orden_produccion_id']);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener solicitud por ID
    public function obtenerSolicitudPorId($id) {
        $query = "SELECT she.*, op.codigo_op, op.nombre_producto, op.cliente,
                         u.nombre_completo as usuario_nombre, u.email as usuario_email,
                         ua.nombre_completo as aprobador_nombre
                  FROM " . $this->table_name . " she
                  INNER JOIN ordenes_produccion op ON she.orden_produccion_id = op.id
                  INNER JOIN usuarios u ON she.usuario_id = u.id
                  LEFT JOIN usuarios ua ON she.aprobado_por = ua.id
                  WHERE she.id = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar solicitud
    public function actualizarSolicitud($id, $datos) {
        // Calcular total de horas extras desde hora_inicio y hora_fin
        $hora_inicio = new DateTime($datos['hora_inicio']);
        $hora_fin = new DateTime($datos['hora_fin']);
        $diferencia = $hora_inicio->diff($hora_fin);
        $total_horas = $diferencia->h + ($diferencia->i / 60);
        
        // Si la hora de fin es menor que la de inicio, asumimos que cruza la medianoche
        if ($hora_fin < $hora_inicio) {
            $total_horas = (24 - $hora_inicio->format('H') - $hora_inicio->format('i')/60) + ($hora_fin->format('H') + $hora_fin->format('i')/60);
        }
        
        // Redondear a 2 decimales
        $total_horas = round($total_horas, 2);
        
        $query = "UPDATE " . $this->table_name . " 
                  SET orden_produccion_id = :orden_produccion_id,
                      fecha = :fecha,
                      hora_inicio = :hora_inicio,
                      hora_fin = :hora_fin,
                      total_horas_extras = :total_horas_extras,
                      descripcion_trabajo = :descripcion_trabajo
                  WHERE id = :id AND estado = 'pendiente'";

        $stmt = $this->conn->prepare($query);

        $solicitud_id = $id;
        $orden_produccion_id = $datos['orden_produccion_id'];
        $fecha = $datos['fecha'];
        $hora_inicio = $datos['hora_inicio'];
        $hora_fin = $datos['hora_fin'];
        $descripcion_trabajo = $datos['descripcion_trabajo'];

        $stmt->bindParam(':id', $solicitud_id);
        $stmt->bindParam(':orden_produccion_id', $orden_produccion_id);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':hora_inicio', $hora_inicio);
        $stmt->bindParam(':hora_fin', $hora_fin);
        $stmt->bindParam(':total_horas_extras', $total_horas);
        $stmt->bindParam(':descripcion_trabajo', $descripcion_trabajo);

        return $stmt->execute();
    }

    // Aprobar solicitud
    public function aprobarSolicitud($id, $aprobador_id, $comentario = null) {
        // Iniciar transacción
        $this->conn->beginTransaction();
        
        try {
            // Actualizar estado de la solicitud
            $query = "UPDATE " . $this->table_name . " 
                      SET estado = 'aprobada',
                          aprobado_por = :aprobador_id,
                          fecha_respuesta = NOW(),
                          comentario_aprobacion = :comentario
                      WHERE id = :id AND estado = 'pendiente'";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':aprobador_id', $aprobador_id);
            $stmt->bindParam(':comentario', $comentario);
            $stmt->execute();

            // Obtener datos de la solicitud
            $solicitud = $this->obtenerSolicitudPorId($id);
            
            if ($solicitud) {
                // Crear registro de horas extras en la tabla registros_horas
                $query_registro = "INSERT INTO registros_horas 
                                   (usuario_id, orden_produccion_id, fecha, horas_trabajadas, descripcion_trabajo, estado) 
                                   VALUES (:usuario_id, :orden_produccion_id, :fecha, :horas_trabajadas, :descripcion_trabajo, 'registrado')";

                $stmt_registro = $this->conn->prepare($query_registro);
                
                $usuario_id = $solicitud['usuario_id'];
                $orden_produccion_id = $solicitud['orden_produccion_id'];
                $fecha = $solicitud['fecha'];
                $horas_trabajadas = $solicitud['total_horas_extras'];
                $descripcion_trabajo = $solicitud['descripcion_trabajo'];
                
                $stmt_registro->bindParam(':usuario_id', $usuario_id);
                $stmt_registro->bindParam(':orden_produccion_id', $orden_produccion_id);
                $stmt_registro->bindParam(':fecha', $fecha);
                $stmt_registro->bindParam(':horas_trabajadas', $horas_trabajadas);
                $stmt_registro->bindParam(':descripcion_trabajo', $descripcion_trabajo);
                
                $stmt_registro->execute();
            }

            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error al aprobar solicitud: " . $e->getMessage());
            return false;
        }
    }

    // Rechazar solicitud
    public function rechazarSolicitud($id, $rechazador_id, $comentario) {
        $query = "UPDATE " . $this->table_name . " 
                  SET estado = 'rechazada',
                      aprobado_por = :rechazador_id,
                      fecha_respuesta = NOW(),
                      comentario_aprobacion = :comentario
                  WHERE id = :id AND estado = 'pendiente'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':rechazador_id', $rechazador_id);
        $stmt->bindParam(':comentario', $comentario);

        return $stmt->execute();
    }

    // Cancelar solicitud (por el usuario)
    public function cancelarSolicitud($id, $usuario_id) {
        $query = "UPDATE " . $this->table_name . " 
                  SET estado = 'cancelada'
                  WHERE id = :id AND usuario_id = :usuario_id AND estado = 'pendiente'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':usuario_id', $usuario_id);

        return $stmt->execute();
    }

    // Eliminar solicitud
    public function eliminarSolicitud($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ? AND estado = 'pendiente'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }

    // Obtener estadísticas de horas extras
    public function obtenerEstadisticas($fecha_inicio = null, $fecha_fin = null) {
        $query = "SELECT 
                      COUNT(*) as total_solicitudes,
                      SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                      SUM(CASE WHEN estado = 'aprobada' THEN 1 ELSE 0 END) as aprobadas,
                      SUM(CASE WHEN estado = 'rechazada' THEN 1 ELSE 0 END) as rechazadas,
                      SUM(CASE WHEN estado = 'aprobada' THEN total_horas_extras ELSE 0 END) as total_horas_aprobadas,
                      SUM(CASE WHEN estado = 'pendiente' THEN total_horas_extras ELSE 0 END) as total_horas_pendientes
                  FROM " . $this->table_name;
        
        if ($fecha_inicio && $fecha_fin) {
            $query .= " WHERE fecha BETWEEN :fecha_inicio AND :fecha_fin";
        }

        $stmt = $this->conn->prepare($query);
        
        if ($fecha_inicio && $fecha_fin) {
            $stmt->bindParam(':fecha_inicio', $fecha_inicio);
            $stmt->bindParam(':fecha_fin', $fecha_fin);
        }
        
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener horas extras por usuario en un período
    public function obtenerHorasExtrasPorUsuario($usuario_id, $fecha_inicio, $fecha_fin) {
        // Consulta que cuenta las horas desde registros_horas que coinciden con solicitudes aprobadas
        // Esto es necesario porque cuando se aprueba una solicitud, se crea un registro en registros_horas
        $query = "SELECT 
                      COALESCE(SUM(CASE WHEN she.estado = 'aprobada' THEN she.total_horas_extras ELSE 0 END), 0) as horas_aprobadas,
                      COALESCE(SUM(CASE WHEN she.estado = 'pendiente' THEN she.total_horas_extras ELSE 0 END), 0) as horas_pendientes,
                      COUNT(CASE WHEN she.estado = 'aprobada' THEN 1 END) as solicitudes_aprobadas,
                      COUNT(CASE WHEN she.estado = 'pendiente' THEN 1 END) as solicitudes_pendientes,
                      COUNT(CASE WHEN she.estado = 'rechazada' THEN 1 END) as solicitudes_rechazadas
                  FROM " . $this->table_name . " she
                  WHERE she.usuario_id = :usuario_id
                    AND she.fecha BETWEEN :fecha_inicio AND :fecha_fin";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':fecha_fin', $fecha_fin);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener solicitudes pendientes (para notificaciones)
    public function obtenerSolicitudesPendientes() {
        $query = "SELECT she.*, op.codigo_op, op.nombre_producto,
                         u.nombre_completo as usuario_nombre, u.email as usuario_email
                  FROM " . $this->table_name . " she
                  INNER JOIN ordenes_produccion op ON she.orden_produccion_id = op.id
                  INNER JOIN usuarios u ON she.usuario_id = u.id
                  WHERE she.estado = 'pendiente'
                  ORDER BY she.fecha DESC, she.created_at ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
