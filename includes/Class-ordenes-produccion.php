<?php
// includes/Class-ordenes-produccion.php
require_once 'conn-db.php';

class OrdenProduccion {
    private $conn;
    private $table_name = "ordenes_produccion";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Crear nueva orden de producción
    public function crearOrden($datos) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (codigo_op, nombre_producto, descripcion, cliente, cantidad_objetivo, 
                   fecha_inicio, fecha_fin_estimada, estado, prioridad) 
                  VALUES (:codigo_op, :nombre_producto, :descripcion, :cliente, :cantidad_objetivo, 
                          :fecha_inicio, :fecha_fin_estimada, :estado, :prioridad)";

        $stmt = $this->conn->prepare($query);

        $codigo_op = $datos['codigo_op'];
        $nombre_producto = $datos['nombre_producto'];
        $descripcion = $datos['descripcion'] ?? '';
        $cliente = $datos['cliente'];
        $cantidad_objetivo = $datos['cantidad_objetivo'];
        $fecha_inicio = $datos['fecha_inicio'];
        $fecha_fin_estimada = $datos['fecha_fin_estimada'];
        $estado = $datos['estado'] ?? 'activa';
        $prioridad = $datos['prioridad'] ?? 'media';

        $stmt->bindParam(':codigo_op', $codigo_op);
        $stmt->bindParam(':nombre_producto', $nombre_producto);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':cliente', $cliente);
        $stmt->bindParam(':cantidad_objetivo', $cantidad_objetivo);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':fecha_fin_estimada', $fecha_fin_estimada);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':prioridad', $prioridad);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Obtener todas las órdenes de producción
    public function obtenerOrdenes($filtros = []) {
        $query = "SELECT op.*,
                         COUNT(DISTINCT rh.id) as total_registros,
                         COUNT(DISTINCT rh.usuario_id) as total_usuarios,
                         COALESCE(SUM(rh.horas_trabajadas), 0) as horas_trabajadas
                  FROM " . $this->table_name . " op
                  LEFT JOIN registros_horas rh ON op.id = rh.orden_produccion_id AND rh.estado = 'registrado'
                  WHERE 1=1";
        
        if (isset($filtros['estado'])) {
            $query .= " AND op.estado = :estado";
        }
        
        if (isset($filtros['prioridad'])) {
            $query .= " AND op.prioridad = :prioridad";
        }
        
        $query .= " GROUP BY op.id
                    ORDER BY 
                        CASE op.prioridad 
                            WHEN 'urgente' THEN 1 
                            WHEN 'alta' THEN 2 
                            WHEN 'media' THEN 3 
                            WHEN 'baja' THEN 4 
                        END,
                        op.fecha_inicio DESC";

        $stmt = $this->conn->prepare($query);
        
        if (isset($filtros['estado'])) {
            $stmt->bindParam(':estado', $filtros['estado']);
        }
        
        if (isset($filtros['prioridad'])) {
            $stmt->bindParam(':prioridad', $filtros['prioridad']);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener orden por ID
    public function obtenerOrdenPorId($id) {
        $query = "SELECT op.*,
                         COUNT(DISTINCT rh.id) as total_registros,
                         COUNT(DISTINCT rh.usuario_id) as total_usuarios,
                         COALESCE(SUM(rh.horas_trabajadas), 0) as horas_trabajadas
                  FROM " . $this->table_name . " op
                  LEFT JOIN registros_horas rh ON op.id = rh.orden_produccion_id AND rh.estado = 'registrado'
                  WHERE op.id = ?
                  GROUP BY op.id
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener orden por código
    public function obtenerOrdenPorCodigo($codigo_op) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE codigo_op = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $codigo_op);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar orden de producción
    public function actualizarOrden($id, $datos) {
        $query = "UPDATE " . $this->table_name . " 
                  SET codigo_op = :codigo_op,
                      nombre_producto = :nombre_producto,
                      descripcion = :descripcion,
                      cliente = :cliente,
                      cantidad_objetivo = :cantidad_objetivo,
                      fecha_inicio = :fecha_inicio,
                      fecha_fin_estimada = :fecha_fin_estimada,
                      estado = :estado,
                      prioridad = :prioridad";
        
        if (isset($datos['fecha_fin_real'])) {
            $query .= ", fecha_fin_real = :fecha_fin_real";
        }
        
        $query .= " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $orden_id = $id;
        $codigo_op = $datos['codigo_op'];
        $nombre_producto = $datos['nombre_producto'];
        $descripcion = $datos['descripcion'];
        $cliente = $datos['cliente'];
        $cantidad_objetivo = $datos['cantidad_objetivo'];
        $fecha_inicio = $datos['fecha_inicio'];
        $fecha_fin_estimada = $datos['fecha_fin_estimada'];
        $estado = $datos['estado'];
        $prioridad = $datos['prioridad'];

        $stmt->bindParam(':id', $orden_id);
        $stmt->bindParam(':codigo_op', $codigo_op);
        $stmt->bindParam(':nombre_producto', $nombre_producto);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':cliente', $cliente);
        $stmt->bindParam(':cantidad_objetivo', $cantidad_objetivo);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':fecha_fin_estimada', $fecha_fin_estimada);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':prioridad', $prioridad);
        
        if (isset($datos['fecha_fin_real'])) {
            $fecha_fin_real = $datos['fecha_fin_real'];
            $stmt->bindParam(':fecha_fin_real', $fecha_fin_real);
        }

        return $stmt->execute();
    }

    // Eliminar orden de producción
    public function eliminarOrden($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }

    // Cambiar estado de la orden
    public function cambiarEstado($id, $nuevo_estado, $fecha_fin_real = null) {
        $query = "UPDATE " . $this->table_name . " SET estado = :estado";
        
        if ($nuevo_estado === 'completada' && $fecha_fin_real) {
            $query .= ", fecha_fin_real = :fecha_fin_real";
        }
        
        $query .= " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':estado', $nuevo_estado);
        
        if ($nuevo_estado === 'completada' && $fecha_fin_real) {
            $stmt->bindParam(':fecha_fin_real', $fecha_fin_real);
        }

        return $stmt->execute();
    }

    // Verificar si el código de orden ya existe
    public function verificarCodigoExistente($codigo_op, $excluir_id = null) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE codigo_op = :codigo_op";
        
        if ($excluir_id) {
            $query .= " AND id != :excluir_id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':codigo_op', $codigo_op);
        
        if ($excluir_id) {
            $stmt->bindParam(':excluir_id', $excluir_id);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }

    // Obtener órdenes activas (para selección en formularios)
    public function obtenerOrdenesActivas() {
        $query = "SELECT id, codigo_op, nombre_producto, cliente, prioridad
                  FROM " . $this->table_name . "
                  WHERE estado IN ('activa', 'en_proceso')
                  ORDER BY 
                      CASE prioridad 
                          WHEN 'urgente' THEN 1 
                          WHEN 'alta' THEN 2 
                          WHEN 'media' THEN 3 
                          WHEN 'baja' THEN 4 
                      END,
                      fecha_inicio DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener estadísticas de una orden específica
    public function obtenerEstadisticasOrden($id) {
        $query = "SELECT 
                      op.*,
                      COUNT(DISTINCT rh.id) as total_registros,
                      COUNT(DISTINCT rh.usuario_id) as total_trabajadores,
                      COALESCE(SUM(rh.horas_trabajadas), 0) as total_horas,
                      COALESCE(AVG(rh.horas_trabajadas), 0) as promedio_horas_registro,
                      MIN(rh.fecha) as primera_fecha_trabajo,
                      MAX(rh.fecha) as ultima_fecha_trabajo,
                      DATEDIFF(CURRENT_DATE, op.fecha_inicio) as dias_transcurridos
                  FROM " . $this->table_name . " op
                  LEFT JOIN registros_horas rh ON op.id = rh.orden_produccion_id AND rh.estado = 'registrado'
                  WHERE op.id = :id
                  GROUP BY op.id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener resumen de órdenes por estado
    public function obtenerResumenPorEstado() {
        $query = "SELECT 
                      estado,
                      COUNT(*) as total,
                      SUM(cantidad_objetivo) as suma_cantidad_objetivo
                  FROM " . $this->table_name . "
                  GROUP BY estado
                  ORDER BY 
                      CASE estado 
                          WHEN 'activa' THEN 1 
                          WHEN 'en_proceso' THEN 2 
                          WHEN 'pausada' THEN 3 
                          WHEN 'completada' THEN 4 
                          WHEN 'cancelada' THEN 5 
                      END";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
