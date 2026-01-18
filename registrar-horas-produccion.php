<?php
    // registrar-horas-produccion.php - Vista para rol de producción: registro de horas como hoja de Excel
    session_start();
    require_once 'includes/Class-usuario.php';
    require_once 'includes/Class-ordenes-produccion.php';
    require_once 'includes/Class-registro-horas.php';
    require_once 'includes/Class-horas-produccion.php';

    $Usuario_class = new Usuario();

    if (! $Usuario_class->usuarioLogueado()) {
        header("Location: login.php");
        exit;
    }

    $usuario_id    = $_SESSION['user_id'];
    $es_produccion = isset($_SESSION['user_rol']) && ($_SESSION['user_rol'] === 'produccion' || $_SESSION['user_rol'] === 'Produccion' || $_SESSION['user_rol'] === 'administrador' || $_SESSION['user_rol'] === 'Administrador' || $_SESSION['user_rol'] === 'root');

    // Solo permitir acceso a rol producción
    if (! $es_produccion) {
        header("Location: index.php");
        exit;
    }

    // Instanciar clases
    $database              = new Database();
    $conn                  = $database->getConnection();
    $Usuario_class         = new Usuario();
    $OrdenProduccion_class = new OrdenProduccion();
    $RegistroHoras_class   = new RegistroHoras();
    $HorasProduccion_class = new HorasProduccion($conn);

    // Obtener listas para selects
    $trabajadores    = $Usuario_class->obtenerUsuarios(['rol' => 'trabajador']);
    $ordenes_activas = $OrdenProduccion_class->obtenerOrdenes(['estado' => 'activa']);

    // Obtener registros existentes
    $resultado_registros  = $HorasProduccion_class->obtenerRegistros();
    $registros_existentes = $resultado_registros['success'] ? $resultado_registros['registros'] : [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Registro de Horas - Producción | Time Production</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta content="Sistema de gestión de tiempos y producción - Talleres Unidos Ltda" name="description" />
    <meta content="Talleres Unidos Ltda" name="author" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico" />

    <!-- Daterangepicker css -->
    <link rel="stylesheet" href="assets/vendor/daterangepicker/daterangepicker.css" />

    <!-- Vector Map css -->
    <link rel="stylesheet" href="assets/vendor/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css" />

    <!-- Theme Config Js -->
    <script src="assets/js/hyper-config.js"></script>

    <!-- Plugin css -->
    <link rel="stylesheet" href="assets/vendor/jquery-toast-plugin/jquery.toast.min.css">

    <!-- App css -->
    <link href="assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />

    <!-- Select2 css -->
    <link href="assets/vendor/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

    <!-- Custom CSS for Excel-like view -->
    <style>
    .excel-table {
        border-collapse: collapse;
        width: 100%;
        font-size: 0.875rem;
    }
    .excel-table th, .excel-table td {
        border: 1px solid #dee2e6;
        padding: 0.5rem;
        text-align: center;
        vertical-align: middle;
    }
    .excel-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .excel-table input, .excel-table select, .excel-table textarea {
        width: 100%;
        border: none;
        padding: 0.25rem;
        text-align: center;
    }
    .excel-table input:focus, .excel-table select:focus, .excel-table textarea:focus {
        outline: 2px solid #007bff;
        border-radius: 0;
    }
    .hora-input {
        width: 60px;
    }
    .dia-input {
        width: 50px;
        text-align: center;
    }
    .total-horas {
        background-color: #e9ecef;
        font-weight: bold;
    }
    .scroll-container {
        max-height: 70vh;
        overflow-y: auto;
        border: 1px solid #dee2e6;
    }
    .btn-add-row {
        margin-top: 1rem;
    }
    </style>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">
        <!-- ========== Topbar Start ========== -->
        <?php include "includes/header.php"; ?>
        <!-- ========== Topbar End ========== -->

        <?php include "includes/sidebar.php"; ?>

        <!-- Toast Container -->
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
            <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="me-2" id="toastIcon"></i>
                    <strong class="me-auto" id="toastTitle">Notificación</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body" id="toastMessage">
                    Mensaje del sistema
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">
                <!-- Start Content-->
                <div class="container-fluid">

                    <!-- Page Title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Registro de Horas - Producción</li>
                                    </ol>
                                </div>
                                <h4 class="page-title">Registro de Horas de Trabajo</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Mensajes de éxito o error -->
                    <?php
                        // Mostrar alerta de éxito si existe
                        if (isset($_SESSION['exito'])) {
                            echo '<div class="alert alert-success alert-dismissible fade show" role="alert" id="autoCloseAlert">
                            <i class="ri-check-line me-1 align-middle font-16"></i> ' . htmlspecialchars($_SESSION['exito']) . '
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
                            unset($_SESSION['exito']);
                        }

                        // Mostrar alerta de error si existe
                        if (isset($_SESSION['error'])) {
                            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert" id="autoCloseAlert">
                            <i class="ri-close-line me-1 align-middle font-16"></i> ' . htmlspecialchars($_SESSION['error']) . '
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
                            unset($_SESSION['error']);
                        }
                    ?>

                    <!-- Formulario de Registro -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="header-title">Horas de Producción</h4>
                                    <p class="card-subtitle">Complete la información de horas trabajadas por cada trabajador.</p>
                                </div>
                                <div class="card-body">
                                    <!-- Selector de Mes -->
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="mesSelector" class="form-label">Seleccionar Mes</label>
                                            <input type="month" id="mesSelector" class="form-control" value="<?php echo date('Y-m'); ?>">
                                        </div>
                                        <div class="col-md-8 text-end align-self-end">
                                            <button type="button" id="btnAddRow" class="btn btn-primary">
                                                <i class="ri-add-line"></i> Agregar Fila
                                            </button>
                                        </div>
                                    </div>
                                    <form id="registroHorasForm">
                                        <div class="scroll-container">
                                            <table class="excel-table" id="registroTable">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 60px;">Día</th>
                                                        <th style="width: 120px;">OP</th>
                                                        <th style="width: 200px;">Descripción</th>
                                                        <th style="width: 150px;">Trabajador</th>
                                                        <th style="width: 100px;">Máquina</th>
                                                        <th style="width: 60px;">HR</th>
                                                        <th style="width: 60px;">HED</th>
                                                        <th style="width: 60px;">HEN</th>
                                                        <th style="width: 60px;">HEFD</th>
                                                        <th style="width: 60px;">HEFN</th>
                                                        <th style="width: 100px;">Permiso</th>
                                                        <th style="width: 80px;">Comida</th>
                                                        <th style="width: 80px;">Total</th>
                                                        <th style="width: 150px;">Observaciones</th>
                                                        <th style="width: 100px;">Horario</th>
                                                        <th style="width: 50px;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="registroBody">
                                                    <!-- Registros existentes -->
                                                    <?php foreach ($registros_existentes as $index => $registro): ?>
                                                    <tr>
                                                        <td>
                                                            <input type="number" name="registros[<?php echo $index; ?>][dia]" class="form-control dia-input" value="<?php echo date('j', strtotime($registro['fecha'])); ?>" min="1" max="31" required>
                                                            <input type="hidden" name="registros[<?php echo $index; ?>][id]" value="<?php echo $registro['id']; ?>">
                                                            <input type="hidden" name="registros[<?php echo $index; ?>][fecha]" value="<?php echo htmlspecialchars($registro['fecha']); ?>">
                                                        </td>
                                                        <td>
                                                            <select name="registros[<?php echo $index; ?>][op]" class="form-control select2-op" required>
                                                                <option value="">Seleccionar OP</option>
                                                                <?php foreach ($ordenes_activas as $orden): ?>
                                                                <option value="<?php echo $orden['codigo_op']; ?>"<?php echo($orden['codigo_op'] == $registro['codigo_op']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($orden['codigo_op'] . ' - ' . $orden['nombre_producto']); ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </td>
                                                        <td><textarea name="registros[<?php echo $index; ?>][descripcion]" class="form-control" rows="1" placeholder="Descripción de actividades"><?php echo htmlspecialchars($registro['descripcion'] ?? ''); ?></textarea></td>
                                                        <td>
                                                            <select name="registros[<?php echo $index; ?>][trabajador_id]" class="form-control select2-trabajador" required>
                                                                <option value="">Seleccionar</option>
                                                                <?php foreach ($trabajadores as $trabajador): ?>
                                                                <option value="<?php echo $trabajador['id']; ?>"<?php echo($trabajador['id'] == $registro['usuario_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($trabajador['nombre_completo']); ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" name="registros[<?php echo $index; ?>][maquina]" class="form-control" value="<?php echo htmlspecialchars($registro['maquina']); ?>" placeholder="Código" maxlength="20"></td>
                                                        <td><input type="number" name="registros[<?php echo $index; ?>][hr]" class="form-control hora-input hr-input" step="0.5" min="0" max="24" value="<?php echo $registro['hr']; ?>"></td>
                                                        <td><input type="number" name="registros[<?php echo $index; ?>][hed]" class="form-control hora-input hed-input" step="0.5" min="0" max="24" value="<?php echo $registro['hed']; ?>"></td>
                                                        <td><input type="number" name="registros[<?php echo $index; ?>][hen]" class="form-control hora-input hen-input" step="0.5" min="0" max="24" value="<?php echo $registro['hen']; ?>"></td>
                                                        <td><input type="number" name="registros[<?php echo $index; ?>][hefd]" class="form-control hora-input hefd-input" step="0.5" min="0" max="24" value="<?php echo $registro['hefd']; ?>"></td>
                                                        <td><input type="number" name="registros[<?php echo $index; ?>][hefn]" class="form-control hora-input hefn-input" step="0.5" min="0" max="24" value="<?php echo $registro['hefn']; ?>"></td>
                                                        <td><input type="text" name="registros[<?php echo $index; ?>][permiso]" class="form-control" value="<?php echo htmlspecialchars($registro['permiso']); ?>" placeholder="Permiso" maxlength="50"></td>
                                                        <td><input type="checkbox" name="registros[<?php echo $index; ?>][comida]" value="1"<?php echo($registro['comida'] == 1) ? 'checked' : ''; ?>></td>
                                                        <td><input type="text" name="registros[<?php echo $index; ?>][total_horas]" class="form-control total-horas" value="<?php echo $registro['total_horas']; ?>" readonly></td>
                                                        <td><textarea name="registros[<?php echo $index; ?>][observaciones]" class="form-control" rows="1" placeholder="Observaciones"><?php echo htmlspecialchars($registro['observaciones']); ?></textarea></td>
                                                        <td><input type="text" name="registros[<?php echo $index; ?>][horario]" class="form-control" value="<?php echo htmlspecialchars($registro['horario']); ?>" placeholder="ej: 7 am - 5 pm"></td>
                                                        <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="ri-delete-bin-line"></i></button></td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                    <!-- Filas dinámicas se agregarán aquí -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </form>
                                    <br>
                                    <button type="button" id="btnGuardar" class="btn btn-success">
                                            <i class="ri-save-line"></i> Guardar Cambios
                                        </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- container -->
            </div>
            <!-- content -->

            <!-- Footer Start -->
            <?php include "includes/footer.php"; ?>
            <!-- end Footer -->
        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->
    </div>
    <!-- END wrapper -->

    <!-- Vendor js -->
    <script src="assets/js/vendor.min.js"></script>
    
    <!-- App js -->
    <script src="assets/js/app.min.js"></script>

    <!-- Select2 js -->
    <script src="assets/vendor/select2/js/select2.min.js"></script>

    <script>
    $(document).ready(function() {
        // Función para mostrar toast
        function showToast(message, type = 'success') {
            const toastEl = document.getElementById('liveToast');
            const toastTitle = document.getElementById('toastTitle');
            const toastMessage = document.getElementById('toastMessage');
            const toastIcon = document.getElementById('toastIcon');
            const toastHeader = toastEl.querySelector('.toast-header');

            // Configurar según el tipo
            if (type === 'success') {
                toastTitle.textContent = 'Éxito';
                toastIcon.className = 'ri-check-line text-success me-2';
                toastHeader.className = 'toast-header bg-success-subtle';
            } else if (type === 'error') {
                toastTitle.textContent = 'Error';
                toastIcon.className = 'ri-close-line text-danger me-2';
                toastHeader.className = 'toast-header bg-danger-subtle';
            } else if (type === 'warning') {
                toastTitle.textContent = 'Advertencia';
                toastIcon.className = 'ri-alert-line text-warning me-2';
                toastHeader.className = 'toast-header bg-warning-subtle';
            }

            toastMessage.textContent = message;

            const toast = new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: 3000
            });
            toast.show();
        }

        // Inicializar Select2
        $('.select2-trabajador').select2({
            placeholder: 'Seleccionar trabajador',
            allowClear: true,
            width: '100%'
        });

        $('.select2-op').select2({
            placeholder: 'Seleccionar OP',
            allowClear: true,
            width: '100%',
            templateSelection: function (option) {
                // Mostrar solo el código OP cuando está seleccionado
                if (option.text && option.text.includes(' - ')) {
                    return option.text.split(' - ')[0];
                }
                return option.text;
            }
        });

        // Función para calcular total de horas
        function calcularTotal(row) {
            var hr = parseFloat($(row).find('.hr-input').val()) || 0;
            var hed = parseFloat($(row).find('.hed-input').val()) || 0;
            var hen = parseFloat($(row).find('.hen-input').val()) || 0;
            var hefd = parseFloat($(row).find('.hefd-input').val()) || 0;
            var hefn = parseFloat($(row).find('.hefn-input').val()) || 0;
            var total = hr + hed + hen + hefd + hefn;
            $(row).find('.total-horas').val(total.toFixed(1));
        }

        // Función para verificar si debe agregar una nueva fila
        function checkAndAddRow() {
            var lastRow = $('#registroBody tr:last');
            var hrInput = lastRow.find('.hr-input');

            // Si la última fila tiene un valor en HR, agregar una nueva fila
            if (hrInput.val() && hrInput.val().trim() !== '') {
                addRow();
            }
        }

        // Función para agregar una nueva fila
        function addRow() {
            var rowCount = $('#registroBody tr').length;
            var newRow = `
                <tr>
                    <td><input type="number" name="registros[${rowCount}][dia]" class="form-control dia-input" min="1" max="31" placeholder="Día" required></td>
                    <td>
                        <select name="registros[${rowCount}][op]" class="form-control select2-op" required>
                            <option value="">Seleccionar OP</option>
                            <?php foreach ($ordenes_activas as $orden): ?>
                            <option value="<?php echo $orden['codigo_op']; ?>"><?php echo htmlspecialchars($orden['codigo_op'] . ' - ' . $orden['nombre_producto']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><textarea name="registros[${rowCount}][descripcion]" class="form-control" rows="1" placeholder="Descripción de actividades"></textarea></td>
                    <td>
                        <select name="registros[${rowCount}][trabajador_id]" class="form-control select2-trabajador" required>
                            <option value="">Seleccionar</option>
                            <?php foreach ($trabajadores as $trabajador): ?>
                            <option value="<?php echo $trabajador['id']; ?>"><?php echo htmlspecialchars($trabajador['nombre_completo']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="text" name="registros[${rowCount}][maquina]" class="form-control" placeholder="Código" maxlength="20"></td>
                    <td><input type="number" name="registros[${rowCount}][hr]" class="form-control hora-input hr-input" step="0.5" min="0" max="24"></td>
                    <td><input type="number" name="registros[${rowCount}][hed]" class="form-control hora-input hed-input" step="0.5" min="0" max="24"></td>
                    <td><input type="number" name="registros[${rowCount}][hen]" class="form-control hora-input hen-input" step="0.5" min="0" max="24"></td>
                    <td><input type="number" name="registros[${rowCount}][hefd]" class="form-control hora-input hefd-input" step="0.5" min="0" max="24"></td>
                    <td><input type="number" name="registros[${rowCount}][hefn]" class="form-control hora-input hefn-input" step="0.5" min="0" max="24"></td>
                    <td><input type="text" name="registros[${rowCount}][permiso]" class="form-control" placeholder="Permiso" maxlength="50"></td>
                    <td><input type="checkbox" name="registros[${rowCount}][comida]" value="1"></td>
                    <td><input type="text" name="registros[${rowCount}][total_horas]" class="form-control total-horas" readonly></td>
                    <td><textarea name="registros[${rowCount}][observaciones]" class="form-control" rows="1" placeholder="Observaciones"></textarea></td>
                    <td><input type="text" name="registros[${rowCount}][horario]" class="form-control" value="7 am - 5 pm" placeholder="ej: 7 am - 5 pm"></td>
                    <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="ri-delete-bin-line"></i></button></td>
                </tr>
            `;
            $('#registroBody').append(newRow);

            // Re-inicializar Select2 para la nueva fila
            $('.select2-trabajador').select2({
                placeholder: 'Seleccionar trabajador',
                allowClear: true,
                width: '100%'
            });

            $('.select2-op').select2({
                placeholder: 'Seleccionar OP',
                allowClear: true,
                width: '100%',
                templateSelection: function (option) {
                    // Mostrar solo el código OP cuando está seleccionado
                    if (option.text && option.text.includes(' - ')) {
                        return option.text.split(' - ')[0];
                    }
                    return option.text;
                }
            });
        }

        // Agregar fila inicial si no hay registros
        if ($('#registroBody tr').length === 0) {
            addRow();
        }

        // Evento para cambio de mes
        $('#mesSelector').on('change', function() {
            cargarRegistrosPorMes($(this).val());
        });

        // Función para cargar registros por mes
        function cargarRegistrosPorMes(mes) {
            $.ajax({
                url: 'action/obtener-registros-mes.php',
                type: 'GET',
                data: { mes: mes },
                success: function(response) {
                    if (response.success) {
                        $('#registroBody').empty();
                        
                        if (response.registros && response.registros.length > 0) {
                            // Cargar registros existentes
                            response.registros.forEach(function(registro, index) {
                                var dia = new Date(registro.fecha + ' 00:00:00').getDate();
                                var row = crearFilaConDatos(registro, dia, index);
                                $('#registroBody').append(row);
                            });
                        }
                        
                        // Agregar fila vacía al final
                        addRow();
                        
                        // Re-inicializar Select2
                        $('.select2-trabajador').select2({
                            placeholder: 'Seleccionar trabajador',
                            allowClear: true,
                            width: '100%'
                        });

                        $('.select2-op').select2({
                            placeholder: 'Seleccionar OP',
                            allowClear: true,
                            width: '100%',
                            templateSelection: function (option) {
                                if (option.text && option.text.includes(' - ')) {
                                    return option.text.split(' - ')[0];
                                }
                                return option.text;
                            }
                        });
                    } else {
                        showToast('Error al cargar registros: ' + response.message, 'error');
                    }
                },
                error: function() {
                    showToast('Error de comunicación al cargar registros', 'error');
                }
            });
        }

        // Función para crear fila con datos
        function crearFilaConDatos(registro, dia, index) {
            var trabajadorOptions = '';
            <?php foreach ($trabajadores as $trabajador): ?>
            trabajadorOptions += '<option value="<?php echo $trabajador['id']; ?>"' + (registro.usuario_id == <?php echo $trabajador['id']; ?> ? ' selected' : '') + '><?php echo htmlspecialchars($trabajador['nombre_completo']); ?></option>';
            <?php endforeach; ?>
            
            var opOptions = '<option value="">Seleccionar OP</option>';
            <?php foreach ($ordenes_activas as $orden): ?>
            opOptions += '<option value="<?php echo $orden['codigo_op']; ?>"' + (registro.codigo_op === '<?php echo $orden['codigo_op']; ?>' ? ' selected' : '') + '><?php echo htmlspecialchars($orden['codigo_op'] . ' - ' . $orden['nombre_producto']); ?></option>';
            <?php endforeach; ?>
            
            return `
                <tr>
                    <td>
                        <input type="number" name="registros[${index}][dia]" class="form-control dia-input" value="${dia}" min="1" max="31" required>
                        <input type="hidden" name="registros[${index}][id]" value="${registro.id}">
                        <input type="hidden" name="registros[${index}][fecha]" value="${registro.fecha}">
                    </td>
                    <td>
                        <select name="registros[${index}][op]" class="form-control select2-op" required>
                            ${opOptions}
                        </select>
                    </td>
                    <td><textarea name="registros[${index}][descripcion]" class="form-control" rows="1" placeholder="Descripción de actividades">${registro.descripcion || ''}</textarea></td>
                    <td>
                        <select name="registros[${index}][trabajador_id]" class="form-control select2-trabajador" required>
                            <option value="">Seleccionar</option>
                            ${trabajadorOptions}
                        </select>
                    </td>
                    <td><input type="text" name="registros[${index}][maquina]" class="form-control" value="${registro.maquina || ''}" placeholder="Código" maxlength="20"></td>
                    <td><input type="number" name="registros[${index}][hr]" class="form-control hora-input hr-input" step="0.5" min="0" max="24" value="${registro.hr}"></td>
                    <td><input type="number" name="registros[${index}][hed]" class="form-control hora-input hed-input" step="0.5" min="0" max="24" value="${registro.hed}"></td>
                    <td><input type="number" name="registros[${index}][hen]" class="form-control hora-input hen-input" step="0.5" min="0" max="24" value="${registro.hen}"></td>
                    <td><input type="number" name="registros[${index}][hefd]" class="form-control hora-input hefd-input" step="0.5" min="0" max="24" value="${registro.hefd}"></td>
                    <td><input type="number" name="registros[${index}][hefn]" class="form-control hora-input hefn-input" step="0.5" min="0" max="24" value="${registro.hefn}"></td>
                    <td><input type="text" name="registros[${index}][permiso]" class="form-control" value="${registro.permiso || ''}" placeholder="Permiso" maxlength="50"></td>
                    <td><input type="checkbox" name="registros[${index}][comida]" value="1" ${registro.comida == 1 ? 'checked' : ''}></td>
                    <td><input type="text" name="registros[${index}][total_horas]" class="form-control total-horas" value="${registro.total_horas}" readonly></td>
                    <td><textarea name="registros[${index}][observaciones]" class="form-control" rows="1" placeholder="Observaciones">${registro.observaciones || ''}</textarea></td>
                    <td><input type="text" name="registros[${index}][horario]" class="form-control" value="${registro.horario || '7 am - 5 pm'}" placeholder="ej: 7 am - 5 pm"></td>
                    <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="ri-delete-bin-line"></i></button></td>
                </tr>
            `;
        }

        // Evento para botón de agregar fila manual
        $('#btnAddRow').on('click', function() {
            addRow();
        });

        // Evento para guardar cambios vía AJAX
        $('#btnGuardar').on('click', function() {
            var formData = $('#registroHorasForm').serializeArray();
            var registros = [];
            var mesSeleccionado = $('#mesSelector').val(); // formato: YYYY-MM

            // Procesar datos del formulario
            $('#registroBody tr').each(function() {
                var row = $(this);
                var dia = parseInt(row.find('input[name*="[dia]"]').val());
                var trabajadorId = row.find('select[name*="[trabajador_id]"]').val();
                var op = row.find('select[name*="[op]"]').val();
                var hr = parseFloat(row.find('input[name*="[hr]"]').val()) || 0;
                
                // Validar día
                if (!dia || dia < 1 || dia > 31) {
                    return; // Saltar esta fila
                }
                
                // Construir fecha completa
                var fecha = mesSeleccionado + '-' + String(dia).padStart(2, '0');

                // Solo agregar si tiene todos los datos obligatorios: fecha, op, trabajador y HR > 0
                if (fecha && trabajadorId && op && hr > 0) {
                    var registro = {
                        id: row.find('input[name*="[id]"]').val() || null,
                        fecha: fecha,
                        op: op,
                        descripcion: row.find('textarea[name*="[descripcion]"]').val(),
                        trabajador_id: trabajadorId,
                        maquina: row.find('input[name*="[maquina]"]').val(),
                        hr: hr,
                        hed: parseFloat(row.find('input[name*="[hed]"]').val()) || 0,
                        hen: parseFloat(row.find('input[name*="[hen]"]').val()) || 0,
                        hefd: parseFloat(row.find('input[name*="[hefd]"]').val()) || 0,
                        hefn: parseFloat(row.find('input[name*="[hefn]"]').val()) || 0,
                        permiso: row.find('input[name*="[permiso]"]').val(),
                        comida: row.find('input[name*="[comida]"]').is(':checked') ? 1 : 0,
                        total_horas: row.find('input[name*="[total_horas]"]').val(),
                        observaciones: row.find('textarea[name*="[observaciones]"]').val(),
                        horario: row.find('input[name*="[horario]"]').val()
                    };
                    registros.push(registro);
                }
            });

            if (registros.length === 0) {
                showToast('No hay registros para guardar', 'warning');
                return;
            }

            // Mostrar loading
            var $btn = $(this);
            var originalHtml = $btn.html();
            $btn.prop('disabled', true).html('<i class="ri-loader-4-line spinner-border spinner-border-sm"></i> Guardando...');

            // Enviar datos vía AJAX
            $.ajax({
                url: 'action/registrar-horas-produccion.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ registros: registros }),
                success: function(response) {
                    if (response.success) {
                        showToast(response.message || 'Datos guardados correctamente', 'success');
                        
                        // Actualizar IDs de registros guardados sin recargar la página
                        if (response.registros_guardados) {
                            response.registros_guardados.forEach(function(registro) {
                                // Buscar la fila correspondiente y actualizar su ID
                                $('#registroBody tr').each(function() {
                                    var row = $(this);
                                    var diaInput = row.find('input[name*="[dia]"]');
                                    var dia = parseInt(diaInput.val());
                                    var mesSeleccionado = $('#mesSelector').val();
                                    var fechaConstructa = mesSeleccionado + '-' + String(dia).padStart(2, '0');
                                    var trabajadorId = row.find('select[name*="[trabajador_id]"]').val();
                                    var op = row.find('select[name*="[op]"]').val();
                                    
                                    if (fechaConstructa === registro.fecha && 
                                        trabajadorId == registro.trabajador_id && 
                                        op === registro.op &&
                                        !row.find('input[name*="[id]"]').val()) {
                                        // Esta fila fue recién guardada, asignarle el ID y fecha
                                        var hiddenIdInput = row.find('input[name*="[id]"]');
                                        var hiddenFechaInput = row.find('input[name*="[fecha]"]');
                                        
                                        if (hiddenIdInput.length === 0) {
                                            diaInput.after('<input type="hidden" name="registros[' + row.index() + '][id]" value="' + registro.id + '">');
                                        } else {
                                            hiddenIdInput.val(registro.id);
                                        }
                                        
                                        if (hiddenFechaInput.length === 0) {
                                            diaInput.after('<input type="hidden" name="registros[' + row.index() + '][fecha]" value="' + registro.fecha + '">');
                                        } else {
                                            hiddenFechaInput.val(registro.fecha);
                                        }
                                    }
                                });
                            });
                        }
                        
                        // Restaurar botón
                        $btn.prop('disabled', false).html(originalHtml);
                    } else {
                        showToast(response.message || 'Error al guardar los datos', 'error');
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                },
                error: function(xhr) {
                    showToast('Error de comunicación con el servidor', 'error');
                    $btn.prop('disabled', false).html(originalHtml);
                }
            });
        });

        // Evento para agregar fila automáticamente cuando se ingresa valor en HR
        $(document).on('input', '.hr-input', function() {
            var row = $(this).closest('tr');
            calcularTotal(row);

            // Verificar si debe agregar una nueva fila después de un pequeño delay
            setTimeout(function() {
                checkAndAddRow();
            }, 100);
        });

        // Evento para calcular total al cambiar otras horas
        $(document).on('input', '.hora-input:not(.hr-input)', function() {
            var row = $(this).closest('tr');
            calcularTotal(row);
        });

        // Evento para remover fila
        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
        });

        // Controlador de alerta - Ocultar automáticamente después de 3 segundos
        const alert = document.getElementById('autoCloseAlert');
        if (alert) {
            setTimeout(() => {
                const bootstrapAlert = new bootstrap.Alert(alert);
                bootstrapAlert.close();
            }, 3000);
        }
    });
    </script>
    
    <!-- Typehead Demo js -->
    <script src="assets/js/pages/demo.typehead.js"></script>

</body>
</html>