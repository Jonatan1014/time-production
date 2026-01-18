<?php
// index.php - Dashboard principal
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

$usuario_id = $_SESSION['user_id'];
$es_admin = isset($_SESSION['user_rol']) && ($_SESSION['user_rol'] === 'administrador' || $_SESSION['user_rol'] === 'Administrador');

// Instanciar clases
$RegistroHoras_class = new RegistroHoras();
$OrdenProduccion_class = new OrdenProduccion();
$HoraExtra_class = new HoraExtra();

// Instanciar clase de costos si es admin
if ($es_admin) {
    require_once 'includes/Class-costos.php';
    require_once 'includes/Class-configuracion.php';
    $Costos_class = new Costos();
    $Configuracion_class = new Configuracion();
    $mostrar_costos = $Configuracion_class->obtenerValor('mostrar_costos', 1);
}

// Obtener fechas para el período (último mes)
$fecha_fin = date('Y-m-d');
$fecha_inicio = date('Y-m-d', strtotime('-30 days'));

// Obtener estadísticas
if ($es_admin) {
    $estadisticas_generales = $RegistroHoras_class->obtenerEstadisticasGenerales($fecha_inicio, $fecha_fin);
    $estadisticas_horas_extras = $HoraExtra_class->obtenerEstadisticas($fecha_inicio, $fecha_fin);
    $ordenes = $OrdenProduccion_class->obtenerOrdenes(['estado' => 'activa']);
    
    // Calcular costos si está habilitado
    if ($mostrar_costos) {
        $costos_resumen = $Costos_class->calcularCostosReporte([
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin
        ]);
    }
} else {
    $total_horas_usuario = $RegistroHoras_class->obtenerTotalHorasPorUsuario($usuario_id, $fecha_inicio, $fecha_fin);
    $horas_extras_usuario = $HoraExtra_class->obtenerHorasExtrasPorUsuario($usuario_id, $fecha_inicio, $fecha_fin);
    $registros_recientes = $RegistroHoras_class->obtenerRegistrosPorUsuario($usuario_id, $fecha_inicio, $fecha_fin);
}

$ordenes_activas = $OrdenProduccion_class->obtenerOrdenesActivas();
$resumen_estados = $OrdenProduccion_class->obtenerResumenPorEstado();
?>

<!DOCTYPE html>
<html lang="es">
<!-- Mirrored from coderthemes.com/hyper/saas/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 17 Oct 2023 20:31:50 GMT -->

