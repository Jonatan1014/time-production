<?php
// includes/Class-costos.php
require_once 'conn-db.php';
require_once 'Class-configuracion.php';
require_once 'Class-festivos.php';

class Costos {
    private $conn;
    private $config;
    private $festivos;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->config = new Configuracion();
        $this->festivos = new Festivos();
    }

    /**
     * Determina si una fecha es fin de semana (sábado o domingo)
     */
    public function esFinDeSemana($fecha) {
        $dia_semana = date('N', strtotime($fecha)); // 1=Lunes, 7=Domingo
        return ($dia_semana >= 6); // 6=Sábado, 7=Domingo
    }

    /**
     * Determina si una fecha es día festivo
     */
    public function esFestivo($fecha) {
        return $this->festivos->esFestivo($fecha);
    }

    /**
     * Determina si una hora está dentro del horario diurno
     */
    public function esDiurna($hora) {
        $hora_diurna_inicio = $this->config->obtenerValor('hora_diurna_inicio', '06:00');
        $hora_diurna_fin = $this->config->obtenerValor('hora_diurna_fin', '18:00');

        // Convertir horas a minutos para comparación
        $hora_minutos = $this->horaAMinutos($hora);
        $inicio_minutos = $this->horaAMinutos($hora_diurna_inicio);
        $fin_minutos = $this->horaAMinutos($hora_diurna_fin);

        return ($hora_minutos >= $inicio_minutos && $hora_minutos < $fin_minutos);
    }

    /**
     * Convierte una hora en formato HH:MM a minutos totales
     */
    private function horaAMinutos($hora) {
        list($horas, $minutos) = explode(':', $hora);
        return (intval($horas) * 60) + intval($minutos);
    }

    /**
     * Determina el tipo de recargo aplicable para horas extras
     */
    public function determinarTipoRecargo($fecha, $hora_inicio) {
        $es_diurna = $this->esDiurna($hora_inicio);
        $es_fin_semana = $this->esFinDeSemana($fecha);
        $es_festivo = $this->esFestivo($fecha);

        if ($es_festivo) {
            return $es_diurna ? 'festivo_diurno' : 'festivo_nocturno';
        } elseif ($es_fin_semana) {
            return $es_diurna ? 'fin_semana_diurno' : 'fin_semana_nocturno';
        } else {
            return $es_diurna ? 'extra_diurna' : 'extra_nocturna';
        }
    }

    /**
     * Obtiene el factor multiplicador según el tipo de recargo
     */
    public function obtenerFactorRecargo($tipo_recargo) {
        $factores = [
            'extra_diurna' => $this->config->obtenerValor('factor_extra_diurna', 1.25),
            'extra_nocturna' => $this->config->obtenerValor('factor_extra_nocturna', 1.35),
            'fin_semana_diurno' => $this->config->obtenerValor('factor_fin_semana_diurno', 2.1),
            'fin_semana_nocturno' => $this->config->obtenerValor('factor_fin_semana_nocturno', 2.5),
            'festivo_diurno' => $this->config->obtenerValor('factor_festivo_diurno', 2.1),
            'festivo_nocturno' => $this->config->obtenerValor('factor_festivo_nocturno', 2.5)
        ];

        return floatval($factores[$tipo_recargo] ?? 1.0);
    }

    /**
     * Calcula el costo de horas normales
     */
    public function calcularCostoHorasNormales($usuario_id, $horas) {
        $valor_hora = $this->obtenerValorHoraUsuario($usuario_id);
        return $valor_hora * $horas;
    }

    /**
     * Calcula el costo de horas extras (diurnas o nocturnas, fin de semana, festivos)
     */
    public function calcularCostoHorasExtras($usuario_id, $horas, $hora_inicio, $hora_fin, $fecha = null) {
        $valor_hora_base = $this->obtenerValorHoraUsuario($usuario_id);

        // Si no se proporciona fecha, asumir hoy
        if (!$fecha) {
            $fecha = date('Y-m-d');
        }

        // Determinar tipo de recargo
        $tipo_recargo = $this->determinarTipoRecargo($fecha, $hora_inicio);
        $factor = $this->obtenerFactorRecargo($tipo_recargo);

        // Cálculo directo con factor multiplicador
        $valor_hora_extra = $valor_hora_base * $factor;
        $costo_total = $valor_hora_extra * $horas;

        return [
            'valor_hora_base' => $valor_hora_base,
            'tipo_recargo' => $tipo_recargo,
            'factor_recargo' => $factor,
            'valor_hora_extra' => $valor_hora_extra,
            'horas' => $horas,
            'costo_total' => $costo_total,
            'es_diurna' => strpos($tipo_recargo, 'diurno') !== false,
            'es_fin_semana' => strpos($tipo_recargo, 'fin_semana') !== false,
            'es_festivo' => strpos($tipo_recargo, 'festivo') !== false
        ];
    }

    /**
     * Obtiene el valor hora base de un usuario
     */
    public function obtenerValorHoraUsuario($usuario_id) {
        $query = "SELECT valor_hora_base FROM usuarios WHERE id = :usuario_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ? floatval($resultado['valor_hora_base']) : 0;
    }

    /**
     * Calcula costos totales de un reporte con filtros
     */
    public function calcularCostosReporte($filtros = []) {
        // Obtener registros de horas normales
        $query_normales = "SELECT r.usuario_id, r.horas_trabajadas, u.valor_hora_base
                          FROM registros_horas r
                          INNER JOIN usuarios u ON r.usuario_id = u.id
                          WHERE 1=1";
        
        $params = [];
        
        if (isset($filtros['fecha_inicio']) && isset($filtros['fecha_fin'])) {
            $query_normales .= " AND r.fecha BETWEEN :fecha_inicio AND :fecha_fin";
            $params[':fecha_inicio'] = $filtros['fecha_inicio'];
            $params[':fecha_fin'] = $filtros['fecha_fin'];
        }
        
        if (isset($filtros['usuario_id'])) {
            $query_normales .= " AND r.usuario_id = :usuario_id";
            $params[':usuario_id'] = $filtros['usuario_id'];
        }
        
        $stmt = $this->conn->prepare($query_normales);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $registros_normales = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calcular costo de horas normales
        $costo_horas_normales = 0;
        $total_horas_normales = 0;
        foreach ($registros_normales as $registro) {
            $costo = floatval($registro['valor_hora_base']) * floatval($registro['horas_trabajadas']);
            $costo_horas_normales += $costo;
            $total_horas_normales += floatval($registro['horas_trabajadas']);
        }
        
        // Obtener horas extras aprobadas
        $query_extras = "SELECT he.usuario_id, he.total_horas_extras, he.hora_inicio, he.hora_fin, 
                               he.fecha, u.valor_hora_base
                        FROM solicitudes_horas_extras he
                        INNER JOIN usuarios u ON he.usuario_id = u.id
                        WHERE he.estado = 'aprobada'";
        
        $params_extras = [];
        
        if (isset($filtros['fecha_inicio']) && isset($filtros['fecha_fin'])) {
            $query_extras .= " AND he.fecha BETWEEN :fecha_inicio AND :fecha_fin";
            $params_extras[':fecha_inicio'] = $filtros['fecha_inicio'];
            $params_extras[':fecha_fin'] = $filtros['fecha_fin'];
        }
        
        if (isset($filtros['usuario_id'])) {
            $query_extras .= " AND he.usuario_id = :usuario_id";
            $params_extras[':usuario_id'] = $filtros['usuario_id'];
        }
        
        $stmt_extras = $this->conn->prepare($query_extras);
        foreach ($params_extras as $key => $value) {
            $stmt_extras->bindValue($key, $value);
        }
        $stmt_extras->execute();
        $registros_extras = $stmt_extras->fetchAll(PDO::FETCH_ASSOC);
        
        // Calcular costo de horas extras
        $costo_extras_diurnas = 0;
        $costo_extras_nocturnas = 0;
        $costo_extras_fin_semana = 0;
        $costo_extras_festivos = 0;
        $horas_extras_diurnas = 0;
        $horas_extras_nocturnas = 0;
        $horas_extras_fin_semana = 0;
        $horas_extras_festivos = 0;

        foreach ($registros_extras as $registro) {
            $costo_detalle = $this->calcularCostoHorasExtras(
                $registro['usuario_id'],
                $registro['total_horas_extras'],
                $registro['hora_inicio'],
                $registro['hora_fin'],
                $registro['fecha'] ?? null
            );

            if ($costo_detalle['es_festivo']) {
                $costo_extras_festivos += $costo_detalle['costo_total'];
                $horas_extras_festivos += $costo_detalle['horas'];
            } elseif ($costo_detalle['es_fin_semana']) {
                $costo_extras_fin_semana += $costo_detalle['costo_total'];
                $horas_extras_fin_semana += $costo_detalle['horas'];
            } elseif ($costo_detalle['es_diurna']) {
                $costo_extras_diurnas += $costo_detalle['costo_total'];
                $horas_extras_diurnas += $costo_detalle['horas'];
            } else {
                $costo_extras_nocturnas += $costo_detalle['costo_total'];
                $horas_extras_nocturnas += $costo_detalle['horas'];
            }
        }

        return [
            'horas_normales' => $total_horas_normales,
            'costo_horas_normales' => $costo_horas_normales,
            'horas_extras_diurnas' => $horas_extras_diurnas,
            'costo_extras_diurnas' => $costo_extras_diurnas,
            'horas_extras_nocturnas' => $horas_extras_nocturnas,
            'costo_extras_nocturnas' => $costo_extras_nocturnas,
            'horas_extras_fin_semana' => $horas_extras_fin_semana,
            'costo_extras_fin_semana' => $costo_extras_fin_semana,
            'horas_extras_festivos' => $horas_extras_festivos,
            'costo_extras_festivos' => $costo_extras_festivos,
            'costo_total' => $costo_horas_normales + $costo_extras_diurnas + $costo_extras_nocturnas + $costo_extras_fin_semana + $costo_extras_festivos
        ];
    }

    /**
     * Obtiene información de tarifas configuradas
     */
    public function obtenerTarifas() {
        return [
            'hora_diurna_inicio' => $this->config->obtenerValor('hora_diurna_inicio', '06:00'),
            'hora_diurna_fin' => $this->config->obtenerValor('hora_diurna_fin', '18:00'),
            'factor_extra_diurna' => $this->config->obtenerValor('factor_extra_diurna', 1.25),
            'factor_extra_nocturna' => $this->config->obtenerValor('factor_extra_nocturna', 1.35),
            'factor_fin_semana_diurno' => $this->config->obtenerValor('factor_fin_semana_diurno', 2.1),
            'factor_fin_semana_nocturno' => $this->config->obtenerValor('factor_fin_semana_nocturno', 2.5),
            'factor_festivo_diurno' => $this->config->obtenerValor('factor_festivo_diurno', 2.1),
            'factor_festivo_nocturno' => $this->config->obtenerValor('factor_festivo_nocturno', 2.5),
            'mostrar_costos' => $this->config->obtenerValor('mostrar_costos', 1)
        ];
    }
}
?>
