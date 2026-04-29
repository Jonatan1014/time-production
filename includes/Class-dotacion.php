<?php
// includes/Class-dotacion.php
require_once 'conn-db.php';

class Dotacion {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function obtenerItems($solo_activos = true) {
        $query = "SELECT * FROM dotacion_items";
        if ($solo_activos) {
            $query .= " WHERE is_active = 1";
        }
        $query .= " ORDER BY nombre ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerItemPorId($id) {
        $query = "SELECT * FROM dotacion_items WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function itemExiste($nombre, $exclude_id = null) {
        $query = "SELECT COUNT(*) as total FROM dotacion_items WHERE nombre = :nombre";
        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        if ($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id, PDO::PARAM_INT);
        }
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado['total'] > 0;
    }

    public function crearItem($datos) {
        try {
            $query = "INSERT INTO dotacion_items (nombre, descripcion, is_active) VALUES (:nombre, :descripcion, :is_active)";
            $stmt = $this->conn->prepare($query);

            $descripcion = $datos['descripcion'] ?? null;
            $is_active = $datos['is_active'] ?? 1;

            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':is_active', $is_active, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Item de dotacion creado exitosamente',
                    'id' => $this->conn->lastInsertId()
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al crear el item de dotacion'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    public function actualizarItem($id, $datos) {
        try {
            $query = "UPDATE dotacion_items SET nombre = :nombre, descripcion = :descripcion, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            $descripcion = $datos['descripcion'] ?? null;

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':descripcion', $descripcion);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Item de dotacion actualizado exitosamente'
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al actualizar el item de dotacion'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    public function cambiarEstadoItem($id, $estado) {
        try {
            $query = "UPDATE dotacion_items SET is_active = :estado, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Estado del item actualizado exitosamente'
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al actualizar el estado del item'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    public function obtenerUsuariosActivos() {
        $query = "SELECT id, nombre_completo, username, email FROM usuarios WHERE is_active = 1 ORDER BY nombre_completo ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerEntregas() {
        $query = "SELECT de.*, u.nombre_completo AS usuario_nombre, u.username AS usuario_username, "
               . "e.nombre_completo AS entregado_por_nombre, "
               . "GROUP_CONCAT(CONCAT(di.nombre, ' x', dei.cantidad) ORDER BY di.nombre SEPARATOR ', ') AS items "
               . "FROM dotacion_entregas de "
               . "INNER JOIN usuarios u ON de.usuario_id = u.id "
               . "INNER JOIN usuarios e ON de.entregado_por = e.id "
               . "LEFT JOIN dotacion_entrega_items dei ON de.id = dei.entrega_id "
               . "LEFT JOIN dotacion_items di ON dei.item_id = di.id "
               . "GROUP BY de.id "
               . "ORDER BY de.fecha_entrega DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPendientes($fecha_actual) {
        $query = "SELECT u.id, u.nombre_completo, u.username, u.email, d.nombre AS departamento, c.nombre AS cargo, "
               . "ue.fecha_entrega AS ultima_entrega, ue.proxima_entrega, "
               . "CASE WHEN ue.proxima_entrega IS NULL THEN NULL "
               . "ELSE DATEDIFF(:fecha_actual_diff, ue.proxima_entrega) END AS dias_retraso "
               . "FROM usuarios u "
               . "LEFT JOIN ( "
               . "  SELECT de1.* FROM dotacion_entregas de1 "
               . "  INNER JOIN ( "
               . "    SELECT usuario_id, MAX(fecha_entrega) AS max_fecha "
               . "    FROM dotacion_entregas GROUP BY usuario_id "
               . "  ) de2 ON de1.usuario_id = de2.usuario_id AND de1.fecha_entrega = de2.max_fecha "
               . ") ue ON u.id = ue.usuario_id "
               . "LEFT JOIN departamentos d ON u.departamento_id = d.id "
               . "LEFT JOIN cargos c ON u.cargo_id = c.id "
               . "WHERE u.is_active = 1 AND (ue.proxima_entrega IS NULL OR ue.proxima_entrega <= :fecha_actual_limite) "
               . "ORDER BY u.nombre_completo ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':fecha_actual_diff', $fecha_actual);
        $stmt->bindParam(':fecha_actual_limite', $fecha_actual);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrarEntrega($datos, $items, $intervalo_meses) {
        try {
            $intervalo = (int)$intervalo_meses;
            if ($intervalo <= 0) {
                $intervalo = 1;
            }

            $proxima_entrega = $this->calcularProximaEntrega($datos['fecha_entrega'], $intervalo);

            $this->conn->beginTransaction();

            $query = "INSERT INTO dotacion_entregas (usuario_id, entregado_por, fecha_entrega, proxima_entrega, observaciones) "
                   . "VALUES (:usuario_id, :entregado_por, :fecha_entrega, :proxima_entrega, :observaciones)";
            $stmt = $this->conn->prepare($query);

            $observaciones = $datos['observaciones'] ?? null;

            $stmt->bindParam(':usuario_id', $datos['usuario_id'], PDO::PARAM_INT);
            $stmt->bindParam(':entregado_por', $datos['entregado_por'], PDO::PARAM_INT);
            $stmt->bindParam(':fecha_entrega', $datos['fecha_entrega']);
            $stmt->bindParam(':proxima_entrega', $proxima_entrega);
            $stmt->bindParam(':observaciones', $observaciones);
            $stmt->execute();

            $entrega_id = $this->conn->lastInsertId();

            $query_item = "INSERT INTO dotacion_entrega_items (entrega_id, item_id, cantidad) VALUES (:entrega_id, :item_id, :cantidad)";
            $stmt_item = $this->conn->prepare($query_item);

            $tiene_items = false;
            foreach ($items as $item_id => $cantidad) {
                $cantidad = (int)$cantidad;
                if ($cantidad <= 0) {
                    continue;
                }

                $tiene_items = true;
                $stmt_item->bindParam(':entrega_id', $entrega_id, PDO::PARAM_INT);
                $stmt_item->bindParam(':item_id', $item_id, PDO::PARAM_INT);
                $stmt_item->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
                $stmt_item->execute();
            }

            if (!$tiene_items) {
                $this->conn->rollBack();
                return [
                    'success' => false,
                    'message' => 'Debe seleccionar al menos un item de dotacion'
                ];
            }

            $this->conn->commit();
            return [
                'success' => true,
                'message' => 'Entrega de dotacion registrada correctamente'
            ];
        } catch (PDOException $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    private function calcularProximaEntrega($fecha_entrega, $intervalo_meses) {
        $fecha = new DateTime($fecha_entrega);
        $fecha->modify('+' . $intervalo_meses . ' months');
        return $fecha->format('Y-m-d');
    }
}
?>
