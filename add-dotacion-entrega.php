<?php
// add-dotacion-entrega.php - Registrar entrega de dotacion
session_start();
require_once 'includes/Class-usuario.php';
require_once 'includes/Class-dotacion.php';
require_once 'includes/Class-configuracion.php';

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

$Dotacion_class = new Dotacion();
$Configuracion_class = new Configuracion();

$usuarios = $Dotacion_class->obtenerUsuariosActivos();
$items = $Dotacion_class->obtenerItems(true);

$intervalo = (int)$Configuracion_class->obtenerValor('dotacion_intervalo_meses', 4);
if ($intervalo <= 0) {
    $intervalo = 1;
}

$usuario_seleccionado = (int)($_GET['usuario_id'] ?? 0);
$fecha_entrega = date('Y-m-d');

$pageTitle = "Registrar Entrega de Dotacion";
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
                                    <a href="dotaciones.php" class="btn btn-secondary">
                                        <i class="mdi mdi-arrow-left me-1"></i> Volver
                                    </a>
                                </div>
                                <h4 class="page-title">
                                    <i class="mdi mdi-clipboard-plus-outline me-1"></i>
                                    Registrar Entrega de Dotacion
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
                                    <form id="form-registrar-dotacion" action="action/add-dotacion-entrega.php" method="POST">

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">
                                                    <i class="mdi mdi-account-outline me-1"></i>Usuario <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select" name="usuario_id" required>
                                                    <option value="">Seleccionar usuario</option>
                                                    <?php foreach ($usuarios as $usuario): ?>
                                                        <option value="<?php echo $usuario['id']; ?>" <?php echo ($usuario_seleccionado === (int)$usuario['id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($usuario['nombre_completo']); ?> (@<?php echo htmlspecialchars($usuario['username']); ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">
                                                    <i class="mdi mdi-calendar me-1"></i>Fecha de Entrega <span class="text-danger">*</span>
                                                </label>
                                                <input type="date" class="form-control" name="fecha_entrega" id="fecha-entrega" value="<?php echo $fecha_entrega; ?>" required>
                                            </div>

                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">
                                                    <i class="mdi mdi-calendar-clock me-1"></i>Proxima Entrega
                                                </label>
                                                <input type="date" class="form-control" id="proxima-entrega" value="" readonly>
                                                <small class="text-muted">Intervalo: <?php echo $intervalo; ?> mes(es)</small>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <label class="form-label">
                                                    <i class="mdi mdi-note-text-outline me-1"></i>Observaciones
                                                </label>
                                                <textarea class="form-control" name="observaciones" rows="2" placeholder="Notas adicionales"></textarea>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <h5 class="mb-3">
                                                    <i class="mdi mdi-package-variant-closed me-1"></i>
                                                    Items de Dotacion <span class="text-danger">*</span>
                                                </h5>

                                                <?php if (count($items) > 0): ?>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Item</th>
                                                                <th>Descripcion</th>
                                                                <th style="width: 140px;">Cantidad</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($items as $item): ?>
                                                            <tr>
                                                                <td><strong><?php echo htmlspecialchars($item['nombre']); ?></strong></td>
                                                                <td><?php echo htmlspecialchars($item['descripcion'] ?? ''); ?></td>
                                                                <td>
                                                                    <input type="number" class="form-control" name="items[<?php echo $item['id']; ?>]" min="0" value="0">
                                                                </td>
                                                            </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <?php else: ?>
                                                <div class="alert alert-warning">
                                                    <i class="mdi mdi-alert me-2"></i>
                                                    No hay items activos de dotacion. Debes crear items antes de registrar entregas.
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-success btn-lg" <?php echo (count($items) === 0) ? 'disabled' : ''; ?>>
                                                    <i class="mdi mdi-content-save me-1"></i> Registrar Entrega
                                                </button>
                                                <a href="dotaciones.php" class="btn btn-secondary btn-lg ms-2">
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
    function calcularProximaEntrega(fecha, intervalo) {
        if (!fecha) {
            return '';
        }

        const partes = fecha.split('-');
        if (partes.length !== 3) {
            return '';
        }

        const year = parseInt(partes[0], 10);
        const month = parseInt(partes[1], 10) - 1;
        const day = parseInt(partes[2], 10);

        const fechaEntrega = new Date(year, month, day);
        fechaEntrega.setMonth(fechaEntrega.getMonth() + intervalo);

        const y = fechaEntrega.getFullYear();
        const m = String(fechaEntrega.getMonth() + 1).padStart(2, '0');
        const d = String(fechaEntrega.getDate()).padStart(2, '0');

        return `${y}-${m}-${d}`;
    }

    $(document).ready(function() {
        const intervalo = <?php echo $intervalo; ?>;
        const fechaInput = $('#fecha-entrega');
        const proximaInput = $('#proxima-entrega');

        function actualizarProxima() {
            const fecha = fechaInput.val();
            proximaInput.val(calcularProximaEntrega(fecha, intervalo));
        }

        fechaInput.on('change', actualizarProxima);
        actualizarProxima();

        $('#form-registrar-dotacion').on('submit', function(e) {
            let total = 0;
            $('input[name^="items["]').each(function() {
                const valor = parseInt($(this).val(), 10) || 0;
                total += valor;
            });

            if (total <= 0) {
                e.preventDefault();
                alert('Debes registrar al menos un item con cantidad mayor a 0.');
                return false;
            }

            return true;
        });
    });
    </script>

</body>
</html>
