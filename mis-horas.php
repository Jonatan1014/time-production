<?php
// mis-horas.php - Vista de horas trabajadas del usuario
session_start();
require_once 'includes/Class-usuario.php';
require_once 'includes/Class-registros-horas.php';
require_once 'includes/Class-horas-extras.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['user_id'];
$RegistroHoras_class = new RegistroHoras();
$HoraExtra_class = new HoraExtra();

// Obtener período (por defecto último mes)
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d');
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-d', strtotime('-30 days'));

// Obtener registros
$registros = $RegistroHoras_class->obtenerRegistrosPorUsuario($usuario_id, $fecha_inicio, $fecha_fin);
$total_horas = $RegistroHoras_class->obtenerTotalHorasPorUsuario($usuario_id, $fecha_inicio, $fecha_fin);
$horas_extras = $HoraExtra_class->obtenerHorasExtrasPorUsuario($usuario_id, $fecha_inicio, $fecha_fin);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Mis Horas | Time Production</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="assets/images/favicon.ico" />
    <script src="assets/js/hyper-config.js"></script>
    <link href="assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="wrapper">
        <?php include("includes/header.php"); ?>
        <?php include("includes/sidebar.php"); ?>

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <a href="registrar-horas.php" class="btn btn-primary">
                                        <i class="ri-add-circle-line"></i> Registrar Horas
                                    </a>
                                    <a href="solicitar-horas-extras.php" class="btn btn-warning">
                                        <i class="ri-timer-flash-line"></i> Solicitar Horas Extras
                                    </a>
                                </div>
                                <h4 class="page-title">Mis Horas de Trabajo</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <form method="GET" action="" class="row g-3">
                                        <div class="col-md-4">
                                            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                                                   value="<?php echo $fecha_inicio; ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="fecha_fin" class="form-label">Fecha Fin</label>
                                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                                                   value="<?php echo $fecha_fin; ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">&nbsp;</label>
                                            <div>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="ri-search-line"></i> Filtrar
                                                </button>
                                                <a href="mis-horas.php" class="btn btn-secondary">
                                                    <i class="ri-refresh-line"></i> Limpiar
                                                </a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="ri-time-line widget-icon bg-primary-lighten text-primary"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0" title="Horas Normales">Horas Normales</h5>
                                    <h3 class="mt-3 mb-3 text-primary"><?php echo number_format($total_horas['total_horas'], 2); ?> hrs</h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-nowrap"><i class="ri-file-list-line"></i> <?php echo $total_horas['total_registros']; ?> registros</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="ri-timer-flash-line widget-icon bg-warning-lighten text-warning"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0" title="Horas Extras">Horas Extras</h5>
                                    <h3 class="mt-3 mb-3 text-warning"><?php echo number_format($horas_extras['horas_aprobadas'], 2); ?> hrs</h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-success me-2"><i class="ri-checkbox-circle-line"></i> <?php echo $horas_extras['solicitudes_aprobadas']; ?> aprobadas</span>
                                        <span class="text-warning"><i class="ri-time-line"></i> <?php echo $horas_extras['solicitudes_pendientes']; ?> pendientes</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="ri-calendar-check-line widget-icon bg-success-lighten text-success"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0" title="Total General">Total General</h5>
                                    <h3 class="mt-3 mb-3 text-success">
                                        <?php echo number_format($total_horas['total_horas'] + $horas_extras['horas_aprobadas'], 2); ?> hrs
                                    </h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-nowrap"><i class="ri-calendar-event-line"></i> <?php echo date('d/m/Y', strtotime($fecha_inicio)); ?> - <?php echo date('d/m/Y', strtotime($fecha_fin)); ?></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Horas Normales -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">
                                        <i class="ri-time-line text-primary"></i> Horas Normales de Trabajo
                                        <span class="badge badge-primary-lighten fs-6 ms-2">
                                            Total: <?php echo number_format($total_horas['total_horas'], 2); ?> hrs
                                        </span>
                                    </h4>
                                    
                                    <?php if (count($registros) > 0): ?>
                                    <div class="table-responsive">
                                        <table id="tabla-horas-normales" class="table table-striped table-hover dt-responsive nowrap w-100">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Orden Producción</th>
                                                    <th>Cliente</th>
                                                    <th>Horas</th>
                                                    <th>Descripción</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($registros as $registro): ?>
                                                <tr>
                                                    <td>
                                                        <i class="ri-calendar-event-line text-muted"></i>
                                                        <?php echo date('d/m/Y', strtotime($registro['fecha'])); ?>
                                                    </td>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($registro['codigo_op']); ?></strong><br>
                                                        <small class="text-muted"><?php echo htmlspecialchars($registro['nombre_producto']); ?></small>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($registro['cliente']); ?></td>
                                                    <td>
                                                        <span class="badge badge-primary-lighten fs-6">
                                                            <i class="ri-time-line"></i> <?php echo number_format($registro['horas_trabajadas'], 1); ?> hrs
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="text-truncate d-inline-block" style="max-width: 200px;" 
                                                              title="<?php echo htmlspecialchars($registro['descripcion_trabajo']); ?>">
                                                            <?php echo htmlspecialchars($registro['descripcion_trabajo']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $estado_class = match($registro['estado']) {
                                                            'registrado' => 'info',
                                                            'validado' => 'success',
                                                            'rechazado' => 'danger',
                                                            default => 'secondary'
                                                        };
                                                        ?>
                                                        <span class="badge badge-<?php echo $estado_class; ?>-lighten">
                                                            <?php echo ucfirst($registro['estado']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="detalle-registro.php?id=<?php echo $registro['id']; ?>" 
                                                           class="btn btn-sm btn-info" title="Ver detalle">
                                                            <i class="ri-eye-line"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php else: ?>
                                    <div class="alert alert-info border-info">
                                        <i class="ri-information-line"></i>
                                        No hay registros de horas normales en el período seleccionado.
                                        <a href="registrar-horas.php" class="alert-link">Registrar ahora</a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Horas Extras -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">
                                        <i class="ri-timer-flash-line text-warning"></i> Horas Extras
                                        <span class="badge badge-warning-lighten fs-6 ms-2">
                                            Total Aprobadas: <?php echo number_format($horas_extras['horas_aprobadas'], 2); ?> hrs
                                        </span>
                                        <?php if ($horas_extras['solicitudes_pendientes'] > 0): ?>
                                        <span class="badge badge-info-lighten fs-6 ms-1">
                                            <i class="ri-time-line"></i> <?php echo $horas_extras['solicitudes_pendientes']; ?> Pendientes
                                        </span>
                                        <?php endif; ?>
                                    </h4>
                                    
                                    <?php 
                                    $solicitudes_extras = $HoraExtra_class->obtenerSolicitudesPorUsuario($usuario_id);
                                    // Filtrar por fecha
                                    $solicitudes_extras_filtradas = array_filter($solicitudes_extras, function($sol) use ($fecha_inicio, $fecha_fin) {
                                        return $sol['fecha'] >= $fecha_inicio && $sol['fecha'] <= $fecha_fin;
                                    });
                                    ?>
                                    
                                    <?php if (count($solicitudes_extras_filtradas) > 0): ?>
                                    <div class="table-responsive">
                                        <table id="tabla-horas-extras" class="table table-striped table-hover dt-responsive nowrap w-100">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Orden Producción</th>
                                                    <th>Horario</th>
                                                    <th>Horas</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($solicitudes_extras_filtradas as $extra): ?>
                                                <tr>
                                                    <td>
                                                        <i class="ri-calendar-event-line text-muted"></i>
                                                        <?php echo date('d/m/Y', strtotime($extra['fecha'])); ?>
                                                    </td>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($extra['codigo_op']); ?></strong><br>
                                                        <small class="text-muted"><?php echo htmlspecialchars($extra['nombre_producto']); ?></small>
                                                    </td>
                                                    <td>
                                                        <?php if (isset($extra['hora_inicio']) && isset($extra['hora_fin'])): ?>
                                                            <i class="ri-time-line text-info"></i>
                                                            <?php echo date('H:i', strtotime($extra['hora_inicio'])); ?>
                                                            <i class="ri-arrow-right-s-line"></i>
                                                            <?php echo date('H:i', strtotime($extra['hora_fin'])); ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                        $horas_extra_valor = isset($extra['total_horas_extras']) ? $extra['total_horas_extras'] : (isset($extra['horas_extras']) ? $extra['horas_extras'] : 0);
                                                        ?>
                                                        <span class="badge badge-warning-lighten fs-6">
                                                            <i class="ri-timer-flash-line"></i> <?php echo number_format($horas_extra_valor, 2); ?> hrs
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $estado_class_extra = match($extra['estado']) {
                                                            'pendiente' => 'warning',
                                                            'aprobada' => 'success',
                                                            'rechazada' => 'danger',
                                                            'cancelada' => 'secondary',
                                                            default => 'secondary'
                                                        };
                                                        ?>
                                                        <span class="badge badge-<?php echo $estado_class_extra; ?>-lighten">
                                                            <?php echo ucfirst($extra['estado']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="detalle-hora-extra.php?id=<?php echo $extra['id']; ?>" 
                                                           class="btn btn-sm btn-warning" title="Ver detalle">
                                                            <i class="ri-eye-line"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php else: ?>
                                    <div class="alert alert-warning border-warning">
                                        <i class="ri-information-line"></i>
                                        No hay solicitudes de horas extras en el período seleccionado.
                                        <a href="solicitar-horas-extras.php" class="alert-link">Solicitar horas extras</a>
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
    <script src="assets/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>

    <script>
    $(document).ready(function() {
        // Tabla de horas normales
        $('#tabla-horas-normales').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            order: [[0, 'desc']],
            pageLength: 25,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="ri-file-excel-line"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    title: 'Horas Normales'
                },
                {
                    extend: 'pdf',
                    text: '<i class="ri-file-pdf-line"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    title: 'Horas Normales'
                }
            ]
        });
        
        // Tabla de horas extras
        $('#tabla-horas-extras').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            order: [[0, 'desc']],
            pageLength: 25,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="ri-file-excel-line"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    title: 'Horas Extras'
                },
                {
                    extend: 'pdf',
                    text: '<i class="ri-file-pdf-line"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    title: 'Horas Extras'
                }
            ]
        });
    });
    </script>

</body>
</html>
