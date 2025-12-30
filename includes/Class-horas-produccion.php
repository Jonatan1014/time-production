<?php
/**
 * Class HorasProduccion
 * Maneja las operaciones CRUD para registros de horas de producción
 * Incluye validación, transacciones y métodos para gestión masiva
 */
class HorasProduccion {
    private $conn;
    private $table = 'registros_horas_produccion';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Crear un registro individual de horas de producción
     */
    public function crearRegistro($datos) {
        try {
            // Validar datos requeridos
            $this->validarDatosRegistro($datos);

            // Calcular total de horas si no se proporciona
            if (!isset($datos['total_horas']) || $datos['total_horas'] == 0) {
                $datos['total_horas'] = $this->calcularTotalHoras($datos);
            }

            $query = "INSERT INTO " . $this->table . "
                     (usuario_id, orden_produccion_id, fecha, descripcion, maquina, hr, hed, hen, hefd, hefn,
                      permiso, comida, total_horas, observaciones, horario)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($query);

            $stmt->execute([
                $datos['usuario_id'],
                $datos['orden_produccion_id'],
                $datos['fecha'],
                $datos['descripcion'],
                $datos['maquina'],
                $datos['hr'],
                $datos['hed'],
                $datos['hen'],
                $datos['hefd'],
                $datos['hefn'],
                $datos['permiso'],
                $datos['comida'],
                $datos['total_horas'],
                $datos['observaciones'],
                $datos['horario']
            ]);

            return [
                'success' => true,
                'id' => $this->conn->lastInsertId(),
                'mensaje' => 'Registro creado exitosamente'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'mensaje' => $e->getMessage()
            ];
        }
    }

