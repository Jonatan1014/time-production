<?php
// includes/Class-horarios-laborales.php
require_once 'conn-db.php';

class HorarioLaboral {
    private $conn;
    private $table_name = "horarios_laborales";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Obtener horario por día de la semana
     * @param string $dia_semana Día de la semana (lunes, martes, etc.)
     * @return array|false Horario del día o false si no existe
     */
    public function obtenerPorDia($dia_semana) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE dia_semana = :dia_semana LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':dia_semana', $dia_semana);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener todos los horarios
     * @return array Lista de horarios
     */
    public function obtenerTodos() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY FIELD(dia_semana, 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo')";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener solo días laborables
     * @return array Lista de días laborables
     */
    public function obtenerDiasLaborables() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE es_laborable = 1 ORDER BY FIELD(dia_semana, 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo')";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Actualizar horario de un día
     * @param string $dia_semana Día a actualizar
     * @param array $datos Datos del horario
     * @return bool True si se actualizó correctamente
     */
    public function actualizar($dia_semana, $datos) {
        $query = "UPDATE " . $this->table_name . " 
                  SET hora_inicio_manana = :hora_inicio_manana,
                      hora_fin_manana = :hora_fin_manana,
                      hora_inicio_tarde = :hora_inicio_tarde,
                      hora_fin_tarde = :hora_fin_tarde,
                      horas_totales = :horas_totales,
                      es_laborable = :es_laborable,
                      descripcion = :descripcion
                  WHERE dia_semana = :dia_semana";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':dia_semana', $dia_semana);
        $stmt->bindParam(':hora_inicio_manana', $datos['hora_inicio_manana']);
        $stmt->bindParam(':hora_fin_manana', $datos['hora_fin_manana']);
        $stmt->bindParam(':hora_inicio_tarde', $datos['hora_inicio_tarde']);
        $stmt->bindParam(':hora_fin_tarde', $datos['hora_fin_tarde']);
        $stmt->bindParam(':horas_totales', $datos['horas_totales']);
        $stmt->bindParam(':es_laborable', $datos['es_laborable']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);

        return $stmt->execute();
    }

    /**
     * Verificar si una fecha es laborable
     * @param string $fecha Fecha a verificar (Y-m-d)
     * @return bool True si es laborable
     */
    public function esFechaLaborable($fecha) {
        $dias_semana = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
        $dia_semana = $dias_semana[date('w', strtotime($fecha))];
        
        $horario = $this->obtenerPorDia($dia_semana);
        return $horario && $horario['es_laborable'] == 1;
    }

    /**
     * Obtener horas disponibles de un día
     * @param string $fecha Fecha a verificar (Y-m-d)
     * @return float|false Horas disponibles o false si no es laborable
     */
    public function obtenerHorasDisponibles($fecha) {
        $dias_semana = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
        $dia_semana = $dias_semana[date('w', strtotime($fecha))];
        
        $horario = $this->obtenerPorDia($dia_semana);
        
        if (!$horario || $horario['es_laborable'] == 0) {
            return false;
        }
        
        return floatval($horario['horas_totales']);
    }

    /**
     * Crear o insertar horario inicial
     * @param array $datos Datos del horario
     * @return bool True si se creó correctamente
     */
    public function crear($datos) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (dia_semana, hora_inicio_manana, hora_fin_manana, hora_inicio_tarde, hora_fin_tarde, 
                   horas_totales, es_laborable, descripcion) 
                  VALUES (:dia_semana, :hora_inicio_manana, :hora_fin_manana, :hora_inicio_tarde, :hora_fin_tarde,
                          :horas_totales, :es_laborable, :descripcion)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':dia_semana', $datos['dia_semana']);
        $stmt->bindParam(':hora_inicio_manana', $datos['hora_inicio_manana']);
        $stmt->bindParam(':hora_fin_manana', $datos['hora_fin_manana']);
        $stmt->bindParam(':hora_inicio_tarde', $datos['hora_inicio_tarde']);
        $stmt->bindParam(':hora_fin_tarde', $datos['hora_fin_tarde']);
        $stmt->bindParam(':horas_totales', $datos['horas_totales']);
        $stmt->bindParam(':es_laborable', $datos['es_laborable']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);

        return $stmt->execute();
    }

    /**
     * Inicializar horarios por defecto (Lunes a Viernes 8 horas)
     * @return bool True si se inicializó correctamente
     */
    public function inicializarHorariosDefecto() {
        $dias = [
            ['dia' => 'lunes', 'laborable' => 1],
            ['dia' => 'martes', 'laborable' => 1],
            ['dia' => 'miercoles', 'laborable' => 1],
            ['dia' => 'jueves', 'laborable' => 1],
            ['dia' => 'viernes', 'laborable' => 1],
            ['dia' => 'sabado', 'laborable' => 0],
            ['dia' => 'domingo', 'laborable' => 0]
        ];

        foreach ($dias as $dia) {
            $datos = [
                'dia_semana' => $dia['dia'],
                'hora_inicio_manana' => '08:00:00',
                'hora_fin_manana' => '12:00:00',
                'hora_inicio_tarde' => '14:00:00',
                'hora_fin_tarde' => '18:00:00',
                'horas_totales' => $dia['laborable'] ? 8.0 : 0.0,
                'es_laborable' => $dia['laborable'],
                'descripcion' => $dia['laborable'] ? 'Horario laboral estándar' : 'Día no laborable'
            ];

            // Verificar si ya existe
            $existe = $this->obtenerPorDia($dia['dia']);
            if (!$existe) {
                $this->crear($datos);
            }
        }

        return true;
    }
}
?>
