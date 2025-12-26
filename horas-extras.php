<?php
// horas-extras.php - Gestión de horas extras (para trabajadores y administradores)
session_start();
require_once 'includes/Class-usuario.php';
require_once 'includes/Class-horas-extras.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['user_id'];
$es_admin = isset($_SESSION['user_rol']) && ($_SESSION['user_rol'] === 'administrador' || $_SESSION['user_rol'] === 'Administrador');

$HoraExtra_class = new HoraExtra();

// Si es administrador, mostrar todas las solicitudes; si no, solo las del usuario
if ($es_admin) {
    // Obtener solicitudes separadas por estado
    $solicitudes_pendientes = $HoraExtra_class->obtenerTodasSolicitudes(['estado' => 'pendiente']);
    $solicitudes_procesadas = $HoraExtra_class->obtenerTodasSolicitudes(['estado' => ['aprobada', 'rechazada']]);
    $estadisticas = $HoraExtra_class->obtenerEstadisticas();
} else {
    // Para usuarios, separar por estado
    $solicitudes_pendientes = $HoraExtra_class->obtenerSolicitudesPorUsuario($usuario_id, 'pendiente');
    $solicitudes_procesadas = array_merge(
        $HoraExtra_class->obtenerSolicitudesPorUsuario($usuario_id, 'aprobada'),
        $HoraExtra_class->obtenerSolicitudesPorUsuario($usuario_id, 'rechazada')
    );
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Horas Extras | Time Production</title>
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
                                    <a href="solicitar-horas-extras.php" class="btn btn-warning">
                                        <i class="ri-timer-flash-line"></i> Nueva Solicitud
                                    </a>
                                </div>
                                <h4 class="page-title">
                                    <?php echo $es_admin ? 'Gestión de Horas Extras' : 'Mis Horas Extras'; ?>
                                </h4>
                            </div>
                        </div>
                    </div>

                    <?php if ($es_admin && isset($estadisticas)): ?>
                    <!-- Estadísticas (solo para admin) -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="ri-time-line widget-icon bg-warning-lighten text-warning"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0">Pendientes</h5>
                                    <h3 class="mt-3 mb-3"><?php echo $estadisticas['pendientes']; ?></h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-nowrap"><?php echo number_format($estadisticas['total_horas_pendientes'], 2); ?> horas</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="ri-checkbox-circle-line widget-icon bg-success-lighten text-success"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0">Aprobadas</h5>
                                    <h3 class="mt-3 mb-3"><?php echo $estadisticas['aprobadas']; ?></h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-nowrap"><?php echo number_format($estadisticas['total_horas_aprobadas'], 2); ?> horas</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="ri-close-circle-line widget-icon bg-danger-lighten text-danger"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0">Rechazadas</h5>
                                    <h3 class="mt-3 mb-3"><?php echo $estadisticas['rechazadas']; ?></h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-nowrap">Este período</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="ri-list-check widget-icon bg-info-lighten text-info"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0">Total</h5>
                                    <h3 class="mt-3 mb-3"><?php echo $estadisticas['total_solicitudes']; ?></h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-nowrap">Solicitudes</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Tabla de Solicitudes Pendientes -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4 class="header-title mb-0">
                                            <i class="ri-time-line text-warning me-2"></i>
                                            Solicitudes Pendientes
                                        </h4>
                                        <span class="badge bg-warning text-dark fs-5">
                                            <?php echo count($solicitudes_pendientes); ?> pendiente(s)
                                        </span>
                                    </div>
                                    
                                    <?php if (count($solicitudes_pendientes) > 0): ?>
                                    <div class="table-responsive">
                                        <table id="tabla-pendientes" class="table table-striped table-hover dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <?php if ($es_admin): ?>
                                                    <th>Usuario</th>
                                                    <?php endif; ?>
                                                    <th>Fecha</th>
                                                    <th>Orden Producción</th>
                                                    <th>Horario</th>
                                                    <th>Total Horas</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($solicitudes_pendientes as $solicitud): ?>
                                                <tr>
                                                    <?php if ($es_admin): ?>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($solicitud['usuario_nombre']); ?></strong><br>
                                                        <small class="text-muted"><?php echo htmlspecialchars($solicitud['username']); ?></small>
                                                    </td>
                                                    <?php endif; ?>
                                                    <td><?php echo date('d/m/Y', strtotime($solicitud['fecha'])); ?></td>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($solicitud['codigo_op']); ?></strong><br>
                                                        <small class="text-muted"><?php echo htmlspecialchars($solicitud['nombre_producto']); ?></small>
                                                    </td>
                                                    <td>
                                                        <?php if (isset($solicitud['hora_inicio']) && isset($solicitud['hora_fin'])): ?>
                                                            <i class="ri-time-line"></i> 
                                                            <strong><?php echo date('H:i', strtotime($solicitud['hora_inicio'])); ?></strong>
                                                            <i class="ri-arrow-right-line"></i>
                                                            <strong><?php echo date('H:i', strtotime($solicitud['hora_fin'])); ?></strong>
                                                        <?php else: ?>
                                                            <span class="text-muted">No especificado</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                        $total_horas = isset($solicitud['total_horas_extras']) ? $solicitud['total_horas_extras'] : (isset($solicitud['horas_extras']) ? $solicitud['horas_extras'] : 0);
                                                        ?>
                                                        <span class="badge bg-warning text-dark fs-6">
                                                            <i class="ri-timer-2-line"></i> <?php echo number_format($total_horas, 2); ?> hrs
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="detalle-hora-extra.php?id=<?php echo $solicitud['id']; ?>" 
                                                           class="btn btn-sm btn-info" title="Ver detalle">
                                                            <i class="ri-eye-line"></i>
                                                        </a>
                                                        
                                                        <?php if ($es_admin): ?>
                                                        <button class="btn btn-sm btn-success" 
                                                                onclick="aprobarSolicitud(<?php echo $solicitud['id']; ?>)" 
                                                                title="Aprobar">
                                                            <i class="ri-checkbox-circle-line"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" 
                                                                onclick="rechazarSolicitud(<?php echo $solicitud['id']; ?>)" 
                                                                title="Rechazar">
                                                            <i class="ri-close-circle-line"></i>
                                                        </button>
                                                        <?php else: ?>
                                                        <button class="btn btn-sm btn-danger" 
                                                                onclick="cancelarSolicitud(<?php echo $solicitud['id']; ?>)" 
                                                                title="Cancelar">
                                                            <i class="ri-close-line"></i>
                                                        </button>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php else: ?>
                                    <div class="alert alert-info mb-0">
                                        <i class="ri-information-line"></i>
                                        No hay solicitudes pendientes.
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Solicitudes Procesadas (Aprobadas y Rechazadas) -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4 class="header-title mb-0">
                                            <i class="ri-checkbox-circle-line text-primary me-2"></i>
                                            Historial de Solicitudes
                                        </h4>
                                        <span class="badge bg-primary fs-5">
                                            <?php echo count($solicitudes_procesadas); ?> procesada(s)
                                        </span>
                                    </div>
                                    
                                    <?php if (count($solicitudes_procesadas) > 0): ?>
                                    <div class="table-responsive">
                                        <table id="tabla-procesadas" class="table table-striped table-hover dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <?php if ($es_admin): ?>
                                                    <th>Usuario</th>
                                                    <?php endif; ?>
                                                    <th>Fecha</th>
                                                    <th>Orden Producción</th>
                                                    <th>Horario</th>
                                                    <th>Total Horas</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($solicitudes_procesadas as $solicitud): ?>
                                                <tr>
                                                    <?php if ($es_admin): ?>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($solicitud['usuario_nombre']); ?></strong><br>
                                                        <small class="text-muted"><?php echo htmlspecialchars($solicitud['username']); ?></small>
                                                    </td>
                                                    <?php endif; ?>
                                                    <td><?php echo date('d/m/Y', strtotime($solicitud['fecha'])); ?></td>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($solicitud['codigo_op']); ?></strong><br>
                                                        <small class="text-muted"><?php echo htmlspecialchars($solicitud['nombre_producto']); ?></small>
                                                    </td>
                                                    <td>
                                                        <?php if (isset($solicitud['hora_inicio']) && isset($solicitud['hora_fin'])): ?>
                                                            <i class="ri-time-line"></i> 
                                                            <strong><?php echo date('H:i', strtotime($solicitud['hora_inicio'])); ?></strong>
                                                            <i class="ri-arrow-right-line"></i>
                                                            <strong><?php echo date('H:i', strtotime($solicitud['hora_fin'])); ?></strong>
                                                        <?php else: ?>
                                                            <span class="text-muted">No especificado</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                        $total_horas = isset($solicitud['total_horas_extras']) ? $solicitud['total_horas_extras'] : (isset($solicitud['horas_extras']) ? $solicitud['horas_extras'] : 0);
                                                        ?>
                                                        <span class="badge bg-<?php echo $solicitud['estado'] === 'aprobada' ? 'success' : 'secondary'; ?> fs-6">
                                                            <i class="ri-timer-2-line"></i> <?php echo number_format($total_horas, 2); ?> hrs
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $estado_class = $solicitud['estado'] === 'aprobada' ? 'success' : 'danger';
                                                        $estado_icon = $solicitud['estado'] === 'aprobada' ? 'ri-checkbox-circle-line' : 'ri-close-circle-line';
                                                        ?>
                                                        <span class="badge bg-<?php echo $estado_class; ?> fs-6">
                                                            <i class="<?php echo $estado_icon; ?>"></i>
                                                            <?php echo ucfirst($solicitud['estado']); ?>
                                                        </span>
                                                        <?php if (!empty($solicitud['aprobador_nombre'])): ?>
                                                        <br><small class="text-muted">
                                                            Por: <?php echo htmlspecialchars($solicitud['aprobador_nombre']); ?>
                                                        </small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="detalle-hora-extra.php?id=<?php echo $solicitud['id']; ?>" 
                                                           class="btn btn-sm btn-info" title="Ver detalle">
                                                            <i class="ri-eye-line"></i> Ver
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
                                        No hay solicitudes procesadas.
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

    <!-- Modal Aprobar -->
    <div id="modal-aprobar" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Aprobar Horas Extras</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="action/aprobar-hora-extra.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="aprobar-id" name="solicitud_id">
                        <div class="mb-3">
                            <label class="form-label">Comentario (opcional)</label>
                            <textarea class="form-control" name="comentario" rows="3" 
                                      placeholder="Agregar comentario sobre la aprobación..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Aprobar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Rechazar -->
    <div id="modal-rechazar" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Rechazar Horas Extras</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="action/rechazar-hora-extra.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="rechazar-id" name="solicitud_id">
                        <div class="mb-3">
                            <label class="form-label">Motivo del rechazo <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="comentario" rows="3" required
                                      placeholder="Explique el motivo del rechazo..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Rechazar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include("includes/js.php"); ?>
    <script src="assets/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>

    <script>
    $(document).ready(function() {
        // Tabla de solicitudes pendientes
        $('#tabla-pendientes').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            order: [[<?php echo $es_admin ? '1' : '0'; ?>, 'desc']],
            pageLength: 10,
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
        });

        // Tabla de solicitudes procesadas
        $('#tabla-procesadas').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            order: [[<?php echo $es_admin ? '1' : '0'; ?>, 'desc']],
            pageLength: 25,
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
        });
    });

    function aprobarSolicitud(id) {
        $('#aprobar-id').val(id);
        $('#modal-aprobar').modal('show');
    }

    function rechazarSolicitud(id) {
        $('#rechazar-id').val(id);
        $('#modal-rechazar').modal('show');
    }

    function cancelarSolicitud(id) {
        if (confirm('¿Está seguro de cancelar esta solicitud?')) {
            window.location.href = 'action/cancelar-hora-extra.php?id=' + id;
        }
    }
    </script>

</body>
</html>
