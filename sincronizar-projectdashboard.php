<?php
// sincronizar-projectdashboard.php
session_start();
require_once 'includes/Class-usuario.php';

$Usuario_class = new Usuario();

// Verificar autenticación y permisos
if (!$Usuario_class->usuarioLogueado()) {
    header("Location: login.php");
    exit;
}

if (!$Usuario_class->verificarPermisos('Administrador')) {
    header("Location: index.php");
    exit;
}

require_once 'includes/Class-sincronizacion.php';
require_once 'includes/Class-configuracion.php';

$Sincronizacion_class = new Sincronizacion();
$Config_class = new Configuracion();

// Obtener estadísticas
$estadisticas = $Sincronizacion_class->obtenerEstadisticas();

// Verificar si la sincronización está habilitada
$sincronizacion_habilitada = $Config_class->obtenerValor('projectdashboard_habilitado', false);

// Obtener filtros
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-t');
$usuario_id = $_GET['usuario_id'] ?? null;

$filtros = [
    'fecha_inicio' => $fecha_inicio,
    'fecha_fin' => $fecha_fin
];

if ($usuario_id) {
    $filtros['usuario_id'] = $usuario_id;
}

// Obtener registros pendientes agrupados
$registros_pendientes = $Sincronizacion_class->obtenerRegistrosPendientesAgrupados($filtros);

// Obtener horas de producción pendientes
$horas_produccion_pendientes = $Sincronizacion_class->obtenerHorasProduccionPendientes($filtros);

// Obtener historial reciente
$historial = $Sincronizacion_class->obtenerHistorialSincronizacion($filtros, 50);

// Obtener historial de producción sincronizada
$historial_produccion = $Sincronizacion_class->obtenerHistorialProduccionSincronizada($filtros, 50);

// Obtener lista de usuarios para filtro
$Usuario_class_list = new Usuario();
$usuarios = $Usuario_class_list->obtenerUsuarios(['rol' => 'trabajador']);

