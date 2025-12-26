<?php
// includes/Class-configuracion.php
require_once 'conn-db.php';

class Configuracion {
    private $conn;
    private $table_name = "configuracion_sistema";
    private static $cache = [];

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Obtener valor de configuración por clave
     * @param string $clave Clave de configuración
     * @param mixed $default Valor por defecto si no existe
     * @return mixed Valor de la configuración
     */
    public function obtenerValor($clave, $default = null) {
        // Verificar si está en cache
        if (isset(self::$cache[$clave])) {
            return self::$cache[$clave];
        }

        $query = "SELECT valor, tipo FROM " . $this->table_name . " WHERE clave = :clave LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':clave', $clave);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($resultado) {
            $valor = $this->convertirTipo($resultado['valor'], $resultado['tipo']);
            self::$cache[$clave] = $valor;
            return $valor;
        }
        
        return $default;
    }

    /**
     * Obtener múltiples configuraciones por claves
     * @param array $claves Array de claves de configuración
     * @return array Array asociativo con clave => valor
     */
    public function obtenerMultiples($claves) {
        $placeholders = str_repeat('?,', count($claves) - 1) . '?';
        $query = "SELECT clave, valor, tipo FROM " . $this->table_name . " WHERE clave IN ($placeholders)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($claves);
        
        $resultado = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $valor = $this->convertirTipo($row['valor'], $row['tipo']);
            $resultado[$row['clave']] = $valor;
            self::$cache[$row['clave']] = $valor;
        }
        
        return $resultado;
    }

    /**
     * Obtener todas las configuraciones de una categoría
     * @param string $categoria Categoría de configuración
     * @return array Array asociativo con clave => valor
     */
    public function obtenerPorCategoria($categoria) {
        $query = "SELECT clave, valor, tipo FROM " . $this->table_name . " WHERE categoria = :categoria";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':categoria', $categoria);
        $stmt->execute();
        
        $resultado = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $valor = $this->convertirTipo($row['valor'], $row['tipo']);
            $resultado[$row['clave']] = $valor;
            self::$cache[$row['clave']] = $valor;
        }
        
        return $resultado;
    }

    /**
     * Actualizar valor de configuración
     * @param string $clave Clave de configuración
     * @param mixed $valor Nuevo valor
     * @return bool True si se actualizó correctamente
     */
    public function actualizarValor($clave, $valor) {
        $query = "UPDATE " . $this->table_name . " SET valor = :valor WHERE clave = :clave";
        $stmt = $this->conn->prepare($query);
        
        $valor_str = is_bool($valor) ? ($valor ? '1' : '0') : (string)$valor;
        
        $stmt->bindParam(':valor', $valor_str);
        $stmt->bindParam(':clave', $clave);
        
        $resultado = $stmt->execute();
        
        // Limpiar cache
        if ($resultado && isset(self::$cache[$clave])) {
            unset(self::$cache[$clave]);
        }
        
        return $resultado;
    }

    /**
     * Convertir valor según su tipo
     * @param string $valor Valor como string
     * @param string $tipo Tipo de dato
     * @return mixed Valor convertido
     */
    private function convertirTipo($valor, $tipo) {
        switch ($tipo) {
            case 'numero':
                return (int)$valor;
            case 'decimal':
                return (float)$valor;
            case 'booleano':
                return $valor === '1' || $valor === 'true' || $valor === true;
            case 'json':
                return json_decode($valor, true);
            case 'texto':
            default:
                return $valor;
        }
    }

    /**
     * Obtener configuración de horas (para validaciones)
     * @return array Configuración de horas
     */
    public function obtenerConfigHoras() {
        $claves = [
            'horas_maximas_por_dia',
            'horas_minimas_por_registro',
            'horas_maximas_extras',
            'incremento_horas'
        ];
        
        return $this->obtenerMultiples($claves);
    }

    /**
     * Validar que las horas cumplan con las reglas del sistema
     * @param float $horas Horas a validar
     * @param string $tipo 'normal' o 'extra'
     * @return array ['valido' => bool, 'errores' => array]
     */
    public function validarHoras($horas, $tipo = 'normal') {
        $config = $this->obtenerConfigHoras();
        $errores = [];
        
        $horas = floatval($horas);
        
        // Validar mínimo
        $minimo = $config['horas_minimas_por_registro'] ?? 0.5;
        if ($horas < $minimo) {
            $errores[] = "Debe registrar al menos " . number_format($minimo, 1) . " horas";
        }
        
        // Validar máximo según tipo
        if ($tipo === 'normal') {
            $maximo = $config['horas_maximas_por_dia'] ?? 8.5;
            if ($horas > $maximo) {
                $errores[] = "No puede registrar más de " . number_format($maximo, 1) . " horas normales por día";
            }
        } else {
            $maximo = $config['horas_maximas_extras'] ?? 4.0;
            if ($horas > $maximo) {
                $errores[] = "No puede solicitar más de " . number_format($maximo, 1) . " horas extras por día";
            }
        }
        
        // Validar incremento
        $incremento = $config['incremento_horas'] ?? 0.5;
        if (fmod($horas, $incremento) != 0) {
            $errores[] = "Las horas deben ser múltiplos de " . number_format($incremento, 1) . " (ejemplo: " . 
                         number_format($incremento, 1) . ", " . 
                         number_format($incremento * 2, 1) . ", " . 
                         number_format($incremento * 3, 1) . "...)";
        }
        
        return [
            'valido' => empty($errores),
            'errores' => $errores,
            'config' => $config
        ];
    }

    /**
     * Obtener horas laborales de un día específico
     * @param string $fecha Fecha en formato Y-m-d
     * @return array Información del horario laboral del día
     */
    public function obtenerHorasLaboralesDia($fecha) {
        // Convertir fecha a día de la semana en español
        $timestamp = strtotime($fecha);
        $dia_ingles = strtolower(date('l', $timestamp));
        
        $dias_map = [
            'monday' => 'lunes',
            'tuesday' => 'martes',
            'wednesday' => 'miercoles',
            'thursday' => 'jueves',
            'friday' => 'viernes',
            'saturday' => 'sabado',
            'sunday' => 'domingo'
        ];
        
        $dia_semana = $dias_map[$dia_ingles] ?? 'lunes';
        
        $query = "SELECT * FROM horarios_laborales WHERE dia_semana = :dia_semana LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':dia_semana', $dia_semana);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($resultado) {
            return [
                'dia_semana' => $resultado['dia_semana'],
                'horas_totales' => floatval($resultado['horas_totales']),
                'es_laborable' => (bool)$resultado['es_laborable'],
                'hora_inicio_manana' => $resultado['hora_inicio_manana'],
                'hora_fin_manana' => $resultado['hora_fin_manana'],
                'hora_inicio_tarde' => $resultado['hora_inicio_tarde'],
                'hora_fin_tarde' => $resultado['hora_fin_tarde'],
                'descripcion' => $resultado['descripcion']
            ];
        }
        
        // Si no hay configuración, usar valores por defecto
        return [
            'dia_semana' => $dia_semana,
            'horas_totales' => 8.0,
            'es_laborable' => true,
            'descripcion' => 'Horario estándar'
        ];
    }

    /**
     * Limpiar cache de configuración
     */
    public static function limpiarCache() {
        self::$cache = [];
    }
}
?>
