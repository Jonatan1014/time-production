<?php
/**
 * Clase Registro de Horas
 * Gestiona los eventos de registro de horas normales y extras
 * con validaciones completas
 */

require_once 'conn-db.php';

class RegistroHoras {
    private $conn;
    private $table_name = "registros_horas";
    private $table_extras = "solicitudes_horas_extras";
    private $table_resumen = "resumen_diario_horas";
    
    // Constantes de validación
    const HORAS_MIN_NORMALES = 0.5;
    const HORAS_MAX_NORMALES = 8.5;
    const HORAS_MIN_EXTRAS = 0.5;
    const HORAS_MAX_EXTRAS = 8.0;
    const HORAS_MAX_DIA = 16.0; // Total máximo por día (normales + extras)
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // =====================================================
    // REGISTROS DE HORAS NORMALES
    // =====================================================
    
    /**
     * Crear registro de horas normales
     * @param array $datos - Datos del registro
     * @return int|false - ID del registro creado o false si falla
     */
    public function crearRegistroHoras($datos) {
        // Validar datos requeridos
        $validacion = $this->validarDatosRegistro($datos);
        if ($validacion !== true) {
            throw new Exception($validacion);
        }
        
        // Verificar si ya existe un registro para ese usuario, orden y fecha
        if ($this->existeRegistro($datos['usuario_id'], $datos['orden_produccion_id'], $datos['fecha'])) {
            throw new Exception('Ya existe un registro para esta orden de producción en esta fecha. Por favor, edítalo en lugar de crear uno nuevo.');
        }
        
        // Validar horas totales del día
        $horasTotalesDia = $this->obtenerHorasTotalesDia($datos['usuario_id'], $datos['fecha']);
        $nuevasHorasTotales = $horasTotalesDia + $datos['horas_trabajadas'];
        
        if ($nuevasHorasTotales > self::HORAS_MAX_NORMALES) {
            throw new Exception(sprintf(
                'Las horas normales exceden el límite diario. Ya tienes %.2f horas registradas. Máximo permitido: %.2f horas.',
                $horasTotalesDia,
                self::HORAS_MAX_NORMALES
            ));
        }
        
        $query = "INSERT INTO " . $this->table_name . " 
                  (usuario_id, orden_produccion_id, fecha, horas_trabajadas, descripcion_trabajo, observaciones, estado) 
                  VALUES (:usuario_id, :orden_produccion_id, :fecha, :horas_trabajadas, :descripcion_trabajo, :observaciones, :estado)";
        
        $stmt = $this->conn->prepare($query);
        
        $usuario_id = $datos['usuario_id'];
        $orden_id = $datos['orden_produccion_id'];
        $fecha = $datos['fecha'];
        $horas = $datos['horas_trabajadas'];
        $descripcion = $datos['descripcion_trabajo'];
        $observaciones = $datos['observaciones'] ?? null;
        $estado = $datos['estado'] ?? 'registrado';
        
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':orden_produccion_id', $orden_id);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':horas_trabajadas', $horas);
        $stmt->bindParam(':descripcion_trabajo', $descripcion);
        $stmt->bindParam(':observaciones', $observaciones);
        $stmt->bindParam(':estado', $estado);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    /**
     * Actualizar registro de horas normales
     */
    public function actualizarRegistroHoras($id, $datos) {
        // Obtener registro actual
        $registroActual = $this->obtenerRegistroPorId($id);
        if (!$registroActual) {
            throw new Exception('Registro no encontrado.');
        }
        
        // Validar que no esté validado (solo se pueden editar registros en estado 'registrado')
        if ($registroActual['estado'] === 'validado') {
            throw new Exception('No se puede modificar un registro ya validado.');
        }
        
        // Validar datos
        if (isset($datos['horas_trabajadas'])) {
            if ($datos['horas_trabajadas'] < self::HORAS_MIN_NORMALES || $datos['horas_trabajadas'] > self::HORAS_MAX_NORMALES) {
                throw new Exception(sprintf(
                    'Las horas trabajadas deben estar entre %.1f y %.1f horas.',
                    self::HORAS_MIN_NORMALES,
                    self::HORAS_MAX_NORMALES
                ));
            }
            
            // Validar total del día (excluyendo este registro)
            $horasTotalesDia = $this->obtenerHorasTotalesDia($registroActual['usuario_id'], $registroActual['fecha'], $id);
            $nuevasHorasTotales = $horasTotalesDia + $datos['horas_trabajadas'];
            
            if ($nuevasHorasTotales > self::HORAS_MAX_NORMALES) {
                throw new Exception(sprintf(
                    'Las horas totales del día exceden el límite. Total actual (sin este registro): %.2f horas. Máximo: %.2f horas.',
                    $horasTotalesDia,
                    self::HORAS_MAX_NORMALES
                ));
            }
        }
        
        $query = "UPDATE " . $this->table_name . " 
                  SET horas_trabajadas = :horas_trabajadas,
                      descripcion_trabajo = :descripcion_trabajo,
                      observaciones = :observaciones
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $horas = $datos['horas_trabajadas'] ?? $registroActual['horas_trabajadas'];
        $descripcion = $datos['descripcion_trabajo'] ?? $registroActual['descripcion_trabajo'];
        $observaciones = $datos['observaciones'] ?? $registroActual['observaciones'];
        $registro_id = $id;
        
        $stmt->bindParam(':id', $registro_id);
        $stmt->bindParam(':horas_trabajadas', $horas);
        $stmt->bindParam(':descripcion_trabajo', $descripcion);
        $stmt->bindParam(':observaciones', $observaciones);
        
        return $stmt->execute();
    }
    
    /**
     * Eliminar registro de horas
     */
    public function eliminarRegistroHoras($id) {
        $registro = $this->obtenerRegistroPorId($id);
        if (!$registro) {
            throw new Exception('Registro no encontrado.');
        }
        
        if ($registro['estado'] === 'validado') {
            throw new Exception('No se puede eliminar un registro validado.');
        }
        
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $registro_id = $id;
        $stmt->bindParam(1, $registro_id);
        
        return $stmt->execute();
    }
    
    /**
     * Validar registro de horas (por administrador)
     */
    public function validarRegistroHoras($id, $validador_id, $estado, $comentario = null) {
        if (!in_array($estado, ['validado', 'rechazado'])) {
            throw new Exception('Estado de validación inválido.');
        }
        
        $query = "UPDATE " . $this->table_name . " 
                  SET estado = :estado,
                      validado_por = :validado_por,
                      fecha_validacion = NOW(),
                      comentario_validacion = :comentario
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $registro_id = $id;
        
        $stmt->bindParam(':id', $registro_id);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':validado_por', $validador_id);
        $stmt->bindParam(':comentario', $comentario);
        
        return $stmt->execute();
    }
    
    // =====================================================
    // SOLICITUDES DE HORAS EXTRAS
    // =====================================================
    
    /**
     * Crear solicitud de horas extras
     */
    public function crearSolicitudHorasExtras($datos) {
        // Validar datos requeridos
        $validacion = $this->validarDatosSolicitudExtras($datos);
        if ($validacion !== true) {
            throw new Exception($validacion);
        }
        
        // Calcular total de horas extras
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
        
        // Validar rango de horas
        if ($total_horas < self::HORAS_MIN_EXTRAS || $total_horas > self::HORAS_MAX_EXTRAS) {
            throw new Exception(sprintf(
                'Las horas extras deben estar entre %.1f y %.1f horas. Calculado: %.2f horas',
                self::HORAS_MIN_EXTRAS,
                self::HORAS_MAX_EXTRAS,
                $total_horas
            ));
        }
        
        // Verificar que existan horas normales registradas ese día
        $horasNormales = $this->obtenerHorasNormalesDia($datos['usuario_id'], $datos['fecha']);
        if ($horasNormales == 0) {
            throw new Exception('Debes registrar horas normales antes de solicitar horas extras para este día.');
        }
        
        // Validar límite total del día
        $horasExtrasDia = $this->obtenerHorasExtrasDia($datos['usuario_id'], $datos['fecha']);
        $totalDia = $horasNormales + $horasExtrasDia + $total_horas;
        
        if ($totalDia > self::HORAS_MAX_DIA) {
            throw new Exception(sprintf(
                'El total de horas excede el límite diario. Horas registradas: %.2f normal + %.2f extras. Máximo permitido: %.2f horas totales.',
                $horasNormales,
                $horasExtrasDia,
                self::HORAS_MAX_DIA
            ));
        }
        
        // Verificar si ya existe una solicitud pendiente o aprobada para ese día y orden
        if ($this->existeSolicitudExtra($datos['usuario_id'], $datos['orden_produccion_id'], $datos['fecha'])) {
            throw new Exception('Ya existe una solicitud de horas extras para esta orden en esta fecha.');
        }
        
        $query = "INSERT INTO " . $this->table_extras . " 
                  (usuario_id, orden_produccion_id, fecha, hora_inicio, hora_fin, total_horas_extras, descripcion_trabajo, estado) 
                  VALUES (:usuario_id, :orden_produccion_id, :fecha, :hora_inicio, :hora_fin, :total_horas_extras, :descripcion_trabajo, :estado)";
        
        $stmt = $this->conn->prepare($query);
        
        $usuario_id = $datos['usuario_id'];
        $orden_id = $datos['orden_produccion_id'];
        $fecha = $datos['fecha'];
        $hora_inicio = $datos['hora_inicio'];
        $hora_fin = $datos['hora_fin'];
        $descripcion = $datos['descripcion_trabajo'];
        $estado = 'pendiente';
        
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':orden_produccion_id', $orden_id);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':hora_inicio', $hora_inicio);
        $stmt->bindParam(':hora_fin', $hora_fin);
        $stmt->bindParam(':total_horas_extras', $total_horas);
        $stmt->bindParam(':descripcion_trabajo', $descripcion);
        $stmt->bindParam(':estado', $estado);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    /**
     * Aprobar o rechazar solicitud de horas extras
     */
    public function responderSolicitudExtras($solicitud_id, $aprobador_id, $estado, $comentario = null) {
        if (!in_array($estado, ['aprobada', 'rechazada'])) {
            throw new Exception('Estado de respuesta inválido.');
        }
        
        $solicitud = $this->obtenerSolicitudExtrasPorId($solicitud_id);
        if (!$solicitud) {
            throw new Exception('Solicitud no encontrada.');
        }
        
        if ($solicitud['estado'] !== 'pendiente') {
            throw new Exception('Solo se pueden aprobar/rechazar solicitudes pendientes.');
        }
        
        $query = "UPDATE " . $this->table_extras . " 
                  SET estado = :estado,
                      aprobado_por = :aprobado_por,
                      fecha_respuesta = NOW(),
                      comentario_aprobacion = :comentario
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $id = $solicitud_id;
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':aprobado_por', $aprobador_id);
        $stmt->bindParam(':comentario', $comentario);
        
        return $stmt->execute();
    }
    
    /**
     * Cancelar solicitud de horas extras (por el usuario)
     */
    public function cancelarSolicitudExtras($solicitud_id, $usuario_id) {
        $solicitud = $this->obtenerSolicitudExtrasPorId($solicitud_id);
        
        if (!$solicitud) {
            throw new Exception('Solicitud no encontrada.');
        }
        
        if ($solicitud['usuario_id'] != $usuario_id) {
            throw new Exception('No tienes permiso para cancelar esta solicitud.');
        }
        
        if ($solicitud['estado'] !== 'pendiente') {
            throw new Exception('Solo se pueden cancelar solicitudes pendientes.');
        }
        
        $query = "UPDATE " . $this->table_extras . " SET estado = 'cancelada' WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $id = $solicitud_id;
        $stmt->bindParam(1, $id);
        
        return $stmt->execute();
    }
    
    // =====================================================
    // CONSULTAS Y VALIDACIONES
    // =====================================================
    
    /**
     * Verificar si existe un registro
     */
    private function existeRegistro($usuario_id, $orden_id, $fecha) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                  WHERE usuario_id = ? AND orden_produccion_id = ? AND fecha = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$usuario_id, $orden_id, $fecha]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $resultado['total'] > 0;
    }
    
    /**
     * Verificar si existe solicitud de horas extras
     */
    private function existeSolicitudExtra($usuario_id, $orden_id, $fecha) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_extras . " 
                  WHERE usuario_id = ? AND orden_produccion_id = ? AND fecha = ? 
                  AND estado IN ('pendiente', 'aprobada')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$usuario_id, $orden_id, $fecha]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $resultado['total'] > 0;
    }
    
    /**
     * Obtener total de horas normales de un día (excluyendo un registro específico)
     */
    private function obtenerHorasTotalesDia($usuario_id, $fecha, $excluir_id = null) {
        $query = "SELECT COALESCE(SUM(horas_trabajadas), 0) as total 
                  FROM " . $this->table_name . " 
                  WHERE usuario_id = ? AND fecha = ?";
        
        $params = [$usuario_id, $fecha];
        
        if ($excluir_id !== null) {
            $query .= " AND id != ?";
            $params[] = $excluir_id;
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return floatval($resultado['total']);
    }
    
    /**
     * Obtener horas normales registradas en un día
     */
    private function obtenerHorasNormalesDia($usuario_id, $fecha) {
        return $this->obtenerHorasTotalesDia($usuario_id, $fecha);
    }
    
    /**
     * Obtener horas extras aprobadas o pendientes en un día
     */
    private function obtenerHorasExtrasDia($usuario_id, $fecha) {
        $query = "SELECT COALESCE(SUM(total_horas_extras), 0) as total 
                  FROM " . $this->table_extras . " 
                  WHERE usuario_id = ? AND fecha = ? AND estado IN ('pendiente', 'aprobada')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$usuario_id, $fecha]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return floatval($resultado['total']);
    }
    
    /**
     * Obtener registro por ID
     */
    public function obtenerRegistroPorId($id) {
        $query = "SELECT r.*, 
                         u.nombre_completo as usuario_nombre,
                         op.codigo_op, op.nombre_producto,
                         v.nombre_completo as validador_nombre
                  FROM " . $this->table_name . " r
                  INNER JOIN usuarios u ON r.usuario_id = u.id
                  INNER JOIN ordenes_produccion op ON r.orden_produccion_id = op.id
                  LEFT JOIN usuarios v ON r.validado_por = v.id
                  WHERE r.id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener solicitud de horas extras por ID
     */
    public function obtenerSolicitudExtrasPorId($id) {
        $query = "SELECT he.*, 
                         u.nombre_completo as usuario_nombre,
                         op.codigo_op, op.nombre_producto,
                         a.nombre_completo as aprobador_nombre
                  FROM " . $this->table_extras . " he
                  INNER JOIN usuarios u ON he.usuario_id = u.id
                  INNER JOIN ordenes_produccion op ON he.orden_produccion_id = op.id
                  LEFT JOIN usuarios a ON he.aprobado_por = a.id
                  WHERE he.id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener registros de un usuario
     */
    public function obtenerRegistrosUsuario($usuario_id, $fecha_inicio = null, $fecha_fin = null) {
        $query = "SELECT r.*, 
                         op.codigo_op, op.nombre_producto,
                         v.nombre_completo as validador_nombre
                  FROM " . $this->table_name . " r
                  INNER JOIN ordenes_produccion op ON r.orden_produccion_id = op.id
                  LEFT JOIN usuarios v ON r.validado_por = v.id
                  WHERE r.usuario_id = ?";
        
        $params = [$usuario_id];
        
        if ($fecha_inicio && $fecha_fin) {
            $query .= " AND r.fecha BETWEEN ? AND ?";
            $params[] = $fecha_inicio;
            $params[] = $fecha_fin;
        }
        
        $query .= " ORDER BY r.fecha DESC, r.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener solicitudes de horas extras de un usuario
     */
    public function obtenerSolicitudesExtrasUsuario($usuario_id, $estado = null) {
        $query = "SELECT he.*, 
                         op.codigo_op, op.nombre_producto,
                         a.nombre_completo as aprobador_nombre
                  FROM " . $this->table_extras . " he
                  INNER JOIN ordenes_produccion op ON he.orden_produccion_id = op.id
                  LEFT JOIN usuarios a ON he.aprobado_por = a.id
                  WHERE he.usuario_id = ?";
        
        $params = [$usuario_id];
        
        if ($estado) {
            $query .= " AND he.estado = ?";
            $params[] = $estado;
        }
        
        $query .= " ORDER BY he.fecha DESC, he.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener todas las solicitudes de horas extras pendientes (para administrador)
     */
    public function obtenerSolicitudesExtrasPendientes() {
        $query = "SELECT he.*, 
                         u.nombre_completo as usuario_nombre, u.departamento_id,
                         d.nombre as departamento, d.codigo as departamento_codigo,
                         op.codigo_op, op.nombre_producto,
                         DATEDIFF(NOW(), he.fecha_solicitud) as dias_pendiente
                  FROM " . $this->table_extras . " he
                  INNER JOIN usuarios u ON he.usuario_id = u.id
                  LEFT JOIN departamentos d ON u.departamento_id = d.id
                  INNER JOIN ordenes_produccion op ON he.orden_produccion_id = op.id
                  WHERE he.estado = 'pendiente'
                  ORDER BY he.prioridad DESC, he.fecha_solicitud ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener registros pendientes de validación
     */
    public function obtenerRegistrosPendientesValidacion() {
        $query = "SELECT r.*, 
                         u.nombre_completo as usuario_nombre, u.departamento_id,
                         d.nombre as departamento, d.codigo as departamento_codigo,
                         op.codigo_op, op.nombre_producto
                  FROM " . $this->table_name . " r
                  INNER JOIN usuarios u ON r.usuario_id = u.id
                  LEFT JOIN departamentos d ON u.departamento_id = d.id
                  INNER JOIN ordenes_produccion op ON r.orden_produccion_id = op.id
                  WHERE r.estado = 'registrado'
                  ORDER BY r.fecha DESC, r.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener resumen diario de un usuario
     */
    public function obtenerResumenDiario($usuario_id, $fecha) {
        $query = "SELECT 
                    COALESCE(SUM(r.horas_trabajadas), 0) as horas_normales,
                    COALESCE(
                        (SELECT SUM(he.total_horas_extras) 
                         FROM " . $this->table_extras . " he 
                         WHERE he.usuario_id = ? AND he.fecha = ? AND he.estado = 'aprobada'), 
                        0
                    ) as horas_extras_aprobadas,
                    COALESCE(
                        (SELECT SUM(he.total_horas_extras) 
                         FROM " . $this->table_extras . " he 
                         WHERE he.usuario_id = ? AND he.fecha = ? AND he.estado = 'pendiente'), 
                        0
                    ) as horas_extras_pendientes,
                    COUNT(r.id) as numero_registros
                  FROM " . $this->table_name . " r
                  WHERE r.usuario_id = ? AND r.fecha = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$usuario_id, $fecha, $usuario_id, $fecha, $usuario_id, $fecha]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $resultado['total_horas'] = $resultado['horas_normales'] + $resultado['horas_extras_aprobadas'];
        $resultado['puede_solicitar_extras'] = $resultado['horas_normales'] > 0;
        $resultado['horas_disponibles_extras'] = max(0, self::HORAS_MAX_DIA - $resultado['total_horas'] - $resultado['horas_extras_pendientes']);
        
        return $resultado;
    }
    
    // =====================================================
    // VALIDACIONES
    // =====================================================
    
    /**
     * Validar datos de registro de horas normales
     */
    private function validarDatosRegistro($datos) {
        if (empty($datos['usuario_id'])) {
            return 'El ID de usuario es requerido.';
        }
        
        if (empty($datos['orden_produccion_id'])) {
            return 'La orden de producción es requerida.';
        }
        
        if (empty($datos['fecha'])) {
            return 'La fecha es requerida.';
        }
        
        // Validar que la fecha no sea futura
        if (strtotime($datos['fecha']) > strtotime(date('Y-m-d'))) {
            return 'No se pueden registrar horas en fechas futuras.';
        }
        
        // Validar que la fecha no sea muy antigua (máximo 30 días atrás)
        $fecha_limite = date('Y-m-d', strtotime('-30 days'));
        if (strtotime($datos['fecha']) < strtotime($fecha_limite)) {
            return 'No se pueden registrar horas con más de 30 días de antigüedad.';
        }
        
        if (empty($datos['horas_trabajadas']) || !is_numeric($datos['horas_trabajadas'])) {
            return 'Las horas trabajadas son requeridas y deben ser un número válido.';
        }
        
        if ($datos['horas_trabajadas'] < self::HORAS_MIN_NORMALES || $datos['horas_trabajadas'] > self::HORAS_MAX_NORMALES) {
            return sprintf(
                'Las horas trabajadas deben estar entre %.1f y %.1f horas.',
                self::HORAS_MIN_NORMALES,
                self::HORAS_MAX_NORMALES
            );
        }
        
        if (empty($datos['descripcion_trabajo'])) {
            return 'La descripción del trabajo es requerida.';
        }
        
        if (strlen($datos['descripcion_trabajo']) < 10) {
            return 'La descripción del trabajo debe tener al menos 10 caracteres.';
        }
        
        return true;
    }
    
    /**
     * Validar datos de solicitud de horas extras
     */
    private function validarDatosSolicitudExtras($datos) {
        if (empty($datos['usuario_id'])) {
            return 'El ID de usuario es requerido.';
        }
        
        if (empty($datos['orden_produccion_id'])) {
            return 'La orden de producción es requerida.';
        }
        
        if (empty($datos['fecha'])) {
            return 'La fecha es requerida.';
        }
        
        // Validar que la fecha no sea futura
        if (strtotime($datos['fecha']) > strtotime(date('Y-m-d'))) {
            return 'No se pueden solicitar horas extras en fechas futuras.';
        }
        
        if (empty($datos['hora_inicio'])) {
            return 'La hora de inicio es requerida.';
        }
        
        if (empty($datos['hora_fin'])) {
            return 'La hora de finalización es requerida.';
        }
        
        // Validar formato de horas
        if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $datos['hora_inicio'])) {
            return 'El formato de hora de inicio es inválido. Use HH:MM';
        }
        
        if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $datos['hora_fin'])) {
            return 'El formato de hora de fin es inválido. Use HH:MM';
        }
        
        // Descripción del trabajo es opcional para horas extras
        // Solo validar si se proporciona
        if (!empty($datos['descripcion_trabajo']) && strlen($datos['descripcion_trabajo']) < 10) {
            return 'Si proporciona una descripción, debe tener al menos 10 caracteres.';
        }
        
        return true;
    }
    
    /**
     * Verificar si un usuario puede registrar horas en una fecha
     */
    public function puedeRegistrarHoras($usuario_id, $fecha) {
        $resumen = $this->obtenerResumenDiario($usuario_id, $fecha);
        
        return [
            'puede_registrar' => $resumen['horas_normales'] < self::HORAS_MAX_NORMALES,
            'horas_disponibles' => max(0, self::HORAS_MAX_NORMALES - $resumen['horas_normales']),
            'horas_registradas' => $resumen['horas_normales'],
            'mensaje' => $resumen['horas_normales'] >= self::HORAS_MAX_NORMALES 
                ? 'Has alcanzado el límite de horas normales para este día.' 
                : sprintf('Puedes registrar hasta %.2f horas más.', max(0, self::HORAS_MAX_NORMALES - $resumen['horas_normales']))
        ];
    }
    
    /**
     * Verificar si un usuario puede solicitar horas extras en una fecha
     */
    public function puedeSolicitarExtras($usuario_id, $fecha) {
        $resumen = $this->obtenerResumenDiario($usuario_id, $fecha);
        
        if (!$resumen['puede_solicitar_extras']) {
            return [
                'puede_solicitar' => false,
                'mensaje' => 'Debes registrar horas normales antes de solicitar horas extras.'
            ];
        }
        
        if ($resumen['horas_disponibles_extras'] <= 0) {
            return [
                'puede_solicitar' => false,
                'mensaje' => 'Has alcanzado el límite máximo de horas para este día (incluyendo solicitudes pendientes).'
            ];
        }
        
        return [
            'puede_solicitar' => true,
            'horas_disponibles' => $resumen['horas_disponibles_extras'],
            'mensaje' => sprintf('Puedes solicitar hasta %.2f horas extras.', $resumen['horas_disponibles_extras'])
        ];
    }
}
?>
