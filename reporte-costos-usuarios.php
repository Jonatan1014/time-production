<?php
session_start();
require_once 'includes/Class-usuario.php';
require_once 'includes/Class-registros-horas.php';
require_once 'includes/Class-horas-extras.php';
require_once 'includes/Class-costos.php';
require_once 'includes/Class-configuracion.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    header("Location: login.php");
    exit;
}

$es_admin = isset($_SESSION['user_rol']) && ($_SESSION['user_rol'] === 'administrador' || $_SESSION['user_rol'] === 'Administrador');

if (!$es_admin) {
    $_SESSION['error'] = "No tiene permisos para acceder a esta página";
    header("Location: index.php");
    exit;
}

$RegistroHoras_class = new RegistroHoras();
$HorasExtras_class = new HoraExtra();
$Costos_class = new Costos();
$Configuracion_class = new Configuracion();

// Obtener filtros
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-t');

// Obtener todos los usuarios trabajadores
$usuarios = $Usuario_class->obtenerUsuarios(['rol' => 'trabajador']);

// Calcular costos por usuario
$reporte_usuarios = [];
foreach ($usuarios as $usuario) {
    $filtros = [
        'fecha_inicio' => $fecha_inicio,
        'fecha_fin' => $fecha_fin,
        'usuario_id' => $usuario['id']
    ];
    
    $costos = $Costos_class->calcularCostosReporte($filtros);
    
    $reporte_usuarios[] = [
        'usuario' => $usuario,
        'costos' => $costos
    ];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Costos por Usuario | Sistema de Horas</title>
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

                    <!-- Page Title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <button onclick="window.print()" class="btn btn-primary">
                                        <i class="mdi mdi-printer me-1"></i> Imprimir
                                    </button>
                                </div>
                                <h4 class="page-title">Reporte de Costos por Usuario</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <form method="GET" action="reporte-costos-usuarios.php" class="row g-3">
                                        <div class="col-md-5">
                                            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                                                   value="<?php echo $fecha_inicio; ?>" required>
                                        </div>
                                        
                                        <div class="col-md-5">
                                            <label for="fecha_fin" class="form-label">Fecha Fin</label>
                                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                                                   value="<?php echo $fecha_fin; ?>" required>
                                        </div>

                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="mdi mdi-magnify me-1"></i> Buscar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Costos por Usuario -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">
                                        <i class="mdi mdi-account-group text-primary me-2"></i>
                                        Detalle de Costos por Empleado
                                        <span class="badge bg-light text-dark fs-6 ms-2">
                                            <?php echo date('d/m/Y', strtotime($fecha_inicio)) . ' - ' . date('d/m/Y', strtotime($fecha_fin)); ?>
                                        </span>
                                    </h4>
                                    
                                    <div class="table-responsive">
                                        <table id="tabla-costos-usuarios" class="table table-striped table-hover dt-responsive nowrap w-100">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Usuario</th>
                                                    <th>Departamento</th>
                                                    <th>Valor Hora</th>
                                                    <th>Hrs Normales</th>
                                                    <th>Costo Normales</th>
                                                    <th>Hrs Extras Diurnas</th>
                                                    <th>Costo Diurnas</th>
                                                    <th>Hrs Extras Nocturnas</th>
                                                    <th>Costo Nocturnas</th>
                                                    <th>Total Horas</th>
                                                    <th>Costo Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $total_general = 0;
                                                $total_horas_general = 0;
                                                foreach ($reporte_usuarios as $item): 
                                                    $usuario = $item['usuario'];
                                                    $costos = $item['costos'];
                                                    $total_horas = $costos['horas_normales'] + $costos['horas_extras_diurnas'] + $costos['horas_extras_nocturnas'];
                                                    $total_general += $costos['costo_total'];
                                                    $total_horas_general += $total_horas;
                                                ?>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($usuario['nombre_completo']); ?></strong><br>
                                                        <small class="text-muted"><?php echo htmlspecialchars($usuario['email']); ?></small>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($usuario['departamento'] ?? 'N/A'); ?></td>
                                                    <td>$<?php echo number_format($usuario['valor_hora_base'], 0, ',', '.'); ?></td>
                                                    <td><?php echo number_format($costos['horas_normales'], 1); ?></td>
                                                    <td>$<?php echo number_format($costos['costo_horas_normales'], 0, ',', '.'); ?></td>
                                                    <td><?php echo number_format($costos['horas_extras_diurnas'], 1); ?></td>
                                                    <td>$<?php echo number_format($costos['costo_extras_diurnas'], 0, ',', '.'); ?></td>
                                                    <td><?php echo number_format($costos['horas_extras_nocturnas'], 1); ?></td>
                                                    <td>$<?php echo number_format($costos['costo_extras_nocturnas'], 0, ',', '.'); ?></td>
                                                    <td><strong><?php echo number_format($total_horas, 1); ?></strong></td>
                                                    <td><strong class="text-primary">$<?php echo number_format($costos['costo_total'], 0, ',', '.'); ?></strong></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot class="table-light">
                                                <tr>
                                                    <th colspan="9" class="text-end">TOTALES:</th>
                                                    <th><?php echo number_format($total_horas_general, 1); ?> hrs</th>
                                                    <th class="text-primary">$<?php echo number_format($total_general, 0, ',', '.'); ?></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
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
            $('#tabla-costos-usuarios').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                order: [[10, 'desc']], // Ordenar por costo total descendente
                pageLength: 25,
                dom: 'Bfrtip',
                footerCallback: function() {
                    // Ya tenemos el footer con totales en PHP
                }
            });
        });
    </script>

    <style media="print">
        .btn, .page-title-right, .sidebar, .navbar-custom, .card-title i {
            display: none !important;
        }
        
        .content-page {
            margin-left: 0 !important;
        }
    </style>

</body>
</html>
