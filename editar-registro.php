<?php
session_start();
require_once 'includes/Class-usuario.php';
require_once 'includes/Class-registros-horas.php';
require_once 'includes/Class-configuracion.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    header("Location: login.php");
    exit;
}

// Obtener ID del registro
$id_registro = $_GET['id'] ?? null;

if (!$id_registro) {
    $_SESSION['error'] = "Registro no especificado";
    header("Location: mis-horas.php");
    exit;
}

$RegistroHoras_class = new RegistroHoras();
$registro = $RegistroHoras_class->obtenerRegistroPorId($id_registro);

if (!$registro) {
    $_SESSION['error'] = "Registro no encontrado";
    header("Location: mis-horas.php");
    exit;
}

// Verificar permisos: solo el usuario dueño puede editar
$es_propietario = $registro['usuario_id'] == $_SESSION['user_id'];

if (!$es_propietario) {
    $_SESSION['error'] = "No tiene permisos para editar este registro";
    header("Location: detalle-registro.php?id=" . $id_registro);
    exit;
}

// Verificar que el registro esté en estado 'registrado'
if ($registro['estado'] !== 'registrado') {
    $_SESSION['error'] = "No se puede editar un registro que ya fue " . $registro['estado'];
    header("Location: detalle-registro.php?id=" . $id_registro);
    exit;
}

// Verificar que no haya sido editado previamente
if (isset($registro['editado']) && $registro['editado'] == 1) {
    $_SESSION['error'] = "Este registro ya fue editado anteriormente. Solo se permite una edición por registro";
    header("Location: detalle-registro.php?id=" . $id_registro);
    exit;
}

// Obtener configuración de incrementos
$Configuracion_class = new Configuracion();
$incremento_horas = $Configuracion_class->obtenerValor('incremento_horas', 0.25);
$horas_maximas_dia = $Configuracion_class->obtenerValor('horas_maximas_por_dia', 24);

