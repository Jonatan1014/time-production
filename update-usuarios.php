<?php
session_start();
require_once 'includes/Class-usuario.php';

// Verificar si el usuario está logueado
$Usuario_class = new Usuario();
if (!$Usuario_class->usuarioLogueado()) {
    header('Location: login.php');
    exit;
}

// Verificar permisos de administrador
if (!$Usuario_class->verificarPermisos('Administrador')) {
    $_SESSION['error'] = 'No tienes permisos para acceder a esta página';
    header('Location: index.php');
    exit;
}

// Verificar si se pasó un ID de usuario para actualizar
if (!isset($_GET['id'])) {
    $_SESSION['error'] = 'ID de usuario no proporcionado.';
    header('Location: usuarios.php');
    exit;
}

$id_usuario = (int)$_GET['id'];

// Obtener datos del usuario a actualizar
$datosUser = $Usuario_class->obtenerUsuarioPorId($id_usuario);

if (!$datosUser) {
    $_SESSION['error'] = 'Usuario no encontrado.';
    header('Location: usuarios.php');
    exit;
}

// Obtener roles disponibles
$roles = $Usuario_class->obtenerRoles();

// Obtener departamentos activos
$departamentos = $Usuario_class->obtenerDepartamentosActivos();

// Obtener cargos activos
require_once 'includes/Class-cargos.php';
$Cargos_class = new Cargos();
$cargos = $Cargos_class->obtenerCargosActivos();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Actualizar Usuario | Sysmaint</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />

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
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">
        <!-- ========== Topbar Start ========== -->
        <?php
        include("includes/header.php");
        ?>
        <!-- ========== Topbar End ========== -->

        <?php
        include("includes/sidebar.php");
        ?>

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">
                <!-- Start Content-->
                <div class="container-fluid">
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

                    <!-- titulo pagina-->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <form class="d-flex">
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-light"
                                                id="dash-daterange" />
                                            <span class="input-group-text bg-primary border-primary text-white">
                                                <i class="mdi mdi-calendar-range font-13"></i>
                                            </span>
                                        </div>
                                    </form>
                                </div>
                                <h4 class="page-title">Usuarios</h4>
                            </div>
                        </div>
                    </div>

                    <!-- formulario -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">
                                        <i class="mdi mdi-account-edit-outline me-2"></i>Actualizar Usuario
                                    </h4>
                                    <p class="text-muted mb-4">Usuario: <strong><?php echo htmlspecialchars($datosUser['nombre_completo']); ?></strong> (@<?php echo htmlspecialchars($datosUser['username']); ?>)</p>
                                    
                                    <form method="POST" action="action/update-usuarios.php" id="form-actualizar-usuario">
                                    <div class="row">
                                        <!-- Campo oculto para el ID del usuario -->
                                        <input type="hidden" name="id" value="<?php echo $datosUser['id']; ?>">
                                        
                                        <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="user-nombre" class="form-label">
                                                        <i class="mdi mdi-account-outline me-1"></i>Nombre Completo <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" id="user-nombre" class="form-control"
                                                        name="nombre_completo" value="<?php echo htmlspecialchars($datosUser['nombre_completo']); ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="user-username" class="form-label">
                                                        <i class="mdi mdi-account-circle-outline me-1"></i>Nombre de Usuario <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" id="user-username" class="form-control"
                                                        name="username" value="<?php echo htmlspecialchars($datosUser['username']); ?>" required
                                                        pattern="[a-zA-Z0-9_]+" title="Solo letras, números y guiones bajos">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="user-email" class="form-label">
                                                        <i class="mdi mdi-email-outline me-1"></i>Correo Electrónico <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="email" id="user-email" class="form-control"
                                                        name="email" value="<?php echo htmlspecialchars($datosUser['email']); ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="user-password" class="form-label">
                                                        <i class="mdi mdi-lock-outline me-1"></i>Nueva Contraseña
                                                    </label>
                                                    <div class="input-group input-group-merge">
                                                        <input type="password" id="user-password" class="form-control"
                                                            name="password" placeholder="Dejar en blanco para mantener actual">
                                                        <div class="input-group-text" data-password="false">
                                                            <span class="password-eye"></span>
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">Solo completar si desea cambiar la contraseña (mínimo 6 caracteres)</small>
                                                </div>

                                        </div> <!-- end col -->

                                        <div class="col-lg-6">

                                            <div class="mb-3">
                                                <label for="user-departamento" class="form-label">
                                                    <i class="mdi mdi-office-building-outline me-1"></i>Departamento
                                                </label>
                                                <select class="form-select" id="user-departamento" name="departamento_id">
                                                    <option value="">Seleccionar departamento</option>
                                                    <?php foreach ($departamentos as $depto): ?>
                                                        <option value="<?php echo $depto['id']; ?>" 
                                                            <?php echo ($datosUser['departamento_id'] == $depto['id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($depto['nombre']); ?>
                                                            <?php if (!empty($depto['codigo'])): ?>
                                                                (<?php echo htmlspecialchars($depto['codigo']); ?>)
                                                            <?php endif; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="user-cargo" class="form-label">
                                                    <i class="mdi mdi-briefcase-outline me-1"></i>Cargo
                                                </label>
                                                <select class="form-select" id="user-cargo" name="cargo_id">
                                                    <option value="">Seleccionar cargo</option>
                                                    <?php foreach ($cargos as $cargo): ?>
                                                        <option value="<?php echo $cargo['id']; ?>" 
                                                            <?php echo ($datosUser['cargo_id'] == $cargo['id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($cargo['nombre']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                                <input type="date" id="user-fecha-ingreso" class="form-control"
                                                    name="fecha_ingreso" value="<?php echo htmlspecialchars($datosUser['fecha_ingreso'] ?? ''); ?>">
                                            </div>

                                            <div class="mb-3">
                                                <label for="user-valor-hora" class="form-label">
                                                    <i class="mdi mdi-currency-usd me-1"></i>Valor Hora Base
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" id="user-valor-hora" class="form-control"
                                                        name="valor_hora_base" value="<?php echo htmlspecialchars($datosUser['valor_hora_base'] ?? '0'); ?>" 
                                                        step="0.01" min="0">
                                                </div>
                                                <small class="text-muted">Tarifa base por hora trabajada</small>
                                            </div>

                                            <div class="mb-3">
                                                <label for="user-rol" class="form-label">
                                                    <i class="mdi mdi-shield-account-outline me-1"></i>Rol <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select" id="user-rol" name="rol" required>
                                                    <option value="">Seleccionar rol</option>
                                                    <?php foreach ($roles as $rol): ?>
                                                        <option value="<?php echo htmlspecialchars($rol['nombre']); ?>" 
                                                            <?php echo (strtolower($datosUser['rol']) == strtolower($rol['nombre'])) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($rol['nombre']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label d-block">
                                                    <i class="mdi mdi-account-check-outline me-1"></i>Estado del Usuario
                                                </label>
                                                <div class="form-check form-switch">
                                                    <input type="checkbox" class="form-check-input" id="user-estado" name="is_active" value="1"
                                                        <?php echo ($datosUser['is_active'] == 1) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="user-estado">
                                                        <strong>Usuario Activo</strong>
                                                        <small class="text-muted d-block">El usuario puede iniciar sesión</small>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="alert alert-info border-0">
                                                <small>
                                                    <i class="mdi mdi-information-outline me-1"></i>
                                                    <strong>Registro:</strong> <?php echo date('d/m/Y H:i', strtotime($datosUser['created_at'])); ?>
                                                    <?php if ($datosUser['updated_at'] != $datosUser['created_at']): ?>
                                                    <br><strong>Última actualización:</strong> <?php echo date('d/m/Y H:i', strtotime($datosUser['updated_at'])); ?>
                                                    <?php endif; ?>
                                                </small>
                                            </div>

                                        </div>
                                    </div>
                                    <!-- end row campos -->
                                    
                                    <hr class="my-4">
                                    
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between">
                                                <a href="usuarios.php" class="btn btn-light btn-lg">
                                                    <i class="mdi mdi-arrow-left me-1"></i> Volver al Listado
                                                </a>
                                                <button type="submit" class="btn btn-primary btn-lg" name="submit">
                                                    <i class="mdi mdi-content-save me-1"></i> Guardar Cambios
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    </form>

                                    </div>
                                    <!-- end row-->
                                </div> <!-- end card-body -->
                            </div> <!-- end card -->
                        </div><!-- end col -->
                    </div><!-- end row -->
                </div>
                <!-- container -->
            </div>
            <!-- content -->
            <!-- Footer Start -->
            <?php
            include("includes/footer.php");
            ?>
            <!-- end Footer -->

        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->
    </div>
    <!-- END wrapper -->

    <!-- Vendor js -->
    <?php
    include("includes/js.php");
    ?>
    
    <script>
    // Validación del formulario de actualización de usuario
    document.getElementById('form-actualizar-usuario').addEventListener('submit', function(e) {
        const username = document.getElementById('user-username').value.trim();
        const email = document.getElementById('user-email').value.trim();
        const password = document.getElementById('user-password').value;
        const nombreCompleto = document.getElementById('user-nombre').value.trim();
        
        // Validar nombre completo
        if (nombreCompleto === '') {
            e.preventDefault();
            alert('Por favor ingresa el nombre completo del usuario.');
            document.getElementById('user-nombre').focus();
            return false;
        }
        
        // Validar username
        if (username === '') {
            e.preventDefault();
            alert('Por favor ingresa el nombre de usuario.');
            document.getElementById('user-username').focus();
            return false;
        }
        
        if (!/^[a-zA-Z0-9_]+$/.test(username)) {
            e.preventDefault();
            alert('El nombre de usuario solo puede contener letras, números y guiones bajos (_).');
            document.getElementById('user-username').focus();
            return false;
        }
        
        // Validar email
        if (email === '') {
            e.preventDefault();
            alert('Por favor ingresa el correo electrónico.');
            document.getElementById('user-email').focus();
            return false;
        }
        
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('Por favor ingresa un correo electrónico válido.');
            document.getElementById('user-email').focus();
            return false;
        }
        
        // Validar contraseña solo si se ingresó una nueva
        if (password !== '' && password.length < 6) {
            e.preventDefault();
            alert('La contraseña debe tener al menos 6 caracteres.');
            document.getElementById('user-password').focus();
            return false;
        }
        
        return true;
    });
    
    // Confirmar antes de guardar cambios
    document.getElementById('form-actualizar-usuario').addEventListener('submit', function(e) {
        if (!this.checkValidity()) {
            return;
        }
        
        const confirmar = confirm('¿Estás seguro de que deseas actualizar los datos de este usuario?');
        if (!confirmar) {
            e.preventDefault();
        }
    });
    </script>

</body>

</html>