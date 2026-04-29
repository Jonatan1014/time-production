<?php
// dotaciones.php - Gestion de entregas de dotacion
session_start();
require_once 'includes/Class-usuario.php';
require_once 'includes/Class-dotacion.php';
require_once 'includes/Class-configuracion.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    header("Location: login.php");
    exit;
}

if (!$Usuario_class->verificarPermisos('administrador_dotacion')) {
    $_SESSION['error'] = "No tiene permisos para acceder a esta pagina";
    header("Location: index.php");
    exit;
}

$Dotacion_class = new Dotacion();
$Configuracion_class = new Configuracion();

$intervalo = (int)$Configuracion_class->obtenerValor('dotacion_intervalo_meses', 4);
if ($intervalo <= 0) {
    $intervalo = 1;
}

$fecha_actual = date('Y-m-d');
$pendientes = $Dotacion_class->obtenerPendientes($fecha_actual);
$entregas = $Dotacion_class->obtenerEntregas();

$pageTitle = "Entregas de Dotacion";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title><?php echo $pageTitle; ?> | Time Production</title>
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
                                <div class="page-title-right d-flex gap-2">
                                    <a href="add-dotacion-entrega.php" class="btn btn-success">
                                        <i class="mdi mdi-plus me-1"></i> Registrar Entrega
                                    </a>
                                    <a href="dotaciones-items.php" class="btn btn-outline-primary">
                                        <i class="mdi mdi-package-variant me-1"></i> Items de Dotacion
                                    </a>
                                </div>
                                <h4 class="page-title">
                                    <i class="mdi mdi-clipboard-list-outline me-1"></i>
                                    Entregas de Dotacion
                                </h4>
                            </div>
                        </div>
                    </div>

                    <?php if (isset($_SESSION['exito'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['exito']; unset($_SESSION['exito']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <ul class="nav nav-tabs nav-bordered mb-3">
                                        <li class="nav-item">
                                            <a href="#pendientes" data-bs-toggle="tab" aria-expanded="true" class="nav-link active">
                                                <i class="mdi mdi-alert-circle-outline me-1"></i>
                                                Pendientes <span class="badge bg-danger ms-1"><?php echo count($pendientes); ?></span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#historial" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                                <i class="mdi mdi-history me-1"></i>
                                                Historial de Entregas
                                            </a>
                                        </li>
                                    </ul>

                                    <div class="tab-content">
                                        <div class="tab-pane show active" id="pendientes">
                                            <div class="alert alert-info">
                                                <i class="mdi mdi-information me-2"></i>
                                                La dotacion se entrega cada <strong><?php echo $intervalo; ?></strong> mes(es).
                                            </div>

                                            <?php if (count($pendientes) > 0): ?>
                                            <div class="table-responsive">
                                                <table id="tabla-dotacion-pendientes" class="table table-striped table-hover dt-responsive nowrap w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>Usuario</th>
                                                            <th>Departamento</th>
                                                            <th>Cargo</th>
                                                            <th>Ultima Entrega</th>
                                                            <th>Proxima Entrega</th>
                                                            <th>Dias de Retraso</th>
                                                            <th>Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($pendientes as $pendiente): ?>
                                                        <tr>
                                                            <td>
                                                                <strong><?php echo htmlspecialchars($pendiente['nombre_completo']); ?></strong><br>
                                                                <small class="text-muted">@<?php echo htmlspecialchars($pendiente['username']); ?></small>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($pendiente['departamento'] ?? 'Sin departamento'); ?></td>
                                                            <td><?php echo htmlspecialchars($pendiente['cargo'] ?? 'Sin cargo'); ?></td>
                                                            <td>
                                                                <?php if (!empty($pendiente['ultima_entrega'])): ?>
                                                                    <?php echo date('d/m/Y', strtotime($pendiente['ultima_entrega'])); ?>
                                                                <?php else: ?>
                                                                    <span class="text-danger">Sin entrega</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php if (!empty($pendiente['proxima_entrega'])): ?>
                                                                    <?php echo date('d/m/Y', strtotime($pendiente['proxima_entrega'])); ?>
                                                                <?php else: ?>
                                                                    <span class="text-danger">Sin programar</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($pendiente['dias_retraso'] === null): ?>
                                                                    <span class="badge bg-warning">Pendiente inicial</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-danger"><?php echo (int)$pendiente['dias_retraso']; ?> dias</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <a href="add-dotacion-entrega.php?usuario_id=<?php echo $pendiente['id']; ?>"
                                                                   class="btn btn-sm btn-outline-primary" title="Registrar entrega">
                                                                    <i class="mdi mdi-plus"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <?php else: ?>
                                            <div class="alert alert-success">
                                                <i class="mdi mdi-check-circle me-2"></i>
                                                No hay usuarios pendientes de dotacion.
                                            </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="tab-pane" id="historial">
                                            <?php if (count($entregas) > 0): ?>
                                            <div class="table-responsive">
                                                <table id="tabla-dotacion-historial" class="table table-striped table-hover dt-responsive nowrap w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>Fecha Entrega</th>
                                                            <th>Usuario</th>
                                                            <th>Items</th>
                                                            <th>Proxima Entrega</th>
                                                            <th>Entregado Por</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($entregas as $entrega): ?>
                                                        <tr>
                                                            <td><?php echo date('d/m/Y', strtotime($entrega['fecha_entrega'])); ?></td>
                                                            <td>
                                                                <strong><?php echo htmlspecialchars($entrega['usuario_nombre']); ?></strong><br>
                                                                <small class="text-muted">@<?php echo htmlspecialchars($entrega['usuario_username']); ?></small>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($entrega['items'] ?? 'Sin items'); ?></td>
                                                            <td><?php echo date('d/m/Y', strtotime($entrega['proxima_entrega'])); ?></td>
                                                            <td><?php echo htmlspecialchars($entrega['entregado_por_nombre']); ?></td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <?php else: ?>
                                            <div class="alert alert-info">
                                                <i class="mdi mdi-information me-2"></i>
                                                Aun no hay entregas registradas.
                                            </div>
                                            <?php endif; ?>
                                        </div>
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
    <script src="assets/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#tabla-dotacion-pendientes').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            order: [[0, 'asc']],
            pageLength: 25
        });

        $('#tabla-dotacion-historial').DataTable({
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
