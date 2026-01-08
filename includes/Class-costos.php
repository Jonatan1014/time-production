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
     * Determina si una fecha es sábado
     */
    public function esSabado($fecha) {
        $dia_semana = date('N', strtotime($fecha)); // 1=Lunes, 7=Domingo
        return ($dia_semana == 6);
    }

    /**
     * Determina si una fecha es domingo
     */
    public function esDomingo($fecha) {
        $dia_semana = date('N', strtotime($fecha)); // 1=Lunes, 7=Domingo
        return ($dia_semana == 7);
    }

    /**
     * Determina si una fecha es fin de semana (sábado o domingo)
     */
    public function esFinDeSemana($fecha) {
        $dia_semana = date('N', strtotime($fecha)); // 1=Lunes, 7=Domingo
        return ($dia_semana >= 6); // 6=Sábado, 7=Domingo
    }

    /**
     * Determina si una fecha es día dominical o festivo
     * (domingo o festivo - legislación colombiana)
     */
    public function esDominicalOFestivo($fecha) {
        return $this->esDomingo($fecha) || $this->esFestivo($fecha);
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
        $hora_diurna_fin = $this->config->obtenerValor('hora_diurna_fin', '21:00');

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
     * Calcula el costo de horas normales según legislación colombiana
     * Detecta automáticamente si son horas dominicales/festivas y aplica recargo
     * 
     * @param int $usuario_id ID del usuario
     * @param float $horas Número de horas
     * @param string $fecha Fecha del registro (Y-m-d)
     * @param string $hora_inicio Hora de inicio (HH:MM) - opcional
     * @param string $hora_fin Hora de fin (HH:MM) - opcional
     * @return array Detalles del costo calculado
     */
    public function calcularCostoHorasNormales($usuario_id, $horas, $fecha = null, $hora_inicio = null, $hora_fin = null) {
        $valor_hora_base = $this->obtenerValorHoraUsuario($usuario_id);
        
        // Si no se proporciona fecha, asumir hoy
        if (!$fecha) {
            $fecha = date('Y-m-d');
        }

        // Verificar si es dominical o festivo
        $es_dominical_festivo = $this->esDominicalOFestivo($fecha);
        $es_domingo = $this->esDomingo($fecha);
        $es_festivo = $this->esFestivo($fecha);

        // Determinar si es diurno o nocturno (si se proporciona hora)
        $es_diurna = true;
        if ($hora_inicio) {
            $es_diurna = $this->esDiurna($hora_inicio);
        }

        // Factor aplicable
        $factor = 1.0;
        $tipo_hora = 'ordinaria_regular';
        
        if ($es_dominical_festivo) {
            // Horas en domingo o festivo tienen factor 1.75x
            $factor = floatval($this->config->obtenerValor('factor_dominical', 1.75));
            $tipo_hora = $es_festivo ? 'festivo_regular' : 'dominical_regular';
            
            // Si además es nocturna, aplicar recargo nocturno dominical
            if (!$es_diurna && $hora_inicio) {
                $factor = floatval($this->config->obtenerValor('recargo_nocturno_dominical', 2.1));
                $tipo_hora = $es_festivo ? 'festivo_nocturno_regular' : 'dominical_nocturno_regular';
            }
        } elseif ($hora_inicio && !$es_diurna) {
            // Hora nocturna ordinaria (lunes-sábado noche) - recargo adicional 0.35x
            // Factor = 1.0 + 0.35 = 1.35x
            $recargo = floatval($this->config->obtenerValor('recargo_nocturno_ordinario', 0.35));
            $factor = 1.0 + $recargo;
            $tipo_hora = 'ordinaria_nocturna';
        }

        $valor_hora = $valor_hora_base * $factor;
        $costo_total = $valor_hora * $horas;

        return [
            'valor_hora_base' => $valor_hora_base,
            'tipo_hora' => $tipo_hora,
            'factor' => $factor,
            'valor_hora' => $valor_hora,
            'horas' => $horas,
            'costo_total' => $costo_total,
            'es_diurna' => $es_diurna,
            'es_dominical' => $es_domingo,
            'es_festivo' => $es_festivo,
            'fecha' => $fecha
        ];
    }

    /**
     * Determina el tipo de recargo aplicable para horas extras según legislación colombiana
     * 
     * Legislación laboral colombiana:
     * - Hora extra diurna ordinaria (lun-sáb día): 1.25x
     * - Hora extra nocturna ordinaria (lun-sáb noche): 1.75x
     * - Hora extra diurna dominical/festiva: 2.0x
     * - Hora extra nocturna dominical/festiva: 2.5x
     * 
     * @param string $fecha Fecha en formato Y-m-d
     * @param string $hora_inicio Hora de inicio en formato HH:MM
     * @return string Tipo de recargo
     */
    public function determinarTipoRecargo($fecha, $hora_inicio) {
        $es_diurna = $this->esDiurna($hora_inicio);
        $es_dominical_festivo = $this->esDominicalOFestivo($fecha);

        if ($es_dominical_festivo) {
            return $es_diurna ? 'dominical_diurno' : 'dominical_nocturno';
        } else {
            return $es_diurna ? 'extra_diurna' : 'extra_nocturna';
        }
    }

    /**
     * Obtiene el factor multiplicador según el tipo de recargo
     * Basado en legislación laboral colombiana
     */
    public function obtenerFactorRecargo($tipo_recargo) {
        $factores = [
            // Horas extras ordinarias (lunes a sábado)
            'extra_diurna' => floatval($this->config->obtenerValor('factor_extra_diurna', 1.25)),
            'extra_nocturna' => floatval($this->config->obtenerValor('factor_extra_nocturna', 1.75)),
            
            // Horas extras dominicales y festivas
            'dominical_diurno' => floatval($this->config->obtenerValor('factor_dominical_diurno', 2.0)),
            'dominical_nocturno' => floatval($this->config->obtenerValor('factor_dominical_nocturno', 2.5))
        ];

        return $factores[$tipo_recargo] ?? 1.0;
    }

    /**
     * Calcula el costo de horas extras (diurnas o nocturnas, fin de semana, festivos)
     * Según legislación laboral colombiana
     * 
     * @param int $usuario_id ID del usuario
     * @param float $horas Horas extras trabajadas
     * @param string $hora_inicio Hora de inicio (HH:MM)
     * @param string $hora_fin Hora de fin (HH:MM)
     * @param string $fecha Fecha del trabajo (Y-m-d)
     * @return array Detalles del costo calculado
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

        // Información adicional para análisis
        $es_diurna = $this->esDiurna($hora_inicio);
        $es_dominical = $this->esDomingo($fecha);
        $es_festivo = $this->esFestivo($fecha);
        $es_dominical_festivo = $es_dominical || $es_festivo;

        return [
            'valor_hora_base' => $valor_hora_base,
            'tipo_recargo' => $tipo_recargo,
            'factor_recargo' => $factor,
            'valor_hora_extra' => $valor_hora_extra,
            'horas' => $horas,
            'costo_total' => $costo_total,
            'es_diurna' => $es_diurna,
            'es_dominical' => $es_dominical,
            'es_festivo' => $es_festivo,
            'es_dominical_festivo' => $es_dominical_festivo,
            'fecha' => $fecha,
            'hora_inicio' => $hora_inicio,
            'hora_fin' => $hora_fin
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
     * Calcula el costo de una hora según el tipo
     * 
     * @param int $cargo_id ID del cargo (usuario_id en este contexto)
     * @param string $tipo Tipo de hora: hr, hed, hen, hefd, hefn
     * @return float Costo de la hora
     */
    public function calcularCostoHora($cargo_id, $tipo) {
        // Obtener valor hora base del usuario (cargo_id es realmente usuario_id)
        $valor_hora_base = $this->obtenerValorHoraUsuario($cargo_id);
        
        if ($valor_hora_base == 0) {
            return 0;
        }
        
        // Factores según legislación colombiana
        $factores = [
            'hr' => 1.0,      // Hora regular
            'hed' => 1.25,    // Hora extra diurna
            'hen' => 1.75,    // Hora extra nocturna
            'hefd' => 1.75,   // Hora extra festiva diurna
            'hefn' => 2.1     // Hora extra festiva nocturna
        ];
        
        $factor = isset($factores[$tipo]) ? $factores[$tipo] : 1.0;
        
        return $valor_hora_base * $factor;
    }

    /**
     * Calcula costos totales de un reporte con filtros
     * Considera legislación laboral colombiana para todos los tipos de horas
     */
    public function calcularCostosReporte($filtros = []) {
        // Obtener registros de horas normales
        $query_normales = "SELECT r.id, r.usuario_id, r.fecha, r.horas_trabajadas, u.valor_hora_base
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
        
        // Calcular costo de horas normales con detección de dominicales/festivos
        $costo_horas_normales = 0;
        $total_horas_normales = 0;
        $horas_dominicales = 0;
        $costo_dominicales = 0;
        $horas_festivas = 0;
        $costo_festivas = 0;
        
        foreach ($registros_normales as $registro) {
            $detalle = $this->calcularCostoHorasNormales(
                $registro['usuario_id'],
                floatval($registro['horas_trabajadas']),
                $registro['fecha']
            );
            
            $costo_horas_normales += $detalle['costo_total'];
            $total_horas_normales += $detalle['horas'];
            
            // Clasificar si es dominical o festivo
            if ($detalle['es_festivo']) {
                $horas_festivas += $detalle['horas'];
                $costo_festivas += $detalle['costo_total'];
            } elseif ($detalle['es_dominical']) {
                $horas_dominicales += $detalle['horas'];
                $costo_dominicales += $detalle['costo_total'];
            }
        }
        
        // Obtener horas extras aprobadas
        $query_extras = "SELECT he.id, he.usuario_id, he.total_horas_extras, he.hora_inicio, he.hora_fin, 
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
        
        // Calcular costo de horas extras según legislación colombiana
        $costo_extras_diurnas = 0;
        $costo_extras_nocturnas = 0;
        $costo_extras_dominicales = 0;
        $horas_extras_diurnas = 0;
        $horas_extras_nocturnas = 0;
        $horas_extras_dominicales = 0;

        foreach ($registros_extras as $registro) {
            $costo_detalle = $this->calcularCostoHorasExtras(
                $registro['usuario_id'],
                $registro['total_horas_extras'],
                $registro['hora_inicio'],
                $registro['hora_fin'],
                $registro['fecha']
            );

            // Clasificar según tipo (dominical/festivo tiene prioridad)
            if ($costo_detalle['es_dominical_festivo']) {
                $costo_extras_dominicales += $costo_detalle['costo_total'];
                $horas_extras_dominicales += $costo_detalle['horas'];
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
            'horas_dominicales_regulares' => $horas_dominicales,
            'costo_dominicales_regulares' => $costo_dominicales,
            'horas_festivas_regulares' => $horas_festivas,
            'costo_festivas_regulares' => $costo_festivas,
            'horas_extras_diurnas' => $horas_extras_diurnas,
            'costo_extras_diurnas' => $costo_extras_diurnas,
            'horas_extras_nocturnas' => $horas_extras_nocturnas,
            'costo_extras_nocturnas' => $costo_extras_nocturnas,
            'horas_extras_dominicales' => $horas_extras_dominicales,
            'costo_extras_dominicales' => $costo_extras_dominicales,
            'costo_total' => $costo_horas_normales + $costo_extras_diurnas + $costo_extras_nocturnas + $costo_extras_dominicales
        ];
    }

    /**
     * Obtiene información de tarifas configuradas
     * Según legislación laboral colombiana
     */
    public function obtenerTarifas() {
        return [
            'hora_diurna_inicio' => $this->config->obtenerValor('hora_diurna_inicio', '06:00'),
            'hora_diurna_fin' => $this->config->obtenerValor('hora_diurna_fin', '21:00'),
            'factor_extra_diurna' => $this->config->obtenerValor('factor_extra_diurna', 1.25),
            'factor_extra_nocturna' => $this->config->obtenerValor('factor_extra_nocturna', 1.75),
            'recargo_nocturno_ordinario' => $this->config->obtenerValor('recargo_nocturno_ordinario', 0.35),
            'factor_dominical' => $this->config->obtenerValor('factor_dominical', 1.75),
            'factor_dominical_diurno' => $this->config->obtenerValor('factor_dominical_diurno', 2.0),
            'factor_dominical_nocturno' => $this->config->obtenerValor('factor_dominical_nocturno', 2.5),
            'recargo_nocturno_dominical' => $this->config->obtenerValor('recargo_nocturno_dominical', 2.1),
            'mostrar_costos' => $this->config->obtenerValor('mostrar_costos', 1)
        ];
    }
}