<head>
    <meta charset="utf-8" />
    <title>Dashboard | Time Production</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta content="Sistema de gestión de tiempos y producción - Talleres Unidos Ltda" name="description" />
    <meta content="Talleres Unidos Ltda" name="author" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico" />

    <!-- Daterangepicker css -->
    <link rel="stylesheet" href="assets/vendor/daterangepicker/daterangepicker.css" />

    <!-- Vector Map css -->
    <link rel="stylesheet" href="assets/vendor/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css" />

    <!-- Theme Config Js -->
    <script src="assets/js/hyper-config.js"></script>

    <!-- App css -->
    <link href="assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />

    <!-- Custom Dashboard CSS -->
    <style>
    .widget-rounded-circle .card-body {
        padding: 1.5rem;
    }

    .widget-rounded-circle .avatar-lg {
        height: 4rem;
        width: 4rem;
    }

    .widget-rounded-circle .avatar-title {
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card {
        box-shadow: 0 0 35px 0 rgba(154, 161, 171, .15);
        margin-bottom: 24px;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(67, 94, 190, 0.05);
    }

    .badge {
        padding: 0.35em 0.65em;
        font-weight: 600;
    }

    @media print {

        .page-title-right,
        .sidebar,
        .topnav,
        .navbar-custom,
        .footer {
            display: none !important;
        }

        .content-page {
            margin-left: 0 !important;
            padding-top: 0 !important;
        }

        .card {
            page-break-inside: avoid;
        }
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
    }

    canvas {
        max-height: 400px;
    }
    </style>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">
        <!-- ========== Topbar Start ========== -->
        <?php
        include("includes/header.php");
        ?>
        <!-- ========== Topbar End ========== -->

        <?php
        include("includes/sidebar.php");
        ?>


        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">
                <!-- Start Content-->
                <div class="container-fluid">

                    <!-- Page Title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <a href="registrar-horas.php" class="btn btn-primary btn-sm">
                                        <i class="ri-add-circle-line"></i> Registrar Horas
                                    </a>
                                    <?php if ($es_admin): ?>
                                    <a href="reporte-horas.php" class="btn btn-success btn-sm">
                                        <i class="ri-file-chart-line"></i> Ver Reportes
                                    </a>
                                    <?php endif; ?>
                                </div>
                                <h4 class="page-title">Dashboard - Talleres Unidos Ltda</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas Principales -->
                    <?php if ($es_admin): ?>
                    <!-- Vista para Administradores -->
                    <div class="row">
                        <div class="col-md-6 col-xl-3">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="ri-user-line widget-icon"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0">Trabajadores Activos</h5>
                                    <h3 class="mt-3 mb-3"><?php echo $estadisticas_generales['total_usuarios']; ?></h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-nowrap">Últimos 30 días</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="ri-time-line widget-icon bg-success-lighten text-success"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0">Total Horas</h5>
                                    <h3 class="mt-3 mb-3"><?php echo number_format($estadisticas_generales['total_horas'], 2); ?></h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-success me-2">
                                            <i class="mdi mdi-arrow-up-bold"></i> <?php echo $estadisticas_generales['total_registros']; ?> registros
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="ri-timer-flash-line widget-icon bg-warning-lighten text-warning"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0">Horas Extras Pendientes</h5>
                                    <h3 class="mt-3 mb-3"><?php echo $estadisticas_horas_extras['pendientes']; ?></h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-nowrap"><?php echo number_format($estadisticas_horas_extras['total_horas_pendientes'], 2); ?> horas</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="ri-file-list-line widget-icon bg-info-lighten text-info"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0">Órdenes Activas</h5>
                                    <h3 class="mt-3 mb-3"><?php echo count($ordenes); ?></h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-nowrap">En producción</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($mostrar_costos && isset($costos_resumen)): ?>
                    <!-- Tarjetas de Costos -->
                    <div class="row">
                        <div class="col-md-6 col-xl-3">
                            <div class="card widget-flat border-primary">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="mdi mdi-currency-usd widget-icon bg-primary-lighten text-primary"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0">Costo Total</h5>
                                    <h3 class="mt-3 mb-3 text-primary">$<?php echo number_format($costos_resumen['costo_total'], 0, ',', '.'); ?></h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-nowrap">Últimos 30 días</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="mdi mdi-clock-outline widget-icon bg-success-lighten text-success"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0">Horas Normales</h5>
                                    <h3 class="mt-3 mb-3">$<?php echo number_format($costos_resumen['costo_horas_normales'], 0, ',', '.'); ?></h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-success me-2">
                                            <?php echo number_format($costos_resumen['horas_normales'], 1); ?> hrs
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="mdi mdi-weather-sunny widget-icon bg-warning-lighten text-warning"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0">Extras Diurnas</h5>
                                    <h3 class="mt-3 mb-3">$<?php echo number_format($costos_resumen['costo_extras_diurnas'], 0, ',', '.'); ?></h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-warning me-2">
                                            <?php echo number_format($costos_resumen['horas_extras_diurnas'], 1); ?> hrs
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="mdi mdi-weather-night widget-icon bg-dark-lighten text-dark"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0">Extras Nocturnas</h5>
                                    <h3 class="mt-3 mb-3">$<?php echo number_format($costos_resumen['costo_extras_nocturnas'], 0, ',', '.'); ?></h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-dark me-2">
                                            <?php echo number_format($costos_resumen['horas_extras_nocturnas'], 1); ?> hrs
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <!-- Vista para Trabajadores -->
                    <div class="row">
                        <div class="col-md-6 col-xl-4">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="ri-time-line widget-icon"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0">Mis Horas Trabajadas</h5>
                                    <h3 class="mt-3 mb-3"><?php echo number_format($total_horas_usuario['total_horas'], 2); ?> hrs</h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-nowrap"><?php echo $total_horas_usuario['total_registros']; ?> registros (últimos 30 días)</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-4">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="ri-timer-flash-line widget-icon bg-warning-lighten text-warning"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0">Mis Horas Extras</h5>
                                    <h3 class="mt-3 mb-3"><?php echo number_format($horas_extras_usuario['horas_aprobadas'], 2); ?> hrs</h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-warning"><?php echo $horas_extras_usuario['solicitudes_pendientes']; ?> pendientes</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-4">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="ri-calendar-check-line widget-icon bg-success-lighten text-success"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0">Total</h5>
                                    <h3 class="mt-3 mb-3">
                                        <?php echo number_format($total_horas_usuario['total_horas'] + $horas_extras_usuario['horas_aprobadas'], 2); ?> hrs
                                    </h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-nowrap">Últimos 30 días</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Órdenes de Producción Activas -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4 class="header-title">Órdenes de Producción Activas</h4>
                                        <a href="ordenes-produccion.php" class="btn btn-sm btn-primary">
                                            Ver todas <i class="ri-arrow-right-line"></i>
                                        </a>
                                    </div>
                                    
                                    <?php if (count($ordenes_activas) > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Código OP</th>
                                                    <th>Producto</th>
                                                    <th>Cliente</th>
                                                    <th>Prioridad</th>
                                                    <th>Fecha Límite</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $limit = $es_admin ? 10 : 5;
                                                $contador = 0;
                                                foreach ($ordenes_activas as $orden): 
                                                    if ($contador >= $limit) break;
                                                    $contador++;
                                                ?>
                                                <tr>
                                                    <td><strong><?php echo htmlspecialchars($orden['codigo_op']); ?></strong></td>
                                                    <td><?php echo htmlspecialchars($orden['nombre_producto']); ?></td>
                                                    <td><?php echo htmlspecialchars($orden['cliente']); ?></td>
                                                    <td>
                                                        <?php
                                                        $prioridad_class = match($orden['prioridad']) {
                                                            'urgente' => 'danger',
                                                            'alta' => 'warning',
                                                            'media' => 'info',
                                                            'baja' => 'secondary',
                                                            default => 'secondary'
                                                        };
                                                        ?>
                                                        <span class="badge bg-<?php echo $prioridad_class; ?>">
                                                            <?php echo ucfirst($orden['prioridad']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">
                                                            <?php echo date('d/m/Y', strtotime($orden['fecha_fin_estimada'] ?? 'now')); ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <a href="registrar-horas.php?orden=<?php echo $orden['id']; ?>" 
                                                           class="btn btn-sm btn-primary" title="Registrar horas">
                                                            <i class="ri-time-line"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php else: ?>
                                    <div class="alert alert-info mb-0">
                                        <i class="ri-information-line"></i>
                                        No hay órdenes de producción activas en este momento.
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Registros Recientes / Resumen de Estados -->
                    <div class="row">
                        <?php if (!$es_admin && isset($registros_recientes)): ?>
                        <!-- Vista de Trabajador: Registros Recientes -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4 class="header-title">Mis Registros Recientes</h4>
                                        <a href="mis-horas.php" class="btn btn-sm btn-primary">
                                            Ver todos <i class="ri-arrow-right-line"></i>
                                        </a>
                                    </div>
                                    
                                    <?php if (count($registros_recientes) > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Orden Producción</th>
                                                    <th>Horas</th>
                                                    <th>Descripción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $count = 0;
                                                foreach ($registros_recientes as $registro): 
                                                    if ($count >= 5) break;
                                                    $count++;
                                                ?>
                                                <tr>
                                                    <td><?php echo date('d/m/Y', strtotime($registro['fecha'])); ?></td>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($registro['codigo_op']); ?></strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-primary">
                                                            <?php echo number_format($registro['horas_trabajadas'], 2); ?> hrs
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="text-truncate d-inline-block" style="max-width: 300px;">
                                                            <?php echo htmlspecialchars($registro['descripcion_trabajo']); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php else: ?>
                                    <div class="alert alert-info mb-0">
                                        <i class="ri-information-line"></i>
                                        No tiene registros de horas en los últimos 30 días.
                                        <a href="registrar-horas.php" class="alert-link">Registrar ahora</a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Resumen de Estados de Órdenes -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">Estado de Órdenes de Producción</h4>
                                    <div class="row">
                                        <?php foreach ($resumen_estados as $estado): 
                                            $config = match($estado['estado']) {
                                                'activa' => ['color' => 'primary', 'icon' => 'ri-play-circle-line', 'label' => 'Activas'],
                                                'en_proceso' => ['color' => 'info', 'icon' => 'ri-time-line', 'label' => 'En Proceso'],
                                                'pausada' => ['color' => 'warning', 'icon' => 'ri-pause-circle-line', 'label' => 'Pausadas'],
                                                'completada' => ['color' => 'success', 'icon' => 'ri-checkbox-circle-line', 'label' => 'Completadas'],
                                                'cancelada' => ['color' => 'danger', 'icon' => 'ri-close-circle-line', 'label' => 'Canceladas'],
                                                default => ['color' => 'secondary', 'icon' => 'ri-file-list-line', 'label' => ucfirst($estado['estado'])]
                                            };
                                        ?>
                                        <div class="col-md-6 col-xl-2-4">
                                            <div class="card-body text-center">
                                                <i class="<?php echo $config['icon']; ?> text-<?php echo $config['color']; ?>" style="font-size: 2rem;"></i>
                                                <h3 class="mt-2 mb-1"><?php echo $estado['total']; ?></h3>
                                                <p class="text-muted mb-0"><?php echo $config['label']; ?></p>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- container -->
            </div>
            <!-- content -->

            <!-- Footer Start -->
            <?php
            include("includes/footer.php");
            ?>
            <!-- end Footer -->
        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->
    </div>
    <!-- END wrapper -->

    <!-- Vendor js -->
    <?php include("includes/js.php"); ?>

    <script>
    // Mostrar mensajes de sesión si existen
    $(document).ready(function() {
        <?php if (isset($_SESSION['success'])): ?>
        toastr.success('<?php echo $_SESSION['success']; unset($_SESSION['success']); ?>');
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
        toastr.error('<?php echo $_SESSION['error']; unset($_SESSION['error']); ?>');
        <?php endif; ?>
    });
    </script>

</body>

</html>