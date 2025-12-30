<?php
// registrar-horas-produccion.php - Vista para rol de producción: registro de horas como hoja de Excel
session_start();
require_once 'includes/Class-usuario.php';
require_once 'includes/Class-ordenes-produccion.php';
require_once 'includes/Class-registro-horas.php';
require_once 'includes/Class-horas-produccion.php';
require_once 'includes/conn-db.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['user_id'];
$es_produccion = isset($_SESSION['user_rol']) && ($_SESSION['user_rol'] === 'produccion' || $_SESSION['user_rol'] === 'Produccion');

// Solo permitir acceso a rol producción
if (!$es_produccion) {
    header("Location: index.php");
    exit;
}

// Instanciar clases
$database = new Database();
$conn = $database->getConnection();
$Usuario_class = new Usuario();
$OrdenProduccion_class = new OrdenProduccion();
$RegistroHoras_class = new RegistroHoras();
$HorasProduccion_class = new HorasProduccion($conn);

// Obtener listas para selects
$trabajadores = $Usuario_class->obtenerUsuarios(['rol' => 'trabajador']);
$ordenes_activas = $OrdenProduccion_class->obtenerOrdenes(['estado' => 'activa']);

// Obtener registros existentes
$resultado_registros = $HorasProduccion_class->obtenerRegistros();
$registros_existentes = $resultado_registros['success'] ? $resultado_registros['registros'] : [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Registro de Horas - Producción | Time Production</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta content="Sistema de Control de Horas de Producción" name="description" />
    <meta content="Time Production" name="author" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico" />

    <!-- Daterangepicker css -->
    <link rel="stylesheet" href="assets/vendor/daterangepicker/daterangepicker.css" />

    <!-- Vector Map css -->
    <link rel="stylesheet" href="assets/vendor/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css" />

    <!-- Theme Config Js -->
    <script src="assets/js/hyper-config.js"></script>

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
    .total-horas {
        background-color: #e9ecef;
        font-weight: bold;
    }
    .horario-fijo {
        background-color: #f8f9fa;
        font-style: italic;
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
        <?php include("includes/header.php"); ?>
        <!-- ========== Topbar End ========== -->

        <?php include("includes/sidebar.php"); ?>

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

                    <!-- Formulario de Registro -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="header-title">Horas de Produccion</h4>
                                    <p class="card-subtitle">Complete la información de horas trabajadas por cada trabajador.</p>
                                </div>
                                <div class="card-body">
                                    <form id="registroHorasForm" action="action/registrar-horas-produccion.php" method="post">
                                        <div class="scroll-container">
                                            <table class="excel-table" id="registroTable">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 100px;">Fecha</th>
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
                                                        <th style="width: 50px;">Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="registroBody">
                                                    <!-- Registros existentes -->
                                                    <?php foreach ($registros_existentes as $index => $registro): ?>
                                                    <tr>
                                                        <td><input type="date" name="registros[<?php echo $index; ?>][fecha]" class="form-control" value="<?php echo htmlspecialchars($registro['fecha']); ?>" required>
                                                            <input type="hidden" name="registros[<?php echo $index; ?>][id]" value="<?php echo $registro['id']; ?>">
                                                        </td>
                                                        <td>
                                                            <select name="registros[<?php echo $index; ?>][op]" class="form-control select2-op" required>
                                                                <option value="">Seleccionar OP</option>
                                                                <?php foreach ($ordenes_activas as $orden): ?>
                                                                <option value="<?php echo $orden['codigo_op']; ?>" <?php echo ($orden['codigo_op'] == $registro['codigo_op']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($orden['codigo_op'] . ' - ' . $orden['nombre_producto']); ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </td>
                                                        <td><textarea name="registros[<?php echo $index; ?>][descripcion]" class="form-control" rows="1" placeholder="Descripción de actividades"><?php echo htmlspecialchars($registro['descripcion'] ?? ''); ?></textarea></td>
                                                        <td>
                                                            <select name="registros[<?php echo $index; ?>][trabajador_id]" class="form-control select2-trabajador" required>
                                                                <option value="">Seleccionar</option>
                                                                <?php foreach ($trabajadores as $trabajador): ?>
                                                                <option value="<?php echo $trabajador['id']; ?>" <?php echo ($trabajador['id'] == $registro['usuario_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($trabajador['nombre_completo']); ?></option>
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
                                                        <td><input type="checkbox" name="registros[<?php echo $index; ?>][comida]" value="1" <?php echo ($registro['comida'] == 1) ? 'checked' : ''; ?>></td>
                                                        <td><input type="text" name="registros[<?php echo $index; ?>][total_horas]" class="form-control total-horas" value="<?php echo $registro['total_horas']; ?>" readonly></td>
                                                        <td><textarea name="registros[<?php echo $index; ?>][observaciones]" class="form-control" rows="1" placeholder="Observaciones"><?php echo htmlspecialchars($registro['observaciones']); ?></textarea></td>
                                                        <td><input type="text" name="registros[<?php echo $index; ?>][horario]" class="form-control horario-fijo" value="<?php echo htmlspecialchars($registro['horario']); ?>" readonly></td>
                                                        <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="ri-delete-bin-line"></i></button></td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                    <!-- Filas dinámicas se agregarán aquí -->
                                                </tbody>
                                            </table>
                                        </div>
                                        <button type="submit" class="btn btn-success">
                                            <i class="ri-save-line"></i> Actualizar Información
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- container -->
            </div>
            <!-- content -->

            <!-- Footer Start -->
            <?php include("includes/footer.php"); ?>
            <!-- end Footer -->
        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->
    </div>
    <!-- END wrapper -->

    <!-- Vendor js -->
    <?php include("includes/js.php"); ?>

    <!-- Select2 js -->
    <script src="assets/vendor/select2/js/select2.min.js"></script>

    <script>
    $(document).ready(function() {
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
                    <td><input type="date" name="registros[${rowCount}][fecha]" class="form-control" required></td>
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
                    <td><input type="text" name="registros[${rowCount}][horario]" class="form-control horario-fijo" value="7 am - 5 pm" readonly></td>
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

        // Agregar fila inicial
        addRow();

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

        // Mostrar mensajes de sesión si existen
        <?php if (isset($_SESSION['success'])): ?>
        toastr.success('<?php echo $_SESSION['success']; unset($_SESSION['success']); ?>');
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
        toastr.error('<?php echo $_SESSION['error']; unset($_SESSION['error']); ?>');
        <?php endif; ?>
    });
    </script>

</body>
</html>