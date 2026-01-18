<?php
session_start();
require_once 'includes/Class-usuario.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    header("Location: login.php");
    exit;
}

// Obtener datos del usuario actual
$usuario = $Usuario_class->obtenerUsuarioPorId($_SESSION['user_id']);

if (!$usuario) {
    $_SESSION['error'] = 'No se pudo cargar la información del usuario';
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Mi Perfil | Time Production</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="shortcut icon" href="assets/images/favicon.ico" />
    <script src="assets/js/hyper-config.js"></script>
    <link href="assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    
    <style>
        .profile-image-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto;
        }
        
        .profile-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 4px solid var(--ct-card-bg);
            box-shadow: 0 0 20px rgba(0,0,0,0.3);
            background-color: var(--ct-card-bg);
        }
        
        .profile-image-upload {
            position: absolute;
            bottom: 0;
            right: 0;
            background: var(--ct-primary);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
        }
        
        .profile-image-upload:hover {
            background: var(--ct-primary-dark);
            transform: scale(1.05);
        }
        
        .profile-image-upload input {
            display: none;
        }
        
        .info-item {
            padding: 12px 0;
            border-bottom: 1px solid var(--ct-border-color);
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: var(--ct-body-color);
            margin-bottom: 4px;
            font-size: 13px;
        }
        
        .info-value {
            color: var(--ct-body-color);
            font-size: 15px;
        }
        
        .text-muted {
            opacity: 0.7;
        }
        
        .card {
            box-shadow: 0 0 35px 0 rgba(154,161,171,.15);
        }
        
        [data-bs-theme="dark"] .profile-image {
            border-color: var(--ct-card-bg);
        }
        
        [data-bs-theme="dark"] .info-item {
            border-bottom-color: rgba(255,255,255,0.1);
        }
    </style>
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
                                <h4 class="page-title">Mi Perfil</h4>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Información del Perfil -->
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <div class="profile-image-container mb-3">
                                            <img src="assets/images/uploads/users/image.png" 
                                                 alt="Foto de perfil" 
                                                 class="rounded-circle profile-image"
                                                 id="preview-imagen">
                                        </div>
                                        
                                        <h4 class="mb-1 mt-2"><?php echo htmlspecialchars($usuario['nombre_completo']); ?></h4>
                                        <p class="mb-1">
                                            <span class="badge badge-primary-lighten fs-6"><?php echo htmlspecialchars(ucfirst($usuario['rol'])); ?></span>
                                        </p>
                                    </div>

                                    <div class="mt-4">
                                        <h5 class="mb-3">Información Personal</h5>
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="ri-account-circle-line text-primary me-1"></i> Usuario
                                            </div>
                                            <div class="info-value"><?php echo htmlspecialchars($usuario['username']); ?></div>
                                        </div>
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="ri-mail-line text-info me-1"></i> Email
                                            </div>
                                            <div class="info-value"><?php echo htmlspecialchars($usuario['email']); ?></div>
                                        </div>
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="ri-building-line text-success me-1"></i> Departamento
                                            </div>
                                            <div class="info-value"><?php echo htmlspecialchars($usuario['departamento'] ?? 'No asignado'); ?></div>
                                        </div>
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="ri-calendar-event-line text-warning me-1"></i> Miembro desde
                                            </div>
                                            <div class="info-value"><?php echo date('d/m/Y', strtotime($usuario['created_at'])); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Editar Información -->
                        <div class="col-lg-8">
                            <!-- Datos Personales -->
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">
                                        <i class="ri-edit-box-line text-primary me-2"></i>
                                        Editar Datos Personales
                                    </h4>
                                    
                                    <form method="POST" action="action/actualizar-perfil.php" id="form-datos">
                                        <div class="mb-3">
                                            <label for="nombre_completo" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" 
                                                   value="<?php echo htmlspecialchars($usuario['nombre_completo']); ?>" required>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="username" class="form-label">Usuario <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="username" name="username" 
                                                       value="<?php echo htmlspecialchars($usuario['username']); ?>" required>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="email" name="email" 
                                                       value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="departamento" class="form-label">Departamento</label>
                                            <input type="text" class="form-control" id="departamento" name="departamento" 
                                                   value="<?php echo htmlspecialchars($usuario['departamento'] ?? 'No asignado'); ?>" 
                                                   readonly disabled style="background-color: var(--ct-input-disabled-bg); cursor: not-allowed;">
                                            <small class="text-muted">
                                                <i class="ri-information-line me-1"></i>
                                                Solo un administrador puede cambiar tu departamento
                                            </small>
                                        </div>

                                        <div class="text-end">
                                            <button type="submit" class="btn btn-primary" name="actualizar_datos">
                                                <i class="ri-save-line me-1"></i> Guardar Cambios
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Cambiar Contraseña -->
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">
                                        <i class="ri-lock-password-line text-warning me-2"></i>
                                        Cambiar Contraseña
                                    </h4>
                                    
                                    <form method="POST" action="action/actualizar-perfil.php" id="form-password">
                                        <div class="mb-3">
                                            <label for="password_actual" class="form-label">Contraseña Actual <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="password_actual" name="password_actual" 
                                                       placeholder="Ingresa tu contraseña actual" required>
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_actual')">
                                                    <i class="ri-eye-line" id="icon-password_actual"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="password_nueva" class="form-label">Nueva Contraseña <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="password_nueva" name="password_nueva" 
                                                           placeholder="Mínimo 8 caracteres" required minlength="8">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_nueva')">
                                                        <i class="ri-eye-line" id="icon-password_nueva"></i>
                                                    </button>
                                                </div>
                                                <small class="text-muted">Mínimo 8 caracteres</small>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="password_confirmar" class="form-label">Confirmar Nueva Contraseña <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="password_confirmar" name="password_confirmar" 
                                                           placeholder="Repite la nueva contraseña" required minlength="8">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmar')">
                                                        <i class="ri-eye-line" id="icon-password_confirmar"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert alert-info border-info">
                                            <i class="ri-information-line me-2"></i>
                                            <strong>Recomendaciones de seguridad:</strong>
                                            <ul class="mb-0 mt-2">
                                                <li>Usa al menos 8 caracteres</li>
                                                <li>Combina letras mayúsculas y minúsculas</li>
                                                <li>Incluye números y caracteres especiales</li>
                                            </ul>
                                        </div>

                                        <div class="text-end">
                                            <button type="submit" class="btn btn-warning" name="cambiar_password">
                                                <i class="ri-lock-password-line me-1"></i> Cambiar Contraseña
                                            </button>
                                        </div>
                                    </form>
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

    <script>
        // Validar que las contraseñas coincidan
        document.getElementById('form-password').addEventListener('submit', function(e) {
            const nueva = document.getElementById('password_nueva').value;
            const confirmar = document.getElementById('password_confirmar').value;
            
            if (nueva !== confirmar) {
                e.preventDefault();
                alert('Las contraseñas no coinciden. Por favor verifica.');
                return false;
            }
        });

        // Toggle password visibility
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById('icon-' + inputId);
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'ri-eye-off-line';
            } else {
                input.type = 'password';
                icon.className = 'ri-eye-line';
            }
        }
    </script>

</body>
</html>
