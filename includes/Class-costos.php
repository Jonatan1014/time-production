<?php
// includes/Class-costos.php
require_once 'conn-db.php';
require_once 'Class-configuracion.php';

class Costos {
    private $conn;
    private $config;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->config = new Configuracion();
    }

    /**
     * Determina si una hora específica es diurna o nocturna
     */
    public function esDiurna($hora_inicio, $hora_fin = null) {
        $turno_diurno_inicio = $this->config->obtenerValor('hora_diurna_inicio', '06:00');
        $turno_diurno_fin = $this->config->obtenerValor('hora_diurna_fin', '18:00');

        $inicio = strtotime($turno_diurno_inicio);
        $fin = strtotime($turno_diurno_fin);
        $hora = strtotime($hora_inicio);

        // Si la hora está entre inicio y fin del turno diurno
        return ($hora >= $inicio && $hora < $fin);
    }

    /**
     * Calcula el costo de horas normales
     */
    public function calcularCostoHorasNormales($usuario_id, $horas) {
        $valor_hora = $this->obtenerValorHoraUsuario($usuario_id);
        return $valor_hora * $horas;
    }

    /**
     * Calcula el costo de horas extras (diurnas o nocturnas)
     */
    public function calcularCostoHorasExtras($usuario_id, $horas, $hora_inicio, $hora_fin) {
        $valor_hora_base = $this->obtenerValorHoraUsuario($usuario_id);
        
        // Determinar si son horas diurnas o nocturnas
        $es_diurna = $this->esDiurna($hora_inicio);
        
        if ($es_diurna) {
            $porcentaje = $this->config->obtenerValor('porcentaje_extra_diurna', 25);
            $tipo = 'diurna';
        } else {
            $porcentaje = $this->config->obtenerValor('porcentaje_extra_nocturna', 75);
            $tipo = 'nocturna';
        }
        
        // Cálculo correcto: valor_base + (valor_base * porcentaje/100)
        $recargo = $valor_hora_base * ($porcentaje / 100);
        $valor_hora_extra = $valor_hora_base + $recargo;
        $costo_total = $valor_hora_extra * $horas;
        
        return [
            'valor_hora_base' => $valor_hora_base,
            'porcentaje_recargo' => $porcentaje,
            'valor_hora_extra' => $valor_hora_extra,
            'tipo' => $tipo,
            'horas' => $horas,
            'costo_total' => $costo_total
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
                               u.valor_hora_base
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
        $horas_extras_diurnas = 0;
        $horas_extras_nocturnas = 0;
        
        foreach ($registros_extras as $registro) {
            $costo_detalle = $this->calcularCostoHorasExtras(
                $registro['usuario_id'], 
                $registro['total_horas_extras'],
                $registro['hora_inicio'],
                $registro['hora_fin']
            );
            
            if ($costo_detalle['tipo'] === 'diurna') {
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
            'costo_total' => $costo_horas_normales + $costo_extras_diurnas + $costo_extras_nocturnas
        ];
    }

    /**
     * Obtiene información de tarifas configuradas
     */
    public function obtenerTarifas() {
        return [
            'hora_diurna_inicio' => $this->config->obtenerValor('hora_diurna_inicio', '06:00'),
            'hora_diurna_fin' => $this->config->obtenerValor('hora_diurna_fin', '18:00'),
            'porcentaje_extra_diurna' => $this->config->obtenerValor('porcentaje_extra_diurna', 25),
            'porcentaje_extra_nocturna' => $this->config->obtenerValor('porcentaje_extra_nocturna', 75),
            'mostrar_costos' => $this->config->obtenerValor('mostrar_costos', 1)
        ];
    }
}
?>