$pageTitle = "Sincronización ProjectDashboard";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title><?php echo $pageTitle; ?> | Time Production</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- Hyper Config -->
    <script src="assets/js/hyper-config.js"></script>

    <!-- App css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />

    <!-- DataTables -->
    <link href="assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <?php include("includes/header.php"); ?>
        <?php include("includes/sidebar.php"); ?>

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">

                <!-- Start Content-->
                <div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Sincronización ProjectDashboard</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <i class="mdi mdi-cloud-sync me-1"></i>
                    Sincronización con ProjectDashboard
                </h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <!-- Estadísticas -->
    <div class="row">
        <div class="col-md-2">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-sync widget-icon bg-primary text-white"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0" style="font-size: 0.85rem;">Total Sincronizados</h5>
                    <h3 class="mt-3 mb-3"><?php echo number_format($estadisticas['total_sincronizados']); ?></h3>
                    <p class="mb-0 text-muted">
                        <span class="text-nowrap" style="font-size: 0.75rem;">Todos los registros</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-clock-outline widget-icon bg-warning text-white"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0" style="font-size: 0.85rem;">Horas Normales</h5>
                    <h3 class="mt-3 mb-3"><?php echo number_format($estadisticas['pendientes_normales']); ?></h3>
                    <p class="mb-0 text-muted">
                        <span class="text-nowrap" style="font-size: 0.75rem;">Pendientes</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-clock-plus-outline widget-icon bg-info text-white"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0" style="font-size: 0.85rem;">Horas Extras</h5>
                    <h3 class="mt-3 mb-3"><?php echo number_format($estadisticas['pendientes_extras']); ?></h3>
                    <p class="mb-0 text-muted">
                        <span class="text-nowrap" style="font-size: 0.75rem;">Pendientes</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-factory widget-icon bg-secondary text-white"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0" style="font-size: 0.85rem;">Horas Producción</h5>
                    <h3 class="mt-3 mb-3"><?php echo number_format($estadisticas['pendientes_produccion']); ?></h3>
                    <p class="mb-0 text-muted">
                        <span class="text-nowrap" style="font-size: 0.75rem;">Pendientes</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-alert-circle widget-icon bg-danger text-white"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0" style="font-size: 0.85rem;">Total Pendientes</h5>
                    <h3 class="mt-3 mb-3"><?php echo number_format($estadisticas['pendientes_total']); ?></h3>
                    <p class="mb-0 text-muted">
                        <span class="text-nowrap" style="font-size: 0.75rem;">Todos los tipos</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-calendar-clock widget-icon bg-success text-white"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0" style="font-size: 0.85rem;">Última Sincronización</h5>
                    <h3 class="mt-3 mb-3">
                        <?php 
                        if ($estadisticas['ultima_sincronizacion']) {
                            echo date('d/m/Y', strtotime($estadisticas['ultima_sincronizacion']));
                        } else {
                            echo '<small>Nunca</small>';
                        }
                        ?>
                    </h3>
                    <p class="mb-0 text-muted">
                        <span class="text-nowrap" style="font-size: 0.75rem;">
                            <?php 
                            if ($estadisticas['ultima_sincronizacion']) {
                                echo date('H:i', strtotime($estadisticas['ultima_sincronizacion']));
                            }
                            ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php if (!$sincronizacion_habilitada): ?>
    <div class="row">
        <div class="col-12">
            <div class="alert alert-warning" role="alert">
                <i class="mdi mdi-alert-outline me-1"></i>
                <strong>Sincronización deshabilitada.</strong> 
                Debe habilitar la sincronización en <a href="configuracion.php" class="alert-link">Configuración</a> para enviar datos a ProjectDashboard.
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Filtros -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">
                        <i class="mdi mdi-filter-variant me-1"></i>
                        Filtros de Búsqueda
                    </h4>
                    <form method="GET" action="" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Fecha Inicio</label>
                            <input type="date" class="form-control" name="fecha_inicio" value="<?php echo $fecha_inicio; ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha Fin</label>
                            <input type="date" class="form-control" name="fecha_fin" value="<?php echo $fecha_fin; ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Usuario</label>
                            <select class="form-select" name="usuario_id">
                                <option value="">Todos los usuarios</option>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <option value="<?php echo $usuario['id']; ?>" <?php echo ($usuario_id == $usuario['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($usuario['nombre_completo']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="mdi mdi-magnify me-1"></i>
                                Filtrar
                            </button>
                        </div>
                    </form>
                    
                    <!-- Filtro de OPs para excluir -->
                    <?php if (count($registros_pendientes) > 0): ?>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <strong><i class="mdi mdi-filter-outline me-1"></i>Filtrar por OP:</strong>
                                Puede seleccionar qué OPs desea incluir o excluir de la sincronización.
                                <button type="button" class="btn btn-sm btn-outline-primary ms-2" id="btnSeleccionarTodas">
                                    <i class="mdi mdi-check-all"></i> Incluir todas
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary ms-1" id="btnDeseleccionarTodas">
                                    <i class="mdi mdi-close"></i> Excluir todas
                                </button>
                            </div>
                            <div id="filtroOPs">
                                <?php 
                                // Obtener OPs únicas
                                $ops_unicas = [];
                                foreach ($registros_pendientes as $reg) {
                                    $op = $reg['proyecto_numero'];
                                    if (!isset($ops_unicas[$op])) {
                                        $ops_unicas[$op] = [
                                            'codigo' => $op,
                                            'orden_id' => $reg['orden_produccion_id'],
                                            'registros' => 0,
                                            'horas' => 0,
                                            'monto' => 0
                                        ];
                                    }
                                    $ops_unicas[$op]['registros']++;
                                    $ops_unicas[$op]['horas'] += $reg['tiempo_ordinario'] + $reg['tiempo_extra'];
                                    $ops_unicas[$op]['monto'] += $reg['total_pagado'];
                                }
                                ?>
                                <div class="row">
                                    <?php foreach ($ops_unicas as $op): ?>
                                    <div class="col-md-3 mb-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input op-filter" type="checkbox" id="op_<?php echo $op['orden_id']; ?>" value="<?php echo $op['codigo']; ?>" data-op-id="<?php echo $op['orden_id']; ?>" checked>
                                            <label class="form-check-label" for="op_<?php echo $op['orden_id']; ?>">
                                                <strong><?php echo htmlspecialchars($op['codigo']); ?></strong>
                                                <br><small class="text-muted"><?php echo $op['registros']; ?> reg. | <?php echo number_format($op['horas'], 1); ?> hrs | $<?php echo number_format($op['monto']); ?></small>
                                            </label>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Registros Pendientes -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title">
                            <i class="mdi mdi-clock-alert-outline me-1 text-warning"></i>
                            Registros Pendientes de Sincronización
                            <span class="badge bg-warning ms-2" id="contadorVisibles"><?php echo count($registros_pendientes); ?></span>
                        </h4>
                        <div>
                            <button type="button" class="btn btn-success" id="btnExportarCSV" <?php echo (count($registros_pendientes) == 0) ? 'disabled' : ''; ?>>
                                <i class="mdi mdi-file-delimited me-1"></i>
                                Exportar CSV
                            </button>
                            <button type="button" class="btn btn-primary" id="btnMarcarSincronizados" <?php echo (count($registros_pendientes) == 0) ? 'disabled' : ''; ?>>
                                <i class="mdi mdi-check-all me-1"></i>
                                Sincronizar con ProjectDashboard
                            </button>
                        </div>
                    </div>

                    <?php if (count($registros_pendientes) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover mb-0" id="tablaPendientes">
                            <thead class="table-light">
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th>Proyecto #</th>
                                    <th>Fecha</th>
                                    <th>Nombre Empleado</th>
                                    <th>Cargo</th>
                                    <th>Área de Trabajo</th>
                                    <th class="text-end">Tiempo Ordinario (hrs)</th>
                                    <th class="text-end">Tiempo Extra (hrs)</th>
                                    <th class="text-end">Total Pagado ($)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($registros_pendientes as $registro): ?>
                                <tr data-registro='<?php echo json_encode($registro); ?>'>
                                    <td><input type="checkbox" class="check-registro"></td>
                                    <td class="op-cell" data-op-id="<?php echo $registro['orden_produccion_id']; ?>"><?php echo htmlspecialchars($registro['proyecto_numero']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($registro['fecha'])); ?></td>
                                    <td><?php echo htmlspecialchars($registro['nombre_empleado']); ?></td>
                                    <td><?php echo htmlspecialchars($registro['cargo']); ?></td>
                                    <td><?php echo htmlspecialchars($registro['area_trabajo']); ?></td>
                                    <td class="text-end"><?php echo str_replace('.', ',', number_format($registro['tiempo_ordinario'], 1)); ?></td>
                                    <td class="text-end"><?php echo str_replace('.', ',', number_format($registro['tiempo_extra'], 1)); ?></td>
                                    <td class="text-end fw-bold">$<?php echo number_format($registro['total_pagado']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr id="filaTotales">
                                    <th colspan="6" class="text-end">TOTALES:</th>
                                    <th class="text-end" id="totalOrdinario">
                                        <?php echo str_replace('.', ',', number_format(array_sum(array_column($registros_pendientes, 'tiempo_ordinario')), 1)); ?>
                                    </th>
                                    <th class="text-end" id="totalExtra">
                                        <?php echo str_replace('.', ',', number_format(array_sum(array_column($registros_pendientes, 'tiempo_extra')), 1)); ?>
                                    </th>
                                    <th class="text-end fw-bold" id="totalPagado">
                                        $<?php echo number_format(array_sum(array_column($registros_pendientes, 'total_pagado'))); ?>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info mb-0">
                        <i class="mdi mdi-information-outline me-1"></i>
                        No hay registros pendientes de sincronización en el período seleccionado.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Horas de Producción Pendientes -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title">
                            <i class="mdi mdi-factory me-1 text-secondary"></i>
                            Horas de Producción Pendientes de Sincronización
                            <span class="badge bg-secondary ms-2" id="contadorProduccion"><?php echo count($horas_produccion_pendientes); ?></span>
                        </h4>
                        <div>
                            <button type="button" class="btn btn-success" id="btnExportarProduccionCSV" <?php echo (count($horas_produccion_pendientes) == 0) ? 'disabled' : ''; ?>>
                                <i class="mdi mdi-file-delimited me-1"></i>
                                Exportar CSV
                            </button>
                            <button type="button" class="btn btn-primary" id="btnSincronizarProduccion" <?php echo (count($horas_produccion_pendientes) == 0) ? 'disabled' : ''; ?>>
                                <i class="mdi mdi-check-all me-1"></i>
                                Sincronizar Producción
                            </button>
                        </div>
                    </div>

                    <?php if (count($horas_produccion_pendientes) > 0): ?>
                    <div class="alert alert-info">
                        <i class="mdi mdi-information-outline me-1"></i>
                        <strong>Registros de Planta:</strong> Estas horas fueron registradas por el jefe de producción. 
                        Los costos se calculan automáticamente según los tipos de horas trabajadas.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover mb-0" id="tablaProduccion">
                            <thead class="table-light">
                                <tr>
                                    <th><input type="checkbox" id="selectAllProduccion"></th>
                                    <th>Fecha</th>
                                    <th>Proyecto #</th>
                                    <th>Trabajador</th>
                                    <th>Cargo</th>
                                    <th>Área</th>
                                    <th class="text-center">Total Horas</th>
                                    <th>Horario Trabajo</th>
                                    <th class="text-end">Costo Total ($)</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $costos_class = new Costos();
                                $total_horas_produccion = 0;
                                $total_costo_produccion = 0;
                                
                                foreach ($horas_produccion_pendientes as $prod): 
                                    // Calcular costo total basado en los tipos de horas
                                    $costo_total = 0;
                                    
                                    // HR - Horas Regulares (normales)
                                    if ($prod['hr'] > 0) {
                                        $costo_hr = $costos_class->calcularCostoHorasNormales(
                                            $prod['usuario_id'],
                                            floatval($prod['hr']),
                                            $prod['fecha']
                                        );
                                        $costo_total += $costo_hr['costo_total'];
                                    }
                                    
                                    // HED - Horas Extra Diurna
                                    if ($prod['hed'] > 0) {
                                        $costo_hed = $costos_class->calcularCostoHorasExtras(
                                            $prod['usuario_id'],
                                            floatval($prod['hed']),
                                            '08:00', // Hora diurna
                                            '17:00',
                                            $prod['fecha']
                                        );
                                        $costo_total += $costo_hed['costo_total'];
                                    }
                                    
                                    // HEN - Horas Extra Nocturna
                                    if ($prod['hen'] > 0) {
                                        $costo_hen = $costos_class->calcularCostoHorasExtras(
                                            $prod['usuario_id'],
                                            floatval($prod['hen']),
                                            '22:00', // Hora nocturna
                                            '06:00',
                                            $prod['fecha']
                                        );
                                        $costo_total += $costo_hen['costo_total'];
                                    }
                                    
                                    // HEFD - Horas Extra Festiva Diurna
                                    if ($prod['hefd'] > 0) {
                                        $costo_hefd = $costos_class->calcularCostoHorasExtras(
                                            $prod['usuario_id'],
                                            floatval($prod['hefd']),
                                            '08:00',
                                            '17:00',
                                            $prod['fecha']
                                        );
                                        $costo_total += $costo_hefd['costo_total'];
                                    }
                                    
                                    // HEFN - Horas Extra Festiva Nocturna
                                    if ($prod['hefn'] > 0) {
                                        $costo_hefn = $costos_class->calcularCostoHorasExtras(
                                            $prod['usuario_id'],
                                            floatval($prod['hefn']),
                                            '22:00',
                                            '06:00',
                                            $prod['fecha']
                                        );
                                        $costo_total += $costo_hefn['costo_total'];
                                    }
                                    
                                    $total_horas_produccion += floatval($prod['total_horas']);
                                    $total_costo_produccion += $costo_total;
                                ?>
                                <tr data-registro-produccion='<?php echo json_encode(array_merge($prod, ['costo_calculado' => $costo_total])); ?>'>
                                    <td><input type="checkbox" class="check-produccion"></td>
                                    <td><?php echo date('d/m/Y', strtotime($prod['fecha'])); ?></td>
                                    <td><strong><?php echo htmlspecialchars($prod['proyecto_numero']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($prod['nombre_completo']); ?></td>
                                    <td><?php echo htmlspecialchars($prod['cargo'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($prod['departamento'] ?? 'N/A'); ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-primary fs-6">
                                            <?php echo number_format($prod['total_horas'], 1); ?> hrs
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($prod['horario'] ?? 'N/A'); ?></td>
                                    <td class="text-end fw-bold">$<?php echo number_format($costo_total, 0); ?></td>
                                    <td>
                                        <?php if (!empty($prod['observaciones'])): ?>
                                            <small><?php echo htmlspecialchars($prod['observaciones']); ?></small>
                                        <?php else: ?>
                                            <small class="text-muted">-</small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr id="filaTotalesProduccion">
                                    <th colspan="6" class="text-end">TOTALES:</th>
                                    <th class="text-center" id="totalHorasProduccion">
                                        <span class="badge bg-success fs-6">
                                            <?php echo number_format($total_horas_produccion, 1); ?> hrs
                                        </span>
                                    </th>
                                    <th></th>
                                    <th class="text-end fw-bold fs-5" id="totalCostoProduccion">
                                        $<?php echo number_format($total_costo_produccion, 0); ?>
                                    </th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="mt-3">
                        <div class="alert alert-warning">
                            <i class="mdi mdi-alert me-1"></i>
                            <strong>Nota:</strong> Los costos se calculan automáticamente según:
                            <ul class="mb-0 mt-2">
                                <li><strong>HR:</strong> Horas regulares/normales</li>
                                <li><strong>HED:</strong> Horas extra diurnas (factor <?php echo $Config_class->obtenerValor('factor_extra_diurna', 1.25); ?>x)</li>
                                <li><strong>HEN:</strong> Horas extra nocturnas (factor <?php echo $Config_class->obtenerValor('factor_extra_nocturna', 1.75); ?>x)</li>
                                <li><strong>HEFD:</strong> Horas extra festivas diurnas (factor <?php echo $Config_class->obtenerValor('factor_dominical', 1.75); ?>x)</li>
                                <li><strong>HEFN:</strong> Horas extra festivas nocturnas (factor <?php echo $Config_class->obtenerValor('recargo_nocturno_dominical', 2.1); ?>x)</li>
                            </ul>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info mb-0">
                        <i class="mdi mdi-information-outline me-1"></i>
                        No hay horas de producción pendientes de sincronización en el período seleccionado.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Historial de Sincronizaciones -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">
                        <i class="mdi mdi-history me-1"></i>
                        Historial de Sincronizaciones
                    </h4>

                    <?php if (count($historial) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0" id="tablaHistorial">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha Sincronización</th>
                                    <th>Tipo</th>
                                    <th>Usuario</th>
                                    <th>Proyecto</th>
                                    <th>Fecha Registro</th>
                                    <th class="text-end">Hrs Ordinarias</th>
                                    <th class="text-end">Hrs Extras</th>
                                    <th class="text-end">Total Pagado ($)</th>
                                    <th>Sincronizado Por</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($historial as $item): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i', strtotime($item['fecha_sincronizacion'])); ?></td>
                                    <td>
                                        <?php if ($item['tipo_registro'] == 'horas_normales'): ?>
                                            <span class="badge bg-primary">Normales</span>
                                        <?php else: ?>
                                            <span class="badge bg-info">Extras</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($item['usuario_nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($item['proyecto_numero']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($item['fecha_registro'])); ?></td>
                                    <td class="text-end"><?php echo number_format($item['horas_ordinarias'], 1); ?></td>
                                    <td class="text-end"><?php echo number_format($item['horas_extras'], 1); ?></td>
                                    <td class="text-end fw-bold">$<?php echo number_format($item['total_pagado']); ?></td>
                                    <td><?php echo htmlspecialchars($item['sincronizado_por_nombre']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info mb-0">
                        <i class="mdi mdi-information-outline me-1"></i>
                        No hay historial de sincronizaciones en el período seleccionado.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Historial de Sincronizaciones - Producción -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">
                        <i class="mdi mdi-history me-1 text-secondary"></i>
                        Historial de Sincronizaciones - Horas de Producción
                    </h4>

                    <?php if (count($historial_produccion) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0" id="tablaHistorialProduccion">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha Sincronización</th>
                                    <th>Usuario</th>
                                    <th>Cargo</th>
                                    <th>Proyecto</th>
                                    <th>Fecha Registro</th>
                                    <th class="text-end">Hrs Ordinarias</th>
                                    <th class="text-end">Hrs Extras</th>
                                    <th class="text-end">Total Pagado ($)</th>
                                    <th>Horario</th>
                                    <th>Sincronizado Por</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($historial_produccion as $item): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i', strtotime($item['fecha_sincronizacion'])); ?></td>
                                    <td><?php echo htmlspecialchars($item['usuario_nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($item['cargo'] ?? 'N/A'); ?></td>
                                    <td><strong><?php echo htmlspecialchars($item['proyecto_numero']); ?></strong></td>
                                    <td><?php echo date('d/m/Y', strtotime($item['fecha_registro'])); ?></td>
                                    <td class="text-end"><?php echo number_format($item['horas_ordinarias'], 1); ?></td>
                                    <td class="text-end"><?php echo number_format($item['horas_extras'], 1); ?></td>
                                    <td class="text-end fw-bold">$<?php echo number_format($item['total_pagado']); ?></td>
                                    <td><?php echo htmlspecialchars($item['horario'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($item['sincronizado_por_nombre']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info mb-0">
                        <i class="mdi mdi-information-outline me-1"></i>
                        No hay historial de sincronizaciones de producción en el período seleccionado.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
                </div>
                <!-- container -->

            </div>
            <!-- content -->

            <?php include("includes/footer.php"); ?>

        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <!-- Vendor js -->
    <script src="assets/js/vendor.min.js"></script>

    <!-- App js -->
    <script src="assets/js/app.min.js"></script>

    <!-- DataTables -->
    <script src="assets/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="assets/vendor/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/vendor/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
let tablaPendientes;
let tablaProduccion;

$(document).ready(function() {
    // DataTables - inicializar cada tabla por separado
    if ($.fn.DataTable) {
        // Tabla de pendientes con búsqueda personalizada
        if ($('#tablaPendientes').length) {
            // Función de filtro personalizada para OPs
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    if (settings.nTable.id !== 'tablaPendientes') {
                        return true;
                    }
                    
                    // Obtener la fila original (antes de que DataTables la procese)
                    const row = settings.aoData[dataIndex].nTr;
                    const opId = $(row).find('.op-cell').data('op-id');
                    
                    // Verificar si la OP está seleccionada (checked = mostrar, unchecked = ocultar)
                    const opCheckbox = $(`.op-filter[data-op-id="${opId}"]`);
                    
                    // Si no existe el checkbox o está marcado, mostrar la fila
                    if (opCheckbox.length === 0) {
                        return true;
                    }
                    
                    return opCheckbox.prop('checked');
                }
            );
            
            tablaPendientes = $('#tablaPendientes').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                order: [[2, 'desc']],
                pageLength: 25,
                responsive: true,
                footerCallback: function(row, data, start, end, display) {
                    let api = this.api();
                    
                    // Calcular totales de las filas visibles
                    let totalOrdinario = 0;
                    let totalExtra = 0;
                    let totalPagado = 0;
                    
                    api.rows({search: 'applied'}).every(function() {
                        const registro = JSON.parse($(this.node()).attr('data-registro'));
                        totalOrdinario += parseFloat(registro.tiempo_ordinario) || 0;
                        totalExtra += parseFloat(registro.tiempo_extra) || 0;
                        totalPagado += parseFloat(registro.total_pagado) || 0;
                    });
                    
                    // Actualizar totales en footer
                    $('#totalOrdinario').html(totalOrdinario.toFixed(1).replace('.', ','));
                    $('#totalExtra').html(totalExtra.toFixed(1).replace('.', ','));
                    $('#totalPagado').html('$' + Math.round(totalPagado).toLocaleString('es-CL'));
                    
                    // Actualizar contador
                    $('#contadorVisibles').text(display.length);
                    
                    // Habilitar/deshabilitar botones
                    const hayVisibles = display.length > 0;
                    $('#btnExportarCSV, #btnMarcarSincronizados').prop('disabled', !hayVisibles);
                }
            });
        }
        
        // Tabla de horas de producción
        if ($('#tablaProduccion').length) {
            tablaProduccion = $('#tablaProduccion').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                order: [[1, 'desc']], // Ordenar por fecha descendente (columna 1 ahora por el checkbox)
                pageLength: 25,
                responsive: true,
                footerCallback: function(row, data, start, end, display) {
                    let api = this.api();
                    
                    // Calcular totales de las filas visibles
                    let totalHoras = 0;
                    let totalCosto = 0;
                    
                    api.rows({search: 'applied'}).every(function() {
                        const registro = JSON.parse($(this.node()).attr('data-registro-produccion'));
                        totalHoras += parseFloat(registro.total_horas) || 0;
                        totalCosto += parseFloat(registro.costo_calculado) || 0;
                    });
                    
                    // Actualizar totales en footer
                    $('#totalHorasProduccion').html(
                        '<span class="badge bg-success fs-6">' + 
                        totalHoras.toFixed(1) + ' hrs</span>'
                    );
                    $('#totalCostoProduccion').html('$' + Math.round(totalCosto).toLocaleString('es-CL'));
                    
                    // Actualizar contador
                    $('#contadorProduccion').text(display.length);
                    
                    // Habilitar/deshabilitar botones
                    const hayVisibles = display.length > 0;
                    $('#btnExportarProduccionCSV, #btnSincronizarProduccion').prop('disabled', !hayVisibles);
                }
            });
        }
        
        // Tabla de historial
        if ($('#tablaHistorial').length) {
            $('#tablaHistorial').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                order: [[0, 'desc']],
                pageLength: 25,
                responsive: true
            });
        }
        
        // Tabla de historial de producción
        if ($('#tablaHistorialProduccion').length) {
            $('#tablaHistorialProduccion').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                order: [[0, 'desc']],
                pageLength: 25,
                responsive: true
            });
        }
    }

    // Select all - Registros normales
    $('#selectAll').on('change', function() {
        const isChecked = $(this).prop('checked');
        tablaPendientes.rows({search: 'applied'}).every(function() {
            $(this.node()).find('.check-registro').prop('checked', isChecked);
        });
    });

    // Select all - Producción
    $('#selectAllProduccion').on('change', function() {
        const isChecked = $(this).prop('checked');
        if (tablaProduccion) {
            tablaProduccion.rows({search: 'applied'}).every(function() {
                $(this.node()).find('.check-produccion').prop('checked', isChecked);
            });
        }
    });

    // Filtro de OPs
    $('.op-filter').on('change', function() {
        if (tablaPendientes) {
            tablaPendientes.draw();
        }
    });

    $('#btnSeleccionarTodas').on('click', function() {
        $('.op-filter').prop('checked', true);
        if (tablaPendientes) {
            tablaPendientes.draw();
        }
    });

    $('#btnDeseleccionarTodas').on('click', function() {
        $('.op-filter').prop('checked', false);
        if (tablaPendientes) {
            tablaPendientes.draw();
        }
    });

    // Exportar CSV
    $('#btnExportarCSV').on('click', function() {
        exportarCSV();
    });

    // Marcar como sincronizados
    $('#btnMarcarSincronizados').on('click', function() {
        marcarComoSincronizados();
    });

    // Exportar CSV - Producción
    $('#btnExportarProduccionCSV').on('click', function() {
        exportarProduccionCSV();
    });

    // Sincronizar Producción
    $('#btnSincronizarProduccion').on('click', function() {
        sincronizarProduccion();
    });
});

function exportarCSV() {
    const registros = [];
    
    // Obtener solo checkboxes marcados de filas visibles en DataTable
    tablaPendientes.rows({search: 'applied'}).every(function() {
        const checkbox = $(this.node()).find('.check-registro');
        if (checkbox.is(':checked')) {
            const registro = JSON.parse($(this.node()).attr('data-registro'));
            registros.push(registro);
        }
    });

    if (registros.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Selección requerida',
            text: 'Debe seleccionar al menos un registro visible para exportar.'
        });
        return;
    }

    // Crear CSV
    let csv = 'Marca temporal,Proyecto #,Fecha,Nombre Empleado,Cargo,Área de Trabajo,Tiempo Ordinario,Tiempo Extra,Total pagado ($)\n';
    
    const timestamp = new Date().toLocaleString('es-CL');
    
    registros.forEach(registro => {
        const fecha = new Date(registro.fecha + 'T00:00:00').toLocaleDateString('es-CL');
        const tiempoOrdinario = registro.tiempo_ordinario.toFixed(1).replace('.', ',');
        const tiempoExtra = registro.tiempo_extra.toFixed(1).replace('.', ',');
        
        csv += `"${timestamp}",`;
        csv += `"${registro.proyecto_numero}",`;
        csv += `"${fecha}",`;
        csv += `"${registro.nombre_empleado}",`;
        csv += `"${registro.cargo}",`;
        csv += `"${registro.area_trabajo}",`;
        csv += `"${tiempoOrdinario}",`;
        csv += `"${tiempoExtra}",`;
        csv += `"${Math.round(registro.total_pagado)}"\n`;
    });

    // Descargar archivo
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `sincronizacion_projectdashboard_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    Swal.fire({
        icon: 'success',
        title: 'Exportación exitosa',
        text: `Se exportaron ${registros.length} registros.`,
        showConfirmButton: false,
        timer: 2000
    });
}

function marcarComoSincronizados() {
    const registros = [];
    
    // Obtener solo checkboxes marcados de filas visibles en DataTable
    tablaPendientes.rows({search: 'applied'}).every(function() {
        const checkbox = $(this.node()).find('.check-registro');
        if (checkbox.is(':checked')) {
            const registro = JSON.parse($(this.node()).attr('data-registro'));
            registros.push(registro);
        }
    });

    if (registros.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Selección requerida',
            text: 'Debe seleccionar al menos un registro visible para sincronizar.'
        });
        return;
    }

    Swal.fire({
        title: '¿Confirmar sincronización?',
        text: `Se enviarán ${registros.length} registros a ProjectDashboard y se marcarán como sincronizados. Esta acción no se puede deshacer.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, sincronizar ahora',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Enviando y sincronizando...',
                html: 'Por favor espere mientras se procesan los registros.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: 'action/enviar-webhook.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ registros: registros }),
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sincronización completada',
                            html: `
                                <p><strong>Registros enviados:</strong> ${response.enviados}</p>
                                <p><strong>Registros sincronizados:</strong> ${response.sincronizados}</p>
                                <p><small>Código HTTP: ${response.http_code}</small></p>
                            `,
                            showConfirmButton: true
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al sincronizar',
                            html: `
                                <p>${response.message}</p>
                                ${response.http_code ? `<p><small>Código HTTP: ${response.http_code}</small></p>` : ''}
                            `,
                            confirmButtonText: 'Entendido'
                        });
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Error de comunicación con el servidor.';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMsg = response.message || errorMsg;
                    } catch (e) {
                        // Mantener mensaje por defecto
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMsg
                    });
                }
            });
        }
    });
}

