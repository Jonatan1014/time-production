<?php
session_start();
require_once 'includes/Class-usuario.php';
require_once 'includes/Class-registros-horas.php';
require_once 'includes/Class-ordenes-produccion.php';
require_once 'includes/Class-horas-extras.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    header("Location: login.php");
    exit;
}

$RegistroHoras_class = new RegistroHoras();
$OrdenProduccion_class = new OrdenProduccion();
$HorasExtras_class = new HoraExtra();

$es_admin = isset($_SESSION['user_rol']) && ($_SESSION['user_rol'] === 'administrador' || $_SESSION['user_rol'] === 'Administrador');

// Instanciar clase de costos
require_once 'includes/Class-costos.php';
require_once 'includes/Class-configuracion.php';
$Costos_class = new Costos();
$Configuracion_class = new Configuracion();
$mostrar_costos = $es_admin && $Configuracion_class->obtenerValor('mostrar_costos', 1);

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
$usuarios = $es_admin ? $Usuario_class->obtenerUsuarios() : [];
$ordenes = $OrdenProduccion_class->obtenerOrdenesActivas();

// Calcular estadísticas
$total_horas_normales = 0;
$total_registros = count($registros);
$dias_trabajados = [];

foreach ($registros as $registro) {
    $total_horas_normales += $registro['horas_trabajadas'];
    $dias_trabajados[$registro['fecha']] = true;
}

// Obtener horas extras aprobadas en el rango
$filtros_extras = [
    'fecha_inicio' => $fecha_inicio,
    'fecha_fin' => $fecha_fin,
    'estado' => 'aprobada'
];

if ($usuario_id) {
    $filtros_extras['usuario_id'] = $usuario_id;
}

$horas_extras = $HorasExtras_class->obtenerTodasSolicitudes($filtros_extras);
$total_horas_extras = 0;

foreach ($horas_extras as $extra) {
    $total_horas_extras += $extra['total_horas_extras'];
}

$total_horas = $total_horas_normales + $total_horas_extras;
$total_dias_trabajados = count($dias_trabajados);
$promedio_horas_dia = $total_dias_trabajados > 0 ? $total_horas / $total_dias_trabajados : 0;