    /**
     * Crear múltiples registros de horas de producción en una transacción
     */
    public function crearRegistrosMasivos($registros) {
        try {
            $this->conn->begin_transaction();

            $resultados = [];
            $errores = [];
            $ids_creados = [];

            foreach ($registros as $index => $registro) {
                $resultado = $this->crearRegistro($registro);

                if ($resultado['success']) {
                    $ids_creados[] = $resultado['id'];
                    $resultados[] = "Registro " . ($index + 1) . ": Creado (ID: " . $resultado['id'] . ")";
                } else {
                    $errores[] = "Registro " . ($index + 1) . ": " . $resultado['mensaje'];
                }
            }

            if (empty($errores)) {
                $this->conn->commit();
                return [
                    'success' => true,
                    'mensaje' => 'Todos los registros creados exitosamente',
                    'ids_creados' => $ids_creados,
                    'total' => count($registros)
                ];
            } else {
                $this->conn->rollback();
                return [
                    'success' => false,
                    'mensaje' => 'Errores en algunos registros: ' . implode('; ', $errores),
                    'errores' => $errores
                ];
            }

        } catch (Exception $e) {
            $this->conn->rollback();
            return [
                'success' => false,
                'mensaje' => 'Error en transacción: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener registros con filtros opcionales
     */
    public function obtenerRegistros($filtros = []) {
        try {
            $query = "SELECT rp.*, u.nombre_completo, op.codigo_op, op.nombre_producto
                     FROM " . $this->table . " rp
                     INNER JOIN usuarios u ON rp.usuario_id = u.id
                     INNER JOIN ordenes_produccion op ON rp.orden_produccion_id = op.id
                     WHERE 1=1";

            $params = [];
            $types = "";

            // Aplicar filtros
            if (isset($filtros['usuario_id'])) {
                $query .= " AND rp.usuario_id = ?";
                $params[] = $filtros['usuario_id'];
                $types .= "i";
            }

            if (isset($filtros['orden_produccion_id'])) {
                $query .= " AND rp.orden_produccion_id = ?";
                $params[] = $filtros['orden_produccion_id'];
                $types .= "i";
            }

            if (isset($filtros['fecha'])) {
                $query .= " AND rp.fecha = ?";
                $params[] = $filtros['fecha'];
                $types .= "s";
            }

            if (isset($filtros['fecha_desde'])) {
                $query .= " AND rp.fecha >= ?";
                $params[] = $filtros['fecha_desde'];
                $types .= "s";
            }

            if (isset($filtros['fecha_hasta'])) {
                $query .= " AND rp.fecha <= ?";
                $params[] = $filtros['fecha_hasta'];
                $types .= "s";
            }

            $query .= " ORDER BY rp.fecha DESC, rp.created_at DESC";

            if (isset($filtros['limit'])) {
                $query .= " LIMIT ?";
                $params[] = $filtros['limit'];
                $types .= "i";
            }

            $stmt = $this->conn->prepare($query);

            $stmt->execute($params);
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'success' => true,
                'registros' => $registros,
                'total' => count($registros)
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'mensaje' => 'Error al obtener registros: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar un registro de horas de producción
     */
    public function actualizarRegistro($id, $datos) {
        try {
            // Verificar que el registro existe
            if (!$this->registroExiste($id)) {
                throw new Exception("Registro no encontrado");
            }

            // Validar datos
            $this->validarDatosRegistro($datos, false);

            // Recalcular total si se modifican las horas
            if (isset($datos['hr']) || isset($datos['hed']) || isset($datos['hen']) ||
                isset($datos['hefd']) || isset($datos['hefn'])) {
                $registro_actual = $this->obtenerRegistroPorId($id);
                if ($registro_actual['success']) {
                    $datos_completos = array_merge($registro_actual['registro'], $datos);
                    $datos['total_horas'] = $this->calcularTotalHoras($datos_completos);
                }
            }

            $query = "UPDATE " . $this->table . " SET
                     usuario_id = ?, orden_produccion_id = ?, fecha = ?, descripcion = ?, maquina = ?,
                     hr = ?, hed = ?, hen = ?, hefd = ?, hefn = ?,
                     permiso = ?, comida = ?, total_horas = ?, observaciones = ?,
                     horario = ?, updated_at = CURRENT_TIMESTAMP
                     WHERE id = ?";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                $datos['usuario_id'],
                $datos['orden_produccion_id'],
                $datos['fecha'],
                $datos['descripcion'],
                $datos['maquina'],
                $datos['hr'],
                $datos['hed'],
                $datos['hen'],
                $datos['hefd'],
                $datos['hefn'],
                $datos['permiso'],
                $datos['comida'],
                $datos['total_horas'],
                $datos['observaciones'],
                $datos['horario'],
                $id
            ]);

            return [
                'success' => true,
                'mensaje' => 'Registro actualizado exitosamente'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'mensaje' => $e->getMessage()
            ];
        }
    }

    /**
     * Eliminar un registro de horas de producción
     */
    public function eliminarRegistro($id) {
        try {
            // Verificar que el registro existe
            if (!$this->registroExiste($id)) {
                throw new Exception("Registro no encontrado");
            }

            $query = "DELETE FROM " . $this->table . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);

            return [
                'success' => true,
                'mensaje' => 'Registro eliminado exitosamente'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'mensaje' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener estadísticas de horas de producción
     */
    public function obtenerEstadisticas($filtros = []) {
        try {
            $query = "SELECT
                     COUNT(*) as total_registros,
                     SUM(total_horas) as total_horas,
                     SUM(hr) as total_hr,
                     SUM(hed) as total_hed,
                     SUM(hen) as total_hen,
                     SUM(hefd) as total_hefd,
                     SUM(hefn) as total_hefn,
                     AVG(total_horas) as promedio_horas_diarias,
                     COUNT(DISTINCT usuario_id) as trabajadores_unicos,
                     COUNT(DISTINCT orden_produccion_id) as ordenes_unicas,
                     MIN(fecha) as fecha_inicio,
                     MAX(fecha) as fecha_fin
                     FROM " . $this->table . "
                     WHERE 1=1";

            $params = [];
            $types = "";

            // Aplicar filtros similares a obtenerRegistros
            if (isset($filtros['usuario_id'])) {
                $query .= " AND usuario_id = ?";
                $params[] = $filtros['usuario_id'];
                $types .= "i";
            }

            if (isset($filtros['orden_produccion_id'])) {
                $query .= " AND orden_produccion_id = ?";
                $params[] = $filtros['orden_produccion_id'];
                $types .= "i";
            }

            if (isset($filtros['fecha_desde'])) {
                $query .= " AND fecha >= ?";
                $params[] = $filtros['fecha_desde'];
                $types .= "s";
            }

            if (isset($filtros['fecha_hasta'])) {
                $query .= " AND fecha <= ?";
                $params[] = $filtros['fecha_hasta'];
                $types .= "s";
            }

            $stmt = $this->conn->prepare($query);

            $stmt->execute($params);
            $estadisticas = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                'success' => true,
                'estadisticas' => $estadisticas
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'mensaje' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validar datos de registro
     */
    private function validarDatosRegistro($datos, $requerir_id = true) {
        $campos_requeridos = ['usuario_id', 'orden_produccion_id', 'fecha'];

        foreach ($campos_requeridos as $campo) {
            if (!isset($datos[$campo]) || empty($datos[$campo])) {
                throw new Exception("Campo requerido faltante: " . $campo);
            }
        }

        // Validar tipos de horas
        $campos_horas = ['hr', 'hed', 'hen', 'hefd', 'hefn'];
        foreach ($campos_horas as $campo) {
            if (isset($datos[$campo])) {
                $valor = floatval($datos[$campo]);
                if ($valor < 0 || $valor > 24) {
                    throw new Exception("Valor inválido para " . $campo . ": debe estar entre 0 y 24");
                }
                $datos[$campo] = $valor;
            } else {
                $datos[$campo] = 0;
            }
        }

        // Validar fecha
        if (!strtotime($datos['fecha'])) {
            throw new Exception("Fecha inválida");
        }

        // Validar IDs
        if (!is_numeric($datos['usuario_id']) || $datos['usuario_id'] <= 0) {
            throw new Exception("ID de usuario inválido");
        }

        if (!is_numeric($datos['orden_produccion_id']) || $datos['orden_produccion_id'] <= 0) {
            throw new Exception("ID de orden de producción inválido");
        }

        return true;
    }

    /**
     * Calcular total de horas
     */
    private function calcularTotalHoras($datos) {
        return floatval($datos['hr'] ?? 0) +
               floatval($datos['hed'] ?? 0) +
               floatval($datos['hen'] ?? 0) +
               floatval($datos['hefd'] ?? 0) +
               floatval($datos['hefn'] ?? 0);
    }

    /**
     * Verificar si un registro existe
     */
    private function registroExiste($id) {
        $query = "SELECT id FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result !== false;
    }

    /**
     * Obtener un registro por ID
     */
    private function obtenerRegistroPorId($id) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return [
                    'success' => true,
                    'registro' => $result
                ];
            } else {
                return [
                    'success' => false,
                    'mensaje' => 'Registro no encontrado'
                ];
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'mensaje' => 'Error al obtener registro: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar resumen diario (método manual si es necesario)
     */
    public function actualizarResumenDiario($usuario_id, $fecha) {
        try {
            $query = "SELECT SUM(total_horas) as total_horas_produccion,
                             COUNT(*) as numero_registros_produccion
                      FROM " . $this->table . "
                      WHERE usuario_id = ? AND fecha = ?";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([$usuario_id, $fecha]);
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($datos['total_horas_produccion'] > 0) {
                $query_update = "INSERT INTO resumen_diario_horas
                               (usuario_id, fecha, horas_normales, numero_registros)
                               VALUES (?, ?, ?, ?)
                               ON DUPLICATE KEY UPDATE
                               horas_normales = VALUES(horas_normales),
                               numero_registros = VALUES(numero_registros),
                               total_horas = horas_normales + horas_extras";

                $stmt_update = $this->conn->prepare($query_update);
                $stmt_update->execute([
                    $usuario_id,
                    $fecha,
                    $datos['total_horas_produccion'],
                    $datos['numero_registros_produccion']
                ]);
            }

            return ['success' => true];

        } catch (Exception $e) {
            return [
                'success' => false,
                'mensaje' => 'Error al actualizar resumen: ' . $e->getMessage()
            ];
        }
    }
}
?>