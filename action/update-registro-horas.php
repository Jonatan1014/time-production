<?php
session_start();
require_once '../includes/Class-usuario.php';
require_once '../includes/Class-registros-horas.php';

$Usuario_class = new Usuario();

// Verificar que el usuario esté logueado
if (!$Usuario_class->usuarioLogueado()) {
    $_SESSION['error'] = "Debe iniciar sesión";
    header("Location: ../login.php");
    exit;
}

// Verificar que se envió el formulario
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Método no permitido";
    header("Location: ../mis-horas.php");
    exit;
}

// Obtener datos del formulario
$id = $_POST['id'] ?? null;
$fecha = $_POST['fecha'] ?? null;
$orden_produccion_id = $_POST['orden_produccion_id'] ?? null;
$horas_trabajadas = $_POST['horas_trabajadas'] ?? null;
$descripcion_trabajo = trim($_POST['descripcion_trabajo'] ?? '');

// Validar datos requeridos
if (!$id || !$fecha || !$orden_produccion_id || !$horas_trabajadas || empty($descripcion_trabajo)) {
    $_SESSION['error'] = "Todos los campos obligatorios deben ser completados";
    header("Location: ../editar-registro.php?id=" . $id);
    exit;
}

// Validar longitud de descripción
if (strlen($descripcion_trabajo) < 10) {
    $_SESSION['error'] = "La descripción del trabajo debe tener al menos 10 caracteres";
    header("Location: ../editar-registro.php?id=" . $id);
    exit;
}

$RegistroHoras_class = new RegistroHoras();

// Obtener el registro actual
$registro = $RegistroHoras_class->obtenerRegistroPorId($id);

if (!$registro) {
    $_SESSION['error'] = "Registro no encontrado";
    header("Location: ../mis-horas.php");
    exit;
}

// Verificar que el usuario sea el dueño del registro
if ($registro['usuario_id'] != $_SESSION['user_id']) {
    $_SESSION['error'] = "No tiene permisos para editar este registro";
    header("Location: ../detalle-registro.php?id=" . $id);
    exit;
}

// Verificar que el registro esté en estado 'registrado'
if ($registro['estado'] !== 'registrado') {
    $_SESSION['error'] = "No se puede editar un registro que ya fue " . $registro['estado'];
    header("Location: ../detalle-registro.php?id=" . $id);
    exit;
}

// Verificar que no haya sido editado previamente
if (isset($registro['editado']) && $registro['editado'] == 1) {
    $_SESSION['error'] = "Este registro ya fue editado anteriormente. Solo se permite una edición por registro";
    header("Location: ../detalle-registro.php?id=" . $id);
    exit;
}

// Obtener horario laboral del día seleccionado
require_once '../includes/Class-horarios-laborales.php';
$Horarios_class = new HorarioLaboral();

// Obtener día de la semana en español
$dias_semana = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
$dia_semana = $dias_semana[date('w', strtotime($fecha))];

// Obtener horario del día
$horario_dia = $Horarios_class->obtenerPorDia($dia_semana);

if (!$horario_dia) {
    $_SESSION['error'] = "No hay horario laboral configurado para " . ucfirst($dia_semana);
    header("Location: ../editar-registro.php?id=" . $id);
    exit;
}

// Verificar que sea día laborable
if ($horario_dia['es_laborable'] == 0) {
    $_SESSION['error'] = "El " . ucfirst($dia_semana) . " no es un día laborable según la configuración del sistema";
    header("Location: ../editar-registro.php?id=" . $id);
    exit;
}

$horas_maximas_dia = $horario_dia['horas_totales'];

// Calcular total de horas ya registradas en ese día (excluyendo el registro actual)
$total_horas_dia = $RegistroHoras_class->obtenerTotalHorasDia($registro['usuario_id'], $fecha, $id);

// Validar que no se excedan las horas permitidas para ese día
if (($total_horas_dia + $horas_trabajadas) > $horas_maximas_dia) {
    $horas_disponibles = $horas_maximas_dia - $total_horas_dia;
    $_SESSION['error'] = "Las horas totales del " . ucfirst($dia_semana) . " no pueden exceder " . $horas_maximas_dia . " horas. Ya tienes " . number_format($total_horas_dia, 2) . " horas registradas. Disponibles: " . number_format($horas_disponibles, 2) . " horas";
    header("Location: ../editar-registro.php?id=" . $id);
    exit;
}

// Preparar datos para actualización
$datos_actualizacion = [
    'fecha' => $fecha,
    'orden_produccion_id' => $orden_produccion_id,
    'horas_trabajadas' => $horas_trabajadas,
    'descripcion_trabajo' => $descripcion_trabajo
];

// Actualizar registro y marcar como editado
$resultado = $RegistroHoras_class->actualizarRegistroUnaVez($id, $datos_actualizacion);

if ($resultado) {
    $_SESSION['success'] = "Registro actualizado exitosamente. Este registro ya no puede ser editado nuevamente.";
    header("Location: ../detalle-registro.php?id=" . $id);
} else {
    $_SESSION['error'] = "Error al actualizar el registro. Por favor, intente nuevamente.";
    header("Location: ../editar-registro.php?id=" . $id);
}
exit;
?>
