<?php
// reporte-produccion.php - Reporte de análisis de productividad
session_start();
require_once 'includes/Class-usuario.php';
require_once 'includes/Class-horas-produccion.php';
require_once 'includes/Class-costos.php';
require_once 'includes/conn-db.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    header("Location: login.php");
    exit;
}

$es_admin_o_produccion = isset($_SESSION['user_rol']) && 
    in_array($_SESSION['user_rol'], ['produccion', 'Produccion', 'administrador', 'Administrador', 'root', 'Root']);

if (!$es_admin_o_produccion) {
    header("Location: index.php");
    exit;
}

$database = new Database();
$conn = $database->getConnection();
$Costos_class = new Costos();

// Obtener filtros
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-t');
$trabajador_id = $_GET['trabajador_id'] ?? '';
$op_id = $_GET['op_id'] ?? '';

// Obtener lista de trabajadores para el filtro
$query_trabajadores = "SELECT DISTINCT u.id, u.nombre_completo 
                       FROM usuarios u 
                       INNER JOIN registros_horas_produccion rhp ON u.id = rhp.usuario_id
                       WHERE u.is_active = 1
                       ORDER BY u.nombre_completo";
$stmt_trabajadores = $conn->prepare($query_trabajadores);
$stmt_trabajadores->execute();
$lista_trabajadores = $stmt_trabajadores->fetchAll(PDO::FETCH_ASSOC);

// Obtener lista de OPs para el filtro
$query_ops = "SELECT DISTINCT op.id, op.codigo_op, op.nombre_producto 
              FROM ordenes_produccion op 
              INNER JOIN registros_horas_produccion rhp ON op.id = rhp.orden_produccion_id
              ORDER BY op.codigo_op";
$stmt_ops = $conn->prepare($query_ops);
$stmt_ops->execute();
$lista_ops = $stmt_ops->fetchAll(PDO::FETCH_ASSOC);

// Construir consulta principal con filtros
$query = "SELECT 
            rhp.*,
            u.nombre_completo,
            u.cargo_id,
            c.nombre as nombre_cargo,
            d.nombre as nombre_departamento,
            op.codigo_op,
            op.nombre_producto,
            op.cliente
          FROM registros_horas_produccion rhp
          INNER JOIN usuarios u ON rhp.usuario_id = u.id
          LEFT JOIN cargos c ON u.cargo_id = c.id
          LEFT JOIN departamentos d ON u.departamento_id = d.id
          LEFT JOIN ordenes_produccion op ON rhp.orden_produccion_id = op.id
          WHERE rhp.fecha BETWEEN :fecha_inicio AND :fecha_fin";

// Agregar filtro por trabajador si está seleccionado
if (!empty($trabajador_id)) {
    $query .= " AND rhp.usuario_id = :trabajador_id";
}

// Agregar filtro por OP si está seleccionado
if (!empty($op_id)) {
    $query .= " AND rhp.orden_produccion_id = :op_id";
}

$query .= " ORDER BY rhp.fecha ASC";

$stmt = $conn->prepare($query);
$stmt->bindParam(':fecha_inicio', $fecha_inicio);
$stmt->bindParam(':fecha_fin', $fecha_fin);

// Bind de parámetros opcionales
if (!empty($trabajador_id)) {
    $stmt->bindParam(':trabajador_id', $trabajador_id);
}
if (!empty($op_id)) {
    $stmt->bindParam(':op_id', $op_id);
}

$stmt->execute();
$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Inicializar arrays para análisis
$totales_por_op = [];
$totales_por_trabajador = [];
$totales_por_departamento = [];
$distribucion_horas = ['HR' => 0, 'HED' => 0, 'HEN' => 0, 'HEFD' => 0, 'HEFN' => 0];
$costo_total = 0;
$horas_totales = 0;

