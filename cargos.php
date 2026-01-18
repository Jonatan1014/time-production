<?php
// cargos.php - Listado de cargos
session_start();
require_once 'includes/Class-usuario.php';
require_once 'includes/Class-cargos.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    header("Location: login.php");
    exit;
}

if (!$Usuario_class->verificarPermisos('Administrador')) {
    header("Location: index.php");
    exit;
}

$Cargos_class = new Cargos();
$cargos = $Cargos_class->obtenerTodosCargos();

$pageTitle = "Gestión de Cargos";
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
                                <div class="page-title-right">
                                    <a href="add-cargos.php" class="btn btn-success">
                                        <i class="mdi mdi-plus me-1"></i> Agregar Cargo
                                    </a>
                                </div>
                                <h4 class="page-title">
                                    <i class="mdi mdi-briefcase me-1"></i>
                                    Gestión de Cargos
                                </h4>
                            </div>
                        </div>
                    </div>

                    <!-- Mensajes de éxito/error -->
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
                                    <h4 class="header-title mb-3">
                                        <i class="mdi mdi-briefcase-outline me-1"></i>
                                        Cargos Registrados
                                    </h4>

                                    <?php if (count($cargos) > 0): ?>
                                    <div class="table-responsive">
                                        <table id="tabla-cargos" class="table table-striped table-hover dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nombre</th>
                                                    <th>Descripción</th>
                                                    <th>Estado</th>
                                                    <th>Fecha Creación</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($cargos as $cargo): ?>
                                                <tr>
                                                    <td><?php echo $cargo['id']; ?></td>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($cargo['nombre']); ?></strong>
                                                    </td>
                                                    <td>
                                                        <?php echo htmlspecialchars($cargo['descripcion'] ?? 'Sin descripción'); ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($cargo['is_active'] == 1): ?>
                                                            <span class="badge bg-success">Activo</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Inactivo</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo date('d/m/Y', strtotime($cargo['created_at'])); ?></td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="update-cargos.php?id=<?php echo $cargo['id']; ?>"
                                                               class="btn btn-sm btn-outline-primary"
                                                               title="Editar cargo">
                                                                <i class="mdi mdi-pencil"></i>
                                                            </a>
                                                            <?php if ($cargo['is_active'] == 1): ?>
                                                            <button type="button" class="btn btn-sm btn-outline-warning"
                                                                    onclick="cambiarEstadoCargo(<?php echo $cargo['id']; ?>, 0)"
                                                                    title="Desactivar cargo">
                                                                <i class="mdi mdi-close"></i>
                                                            </button>
                                                            <?php else: ?>
                                                            <button type="button" class="btn btn-sm btn-outline-success"
                                                                    onclick="cambiarEstadoCargo(<?php echo $cargo['id']; ?>, 1)"
                                                                    title="Activar cargo">
                                                                <i class="mdi mdi-check"></i>
                                                            </button>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="mdi mdi-information me-2"></i>
                                        No hay cargos registrados. <a href="add-cargos.php">Crear el primer cargo</a>.
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
        $('#tabla-cargos').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            order: [[0, 'asc']],
            pageLength: 25
        });
    });

    function cambiarEstadoCargo(id, estado) {
        const accion = estado === 1 ? 'activar' : 'desactivar';
        const confirmacion = confirm(`¿Estás seguro de que deseas ${accion} este cargo?`);

        if (confirmacion) {
            $.ajax({
                url: 'action/cambiar-estado-cargo.php',
                type: 'POST',
                data: { id: id, estado: estado },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error al procesar la solicitud');
                }
            });
        }
    }
    </script>

</body>
</html>