// Obtener lista de órdenes de producción activas
require_once 'includes/Class-ordenes-produccion.php';
$OrdenProduccion_class = new OrdenProduccion();
$ordenes_activas = $OrdenProduccion_class->obtenerOrdenesActivas();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Editar Registro | Time Production</title>
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

                    <!-- Page Title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <a href="detalle-registro.php?id=<?php echo $registro['id']; ?>" class="btn btn-secondary">
                                        <i class="mdi mdi-arrow-left me-1"></i> Cancelar
                                    </a>
                                </div>
                                <h4 class="page-title">Editar Registro de Horas</h4>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-8">
                            <!-- Alerta Informativa -->
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <i class="mdi mdi-alert-circle-outline me-2"></i>
                                <strong>Importante:</strong> Solo puedes editar este registro <strong>una vez</strong>. 
                                Asegúrate de revisar todos los datos antes de guardar los cambios.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>

                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-4">
                                        <i class="mdi mdi-pencil text-primary me-2"></i>
                                        Editar Información del Registro
                                    </h4>

                                    <form id="formEditarRegistro" action="action/update-registro-horas.php" method="POST">
                                        <input type="hidden" name="id" value="<?php echo $registro['id']; ?>">
                                        
                                        <!-- Información No Editable -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="fecha" class="form-label">
                                                    Fecha <span class="text-danger">*</span>
                                                </label>
                                                <input type="date" class="form-control" id="fecha" name="fecha" 
                                                       value="<?php echo $registro['fecha']; ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Registro Nº</label>
                                                <input type="text" class="form-control" value="#<?php echo str_pad($registro['id'], 6, '0', STR_PAD_LEFT); ?>" disabled>
                                            </div>
                                        </div>

                                        <!-- Orden de Producción -->
                                        <div class="mb-3">
                                            <label for="orden_produccion_id" class="form-label">
                                                Orden de Producción <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select" id="orden_produccion_id" name="orden_produccion_id" required>
                                                <option value="">Seleccione una orden de producción</option>
                                                <?php foreach ($ordenes_activas as $orden): ?>
                                                    <option value="<?php echo $orden['id']; ?>" 
                                                            <?php echo ($orden['id'] == $registro['orden_produccion_id']) ? 'selected' : ''; ?>
                                                            data-codigo="<?php echo htmlspecialchars($orden['codigo_op']); ?>"
                                                            data-producto="<?php echo htmlspecialchars($orden['nombre_producto']); ?>"
                                                            data-cliente="<?php echo htmlspecialchars($orden['cliente']); ?>">
                                                        <?php echo htmlspecialchars($orden['codigo_op']) . ' - ' . 
                                                                   htmlspecialchars($orden['nombre_producto']) . ' (' . 
                                                                   htmlspecialchars($orden['cliente']) . ')'; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <!-- Horas Trabajadas -->
                                        <div class="mb-3">
                                            <label for="horas_trabajadas" class="form-label">
                                                Horas Trabajadas <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select" id="horas_trabajadas" name="horas_trabajadas" required>
                                                <option value="">Seleccione las horas</option>
                                                <?php
                                                for ($i = $incremento_horas; $i <= $horas_maximas_dia; $i += $incremento_horas) {
                                                    $selected = (abs($i - $registro['horas_trabajadas']) < 0.01) ? 'selected' : '';
                                                    $horas_enteras = floor($i);
                                                    $minutos = ($i - $horas_enteras) * 60;
                                                    
                                                    if ($minutos > 0) {
                                                        $label = sprintf('%d h %d min', $horas_enteras, $minutos);
                                                    } else {
                                                        $label = sprintf('%d hora%s', $horas_enteras, $horas_enteras != 1 ? 's' : '');
                                                    }
                                                    
                                                    echo "<option value=\"$i\" $selected>$label</option>";
                                                }
                                                ?>
                                            </select>
                                            <small class="text-muted">
                                                Incremento configurado: <?php echo $incremento_horas; ?> horas
                                            </small>
                                        </div>

                                        <!-- Descripción del Trabajo -->
                                        <div class="mb-4">
                                            <label for="descripcion_trabajo" class="form-label">
                                                Descripción del Trabajo <span class="text-danger">*</span>
                                            </label>
                                            <textarea class="form-control" 
                                                      id="descripcion_trabajo" 
                                                      name="descripcion_trabajo" 
                                                      rows="4" 
                                                      required
                                                      placeholder="Describe el trabajo realizado durante este período"><?php echo htmlspecialchars($registro['descripcion_trabajo']); ?></textarea>
                                            <small class="text-muted">Mínimo 10 caracteres</small>
                                        </div>

                                        <!-- Botones -->
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="detalle-registro.php?id=<?php echo $registro['id']; ?>" class="btn btn-secondary">
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

                        <!-- Panel Lateral -->
                        <div class="col-lg-4">
                            <!-- Información del Registro Original -->
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">
                                        <i class="mdi mdi-information-outline me-1"></i>
                                        Datos Originales
                                    </h5>

                                    <div class="mb-3 pb-3 border-bottom">
                                        <div class="text-muted fw-semibold mb-2">Fecha Original</div>
                                        <p class="mb-0"><?php echo date('d/m/Y', strtotime($registro['fecha'])); ?></p>
                                    </div>

                                    <div class="mb-3 pb-3 border-bottom">
                                        <div class="text-muted fw-semibold mb-2">Fecha de Creación</div>
                                        <p class="mb-0"><small><?php echo date('d/m/Y H:i', strtotime($registro['created_at'])); ?></small></p>
                                    </div>

                                    <div class="mb-3 pb-3 border-bottom">
                                        <div class="text-muted fw-semibold mb-2">Orden Original</div>
                                        <p class="mb-0">
                                            <strong><?php echo htmlspecialchars($registro['codigo_op']); ?></strong><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($registro['nombre_producto']); ?></small>
                                        </p>
                                    </div>

                                    <div class="mb-3 pb-3 border-bottom">
                                        <div class="text-muted fw-semibold mb-2">Horas Originales</div>
                                        <p class="mb-0">
                                            <?php
                                            $horas_enteras = floor($registro['horas_trabajadas']);
                                            $minutos = ($registro['horas_trabajadas'] - $horas_enteras) * 60;
                                            if ($minutos > 0) {
                                                echo sprintf('%d h %d min', $horas_enteras, $minutos);
                                            } else {
                                                echo sprintf('%d hora%s', $horas_enteras, $horas_enteras != 1 ? 's' : '');
                                            }
                                            ?>
                                        </p>
                                    </div>

                                    <div class="alert alert-info mb-0">
                                        <i class="mdi mdi-information me-1"></i>
                                        <small>Esta es la información original de tu registro. Puedes compararla con los cambios que estás realizando.</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Recordatorio -->
                            <div class="card border-warning">
                                <div class="card-body">
                                    <h5 class="card-title text-warning mb-3">
                                        <i class="mdi mdi-alert me-1"></i>
                                        Recordatorio
                                    </h5>
                                    <ul class="mb-0 ps-3">
                                        <li class="mb-2">Solo puedes editar este registro <strong>una vez</strong></li>
                                        <li class="mb-2">Los cambios son permanentes</li>
                                        <li class="mb-2">Revisa bien antes de guardar</li>
                                        <li class="mb-0">No podrás editar después de validar</li>
                                    </ul>
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
            const REGISTRO_ID = <?php echo $registro['id']; ?>;
            let HORAS_MAX_DIA = <?php echo $horas_maximas_dia; ?>;
            let HORAS_REGISTRADAS_DIA = 0;
            let HORAS_DISPONIBLES = HORAS_MAX_DIA;
            
            // Mostrar información del día seleccionado y obtener horario
            function actualizarInfoDia() {
                const fecha = $('#fecha').val();
                if (fecha) {
                    // Crear contenedor de info si no existe
                    if ($('#info-dia-container').length === 0) {
                        $('#fecha').after('<div id="info-dia-container" class="mt-2"></div>');
                    }
                    
                    // Mostrar indicador de carga
                    $('#info-dia-container').html('<small class="text-muted"><i class="mdi mdi-loading mdi-spin"></i> Consultando horario...</small>');
                    
                    // Obtener horario del día seleccionado
                    $.ajax({
                        url: 'action/get-horario-dia.php',
                        method: 'GET',
                        data: { 
                            fecha: fecha,
                            excluir_id: REGISTRO_ID  // Excluir este registro del cálculo
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                const horario = response.horario;
                                HORAS_MAX_DIA = horario.horas_totales;
                                HORAS_REGISTRADAS_DIA = response.horas_registradas;
                                HORAS_DISPONIBLES = response.horas_disponibles;
                                
                                // Actualizar el máximo en el select de horas
                                $('#horas_trabajadas option').each(function() {
                                    const valor = parseFloat($(this).val());
                                    if (valor > HORAS_MAX_DIA) {
                                        $(this).prop('disabled', true).addClass('text-muted');
                                    } else {
                                        $(this).prop('disabled', false).removeClass('text-muted');
                                    }
                                });
                                
                                // Mostrar información del día
                                let infoHtml = '<div class="alert alert-' + (horario.es_laborable ? 'info' : 'warning') + ' border-' + (horario.es_laborable ? 'info' : 'warning') + ' mb-0">';
                                infoHtml += '<div class="d-flex justify-content-between align-items-start">';
                                infoHtml += '<div>';
                                infoHtml += '<strong><i class="mdi mdi-calendar"></i> ' + horario.dia_semana + '</strong><br>';
                                infoHtml += '<small>' + horario.descripcion + '</small><br>';
                                infoHtml += '<small class="text-muted">Jornada: ' + horario.horas_totales.toFixed(1) + ' hrs permitidas</small>';
                                infoHtml += '</div>';
                                infoHtml += '<div class="text-end">';
                                infoHtml += '<div class="badge bg-secondary mb-1">Ya registradas: ' + HORAS_REGISTRADAS_DIA.toFixed(1) + ' hrs</div><br>';
                                infoHtml += '<div class="badge bg-' + (HORAS_DISPONIBLES > 0 ? 'success' : 'danger') + '">Disponibles: ' + HORAS_DISPONIBLES.toFixed(1) + ' hrs</div>';
                                infoHtml += '</div>';
                                infoHtml += '</div>';
                                
                                if (!horario.es_laborable) {
                                    infoHtml += '<hr class="my-2">';
                                    infoHtml += '<small class="text-warning"><i class="mdi mdi-alert"></i> <strong>Atención:</strong> Este día no es laborable según la configuración del sistema</small>';
                                }
                                
                                if (HORAS_DISPONIBLES <= 0) {
                                    infoHtml += '<hr class="my-2">';
                                    infoHtml += '<small class="text-danger"><i class="mdi mdi-close-circle"></i> <strong>No hay horas disponibles</strong> para registrar en este día</small>';
                                }
                                
                                infoHtml += '</div>';
                                
                                $('#info-dia-container').html(infoHtml);
                                
                                // Validar horas seleccionadas
                                validarHorasSeleccionadas();
                            } else {
                                $('#info-dia-container').html('<small class="text-danger"><i class="mdi mdi-alert"></i> ' + response.message + '</small>');
                            }
                        },
                        error: function() {
                            $('#info-dia-container').html('<small class="text-danger"><i class="mdi mdi-alert"></i> Error al consultar el horario del día</small>');
                        }
                    });
                }
            }
            
            // Validar las horas seleccionadas
            function validarHorasSeleccionadas() {
                const horasSeleccionadas = parseFloat($('#horas_trabajadas').val()) || 0;
                const totalDia = HORAS_REGISTRADAS_DIA + horasSeleccionadas;
                
                // Remover alertas previas
                $('#alerta-horas-excedidas').remove();
                
                if (horasSeleccionadas > 0 && totalDia > HORAS_MAX_DIA) {
                    const alertaHtml = '<div id="alerta-horas-excedidas" class="alert alert-danger border-danger mt-2">' +
                        '<i class="mdi mdi-alert-circle"></i> <strong>¡Atención!</strong> ' +
                        'Las horas seleccionadas (' + horasSeleccionadas.toFixed(1) + ' hrs) más las ya registradas (' + 
                        HORAS_REGISTRADAS_DIA.toFixed(1) + ' hrs) suman ' + totalDia.toFixed(1) + ' hrs, ' +
                        'excediendo el límite de ' + HORAS_MAX_DIA.toFixed(1) + ' hrs para este día.' +
                        '</div>';
                    $('#horas_trabajadas').closest('.mb-3').after(alertaHtml);
                }
            }
            
            // Validar al cambiar las horas
            $('#horas_trabajadas').on('change', validarHorasSeleccionadas);
            
            // Actualizar info al cambiar fecha
            $('#fecha').on('change', actualizarInfoDia);
            
            // Inicializar con la fecha actual
            actualizarInfoDia();

            // Validación del formulario
            $('#formEditarRegistro').on('submit', function(e) {
                let isValid = true;
                let errorMsg = '';

                // Validar fecha
                if ($('#fecha').val() === '') {
                    isValid = false;
                    errorMsg += '- Debe seleccionar una fecha\n';
                }

                // Validar orden de producción
                if ($('#orden_produccion_id').val() === '') {
                    isValid = false;
                    errorMsg += '- Debe seleccionar una orden de producción\n';
                }

                // Validar horas
                const horasSeleccionadas = parseFloat($('#horas_trabajadas').val()) || 0;
                if (horasSeleccionadas === 0) {
                    isValid = false;
                    errorMsg += '- Debe seleccionar las horas trabajadas\n';
                }

                // Validar que no exceda las horas disponibles
                const totalDia = HORAS_REGISTRADAS_DIA + horasSeleccionadas;
                if (totalDia > HORAS_MAX_DIA) {
                    isValid = false;
                    errorMsg += '- Las horas exceden el límite permitido para este día\n';
                    errorMsg += '  (Ya registradas: ' + HORAS_REGISTRADAS_DIA.toFixed(1) + ' hrs, ';
                    errorMsg += 'Seleccionadas: ' + horasSeleccionadas.toFixed(1) + ' hrs, ';
                    errorMsg += 'Total: ' + totalDia.toFixed(1) + ' hrs, ';
                    errorMsg += 'Límite: ' + HORAS_MAX_DIA.toFixed(1) + ' hrs)\n';
                }

                // Validar descripción
                const descripcion = $('#descripcion_trabajo').val().trim();
                if (descripcion.length < 10) {
                    isValid = false;
                    errorMsg += '- La descripción debe tener al menos 10 caracteres\n';
                }

                if (!isValid) {
                    e.preventDefault();
                    alert('Por favor corrija los siguientes errores:\n\n' + errorMsg);
                    return false;
                }

                // Confirmación final con información de horas
                let confirmMsg = '¿Estás seguro de guardar estos cambios?\n\n';
                confirmMsg += 'Resumen:\n';
                confirmMsg += '• Horas seleccionadas: ' + horasSeleccionadas.toFixed(1) + ' hrs\n';
                confirmMsg += '• Total del día: ' + totalDia.toFixed(1) + ' / ' + HORAS_MAX_DIA.toFixed(1) + ' hrs\n';
                confirmMsg += '• Disponibles: ' + (HORAS_MAX_DIA - totalDia).toFixed(1) + ' hrs\n\n';
                confirmMsg += '⚠️ Recuerda que solo puedes editar este registro UNA VEZ.';
                
                if (!confirm(confirmMsg)) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    </script>

</body>
</html>
