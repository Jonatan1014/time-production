<?php
session_start();
require_once 'includes/Class-usuario.php';

// Obtener departamentos activos para el formulario
$Usuario_class = new Usuario();
$departamentos = $Usuario_class->obtenerDepartamentosActivos();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Registro de Usuario</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Sistema de gestión de tiempos y producción - Talleres Unidos Ltda" name="description" />
    <meta content="Talleres Unidos Ltda" name="author" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- Theme Config Js -->
    <script src="assets/js/hyper-config.js"></script>

    <!-- App css -->
    <link href="assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
</head>

<body class="authentication-bg position-relative">
    <div class="position-absolute start-0 end-0 start-0 bottom-0 w-100 h-100">
        <svg xmlns='http://www.w3.org/2000/svg' width='100%' height='100%' viewBox='0 0 800 800'>
            <g fill-opacity='0.22'>
                <circle style="fill: rgba(var(--ct-primary-rgb), 0.1);" cx='400' cy='400' r='600' />
                <circle style="fill: rgba(var(--ct-primary-rgb), 0.2);" cx='400' cy='400' r='500' />
                <circle style="fill: rgba(var(--ct-primary-rgb), 0.3);" cx='400' cy='400' r='300' />
                <circle style="fill: rgba(var(--ct-primary-rgb), 0.4);" cx='400' cy='400' r='200' />
                <circle style="fill: rgba(var(--ct-primary-rgb), 0.5);" cx='400' cy='400' r='100' />
            </g>
        </svg>
    </div>
    <div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5 position-relative">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-6 col-lg-8">
                    <div class="card">

                        <!-- Logo -->
                        <div class="card-header py-4 text-center bg-primary">
                            <a href="index.php">
                                <span class="text-white h3">Time Production</span>
                            </a>
                        </div>

                        <div class="card-body p-4">

                            <div class="text-center w-75 m-auto">
                                <h4 class="text-dark-50 text-center pb-0 fw-bold">Registro de Usuario</h4>
                                <p class="text-muted mb-4">Crea tu cuenta para acceder al sistema de gestión de horas de producción</p>
                            </div>

                            <?php
                            if (isset($_SESSION['error'])) {
                                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        ' . $_SESSION['error'] . '
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                      </div>';
                                unset($_SESSION['error']);
                            }
                            if (isset($_SESSION['exito'])) {
                                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                        ' . $_SESSION['exito'] . '
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                      </div>';
                                unset($_SESSION['exito']);
                            }
                            ?>

                            <form action="action/registrar-usuario.php" method="POST" id="form-registro">
                                <!-- Nombre completo -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nombres" class="form-label">
                                                <i class="mdi mdi-account-outline me-1"></i>Nombres <span class="text-danger">*</span>
                                            </label>
                                            <input class="form-control" type="text" id="nombres" name="nombres" 
                                                   placeholder="Ingresa tus nombres" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="apellidos" class="form-label">
                                                <i class="mdi mdi-account-outline me-1"></i>Apellidos <span class="text-danger">*</span>
                                            </label>
                                            <input class="form-control" type="text" id="apellidos" name="apellidos" 
                                                   placeholder="Ingresa tus apellidos" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- Username -->
                                <div class="mb-3">
                                    <label for="username" class="form-label">
                                        <i class="mdi mdi-account-circle-outline me-1"></i>Nombre de Usuario <span class="text-danger">*</span>
                                    </label>
                                    <input class="form-control" type="text" id="username" name="username" 
                                           placeholder="usuario123" required pattern="[a-zA-Z0-9_]+" 
                                           title="Solo letras, números y guiones bajos">
                                    <small class="text-muted">Este será tu usuario para iniciar sesión (solo letras, números y guiones bajos)</small>
                                </div>

                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="mdi mdi-email-outline me-1"></i>Correo Electrónico <span class="text-danger">*</span>
                                    </label>
                                    <input class="form-control" type="email" id="email" name="email" 
                                           placeholder="correo@ejemplo.com" required>
                                </div>

                                <!-- Contraseñas -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">
                                                <i class="mdi mdi-lock-outline me-1"></i>Contraseña <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group input-group-merge">
                                                <input type="password" id="password" class="form-control" name="password"
                                                       placeholder="Mínimo 6 caracteres" required minlength="6">
                                                <div class="input-group-text" data-password="false">
                                                    <span class="password-eye"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password_confirm" class="form-label">
                                                <i class="mdi mdi-lock-check-outline me-1"></i>Confirmar Contraseña <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group input-group-merge">
                                                <input type="password" id="password_confirm" class="form-control" name="password_confirm"
                                                       placeholder="Repite tu contraseña" required minlength="6">
                                                <div class="input-group-text" data-password="false">
                                                    <span class="password-eye"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Departamento (opcional) -->
                                <div class="mb-3">
                                    <label for="departamento" class="form-label">
                                        <i class="mdi mdi-office-building-outline me-1"></i>Departamento
                                    </label>
                                    <select class="form-select" id="departamento" name="departamento_id">
                                        <option value="">Seleccionar departamento (opcional)</option>
                                        <?php foreach ($departamentos as $depto): ?>
                                            <option value="<?php echo $depto['id']; ?>">
                                                <?php echo htmlspecialchars($depto['nombre']); ?>
                                                <?php if (!empty($depto['codigo'])): ?>
                                                    (<?php echo htmlspecialchars($depto['codigo']); ?>)
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Información importante -->
                                <div class="alert alert-warning border-0 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="mdi mdi-alert-circle-outline font-20 me-2"></i>
                                        <small>Tu cuenta será creada como <strong>Trabajador</strong> y estará <strong>inactiva</strong> hasta que un administrador la apruebe. Recibirás una notificación cuando esté activa.</small>
                                    </div>
                                </div>

                                <!-- Términos y condiciones -->
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="terminos" name="terminos" required>
                                        <label class="form-check-label" for="terminos">
                                            Acepto los términos y condiciones del sistema
                                        </label>
                                    </div>
                                </div>

                                <!-- Botón -->
                                <div class="mb-3 text-center">
                                    <button class="btn btn-primary btn-lg w-100" type="submit">
                                        <i class="mdi mdi-account-plus me-1"></i>Crear Cuenta
                                    </button>
                                </div>
                            </form>
                        </div> <!-- end card-body -->
                    </div>
                    <!-- end card -->

                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <p class="text-muted">¿Ya tienes una cuenta? <a href="login.php" class="text-muted ms-1"><b>Iniciar Sesión</b></a></p>
                        </div> <!-- end col -->
                    </div>
                    <!-- end row -->

                </div> <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <!-- end page -->

    <footer class="footer footer-alt">
        <script>document.write(new Date().getFullYear())</script> © Time Production - Talleres Unidos Ltda
    </footer>

    <!-- Vendor js -->
    <script src="assets/js/vendor.min.js"></script>

    <!-- App js -->
    <script src="assets/js/app.min.js"></script>

    <script>
        // Validación del formulario
        document.getElementById('form-registro').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const password_confirm = document.getElementById('password_confirm').value;
            const username = document.getElementById('username').value;
            
            // Validar contraseñas coincidan
            if (password !== password_confirm) {
                e.preventDefault();
                alert('Las contraseñas no coinciden. Por favor, verifica e intenta nuevamente.');
                document.getElementById('password_confirm').focus();
                return false;
            }
            
            // Validar longitud mínima
            if (password.length < 6) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 6 caracteres.');
                document.getElementById('password').focus();
                return false;
            }
            
            // Validar formato de username
            const usernameRegex = /^[a-zA-Z0-9_]+$/;
            if (!usernameRegex.test(username)) {
                e.preventDefault();
                alert('El nombre de usuario solo puede contener letras, números y guiones bajos.');
                document.getElementById('username').focus();
                return false;
            }
            
            return true;
        });

        // Generar username sugerido desde nombres y apellidos
        document.getElementById('nombres').addEventListener('blur', generarUsername);
        document.getElementById('apellidos').addEventListener('blur', generarUsername);
        
        function generarUsername() {
            const nombres = document.getElementById('nombres').value.trim();
            const apellidos = document.getElementById('apellidos').value.trim();
            const usernameField = document.getElementById('username');
            
            // Solo generar si el campo username está vacío
            if (nombres && apellidos && !usernameField.value) {
                const primerNombre = nombres.split(' ')[0].toLowerCase();
                const primerApellido = apellidos.split(' ')[0].toLowerCase();
                const username = primerNombre.charAt(0) + primerApellido;
                // Remover acentos y caracteres especiales
                usernameField.value = username
                    .normalize("NFD")
                    .replace(/[\u0300-\u036f]/g, "")
                    .replace(/[^a-zA-Z0-9_]/g, '');
                usernameField.placeholder = 'Ej: ' + usernameField.value;
            }
        }
    </script>

</body>

</html>
