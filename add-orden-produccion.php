<?php
session_start();
require_once 'includes/Class-usuario.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    header("Location: login.php");
    exit;
}

// Verificar que sea administrador
$es_admin = isset($_SESSION['user_rol']) && ($_SESSION['user_rol'] === 'administrador' || $_SESSION['user_rol'] === 'Administrador');

if (!$es_admin) {
    $_SESSION['error'] = "No tiene permisos para acceder a esta página";
    header("Location: ordenes-produccion.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Nueva Orden de Producción | Sistema de Horas</title>
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

                    <!-- Mensajes -->
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
                                    <a href="ordenes-produccion.php" class="btn btn-secondary">
                                        <i class="mdi mdi-arrow-left me-1"></i> Volver
                                    </a>
                                </div>
                                <h4 class="page-title">Nueva Orden de Producción</h4>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <form method="POST" action="action/add-orden-produccion.php" id="form-orden">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="codigo_op" class="form-label">Código OP <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="codigo_op" name="codigo_op" 
                                                       placeholder="Ej: OP-2024-001" required>
                                                <small class="text-muted">Código único de la orden</small>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="nombre_producto" class="form-label">Nombre del Producto <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="nombre_producto" name="nombre_producto" 
                                                       placeholder="Nombre del producto a fabricar" required>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="descripcion" class="form-label">Descripción</label>
                                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" 
                                                      placeholder="Descripción detallada de la orden de producción"></textarea>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="cliente" class="form-label">Cliente <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="cliente" name="cliente" 
                                                       placeholder="Nombre del cliente" required>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="cantidad_objetivo" class="form-label">Cantidad Objetivo <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="cantidad_objetivo" name="cantidad_objetivo" 
                                                       min="1" placeholder="Cantidad a producir" required>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="fecha_inicio" class="form-label">Fecha de Inicio <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="fecha_fin_estimada" class="form-label">Fecha Fin Estimada <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control" id="fecha_fin_estimada" name="fecha_fin_estimada" required>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="prioridad" class="form-label">Prioridad <span class="text-danger">*</span></label>
                                                <select class="form-select" id="prioridad" name="prioridad" required>
                                                    <option value="baja">Baja</option>
                                                    <option value="media" selected>Media</option>
                                                    <option value="alta">Alta</option>
                                                    <option value="urgente">Urgente</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="text-end mt-3">
                                            <button type="reset" class="btn btn-light me-2">
                                                <i class="mdi mdi-refresh me-1"></i> Limpiar
                                            </button>
                                            <button type="submit" class="btn btn-success">
                                                <i class="mdi mdi-content-save me-1"></i> Crear Orden
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
        // Validación de fechas
        document.getElementById('fecha_inicio').addEventListener('change', function() {
            const fechaInicio = new Date(this.value);
            const fechaFinInput = document.getElementById('fecha_fin_estimada');
            
            // Establecer fecha mínima para fecha fin
            fechaFinInput.min = this.value;
            
            // Si la fecha fin es anterior a la fecha inicio, actualizarla
            if (fechaFinInput.value && new Date(fechaFinInput.value) < fechaInicio) {
                fechaFinInput.value = this.value;
            }
        });
    </script>

</body>
</html>
