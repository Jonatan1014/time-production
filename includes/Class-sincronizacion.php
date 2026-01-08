<?php
// includes/Class-sincronizacion.php
require_once 'conn-db.php';
require_once 'Class-configuracion.php';
require_once 'Class-costos.php';

class Sincronizacion {
    private $conn;
    private $config;
    private $costos;
    private $table_name = "sincronizacion_projectdashboard";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->config = new Configuracion();
        $this->costos = new Costos();
    }

    /**
     * Obtener registros de horas normales NO sincronizados
     * @param array $filtros Filtros opcionales (fecha_inicio, fecha_fin, usuario_id, orden_id)
     * @return array Array de registros pendientes de sincronización
     */
    public function obtenerHorasNormalesPendientes($filtros = []) {
        $query = "SELECT 
                    rh.id,
                    rh.usuario_id,
                    u.nombre_completo,
                    u.email,
                    u.valor_hora_base,
                    d.nombre as departamento,
                    c.nombre as cargo,
                    rh.orden_produccion_id,
                    op.codigo_op as proyecto_numero,
                    rh.fecha,
                    rh.horas_trabajadas,
                    rh.descripcion_trabajo,
                    rh.estado,
                    rh.created_at
                  FROM registros_horas rh
                  INNER JOIN usuarios u ON rh.usuario_id = u.id
                  LEFT JOIN departamentos d ON u.departamento_id = d.id
                  LEFT JOIN cargos c ON u.cargo_id = c.id
                  INNER JOIN ordenes_produccion op ON rh.orden_produccion_id = op.id
                  LEFT JOIN sincronizacion_projectdashboard s ON (s.tipo_registro = 'horas_normales' AND s.registro_id = rh.id)
                  WHERE s.id IS NULL
                  AND rh.estado IN ('registrado', 'validado')";

        $params = [];

        if (isset($filtros['fecha_inicio']) && isset($filtros['fecha_fin'])) {
            $query .= " AND rh.fecha BETWEEN :fecha_inicio AND :fecha_fin";
            $params[':fecha_inicio'] = $filtros['fecha_inicio'];
            $params[':fecha_fin'] = $filtros['fecha_fin'];
        }

        if (isset($filtros['usuario_id'])) {
            $query .= " AND rh.usuario_id = :usuario_id";
            $params[':usuario_id'] = $filtros['usuario_id'];
        }

        if (isset($filtros['orden_id'])) {
            $query .= " AND rh.orden_produccion_id = :orden_id";
            $params[':orden_id'] = $filtros['orden_id'];
        }

        $query .= " ORDER BY rh.fecha DESC, u.nombre_completo ASC";

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener horas extras APROBADAS NO sincronizadas
     * @param array $filtros Filtros opcionales
     * @return array Array de horas extras pendientes de sincronización
     */
    public function obtenerHorasExtrasPendientes($filtros = []) {
        $query = "SELECT 
                    he.id,
                    he.usuario_id,
                    u.nombre_completo,
                    u.email,
                    u.valor_hora_base,
                    d.nombre as departamento,
                    c.nombre as cargo,
                    he.orden_produccion_id,
                    op.codigo_op as proyecto_numero,
                    he.fecha,
                    he.total_horas_extras,
                    he.hora_inicio,
                    he.hora_fin,
                    he.descripcion_trabajo,
                    he.estado,
                    he.fecha_respuesta
                  FROM solicitudes_horas_extras he
                  INNER JOIN usuarios u ON he.usuario_id = u.id
                  LEFT JOIN departamentos d ON u.departamento_id = d.id
                  LEFT JOIN cargos c ON u.cargo_id = c.id
                  INNER JOIN ordenes_produccion op ON he.orden_produccion_id = op.id
                  LEFT JOIN sincronizacion_projectdashboard s ON (s.tipo_registro = 'horas_extras' AND s.registro_id = he.id)
                  WHERE s.id IS NULL
                  AND he.estado = 'aprobada'";

        $params = [];

        if (isset($filtros['fecha_inicio']) && isset($filtros['fecha_fin'])) {
            $query .= " AND he.fecha BETWEEN :fecha_inicio AND :fecha_fin";
            $params[':fecha_inicio'] = $filtros['fecha_inicio'];
            $params[':fecha_fin'] = $filtros['fecha_fin'];
        }

        if (isset($filtros['usuario_id'])) {
            $query .= " AND he.usuario_id = :usuario_id";
            $params[':usuario_id'] = $filtros['usuario_id'];
        }

        if (isset($filtros['orden_id'])) {
            $query .= " AND he.orden_produccion_id = :orden_id";
            $params[':orden_id'] = $filtros['orden_id'];
        }

        $query .= " ORDER BY he.fecha DESC, u.nombre_completo ASC";

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener todos los registros pendientes agrupados por usuario y fecha
     * @param array $filtros Filtros opcionales
     * @return array Array con registros combinados listos para enviar
     */
    public function obtenerRegistrosPendientesAgrupados($filtros = []) {
        $horas_normales = $this->obtenerHorasNormalesPendientes($filtros);
        $horas_extras = $this->obtenerHorasExtrasPendientes($filtros);

        // Crear un array asociativo para agrupar por usuario+fecha+proyecto
        $agrupados = [];

        // Procesar horas normales
        foreach ($horas_normales as $registro) {
            $key = $registro['usuario_id'] . '_' . $registro['fecha'] . '_' . $registro['orden_produccion_id'];
            
            if (!isset($agrupados[$key])) {
                $agrupados[$key] = [
                    'tipo' => 'horas_normales',
                    'registro_id' => $registro['id'],
                    'usuario_id' => $registro['usuario_id'],
                    'nombre_empleado' => $registro['nombre_completo'],
                    'cargo' => $registro['cargo'] ?? 'Sin cargo',
                    'area_trabajo' => $registro['departamento'] ?? 'Sin departamento',
                    'proyecto_numero' => $registro['proyecto_numero'],
                    'orden_produccion_id' => $registro['orden_produccion_id'],
                    'fecha' => $registro['fecha'],
                    'tiempo_ordinario' => 0,
                    'tiempo_extra' => 0,
                    'total_pagado' => 0,
                    'detalles_normales' => [],
                    'detalles_extras' => []
                ];
            }

            $agrupados[$key]['tiempo_ordinario'] += floatval($registro['horas_trabajadas']);
            $agrupados[$key]['detalles_normales'][] = $registro;
        }

        // Procesar horas extras
        foreach ($horas_extras as $registro) {
            $key = $registro['usuario_id'] . '_' . $registro['fecha'] . '_' . $registro['orden_produccion_id'];
            
            if (!isset($agrupados[$key])) {
                $agrupados[$key] = [
                    'tipo' => 'horas_extras',
                    'registro_id' => $registro['id'],
                    'usuario_id' => $registro['usuario_id'],
                    'nombre_empleado' => $registro['nombre_completo'],
                    'cargo' => $registro['cargo'] ?? 'Sin cargo',
                    'area_trabajo' => $registro['departamento'] ?? 'Sin departamento',
                    'proyecto_numero' => $registro['proyecto_numero'],
                    'orden_produccion_id' => $registro['orden_produccion_id'],
                    'fecha' => $registro['fecha'],
                    'tiempo_ordinario' => 0,
                    'tiempo_extra' => 0,
                    'total_pagado' => 0,
                    'detalles_normales' => [],
                    'detalles_extras' => []
                ];
            }

            $agrupados[$key]['tiempo_extra'] += floatval($registro['total_horas_extras']);
            $agrupados[$key]['detalles_extras'][] = $registro;
        }

        // Calcular total pagado para cada registro agrupado
        // USANDO LEGISLACIÓN LABORAL COLOMBIANA
        foreach ($agrupados as &$registro) {
            $total_pagado = 0;
            
            // Calcular costo de horas normales con detección automática de festivos/dominicales
            foreach ($registro['detalles_normales'] as $normal) {
                $costo_normal = $this->costos->calcularCostoHorasNormales(
                    $normal['usuario_id'],
                    floatval($normal['horas_trabajadas']),
                    $normal['fecha']  // Se pasa la fecha para detectar festivos/dominicales
                );
                $total_pagado += $costo_normal['costo_total'];
            }

            // Sumar costos de horas extras con detección de horario diurno/nocturno y festivos
            foreach ($registro['detalles_extras'] as $extra) {
                $costo_extra = $this->costos->calcularCostoHorasExtras(
                    $extra['usuario_id'],
                    $extra['total_horas_extras'],
                    $extra['hora_inicio'],
                    $extra['hora_fin'],
                    $extra['fecha']  // Se pasa la fecha para detectar festivos/dominicales
                );
                $total_pagado += $costo_extra['costo_total'];
            }

            $registro['total_pagado'] = $total_pagado;
        }

        return array_values($agrupados);
    }

    /**
     * Marcar registros como sincronizados
     * @param array $registros Array de registros con tipo_registro, registro_id, etc.
     * @param int $sincronizado_por ID del usuario que realizó la sincronización
     * @param string $respuesta_api Respuesta opcional del sistema externo
     * @return array Resultado de la operación
     */
    public function marcarComoSincronizado($registros, $sincronizado_por, $respuesta_api = null) {
        try {
            $this->conn->beginTransaction();
            
            $insertados = 0;
            $errores = [];

            $query = "INSERT INTO " . $this->table_name . " 
                     (tipo_registro, registro_id, usuario_id, orden_produccion_id, fecha_registro, 
                      horas_ordinarias, horas_extras, total_pagado, sincronizado_por, respuesta_api)
                     VALUES 
                     (:tipo_registro, :registro_id, :usuario_id, :orden_produccion_id, :fecha_registro,
                      :horas_ordinarias, :horas_extras, :total_pagado, :sincronizado_por, :respuesta_api)";

            $stmt = $this->conn->prepare($query);

            foreach ($registros as $registro) {
                // Insertar registro de horas normales
                if (!empty($registro['detalles_normales'])) {
                    foreach ($registro['detalles_normales'] as $detalle) {
                        try {
                            // Calcular costo de horas normales con legislación colombiana
                            // (detecta automáticamente festivos, dominicales, horario nocturno)
                            $costo_normal = $this->costos->calcularCostoHorasNormales(
                                $detalle['usuario_id'],
                                floatval($detalle['horas_trabajadas']),
                                $detalle['fecha']
                            );
                            
                            $stmt->execute([
                                ':tipo_registro' => 'horas_normales',
                                ':registro_id' => $detalle['id'],
                                ':usuario_id' => $registro['usuario_id'],
                                ':orden_produccion_id' => $registro['orden_produccion_id'],
                                ':fecha_registro' => $registro['fecha'],
                                ':horas_ordinarias' => $detalle['horas_trabajadas'],
                                ':horas_extras' => 0,
                                ':total_pagado' => $costo_normal['costo_total'],
                                ':sincronizado_por' => $sincronizado_por,
                                ':respuesta_api' => $respuesta_api
                            ]);
                            $insertados++;
                        } catch (PDOException $e) {
                            // Si es error de duplicado, lo ignoramos
                            if ($e->getCode() != 23000) {
                                $errores[] = "Error en registro normal ID {$detalle['id']}: " . $e->getMessage();
                            }
                        }
                    }
                }

                // Insertar registro de horas extras
                if (!empty($registro['detalles_extras'])) {
                    foreach ($registro['detalles_extras'] as $detalle) {
                        try {
                            // Calcular costo de horas extras con legislación colombiana
                            // (detecta automáticamente festivos, dominicales, horario diurno/nocturno)
                            $costo_extra = $this->costos->calcularCostoHorasExtras(
                                $detalle['usuario_id'],
                                $detalle['total_horas_extras'],
                                $detalle['hora_inicio'],
                                $detalle['hora_fin'],
                                $detalle['fecha']
                            );
                            
                            $stmt->execute([
                                ':tipo_registro' => 'horas_extras',
                                ':registro_id' => $detalle['id'],
                                ':usuario_id' => $registro['usuario_id'],
                                ':orden_produccion_id' => $registro['orden_produccion_id'],
                                ':fecha_registro' => $registro['fecha'],
                                ':horas_ordinarias' => 0,
                                ':horas_extras' => $detalle['total_horas_extras'],
                                ':total_pagado' => $costo_extra['costo_total'],
                                ':sincronizado_por' => $sincronizado_por,
                                ':respuesta_api' => $respuesta_api
                            ]);
                            $insertados++;
                        } catch (PDOException $e) {
                            // Si es error de duplicado, lo ignoramos
                            if ($e->getCode() != 23000) {
                                $errores[] = "Error en registro extra ID {$detalle['id']}: " . $e->getMessage();
                            }
                        }
                    }
                }
            }

            $this->conn->commit();

            return [
                'success' => true,
                'insertados' => $insertados,
                'errores' => $errores
            ];

        } catch (Exception $e) {
            $this->conn->rollBack();
            return [
                'success' => false,
                'message' => 'Error al marcar registros como sincronizados: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener historial de sincronizaciones
     * @param array $filtros Filtros opcionales
     * @param int $limit Límite de registros
     * @return array Historial de sincronizaciones
     */
    public function obtenerHistorialSincronizacion($filtros = [], $limit = 100) {
        $query = "SELECT 
                    s.*,
                    u.nombre_completo as usuario_nombre,
                    op.codigo_op as proyecto_numero,
                    us.nombre_completo as sincronizado_por_nombre
                  FROM " . $this->table_name . " s
                  INNER JOIN usuarios u ON s.usuario_id = u.id
                  INNER JOIN ordenes_produccion op ON s.orden_produccion_id = op.id
                  INNER JOIN usuarios us ON s.sincronizado_por = us.id
                  WHERE 1=1";

        $params = [];

        if (isset($filtros['fecha_inicio']) && isset($filtros['fecha_fin'])) {
            $query .= " AND s.fecha_registro BETWEEN :fecha_inicio AND :fecha_fin";
            $params[':fecha_inicio'] = $filtros['fecha_inicio'];
            $params[':fecha_fin'] = $filtros['fecha_fin'];
        }

        if (isset($filtros['usuario_id'])) {
            $query .= " AND s.usuario_id = :usuario_id";
            $params[':usuario_id'] = $filtros['usuario_id'];
        }

        $query .= " ORDER BY s.fecha_sincronizacion DESC LIMIT " . intval($limit);

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener estadísticas de sincronización
     * @return array Estadísticas generales
     */
    public function obtenerEstadisticas() {
        // Total de registros sincronizados
        $query_total = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->query($query_total);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Registros pendientes de horas normales
        $query_pendientes_normales = "SELECT COUNT(*) as pendientes 
                                      FROM registros_horas rh
                                      LEFT JOIN sincronizacion_projectdashboard s ON (s.tipo_registro = 'horas_normales' AND s.registro_id = rh.id)
                                      WHERE s.id IS NULL 
                                      AND rh.estado IN ('registrado', 'validado')";
        $stmt = $this->conn->query($query_pendientes_normales);
        $pendientes_normales = $stmt->fetch(PDO::FETCH_ASSOC)['pendientes'];

        // Registros pendientes de horas extras
        $query_pendientes_extras = "SELECT COUNT(*) as pendientes 
                                    FROM solicitudes_horas_extras he
                                    LEFT JOIN sincronizacion_projectdashboard s ON (s.tipo_registro = 'horas_extras' AND s.registro_id = he.id)
                                    WHERE s.id IS NULL 
                                    AND he.estado = 'aprobada'";
        $stmt = $this->conn->query($query_pendientes_extras);
        $pendientes_extras = $stmt->fetch(PDO::FETCH_ASSOC)['pendientes'];

        // Última sincronización
        $query_ultima = "SELECT MAX(fecha_sincronizacion) as ultima FROM " . $this->table_name;
        $stmt = $this->conn->query($query_ultima);
        $ultima = $stmt->fetch(PDO::FETCH_ASSOC)['ultima'];

        // Registros pendientes de horas de producción
        $query_pendientes_produccion = "SELECT COUNT(*) as pendientes 
                                        FROM registros_horas_produccion rhp
                                        LEFT JOIN sincronizacion_projectdashboard s ON (s.tipo_registro = 'horas_produccion' AND s.registro_id = rhp.id)
                                        WHERE s.id IS NULL";
        $stmt = $this->conn->query($query_pendientes_produccion);
        $pendientes_produccion = $stmt->fetch(PDO::FETCH_ASSOC)['pendientes'];

        return [
            'total_sincronizados' => $total,
            'pendientes_normales' => $pendientes_normales,
            'pendientes_extras' => $pendientes_extras,
            'pendientes_produccion' => $pendientes_produccion,
            'pendientes_total' => $pendientes_normales + $pendientes_extras + $pendientes_produccion,
            'ultima_sincronizacion' => $ultima
        ];
    }

    /**
     * Obtener horas de producción NO sincronizadas
     * @param array $filtros Filtros opcionales
     * @return array Array de registros de producción pendientes de sincronización
     */
    public function obtenerHorasProduccionPendientes($filtros = []) {
        $query = "SELECT 
                    rhp.id,
                    rhp.usuario_id,
                    u.nombre_completo,
                    u.email,
                    u.valor_hora_base,
                    d.nombre as departamento,
                    c.nombre as cargo,
                    rhp.orden_produccion_id,
                    op.codigo_op as proyecto_numero,
                    rhp.fecha,
                    rhp.descripcion,
                    rhp.maquina,
                    rhp.hr,
                    rhp.hed,
                    rhp.hen,
                    rhp.hefd,
                    rhp.hefn,
                    rhp.permiso,
                    rhp.comida,
                    rhp.total_horas,
                    rhp.observaciones,
                    rhp.horario,
                    rhp.created_at
                  FROM registros_horas_produccion rhp
                  INNER JOIN usuarios u ON rhp.usuario_id = u.id
                  LEFT JOIN departamentos d ON u.departamento_id = d.id
                  LEFT JOIN cargos c ON u.cargo_id = c.id
                  INNER JOIN ordenes_produccion op ON rhp.orden_produccion_id = op.id
                  LEFT JOIN sincronizacion_projectdashboard s ON (s.tipo_registro = 'horas_produccion' AND s.registro_id = rhp.id)
                  WHERE s.id IS NULL";

        $params = [];

        if (isset($filtros['fecha_inicio']) && isset($filtros['fecha_fin'])) {
            $query .= " AND rhp.fecha BETWEEN :fecha_inicio AND :fecha_fin";
            $params[':fecha_inicio'] = $filtros['fecha_inicio'];
            $params[':fecha_fin'] = $filtros['fecha_fin'];
        }

        if (isset($filtros['usuario_id'])) {
            $query .= " AND rhp.usuario_id = :usuario_id";
            $params[':usuario_id'] = $filtros['usuario_id'];
        }

        if (isset($filtros['orden_id'])) {
            $query .= " AND rhp.orden_produccion_id = :orden_id";
            $params[':orden_id'] = $filtros['orden_id'];
        }

        $query .= " ORDER BY rhp.fecha DESC, u.nombre_completo ASC";

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Marcar horas de producción como sincronizadas
     * @param array $registros Array de registros de producción
     * @param int $sincronizado_por ID del usuario que realizó la sincronización
     * @param string $respuesta_api Respuesta opcional del sistema externo
     * @return array Resultado de la operación
     */
    public function marcarProduccionComoSincronizada($registros, $sincronizado_por, $respuesta_api = null) {
        try {
            $this->conn->beginTransaction();
            
            $insertados = 0;
            $errores = [];

            $query = "INSERT INTO " . $this->table_name . " 
                     (tipo_registro, registro_id, usuario_id, orden_produccion_id, fecha_registro, 
                      horas_ordinarias, horas_extras, total_pagado, sincronizado_por, respuesta_api)
                     VALUES 
                     (:tipo_registro, :registro_id, :usuario_id, :orden_produccion_id, :fecha_registro,
                      :horas_ordinarias, :horas_extras, :total_pagado, :sincronizado_por, :respuesta_api)";

            $stmt = $this->conn->prepare($query);

            foreach ($registros as $registro) {
                try {
                    // Calcular horas ordinarias y extras
                    $hr = floatval($registro['hr'] ?? 0);
                    $hed = floatval($registro['hed'] ?? 0);
                    $hen = floatval($registro['hen'] ?? 0);
                    $hefd = floatval($registro['hefd'] ?? 0);
                    $hefn = floatval($registro['hefn'] ?? 0);
                    
                    $horas_ordinarias = $hr;
                    $horas_extras = $hed + $hen + $hefd + $hefn;
                    $costo_total = floatval($registro['costo_calculado'] ?? 0);
                    
                    $stmt->execute([
                        ':tipo_registro' => 'horas_produccion',
                        ':registro_id' => $registro['id'],
                        ':usuario_id' => $registro['usuario_id'],
                        ':orden_produccion_id' => $registro['orden_produccion_id'],
                        ':fecha_registro' => $registro['fecha'],
                        ':horas_ordinarias' => $horas_ordinarias,
                        ':horas_extras' => $horas_extras,
                        ':total_pagado' => $costo_total,
                        ':sincronizado_por' => $sincronizado_por,
                        ':respuesta_api' => $respuesta_api
                    ]);
                    $insertados++;
                } catch (PDOException $e) {
                    // Si es error de duplicado, lo ignoramos
                    if ($e->getCode() != 23000) {
                        $errores[] = "Error en registro producción ID {$registro['id']}: " . $e->getMessage();
                    }
                }
            }

            $this->conn->commit();

            return [
                'success' => true,
                'insertados' => $insertados,
                'errores' => $errores
            ];

        } catch (Exception $e) {
            $this->conn->rollBack();
            return [
                'success' => false,
                'message' => 'Error al marcar producción como sincronizada: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener historial de horas de producción sincronizadas
     * @param array $filtros Filtros opcionales
     * @param int $limite Número máximo de registros
     * @return array Array de registros sincronizados
     */
    public function obtenerHistorialProduccionSincronizada($filtros = [], $limite = 50) {
        $query = "SELECT 
                    s.id,
                    s.fecha_sincronizacion,
                    s.tipo_registro,
                    s.registro_id,
                    s.usuario_id,
                    u.nombre_completo as usuario_nombre,
                    s.orden_produccion_id,
                    op.codigo_op as proyecto_numero,
                    s.fecha_registro,
                    s.horas_ordinarias,
                    s.horas_extras,
                    s.total_pagado,
                    s.sincronizado_por,
                    admin.nombre_completo as sincronizado_por_nombre,
                    rhp.horario,
                    rhp.maquina,
                    rhp.observaciones,
                    c.nombre as cargo,
                    d.nombre as departamento
                  FROM " . $this->table_name . " s
                  INNER JOIN usuarios u ON s.usuario_id = u.id
                  INNER JOIN ordenes_produccion op ON s.orden_produccion_id = op.id
                  INNER JOIN usuarios admin ON s.sincronizado_por = admin.id
                  LEFT JOIN registros_horas_produccion rhp ON s.registro_id = rhp.id AND s.tipo_registro = 'horas_produccion'
                  LEFT JOIN cargos c ON u.cargo_id = c.id
                  LEFT JOIN departamentos d ON u.departamento_id = d.id
                  WHERE s.tipo_registro = 'horas_produccion'";

        $params = [];

        if (isset($filtros['fecha_inicio']) && isset($filtros['fecha_fin'])) {
            $query .= " AND s.fecha_registro BETWEEN :fecha_inicio AND :fecha_fin";
            $params[':fecha_inicio'] = $filtros['fecha_inicio'];
            $params[':fecha_fin'] = $filtros['fecha_fin'];
        }

        if (isset($filtros['usuario_id'])) {
            $query .= " AND s.usuario_id = :usuario_id";
            $params[':usuario_id'] = $filtros['usuario_id'];
        }

        $query .= " ORDER BY s.fecha_sincronizacion DESC LIMIT " . intval($limite);

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