// Procesar datos
foreach ($registros as $registro) {
    $codigo_op = $registro['codigo_op'] ?? 'Sin OP';
    $nombre_producto = $registro['nombre_producto'] ?? 'Sin producto';
    $trabajador = $registro['nombre_completo'];
    $departamento = $registro['nombre_departamento'] ?? 'Sin departamento';
    $cargo_id = $registro['cargo_id'];
    
    // Calcular costos por registro
    $costo_registro = 0;
    if ($cargo_id) {
        $costo_registro += $Costos_class->calcularCostoHora($cargo_id, 'hr') * $registro['hr'];
        $costo_registro += $Costos_class->calcularCostoHora($cargo_id, 'hed') * $registro['hed'];
        $costo_registro += $Costos_class->calcularCostoHora($cargo_id, 'hen') * $registro['hen'];
        $costo_registro += $Costos_class->calcularCostoHora($cargo_id, 'hefd') * $registro['hefd'];
        $costo_registro += $Costos_class->calcularCostoHora($cargo_id, 'hefn') * $registro['hefn'];
    }
    
    $horas_registro = $registro['total_horas'];
    
    // Totales por OP
    if (!isset($totales_por_op[$codigo_op])) {
        $totales_por_op[$codigo_op] = [
            'codigo' => $codigo_op,
            'nombre' => $nombre_producto,
            'cliente' => $registro['cliente'] ?? '',
            'horas' => 0,
            'costo' => 0,
            'registros' => 0
        ];
    }
    $totales_por_op[$codigo_op]['horas'] += $horas_registro;
    $totales_por_op[$codigo_op]['costo'] += $costo_registro;
    $totales_por_op[$codigo_op]['registros']++;
    
    // Totales por trabajador
    if (!isset($totales_por_trabajador[$trabajador])) {
        $totales_por_trabajador[$trabajador] = [
            'nombre' => $trabajador,
            'cargo' => $registro['nombre_cargo'] ?? '',
            'departamento' => $departamento,
            'horas' => 0,
            'costo' => 0,
            'dias_trabajados' => []
        ];
    }
    $totales_por_trabajador[$trabajador]['horas'] += $horas_registro;
    $totales_por_trabajador[$trabajador]['costo'] += $costo_registro;
    $totales_por_trabajador[$trabajador]['dias_trabajados'][$registro['fecha']] = true;
    
    // Totales por departamento
    if (!isset($totales_por_departamento[$departamento])) {
        $totales_por_departamento[$departamento] = [
            'nombre' => $departamento,
            'horas' => 0,
            'costo' => 0
        ];
    }
    $totales_por_departamento[$departamento]['horas'] += $horas_registro;
    $totales_por_departamento[$departamento]['costo'] += $costo_registro;
    
    // Distribución de tipos de horas
    $distribucion_horas['HR'] += $registro['hr'];
    $distribucion_horas['HED'] += $registro['hed'];
    $distribucion_horas['HEN'] += $registro['hen'];
    $distribucion_horas['HEFD'] += $registro['hefd'];
    $distribucion_horas['HEFN'] += $registro['hefn'];
    
    $costo_total += $costo_registro;
    $horas_totales += $horas_registro;
}

// Ordenar por horas (descendente)
usort($totales_por_op, function($a, $b) {
    return $b['horas'] <=> $a['horas'];
});

usort($totales_por_trabajador, function($a, $b) {
    return $b['horas'] <=> $a['horas'];
});

usort($totales_por_departamento, function($a, $b) {
    return $b['horas'] <=> $a['horas'];
});

// Calcular días trabajados por trabajador
foreach ($totales_por_trabajador as &$trabajador) {
    $trabajador['dias_trabajados'] = count($trabajador['dias_trabajados']);
}

