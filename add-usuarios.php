<?php
session_start();
require_once 'includes/Class-usuario.php';
require_once 'includes/Class-cargos.php';

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

// Obtener roles disponibles
$roles = $Usuario_class->obtenerRoles();

// Obtener departamentos activos
$departamentos = $Usuario_class->obtenerDepartamentosActivos();

// Obtener cargos activos
$Cargos_class = new Cargos();
$cargos = $Cargos_class->obtenerCargosActivos();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Añadir usuarios | Time Production</title>
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
                                        <i class="mdi mdi-account-plus-outline me-2"></i>Agregar Nuevo Usuario
                                    </h4>
                                    <p class="text-muted mb-4">Complete todos los campos obligatorios (*) para crear un nuevo usuario en el sistema.</p>
                                    
                                    <form method="POST" action="action/add-usuarios.php" id="form-crear-usuario">
                                    <div class="row">
                                        <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="user-first-name" class="form-label">Nombres <span class="text-danger">*</span></label>
                                                    <input type="text" id="user-first-name" class="form-control"
                                                        name="nombres" required placeholder="Ingresa los nombres">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="user-last-name" class="form-label">Apellidos <span class="text-danger">*</span></label>
                                                    <input type="text" id="user-last-name" class="form-control"
                                                        name="apellidos" required placeholder="Ingresa los apellidos">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="user-username" class="form-label">Nombre de Usuario <span class="text-danger">*</span></label>
                                                    <input type="text" id="user-username" class="form-control"
                                                        name="username" required placeholder="Usuario para login">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="user-email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                                                    <input type="email" id="user-email" class="form-control"
                                                        name="email" required placeholder="correo@ejemplo.com">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="user-password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                                                    <div class="input-group input-group-merge">
                                                        <input type="password" id="user-password" class="form-control"
                                                            name="password" required placeholder="Mínimo 6 caracteres">
                                                        <div class="input-group-text" data-password="false">
                                                            <span class="password-eye"></span>
                                                        </div>
                                                    </div>
                                                </div>

                                        </div> <!-- end col -->

                                        <div class="col-lg-6">

                                            <div class="mb-3">
                                                <label for="user-departamento" class="form-label">
                                                    <i class="mdi mdi-office-building me-1"></i>Departamento
                                                </label>
                                                <select class="form-select" id="user-departamento" name="departamento_id">
                                                    <option value="">Seleccionar departamento</option>
                                                    <?php foreach ($departamentos as $depto): ?>
                                                        <option value="<?php echo $depto['id']; ?>">
                                                            <?php echo htmlspecialchars($depto['nombre']); ?>
                                                            <?php if (!empty($depto['codigo'])): ?>
                                                                (<?php echo htmlspecialchars($depto['codigo']); ?>)
                                                            <?php endif; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <small class="text-muted">Opcional: Asignar área de trabajo</small>
                                            </div>

                                            <div class="mb-3">
                                                <label for="user-cargo" class="form-label">
                                                    <i class="mdi mdi-briefcase me-1"></i>Cargo
                                                </label>
                                                <select class="form-select" id="user-cargo" name="cargo_id">
                                                    <option value="">Seleccionar cargo</option>
                                                    <?php foreach ($cargos as $cargo): ?>
                                                        <option value="<?php echo $cargo['id']; ?>">
                                                            <?php echo htmlspecialchars($cargo['nombre']); ?>
                                                            <?php if (!empty($cargo['codigo'])): ?>
                                                                (<?php echo htmlspecialchars($cargo['codigo']); ?>)
                                                            <?php endif; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <small class="text-muted">Opcional: Asignar posición laboral</small>
                                            </div>

                                            <div class="mb-3">
                                                <label for="user-fecha-ingreso" class="form-label">
                                                    <i class="mdi mdi-calendar me-1"></i>Fecha de Ingreso
                                                </label>
                                                <input type="date" id="user-fecha-ingreso" class="form-control"
                                                    name="fecha_ingreso" value="<?php echo date('Y-m-d'); ?>">
                                                <small class="text-muted">Por defecto: fecha actual</small>
                                            </div>

                                            <div class="mb-3">
                                                <label for="user-valor-hora" class="form-label">
                                                    <i class="mdi mdi-currency-usd me-1"></i>Valor Hora Base
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" id="user-valor-hora" class="form-control"
                                                        name="valor_hora_base" value="0" step="0.01" min="0">
                                                </div>
                                                <small class="text-muted">Tarifa base por hora trabajada (se aplican recargos para horas extras)</small>
                                            </div>

                                            <div class="mb-3">
                                                <label for="user-rol" class="form-label">
                                                    <i class="mdi mdi-shield-account me-1"></i>Rol <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select" id="user-rol" name="rol" required>
                                                    <option value="">Seleccionar rol</option>
                                                    <?php foreach ($roles as $rol): ?>
                                                        <option value="<?php echo htmlspecialchars($rol['nombre']); ?>">
                                                            <?php echo htmlspecialchars($rol['nombre']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <small class="text-muted">Define los permisos del usuario</small>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label d-block">
                                                    <i class="mdi mdi-account-check me-1"></i>Estado Inicial
                                                </label>
                                                <div class="form-check form-switch">
                                                    <input type="checkbox" class="form-check-input" id="user-estado" name="is_active" value="1" checked>
                                                    <label class="form-check-label" for="user-estado">
                                                        <strong>Usuario Activo</strong>
                                                        <small class="text-muted d-block">El usuario podrá iniciar sesión inmediatamente</small>
                                                    </label>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <!-- end row campos-->

                                    <hr class="my-4">

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="alert alert-info border-0 mb-4">
                                                <div class="d-flex align-items-center">
                                                    <i class="mdi mdi-information-variant font-24 me-3"></i>
                                                    <div>
                                                        <h5 class="alert-heading mb-1">Información Importante</h5>
                                                        <p class="mb-0">Las credenciales de acceso serán: <strong>Usuario:</strong> el nombre de usuario ingresado, <strong>Contraseña:</strong> la contraseña definida arriba.</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between">
                                                <a href="usuarios.php" class="btn btn-light btn-lg">
                                                    <i class="mdi mdi-arrow-left me-1"></i> Volver al Listado
                                                </a>
                                                <button type="submit" class="btn btn-primary btn-lg" name="submit">
                                                    <i class="mdi mdi-content-save me-1"></i> Crear Usuario
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
        <!-- End Page Content -->
        <!-- ============================================================== -->
    </div>
    <!-- END wrapper -->

    <!-- Vendor js -->
    <?php
    include("includes/js.php");
    ?>

    <script>
        // Validación del formulario
        document.getElementById('form-crear-usuario').addEventListener('submit', function(e) {
            const password = document.getElementById('user-password').value;
            const username = document.getElementById('user-username').value;
            const email = document.getElementById('user-email').value;
            
            // Validar longitud de contraseña
            if (password.length < 6) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 6 caracteres.');
                document.getElementById('user-password').focus();
                return false;
            }
            
            // Validar formato de username (solo letras, números y guiones bajos)
            const usernameRegex = /^[a-zA-Z0-9_]+$/;
            if (!usernameRegex.test(username)) {
                e.preventDefault();
                alert('El nombre de usuario solo puede contener letras, números y guiones bajos.');
                document.getElementById('user-username').focus();
                return false;
            }
            
            // Validar formato de email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Por favor ingresa un correo electrónico válido.');
                document.getElementById('user-email').focus();
                return false;
            }
            
            return true;
        });

        // Generar username automáticamente desde nombres y apellidos
        document.getElementById('user-first-name').addEventListener('blur', generarUsername);
        document.getElementById('user-last-name').addEventListener('blur', generarUsername);
        
        function generarUsername() {
            const nombres = document.getElementById('user-first-name').value.trim();
            const apellidos = document.getElementById('user-last-name').value.trim();
            const usernameField = document.getElementById('user-username');
            
            // Solo generar si el campo username está vacío
            if (nombres && apellidos && !usernameField.value) {
                const primerNombre = nombres.split(' ')[0].toLowerCase();
                const primerApellido = apellidos.split(' ')[0].toLowerCase();
                const username = primerNombre.charAt(0) + primerApellido;
                usernameField.value = username.normalize("NFD").replace(/[\u0300-\u036f]/g, ""); // Remover acentos
            }
        }

        // Mostrar/ocultar contraseña
        document.querySelectorAll('[data-password]').forEach(function(element) {
            element.addEventListener('click', function() {
                const input = this.parentElement.querySelector('input');
                if (input.type === 'password') {
                    input.type = 'text';
                    this.querySelector('.password-eye').classList.add('show-password');
                } else {
                    input.type = 'password';
                    this.querySelector('.password-eye').classList.remove('show-password');
                }
            });
        });
    </script>

</body>

</html>