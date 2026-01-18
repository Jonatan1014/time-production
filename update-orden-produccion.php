<?php
session_start();
require_once 'includes/Class-usuario.php';
require_once 'includes/Class-ordenes-produccion.php';

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

// Obtener ID de la orden
$id_orden = $_GET['id'] ?? null;

if (!$id_orden) {
    $_SESSION['error'] = "Orden no especificada";
    header("Location: ordenes-produccion.php");
    exit;
}

$OrdenProduccion_class = new OrdenProduccion();
$orden = $OrdenProduccion_class->obtenerOrdenPorId($id_orden);

if (!$orden) {
    $_SESSION['error'] = "Orden no encontrada";
    header("Location: ordenes-produccion.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Editar Orden de Producción | Time Production</title>
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
                                <h4 class="page-title">Editar Orden de Producción</h4>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <form method="POST" action="action/update-orden-produccion.php" id="form-orden">
                                        <input type="hidden" name="id" value="<?php echo $orden['id']; ?>">

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="codigo_op" class="form-label">Código OP <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="codigo_op" name="codigo_op" 
                                                       value="<?php echo htmlspecialchars($orden['codigo_op']); ?>" required>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="nombre_producto" class="form-label">Nombre del Producto <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="nombre_producto" name="nombre_producto" 
                                                       value="<?php echo htmlspecialchars($orden['nombre_producto']); ?>" required>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="descripcion" class="form-label">Descripción</label>
                                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?php echo htmlspecialchars($orden['descripcion']); ?></textarea>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="cliente" class="form-label">Cliente <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="cliente" name="cliente" 
                                                       value="<?php echo htmlspecialchars($orden['cliente']); ?>" required>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="cantidad_objetivo" class="form-label">Cantidad Objetivo <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="cantidad_objetivo" name="cantidad_objetivo" 
                                                       min="1" value="<?php echo $orden['cantidad_objetivo']; ?>" required>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-3 mb-3">
                                                <label for="fecha_inicio" class="form-label">Fecha de Inicio <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                                                       value="<?php echo $orden['fecha_inicio']; ?>" required>
                                            </div>

                                            <div class="col-md-3 mb-3">
                                                <label for="fecha_fin_estimada" class="form-label">Fecha Fin Estimada <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control" id="fecha_fin_estimada" name="fecha_fin_estimada" 
                                                       value="<?php echo $orden['fecha_fin_estimada']; ?>" required>
                                            </div>

                                            <div class="col-md-3 mb-3">
                                                <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                                                <select class="form-select" id="estado" name="estado" required>
                                                    <option value="activa" <?php echo $orden['estado'] == 'activa' ? 'selected' : ''; ?>>Activa</option>
                                                    <option value="en_proceso" <?php echo $orden['estado'] == 'en_proceso' ? 'selected' : ''; ?>>En Proceso</option>
                                                    <option value="completada" <?php echo $orden['estado'] == 'completada' ? 'selected' : ''; ?>>Completada</option>
                                                    <option value="cancelada" <?php echo $orden['estado'] == 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                                                </select>
                                            </div>

                                            <div class="col-md-3 mb-3">
                                                <label for="prioridad" class="form-label">Prioridad <span class="text-danger">*</span></label>
                                                <select class="form-select" id="prioridad" name="prioridad" required>
                                                    <option value="baja" <?php echo $orden['prioridad'] == 'baja' ? 'selected' : ''; ?>>Baja</option>
                                                    <option value="media" <?php echo $orden['prioridad'] == 'media' ? 'selected' : ''; ?>>Media</option>
                                                    <option value="alta" <?php echo $orden['prioridad'] == 'alta' ? 'selected' : ''; ?>>Alta</option>
                                                    <option value="urgente" <?php echo $orden['prioridad'] == 'urgente' ? 'selected' : ''; ?>>Urgente</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row" id="fecha-fin-real-container" style="display: none;">
                                            <div class="col-md-6 mb-3">
                                                <label for="fecha_fin_real" class="form-label">Fecha Fin Real</label>
                                                <input type="date" class="form-control" id="fecha_fin_real" name="fecha_fin_real" 
                                                       value="<?php echo $orden['fecha_fin_real'] ?? ''; ?>">
                                                <small class="text-muted">Solo para órdenes completadas</small>
                                            </div>
                                        </div>

                                        <div class="text-end mt-3">
                                            <a href="ordenes-produccion.php" class="btn btn-light me-2">
                                                <i class="mdi mdi-close me-1"></i> Cancelar
                                            </a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="mdi mdi-content-save me-1"></i> Guardar Cambios
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
        // Mostrar/ocultar fecha fin real según estado
        const estadoSelect = document.getElementById('estado');
        const fechaFinRealContainer = document.getElementById('fecha-fin-real-container');
        const fechaFinRealInput = document.getElementById('fecha_fin_real');

        function toggleFechaFinReal() {
            if (estadoSelect.value === 'completada') {
                fechaFinRealContainer.style.display = 'block';
                if (!fechaFinRealInput.value) {
                    fechaFinRealInput.value = new Date().toISOString().split('T')[0];
                }
            } else {
                fechaFinRealContainer.style.display = 'none';
            }
        }

        estadoSelect.addEventListener('change', toggleFechaFinReal);
        toggleFechaFinReal(); // Ejecutar al cargar

        // Validación de fechas
        document.getElementById('fecha_inicio').addEventListener('change', function() {
            const fechaFinInput = document.getElementById('fecha_fin_estimada');
            fechaFinInput.min = this.value;
        });
    </script>

</body>
</html>
