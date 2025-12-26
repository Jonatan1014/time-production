<?php
session_start();
require_once 'includes/Class-usuario.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    header("Location: login.php");
    exit;
}

// Verificar permisos de administrador
if (!$Usuario_class->verificarPermisos('Administrador')) {
    $_SESSION['error'] = 'No tienes permisos para acceder a esta página';
    header("Location: index.php");
    exit;
}

$id_empresa = $_SESSION['user_empresa_id'] ?? null;

// Obtener usuarios
$usuarios = $Usuario_class->obtenerUsuarios();
$roles = $Usuario_class->obtenerRoles();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Gestión de Usuarios | Sysmaint</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="shortcut icon" href="assets/images/favicon.ico" />
    <script src="assets/js/hyper-config.js"></script>
    <link href="assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    
    <!-- Datatables -->
    <link href="assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="wrapper">
        <?php include("includes/header.php"); ?>
        <?php include("includes/sidebar.php"); ?>

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">

                    <!-- Mensajes -->
                    <?php if (isset($_SESSION['exito'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['exito']; unset($_SESSION['exito']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Page Title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <a href="add-usuarios.php" class="btn btn-primary">
                                        <i class="mdi mdi-plus-circle me-1"></i> Nuevo Usuario
                                    </a>
                                </div>
                                <h4 class="page-title">Gestión de Usuarios</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas -->
                    <div class="row">
                        <div class="col-md-6 col-xl-3">
                            <div class="widget-rounded-circle card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="avatar-lg rounded-circle bg-soft-primary border-primary border">
                                                <i class="mdi mdi-account-group font-22 avatar-title text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-end">
                                                <h3 class="text-dark mt-1">
                                                    <span><?php echo count($usuarios); ?></span>
                                                </h3>
                                                <p class="text-muted mb-1 text-truncate">Total Usuarios</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="widget-rounded-circle card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="avatar-lg rounded-circle bg-soft-success border-success border">
                                                <i class="mdi mdi-account-check font-22 avatar-title text-success"></i>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-end">
                                                <h3 class="text-dark mt-1">
                                                    <span><?php echo count(array_filter($usuarios, function($u) { return $u['is_active'] == 1; })); ?></span>
                                                </h3>
                                                <p class="text-muted mb-1 text-truncate">Activos</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="widget-rounded-circle card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="avatar-lg rounded-circle bg-soft-info border-info border">
                                                <i class="mdi mdi-shield-account font-22 avatar-title text-info"></i>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-end">
                                                <h3 class="text-dark mt-1">
                                                    <span><?php echo count($roles); ?></span>
                                                </h3>
                                                <p class="text-muted mb-1 text-truncate">Roles</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="widget-rounded-circle card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="avatar-lg rounded-circle bg-soft-warning border-warning border">
                                                <i class="mdi mdi-account-plus font-22 avatar-title text-warning"></i>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-end">
                                                <h3 class="text-dark mt-1">
                                                    <span><?php echo count(array_filter($usuarios, function($u) { 
                                                        return strtotime($u['created_at']) > strtotime('-30 days'); 
                                                    })); ?></span>
                                                </h3>
                                                <p class="text-muted mb-1 text-truncate">Nuevos (30d)</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Usuarios -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">Listado de Usuarios</h4>
                                    
                                    <div class="table-responsive">
                                        <table id="tabla-usuarios" class="table table-hover table-centered mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Foto</th>
                                                    <th>Nombre</th>
                                                    <th>Email</th>
                                                    <th>Fecha Ingreso</th>
                                                    <th>Departamento</th>
                                                    <th>Rol</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($usuarios as $usuario): ?>
                                                <tr>
                               <td>
                           <img src="assets/images/uploads/users/user-default.png" 
                                     alt="Foto" class="rounded-circle" width="40" height="40">
                               </td>
                                                    <td>
                                                        <h5 class="font-14 my-1">
                                                            <?php echo htmlspecialchars($usuario['nombre_completo']); ?>
                                                        </h5>
                                                        <small class="text-muted">@<?php echo htmlspecialchars($usuario['username']); ?></small>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                                    <td>
                                                        <?php 
                                                        if (!empty($usuario['fecha_ingreso'])) {
                                                            echo date('d/m/Y', strtotime($usuario['fecha_ingreso']));
                                                        } else {
                                                            echo '<span class="text-muted">No registrada</span>';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($usuario['departamento'] ?? 'N/A'); ?></td>
                                                    <td>
                                                        <span class="badge bg-primary">
                                                            <?php echo htmlspecialchars(ucfirst($usuario['rol'])); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if ($usuario['is_active'] == 1): ?>
                                                            <span class="badge bg-success">Activo</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Inactivo</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="usuario-detalle.php?id=<?php echo $usuario['id']; ?>" 
                                                               class="btn btn-sm btn-info" title="Ver">
                                                                <i class="mdi mdi-eye"></i>
                                                            </a>
                                                            <a href="update-usuarios.php?id=<?php echo $usuario['id']; ?>" 
                                                               class="btn btn-sm btn-primary" title="Editar">
                                                                <i class="mdi mdi-pencil"></i>
                                                            </a>
                                                            <button type="button" 
                                                                    class="btn btn-sm <?php echo $usuario['is_active'] ? 'btn-warning' : 'btn-success'; ?>" 
                                                                    onclick="cambiarEstado(<?php echo $usuario['id']; ?>, '<?php echo htmlspecialchars($usuario['nombre_completo']); ?>', <?php echo $usuario['is_active']; ?>)"
                                                                    title="<?php echo $usuario['is_active'] ? 'Desactivar' : 'Activar'; ?>">
                                                                <i class="mdi mdi-<?php echo $usuario['is_active'] ? 'account-off' : 'account-check'; ?>"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
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

    <!-- Datatables -->
    <script src="assets/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="assets/vendor/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/vendor/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#tabla-usuarios').DataTable({
                language: {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
                },
                responsive: true,
                order: [[1, 'asc']],
                pageLength: 25
            });
        });

        function cambiarEstado(id, nombre, estadoActual) {
            const accion = estadoActual == 1 ? 'desactivar' : 'activar';
            const nuevoEstado = estadoActual == 1 ? 0 : 1;
            
            if (confirm('¿Estás seguro de ' + accion + ' al usuario ' + nombre + '?')) {
                const form = document.getElementById('form-cambiar-estado');
                form.elements['id'].value = id;
                form.elements['estado'].value = nuevoEstado;
                form.submit();
            }
        }
    </script>

    <!-- Formulario oculto para cambiar estado de usuario mediante POST -->
    <form id="form-cambiar-estado" method="POST" action="action/cambiar-estado-usuario.php" style="display:none;">
        <input type="hidden" name="id" value="">
        <input type="hidden" name="estado" value="">
    </form>

</body>
</html>
