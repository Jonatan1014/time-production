<?php
// update-dotacion-item.php - Formulario para actualizar items de dotacion
session_start();
require_once 'includes/Class-usuario.php';
require_once 'includes/Class-dotacion.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    header("Location: login.php");
    exit;
}

if (!$Usuario_class->verificarPermisos('administrador_dotacion')) {
    $_SESSION['error'] = "No tienes permisos para acceder a esta pagina";
    header("Location: index.php");
    exit;
}

$id_item = (int)($_GET['id'] ?? 0);
if ($id_item <= 0) {
    $_SESSION['error'] = 'ID de item no proporcionado.';
    header("Location: dotaciones-items.php");
    exit;
}

$Dotacion_class = new Dotacion();
$item = $Dotacion_class->obtenerItemPorId($id_item);

if (!$item) {
    $_SESSION['error'] = 'Item no encontrado.';
    header("Location: dotaciones-items.php");
    exit;
}

$pageTitle = "Actualizar Item de Dotacion";
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
                                    <a href="dotaciones-items.php" class="btn btn-secondary">
                                        <i class="mdi mdi-arrow-left me-1"></i> Volver al Listado
                                    </a>
                                </div>
                                <h4 class="page-title">
                                    <i class="mdi mdi-package-variant-closed me-1"></i>
                                    Actualizar Item de Dotacion
                                </h4>
                            </div>
                        </div>
                    </div>

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
                                    <form id="form-actualizar-item" action="action/update-dotacion-item.php" method="POST">
                                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="item-nombre" class="form-label">
                                                    <i class="mdi mdi-package-variant me-1"></i>Nombre del Item <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" id="item-nombre" class="form-control"
                                                    name="nombre" value="<?php echo htmlspecialchars($item['nombre']); ?>" required>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="item-descripcion" class="form-label">
                                                    <i class="mdi mdi-text-box-outline me-1"></i>Descripcion
                                                </label>
                                                <input type="text" id="item-descripcion" class="form-control"
                                                    name="descripcion" value="<?php echo htmlspecialchars($item['descripcion'] ?? ''); ?>">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary btn-lg">
                                                    <i class="mdi mdi-content-save me-1"></i> Guardar Cambios
                                                </button>
                                                <a href="dotaciones-items.php" class="btn btn-secondary btn-lg ms-2">
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
        $('#form-actualizar-item').on('submit', function(e) {
            const nombre = $('#item-nombre').val().trim();

            if (nombre === '') {
                e.preventDefault();
                alert('Por favor ingresa el nombre del item.');
                $('#item-nombre').focus();
                return false;
            }

            if (nombre.length < 2) {
                e.preventDefault();
                alert('El nombre del item debe tener al menos 2 caracteres.');
                $('#item-nombre').focus();
                return false;
            }

            return true;
        });
    });
    </script>

</body>
</html>
