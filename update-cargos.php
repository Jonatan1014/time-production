<?php
// update-cargos.php - Formulario para actualizar cargos
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

// Verificar si se pasó un ID de cargo para actualizar
if (!isset($_GET['id'])) {
    $_SESSION['error'] = 'ID de cargo no proporcionado.';
    header('Location: cargos.php');
    exit;
}

$id_cargo = (int)$_GET['id'];

$Cargos_class = new Cargos();
$datosCargo = $Cargos_class->obtenerCargoPorId($id_cargo);

if (!$datosCargo) {
    $_SESSION['error'] = 'Cargo no encontrado.';
    header('Location: cargos.php');
    exit;
}

$pageTitle = "Actualizar Cargo";
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
                                    <i class="mdi mdi-briefcase-edit me-1"></i>
                                    Actualizar Cargo
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
                                        <i class="mdi mdi-briefcase-edit-outline me-2"></i>Actualizar Cargo
                                    </h4>
                                    <p class="text-muted mb-4">Cargo: <strong><?php echo htmlspecialchars($datosCargo['nombre']); ?></strong></p>

                                    <form method="POST" action="action/update-cargos.php" id="form-actualizar-cargo">
                                        <!-- Campo oculto para el ID del cargo -->
                                        <input type="hidden" name="id" value="<?php echo $datosCargo['id']; ?>">

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="cargo-nombre" class="form-label">
                                                    <i class="mdi mdi-briefcase-outline me-1"></i>Nombre del Cargo <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" id="cargo-nombre" class="form-control"
                                                    name="nombre" value="<?php echo htmlspecialchars($datosCargo['nombre']); ?>" required>
                                                <small class="text-muted">Nombre descriptivo del cargo</small>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="cargo-descripcion" class="form-label">
                                                    <i class="mdi mdi-text-box-outline me-1"></i>Descripción
                                                </label>
                                                <input type="text" id="cargo-descripcion" class="form-control"
                                                    name="descripcion" value="<?php echo htmlspecialchars($datosCargo['descripcion'] ?? ''); ?>">
                                                <small class="text-muted">Información adicional sobre las responsabilidades</small>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label d-block">
                                                    <i class="mdi mdi-check-circle-outline me-1"></i>Estado del Cargo
                                                </label>
                                                <div class="form-check form-switch">
                                                    <input type="checkbox" class="form-check-input" id="cargo-estado" name="is_active" value="1"
                                                        <?php echo ($datosCargo['is_active'] == 1) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="cargo-estado">
                                                        <strong>Cargo Activo</strong>
                                                        <small class="text-muted d-block">Los usuarios pueden ser asignados a este cargo</small>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert alert-info border-0">
                                            <small>
                                                <i class="mdi mdi-information-outline me-1"></i>
                                                <strong>Registro:</strong> <?php echo date('d/m/Y H:i', strtotime($datosCargo['created_at'])); ?>
                                                <?php if ($datosCargo['updated_at'] != $datosCargo['created_at']): ?>
                                                <br><strong>Última actualización:</strong> <?php echo date('d/m/Y H:i', strtotime($datosCargo['updated_at'])); ?>
                                                <?php endif; ?>
                                            </small>
                                        </div>

                                        <hr class="my-4">

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="d-flex justify-content-between">
                                                    <a href="cargos.php" class="btn btn-light btn-lg">
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
    // Validación del formulario de actualización de cargo
    document.getElementById('form-actualizar-cargo').addEventListener('submit', function(e) {
        const nombre = document.getElementById('cargo-nombre').value.trim();

        // Validar nombre
        if (nombre === '') {
            e.preventDefault();
            alert('Por favor ingresa el nombre del cargo.');
            document.getElementById('cargo-nombre').focus();
            return false;
        }

        if (nombre.length < 2) {
            e.preventDefault();
            alert('El nombre del cargo debe tener al menos 2 caracteres.');
            document.getElementById('cargo-nombre').focus();
            return false;
        }

        // Confirmar antes de guardar cambios
        if (!this.checkValidity()) {
            return;
        }

        const confirmar = confirm('¿Estás seguro de que deseas actualizar los datos de este cargo?');
        if (!confirmar) {
            e.preventDefault();
        }
    });
    </script>

</body>
</html>