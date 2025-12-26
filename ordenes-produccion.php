<?php
// ordenes-produccion.php - Vista de órdenes de producción
session_start();
require_once 'includes/Class-usuario.php';
require_once 'includes/Class-ordenes-produccion.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    header("Location: login.php");
    exit;
}

$OrdenProduccion_class = new OrdenProduccion();

// Filtros
$filtros = [];
if (isset($_GET['estado']) && !empty($_GET['estado'])) {
    $filtros['estado'] = $_GET['estado'];
}

$ordenes = $OrdenProduccion_class->obtenerOrdenes($filtros);
$resumen_estados = $OrdenProduccion_class->obtenerResumenPorEstado();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Órdenes de Producción | Time Production</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="assets/images/favicon.ico" />
    <script src="assets/js/hyper-config.js"></script>
    <link href="assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
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
                                <?php if (isset($_SESSION['user_rol']) && ($_SESSION['user_rol'] === 'administrador' || $_SESSION['user_rol'] === 'Administrador')): ?>
                                <div class="page-title-right">
                                    <a href="add-orden-produccion.php" class="btn btn-primary">
                                        <i class="ri-add-circle-line"></i> Nueva Orden
                                    </a>
                                </div>
                                <?php endif; ?>
                                <h4 class="page-title">Órdenes de Producción</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Resumen por Estados -->
                    <div class="row">
                        <?php 
                        $estados_config = [
                            'activa' => ['icon' => 'ri-play-circle-line', 'color' => 'primary', 'label' => 'Activas'],
                            'en_proceso' => ['icon' => 'ri-time-line', 'color' => 'info', 'label' => 'En Proceso'],
                            'pausada' => ['icon' => 'ri-pause-circle-line', 'color' => 'warning', 'label' => 'Pausadas'],
                            'completada' => ['icon' => 'ri-checkbox-circle-line', 'color' => 'success', 'label' => 'Completadas'],
                            'cancelada' => ['icon' => 'ri-close-circle-line', 'color' => 'danger', 'label' => 'Canceladas']
                        ];
                        
                        foreach ($resumen_estados as $estado): 
                            $config = $estados_config[$estado['estado']] ?? ['icon' => 'ri-file-list-line', 'color' => 'secondary', 'label' => ucfirst($estado['estado'])];
                        ?>
                        <div class="col-md-6 col-xl-2-4">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="<?php echo $config['icon']; ?> widget-icon bg-<?php echo $config['color']; ?>-lighten text-<?php echo $config['color']; ?>"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0"><?php echo $config['label']; ?></h5>
                                    <h3 class="mt-3 mb-3"><?php echo $estado['total']; ?></h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-nowrap">
                                            <?php echo number_format($estado['suma_cantidad_objetivo']); ?> unidades objetivo
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Filtros -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <form method="GET" action="" class="row g-3">
                                        <div class="col-md-4">
                                            <label for="estado" class="form-label">Estado</label>
                                            <select class="form-select" id="estado" name="estado">
                                                <option value="">Todos los estados</option>
                                                <option value="activa" <?php echo (isset($_GET['estado']) && $_GET['estado'] === 'activa') ? 'selected' : ''; ?>>Activa</option>
                                                <option value="en_proceso" <?php echo (isset($_GET['estado']) && $_GET['estado'] === 'en_proceso') ? 'selected' : ''; ?>>En Proceso</option>
                                                <option value="pausada" <?php echo (isset($_GET['estado']) && $_GET['estado'] === 'pausada') ? 'selected' : ''; ?>>Pausada</option>
                                                <option value="completada" <?php echo (isset($_GET['estado']) && $_GET['estado'] === 'completada') ? 'selected' : ''; ?>>Completada</option>
                                                <option value="cancelada" <?php echo (isset($_GET['estado']) && $_GET['estado'] === 'cancelada') ? 'selected' : ''; ?>>Cancelada</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">&nbsp;</label>
                                            <div>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="ri-search-line"></i> Filtrar
                                                </button>
                                                <a href="ordenes-produccion.php" class="btn btn-secondary">
                                                    <i class="ri-refresh-line"></i> Limpiar
                                                </a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Órdenes -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">Lista de Órdenes</h4>
                                    
                                    <?php if (count($ordenes) > 0): ?>
                                    <div class="table-responsive">
                                        <table id="tabla-ordenes" class="table table-striped table-hover dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th>Código OP</th>
                                                    <th>Producto</th>
                                                    <th>Cliente</th>
                                                    <th>Cantidad</th>
                                                    <th>Fechas</th>
                                                    <th>Horas Trabajadas</th>
                                                    <th>Trabajadores</th>
                                                    <th>Estado</th>
                                                    <th>Prioridad</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($ordenes as $orden): ?>
                                                <tr>
                                                    <td><strong><?php echo htmlspecialchars($orden['codigo_op']); ?></strong></td>
                                                    <td><?php echo htmlspecialchars($orden['nombre_producto']); ?></td>
                                                    <td><?php echo htmlspecialchars($orden['cliente']); ?></td>
                                                    <td>
                                                        <?php echo number_format($orden['cantidad_objetivo']); ?> unidades
                                                    </td>
                                                    <td>
                                                        <small>
                                                            <strong>Inicio:</strong> <?php echo date('d/m/Y', strtotime($orden['fecha_inicio'])); ?><br>
                                                            <strong>Fin Est.:</strong> <?php echo date('d/m/Y', strtotime($orden['fecha_fin_estimada'])); ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">
                                                            <?php echo number_format($orden['horas_trabajadas'], 2); ?> hrs
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary">
                                                            <?php echo $orden['total_usuarios']; ?> trabajadores
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $estado_class = match($orden['estado']) {
                                                            'activa' => 'primary',
                                                            'en_proceso' => 'info',
                                                            'pausada' => 'warning',
                                                            'completada' => 'success',
                                                            'cancelada' => 'danger',
                                                            default => 'secondary'
                                                        };
                                                        ?>
                                                        <span class="badge bg-<?php echo $estado_class; ?>">
                                                            <?php echo ucfirst(str_replace('_', ' ', $orden['estado'])); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $prioridad_class = match($orden['prioridad']) {
                                                            'urgente' => 'danger',
                                                            'alta' => 'warning',
                                                            'media' => 'info',
                                                            'baja' => 'secondary',
                                                            default => 'secondary'
                                                        };
                                                        ?>
                                                        <span class="badge bg-<?php echo $prioridad_class; ?>">
                                                            <?php echo ucfirst($orden['prioridad']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="detalle-orden-produccion.php?id=<?php echo $orden['id']; ?>" 
                                                           class="btn btn-sm btn-info" title="Ver detalle">
                                                            <i class="ri-eye-line"></i>
                                                        </a>
                                                        <?php if (isset($_SESSION['user_rol']) && ($_SESSION['user_rol'] === 'administrador' || $_SESSION['user_rol'] === 'Administrador')): ?>
                                                        <a href="update-orden-produccion.php?id=<?php echo $orden['id']; ?>" 
                                                           class="btn btn-sm btn-warning" title="Editar">
                                                            <i class="ri-edit-line"></i>
                                                        </a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="ri-information-line"></i>
                                        No hay órdenes de producción registradas.
                                        <?php if (isset($_SESSION['user_rol']) && ($_SESSION['user_rol'] === 'administrador' || $_SESSION['user_rol'] === 'Administrador')): ?>
                                        <a href="add-orden-produccion.php" class="alert-link">Crear nueva orden</a>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
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
    <script src="assets/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#tabla-ordenes').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            order: [[8, 'asc'], [0, 'desc']],
            pageLength: 25
        });
    });
    </script>

</body>
</html>
