<?php
session_start();
require_once 'includes/Class-usuario.php';
require_once 'includes/Class-registros-horas.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    header("Location: login.php");
    exit;
}

// Obtener ID del registro
$id_registro = $_GET['id'] ?? null;

if (!$id_registro) {
    $_SESSION['error'] = "Registro no especificado";
    header("Location: mis-horas.php");
    exit;
}

$RegistroHoras_class = new RegistroHoras();
$registro = $RegistroHoras_class->obtenerRegistroPorId($id_registro);

if (!$registro) {
    $_SESSION['error'] = "Registro no encontrado";
    header("Location: mis-horas.php");
    exit;
}

// Verificar permisos: solo el usuario dueño o admin pueden ver el detalle
$es_admin = isset($_SESSION['user_rol']) && ($_SESSION['user_rol'] === 'administrador' || $_SESSION['user_rol'] === 'Administrador');
$es_propietario = $registro['usuario_id'] == $_SESSION['user_id'];

if (!$es_admin && !$es_propietario) {
    $_SESSION['error'] = "No tiene permisos para ver este registro";
    header("Location: mis-horas.php");
    exit;
}

// Convertir horas trabajadas a formato legible
$horas_enteras = floor($registro['horas_trabajadas']);
$minutos = ($registro['horas_trabajadas'] - $horas_enteras) * 60;
$duracion_texto = sprintf('%d horas', $horas_enteras);
if ($minutos > 0) {
    $duracion_texto .= sprintf(' %d minutos', $minutos);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Detalle de Registro | Time Production</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="shortcut icon" href="assets/images/favicon.ico" />
    <script src="assets/js/hyper-config.js"></script>
    <link href="assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="wrapper">
        <?php include("includes/header.php"); ?>
        <?php include("includes/sidebar.php"); ?>

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">

                    <!-- Mensajes -->
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Page Title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <a href="mis-horas.php" class="btn btn-secondary me-2">
                                        <i class="mdi mdi-arrow-left me-1"></i> Volver
                                    </a>
                                    <?php if ($es_propietario && $registro['estado'] === 'registrado' && (!isset($registro['editado']) || $registro['editado'] == 0)): ?>
                                    <a href="editar-registro.php?id=<?php echo $registro['id']; ?>" class="btn btn-warning">
                                        <i class="mdi mdi-pencil me-1"></i> Editar
                                    </a>
                                    <?php endif; ?>
                                </div>
                                <h4 class="page-title">Detalle de Registro de Horas</h4>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Información Principal -->
                        <div class="col-lg-8">
                            <div class="card border-start border-primary border-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h4 class="header-title mb-0">
                                            <i class="mdi mdi-clock-outline text-primary me-2"></i>
                                            Información del Registro
                                        </h4>
                                        <div>
                                            <?php
                                            $estado_badge = match($registro['estado']) {
                                                'registrado' => ['class' => 'primary', 'icon' => 'mdi-clock-outline'],
                                                'validado' => ['class' => 'success', 'icon' => 'mdi-check-circle'],
                                                'rechazado' => ['class' => 'danger', 'icon' => 'mdi-close-circle'],
                                                default => ['class' => 'secondary', 'icon' => 'mdi-help-circle']
                                            };
                                            ?>
                                            <span class="badge bg-<?php echo $estado_badge['class']; ?> fs-6">
                                                <i class="mdi <?php echo $estado_badge['icon']; ?> me-1"></i>
                                                <?php echo ucfirst($registro['estado']); ?>
                                            </span>
                                            <?php if (isset($registro['editado']) && $registro['editado'] == 1): ?>
                                            <span class="badge bg-info fs-6 ms-2">
                                                <i class="mdi mdi-pencil me-1"></i>
                                                Editado
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3 pb-3 border-bottom">
                                            <div class="text-muted fw-semibold mb-2">
                                                <i class="mdi mdi-calendar me-1"></i> Fecha
                                            </div>
                                            <div class="fs-5">
                                                <?php echo date('l, d \d\e F \d\e Y', strtotime($registro['fecha'])); ?>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3 pb-3 border-bottom">
                                            <div class="text-muted fw-semibold mb-2">
                                                <i class="mdi mdi-account me-1"></i> Trabajador
                                            </div>
                                            <div class="fs-5">
                                                <?php echo htmlspecialchars($registro['usuario_nombre']); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3 pb-3 border-bottom">
                                        <div class="text-muted fw-semibold mb-2">
                                            <i class="mdi mdi-file-document me-1"></i> Orden de Producción
                                        </div>
                                        <div class="fs-5">
                                            <strong><?php echo htmlspecialchars($registro['codigo_op']); ?></strong> - 
                                            <?php echo htmlspecialchars($registro['nombre_producto']); ?>
                                            <br>
                                            <small class="text-muted">Cliente: <?php echo htmlspecialchars($registro['cliente']); ?></small>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3 pb-3 border-bottom">
                                            <div class="text-muted fw-semibold mb-2">
                                                <i class="mdi mdi-timer me-1"></i> Horas Trabajadas
                                            </div>
                                            <div>
                                                <span class="badge bg-success fs-3">
                                                    <?php echo number_format($registro['horas_trabajadas'], 1); ?> hrs
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3 pb-3 border-bottom">
                                            <div class="text-muted fw-semibold mb-2">
                                                <i class="mdi mdi-calendar-check me-1"></i> Estado
                                            </div>
                                            <div>
                                                <?php
                                                $estado_badges = [
                                                    'registrado' => 'success',
                                                    'validado' => 'primary',
                                                    'rechazado' => 'danger'
                                                ];
                                                $estado_class = $estado_badges[$registro['estado']] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?php echo $estado_class; ?> fs-5">
                                                    <?php echo ucfirst($registro['estado']); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3 pb-3 border-bottom">
                                        <div class="text-muted fw-semibold mb-2">
                                            <i class="mdi mdi-text-box me-1"></i> Descripción del Trabajo
                                        </div>
                                        <div class="fs-5">
                                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($registro['descripcion_trabajo'])); ?></p>
                                        </div>
                                    </div>

                                    <?php if (!empty($registro['observaciones'])): ?>
                                    <div class="mb-0">
                                        <div class="text-muted fw-semibold mb-2">
                                            <i class="mdi mdi-note-text me-1"></i> Observaciones
                                        </div>
                                        <div class="fs-5">
                                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($registro['observaciones'])); ?></p>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Información de Validación -->
                            <?php if ($registro['estado'] !== 'registrado'): ?>
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">
                                        <i class="mdi mdi-shield-check text-<?php echo $registro['estado'] === 'validado' ? 'success' : 'danger'; ?> me-2"></i>
                                        Información de <?php echo $registro['estado'] === 'validado' ? 'Validación' : 'Rechazo'; ?>
                                    </h4>

                                    <div class="row">
                                        <div class="col-md-6 mb-3 pb-3 border-bottom">
                                            <div class="text-muted fw-semibold mb-2">
                                                <?php echo $registro['estado'] === 'validado' ? 'Validado' : 'Rechazado'; ?> por
                                            </div>
                                            <div class="fs-5">
                                                <?php echo htmlspecialchars($registro['validador_nombre'] ?? 'N/A'); ?>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3 pb-3 border-bottom">
                                            <div class="text-muted fw-semibold mb-2">
                                                Fecha de <?php echo $registro['estado'] === 'validado' ? 'Validación' : 'Rechazo'; ?>
                                            </div>
                                            <div class="fs-5">
                                                <?php 
                                                if ($registro['fecha_validacion']) {
                                                    echo date('d/m/Y H:i', strtotime($registro['fecha_validacion']));
                                                } else {
                                                    echo 'N/A';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if (!empty($registro['comentario_validacion'])): ?>
                                    <div class="mb-0">
                                        <div class="text-muted fw-semibold mb-2">
                                            Comentario
                                        </div>
                                        <div>
                                            <div class="alert alert-<?php echo $registro['estado'] === 'validado' ? 'success' : 'danger'; ?> mb-0">
                                                <i class="mdi mdi-comment-text me-1"></i>
                                                <?php echo nl2br(htmlspecialchars($registro['comentario_validacion'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Panel Lateral -->
                        <div class="col-lg-4">
                            <!-- Resumen Rápido -->
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">
                                        <i class="mdi mdi-information-outline me-1"></i>
                                        Resumen Rápido
                                    </h5>

                                    <div class="mb-3 pb-3 border-bottom">
                                        <div class="text-muted fw-semibold mb-2">Duración Real</div>
                                        <h4 class="mb-0 text-primary"><?php echo $duracion_texto; ?></h4>
                                    </div>

                                    <div class="mb-3 pb-3 border-bottom">
                                        <div class="text-muted fw-semibold mb-2">Registro Nº</div>
                                        <h4 class="mb-0">#<?php echo str_pad($registro['id'], 6, '0', STR_PAD_LEFT); ?></h4>
                                    </div>

                                    <div class="mb-0 <?php echo $registro['created_at'] !== $registro['updated_at'] ? 'pb-3 border-bottom' : ''; ?>">
                                        <div class="text-muted fw-semibold mb-2">Fecha de Creación</div>
                                        <p class="mb-0">
                                            <?php echo date('d/m/Y H:i', strtotime($registro['created_at'])); ?>
                                        </p>
                                    </div>

                                    <?php if ($registro['created_at'] !== $registro['updated_at']): ?>
                                    <div class="mt-3">
                                        <div class="text-muted fw-semibold mb-2">Última Actualización</div>
                                        <p class="mb-0">
                                            <?php echo date('d/m/Y H:i', strtotime($registro['updated_at'])); ?>
                                        </p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Timeline de Estados -->
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-4">
                                        <i class="mdi mdi-timeline-clock me-1"></i>
                                        Historial
                                    </h5>

                                    <div>
                                        <div class="d-flex mb-3 pb-3 border-bottom">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-sm rounded-circle bg-success-lighten">
                                                    <span class="avatar-title text-success rounded-circle">
                                                        <i class="mdi mdi-check"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="mt-0 mb-1">Registro Creado</h5>
                                                <p class="text-muted mb-0">
                                                    <small><?php echo date('d/m/Y H:i', strtotime($registro['created_at'])); ?></small>
                                                </p>
                                            </div>
                                        </div>

                                        <?php if ($registro['created_at'] !== $registro['updated_at']): ?>
                                        <div class="d-flex mb-3 pb-3 border-bottom">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-sm rounded-circle bg-warning-lighten">
                                                    <span class="avatar-title text-warning rounded-circle">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="mt-0 mb-1">Registro Actualizado</h5>
                                                <p class="text-muted mb-0">
                                                    <small><?php echo date('d/m/Y H:i', strtotime($registro['updated_at'])); ?></small>
                                                </p>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <?php if ($registro['estado'] === 'validado'): ?>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-sm rounded-circle bg-success-lighten">
                                                    <span class="avatar-title text-success rounded-circle">
                                                        <i class="mdi mdi-check-circle"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="mt-0 mb-1">Registro Validado</h5>
                                                <p class="text-muted mb-1">
                                                    <small>Por: <?php echo htmlspecialchars($registro['validador_nombre'] ?? 'N/A'); ?></small>
                                                </p>
                                                <p class="text-muted mb-0">
                                                    <small><?php echo date('d/m/Y H:i', strtotime($registro['fecha_validacion'])); ?></small>
                                                </p>
                                            </div>
                                        </div>
                                        <?php elseif ($registro['estado'] === 'rechazado'): ?>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-sm rounded-circle bg-danger-lighten">
                                                    <span class="avatar-title text-danger rounded-circle">
                                                        <i class="mdi mdi-close-circle"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="mt-0 mb-1">Registro Rechazado</h5>
                                                <p class="text-muted mb-1">
                                                    <small>Por: <?php echo htmlspecialchars($registro['validador_nombre'] ?? 'N/A'); ?></small>
                                                </p>
                                                <p class="text-muted mb-0">
                                                    <small><?php echo date('d/m/Y H:i', strtotime($registro['fecha_validacion'])); ?></small>
                                                </p>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Acciones Rápidas -->
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">
                                        <i class="mdi mdi-lightning-bolt me-1"></i>
                                        Acciones
                                    </h5>

                                    <div class="d-grid gap-2">
                                        <a href="orden-produccion.php?id=<?php echo $registro['orden_produccion_id']; ?>" 
                                           class="btn btn-outline-primary">
                                            <i class="mdi mdi-file-document me-1"></i>
                                            Ver Orden de Producción
                                        </a>

                                        <?php if ($es_propietario): ?>
                                        <a href="registrar-horas.php" class="btn btn-outline-success">
                                            <i class="mdi mdi-plus-circle me-1"></i>
                                            Nuevo Registro
                                        </a>
                                        <?php endif; ?>

                                        <button onclick="window.print()" class="btn btn-outline-secondary">
                                            <i class="mdi mdi-printer me-1"></i>
                                            Imprimir
                                        </button>
                                    </div>
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

    <style media="print">
        .btn, .page-title-right, .sidebar, .navbar-custom, .card-title i, .timeline {
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
