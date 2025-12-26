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

// Obtener historial reciente
$historial = $Sincronizacion_class->obtenerHistorialSincronizacion($filtros, 50);

// Obtener lista de usuarios para filtro
$Usuario_class_list = new Usuario();
$usuarios = $Usuario_class_list->obtenerUsuarios(['rol' => 'trabajador']);

$pageTitle = "Sincronización ProjectDashboard";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title><?php echo $pageTitle; ?> | Sistema de Horas</title>
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
        <div class="col-md-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-sync widget-icon bg-primary text-white"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0">Total Sincronizados</h5>
                    <h3 class="mt-3 mb-3"><?php echo number_format($estadisticas['total_sincronizados']); ?></h3>
                    <p class="mb-0 text-muted">
                        <span class="text-nowrap">Todos los registros</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-clock-outline widget-icon bg-warning text-white"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0">Horas Normales Pendientes</h5>
                    <h3 class="mt-3 mb-3"><?php echo number_format($estadisticas['pendientes_normales']); ?></h3>
                    <p class="mb-0 text-muted">
                        <span class="text-nowrap">Registros sin sincronizar</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-clock-plus-outline widget-icon bg-info text-white"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0">Horas Extras Pendientes</h5>
                    <h3 class="mt-3 mb-3"><?php echo number_format($estadisticas['pendientes_extras']); ?></h3>
                    <p class="mb-0 text-muted">
                        <span class="text-nowrap">Solicitudes aprobadas</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-calendar-clock widget-icon bg-success text-white"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0">Última Sincronización</h5>
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
                        <span class="text-nowrap">
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
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
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
        
        // Tabla de historial
        if ($('#tablaHistorial').length) {
            $('#tablaHistorial').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                order: [[0, 'desc']],
                pageLength: 25,
                responsive: true
            });
        }
    }

    // Select all
    $('#selectAll').on('change', function() {
        const isChecked = $(this).prop('checked');
        tablaPendientes.rows({search: 'applied'}).every(function() {
            $(this.node()).find('.check-registro').prop('checked', isChecked);
        });
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
</script>

</body>
</html>