// Calcular costos si está habilitado
if ($mostrar_costos) {
    $costos_resumen = $Costos_class->calcularCostosReporte($filtros);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Reportes de Productividad | Sistema de Horas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="shortcut icon" href="assets/images/favicon.ico" />
    <script src="assets/js/hyper-config.js"></script>
    <link href="assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/vendor/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="wrapper">
        <?php include("includes/header.php"); ?>
        <?php include("includes/sidebar.php"); ?>

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">

                    <!-- Page Title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <button onclick="exportarReporte()" class="btn btn-success">
                                        <i class="mdi mdi-file-excel me-1"></i> Exportar Excel
                                    </button>
                                    <button onclick="imprimirReporte()" class="btn btn-primary ms-2">
                                        <i class="mdi mdi-printer me-1"></i> Imprimir
                                    </button>
                                </div>
                                <h4 class="page-title">Reportes de Productividad</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">
                                        <i class="mdi mdi-filter-outline me-1"></i> Filtros de Búsqueda
                                    </h5>
                                    <form method="GET" action="reportes.php" class="row g-3">
                                        <div class="col-md-3">
                                            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                                                   value="<?php echo $fecha_inicio; ?>">
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <label for="fecha_fin" class="form-label">Fecha Fin</label>
                                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                                                   value="<?php echo $fecha_fin; ?>">
                                        </div>

                                        <?php if ($es_admin): ?>
                                        <div class="col-md-3">
                                            <label for="usuario_id" class="form-label">Usuario</label>
                                            <select class="form-select" id="usuario_id" name="usuario_id">
                                                <option value="">Todos los usuarios</option>
                                                <?php foreach ($usuarios as $usuario): ?>
                                                    <option value="<?php echo $usuario['id']; ?>" 
                                                            <?php echo $usuario_id == $usuario['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($usuario['nombre_completo']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <?php endif; ?>

                                        <div class="col-md-<?php echo $es_admin ? '3' : '6'; ?>">
                                            <label for="orden_id" class="form-label">Orden de Producción</label>
                                            <select class="form-select" id="orden_id" name="orden_id">
                                                <option value="">Todas las órdenes</option>
                                                <?php foreach ($ordenes as $orden): ?>
                                                    <option value="<?php echo $orden['id']; ?>" 
                                                            <?php echo $orden_id == $orden['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($orden['codigo_op'] . ' - ' . $orden['nombre_producto']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="mdi mdi-magnify me-1"></i> Buscar
                                            </button>
                                            <a href="reportes.php" class="btn btn-light ms-2">
                                                <i class="mdi mdi-refresh me-1"></i> Limpiar Filtros
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="mdi mdi-clock-outline widget-icon bg-primary-lighten text-primary"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0" title="Total Horas">Total Horas</h5>
                                    <h3 class="mt-3 mb-3"><?php echo number_format($total_horas, 2); ?> hrs</h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-nowrap">Período: <?php echo date('d/m/Y', strtotime($fecha_inicio)) . ' - ' . date('d/m/Y', strtotime($fecha_fin)); ?></span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="mdi mdi-clock-check-outline widget-icon bg-success-lighten text-success"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0" title="Horas Normales">Horas Normales</h5>
                                    <h3 class="mt-3 mb-3"><?php echo number_format($total_horas_normales, 2); ?> hrs</h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-success me-2"><?php echo $total_registros; ?> registros</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="mdi mdi-clock-plus-outline widget-icon bg-warning-lighten text-warning"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0" title="Horas Extras">Horas Extras</h5>
                                    <h3 class="mt-3 mb-3"><?php echo number_format($total_horas_extras, 2); ?> hrs</h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-warning me-2"><?php echo count($horas_extras); ?> aprobadas</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="mdi mdi-calendar-range widget-icon bg-info-lighten text-info"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0" title="Promedio Diario">Promedio Diario</h5>
                                    <h3 class="mt-3 mb-3"><?php echo number_format($promedio_horas_dia, 2); ?> hrs</h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-info me-2"><?php echo $total_dias_trabajados; ?> días</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($mostrar_costos && isset($costos_resumen)): ?>
                    <!-- Estadísticas de Costos -->
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-light border">
                                <h5 class="alert-heading">
                                    <i class="mdi mdi-currency-usd text-primary me-2"></i>
                                    Análisis de Costos del Período
                                </h5>
                                <hr>
                                <div class="row">
                                    <div class="col-md-3">
                                        <h6 class="text-muted mb-1">Horas Normales</h6>
                                        <h4 class="mb-0">$<?php echo number_format($costos_resumen['costo_horas_normales'], 0, ',', '.'); ?></h4>
                                        <small class="text-muted"><?php echo number_format($costos_resumen['horas_normales'], 1); ?> horas</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h6 class="text-muted mb-1">Extras Diurnas</h6>
                                        <h4 class="mb-0 text-warning">$<?php echo number_format($costos_resumen['costo_extras_diurnas'], 0, ',', '.'); ?></h4>
                                        <small class="text-muted"><?php echo number_format($costos_resumen['horas_extras_diurnas'], 1); ?> horas</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h6 class="text-muted mb-1">Extras Nocturnas</h6>
                                        <h4 class="mb-0 text-dark">$<?php echo number_format($costos_resumen['costo_extras_nocturnas'], 0, ',', '.'); ?></h4>
                                        <small class="text-muted"><?php echo number_format($costos_resumen['horas_extras_nocturnas'], 1); ?> horas</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h6 class="text-muted mb-1">Costo Total</h6>
                                        <h3 class="mb-0 text-primary">$<?php echo number_format($costos_resumen['costo_total'], 0, ',', '.'); ?></h3>
                                        <small class="text-muted"><?php echo number_format($total_horas, 1); ?> horas totales</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Tabla de Horas Extras -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">
                                        <i class="mdi mdi-timer-sand text-warning me-2"></i>
                                        Detalle de Horas Extras
                                        <span class="badge bg-warning-lighten fs-6 ms-2">
                                            Total: <?php echo number_format($total_horas_extras, 2); ?> hrs
                                        </span>
                                    </h4>
                                    
                                    <?php 
                                    // Obtener todas las solicitudes (no solo aprobadas) para mostrar en el reporte
                                    $filtros_todas_extras = [
                                        'fecha_inicio' => $fecha_inicio,
                                        'fecha_fin' => $fecha_fin
                                    ];
                                    
                                    if ($usuario_id) {
                                        $filtros_todas_extras['usuario_id'] = $usuario_id;
                                    }
                                    
                                    $todas_horas_extras = $HorasExtras_class->obtenerTodasSolicitudes($filtros_todas_extras);
                                    ?>
                                    
                                    <?php if (count($todas_horas_extras) > 0): ?>
                                    <div class="table-responsive">
                                        <table id="tabla-horas-extras" class="table table-striped table-hover dt-responsive nowrap w-100">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Usuario</th>
                                                    <th>Orden</th>
                                                    <th>Hora Inicio</th>
                                                    <th>Hora Fin</th>
                                                    <th>Total Horas</th>
                                                    <th>Estado</th>
                                                    <th>Descripción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($todas_horas_extras as $extra): ?>
                                                <tr>
                                                    <td><?php echo date('d/m/Y', strtotime($extra['fecha'])); ?></td>
                                                    <td>
                                                        <?php echo htmlspecialchars($extra['usuario_nombre']); ?>
                                                        <?php if (isset($extra['departamento'])): ?>
                                                            <br><small class="text-muted"><?php echo htmlspecialchars($extra['departamento']); ?></small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($extra['codigo_op']); ?></strong><br>
                                                        <small class="text-muted"><?php echo htmlspecialchars($extra['nombre_producto']); ?></small>
                                                    </td>
                                                    <td><?php echo date('H:i', strtotime($extra['hora_inicio'])); ?></td>
                                                    <td><?php echo date('H:i', strtotime($extra['hora_fin'])); ?></td>
                                                    <td>
                                                        <span class="badge bg-warning fs-6"><?php echo number_format($extra['total_horas_extras'], 1); ?> hrs</span>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $estado_config = [
                                                            'pendiente' => ['class' => 'warning', 'icon' => 'mdi-clock-outline', 'texto' => 'Pendiente'],
                                                            'aprobada' => ['class' => 'success', 'icon' => 'mdi-check-circle', 'texto' => 'Aprobada'],
                                                            'rechazada' => ['class' => 'danger', 'icon' => 'mdi-close-circle', 'texto' => 'Rechazada'],
                                                            'cancelada' => ['class' => 'secondary', 'icon' => 'mdi-cancel', 'texto' => 'Cancelada']
                                                        ];
                                                        $config = $estado_config[$extra['estado']] ?? $estado_config['pendiente'];
                                                        ?>
                                                        <span class="badge bg-<?php echo $config['class']; ?>">
                                                            <i class="mdi <?php echo $config['icon']; ?> me-1"></i>
                                                            <?php echo $config['texto']; ?>
                                                        </span>
                                                        <?php if ($extra['estado'] === 'aprobada' || $extra['estado'] === 'rechazada'): ?>
                                                            <br><small class="text-muted">
                                                                <?php echo htmlspecialchars($extra['aprobador_nombre'] ?? 'N/A'); ?>
                                                            </small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <small><?php echo htmlspecialchars(substr($extra['descripcion_trabajo'], 0, 50)); ?><?php echo strlen($extra['descripcion_trabajo']) > 50 ? '...' : ''; ?></small>
                                                        <?php if (!empty($extra['comentario_aprobacion'])): ?>
                                                            <br><small class="text-muted fst-italic">
                                                                <i class="mdi mdi-comment-text-outline"></i>
                                                                <?php echo htmlspecialchars(substr($extra['comentario_aprobacion'], 0, 40)); ?><?php echo strlen($extra['comentario_aprobacion']) > 40 ? '...' : ''; ?>
                                                            </small>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php else: ?>
                                    <div class="alert alert-warning border-warning">
                                        <i class="mdi mdi-information me-2"></i>
                                        No se encontraron solicitudes de horas extras con los filtros seleccionados.
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de registros -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">
                                        <i class="mdi mdi-clock-outline text-primary me-2"></i>
                                        Detalle de Registros de Horas Normales
                                    </h4>
                                    
                                    <?php if (count($registros) > 0): ?>
                                    <div class="table-responsive">
                                        <table id="tabla-registros" class="table table-striped table-hover dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Usuario</th>
                                                    <th>Orden</th>
                                                    <th>Horas Trabajadas</th>
                                                    <th>Descripción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($registros as $registro): ?>
                                                <tr>
                                                    <td><?php echo date('d/m/Y', strtotime($registro['fecha'])); ?></td>
                                                    <td><?php echo htmlspecialchars($registro['usuario_nombre']); ?></td>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($registro['codigo_op']); ?></strong><br>
                                                        <small class="text-muted"><?php echo htmlspecialchars($registro['nombre_producto']); ?></small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info fs-6"><?php echo number_format($registro['horas_trabajadas'], 1); ?> hrs</span>
                                                    </td>
                                                    <td>
                                                        <small><?php echo htmlspecialchars(substr($registro['descripcion_trabajo'], 0, 50)); ?><?php echo strlen($registro['descripcion_trabajo']) > 50 ? '...' : ''; ?></small>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="mdi mdi-information me-2"></i>
                                        No se encontraron registros con los filtros seleccionados.
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <?php include("includes/footer.php"); ?>
        </div>
    </div>

    <?php include("includes/js.php"); ?>
    
    <!-- JSZip debe cargarse ANTES de DataTables Buttons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    
    <!-- DataTables Core -->
    <script src="assets/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="assets/vendor/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/vendor/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
    
    <!-- DataTables Buttons -->
    <script src="assets/vendor/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="assets/vendor/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script>
    <script src="assets/vendor/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="assets/vendor/datatables.net-buttons/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            // Configuración común de idioma
            const languageConfig = {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            };
            
            // Tabla de horas extras
            $('#tabla-horas-extras').DataTable({
                language: languageConfig,
                order: [[0, 'desc']],
                responsive: true,
                pageLength: 25,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>Brtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="mdi mdi-file-excel"></i> Excel',
                        className: 'btn btn-success btn-sm me-1',
                        title: 'Reporte_Horas_Extras',
                        filename: 'Horas_Extras_<?php echo date("Y-m-d"); ?>',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7],
                            modifier: {
                                page: 'all',
                                search: 'none'
                            },
                            format: {
                                body: function(data, row, column, node) {
                                    // Limpiar HTML de los badges y spans
                                    return $(data).text() || data;
                                }
                            }
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="mdi mdi-file-pdf"></i> PDF',
                        className: 'btn btn-danger btn-sm me-1',
                        title: 'Reporte Horas Extras',
                        filename: 'Horas_Extras_<?php echo date("Y-m-d"); ?>',
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6],
                            modifier: {
                                page: 'all'
                            },
                            format: {
                                body: function(data, row, column, node) {
                                    return $(data).text() || data;
                                }
                            }
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="mdi mdi-printer"></i> Imprimir',
                        className: 'btn btn-info btn-sm',
                        title: 'Reporte Horas Extras',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7]
                        }
                    }
                ]
            });
            
            // Tabla de registros normales
            $('#tabla-registros').DataTable({
                language: languageConfig,
                order: [[0, 'desc']],
                responsive: true,
                pageLength: 25,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>Brtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="mdi mdi-file-excel"></i> Excel',
                        className: 'btn btn-success btn-sm me-1',
                        title: 'Reporte_Horas_Normales',
                        filename: 'Horas_Normales_<?php echo date("Y-m-d"); ?>',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4],
                            modifier: {
                                page: 'all',
                                search: 'none'
                            },
                            format: {
                                body: function(data, row, column, node) {
                                    // Limpiar HTML de los badges y spans
                                    return $(data).text() || data;
                                }
                            }
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="mdi mdi-file-pdf"></i> PDF',
                        className: 'btn btn-danger btn-sm me-1',
                        title: 'Reporte Horas Normales',
                        filename: 'Horas_Normales_<?php echo date("Y-m-d"); ?>',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4],
                            modifier: {
                                page: 'all'
                            },
                            format: {
                                body: function(data, row, column, node) {
                                    return $(data).text() || data;
                                }
                            }
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="mdi mdi-printer"></i> Imprimir',
                        className: 'btn btn-info btn-sm',
                        title: 'Reporte Horas Normales',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4]
                        }
                    }
                ]
            });
        });

        function exportarReporte() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = 'action/exportar-reporte-excel.php?' + params.toString();
        }

        function imprimirReporte() {
            window.print();
        }
    </script>

    <style media="print">
        .btn, .page-title-right, .sidebar, .navbar-custom, .card-title i {
            display: none !important;
        }
        
        .content-page {
            margin-left: 0 !important;
        }
        
        .card {
            border: 1px solid #000 !important;
            page-break-inside: avoid;
        }
    </style>

</body>
</html>
