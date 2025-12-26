<?php
session_start();
require_once 'includes/Class-usuario.php';
require_once 'includes/Class-ordenes-produccion.php';
require_once 'includes/Class-registros-horas.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    header("Location: login.php");
    exit;
}

// Obtener ID de la orden
$id_orden = $_GET['id'] ?? null;

if (!$id_orden) {
    $_SESSION['error'] = "Orden no especificada";
    header("Location: ordenes-produccion.php");
    exit;
}

$OrdenProduccion_class = new OrdenProduccion();
$RegistroHoras_class = new RegistroHoras();

$orden = $OrdenProduccion_class->obtenerEstadisticasOrden($id_orden);

if (!$orden) {
    $_SESSION['error'] = "Orden no encontrada";
    header("Location: ordenes-produccion.php");
    exit;
}

// Obtener registros de horas de esta orden
$registros = $RegistroHoras_class->obtenerRegistrosPorOrden($id_orden);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Detalle Orden - <?php echo htmlspecialchars($orden['codigo_op']); ?> | Sistema de Horas</title>
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

                    <!-- Page Title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <a href="ordenes-produccion.php" class="btn btn-secondary me-2">
                                        <i class="mdi mdi-arrow-left me-1"></i> Volver
                                    </a>
                                    <?php if (isset($_SESSION['user_rol']) && ($_SESSION['user_rol'] === 'administrador' || $_SESSION['user_rol'] === 'Administrador')): ?>
                                    <a href="update-orden-produccion.php?id=<?php echo $orden['id']; ?>" class="btn btn-warning">
                                        <i class="mdi mdi-pencil me-1"></i> Editar
                                    </a>
                                    <?php endif; ?>
                                </div>
                                <h4 class="page-title">Detalle de Orden de Producción</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Información de la Orden -->
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-4">
                                        <i class="mdi mdi-file-document-outline text-primary me-1"></i>
                                        <?php echo htmlspecialchars($orden['codigo_op']); ?>
                                    </h4>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="text-muted mb-1">Producto</label>
                                                <h5><?php echo htmlspecialchars($orden['nombre_producto']); ?></h5>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="text-muted mb-1">Cliente</label>
                                                <h5><?php echo htmlspecialchars($orden['cliente']); ?></h5>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if (!empty($orden['descripcion'])): ?>
                                    <div class="mb-3">
                                        <label class="text-muted mb-1">Descripción</label>
                                        <p><?php echo nl2br(htmlspecialchars($orden['descripcion'])); ?></p>
                                    </div>
                                    <?php endif; ?>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="text-muted mb-1"><i class="mdi mdi-calendar-start"></i> Fecha de Inicio</label>
                                            <p class="fw-bold"><?php echo date('d/m/Y', strtotime($orden['fecha_inicio'])); ?></p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="text-muted mb-1"><i class="mdi mdi-calendar-end"></i> Fecha Fin Estimada</label>
                                            <p class="fw-bold"><?php echo date('d/m/Y', strtotime($orden['fecha_fin_estimada'])); ?></p>
                                        </div>
                                    </div>

                                    <?php if (!empty($orden['fecha_fin_real'])): ?>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="text-muted mb-1"><i class="mdi mdi-calendar-check"></i> Fecha Fin Real</label>
                                            <p class="fw-bold text-success"><?php echo date('d/m/Y', strtotime($orden['fecha_fin_real'])); ?></p>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="text-muted mb-1">Estado</label>
                                            <p>
                                                <?php
                                                $estado_class = match($orden['estado']) {
                                                    'activa' => 'primary',
                                                    'en_proceso' => 'info',
                                                    'pausada' => 'warning',
                                                    'completada' => 'success',
                                                    'cancelada' => 'danger',
                                                    default => 'secondary'
                                                };
                                                ?>
                                                <span class="badge bg-<?php echo $estado_class; ?> fs-5">
                                                    <?php echo ucfirst(str_replace('_', ' ', $orden['estado'])); ?>
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="text-muted mb-1">Prioridad</label>
                                            <p>
                                                <?php
                                                $prioridad_class = match($orden['prioridad']) {
                                                    'urgente' => 'danger',
                                                    'alta' => 'warning',
                                                    'media' => 'info',
                                                    'baja' => 'secondary',
                                                    default => 'secondary'
                                                };
                                                ?>
                                                <span class="badge bg-<?php echo $prioridad_class; ?> fs-5">
                                                    <?php echo ucfirst($orden['prioridad']); ?>
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="text-muted mb-1">Cantidad Objetivo</label>
                                            <p class="fw-bold fs-5"><?php echo number_format($orden['cantidad_objetivo']); ?> unidades</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Estadísticas -->
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-4">
                                        <i class="mdi mdi-chart-box-outline text-info me-1"></i>
                                        Estadísticas
                                    </h4>

                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Total Horas</span>
                                            <span class="fw-bold"><?php echo number_format($orden['total_horas'], 2); ?> hrs</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-primary" style="width: 100%"></div>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Total Registros</span>
                                            <span class="fw-bold"><?php echo $orden['total_registros']; ?></span>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Trabajadores Asignados</span>
                                            <span class="fw-bold"><?php echo $orden['total_trabajadores']; ?></span>
                                        </div>
                                    </div>

                                    <?php if ($orden['total_registros'] > 0): ?>
                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Promedio por Registro</span>
                                            <span class="fw-bold"><?php echo number_format($orden['promedio_horas_registro'], 2); ?> hrs</span>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <?php if (!empty($orden['primera_fecha_trabajo'])): ?>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Primera Fecha Trabajo</span>
                                            <span class="fw-bold"><?php echo date('d/m/Y', strtotime($orden['primera_fecha_trabajo'])); ?></span>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <?php if (!empty($orden['ultima_fecha_trabajo'])): ?>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Última Fecha Trabajo</span>
                                            <span class="fw-bold"><?php echo date('d/m/Y', strtotime($orden['ultima_fecha_trabajo'])); ?></span>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <div class="mb-0">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Días Transcurridos</span>
                                            <span class="fw-bold"><?php echo $orden['dias_transcurridos']; ?> días</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Registros de Horas -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">
                                        <i class="mdi mdi-clock-time-four-outline text-success me-1"></i>
                                        Registros de Horas
                                    </h4>
                                    
                                    <?php if (count($registros) > 0): ?>
                                    <div class="table-responsive">
                                        <table id="tabla-registros" class="table table-striped table-hover dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Usuario</th>
                                                    <th>Horas Trabajadas</th>
                                                    <th>Descripción</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($registros as $registro): ?>
                                                <tr>
                                                    <td><?php echo date('d/m/Y', strtotime($registro['fecha'])); ?></td>
                                                    <td><?php echo htmlspecialchars($registro['usuario_nombre']); ?></td>
                                                    <td>
                                                        <span class="badge bg-info fs-6"><?php echo number_format($registro['horas_trabajadas'], 1); ?> hrs</span>
                                                    </td>
                                                    <td>
                                                        <small><?php echo htmlspecialchars(substr($registro['descripcion_trabajo'], 0, 60)); ?><?php echo strlen($registro['descripcion_trabajo']) > 60 ? '...' : ''; ?></small>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $estado_badge = match($registro['estado']) {
                                                            'registrado' => 'primary',
                                                            'validado' => 'success',
                                                            'rechazado' => 'danger',
                                                            default => 'secondary'
                                                        };
                                                        ?>
                                                        <span class="badge bg-<?php echo $estado_badge; ?>">
                                                            <?php echo ucfirst($registro['estado']); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="mdi mdi-information me-2"></i>
                                        No hay registros de horas para esta orden de producción.
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
            $('#tabla-registros').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                order: [[0, 'desc']],
                pageLength: 25
            });
        });
    </script>

</body>
</html>
