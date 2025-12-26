<?php
// action/exportar-reporte-excel.php
session_start();
require_once '../includes/Class-usuario.php';
require_once '../includes/Class-registros-horas.php';
require_once '../includes/Class-ordenes-produccion.php';
require_once '../includes/Class-horas-extras.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    header("Location: ../login.php");
    exit;
}

$RegistroHoras_class = new RegistroHoras();
$HorasExtras_class = new HoraExtra();

$es_admin = isset($_SESSION['user_rol']) && ($_SESSION['user_rol'] === 'administrador' || $_SESSION['user_rol'] === 'Administrador');

// Obtener filtros
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-t');
$usuario_id = $es_admin ? ($_GET['usuario_id'] ?? null) : $_SESSION['user_id'];
$orden_id = $_GET['orden_id'] ?? null;

// Construir filtros
$filtros = [
    'fecha_inicio' => $fecha_inicio,
    'fecha_fin' => $fecha_fin
];

if ($usuario_id) {
    $filtros['usuario_id'] = $usuario_id;
}

if ($orden_id) {
    $filtros['orden_produccion_id'] = $orden_id;
}

// Obtener datos
$registros = $RegistroHoras_class->obtenerTodosRegistros($filtros);

// Calcular estadísticas
$total_horas_normales = 0;
foreach ($registros as $registro) {
    $total_horas_normales += $registro['horas_trabajadas'];
}

// Obtener todas las horas extras (igual que en reportes.php)
$filtros_todas_extras = [
    'fecha_inicio' => $fecha_inicio,
    'fecha_fin' => $fecha_fin
];

if ($usuario_id) {
    $filtros_todas_extras['usuario_id'] = $usuario_id;
}

if ($orden_id) {
    $filtros_todas_extras['orden_produccion_id'] = $orden_id;
}

$horas_extras = $HorasExtras_class->obtenerTodasSolicitudes($filtros_todas_extras);

// Calcular total de horas extras (solo aprobadas para el resumen)
$total_horas_extras = 0;
foreach ($horas_extras as $extra) {
    if ($extra['estado'] === 'aprobada') {
        $total_horas_extras += $extra['total_horas_extras'];
    }
}

// Nombre del archivo
$filename = "reporte_horas_" . date('Y-m-d_His') . ".csv";

// Headers para descarga
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

// Crear salida CSV
$output = fopen('php://output', 'w');

// BOM para Excel UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Encabezado del reporte
fputcsv($output, ['REPORTE DE HORAS DE PRODUCCIÓN'], ";");
fputcsv($output, ['Generado:', date('d/m/Y H:i:s')], ";");
fputcsv($output, ['Período:', date('d/m/Y', strtotime($fecha_inicio)) . ' - ' . date('d/m/Y', strtotime($fecha_fin))], ";");
fputcsv($output, [], ";");

// Resumen
fputcsv($output, ['RESUMEN'], ";");
fputcsv($output, ['Total Horas Normales:', number_format($total_horas_normales, 2) . ' hrs'], ";");
fputcsv($output, ['Total Horas Extras:', number_format($total_horas_extras, 2) . ' hrs'], ";");
fputcsv($output, ['Total General:', number_format($total_horas_normales + $total_horas_extras, 2) . ' hrs'], ";");
fputcsv($output, ['Total Registros:', count($registros)], ";");
fputcsv($output, [], ";");

// Encabezados de la tabla
fputcsv($output, [
    'Fecha',
    'Usuario',
    'Departamento',
    'Orden',
    'Producto',
    'Horas Trabajadas',
    'Descripción',
    'Estado'
], ";");

// Datos
foreach ($registros as $registro) {
    fputcsv($output, [
        date('d/m/Y', strtotime($registro['fecha'])),
        $registro['usuario_nombre'],
        $registro['departamento'] ?? 'N/A',
        $registro['codigo_op'],
        $registro['nombre_producto'],
        number_format($registro['horas_trabajadas'], 1),
        $registro['descripcion_trabajo'],
        ucfirst($registro['estado'])
    ], ";");
}

// Agregar todas las horas extras
if (count($horas_extras) > 0) {
    fputcsv($output, [], ";");
    fputcsv($output, ['HORAS EXTRAS (TODAS LAS SOLICITUDES)'], ";");
    fputcsv($output, [
        'Fecha',
        'Usuario',
        'Departamento',
        'Orden',
        'Producto',
        'Hora Inicio',
        'Hora Fin',
        'Total Horas',
        'Estado',
        'Descripción',
        'Aprobado Por',
        'Fecha Aprobación'
    ], ";");

    foreach ($horas_extras as $extra) {
        fputcsv($output, [
            date('d/m/Y', strtotime($extra['fecha'])),
            $extra['usuario_nombre'],
            $extra['departamento'] ?? 'N/A',
            $extra['codigo_op'],
            $extra['nombre_producto'],
            date('H:i', strtotime($extra['hora_inicio'])),
            date('H:i', strtotime($extra['hora_fin'])),
            number_format($extra['total_horas_extras'], 1),
            ucfirst($extra['estado']),
            $extra['descripcion_trabajo'],
            $extra['aprobador_nombre'] ?? 'N/A',
            isset($extra['fecha_respuesta']) && $extra['fecha_respuesta'] ? date('d/m/Y H:i', strtotime($extra['fecha_respuesta'])) : 'N/A'
        ], ";");
    }
}

fclose($output);
exit;
?>
