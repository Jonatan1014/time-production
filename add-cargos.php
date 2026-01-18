<?php
// add-cargos.php - Formulario para agregar nuevos cargos
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

$pageTitle = "Agregar Cargo";
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
                                    <a href="cargos.php" class="btn btn-secondary">
                                        <i class="mdi mdi-arrow-left me-1"></i> Volver al Listado
                                    </a>
                                </div>
                                <h4 class="page-title">
                                    <i class="mdi mdi-briefcase-plus me-1"></i>
                                    Agregar Nuevo Cargo
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
                                    <form id="form-agregar-cargo" action="action/add-cargos.php" method="POST">

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="cargo-nombre" class="form-label">
                                                    <i class="mdi mdi-briefcase-outline me-1"></i>Nombre del Cargo <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" id="cargo-nombre" class="form-control"
                                                    name="nombre" placeholder="Ej: Gerente de Producción" required>
                                                <small class="text-muted">Nombre descriptivo del cargo</small>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="cargo-descripcion" class="form-label">
                                                    <i class="mdi mdi-text-box-outline me-1"></i>Descripción
                                                </label>
                                                <input type="text" id="cargo-descripcion" class="form-control"
                                                    name="descripcion" placeholder="Descripción opcional del cargo">
                                                <small class="text-muted">Información adicional sobre las responsabilidades</small>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="alert alert-info">
                                                    <i class="mdi mdi-information-outline me-1"></i>
                                                    <strong>Nota:</strong> El cargo se creará como activo por defecto.
                                                    Podrás cambiar su estado posteriormente si es necesario.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-success btn-lg">
                                                    <i class="mdi mdi-content-save me-1"></i> Crear Cargo
                                                </button>
                                                <a href="cargos.php" class="btn btn-secondary btn-lg ms-2">
                                                    <i class="mdi mdi-close me-1"></i> Cancelar
                                                </a>
                                            </div>
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
    $(document).ready(function() {
        // Validación del formulario
        $('#form-agregar-cargo').on('submit', function(e) {
            const nombre = $('#cargo-nombre').val().trim();

            if (nombre === '') {
                e.preventDefault();
                alert('Por favor ingresa el nombre del cargo.');
                $('#cargo-nombre').focus();
                return false;
            }

            if (nombre.length < 2) {
                e.preventDefault();
                alert('El nombre del cargo debe tener al menos 2 caracteres.');
                $('#cargo-nombre').focus();
                return false;
            }

            return true;
        });
    });
    </script>

</body>
</html>