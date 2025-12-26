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
    header("Location: index.php");
    exit;
}

require_once 'includes/conn-db.php';
$database = new Database();
$conn = $database->getConnection();

// Obtener horarios laborales
$query_horarios = "SELECT * FROM horarios_laborales ORDER BY 
                   FIELD(dia_semana, 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo')";
$stmt_horarios = $conn->prepare($query_horarios);
$stmt_horarios->execute();
$horarios = $stmt_horarios->fetchAll(PDO::FETCH_ASSOC);

// Obtener configuraciones del sistema
$query_config = "SELECT * FROM configuracion_sistema ORDER BY categoria, clave";
$stmt_config = $conn->prepare($query_config);
$stmt_config->execute();
$configuraciones = $stmt_config->fetchAll(PDO::FETCH_ASSOC);

// Agrupar configuraciones por categoría
$config_por_categoria = [];
foreach ($configuraciones as $config) {
    $categoria = $config['categoria'] ?? 'general';
    if (!isset($config_por_categoria[$categoria])) {
        $config_por_categoria[$categoria] = [];
    }
    $config_por_categoria[$categoria][] = $config;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Configuración del Sistema | Sistema de Horas</title>
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
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
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
                                <h4 class="page-title">Configuración del Sistema</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs de Configuración -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <ul class="nav nav-tabs nav-bordered mb-3">
                                        <li class="nav-item">
                                            <a href="#horarios" data-bs-toggle="tab" aria-expanded="false" class="nav-link active">
                                                <i class="mdi mdi-clock-outline me-1"></i>
                                                Horarios Laborales
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#costos" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                                <i class="mdi mdi-currency-usd me-1"></i>
                                                Costos y Tarifas
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#integraciones" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                                <i class="mdi mdi-cloud-sync me-1"></i>
                                                Integraciones
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#sistema" data-bs-toggle="tab" aria-expanded="true" class="nav-link">
                                                <i class="mdi mdi-cog-outline me-1"></i>
                                                Configuración General
                                            </a>
                                        </li>
                                    </ul>

                                    <div class="tab-content">
                                        <!-- Horarios Laborales -->
                                        <div class="tab-pane show active" id="horarios">
                                            <h4 class="header-title mb-3">Horarios de Trabajo por Día</h4>
                                            
                                            <div class="alert alert-info">
                                                <i class="mdi mdi-information me-2"></i>
                                                Define los horarios de trabajo para cada día de la semana. Esto se usa para validar registros de horas.
                                            </div>

                                            <form method="POST" action="action/actualizar-configuracion.php">
                                                <input type="hidden" name="tipo" value="horarios">
                                                
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-centered mb-0">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Día</th>
                                                                <th>Laborable</th>
                                                                <th>Mañana (Inicio)</th>
                                                                <th>Mañana (Fin)</th>
                                                                <th>Tarde (Inicio)</th>
                                                                <th>Tarde (Fin)</th>
                                                                <th>Horas Totales</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php 
                                                            $dias_nombres = [
                                                                'lunes' => 'Lunes',
                                                                'martes' => 'Martes',
                                                                'miercoles' => 'Miércoles',
                                                                'jueves' => 'Jueves',
                                                                'viernes' => 'Viernes',
                                                                'sabado' => 'Sábado',
                                                                'domingo' => 'Domingo'
                                                            ];
                                                            
                                                            foreach ($horarios as $horario): 
                                                            ?>
                                                            <tr>
                                                                <td>
                                                                    <strong><?php echo $dias_nombres[$horario['dia_semana']]; ?></strong>
                                                                    <input type="hidden" name="horarios[<?php echo $horario['id']; ?>][id]" value="<?php echo $horario['id']; ?>">
                                                                </td>
                                                                <td>
                                                                    <div class="form-check form-switch">
                                                                        <input type="checkbox" class="form-check-input" 
                                                                               name="horarios[<?php echo $horario['id']; ?>][es_laborable]" 
                                                                               id="laborable_<?php echo $horario['id']; ?>"
                                                                               value="1"
                                                                               <?php echo $horario['es_laborable'] ? 'checked' : ''; ?>
                                                                               onchange="toggleHorario(<?php echo $horario['id']; ?>)">
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <input type="time" class="form-control form-control-sm horario-input" 
                                                                           name="horarios[<?php echo $horario['id']; ?>][hora_inicio_manana]" 
                                                                           id="inicio_manana_<?php echo $horario['id']; ?>"
                                                                           value="<?php echo $horario['hora_inicio_manana']; ?>"
                                                                           <?php echo !$horario['es_laborable'] ? 'disabled' : ''; ?>>
                                                                </td>
                                                                <td>
                                                                    <input type="time" class="form-control form-control-sm horario-input" 
                                                                           name="horarios[<?php echo $horario['id']; ?>][hora_fin_manana]" 
                                                                           id="fin_manana_<?php echo $horario['id']; ?>"
                                                                           value="<?php echo $horario['hora_fin_manana']; ?>"
                                                                           <?php echo !$horario['es_laborable'] ? 'disabled' : ''; ?>>
                                                                </td>
                                                                <td>
                                                                    <input type="time" class="form-control form-control-sm horario-input" 
                                                                           name="horarios[<?php echo $horario['id']; ?>][hora_inicio_tarde]" 
                                                                           id="inicio_tarde_<?php echo $horario['id']; ?>"
                                                                           value="<?php echo $horario['hora_inicio_tarde']; ?>"
                                                                           <?php echo !$horario['es_laborable'] ? 'disabled' : ''; ?>>
                                                                </td>
                                                                <td>
                                                                    <input type="time" class="form-control form-control-sm horario-input" 
                                                                           name="horarios[<?php echo $horario['id']; ?>][hora_fin_tarde]" 
                                                                           id="fin_tarde_<?php echo $horario['id']; ?>"
                                                                           value="<?php echo $horario['hora_fin_tarde']; ?>"
                                                                           <?php echo !$horario['es_laborable'] ? 'disabled' : ''; ?>>
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control form-control-sm" 
                                                                           name="horarios[<?php echo $horario['id']; ?>][horas_totales]" 
                                                                           step="0.25" min="0" max="24"
                                                                           value="<?php echo $horario['horas_totales']; ?>"
                                                                           <?php echo !$horario['es_laborable'] ? 'disabled' : ''; ?>
                                                                           id="horas_<?php echo $horario['id']; ?>">
                                                                </td>
                                                            </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div class="text-end mt-3">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="mdi mdi-content-save me-1"></i> Guardar Horarios
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Costos y Tarifas -->
                                        <div class="tab-pane" id="costos">
                                            <h4 class="header-title mb-3">Configuración de Costos y Tarifas</h4>
                                            
                                            <div class="alert alert-info">
                                                <i class="mdi mdi-information me-2"></i>
                                                Configure los horarios de turnos y porcentajes de recargo para calcular costos de horas extras.
                                            </div>

                                            <form method="POST" action="action/actualizar-configuracion.php">
                                                <input type="hidden" name="tipo" value="costos">
                                                
                                                <div class="card mb-3">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-primary mb-3">
                                                            <i class="mdi mdi-weather-sunny me-1"></i>
                                                            Horarios de Turnos
                                                        </h5>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Hora Inicio Turno Diurno</label>
                                                                    <?php
                                                                    $hora_diurna_inicio = '';
                                                                    foreach ($configuraciones as $c) {
                                                                        if ($c['clave'] === 'hora_diurna_inicio') {
                                                                            $hora_diurna_inicio = $c['valor'];
                                                                            echo '<input type="hidden" name="configs[' . $c['id'] . '][id]" value="' . $c['id'] . '">';
                                                                            break;
                                                                        }
                                                                    }
                                                                    ?>
                                                                    <input type="time" class="form-control" 
                                                                           name="configs_hora_diurna_inicio" 
                                                                           value="<?php echo htmlspecialchars($hora_diurna_inicio); ?>"
                                                                           required>
                                                                    <small class="text-muted">Ejemplo: 06:00 (6:00 AM)</small>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Hora Fin Turno Diurno</label>
                                                                    <?php
                                                                    $hora_diurna_fin = '';
                                                                    foreach ($configuraciones as $c) {
                                                                        if ($c['clave'] === 'hora_diurna_fin') {
                                                                            $hora_diurna_fin = $c['valor'];
                                                                            echo '<input type="hidden" name="configs[' . $c['id'] . '][id]" value="' . $c['id'] . '">';
                                                                            break;
                                                                        }
                                                                    }
                                                                    ?>
                                                                    <input type="time" class="form-control" 
                                                                           name="configs_hora_diurna_fin" 
                                                                           value="<?php echo htmlspecialchars($hora_diurna_fin); ?>"
                                                                           required>
                                                                    <small class="text-muted">Ejemplo: 18:00 (6:00 PM)</small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="alert alert-warning">
                                                            <i class="mdi mdi-lightbulb-outline me-2"></i>
                                                            <strong>Turno Nocturno:</strong> Se considera de <?php echo $hora_diurna_fin; ?> a <?php echo $hora_diurna_inicio; ?> del día siguiente.
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card mb-3">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-primary mb-3">
                                                            <i class="mdi mdi-percent me-1"></i>
                                                            Porcentajes de Recargo
                                                        </h5>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Recargo Hora Extra Diurna (%)</label>
                                                                    <?php
                                                                    $porcentaje_extra_diurna = '';
                                                                    foreach ($configuraciones as $c) {
                                                                        if ($c['clave'] === 'porcentaje_extra_diurna') {
                                                                            $porcentaje_extra_diurna = $c['valor'];
                                                                            echo '<input type="hidden" name="configs[' . $c['id'] . '][id]" value="' . $c['id'] . '">';
                                                                            break;
                                                                        }
                                                                    }
                                                                    ?>
                                                                    <div class="input-group">
                                                                        <input type="number" class="form-control" 
                                                                               name="configs_porcentaje_extra_diurna" 
                                                                               value="<?php echo htmlspecialchars($porcentaje_extra_diurna); ?>"
                                                                               step="0.1" min="0" max="200" required>
                                                                        <span class="input-group-text">%</span>
                                                                    </div>
                                                                    <small class="text-muted">Recargo aplicado sobre el valor hora base (ej: 25% = 1.25x)</small>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Recargo Hora Extra Nocturna (%)</label>
                                                                    <?php
                                                                    $porcentaje_extra_nocturna = '';
                                                                    foreach ($configuraciones as $c) {
                                                                        if ($c['clave'] === 'porcentaje_extra_nocturna') {
                                                                            $porcentaje_extra_nocturna = $c['valor'];
                                                                            echo '<input type="hidden" name="configs[' . $c['id'] . '][id]" value="' . $c['id'] . '">';
                                                                            break;
                                                                        }
                                                                    }
                                                                    ?>
                                                                    <div class="input-group">
                                                                        <input type="number" class="form-control" 
                                                                               name="configs_porcentaje_extra_nocturna" 
                                                                               value="<?php echo htmlspecialchars($porcentaje_extra_nocturna); ?>"
                                                                               step="0.1" min="0" max="200" required>
                                                                        <span class="input-group-text">%</span>
                                                                    </div>
                                                                    <small class="text-muted">Recargo aplicado sobre el valor hora base (ej: 75% = 1.75x)</small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="card bg-light">
                                                            <div class="card-body">
                                                                <h6 class="card-title">Ejemplo de Cálculo:</h6>
                                                                <p class="mb-2"><strong>Valor Hora Base:</strong> $7.500</p>
                                                                <p class="mb-2">
                                                                    <strong>Hora Extra Diurna (+<?php echo $porcentaje_extra_diurna; ?>%):</strong> 
                                                                    $7.500 + <?php echo $porcentaje_extra_diurna; ?>% = $<?php echo number_format(7500 + ($porcentaje_extra_diurna * 7500 / 100), 0, ',', '.'); ?>
                                                                </p>
                                                                <p class="mb-0">
                                                                    <strong>Hora Extra Nocturna (+<?php echo $porcentaje_extra_nocturna; ?>%):</strong> 
                                                                    $7.500 + <?php echo $porcentaje_extra_nocturna; ?>% = $<?php echo number_format(7500 + ($porcentaje_extra_nocturna * 7500 / 100), 0, ',', '.'); ?>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="text-end mt-3">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="mdi mdi-content-save me-1"></i> Guardar Configuración de Costos
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Integraciones -->
                                        <div class="tab-pane" id="integraciones">
                                            <h4 class="header-title mb-3">
                                                <i class="mdi mdi-cloud-sync me-1"></i>
                                                Configuración de Integraciones
                                            </h4>

                                            <div class="alert alert-info">
                                                <i class="mdi mdi-information me-2"></i>
                                                Configure las integraciones con sistemas externos para sincronizar información de horas y costos.
                                            </div>

                                            <form method="POST" action="action/actualizar-configuracion.php">
                                                <input type="hidden" name="tipo" value="integraciones">

                                                <div class="card mb-3">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-primary mb-3">
                                                            <i class="mdi mdi-application me-1"></i>
                                                            ProjectDashboard
                                                        </h5>

                                                        <div class="mb-3">
                                                            <label class="form-label">Habilitar Sincronización</label>
                                                            <?php
                                                            $pd_habilitado = false;
                                                            foreach ($configuraciones as $c) {
                                                                if ($c['clave'] === 'projectdashboard_habilitado') {
                                                                    $pd_habilitado = ($c['valor'] == '1');
                                                                    break;
                                                                }
                                                            }
                                                            ?>
                                                            <div class="form-check form-switch">
                                                                <input type="checkbox" class="form-check-input" 
                                                                       name="projectdashboard_habilitado" 
                                                                       value="1"
                                                                       <?php echo $pd_habilitado ? 'checked' : ''; ?>>
                                                                <label class="form-check-label">Activar integración con ProjectDashboard</label>
                                                            </div>
                                                            <small class="text-muted">Permite exportar y sincronizar registros de horas con ProjectDashboard</small>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">URL Webhook de ProjectDashboard</label>
                                                            <?php
                                                            $pd_url = '';
                                                            foreach ($configuraciones as $c) {
                                                                if ($c['clave'] === 'projectdashboard_url') {
                                                                    $pd_url = $c['valor'];
                                                                    break;
                                                                }
                                                            }
                                                            ?>
                                                            <input type="url" class="form-control" 
                                                                   name="projectdashboard_url" 
                                                                   value="<?php echo htmlspecialchars($pd_url); ?>"
                                                                   placeholder="https://projectdashboard.ejemplo.com/api/webhook">
                                                            <small class="text-muted">URL del endpoint webhook para enviar datos automáticamente</small>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Token de Autenticación</label>
                                                            <?php
                                                            $pd_token = '';
                                                            foreach ($configuraciones as $c) {
                                                                if ($c['clave'] === 'projectdashboard_webhook_token') {
                                                                    $pd_token = $c['valor'];
                                                                    break;
                                                                }
                                                            }
                                                            ?>
                                                            <input type="password" class="form-control" 
                                                                   name="projectdashboard_webhook_token" 
                                                                   value="<?php echo htmlspecialchars($pd_token); ?>"
                                                                   placeholder="Bearer token">
                                                            <small class="text-muted">Token de autorización para el webhook (opcional)</small>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Sincronización Automática</label>
                                                            <?php
                                                            $pd_auto = false;
                                                            foreach ($configuraciones as $c) {
                                                                if ($c['clave'] === 'projectdashboard_sincronizacion_automatica') {
                                                                    $pd_auto = ($c['valor'] == '1');
                                                                    break;
                                                                }
                                                            }
                                                            ?>
                                                            <div class="form-check form-switch">
                                                                <input type="checkbox" class="form-check-input" 
                                                                       name="projectdashboard_sincronizacion_automatica" 
                                                                       value="1"
                                                                       <?php echo $pd_auto ? 'checked' : ''; ?>>
                                                                <label class="form-check-label">Enviar automáticamente via webhook al aprobar horas</label>
                                                            </div>
                                                            <small class="text-muted">Si está activado, los registros se enviarán automáticamente cuando sean aprobados</small>
                                                        </div>

                                                        <div class="alert alert-warning">
                                                            <i class="mdi mdi-alert-outline me-2"></i>
                                                            <strong>Nota:</strong> Los registros sincronizados no se podrán enviar nuevamente. 
                                                            Revise cuidadosamente antes de marcar como sincronizados.
                                                        </div>

                                                        <div class="d-flex gap-2">
                                                            <a href="sincronizar-projectdashboard.php" class="btn btn-info">
                                                                <i class="mdi mdi-eye me-1"></i>
                                                                Ver Panel de Sincronización
                                                            </a>
                                                            <button type="button" class="btn btn-secondary" id="btnProbarWebhook">
                                                                <i class="mdi mdi-connection me-1"></i>
                                                                Probar Conexión
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="mdi mdi-content-save me-1"></i>
                                                        Guardar Configuración
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Configuración General -->
                                        <div class="tab-pane" id="sistema">
                                            <h4 class="header-title mb-3">Parámetros del Sistema</h4>

                                            <form method="POST" action="action/actualizar-configuracion.php">
                                                <input type="hidden" name="tipo" value="sistema">
                                                
                                                <?php foreach ($config_por_categoria as $categoria => $configs): ?>
                                                <div class="card mb-3">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-primary mb-3">
                                                            <i class="mdi mdi-cog me-1"></i>
                                                            <?php echo ucfirst($categoria); ?>
                                                        </h5>

                                                        <?php foreach ($configs as $config): ?>
                                                        <div class="mb-3">
                                                            <label class="form-label">
                                                                <?php echo htmlspecialchars($config['descripcion'] ?? $config['clave']); ?>
                                                            </label>
                                                            <input type="hidden" name="configs[<?php echo $config['id']; ?>][id]" value="<?php echo $config['id']; ?>">
                                                            
                                                            <?php if ($config['tipo'] === 'booleano'): ?>
                                                                <div class="form-check form-switch">
                                                                    <input type="checkbox" class="form-check-input" 
                                                                           name="configs[<?php echo $config['id']; ?>][valor]" 
                                                                           value="1"
                                                                           <?php echo $config['valor'] == '1' ? 'checked' : ''; ?>>
                                                                </div>
                                                            <?php elseif ($config['tipo'] === 'numero'): ?>
                                                                <input type="number" class="form-control" 
                                                                       name="configs[<?php echo $config['id']; ?>][valor]" 
                                                                       value="<?php echo htmlspecialchars($config['valor']); ?>"
                                                                       step="1">
                                                            <?php elseif ($config['tipo'] === 'decimal'): ?>
                                                                <input type="number" class="form-control" 
                                                                       name="configs[<?php echo $config['id']; ?>][valor]" 
                                                                       value="<?php echo htmlspecialchars($config['valor']); ?>"
                                                                       step="0.1" min="0">
                                                                <small class="text-muted">Valor decimal (ejemplo: 0.5, 1.0, 8.5)</small>
                                                            <?php else: ?>
                                                                <input type="text" class="form-control" 
                                                                       name="configs[<?php echo $config['id']; ?>][valor]" 
                                                                       value="<?php echo htmlspecialchars($config['valor']); ?>">
                                                            <?php endif; ?>
                                                            
                                                            <small class="text-muted">Clave: <?php echo $config['clave']; ?></small>
                                                        </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>

                                                <div class="text-end mt-3">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="mdi mdi-content-save me-1"></i> Guardar Configuración
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        function toggleHorario(id) {
            const laborable = document.getElementById('laborable_' + id).checked;
            const inputs = document.querySelectorAll(`input[id*="_${id}"]`);
            
            inputs.forEach(input => {
                if (input.id !== 'laborable_' + id) {
                    input.disabled = !laborable;
                    if (!laborable) {
                        if (input.type === 'time') {
                            input.value = '00:00:00';
                        } else if (input.type === 'number') {
                            input.value = '0';
                        }
                    }
                }
            });
        }

        // Probar conexión webhook
        document.getElementById('btnProbarWebhook')?.addEventListener('click', function() {
            const btn = this;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> Probando...';

            fetch('action/probar-webhook.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalText;

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Conexión Exitosa',
                        text: data.message,
                        timer: 3000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Conexión',
                        text: data.message,
                        confirmButtonText: 'Entendido'
                    });
                }
            })
            .catch(error => {
                btn.disabled = false;
                btn.innerHTML = originalText;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al probar la conexión: ' + error.message
                });
            });
        });
    </script>

</body>
</html>
