<?php
// solicitar-horas-extras.php - Formulario para solicitar horas extras
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

// Obtener configuración de horas extras
$Configuracion = new Configuracion();
$config_horas = $Configuracion->obtenerConfigHoras();
$horas_min = $config_horas['horas_minimas_por_registro'] ?? 0.5;
$horas_max_extras = $config_horas['horas_maximas_extras'] ?? 4.0;
$incremento = $config_horas['incremento_horas'] ?? 0.5;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Solicitar Horas Extras | Time Production</title>
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
                                <h4 class="page-title">Solicitar Horas Extras</h4>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="alert alert-warning">
                                        <i class="ri-information-line"></i>
                                        <strong>Importante:</strong> Las horas extras requieren aprobación del administrador. 
                                        Asegúrese de justificar adecuadamente su solicitud.
                                    </div>

                                    <form id="form-solicitar-hora-extra" action="action/add-solicitud-hora-extra.php" method="POST">
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="orden_produccion_id" class="form-label">Orden de Producción <span class="text-danger">*</span></label>
                                                <select class="form-select" id="orden_produccion_id" name="orden_produccion_id" required>
                                                    <option value="">Seleccione una orden...</option>
                                                    <?php foreach ($ordenes_activas as $orden): ?>
                                                    <option value="<?php echo $orden['id']; ?>">
                                                        <?php echo htmlspecialchars($orden['codigo_op']); ?> - 
                                                        <?php echo htmlspecialchars($orden['nombre_producto']); ?>
                                                        (<?php echo htmlspecialchars($orden['cliente']); ?>)
                                                    </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="fecha" class="form-label">Fecha <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control" id="fecha" name="fecha" 
                                                       value="<?php echo date('Y-m-d'); ?>" required>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="hora_inicio" class="form-label">Hora de Inicio <span class="text-danger">*</span></label>
                                                <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
                                                <small class="text-muted">Hora en que comenzarán las horas extras</small>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="hora_fin" class="form-label">Hora de Finalización <span class="text-danger">*</span></label>
                                                <input type="time" class="form-control" id="hora_fin" name="hora_fin" required>
                                                <small class="text-muted">Hora en que finalizarán las horas extras</small>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <div class="alert alert-info">
                                                    <i class="ri-information-line"></i>
                                                    <strong>Total de horas extras:</strong> 
                                                    <span id="total-horas-calculadas">0.00</span> horas
                                                    <small class="d-block mt-1">Las horas se calcularán automáticamente según el rango horario.</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label for="descripcion_trabajo" class="form-label">Descripción del Trabajo <span class="text-muted">(Opcional)</span></label>
                                                <textarea class="form-control" id="descripcion_trabajo" name="descripcion_trabajo" 
                                                          rows="4" 
                                                          placeholder="Describa las actividades que realizará durante las horas extras (opcional)..."></textarea>
                                                <small class="text-muted">Campo opcional</small>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-warning">
                                                    <i class="ri-send-plane-line"></i> Enviar Solicitud
                                                </button>
                                                <a href="horas-extras.php" class="btn btn-secondary">
                                                    <i class="ri-arrow-left-line"></i> Volver
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
    $(document).ready(function() {
        // Validación del formulario
        $('#form-solicitar-horas-extras').on('submit', function(e) {
            const horasExtras = parseFloat($('#horas_extras').val());
            const descripcion = $('#descripcion_trabajo').val();
            const horaInicio = $('#hora_inicio').val();
            const horaFin = $('#hora_fin').val();
            
            if (!horaInicio || !horaFin) {
                e.preventDefault();
                alert('Por favor, ingrese la hora de inicio y fin de las horas extras.');
                return false;
            }
            
            const totalHoras = parseFloat($('#total-horas-calculadas').text());
            
            // Validar que sea múltiplo de 0.5
            const esMultiploDe05 = (totalHoras * 2) % 1 === 0;
            
            if (totalHoras < 0.5 || totalHoras > 8) {
                e.preventDefault();
                alert('Las horas extras deben estar entre 0.5 y 8 horas. Total calculado: ' + totalHoras.toFixed(1) + ' horas');
                return false;
            }
            
            if (!esMultiploDe05) {
                e.preventDefault();
                alert('Las horas extras deben ser múltiplo de 0.5 (ejemplo: 0.5, 1.0, 1.5, 2.0, etc.). Total calculado: ' + totalHoras.toFixed(2) + ' horas. Por favor, ajuste la hora de inicio o fin.');
                return false;
            }
        });
        
        // Función para calcular horas entre dos tiempos
        function calcularHoras() {
            const horaInicio = $('#hora_inicio').val();
            const horaFin = $('#hora_fin').val();
            
            if (horaInicio && horaFin) {
                const inicio = new Date('2000-01-01 ' + horaInicio);
                let fin = new Date('2000-01-01 ' + horaFin);
                
                // Si la hora de fin es menor que la de inicio, asumimos que cruza medianoche
                if (fin < inicio) {
                    fin = new Date('2000-01-02 ' + horaFin);
                }
                
                const diferenciaMs = fin - inicio;
                const horas = diferenciaMs / (1000 * 60 * 60);
                const horasRedondeadas = Math.round(horas * 2) / 2; // Redondear a 0.5
                
                $('#total-horas-calculadas').text(horasRedondeadas.toFixed(1));
                
                // Validar que las horas sean múltiplo de 0.5
                const esMultiploDe05 = (horasRedondeadas * 2) % 1 === 0;
                
                // Validar rango y múltiplo de 0.5
                if (horasRedondeadas < 0.5 || horasRedondeadas > 8 || !esMultiploDe05) {
                    $('#total-horas-calculadas').parent().removeClass('alert-info').addClass('alert-danger');
                    if (!esMultiploDe05) {
                        $('#total-horas-calculadas').parent().find('small').text('Las horas deben ser múltiplo de 0.5 (ejemplo: 0.5, 1.0, 1.5, 2.0, etc.)');
                    } else {
                        $('#total-horas-calculadas').parent().find('small').text('Las horas deben estar entre 0.5 y 8 horas.');
                    }
                } else {
                    $('#total-horas-calculadas').parent().removeClass('alert-danger').addClass('alert-info');
                    $('#total-horas-calculadas').parent().find('small').text('Las horas se calcularán automáticamente según el rango horario.');
                }
            }
        }
        
        // Calcular horas al cambiar los campos
        $('#hora_inicio, #hora_fin').on('change', calcularHoras);
    });
    </script>

</body>
</html>
