<?php
session_start();
require_once 'includes/conn-db.php';
require_once 'includes/Class-usuario.php';

$Usuario_class = new Usuario();
if (!$Usuario_class->usuarioLogueado() || !$Usuario_class->verificarPermisos('Administrador')) {
    header('Location: login.php');
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// Obtener roles
$stmt = $conn->prepare("SELECT * FROM roles ORDER BY nombre");
$stmt->execute();
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Gestión de Roles | Time Production</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="assets/images/favicon.ico" />
    <script src="assets/js/hyper-config.js"></script>
    <link href="assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div class="wrapper">
        <?php include 'includes/header.php'; ?>
        <?php include 'includes/sidebar.php'; ?>

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
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-crear-rol">
                                        <i class="mdi mdi-plus-circle me-1"></i> Crear Nuevo Rol
                                    </button>
                                </div>
                                <h4 class="page-title">Gestión de Roles</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="mdi mdi-shield-account widget-icon"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0" title="Total de Roles">Total Roles</h5>
                                    <h3 class="mt-3 mb-3"><?php echo count($roles); ?></h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="mdi mdi-check-circle widget-icon bg-success-lighten text-success"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0" title="Roles Activos">Roles Activos</h5>
                                    <h3 class="mt-3 mb-3"><?php echo count(array_filter($roles, function($r) { return $r['estado'] == 1; })); ?></h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="mdi mdi-account-group widget-icon bg-info-lighten text-info"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0" title="Usuarios con Rol">Usuarios</h5>
                                    <h3 class="mt-3 mb-3">
                                        <?php 
                                        $Usuario_class = new Usuario();
                                        $usuarios = $Usuario_class->obtenerUsuarios();
                                        echo count($usuarios); 
                                        ?>
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Roles -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">Listado de Roles del Sistema</h4>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-hover table-centered mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Nombre del Rol</th>
                                                    <th>Descripción</th>
                                                    <th>Usuarios Asignados</th>
                                                    <th>Estado</th>
                                                    <th>Fecha Creación</th>
                                                    <th class="text-center">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($roles as $r): ?>
                                                <?php 
                                                    // Contar usuarios con este rol
                                                    $usuarios_rol = count(array_filter($usuarios, function($u) use ($r) { 
                                                        return strtolower($u['rol']) === strtolower($r['nombre']); 
                                                    }));
                                                ?>
                                                <tr>
                                                    <td>
                                                        <h5 class="font-14 my-1">
                                                            <i class="mdi mdi-shield-account text-primary me-1"></i>
                                                            <strong><?php echo htmlspecialchars(ucfirst($r['nombre'])); ?></strong>
                                                        </h5>
                                                    </td>
                                                    <td>
                                                        <span class="text-muted">
                                                            <?php echo htmlspecialchars($r['descripcion'] ?? 'Sin descripción'); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info rounded-pill">
                                                            <?php echo $usuarios_rol; ?> usuario<?php echo $usuarios_rol != 1 ? 's' : ''; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if ($r['estado'] == 1): ?>
                                                            <span class="badge bg-success">
                                                                <i class="mdi mdi-check-circle me-1"></i>Activo
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">
                                                                <i class="mdi mdi-close-circle me-1"></i>Inactivo
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                        if (!empty($r['created_at'])) {
                                                            echo date('d/m/Y', strtotime($r['created_at']));
                                                        } else {
                                                            echo 'N/A';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="btn-group" role="group">
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-primary" 
                                                                    onclick="editarRol(<?php echo $r['id']; ?>, '<?php echo htmlspecialchars($r['nombre']); ?>', '<?php echo htmlspecialchars($r['descripcion'] ?? ''); ?>')"
                                                                    title="Editar">
                                                                <i class="mdi mdi-pencil"></i>
                                                            </button>
                                                            <?php if ($usuarios_rol == 0): ?>
                                                            <button type="button" 
                                                                    class="btn btn-sm <?php echo $r['estado'] ? 'btn-warning' : 'btn-success'; ?>" 
                                                                    onclick="cambiarEstadoRol(<?php echo $r['id']; ?>, '<?php echo htmlspecialchars($r['nombre']); ?>', <?php echo $r['estado']; ?>)"
                                                                    title="<?php echo $r['estado'] ? 'Desactivar' : 'Activar'; ?>">
                                                                <i class="mdi mdi-<?php echo $r['estado'] ? 'close-circle' : 'check-circle'; ?>"></i>
                                                            </button>
                                                            <?php else: ?>
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-secondary" 
                                                                    disabled
                                                                    title="No se puede desactivar: tiene usuarios asignados">
                                                                <i class="mdi mdi-lock"></i>
                                                            </button>
                                                            <?php endif; ?>
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
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>

    <!-- Modal Crear Rol -->
    <div class="modal fade" id="modal-crear-rol" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="mdi mdi-plus-circle me-1"></i> Crear Nuevo Rol
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="action/add-role.php">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre del Rol <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control" required 
                                   placeholder="Ej: Supervisor, Operador">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="3" 
                                      placeholder="Describe las responsabilidades de este rol"></textarea>
                        </div>

                        <div class="alert alert-info">
                            <i class="mdi mdi-information-outline me-1"></i>
                            <strong>Nota:</strong> El rol estará activo por defecto y podrá ser asignado a usuarios.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save me-1"></i> Crear Rol
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Rol -->
    <div class="modal fade" id="modal-editar-rol" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="mdi mdi-pencil me-1"></i> Editar Rol
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="action/update-role.php">
                    <input type="hidden" name="id" id="edit-rol-id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre del Rol <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" id="edit-rol-nombre" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" id="edit-rol-descripcion" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save me-1"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include 'includes/js.php'; ?>

    <script>
        function editarRol(id, nombre, descripcion) {
            document.getElementById('edit-rol-id').value = id;
            document.getElementById('edit-rol-nombre').value = nombre;
            document.getElementById('edit-rol-descripcion').value = descripcion;
            
            const modal = new bootstrap.Modal(document.getElementById('modal-editar-rol'));
            modal.show();
        }

        function cambiarEstadoRol(id, nombre, estadoActual) {
            const accion = estadoActual == 1 ? 'desactivar' : 'activar';
            const nuevoEstado = estadoActual == 1 ? 0 : 1;
            
            if (confirm('¿Estás seguro de ' + accion + ' el rol "' + nombre + '"?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'action/cambiar-estado-rol.php';
                
                const inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = 'id';
                inputId.value = id;
                
                const inputEstado = document.createElement('input');
                inputEstado.type = 'hidden';
                inputEstado.name = 'estado';
                inputEstado.value = nuevoEstado;
                
                form.appendChild(inputId);
                form.appendChild(inputEstado);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>

</body>
</html>
