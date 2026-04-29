<?php
// dotaciones-items.php - Gestion de items de dotacion
session_start();
require_once 'includes/Class-usuario.php';
require_once 'includes/Class-dotacion.php';

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
$items = $Dotacion_class->obtenerItems(false);

$pageTitle = "Items de Dotacion";
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
                                    <a href="add-dotacion-item.php" class="btn btn-success">
                                        <i class="mdi mdi-plus me-1"></i> Agregar Item
                                    </a>
                                </div>
                                <h4 class="page-title">
                                    <i class="mdi mdi-package-variant me-1"></i>
                                    Items de Dotacion
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
                                    <h4 class="header-title mb-3">
                                        <i class="mdi mdi-package-variant-closed me-1"></i>
                                        Items Registrados
                                    </h4>

                                    <?php if (count($items) > 0): ?>
                                    <div class="table-responsive">
                                        <table id="tabla-dotacion-items" class="table table-striped table-hover dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nombre</th>
                                                    <th>Descripcion</th>
                                                    <th>Estado</th>
                                                    <th>Fecha Creacion</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($items as $item): ?>
                                                <tr>
                                                    <td><?php echo $item['id']; ?></td>
                                                    <td><strong><?php echo htmlspecialchars($item['nombre']); ?></strong></td>
                                                    <td><?php echo htmlspecialchars($item['descripcion'] ?? 'Sin descripcion'); ?></td>
                                                    <td>
                                                        <?php if ($item['is_active'] == 1): ?>
                                                            <span class="badge bg-success">Activo</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Inactivo</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo date('d/m/Y', strtotime($item['created_at'])); ?></td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="update-dotacion-item.php?id=<?php echo $item['id']; ?>"
                                                               class="btn btn-sm btn-outline-primary"
                                                               title="Editar item">
                                                                <i class="mdi mdi-pencil"></i>
                                                            </a>
                                                            <?php if ($item['is_active'] == 1): ?>
                                                            <button type="button" class="btn btn-sm btn-outline-warning"
                                                                    onclick="cambiarEstadoItem(<?php echo $item['id']; ?>, 0)"
                                                                    title="Desactivar item">
                                                                <i class="mdi mdi-close"></i>
                                                            </button>
                                                            <?php else: ?>
                                                            <button type="button" class="btn btn-sm btn-outline-success"
                                                                    onclick="cambiarEstadoItem(<?php echo $item['id']; ?>, 1)"
                                                                    title="Activar item">
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
                                        No hay items registrados. <a href="add-dotacion-item.php">Crear el primer item</a>.
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
        $('#tabla-dotacion-items').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            order: [[1, 'asc']],
            pageLength: 25
        });
    });

    function cambiarEstadoItem(id, estado) {
        const accion = estado === 1 ? 'activar' : 'desactivar';
        const confirmacion = confirm(`¿Estas seguro de que deseas ${accion} este item?`);

        if (confirmacion) {
            $.ajax({
                url: 'action/cambiar-estado-dotacion-item.php',
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
