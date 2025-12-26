<?php
// includes/Class-cargos.php
require_once 'conn-db.php';

class Cargos {
    private $conn;
    private $table_name = "cargos";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Obtener todos los cargos activos
     * @return array Array de cargos
     */
    public function obtenerCargosActivos() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE is_active = 1 ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener todos los cargos (activos e inactivos)
     * @return array Array de cargos
     */
    public function obtenerTodosCargos() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener cargo por ID
     * @param int $id ID del cargo
     * @return array|null Datos del cargo o null si no existe
     */
    public function obtenerCargoPorId($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crear nuevo cargo
     * @param array $datos Datos del cargo (nombre, descripcion)
     * @return array Resultado de la operación
     */
    public function crearCargo($datos) {
        try {
            $query = "INSERT INTO " . $this->table_name . " (nombre, descripcion) VALUES (:nombre, :descripcion)";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Cargo creado exitosamente',
                    'id' => $this->conn->lastInsertId()
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al crear el cargo'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar cargo
     * @param int $id ID del cargo
     * @param array $datos Datos a actualizar
     * @return array Resultado de la operación
     */
    public function actualizarCargo($id, $datos) {
        try {
            $query = "UPDATE " . $this->table_name . " SET nombre = :nombre, descripcion = :descripcion, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Cargo actualizado exitosamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al actualizar el cargo'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Activar/desactivar cargo
     * @param int $id ID del cargo
     * @param int $estado 1=activo, 0=inactivo
     * @return array Resultado de la operación
     */
    public function cambiarEstadoCargo($id, $estado) {
        try {
            $query = "UPDATE " . $this->table_name . " SET is_active = :estado, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':estado', $estado);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Estado del cargo actualizado exitosamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al actualizar el estado del cargo'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Eliminar cargo (solo si no está siendo usado)
     * @param int $id ID del cargo
     * @return array Resultado de la operación
     */
    public function eliminarCargo($id) {
        try {
            // Verificar si el cargo está siendo usado por usuarios
            $query_check = "SELECT COUNT(*) as total FROM usuarios WHERE cargo_id = :id";
            $stmt_check = $this->conn->prepare($query_check);
            $stmt_check->bindParam(':id', $id);
            $stmt_check->execute();
            $result = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if ($result['total'] > 0) {
                return [
                    'success' => false,
                    'message' => 'No se puede eliminar el cargo porque está siendo usado por ' . $result['total'] . ' usuario(s)'
                ];
            }

            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Cargo eliminado exitosamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al eliminar el cargo'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verificar si un cargo existe por nombre
     * @param string $nombre Nombre del cargo
     * @param int|null $exclude_id ID a excluir (para validación en actualización)
     * @return bool True si existe, false si no
     */
    public function cargoExiste($nombre, $exclude_id = null) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE nombre = :nombre";
        $params = [':nombre' => $nombre];

        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
            $params[':exclude_id'] = $exclude_id;
        }

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total'] > 0;
    }
}
