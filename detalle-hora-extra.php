<?php
// detalle-hora-extra.php - Ver detalles de una solicitud de hora extra
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

// Obtener ID de la solicitud
$solicitud_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($solicitud_id <= 0) {
    $_SESSION['error'] = "Solicitud no válida";
    header("Location: horas-extras.php");
    exit;
}

$HoraExtra_class = new HoraExtra();
$solicitud = $HoraExtra_class->obtenerSolicitudPorId($solicitud_id);

// Verificar que la solicitud existe
if (!$solicitud) {
    $_SESSION['error'] = "Solicitud no encontrada";
    header("Location: horas-extras.php");
    exit;
}

// Si no es admin, verificar que sea del usuario actual
if (!$es_admin && $solicitud['usuario_id'] != $usuario_id) {
    $_SESSION['error'] = "No tiene permisos para ver esta solicitud";
    header("Location: horas-extras.php");
    exit;
}

// Definir clases de estado
$estado_class = match($solicitud['estado']) {
    'pendiente' => 'warning',
    'aprobada' => 'success',
    'rechazada' => 'danger',
    'cancelada' => 'secondary',
    default => 'secondary'
};
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Detalle Solicitud Hora Extra #<?php echo $solicitud_id; ?> | Time Production</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="assets/images/favicon.ico" />
    <script src="assets/js/hyper-config.js"></script>
    <link href="assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <style>
        .info-label {
            font-weight: 600;
            color: var(--ct-text-muted);
            margin-bottom: 8px;
            margin-top: 15px;
            font-size: 0.8125rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-value {
            font-size: 1.05rem;
            margin-bottom: 8px;
            color: var(--ct-body-color);
        }
        .section-title {
            border-bottom: 2px solid var(--ct-border-color);
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-weight: 600;
            color: var(--ct-body-color);
        }
        .horario-card {
            transition: all 0.3s ease;
            border-left: 4px solid #ffc107;
        }
        .horario-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .time-display {
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }
        .arrow-separator {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        /* Compatibilidad modo oscuro */
        [data-bs-theme="dark"] .horario-card .bg-white {
            background-color: var(--ct-card-cap-bg) !important;
        }
        [data-bs-theme="dark"] .time-display {
            color: var(--ct-body-color);
        }
        [data-bs-theme="dark"] .info-label {
            opacity: 0.8;
        }
        .card {
            box-shadow: 0 0 35px 0 rgba(154,161,171,.15);
        }
    </style>
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
                                    <a href="horas-extras.php" class="btn btn-secondary">
                                        <i class="ri-arrow-left-line"></i> Volver
                                    </a>
                                    
                                    <?php if ($es_admin && $solicitud['estado'] === 'pendiente'): ?>
                                    <button class="btn btn-success" onclick="aprobarSolicitud(<?php echo $solicitud_id; ?>)">
                                        <i class="ri-checkbox-circle-line"></i> Aprobar
                                    </button>
                                    <button class="btn btn-danger" onclick="rechazarSolicitud(<?php echo $solicitud_id; ?>)">
                                        <i class="ri-close-circle-line"></i> Rechazar
                                    </button>
                                    <?php endif; ?>
                                </div>
                                <h4 class="page-title">
                                    Detalle de Solicitud de Hora Extra #<?php echo $solicitud_id; ?>
                                </h4>
                            </div>
                        </div>
                    </div>

                    <!-- Estado de la Solicitud -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h3 class="mb-0">
                                                <span class="badge bg-<?php echo $estado_class; ?> fs-4">
                                                    <?php echo strtoupper($solicitud['estado']); ?>
                                                </span>
                                            </h3>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <h2 class="text-warning mb-0">
                                                <i class="ri-timer-flash-line"></i> 
                                                <?php 
                                                $total_horas = isset($solicitud['total_horas_extras']) ? $solicitud['total_horas_extras'] : (isset($solicitud['horas_extras']) ? $solicitud['horas_extras'] : 0);
                                                echo number_format($total_horas, 2); 
                                                ?> horas
                                            </h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información del Horario (destacado) -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card bg-light border-warning horario-card">
                                <div class="card-body">
                                    <h4 class="section-title text-warning">
                                        <i class="ri-time-line"></i> Horario de Horas Extras
                                    </h4>
                                    <div class="row align-items-center">
                                        <?php if (isset($solicitud['hora_inicio']) && isset($solicitud['hora_fin'])): ?>
                                        <div class="col-md-4 text-center">
                                            <div class="p-3 bg-white rounded shadow-sm">
                                                <div class="text-muted small mb-2">Hora de Inicio</div>
                                                <h2 class="mb-0 text-primary time-display">
                                                    <i class="ri-time-line"></i>
                                                    <?php echo date('h:i A', strtotime($solicitud['hora_inicio'])); ?>
                                                </h2>
                                                <div class="text-muted small mt-1 time-display">
                                                    (<?php echo date('H:i', strtotime($solicitud['hora_inicio'])); ?> formato 24h)
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <div class="py-4">
                                                <i class="ri-arrow-right-line fs-1 text-warning arrow-separator"></i>
                                                <div class="mt-2">
                                                    <span class="badge bg-warning text-dark fs-4 time-display">
                                                        <i class="ri-timer-flash-line"></i>
                                                        <?php 
                                                        $total_horas = isset($solicitud['total_horas_extras']) ? $solicitud['total_horas_extras'] : 0;
                                                        echo number_format($total_horas, 2); 
                                                        ?> hrs
                                                    </span>
                                                </div>
                                                <div class="text-muted small mt-2">Total de horas extras</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <div class="p-3 bg-white rounded shadow-sm">
                                                <div class="text-muted small mb-2">Hora de Finalización</div>
                                                <h2 class="mb-0 text-success time-display">
                                                    <i class="ri-time-line"></i>
                                                    <?php echo date('h:i A', strtotime($solicitud['hora_fin'])); ?>
                                                </h2>
                                                <div class="text-muted small mt-1 time-display">
                                                    (<?php echo date('H:i', strtotime($solicitud['hora_fin'])); ?> formato 24h)
                                                </div>
                                            </div>
                                        </div>
                                        <?php else: ?>
                                        <div class="col-12 text-center py-4">
                                            <div class="alert alert-warning mb-0">
                                                <i class="ri-alert-line"></i>
                                                <strong>Horario no especificado</strong>
                                                <p class="mb-0 mt-2">Esta solicitud no tiene horario detallado. Posiblemente es un registro antiguo.</p>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Información del Trabajador y Orden -->
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="section-title">
                                        <i class="ri-user-line"></i> Información del Trabajador
                                    </h4>
                                    
                                    <div class="info-label">
                                        <i class="ri-user-3-line text-primary me-1"></i> Nombre Completo
                                    </div>
                                    <div class="info-value">
                                        <?php echo htmlspecialchars($solicitud['usuario_nombre']); ?>
                                    </div>

                                   

                                    <div class="info-label">
                                        <i class="ri-mail-line text-success me-1"></i> Email
                                    </div>
                                    <div class="info-value">
                                        <?php echo htmlspecialchars($solicitud['usuario_email']); ?>
                                    </div>

                                    <?php if (!empty($solicitud['departamento'])): ?>
                                    <div class="info-label">
                                        <i class="ri-building-line text-warning me-1"></i> Departamento
                                    </div>
                                    <div class="info-value">
                                        <span class="badge badge-secondary-lighten fs-6">
                                            <?php echo htmlspecialchars($solicitud['departamento']); ?>
                                        </span>
                                    </div>
                                    <?php endif; ?>

                                    <div class="info-label">
                                        <i class="ri-calendar-line text-primary me-1"></i> Fecha de Solicitud
                                    </div>
                                    <div class="info-value">
                                        <?php echo date('d/m/Y', strtotime($solicitud['fecha'])); ?>
                                        <small class="text-muted ms-2">
                                            (<?php 
                                            $dias = (time() - strtotime($solicitud['fecha'])) / (60 * 60 * 24);
                                            echo $dias < 1 ? 'Hoy' : 'Hace ' . floor($dias) . ' días';
                                            ?>)
                                        </small>
                                    </div>

                                    <div class="info-label">
                                        <i class="ri-time-line text-info me-1"></i> Fecha/Hora de Creación
                                    </div>
                                    <div class="info-value">
                                        <?php echo date('d/m/Y H:i', strtotime($solicitud['created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información de la Orden de Producción -->
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="section-title">
                                        <i class="ri-file-list-3-line"></i> Orden de Producción
                                    </h4>
                                    
                                    <div class="info-label">
                                        <i class="ri-file-list-3-line text-primary me-1"></i> Código OP
                                    </div>
                                    <div class="info-value">
                                        <span class="badge badge-primary-lighten fs-5">
                                            <?php echo htmlspecialchars($solicitud['codigo_op']); ?>
                                        </span>
                                    </div>

                                    <div class="info-label">
                                        <i class="ri-shopping-bag-line text-info me-1"></i> Producto
                                    </div>
                                    <div class="info-value">
                                        <?php echo htmlspecialchars($solicitud['nombre_producto']); ?>
                                    </div>

                                    <div class="info-label">
                                        <i class="ri-user-3-line text-success me-1"></i> Cliente
                                    </div>
                                    <div class="info-value">
                                        <?php echo htmlspecialchars($solicitud['cliente']); ?>
                                    </div>

                                    <?php if (!empty($solicitud['estado_orden'])): ?>
                                    <div class="info-label">
                                        <i class="ri-flag-line text-warning me-1"></i> Estado de la Orden
                                    </div>
                                    <div class="info-value">
                                        <span class="badge badge-<?php echo $solicitud['estado_orden'] === 'activa' ? 'success' : 'info'; ?>-lighten fs-6">
                                            <?php echo ucfirst($solicitud['estado_orden']); ?>
                                        </span>
                                    </div>
                                    <?php endif; ?>

                                    <div class="info-label">
                                        <i class="ri-calendar-event-line text-primary me-1"></i> Fecha para Horas Extras
                                    </div>
                                    <div class="info-value">
                                        <?php echo date('l, d/m/Y', strtotime($solicitud['fecha'])); ?>
                                        <br>
                                        <small class="text-muted">
                                            <i class="ri-time-line me-1"></i>
                                            <?php 
                                            $dias = (strtotime($solicitud['fecha']) - time()) / (60 * 60 * 24);
                                            if (abs($dias) < 1) {
                                                echo 'Hoy';
                                            } elseif ($dias > 0) {
                                                echo 'En ' . ceil($dias) . ' días';
                                            } else {
                                                echo 'Hace ' . abs(floor($dias)) . ' días';
                                            }
                                            ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Descripción del Trabajo -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="section-title">
                                        <i class="ri-file-text-line"></i> Descripción del Trabajo
                                    </h4>
                                    <?php if (!empty($solicitud['descripcion_trabajo'])): ?>
                                    <div class="alert alert-light border">
                                        <i class="ri-chat-quote-line text-muted"></i>
                                        <p class="mb-0 mt-2" style="white-space: pre-wrap; font-size: 1.05rem; line-height: 1.6;">
                                            <?php echo nl2br(htmlspecialchars($solicitud['descripcion_trabajo'])); ?>
                                        </p>
                                    </div>
                                    <?php else: ?>
                                    <div class="alert alert-info border-info">
                                        <i class="ri-information-line"></i>
                                        <strong>Sin descripción detallada</strong>
                                        <p class="mb-0 mt-2">El trabajador no proporcionó una descripción específica del trabajo a realizar durante las horas extras.</p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información de Aprobación/Rechazo -->
                    <?php if ($solicitud['estado'] !== 'pendiente'): ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-<?php echo $estado_class; ?>">
                                <div class="card-body">
                                    <h4 class="section-title">
                                        <i class="ri-file-shield-line"></i> 
                                        Información de <?php echo $solicitud['estado'] === 'aprobada' ? 'Aprobación' : 'Rechazo'; ?>
                                    </h4>
                                    
                                    <?php if (!empty($solicitud['aprobador_nombre'])): ?>
                                    <div class="info-label">
                                        <?php echo $solicitud['estado'] === 'aprobada' ? 'Aprobado por' : 'Rechazado por'; ?>
                                    </div>
                                    <div class="info-value">
                                        <i class="ri-user-star-line"></i>
                                        <strong><?php echo htmlspecialchars($solicitud['aprobador_nombre']); ?></strong>
                                    </div>
                                    <?php endif; ?>

                                    <?php if (!empty($solicitud['fecha_respuesta'])): ?>
                                    <div class="info-label">Fecha de Respuesta</div>
                                    <div class="info-value">
                                        <i class="ri-calendar-check-line"></i>
                                        <?php echo date('d/m/Y H:i', strtotime($solicitud['fecha_respuesta'])); ?>
                                    </div>
                                    <?php endif; ?>

                                    <?php if (!empty($solicitud['comentario_aprobacion'])): ?>
                                    <div class="info-label">Comentario</div>
                                    <div class="alert alert-<?php echo $estado_class; ?>">
                                        <p class="mb-0" style="white-space: pre-wrap;">
                                            <?php echo nl2br(htmlspecialchars($solicitud['comentario_aprobacion'])); ?>
                                        </p>
                                    </div>
                                    <?php endif; ?>

                                    <?php if ($solicitud['estado'] === 'aprobada'): ?>
                                    <div class="alert alert-success mt-3">
                                        <i class="ri-checkbox-circle-line"></i>
                                        <strong>Esta solicitud fue aprobada.</strong> Las horas han sido registradas automáticamente en el sistema.
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Acciones -->
                    <?php if ($solicitud['estado'] === 'pendiente'): ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Acciones Disponibles</h5>
                                    
                                    <?php if ($es_admin): ?>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-success btn-lg" onclick="aprobarSolicitud(<?php echo $solicitud_id; ?>)">
                                            <i class="ri-checkbox-circle-line"></i> Aprobar Solicitud
                                        </button>
                                        <button class="btn btn-danger btn-lg" onclick="rechazarSolicitud(<?php echo $solicitud_id; ?>)">
                                            <i class="ri-close-circle-line"></i> Rechazar Solicitud
                                        </button>
                                    </div>
                                    <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="ri-information-line"></i>
                                        Esta solicitud está pendiente de revisión por parte del administrador.
                                    </div>
                                    <button class="btn btn-danger" onclick="cancelarSolicitud(<?php echo $solicitud_id; ?>)">
                                        <i class="ri-close-line"></i> Cancelar Solicitud
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
            <?php include("includes/footer.php"); ?>
        </div>
    </div>

    <!-- Modal Aprobar -->
    <div id="modal-aprobar" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h4 class="modal-title">
                        <i class="ri-checkbox-circle-line"></i> Aprobar Solicitud de Horas Extras
                    </h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="action/aprobar-hora-extra.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="aprobar-id" name="solicitud_id" value="<?php echo $solicitud_id; ?>">
                        
                        <div class="alert alert-info">
                            <i class="ri-information-line"></i>
                            Al aprobar esta solicitud, se registrarán automáticamente 
                            <strong><?php echo number_format($solicitud['total_horas_extras'], 1); ?> horas</strong> 
                            en la fecha <strong><?php echo date('d/m/Y', strtotime($solicitud['fecha'])); ?></strong>
                            para la orden de producción <strong><?php echo htmlspecialchars($solicitud['codigo_op']); ?></strong>.
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Comentario (opcional)</label>
                            <textarea class="form-control" name="comentario" rows="4" 
                                      placeholder="Agregar comentario sobre la aprobación..."></textarea>
                            <small class="text-muted">Este comentario será visible para el trabajador.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">
                            <i class="ri-checkbox-circle-line"></i> Aprobar Solicitud
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Rechazar -->
    <div id="modal-rechazar" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h4 class="modal-title">
                        <i class="ri-close-circle-line"></i> Rechazar Solicitud de Horas Extras
                    </h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="action/rechazar-hora-extra.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="rechazar-id" name="solicitud_id" value="<?php echo $solicitud_id; ?>">
                        
                        <div class="alert alert-warning">
                            <i class="ri-alert-line"></i>
                            Está a punto de rechazar la solicitud de 
                            <strong><?php echo number_format($solicitud['total_horas_extras'], 1); ?> horas</strong> 
                            del trabajador <strong><?php echo htmlspecialchars($solicitud['usuario_nombre']); ?></strong>.
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Motivo del Rechazo <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="comentario" rows="4" required
                                      placeholder="Explique claramente el motivo del rechazo. Esta información será visible para el trabajador."></textarea>
                            <small class="text-danger">Campo obligatorio</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="ri-close-circle-line"></i> Rechazar Solicitud
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include("includes/js.php"); ?>

    <script>
    function aprobarSolicitud(id) {
        $('#aprobar-id').val(id);
        $('#modal-aprobar').modal('show');
    }

    function rechazarSolicitud(id) {
        $('#rechazar-id').val(id);
        $('#modal-rechazar').modal('show');
    }

    function cancelarSolicitud(id) {
        if (confirm('¿Está seguro de cancelar esta solicitud?\n\nEsta acción no se puede deshacer.')) {
            window.location.href = 'action/cancelar-hora-extra.php?id=' + id;
        }
    }

    <?php if (isset($_SESSION['success'])): ?>
    $(document).ready(function() {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '<?php echo $_SESSION['success']; ?>',
            confirmButtonColor: '#0acf97'
        });
    });
    <?php unset($_SESSION['success']); endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
    $(document).ready(function() {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?php echo $_SESSION['error']; ?>',
            confirmButtonColor: '#fa5c7c'
        });
    });
    <?php unset($_SESSION['error']); endif; ?>
    </script>

</body>
</html>