// Top 5 de cada categoría
$top_ops = array_slice($totales_por_op, 0, 5);
$top_trabajadores = array_slice($totales_por_trabajador, 0, 10);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Reporte de Producción | Time Production</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <link rel="shortcut icon" href="assets/images/favicon.ico" />
    <script src="assets/js/hyper-config.js"></script>
    <link href="assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    
    <!-- DataTables -->
    <link href="assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="wrapper">
        <?php include "includes/header.php"; ?>
        <?php include "includes/sidebar.php"; ?>

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    
                    <!-- Page Title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Reporte de Producción</li>
                                    </ol>
                                </div>
                                <h4 class="page-title">Análisis de Productividad</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <form method="GET" action="" class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Fecha Inicio</label>
                                            <input type="date" name="fecha_inicio" class="form-control" value="<?php echo $fecha_inicio; ?>" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Fecha Fin</label>
                                            <input type="date" name="fecha_fin" class="form-control" value="<?php echo $fecha_fin; ?>" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Trabajador</label>
                                            <select name="trabajador_id" class="form-select">
                                                <option value="">Todos los trabajadores</option>
                                                <?php foreach ($lista_trabajadores as $trab): ?>
                                                    <option value="<?php echo $trab['id']; ?>" <?php echo ($trabajador_id == $trab['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($trab['nombre_completo']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Orden de Producción</label>
                                            <select name="op_id" class="form-select">
                                                <option value="">Todas las OPs</option>
                                                <?php foreach ($lista_ops as $op): ?>
                                                    <option value="<?php echo $op['id']; ?>" <?php echo ($op_id == $op['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($op['codigo_op'] . ' - ' . $op['nombre_producto']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="ri-filter-line"></i> Filtrar
                                            </button>
                                            <a href="reporte-produccion.php" class="btn btn-secondary">
                                                <i class="ri-refresh-line"></i> Limpiar
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjetas de Resumen -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="ri-time-line widget-icon bg-success-subtle text-success"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0">Total Horas</h5>
                                    <h3 class="mt-3 mb-3"><?php echo number_format($horas_totales, 1); ?></h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-nowrap">Registros: <?php echo count($registros); ?></span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="ri-money-dollar-circle-line widget-icon bg-warning-subtle text-warning"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0">Costo Total</h5>
                                    <h3 class="mt-3 mb-3">$<?php echo number_format($costo_total, 0); ?></h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-nowrap">Costo/Hora: $<?php echo $horas_totales > 0 ? number_format($costo_total / $horas_totales, 0) : 0; ?></span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="ri-stack-line widget-icon bg-info-subtle text-info"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0">Proyectos Activos</h5>
                                    <h3 class="mt-3 mb-3"><?php echo count($totales_por_op); ?></h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-nowrap">Órdenes de Producción</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="ri-team-line widget-icon bg-primary-subtle text-primary"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0">Trabajadores</h5>
                                    <h3 class="mt-3 mb-3"><?php echo count($totales_por_trabajador); ?></h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-nowrap">Personal activo</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Distribución de Horas -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">Distribución de Tipos de Horas</h4>
                                    <div class="row">
                                        <div class="col-md-2 text-center">
                                            <h5>HR</h5>
                                            <h3 class="text-success"><?php echo number_format($distribucion_horas['HR'], 1); ?></h3>
                                            <small class="text-muted">Regular</small>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <h5>HED</h5>
                                            <h3 class="text-warning"><?php echo number_format($distribucion_horas['HED'], 1); ?></h3>
                                            <small class="text-muted">Extra Diurna</small>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <h5>HEN</h5>
                                            <h3 class="text-info"><?php echo number_format($distribucion_horas['HEN'], 1); ?></h3>
                                            <small class="text-muted">Extra Nocturna</small>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <h5>HEFD</h5>
                                            <h3 class="text-primary"><?php echo number_format($distribucion_horas['HEFD'], 1); ?></h3>
                                            <small class="text-muted">Extra Festiva Diurna</small>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <h5>HEFN</h5>
                                            <h3 class="text-danger"><?php echo number_format($distribucion_horas['HEFN'], 1); ?></h3>
                                            <small class="text-muted">Extra Festiva Nocturna</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top 5 Proyectos -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">Top 5 Proyectos por Horas</h4>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>OP</th>
                                                    <th>Producto</th>
                                                    <th class="text-end">Horas</th>
                                                    <th class="text-end">Costo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($top_ops as $index => $op): ?>
                                                <tr>
                                                    <td><?php echo $index + 1; ?></td>
                                                    <td><strong><?php echo htmlspecialchars($op['codigo']); ?></strong></td>
                                                    <td><?php echo htmlspecialchars($op['nombre']); ?></td>
                                                    <td class="text-end"><?php echo number_format($op['horas'], 1); ?></td>
                                                    <td class="text-end">$<?php echo number_format($op['costo'], 0); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">Top 10 Trabajadores Productivos</h4>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Trabajador</th>
                                                    <th class="text-end">Horas</th>
                                                    <th class="text-end">Días</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($top_trabajadores as $index => $trabajador): ?>
                                                <tr>
                                                    <td><?php echo $index + 1; ?></td>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($trabajador['nombre']); ?></strong>
                                                        <br><small class="text-muted"><?php echo htmlspecialchars($trabajador['cargo']); ?></small>
                                                    </td>
                                                    <td class="text-end"><?php echo number_format($trabajador['horas'], 1); ?></td>
                                                    <td class="text-end"><?php echo $trabajador['dias_trabajados']; ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tablas Detalladas -->
                    <div class="row">
                        <!-- Todos los Proyectos -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">Todos los Proyectos</h4>
                                    <table id="tablaProyectos" class="table table-striped dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th>OP</th>
                                                <th>Producto</th>
                                                <th>Cliente</th>
                                                <th>Horas</th>
                                                <th>Costo</th>
                                                <th>Registros</th>
                                                <th>Costo/Hora</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($totales_por_op as $op): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($op['codigo']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($op['nombre']); ?></td>
                                                <td><?php echo htmlspecialchars($op['cliente']); ?></td>
                                                <td><?php echo number_format($op['horas'], 1); ?></td>
                                                <td>$<?php echo number_format($op['costo'], 0); ?></td>
                                                <td><?php echo $op['registros']; ?></td>
                                                <td>$<?php echo number_format($op['horas'] > 0 ? $op['costo'] / $op['horas'] : 0, 0); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Análisis por Departamento -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">Análisis por Departamento</h4>
                                    <table id="tablaDepartamentos" class="table table-striped dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th>Departamento</th>
                                                <th>Horas</th>
                                                <th>Costo</th>
                                                <th>% Horas</th>
                                                <th>% Costo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($totales_por_departamento as $depto): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($depto['nombre']); ?></strong></td>
                                                <td><?php echo number_format($depto['horas'], 1); ?></td>
                                                <td>$<?php echo number_format($depto['costo'], 0); ?></td>
                                                <td><?php echo number_format($horas_totales > 0 ? ($depto['horas'] / $horas_totales * 100) : 0, 1); ?>%</td>
                                                <td><?php echo number_format($costo_total > 0 ? ($depto['costo'] / $costo_total * 100) : 0, 1); ?>%</td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <?php include "includes/footer.php"; ?>
        </div>
    </div>

    <script src="assets/js/vendor.min.js"></script>
    <script src="assets/js/app.min.js"></script>
    
    <!-- DataTables -->
    <script src="assets/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="assets/vendor/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/vendor/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#tablaProyectos').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            order: [[3, 'desc']], // Ordenar por horas descendente
            pageLength: 25
        });

        $('#tablaDepartamentos').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            order: [[1, 'desc']], // Ordenar por horas descendente
            paging: false,
            searching: false
        });
    });
    </script>
</body>
</html>