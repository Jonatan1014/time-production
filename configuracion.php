<?php
    session_start();
    require_once 'includes/Class-usuario.php';

    $Usuario_class = new Usuario();

    if (! $Usuario_class->usuarioLogueado()) {
        header("Location: login.php");
        exit;
    }

    // Verificar que sea administrador
    $es_admin = isset($_SESSION['user_rol']) && ($_SESSION['user_rol'] === 'administrador' || $_SESSION['user_rol'] === 'Administrador');

    if (! $es_admin) {
        $_SESSION['error'] = "No tiene permisos para acceder a esta página";
        header("Location: index.php");
        exit;
    }

    require_once 'includes/conn-db.php';
    $database = new Database();
    $conn     = $database->getConnection();

    // Asegurar configuracion base para dotacion
    $clave_dotacion = 'dotacion_intervalo_meses';
    $query_dotacion = "SELECT id FROM configuracion_sistema WHERE clave = :clave LIMIT 1";
    $stmt_dotacion = $conn->prepare($query_dotacion);
    $stmt_dotacion->bindParam(':clave', $clave_dotacion);
    $stmt_dotacion->execute();

    if ($stmt_dotacion->rowCount() === 0) {
        $query_insert = "INSERT INTO configuracion_sistema (clave, valor, tipo, descripcion, categoria) "
                      . "VALUES (:clave, :valor, 'numero', :descripcion, 'dotacion')";
        $stmt_insert = $conn->prepare($query_insert);
        $valor_default = '4';
        $descripcion = 'Intervalo de entrega de dotacion (meses)';
        $stmt_insert->bindParam(':clave', $clave_dotacion);
        $stmt_insert->bindParam(':valor', $valor_default);
        $stmt_insert->bindParam(':descripcion', $descripcion);
        $stmt_insert->execute();
    }

    // Obtener horarios laborales
    $query_horarios = "SELECT * FROM horarios_laborales ORDER BY
                   FIELD(dia_semana, 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo')";
    $stmt_horarios = $conn->prepare($query_horarios);
    $stmt_horarios->execute();
    $horarios = $stmt_horarios->fetchAll(PDO::FETCH_ASSOC);

    // Obtener configuraciones del sistema
    $query_config = "SELECT * FROM configuracion_sistema ORDER BY categoria, clave";
    $stmt_config  = $conn->prepare($query_config);
    $stmt_config->execute();
    $configuraciones = $stmt_config->fetchAll(PDO::FETCH_ASSOC);

    // Agrupar configuraciones por categoría
    $config_por_categoria = [];
    foreach ($configuraciones as $config) {
        $categoria = $config['categoria'] ?? 'general';
        if (! isset($config_por_categoria[$categoria])) {
            $config_por_categoria[$categoria] = [];
        }
        $config_por_categoria[$categoria][] = $config;
    }
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Configuración del Sistema | Time Production</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="shortcut icon" href="assets/images/favicon.ico" />
    <script src="assets/js/hyper-config.js"></script>
    <link href="assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="wrapper">
        <?php include "includes/header.php"; ?>
        <?php include "includes/sidebar.php"; ?>

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">

                    <!-- Mensajes -->
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['success'];unset($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['error'];unset($_SESSION['error']); ?>
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
                                        <li class="nav-item">
                                            <a href="#festivos" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                                <i class="mdi mdi-calendar-check me-1"></i>
                                                Días Festivos
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
                                                                    'lunes'     => 'Lunes',
                                                                    'martes'    => 'Martes',
                                                                    'miercoles' => 'Miércoles',
                                                                    'jueves'    => 'Jueves',
                                                                    'viernes'   => 'Viernes',
                                                                    'sabado'    => 'Sábado',
                                                                    'domingo'   => 'Domingo',
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
                                                                           data-horario-id="<?php echo $horario['id']; ?>"
                                                                           value="<?php echo $horario['hora_inicio_manana']; ?>"
                                                                           <?php echo ! $horario['es_laborable'] ? 'disabled' : ''; ?>>
                                                                </td>
                                                                <td>
                                                                    <input type="time" class="form-control form-control-sm horario-input"
                                                                           name="horarios[<?php echo $horario['id']; ?>][hora_fin_manana]"
                                                                           id="fin_manana_<?php echo $horario['id']; ?>"
                                                                           data-horario-id="<?php echo $horario['id']; ?>"
                                                                           value="<?php echo $horario['hora_fin_manana']; ?>"
                                                                           <?php echo ! $horario['es_laborable'] ? 'disabled' : ''; ?>>
                                                                </td>
                                                                <td>
                                                                    <input type="time" class="form-control form-control-sm horario-input"
                                                                           name="horarios[<?php echo $horario['id']; ?>][hora_inicio_tarde]"
                                                                           id="inicio_tarde_<?php echo $horario['id']; ?>"
                                                                           data-horario-id="<?php echo $horario['id']; ?>"
                                                                           value="<?php echo $horario['hora_inicio_tarde']; ?>"
                                                                           <?php echo ! $horario['es_laborable'] ? 'disabled' : ''; ?>>
                                                                </td>
                                                                <td>
                                                                    <input type="time" class="form-control form-control-sm horario-input"
                                                                           name="horarios[<?php echo $horario['id']; ?>][hora_fin_tarde]"
                                                                           id="fin_tarde_<?php echo $horario['id']; ?>"
                                                                           data-horario-id="<?php echo $horario['id']; ?>"
                                                                           value="<?php echo $horario['hora_fin_tarde']; ?>"
                                                                           <?php echo ! $horario['es_laborable'] ? 'disabled' : ''; ?>>
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control form-control-sm bg-light"
                                                                           name="horarios[<?php echo $horario['id']; ?>][horas_totales]"
                                                                           step="0.25" min="0" max="24"
                                                                           value="<?php echo $horario['horas_totales']; ?>"
                                                                           <?php echo ! $horario['es_laborable'] ? 'disabled' : ''; ?>
                                                                           id="horas_<?php echo $horario['id']; ?>"
                                                                           readonly>
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
                                                Configure los factores de recargo para calcular costos de horas extras según la legislación laboral colombiana.
                                            </div>

                                            <form method="POST" action="action/actualizar-configuracion.php">
                                                <input type="hidden" name="tipo" value="costos">

                                                <!-- Horarios de Turnos -->
                                                <div class="card mb-3">
                                                    <div class="card-header bg-primary bg-opacity-10">
                                                        <h5 class="card-title mb-0 text-primary">
                                                            <i class="mdi mdi-clock-outline me-1"></i>
                                                            Horarios de Turnos
                                                        </h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Hora Inicio Turno Diurno</label>
                                                                    <?php
                                                                        $hora_diurna_inicio = '06:00';
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
                                                                        $hora_diurna_fin = '21:00';
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
                                                                    <small class="text-muted">Ejemplo: 21:00 (9:00 PM)</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="alert alert-warning mb-0">
                                                            <i class="mdi mdi-weather-night me-2"></i>
                                                            <strong>Turno Nocturno:</strong> Se considera de                                                                                                             <?php echo $hora_diurna_fin; ?> a<?php echo $hora_diurna_inicio; ?> del día siguiente.
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Horas Extras Ordinarias (Lunes a Sábado) -->
                                                <div class="card mb-3">
                                                    <div class="card-header bg-success bg-opacity-10">
                                                        <h5 class="card-title mb-0 text-success">
                                                            <i class="mdi mdi-calendar-week me-1"></i>
                                                            Horas Extras Ordinarias (Lunes a Sábado)
                                                        </h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Hora Extra Diurna Ordinaria</label>
                                                                    <?php
                                                                        $factor_extra_diurna = '1.25';
                                                                        foreach ($configuraciones as $c) {
                                                                            if ($c['clave'] === 'factor_extra_diurna') {
                                                                                $factor_extra_diurna = $c['valor'];
                                                                                echo '<input type="hidden" name="configs[' . $c['id'] . '][id]" value="' . $c['id'] . '">';
                                                                                break;
                                                                            }
                                                                        }
                                                                    ?>
                                                                    <div class="input-group">
                                                                        <input type="number" class="form-control"
                                                                               name="configs_factor_extra_diurna"
                                                                               value="<?php echo htmlspecialchars($factor_extra_diurna); ?>"
                                                                               step="0.01" min="1" max="10" required>
                                                                        <span class="input-group-text">x</span>
                                                                    </div>
                                                                    <small class="text-muted">Valor legal: 1.25x (25% adicional)</small>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Hora Extra Nocturna Ordinaria</label>
                                                                    <?php
                                                                        $factor_extra_nocturna = '1.75';
                                                                        foreach ($configuraciones as $c) {
                                                                            if ($c['clave'] === 'factor_extra_nocturna') {
                                                                                $factor_extra_nocturna = $c['valor'];
                                                                                echo '<input type="hidden" name="configs[' . $c['id'] . '][id]" value="' . $c['id'] . '">';
                                                                                break;
                                                                            }
                                                                        }
                                                                    ?>
                                                                    <div class="input-group">
                                                                        <input type="number" class="form-control"
                                                                               name="configs_factor_extra_nocturna"
                                                                               value="<?php echo htmlspecialchars($factor_extra_nocturna); ?>"
                                                                               step="0.01" min="1" max="10" required>
                                                                        <span class="input-group-text">x</span>
                                                                    </div>
                                                                    <small class="text-muted">Valor legal: 1.75x (75% adicional)</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Recargos Nocturnos -->
                                                <div class="card mb-3">
                                                    <div class="card-header bg-dark bg-opacity-10">
                                                        <h5 class="card-title mb-0 text-dark">
                                                            <i class="mdi mdi-weather-night me-1"></i>
                                                            Recargos Nocturnos
                                                        </h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Recargo Nocturno Ordinario</label>
                                                                    <?php
                                                                        $recargo_nocturno_ordinario = '0.35';
                                                                        foreach ($configuraciones as $c) {
                                                                            if ($c['clave'] === 'recargo_nocturno_ordinario') {
                                                                                $recargo_nocturno_ordinario = $c['valor'];
                                                                                echo '<input type="hidden" name="configs[' . $c['id'] . '][id]" value="' . $c['id'] . '">';
                                                                                break;
                                                                            }
                                                                        }
                                                                    ?>
                                                                    <div class="input-group">
                                                                        <input type="number" class="form-control"
                                                                               name="configs_recargo_nocturno_ordinario"
                                                                               value="<?php echo htmlspecialchars($recargo_nocturno_ordinario); ?>"
                                                                               step="0.01" min="0" max="5" required>
                                                                        <span class="input-group-text">x adicional</span>
                                                                    </div>
                                                                    <small class="text-muted">Valor legal: 0.35x (35% adicional sobre hora ordinaria)</small>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Recargo Nocturno Dominical/Festivo</label>
                                                                    <?php
                                                                        $recargo_nocturno_dominical = '2.1';
                                                                        foreach ($configuraciones as $c) {
                                                                            if ($c['clave'] === 'recargo_nocturno_dominical') {
                                                                                $recargo_nocturno_dominical = $c['valor'];
                                                                                echo '<input type="hidden" name="configs[' . $c['id'] . '][id]" value="' . $c['id'] . '">';
                                                                                break;
                                                                            }
                                                                        }
                                                                    ?>
                                                                    <div class="input-group">
                                                                        <input type="number" class="form-control"
                                                                               name="configs_recargo_nocturno_dominical"
                                                                               value="<?php echo htmlspecialchars($recargo_nocturno_dominical); ?>"
                                                                               step="0.01" min="1" max="10" required>
                                                                        <span class="input-group-text">x</span>
                                                                    </div>
                                                                    <small class="text-muted">Valor legal: 2.1x (trabajo nocturno en domingo/festivo)</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Horas Extras Dominicales y Festivas -->
                                                <div class="card mb-3">
                                                    <div class="card-header bg-warning bg-opacity-10">
                                                        <h5 class="card-title mb-0 text-warning">
                                                            <i class="mdi mdi-calendar-star me-1"></i>
                                                            Horas Extras Dominicales y Festivas
                                                        </h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Hora Extra Diurna Dominical/Festiva</label>
                                                                    <?php
                                                                        $factor_dominical_diurno = '2.0';
                                                                        foreach ($configuraciones as $c) {
                                                                            if ($c['clave'] === 'factor_dominical_diurno') {
                                                                                $factor_dominical_diurno = $c['valor'];
                                                                                echo '<input type="hidden" name="configs[' . $c['id'] . '][id]" value="' . $c['id'] . '">';
                                                                                break;
                                                                            }
                                                                        }
                                                                    ?>
                                                                    <div class="input-group">
                                                                        <input type="number" class="form-control"
                                                                               name="configs_factor_dominical_diurno"
                                                                               value="<?php echo htmlspecialchars($factor_dominical_diurno); ?>"
                                                                               step="0.01" min="1" max="10" required>
                                                                        <span class="input-group-text">x</span>
                                                                    </div>
                                                                    <small class="text-muted">Valor legal: 2.0x (100% adicional)</small>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Hora Extra Nocturna Dominical/Festiva</label>
                                                                    <?php
                                                                        $factor_dominical_nocturno = '2.5';
                                                                        foreach ($configuraciones as $c) {
                                                                            if ($c['clave'] === 'factor_dominical_nocturno') {
                                                                                $factor_dominical_nocturno = $c['valor'];
                                                                                echo '<input type="hidden" name="configs[' . $c['id'] . '][id]" value="' . $c['id'] . '">';
                                                                                break;
                                                                            }
                                                                        }
                                                                    ?>
                                                                    <div class="input-group">
                                                                        <input type="number" class="form-control"
                                                                               name="configs_factor_dominical_nocturno"
                                                                               value="<?php echo htmlspecialchars($factor_dominical_nocturno); ?>"
                                                                               step="0.01" min="1" max="10" required>
                                                                        <span class="input-group-text">x</span>
                                                                    </div>
                                                                    <small class="text-muted">Valor legal: 2.5x (150% adicional)</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Trabajo Dominical y Festivo -->
                                                <div class="card mb-3">
                                                    <div class="card-header bg-danger bg-opacity-10">
                                                        <h5 class="card-title mb-0 text-danger">
                                                            <i class="mdi mdi-calendar-check me-1"></i>
                                                            Trabajo Dominical y Festivo (Horas Ordinarias)
                                                        </h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Factor Día/Hora Dominical o Festivo</label>
                                                                    <?php
                                                                        $factor_dominical = '1.75';
                                                                        foreach ($configuraciones as $c) {
                                                                            if ($c['clave'] === 'factor_dominical') {
                                                                                $factor_dominical = $c['valor'];
                                                                                echo '<input type="hidden" name="configs[' . $c['id'] . '][id]" value="' . $c['id'] . '">';
                                                                                break;
                                                                            }
                                                                        }
                                                                    ?>
                                                                    <div class="input-group">
                                                                        <input type="number" class="form-control"
                                                                               name="configs_factor_dominical"
                                                                               value="<?php echo htmlspecialchars($factor_dominical); ?>"
                                                                               step="0.01" min="1" max="10" required>
                                                                        <span class="input-group-text">x</span>
                                                                    </div>
                                                                    <small class="text-muted">Valor legal: 1.75x (75% adicional por trabajar domingo/festivo)</small>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="alert alert-light h-100 d-flex align-items-center mb-0">
                                                                    <div>
                                                                        <i class="mdi mdi-information-outline text-info me-2"></i>
                                                                        Este factor aplica al trabajo ordinario realizado en domingos o días festivos (no horas extras).
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Tabla Resumen -->
                                                <div class="card mb-3">
                                                    <div class="card-header bg-info bg-opacity-10">
                                                        <h5 class="card-title mb-0 text-info">
                                                            <i class="mdi mdi-table me-1"></i>
                                                            Resumen de Factores de Pago
                                                        </h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-hover mb-0">
                                                                <thead class="table-dark">
                                                                    <tr>
                                                                        <th>Tipo de Hora Trabajada</th>
                                                                        <th class="text-center">Factor</th>
                                                                        <th class="text-end">Ejemplo ($7.500/hora)</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr class="table-success">
                                                                        <td colspan="3" class="fw-bold bg-success bg-opacity-10">Días Ordinarios (Lunes a Sábado)</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><i class="mdi mdi-weather-sunny text-warning me-1"></i> Hora extra diurna ordinaria</td>
                                                                        <td class="text-center fw-bold"><?php echo $factor_extra_diurna; ?>x</td>
                                                                        <td class="text-end">$<?php echo number_format(7500 * floatval($factor_extra_diurna), 0, ',', '.'); ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><i class="mdi mdi-weather-night text-dark me-1"></i> Hora extra nocturna ordinaria</td>
                                                                        <td class="text-center fw-bold"><?php echo $factor_extra_nocturna; ?>x</td>
                                                                        <td class="text-end">$<?php echo number_format(7500 * floatval($factor_extra_nocturna), 0, ',', '.'); ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><i class="mdi mdi-moon-waning-crescent text-secondary me-1"></i> Recargo nocturno ordinario</td>
                                                                        <td class="text-center fw-bold">+<?php echo $recargo_nocturno_ordinario; ?>x</td>
                                                                        <td class="text-end">+$<?php echo number_format(7500 * floatval($recargo_nocturno_ordinario), 0, ',', '.'); ?></td>
                                                                    </tr>
                                                                    <tr class="table-warning">
                                                                        <td colspan="3" class="fw-bold bg-warning bg-opacity-10">Domingos y Festivos</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><i class="mdi mdi-calendar-star text-warning me-1"></i> Día/Hora dominical o festivo</td>
                                                                        <td class="text-center fw-bold"><?php echo $factor_dominical; ?>x</td>
                                                                        <td class="text-end">$<?php echo number_format(7500 * floatval($factor_dominical), 0, ',', '.'); ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><i class="mdi mdi-weather-sunny text-orange me-1"></i> Hora extra diurna dominical/festiva</td>
                                                                        <td class="text-center fw-bold"><?php echo $factor_dominical_diurno; ?>x</td>
                                                                        <td class="text-end">$<?php echo number_format(7500 * floatval($factor_dominical_diurno), 0, ',', '.'); ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><i class="mdi mdi-weather-night text-danger me-1"></i> Hora extra nocturna dominical/festiva</td>
                                                                        <td class="text-center fw-bold"><?php echo $factor_dominical_nocturno; ?>x</td>
                                                                        <td class="text-end">$<?php echo number_format(7500 * floatval($factor_dominical_nocturno), 0, ',', '.'); ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><i class="mdi mdi-moon-full text-purple me-1"></i> Recargo nocturno dominical/festivo</td>
                                                                        <td class="text-center fw-bold"><?php echo $recargo_nocturno_dominical; ?>x</td>
                                                                        <td class="text-end">$<?php echo number_format(7500 * floatval($recargo_nocturno_dominical), 0, ',', '.'); ?></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
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

                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h5 class="card-title text-primary mb-3">
                                                        <i class="mdi mdi-calendar-star me-1"></i>
                                                        API de Días Festivos
                                                    </h5>

                                                    <div class="alert alert-info">
                                                        <i class="mdi mdi-information me-2"></i>
                                                        Configure la conexión a la API de días festivos para calcular automáticamente los recargos en días festivos.
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">País para Días Festivos</label>
                                                        <?php
                                                            $festivos_pais = '';
                                                            foreach ($configuraciones as $c) {
                                                                if ($c['clave'] === 'festivos_pais') {
                                                                    $festivos_pais = $c['valor'];
                                                                    break;
                                                                }
                                                            }
                                                        ?>
                                                        <select class="form-select" name="festivos_pais">
                                                            <option value="">Seleccionar país</option>
                                                            <option value="CO"                                                                               <?php echo($festivos_pais === 'CO') ? 'selected' : ''; ?>>Colombia (CO)</option>
                                                            <option value="US"                                                                               <?php echo($festivos_pais === 'US') ? 'selected' : ''; ?>>Estados Unidos (US)</option>
                                                            <option value="MX"                                                                               <?php echo($festivos_pais === 'MX') ? 'selected' : ''; ?>>México (MX)</option>
                                                            <option value="AR"                                                                               <?php echo($festivos_pais === 'AR') ? 'selected' : ''; ?>>Argentina (AR)</option>
                                                            <option value="CL"                                                                               <?php echo($festivos_pais === 'CL') ? 'selected' : ''; ?>>Chile (CL)</option>
                                                            <option value="PE"                                                                               <?php echo($festivos_pais === 'PE') ? 'selected' : ''; ?>>Perú (PE)</option>
                                                            <option value="EC"                                                                               <?php echo($festivos_pais === 'EC') ? 'selected' : ''; ?>>Ecuador (EC)</option>
                                                        </select>
                                                        <small class="text-muted">Código ISO del país para consultar días festivos</small>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">URL Base de la API</label>
                                                        <?php
                                                            $festivos_url = '';
                                                            foreach ($configuraciones as $c) {
                                                                if ($c['clave'] === 'festivos_api_url') {
                                                                    $festivos_url = $c['valor'];
                                                                    break;
                                                                }
                                                            }
                                                        ?>
                                                        <input type="url" class="form-control"
                                                               name="festivos_api_url"
                                                               value="<?php echo htmlspecialchars($festivos_url ?: 'https://date.nager.at/api/v3/PublicHolidays'); ?>"
                                                               placeholder="https://date.nager.at/api/v3/PublicHolidays">
                                                        <small class="text-muted">URL base de la API de días festivos (Nager.Date)</small>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Habilitar Consulta Automática</label>
                                                        <?php
                                                            $festivos_auto = false;
                                                            foreach ($configuraciones as $c) {
                                                                if ($c['clave'] === 'festivos_consulta_automatica') {
                                                                    $festivos_auto = ($c['valor'] == '1');
                                                                    break;
                                                                }
                                                            }
                                                        ?>
                                                        <div class="form-check form-switch">
                                                            <input type="checkbox" class="form-check-input"
                                                                   name="festivos_consulta_automatica"
                                                                   value="1"
                                                                   <?php echo $festivos_auto ? 'checked' : ''; ?>>
                                                            <label class="form-check-label">Consultar automáticamente días festivos</label>
                                                        </div>
                                                        <small class="text-muted">Si está activado, el sistema consultará automáticamente los días festivos para calcular recargos</small>
                                                    </div>

                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-secondary" id="btnProbarFestivos">
                                                            <i class="mdi mdi-calendar-check me-1"></i>
                                                            Probar API de Festivos
                                                        </button>
                                                        <button type="button" class="btn btn-info" id="btnVerFestivos">
                                                            <i class="mdi mdi-calendar-month me-1"></i>
                                                            Ver Festivos                                                                         <?php echo date('Y'); ?>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
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

                                                            <small class="text-muted">Clave:                                                                                             <?php echo $config['clave']; ?></small>
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

                                        <!-- Días Festivos -->
                                        <div class="tab-pane" id="festivos">
                                            <h4 class="header-title mb-3">Gestión de Días Festivos</h4>

                                            <div class="alert alert-info">
                                                <i class="mdi mdi-information me-2"></i>
                                                <strong>Sistema de Días Festivos:</strong> Consulte y almacene los festivos de cualquier año desde la API de Nager.Date.
                                                Los festivos se usan para calcular recargos en horas extras.
                                                <br><small class="text-muted mt-1 d-block">
                                                    <strong>Cron Job:</strong> <code>0 0 1 1 * php                                                                                                   <?php echo __DIR__; ?>/cron-festivos.php</code>
                                                    <br><strong>URL Externa:</strong> <code><?php echo(isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']); ?>/cron-festivos.php?token=festivos_secure_token_2026</code>
                                                </small>
                                            </div>

                                            <div class="row mb-4">
                                                <!-- Selector de Año -->
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label fw-bold">
                                                        <i class="mdi mdi-calendar me-1"></i>Año a Consultar
                                                    </label>
                                                    <select class="form-select" id="selectAnioFestivos">
                                                        <?php
                                                            $anio_actual = date('Y');
                                                            for ($i = $anio_actual - 2; $i <= $anio_actual + 3; $i++):
                                                        ?>
                                                        <option value="<?php echo $i; ?>"<?php echo($i == $anio_actual) ? 'selected' : ''; ?>>
                                                            <?php echo $i; ?><?php echo($i == $anio_actual) ? ' (Actual)' : ''; ?>
                                                        </option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>

                                                <!-- Botones de acción -->
                                                <div class="col-md-8 mb-3">
                                                    <label class="form-label fw-bold">
                                                        <i class="mdi mdi-cog me-1"></i>Acciones
                                                    </label>
                                                    <div class="btn-group d-flex flex-wrap gap-2">
                                                        <button type="button" class="btn btn-primary" id="btnConsultarFestivos">
                                                            <i class="mdi mdi-cloud-download me-1"></i>
                                                            Consultar desde API
                                                        </button>
                                                        <button type="button" class="btn btn-success" id="btnActualizarFestivos">
                                                            <i class="mdi mdi-refresh me-1"></i>
                                                            Forzar Actualización
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger" id="btnEliminarFestivos">
                                                            <i class="mdi mdi-delete me-1"></i>
                                                            Eliminar Año
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Información del país configurado -->
                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <div class="alert alert-light p-2 d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <strong><i class="mdi mdi-flag me-1"></i>País configurado:</strong>
                                                            <?php
                                                                $pais_festivos = '';
                                                                foreach ($configuraciones as $config) {
                                                                    if ($config['clave'] === 'festivos_pais') {
                                                                        $pais_festivos = $config['valor'];
                                                                        break;
                                                                    }
                                                                }
                                                                $paises_nombres = [
                                                                    'CO' => 'Colombia',
                                                                    'US' => 'Estados Unidos',
                                                                    'MX' => 'México',
                                                                    'AR' => 'Argentina',
                                                                    'CL' => 'Chile',
                                                                    'PE' => 'Perú',
                                                                    'EC' => 'Ecuador',
                                                                ];
                                                                echo htmlspecialchars(($paises_nombres[$pais_festivos] ?? $pais_festivos) ?: 'No configurado');
                                                                echo " ($pais_festivos)";
                                                            ?>
                                                        </div>
                                                        <div id="aniosDisponiblesInfo">
                                                            <span class="badge bg-secondary">Cargando...</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Contenedor de festivos -->
                                            <div class="card">
                                                <div class="card-header bg-light">
                                                    <h5 class="card-title mb-0">
                                                        <i class="mdi mdi-calendar-multiple me-1"></i>
                                                        Festivos del Año <span id="tituloAnioFestivos"><?php echo date('Y'); ?></span>
                                                        <span class="badge bg-primary ms-2" id="totalFestivos">-</span>
                                                    </h5>
                                                </div>
                                                <div class="card-body">
                                                    <div id="festivosContainer">
                                                        <div class="text-center py-4">
                                                            <div class="spinner-border text-primary" role="status">
                                                                <span class="visually-hidden">Cargando...</span>
                                                            </div>
                                                            <p class="mt-2 text-muted">Cargando festivos...</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <?php include "includes/footer.php"; ?>
        </div>
    </div>

    <?php include "includes/js.php"; ?>

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

            // Calcular horas totales si está marcado como laborable
            if (laborable) {
                calcularHorasTotales(id);
            }
        }

        // Función para calcular horas totales automáticamente
        function calcularHorasTotales(id) {
            const inicioManana = document.getElementById('inicio_manana_' + id).value;
            const finManana = document.getElementById('fin_manana_' + id).value;
            const inicioTarde = document.getElementById('inicio_tarde_' + id).value;
            const finTarde = document.getElementById('fin_tarde_' + id).value;

            let horasTotales = 0;

            // Calcular horas de la mañana
            if (inicioManana && finManana) {
                const horasManana = calcularDiferenciaHoras(inicioManana, finManana);
                if (horasManana > 0) {
                    horasTotales += horasManana;
                }
            }

            // Calcular horas de la tarde
            if (inicioTarde && finTarde) {
                const horasTarde = calcularDiferenciaHoras(inicioTarde, finTarde);
                if (horasTarde > 0) {
                    horasTotales += horasTarde;
                }
            }

            // Actualizar el campo de horas totales
            const campoHoras = document.getElementById('horas_' + id);
            if (campoHoras) {
                campoHoras.value = horasTotales.toFixed(2);
            }
        }

        // Función auxiliar para calcular diferencia de horas entre dos tiempos
        function calcularDiferenciaHoras(inicio, fin) {
            if (!inicio || !fin) return 0;

            const [horaInicio, minInicio] = inicio.split(':').map(Number);
            const [horaFin, minFin] = fin.split(':').map(Number);

            const minutosInicio = horaInicio * 60 + minInicio;
            const minutosFin = horaFin * 60 + minFin;

            let diferencia = minutosFin - minutosInicio;

            // Si la diferencia es negativa, significa que cruza la medianoche
            if (diferencia < 0) {
                diferencia += 24 * 60;
            }

            return diferencia / 60; // Convertir minutos a horas
        }

        // Agregar event listeners a todos los campos de horario
        document.addEventListener('DOMContentLoaded', function() {
            const horariosInputs = document.querySelectorAll('.horario-input');

            horariosInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const horarioId = this.getAttribute('data-horario-id');
                    if (horarioId) {
                        calcularHorasTotales(horarioId);
                    }
                });
            });

            // Calcular horas totales iniciales para todos los horarios laborables
            <?php foreach ($horarios as $horario): ?>
                <?php if ($horario['es_laborable']): ?>
                    calcularHorasTotales(<?php echo $horario['id']; ?>);
                <?php endif; ?>
            <?php endforeach; ?>
        });
    </script>

    <script>
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

        // Probar API de festivos
        document.getElementById('btnProbarFestivos')?.addEventListener('click', function() {
            const btn = this;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> Probando...';

            const pais = document.querySelector('select[name="festivos_pais"]').value;
            const anio = new Date().getFullYear();

            fetch('action/probar-festivos.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `pais=${pais}&anio=${anio}`
            })
            .then(response => response.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalText;

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'API Funcionando',
                        html: `
                            <p>${data.message}</p>
                            <small>País: ${pais} | Año: ${anio}</small>
                        `,
                        confirmButtonText: 'Excelente'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error en API',
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
                    text: 'Error al probar la API: ' + error.message
                });
            });
        });

        // Ver festivos del año
        document.getElementById('btnVerFestivos')?.addEventListener('click', function() {
            const btn = this;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> Consultando...';

            const pais = document.querySelector('select[name="festivos_pais"]').value;
            const anio = new Date().getFullYear();

            fetch('action/ver-festivos.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `pais=${pais}&anio=${anio}`
            })
            .then(response => response.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalText;

                if (data.success) {
                    let html = `
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Día Festivo</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    data.festivos.forEach(festivo => {
                        const fecha = new Date(festivo.fecha + 'T00:00:00').toLocaleDateString('es-ES');
                        html += `
                            <tr>
                                <td>${fecha}</td>
                                <td>${festivo.nombre}</td>
                            </tr>
                        `;
                    });

                    html += `
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">Fuente: ${data.fuente === 'cache' ? 'Cache local' : 'API externa'}</small>
                        </div>
                    `;

                    Swal.fire({
                        title: `Días Festivos ${anio} - ${pais.toUpperCase()}`,
                        html: html,
                        width: '600px',
                        confirmButtonText: 'Cerrar'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al consultar festivos',
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
                    text: 'Error al consultar festivos: ' + error.message
                });
            });
        });
    </script>

    <script>
        // Variable global para el año seleccionado
        let anioSeleccionado = new Date().getFullYear();

        // Cargar festivos cuando se activa la pestaña
        document.querySelector('a[href="#festivos"]').addEventListener('shown.bs.tab', function() {
            cargarAniosDisponibles();
            cargarFestivos(anioSeleccionado);
        });

        // Cambio de año en selector
        document.getElementById('selectAnioFestivos').addEventListener('change', function() {
            anioSeleccionado = parseInt(this.value);
            document.getElementById('tituloAnioFestivos').textContent = anioSeleccionado;
            cargarFestivos(anioSeleccionado);
        });

        // Función para cargar años disponibles
        function cargarAniosDisponibles() {
            fetch('action/consultar-festivos.php?action=anios')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('aniosDisponiblesInfo');
                    if (data.success && data.anios.length > 0) {
                        let html = '<small class="text-muted">Años en cache: </small>';
                        data.anios.forEach((anio, idx) => {
                            html += `<span class="badge bg-success me-1">${anio.anio} (${anio.total})</span>`;
                        });
                        container.innerHTML = html;
                    } else {
                        container.innerHTML = '<span class="badge bg-warning">Sin años en cache</span>';
                    }
                })
                .catch(error => {
                    document.getElementById('aniosDisponiblesInfo').innerHTML =
                        '<span class="badge bg-danger">Error</span>';
                });
        }

        // Función para cargar festivos de un año específico
        function cargarFestivos(anio) {
            const container = document.getElementById('festivosContainer');
            container.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2 text-muted">Cargando festivos de ${anio}...</p>
                </div>
            `;

            fetch(`action/consultar-festivos.php?action=obtener&anio=${anio}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('totalFestivos').textContent = data.total + ' festivos';
                    document.getElementById('tituloAnioFestivos').textContent = anio;

                    if (data.success) {
                        if (data.festivos.length === 0) {
                            container.innerHTML = `
                                <div class="alert alert-warning">
                                    <i class="mdi mdi-alert-circle me-2"></i>
                                    No hay festivos almacenados para el año <strong>${anio}</strong>.
                                    Haga clic en "Consultar desde API" para obtenerlos.
                                </div>
                            `;
                        } else {
                            // Agrupar por mes
                            const festivosPorMes = {};
                            data.festivos.forEach(festivo => {
                                const fecha = new Date(festivo.fecha + 'T00:00:00');
                                const mes = fecha.getMonth();
                                if (!festivosPorMes[mes]) {
                                    festivosPorMes[mes] = [];
                                }
                                festivosPorMes[mes].push(festivo);
                            });

                            let html = '<div class="row">';
                            const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                                         'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

                            for (let mes = 0; mes < 12; mes++) {
                                const festivosMes = festivosPorMes[mes] || [];
                                html += `
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card h-100 ${festivosMes.length > 0 ? 'border-success' : ''}">
                                            <div class="card-header py-2 ${festivosMes.length > 0 ? 'bg-success bg-opacity-10' : 'bg-light'}">
                                                <h6 class="mb-0">${meses[mes]}
                                                    ${festivosMes.length > 0 ? `<span class="badge bg-success">${festivosMes.length}</span>` : ''}
                                                </h6>
                                            </div>
                                            <div class="card-body py-2">
                                                ${festivosMes.length > 0 ?
                                                    '<ul class="list-unstyled mb-0 small">' +
                                                    festivosMes.map(f => {
                                                        const d = new Date(f.fecha + 'T00:00:00');
                                                        const diasSemana = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
                                                        return `
                                                            <li class="mb-1 d-flex align-items-center">
                                                                <span class="badge bg-primary me-2">${d.getDate()}</span>
                                                                <span class="text-muted me-1">${diasSemana[d.getDay()]}</span>
                                                                <span class="fw-medium">${f.nombre}</span>
                                                            </li>
                                                        `;
                                                    }).join('') +
                                                    '</ul>' :
                                                    '<small class="text-muted">Sin festivos este mes</small>'
                                                }
                                            </div>
                                        </div>
                                    </div>
                                `;
                            }
                            html += '</div>';
                            container.innerHTML = html;
                        }
                    } else {
                        container.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="mdi mdi-alert-circle me-2"></i>
                                Error al cargar festivos: ${data.mensaje || 'Error desconocido'}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    container.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="mdi mdi-alert-circle me-2"></i>
                            Error de conexión: ${error.message}
                        </div>
                    `;
                });
        }

        // Botón consultar desde API
        document.getElementById('btnConsultarFestivos').addEventListener('click', function() {
            const btn = this;
            const originalText = btn.innerHTML;
            const anio = document.getElementById('selectAnioFestivos').value;

            btn.disabled = true;
            btn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i>Consultando API...';

            fetch(`action/consultar-festivos.php?action=consultar&anio=${anio}`)
                .then(response => response.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Consulta Exitosa',
                            html: `<p>${data.mensaje}</p>
                                   <small class="text-muted">${data.desde_cache ? 'Datos desde cache local' : 'Datos obtenidos desde API'}</small>`,
                            timer: 3000,
                            showConfirmButton: false
                        });
                        cargarFestivos(anio);
                        cargarAniosDisponibles();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.mensaje
                        });
                    }
                })
                .catch(error => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Conexión',
                        text: error.message
                    });
                });
        });

        // Botón forzar actualización
        document.getElementById('btnActualizarFestivos').addEventListener('click', function() {
            const btn = this;
            const originalText = btn.innerHTML;
            const anio = document.getElementById('selectAnioFestivos').value;

            Swal.fire({
                title: '¿Forzar actualización?',
                text: `Esto reemplazará todos los festivos del año ${anio} con datos frescos de la API.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, actualizar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i>Actualizando...';

                    fetch(`action/consultar-festivos.php?action=consultar&anio=${anio}&forzar=1`)
                        .then(response => response.json())
                        .then(data => {
                            btn.disabled = false;
                            btn.innerHTML = originalText;

                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Actualización Exitosa',
                                    text: data.mensaje,
                                    timer: 2500,
                                    showConfirmButton: false
                                });
                                cargarFestivos(anio);
                                cargarAniosDisponibles();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.mensaje
                                });
                            }
                        })
                        .catch(error => {
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: error.message
                            });
                        });
                }
            });
        });

        // Botón eliminar festivos del año
        document.getElementById('btnEliminarFestivos').addEventListener('click', function() {
            const anio = document.getElementById('selectAnioFestivos').value;

            Swal.fire({
                title: '¿Eliminar festivos?',
                text: `Se eliminarán todos los festivos del año ${anio} del cache local.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`action/consultar-festivos.php?action=eliminar&anio=${anio}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Eliminado',
                                    text: data.mensaje,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                cargarFestivos(anio);
                                cargarAniosDisponibles();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.mensaje
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: error.message
                            });
                        });
                }
            });
        });
    </script>

</body>
</html>
