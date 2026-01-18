<?php
session_start();
require_once 'includes/Class-usuario.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    header('Location: login.php');
    exit;
}

// Verificar permisos de administrador o que sea el mismo usuario
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    $_SESSION['error'] = 'ID de usuario no válido.';
    header('Location: usuarios.php');
    exit;
}

$usuario = $Usuario_class->obtenerUsuarioPorId($id);
if (!$usuario) {
    $_SESSION['error'] = 'Usuario no encontrado';
    header('Location: usuarios.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Detalle Usuario | Time Production</title>
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
    <div class="wrapper">
        <?php include 'includes/header.php'; ?>
        <?php include 'includes/sidebar.php'; ?>

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">

                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <h4 class="page-title">Detalle de Usuario</h4>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <img src="assets/images/uploads/users/image.png" alt="foto" class="rounded-circle mb-3" width="140" height="140">
                                    <h4><?php echo htmlspecialchars($usuario['nombre_completo']); ?></h4>
                                    <p class="text-muted"><?php echo htmlspecialchars(ucfirst($usuario['rol'])); ?></p>

                                    <hr />
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
                                    <p><strong>Username:</strong> <?php echo htmlspecialchars($usuario['username']); ?></p>
                                    <?php if (!empty($usuario['fecha_ingreso'])): ?>
                                    <p><strong>Fecha Ingreso:</strong> <?php echo date('d/m/Y', strtotime($usuario['fecha_ingreso'])); ?></p>
                                    <?php endif; ?>
                                    <p><strong>Valor Hora:</strong> $<?php echo number_format($usuario['valor_hora_base'] ?? 0, 2); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title">Información Laboral</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong><i class="mdi mdi-office-building-outline me-2"></i>Departamento:</strong> <?php echo htmlspecialchars($usuario['departamento'] ?? 'No asignado'); ?></p>
                                            <p><strong><i class="mdi mdi-briefcase-outline me-2"></i>Cargo:</strong> <?php echo htmlspecialchars($usuario['cargo'] ?? 'No asignado'); ?></p>
                                            <p><strong><i class="mdi mdi-account-check-outline me-2"></i>Rol:</strong> <?php echo htmlspecialchars(ucfirst($usuario['rol'])); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong><i class="mdi mdi-calendar-check-outline me-2"></i>Estado:</strong> <?php echo ($usuario['is_active'] == 1) ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'; ?></p>
                                            <p><strong><i class="mdi mdi-cash me-2"></i>Valor Hora:</strong> $<?php echo number_format($usuario['valor_hora_base'] ?? 0, 2); ?></p>
                                            <?php if (!empty($usuario['fecha_ingreso'])): ?>
                                            <p><strong><i class="mdi mdi-calendar-plus me-2"></i>Fecha Ingreso:</strong> <?php echo date('d/m/Y', strtotime($usuario['fecha_ingreso'])); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-body">
                                    <h4 class="header-title">Información del Sistema</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong><i class="mdi mdi-calendar-clock me-2"></i>Creado:</strong> <?php echo date('d/m/Y H:i', strtotime($usuario['created_at'] ?? 'now')); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <?php if (!empty($usuario['updated_at']) && $usuario['updated_at'] != $usuario['created_at']): ?>
                                            <p><strong><i class="mdi mdi-calendar-edit me-2"></i>Última actualización:</strong> <?php echo date('d/m/Y H:i', strtotime($usuario['updated_at'])); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-body">
                                    <h4 class="header-title">Acciones</h4>
                                    <div class="d-flex gap-2">
                                        <a href="update-usuarios.php?id=<?php echo $usuario['id']; ?>" class="btn btn-primary">
                                            <i class="mdi mdi-pencil me-1"></i>Editar Usuario
                                        </a>
                                        <a href="usuarios.php" class="btn btn-light">
                                            <i class="mdi mdi-arrow-left me-1"></i>Volver al Listado
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>

    <!-- Vendor js -->
    <?php include 'includes/js.php'; ?>

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
