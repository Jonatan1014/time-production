<?php
// registrar-horas.php - Formulario para registrar horas de trabajo
session_start();
require_once 'includes/Class-usuario.php';
require_once 'includes/Class-ordenes-produccion.php';
require_once 'includes/Class-configuracion.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    header("Location: login.php");
    exit;
}

$OrdenProduccion_class = new OrdenProduccion();
$ordenes_activas = $OrdenProduccion_class->obtenerOrdenesActivas();

// Obtener configuración de horas
$Configuracion = new Configuracion();
$config_horas = $Configuracion->obtenerConfigHoras();
$horas_min = $config_horas['horas_minimas_por_registro'] ?? 0.5;
$incremento = $config_horas['incremento_horas'] ?? 0.5;

// Obtener horario laboral del día actual
$horario_hoy = $Configuracion->obtenerHorasLaboralesDia(date('Y-m-d'));
$horas_max = $horario_hoy['horas_totales'];

// Obtener horas ya registradas del usuario para hoy
require_once 'includes/Class-registros-horas.php';
$RegistroHoras_class = new RegistroHoras();
$usuario_id = $_SESSION['user_id'];
$horas_registradas_hoy = $RegistroHoras_class->obtenerTotalHorasDia($usuario_id, date('Y-m-d'));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Registrar Horas | Time Production</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="assets/images/favicon.ico" />
    <script src="assets/js/hyper-config.js"></script>
    <link href="assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <style>
        .registro-item {
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .registro-item:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .registro-item .card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 2px solid var(--ct-primary);
        }
        
        [data-bs-theme="dark"] .registro-item .card-header {
            background: linear-gradient(135deg, #2c3136 0%, #1f2327 100%);
        }
        
        .btn-eliminar-registro {
            transition: all 0.2s ease;
        }
        
        .btn-eliminar-registro:hover {
            transform: scale(1.05);
        }
        
        .btn-agregar-registro {
            box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
            transition: all 0.2s ease;
        }
        
        .btn-agregar-registro:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.4);
        }
        
        .border-danger {
            border-width: 2px !important;
            animation: pulse-border 1.5s infinite;
        }
        
        @keyframes pulse-border {
            0%, 100% { border-color: #dc3545; }
            50% { border-color: #ff6b7a; }
        }
        
        .alert-warning {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        #btn-registrar:disabled {
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        #mensaje-bloqueado {
            animation: shake 0.5s ease-in-out;
            font-weight: bold;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        #alerta-exceso {
            animation: slideDown 0.3s ease-out;
        }
        
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
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
                                <h4 class="page-title">Registrar Horas de Trabajo</h4>
                                <p class="text-muted">Seleccione la fecha y orden de producción, luego agregue múltiples registros de horas con sus descripciones</p>
                            </div>
                        </div>
                    </div>

                    <?php if (isset($_SESSION['success'])): ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="ri-check-line me-2"></i>
                                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="ri-error-warning-line me-2"></i>
                                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <form id="form-registrar-horas" action="action/add-registro-horas.php" method="POST">
                                        
                                        <!-- Información global: Fecha -->
                                        <div class="row mb-4">
                                            <div class="col-md-12">
                                                <div class="alert alert-primary border-primary">
                                                    <h5 class="alert-heading">
                                                        <i class="ri-information-line"></i> Fecha del Registro
                                                    </h5>
                                                    <p class="mb-3">Seleccione la fecha para todos los períodos de trabajo</p>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label for="fecha" class="form-label fw-bold">
                                                                <i class="ri-calendar-event-line"></i> Fecha <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="date" class="form-control form-control-lg" id="fecha" name="fecha" 
                                                                   value="<?php echo date('Y-m-d'); ?>" 
                                                                   max="<?php echo date('Y-m-d'); ?>" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <hr class="mb-4">

                                        <!-- Contenedor de registros múltiples -->
                                        <div id="registros-container">
                                            <!-- Registro inicial -->
                                            <div class="registro-item card border-primary mb-3" data-registro="1">
                                                <div class="card-header bg-light">
                                                    <h5 class="mb-0">
                                                        <i class="ri-time-line text-primary"></i> Período de Trabajo #<span class="numero-registro">1</span>
                                                        <button type="button" class="btn btn-sm btn-danger float-end btn-eliminar-registro" style="display: none;">
                                                            <i class="ri-delete-bin-line"></i> Eliminar
                                                        </button>
                                                    </h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-3 mb-3">
                                                            <label class="form-label">Horas Trabajadas <span class="text-danger">*</span></label>
                                                            <div class="input-group input-group-lg">
                                                                <input type="number" class="form-control horas-input" 
                                                                       name="registros[0][horas_trabajadas]"
                                                                       step="<?php echo $incremento; ?>" 
                                                                       min="<?php echo $horas_min; ?>" 
                                                                       max="<?php echo $horas_max; ?>" 
                                                                       required 
                                                                       placeholder="<?php echo $horas_min; ?>">
                                                                <span class="input-group-text"><i class="ri-time-line"></i> hrs</span>
                                                            </div>
                                                            <small class="text-muted">Entre <?php echo number_format($horas_min, 1); ?> y <?php echo number_format($horas_max, 1); ?> horas</small>
                                                        </div>

                                                        <div class="col-md-9 mb-3">
                                                            <label class="form-label">Orden de Producción <span class="text-danger">*</span></label>
                                                            <select class="form-select form-select-lg orden-select" name="registros[0][orden_produccion_id]" required>
                                                                <option value="">Seleccione una orden...</option>
                                                                <?php foreach ($ordenes_activas as $orden): ?>
                                                                <option value="<?php echo $orden['id']; ?>" 
                                                                        data-prioridad="<?php echo $orden['prioridad']; ?>">
                                                                    <?php echo htmlspecialchars($orden['codigo_op']); ?> - 
                                                                    <?php echo htmlspecialchars($orden['nombre_producto']); ?>
                                                                    (<?php echo htmlspecialchars($orden['cliente']); ?>)
                                                                    <?php if ($orden['prioridad'] === 'urgente'): ?>
                                                                        🔴 URGENTE
                                                                    <?php endif; ?>
                                                                </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                            <small class="text-muted">Cada período puede tener una orden diferente</small>
                                                        </div>

                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">Descripción del Trabajo <span class="text-danger">*</span></label>
                                                            <textarea class="form-control descripcion-textarea" 
                                                                      name="registros[0][descripcion_trabajo]"
                                                                      rows="4" 
                                                                      required 
                                                                      placeholder="Describa detalladamente las actividades realizadas en este período de trabajo..."></textarea>
                                                            <small class="text-muted">Mínimo 5 caracteres</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer text-center bg-light">
                                                    <button type="button" class="btn btn-success btn-agregar-registro">
                                                        <i class="ri-add-circle-line"></i> Agregar Otro Período de Trabajo
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Resumen de horas totales -->
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <div id="alerta-horas" class="alert alert-info border-info">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-2">
                                                                <i class="ri-checkbox-circle-line"></i> <strong>Horas ya registradas hoy:</strong>
                                                                <span class="badge badge-info-lighten fs-6 ms-1">
                                                                    <span id="horas-registradas"><?php echo number_format($horas_registradas_hoy, 1); ?></span> hrs
                                                                </span>
                                                            </div>
                                                            <div class="mb-2">
                                                                <i class="ri-add-circle-line"></i> <strong>Nuevas horas a registrar:</strong>
                                                                <span class="badge badge-warning-lighten fs-6 ms-1">
                                                                    <span id="total-horas">0.0</span> hrs
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 text-md-end">
                                                            <div class="mb-2">
                                                                <strong>TOTAL DEL DÍA (<span class="text-capitalize" id="nombre-dia"><?php echo $horario_hoy['dia_semana']; ?></span>):</strong>
                                                            </div>
                                                            <h3 class="mb-0">
                                                                <span id="total-dia" class="text-info">0.0</span> / <span id="limite-dia"><?php echo number_format($horas_max, 1); ?></span> hrs
                                                            </h3>
                                                            <small class="text-muted">
                                                                <i class="ri-information-line"></i> Disponibles: <strong id="horas-disponibles"><?php echo number_format($horas_max - $horas_registradas_hoy, 1); ?></strong> hrs
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="mt-2">
                                                        <small class="text-muted">
                                                            <i class="ri-file-list-line"></i> Número de períodos: <strong id="total-registros">1</strong>
                                                        </small>
                                                    </div>
                                                    <small class="text-info d-block mt-2">
                                                        <i class="ri-calendar-line"></i> <?php echo $horario_hoy['descripcion']; ?> 
                                                        (<?php echo number_format($horario_hoy['horas_totales'], 1); ?> hrs permitidas)
                                                    </small>
                                                </div>
                                                
                                                <!-- Alerta cuando se excede el límite -->
                                                <div id="alerta-exceso" class="alert alert-danger border-danger" style="display: none;">
                                                    <h5 class="alert-heading">
                                                        <i class="ri-error-warning-line"></i> ¡Límite de Horas Excedido!
                                                    </h5>
                                                    <p class="mb-0">
                                                        El total de horas que intenta registrar (<strong id="exceso-total">0.0</strong> hrs) 
                                                        más las ya registradas (<strong id="exceso-registradas">0.0</strong> hrs)
                                                        superaría el límite de <strong><span id="exceso-limite"><?php echo number_format($horas_max, 1); ?></span></strong> horas 
                                                        configuradas para el <strong class="text-capitalize"><span id="exceso-dia"><?php echo $horario_hoy['dia_semana']; ?></span></strong>.
                                                    </p>
                                                    <p class="mb-0 mt-2">
                                                        <i class="ri-lightbulb-line"></i> Por favor, ajuste las horas en uno o más períodos para poder continuar.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <button type="submit" id="btn-registrar" class="btn btn-primary btn-lg">
                                                    <i class="ri-save-line"></i> Registrar Todos (<span id="count-registros">1</span>)
                                                </button>
                                                <a href="mis-horas.php" class="btn btn-secondary btn-lg">
                                                    <i class="ri-arrow-left-line"></i> Cancelar
                                                </a>
                                                <div id="mensaje-bloqueado" class="text-danger mt-2" style="display: none;">
                                                    <i class="ri-lock-line"></i> El botón está bloqueado porque se excede el límite de horas permitidas
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
    $(document).ready(function() {
        // Valores de configuración
        const HORAS_MIN = <?php echo $horas_min; ?>;
        let HORAS_MAX = <?php echo $horas_max; ?>;
        const INCREMENTO = <?php echo $incremento; ?>;
        let HORAS_REGISTRADAS_HOY = <?php echo $horas_registradas_hoy; ?>;
        let DIA_SEMANA_ACTUAL = '<?php echo $horario_hoy['dia_semana']; ?>';
        
        let contadorRegistros = 1;
        
        // Función para calcular total de horas y validar límite
        function calcularTotalHoras() {
            let totalNuevas = 0;
            $('.horas-input').each(function() {
                const valor = parseFloat($(this).val()) || 0;
                totalNuevas += valor;
            });
            
            const totalDia = HORAS_REGISTRADAS_HOY + totalNuevas;
            const horasDisponibles = HORAS_MAX - HORAS_REGISTRADAS_HOY;
            
            // Actualizar valores en la interfaz
            $('#total-horas').text(totalNuevas.toFixed(1));
            $('#horas-registradas').text(HORAS_REGISTRADAS_HOY.toFixed(1));
            $('#total-dia').text(totalDia.toFixed(1));
            $('#limite-dia').text(HORAS_MAX.toFixed(1));
            $('#horas-disponibles').text(horasDisponibles.toFixed(1));
            $('#total-registros').text($('.registro-item').length);
            $('#count-registros').text($('.registro-item').length);
            $('#nombre-dia').text(DIA_SEMANA_ACTUAL);
            
            // Validar si se excede el límite
            if (totalDia > HORAS_MAX) {
                // Mostrar alerta de exceso
                $('#alerta-horas').removeClass('alert-info alert-success alert-warning')
                                 .addClass('alert-danger border-danger');
                $('#total-dia').removeClass('text-info text-success text-warning')
                              .addClass('text-danger');
                $('#alerta-exceso').slideDown();
                $('#exceso-total').text(totalNuevas.toFixed(1));
                $('#exceso-registradas').text(HORAS_REGISTRADAS_HOY.toFixed(1));
                $('#exceso-limite').text(HORAS_MAX.toFixed(1));
                $('#exceso-dia').text(DIA_SEMANA_ACTUAL);
                
                // Deshabilitar botón de registrar
                $('#btn-registrar').prop('disabled', true)
                                  .removeClass('btn-primary')
                                  .addClass('btn-secondary');
                $('#mensaje-bloqueado').slideDown();
            } else {
                // Ocultar alerta de exceso
                $('#alerta-exceso').slideUp();
                $('#mensaje-bloqueado').slideUp();
                
                // Habilitar botón de registrar
                $('#btn-registrar').prop('disabled', false)
                                  .removeClass('btn-secondary')
                                  .addClass('btn-primary');
                
                // Cambiar color según el porcentaje usado
                const porcentajeUsado = (totalDia / HORAS_MAX) * 100;
                if (porcentajeUsado < 70) {
                    $('#alerta-horas').removeClass('alert-danger alert-warning alert-success')
                                     .addClass('alert-info border-info');
                    $('#total-dia').removeClass('text-danger text-warning text-success')
                                  .addClass('text-info');
                } else if (porcentajeUsado < 90) {
                    $('#alerta-horas').removeClass('alert-danger alert-info alert-success')
                                     .addClass('alert-warning border-warning');
                    $('#total-dia').removeClass('text-danger text-info text-success')
                                  .addClass('text-warning');
                } else {
                    $('#alerta-horas').removeClass('alert-danger alert-info alert-warning')
                                     .addClass('alert-success border-success');
                    $('#total-dia').removeClass('text-danger text-info text-warning')
                                  .addClass('text-success');
                }
            }
        }
        
        // Función para actualizar numeración de registros
        function actualizarNumeracion() {
            $('.registro-item').each(function(index) {
                $(this).find('.numero-registro').text(index + 1);
                $(this).attr('data-registro', index + 1);
                
                // Actualizar los nombres de los campos para el array
                $(this).find('.horas-input').attr('name', `registros[${index}][horas_trabajadas]`);
                $(this).find('.orden-select').attr('name', `registros[${index}][orden_produccion_id]`);
                $(this).find('.descripcion-textarea').attr('name', `registros[${index}][descripcion_trabajo]`);
            });
            
            // Mostrar/ocultar botón eliminar
            if ($('.registro-item').length > 1) {
                $('.btn-eliminar-registro').show();
            } else {
                $('.btn-eliminar-registro').hide();
            }
        }
        
        // Agregar nuevo registro
        $(document).on('click', '.btn-agregar-registro', function() {
            contadorRegistros++;
            
            // Obtener las opciones de órdenes del primer select
            const opcionesOrdenes = $('.orden-select').first().html();
            
            const nuevoRegistro = `
                <div class="registro-item card border-primary mb-3" data-registro="${contadorRegistros}">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="ri-time-line text-primary"></i> Período de Trabajo #<span class="numero-registro">${contadorRegistros}</span>
                            <button type="button" class="btn btn-sm btn-danger float-end btn-eliminar-registro">
                                <i class="ri-delete-bin-line"></i> Eliminar
                            </button>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Horas Trabajadas <span class="text-danger">*</span></label>
                                <div class="input-group input-group-lg">
                                    <input type="number" class="form-control horas-input" 
                                           name="registros[${contadorRegistros-1}][horas_trabajadas]"
                                           step="${INCREMENTO}" 
                                           min="${HORAS_MIN}" 
                                           max="${HORAS_MAX}" 
                                           required 
                                           placeholder="${HORAS_MIN}">
                                    <span class="input-group-text"><i class="ri-time-line"></i> hrs</span>
                                </div>
                                <small class="text-muted">Entre ${HORAS_MIN} y ${HORAS_MAX} horas</small>
                            </div>

                            <div class="col-md-9 mb-3">
                                <label class="form-label">Orden de Producción <span class="text-danger">*</span></label>
                                <select class="form-select form-select-lg orden-select" name="registros[${contadorRegistros-1}][orden_produccion_id]" required>
                                    ${opcionesOrdenes}
                                </select>
                                <small class="text-muted">Cada período puede tener una orden diferente</small>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Descripción del Trabajo <span class="text-danger">*</span></label>
                                <textarea class="form-control descripcion-textarea" 
                                          name="registros[${contadorRegistros-1}][descripcion_trabajo]"
                                          rows="4" 
                                          required 
                                          placeholder="Describa detalladamente las actividades realizadas en este período de trabajo..."></textarea>
                                <small class="text-muted">Mínimo 5 caracteres</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center bg-light">
                        <button type="button" class="btn btn-success btn-agregar-registro">
                            <i class="ri-add-circle-line"></i> Agregar Otro Período de Trabajo
                        </button>
                    </div>
                </div>
            `;
            
            $('#registros-container').append(nuevoRegistro);
            actualizarNumeracion();
            calcularTotalHoras();
            
            // Scroll suave al nuevo registro
            $('html, body').animate({
                scrollTop: $('.registro-item:last').offset().top - 100
            }, 500);
        });
        
        // Eliminar registro
        $(document).on('click', '.btn-eliminar-registro', function() {
            if ($('.registro-item').length > 1) {
                if (confirm('¿Está seguro de eliminar este registro?')) {
                    $(this).closest('.registro-item').fadeOut(300, function() {
                        $(this).remove();
                        actualizarNumeracion();
                        calcularTotalHoras();
                    });
                }
            }
        });
        
        // Calcular total al cambiar horas
        $(document).on('input', '.horas-input', function() {
            calcularTotalHoras();
        });
        
        // Resaltar orden urgente en cada select
        $(document).on('change', '.orden-select', function() {
            const prioridad = $(this).find(':selected').data('prioridad');
            if (prioridad === 'urgente') {
                $(this).addClass('border-danger');
            } else {
                $(this).removeClass('border-danger');
            }
        });
        
        // Recalcular cuando cambie la fecha y obtener horario del día
        $('#fecha').on('change', function() {
            const fechaSeleccionada = $(this).val();
            
            if (fechaSeleccionada) {
                // Mostrar indicador de carga
                $('#alerta-horas').addClass('opacity-50');
                
                // Limpiar mensajes previos de horario
                $('#alerta-horas small.text-info').remove();
                
                // Obtener horario del día seleccionado
                $.ajax({
                    url: 'action/get-horario-dia.php',
                    method: 'GET',
                    data: { fecha: fechaSeleccionada },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Actualizar variables globales
                            HORAS_MAX = response.horario.horas_totales;
                            HORAS_REGISTRADAS_HOY = response.horas_registradas;
                            DIA_SEMANA_ACTUAL = response.horario.dia_semana;
                            
                            // Actualizar límites en los inputs
                            $('.horas-input').attr('max', HORAS_MAX);
                            
                            // Actualizar interfaz
                            $('#horas-registradas').text(response.horas_registradas.toFixed(1));
                            $('#horas-disponibles').text(response.horas_disponibles.toFixed(1));
                            
                            // Mostrar información del día
                            if (response.horario.descripcion) {
                                let infoHorario = `<small class="text-info d-block mt-2">
                                    <i class="ri-calendar-line"></i> ${response.horario.descripcion} 
                                    (${response.horario.horas_totales} hrs permitidas)
                                </small>`;
                                $('#alerta-horas').append(infoHorario);
                            }
                            
                            // Verificar si es día no laborable
                            if (!response.horario.es_laborable) {
                                $('#alerta-horas').removeClass('alert-info alert-success alert-warning')
                                                 .addClass('alert-danger border-danger');
                                alert('⚠️ Atención: ' + DIA_SEMANA_ACTUAL.toUpperCase() + ' no es un día laborable según el horario configurado.');
                            }
                            
                            // Recalcular totales
                            calcularTotalHoras();
                        } else {
                            alert('Error al obtener horario del día: ' + response.message);
                        }
                        $('#alerta-horas').removeClass('opacity-50');
                    },
                    error: function() {
                        alert('Error al consultar el horario del día');
                        $('#alerta-horas').removeClass('opacity-50');
                    }
                });
            }
        });
        
        // Validación del formulario
        $('#form-registrar-horas').on('submit', function(e) {
            let errores = [];
            
            // Validar fecha
            const fecha = $('#fecha').val();
            if (!fecha) {
                errores.push('Debe seleccionar una fecha');
            }
            
            // Validar que no se exceda el límite de horas
            let totalNuevas = 0;
            $('.horas-input').each(function() {
                totalNuevas += parseFloat($(this).val()) || 0;
            });
            
            const totalDia = HORAS_REGISTRADAS_HOY + totalNuevas;
            if (totalDia > HORAS_MAX) {
                e.preventDefault();
                alert('¡No se puede registrar!\n\n' + 
                      'El total de horas (' + totalNuevas.toFixed(1) + ' hrs) más las ya registradas (' + 
                      HORAS_REGISTRADAS_HOY.toFixed(1) + ' hrs) superaría el límite de ' + 
                      HORAS_MAX.toFixed(1) + ' horas por día.\n\n' +
                      'Por favor, ajuste las horas antes de continuar.');
                return false;
            }
            
            // Validar cada registro
            $('.registro-item').each(function(index) {
                const numRegistro = index + 1;
                const horas = parseFloat($(this).find('.horas-input').val()) || 0;
                const orden = $(this).find('.orden-select').val();
                const descripcion = $(this).find('.descripcion-textarea').val();
                
                if (!orden) {
                    errores.push(`Período #${numRegistro}: Debe seleccionar una orden de producción`);
                }
                
                if (horas < HORAS_MIN || horas > HORAS_MAX) {
                    errores.push(`Período #${numRegistro}: Las horas deben estar entre ${HORAS_MIN} y ${HORAS_MAX}`);
                }
                
                if (horas % INCREMENTO !== 0) {
                    errores.push(`Período #${numRegistro}: Las horas deben ser múltiplos de ${INCREMENTO}`);
                }
                
                if (descripcion.length < 5) {
                    errores.push(`Período #${numRegistro}: La descripción debe tener al menos 5 caracteres`);
                }
            });
            
            if (errores.length > 0) {
                e.preventDefault();
                alert('Por favor corrija los siguientes errores:\n\n' + errores.join('\n'));
                return false;
            }
        });
        
        // Inicializar
        calcularTotalHoras();
    });
    </script>

</body>
</html>