function exportarProduccionCSV() {
    const registros = [];
    
    // Obtener solo checkboxes marcados de filas visibles en DataTable
    tablaProduccion.rows({search: 'applied'}).every(function() {
        const checkbox = $(this.node()).find('.check-produccion');
        if (checkbox.is(':checked')) {
            const registro = JSON.parse($(this.node()).attr('data-registro-produccion'));
            registros.push(registro);
        }
    });

    if (registros.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Selección requerida',
            text: 'Debe seleccionar al menos un registro de producción visible para exportar.'
        });
        return;
    }

    // Crear CSV
    let csv = 'Marca temporal,Proyecto #,Fecha,Trabajador,Cargo,Área,Total Horas,HR,HED,HEN,HEFD,HEFN,Horario,Máquina,Costo Total,Observaciones\n';
    
    const timestamp = new Date().toLocaleString('es-CL');
    
    registros.forEach(registro => {
        const fecha = new Date(registro.fecha + 'T00:00:00').toLocaleDateString('es-CL');
        
        csv += `"${timestamp}",`;
        csv += `"${registro.proyecto_numero}",`;
        csv += `"${fecha}",`;
        csv += `"${registro.nombre_completo}",`;
        csv += `"${registro.cargo || 'N/A'}",`;
        csv += `"${registro.departamento || 'N/A'}",`;
        csv += `"${registro.total_horas}",`;
        csv += `"${registro.hr || 0}",`;
        csv += `"${registro.hed || 0}",`;
        csv += `"${registro.hen || 0}",`;
        csv += `"${registro.hefd || 0}",`;
        csv += `"${registro.hefn || 0}",`;
        csv += `"${registro.horario || 'N/A'}",`;
        csv += `"${registro.maquina || 'N/A'}",`;
        csv += `"${Math.round(registro.costo_calculado)}",`;
        csv += `"${(registro.observaciones || '').replace(/"/g, '""')}"\n`;
    });

    // Descargar archivo
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `produccion_projectdashboard_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    Swal.fire({
        icon: 'success',
        title: 'Exportación exitosa',
        text: `Se exportaron ${registros.length} registros de producción.`,
        showConfirmButton: false,
        timer: 2000
    });
}

function sincronizarProduccion() {
    const registros = [];
    
    // Obtener solo checkboxes marcados de filas visibles en DataTable
    tablaProduccion.rows({search: 'applied'}).every(function() {
        const checkbox = $(this.node()).find('.check-produccion');
        if (checkbox.is(':checked')) {
            const registro = JSON.parse($(this.node()).attr('data-registro-produccion'));
            registros.push(registro);
        }
    });

    if (registros.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Selección requerida',
            text: 'Debe seleccionar al menos un registro de producción visible para sincronizar.'
        });
        return;
    }

    Swal.fire({
        title: '¿Confirmar sincronización de producción?',
        text: `Se enviarán ${registros.length} registros de producción a ProjectDashboard y se marcarán como sincronizados. Esta acción no se puede deshacer.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, sincronizar ahora',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Enviando y sincronizando...',
                html: 'Por favor espere mientras se procesan los registros de producción.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: 'action/enviar-webhook.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ 
                    registros: registros,
                    tipo: 'horas_produccion'
                }),
                success: function(response) {
                    if (response.success) {
                        let mensajeHTML = `
                            <p><strong>Registros enviados:</strong> ${response.enviados}</p>
                            <p><strong>Registros sincronizados:</strong> ${response.sincronizados}</p>
                            <p><small>Tipo: ${response.tipo_registro || 'N/A'}</small></p>
                            <p><small>Código HTTP: ${response.http_code}</small></p>
                        `;
                        
                        if (response.errores_sync && response.errores_sync.length > 0) {
                            mensajeHTML += '<hr><p class="text-warning"><strong>Advertencias:</strong></p><ul class="text-start">';
                            response.errores_sync.forEach(err => {
                                mensajeHTML += `<li><small>${err}</small></li>`;
                            });
                            mensajeHTML += '</ul>';
                        }
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Sincronización completada',
                            html: mensajeHTML,
                            showConfirmButton: true
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al sincronizar',
                            html: `
                                <p>${response.message}</p>
                                ${response.http_code ? `<p><small>Código HTTP: ${response.http_code}</small></p>` : ''}
                            `,
                            confirmButtonText: 'Entendido'
                        });
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Error de comunicación con el servidor.';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMsg = response.message || errorMsg;
                    } catch (e) {
                        // Mantener mensaje por defecto
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMsg
                    });
                }
            });
        }
    });
}
</script>

</body>
</html>
