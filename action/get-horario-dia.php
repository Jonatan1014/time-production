<?php
// action/get-horario-dia.php - Obtener horario laboral de un día específico
session_start();
header('Content-Type: application/json');

require_once '../includes/Class-usuario.php';
require_once '../includes/Class-configuracion.php';
require_once '../includes/Class-registros-horas.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    echo json_encode([
        'success' => false,
        'message' => 'No autorizado'
    ]);
    exit;
}

if (!isset($_GET['fecha'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Fecha no proporcionada'
    ]);
    exit;
}

$fecha = $_GET['fecha'];
$excluir_id = $_GET['excluir_id'] ?? null; // ID del registro a excluir (para edición)
$usuario_id = $_SESSION['user_id'];

try {
    require_once '../includes/Class-horarios-laborales.php';
    $HorarioLaboral_class = new HorarioLaboral();
    $RegistroHoras_class = new RegistroHoras();
    
    // Obtener día de la semana
    $dias_semana = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
    $dia_semana = $dias_semana[date('w', strtotime($fecha))];
    
    // Obtener horario del día
    $horario = $HorarioLaboral_class->obtenerPorDia($dia_semana);
    
    if (!$horario) {
        // Si no existe, inicializar horarios por defecto
        $HorarioLaboral_class->inicializarHorariosDefecto();
        $horario = $HorarioLaboral_class->obtenerPorDia($dia_semana);
    }
    
    $horario_dia = [
        'dia_semana' => ucfirst($dia_semana),
        'horas_totales' => floatval($horario['horas_totales']),
        'es_laborable' => intval($horario['es_laborable']),
        'descripcion' => $horario['descripcion']
    ];
    
    // Obtener horas ya registradas para esa fecha (excluyendo el registro en edición)
    $horas_registradas = $RegistroHoras_class->obtenerTotalHorasDia($usuario_id, $fecha, $excluir_id);
    
    // Calcular horas disponibles
    $horas_disponibles = $horario_dia['horas_totales'] - $horas_registradas;
    
    echo json_encode([
        'success' => true,
        'horario' => $horario_dia,
        'horas_registradas' => $horas_registradas,
        'horas_disponibles' => max(0, $horas_disponibles),
        'mensaje' => $horario_dia['descripcion']
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener horario: ' . $e->getMessage()
    ]);
}
?>
