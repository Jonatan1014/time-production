<?php
session_start();
require_once 'includes/Class-usuario.php';
require_once 'includes/Class-departamentos.php';

// Verificar si el usuario está logueado
$Usuario_class = new Usuario();
if (!$Usuario_class->usuarioLogueado()) {
    header('Location: login.php');
    exit;
}

// Verificar permisos de administrador
if (!$Usuario_class->verificarPermisos('Administrador')) {
    $_SESSION['error'] = 'No tienes permisos para acceder a esta página';
    header('Location: index.php');
    exit;
}

$Departamento_class = new Departamento();
$departamentos = $Departamento_class->obtenerTodos(true); // Incluir inactivos
$usuarios = $Usuario_class->obtenerUsuarios(); // Para el dropdown de responsables

// Instanciar clase de costos
require_once 'includes/Class-costos.php';
require_once 'includes/Class-configuracion.php';
$Costos_class = new Costos();
$Configuracion_class = new Configuracion();
$mostrar_costos = $Configuracion_class->obtenerValor('mostrar_costos', 1);

// Obtener período (último mes)
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-t');

// Calcular costos por departamento si está habilitado
$costos_departamentos = [];
if ($mostrar_costos) {
    foreach ($departamentos as &$depto) {
        // Obtener usuarios del departamento
        $usuarios_depto = $Usuario_class->obtenerUsuarios(['departamento_id' => $depto['id']]);
        
        $total_costo_depto = 0;
        $total_horas_depto = 0;
        $total_horas_normales = 0;
        $total_horas_extras = 0;
        
        foreach ($usuarios_depto as $usuario) {
            $filtros = [
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'usuario_id' => $usuario['id']
            ];
            
            $costos_usuario = $Costos_class->calcularCostosReporte($filtros);
            $total_costo_depto += $costos_usuario['costo_total'];
            $total_horas_normales += $costos_usuario['horas_normales'];
            $total_horas_extras += $costos_usuario['horas_extras_diurnas'] + $costos_usuario['horas_extras_nocturnas'];
        }
        
        $total_horas_depto = $total_horas_normales + $total_horas_extras;
        
        $depto['costo_total'] = $total_costo_depto;
        $depto['total_horas'] = $total_horas_depto;
        $depto['horas_normales'] = $total_horas_normales;
        $depto['horas_extras'] = $total_horas_extras;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Departamentos | Sysmaint</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="shortcut icon" href="assets/images/favicon.ico" />
    <script src="assets/js/hyper-config.js"></script>
    <link href="assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="wrapper">
        <?php include("includes/header.php"); ?>
        <?php include("includes/sidebar.php"); ?>

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">

                    <!-- Mensajes -->
                    <?php if (isset($_SESSION['exito'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['exito']; unset($_SESSION['exito']); ?>
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
                                <div class="page-title-right">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalDepartamento" onclick="limpiarFormulario()">
                                        <i class="mdi mdi-plus-circle me-1"></i> Nuevo Departamento
                                    </button>
                                </div>
                                <h4 class="page-title">Gestión de Departamentos</h4>
                            </div>
                        </div>
                    </div>

                    <?php if ($mostrar_costos): ?>
                    <!-- Filtros de Período para Costos -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body py-2">
                                    <form method="GET" action="departamentos.php" class="row g-2 align-items-end">
                                        <div class="col-auto">
                                            <label class="form-label mb-1 small"><strong>Período para Costos:</strong></label>
                                        </div>
                                        <div class="col-auto">
                                            <input type="date" class="form-control form-control-sm" name="fecha_inicio" 
                                                   value="<?php echo $fecha_inicio; ?>" required>
                                        </div>
                                        <div class="col-auto">
                                            <span class="mx-1">a</span>
                                        </div>
                                        <div class="col-auto">
                                            <input type="date" class="form-control form-control-sm" name="fecha_fin" 
                                                   value="<?php echo $fecha_fin; ?>" required>
                                        </div>
                                        <div class="col-auto">
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                <i class="mdi mdi-refresh me-1"></i> Actualizar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Resumen de Costos -->
                    <div class="row">
                        <?php 
                        $total_general = 0;
                        $total_horas_general = 0;
                        foreach ($departamentos as $d) {
                            $total_general += $d['costo_total'] ?? 0;
                            $total_horas_general += $d['total_horas'] ?? 0;
                        }
                        ?>
                        <div class="col-md-4">
                            <div class="card widget-flat border-primary">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="mdi mdi-currency-usd widget-icon bg-primary-lighten text-primary"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0">Costo Total</h5>
                                    <h3 class="mt-3 mb-3 text-primary">$<?php echo number_format($total_general, 0, ',', '.'); ?></h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-nowrap">Todos los departamentos</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="mdi mdi-clock-outline widget-icon bg-success-lighten text-success"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0">Total Horas</h5>
                                    <h3 class="mt-3 mb-3"><?php echo number_format($total_horas_general, 1); ?> hrs</h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-nowrap"><?php echo date('d/m/Y', strtotime($fecha_inicio)) . ' - ' . date('d/m/Y', strtotime($fecha_fin)); ?></span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="mdi mdi-office-building widget-icon bg-info-lighten text-info"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0">Departamentos</h5>
                                    <h3 class="mt-3 mb-3"><?php echo count(array_filter($departamentos, function($d) { return $d['is_active']; })); ?></h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-nowrap">Activos</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Tabla de Departamentos -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">
                                        <i class="mdi mdi-office-building-outline me-2"></i>
                                        Listado de Departamentos
                                    </h4>
                                    
                                    <div class="table-responsive">
                                        <table id="tabla-departamentos" class="table table-striped table-hover dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th>Código</th>
                                                    <th>Nombre</th>
                                                    <th>Descripción</th>
                                                    <th>Responsable</th>
                                                    <th>Usuarios</th>
                                                    <?php if ($mostrar_costos): ?>
                                                    <th>Total Horas</th>
                                                    <th>Costo Total</th>
                                                    <?php endif; ?>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($departamentos as $depto): ?>
                                                <tr>
                                                    <td><strong><?php echo htmlspecialchars($depto['codigo'] ?? 'N/A'); ?></strong></td>
                                                    <td><?php echo htmlspecialchars($depto['nombre']); ?></td>
                                                    <td>
                                                        <small><?php echo htmlspecialchars(substr($depto['descripcion'] ?? '', 0, 50)); ?><?php echo strlen($depto['descripcion'] ?? '') > 50 ? '...' : ''; ?></small>
                                                    </td>
                                                    <td>
                                                        <?php if ($depto['responsable_nombre']): ?>
                                                            <i class="mdi mdi-account-circle text-primary me-1"></i>
                                                            <?php echo htmlspecialchars($depto['responsable_nombre']); ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">Sin asignar</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info fs-6">
                                                            <?php echo $depto['total_usuarios']; ?> usuario(s)
                                                        </span>
                                                    </td>
                                                    <?php if ($mostrar_costos): ?>
                                                    <td>
                                                        <strong><?php echo number_format($depto['total_horas'] ?? 0, 1); ?></strong> hrs
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="mdi mdi-clock-outline"></i> <?php echo number_format($depto['horas_normales'] ?? 0, 1); ?> 
                                                            <i class="mdi mdi-clock-plus-outline ms-1"></i> <?php echo number_format($depto['horas_extras'] ?? 0, 1); ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <strong class="text-primary fs-5">$<?php echo number_format($depto['costo_total'] ?? 0, 0, ',', '.'); ?></strong>
                                                        <?php if (($depto['total_horas'] ?? 0) > 0): ?>
                                                        <br>
                                                        <small class="text-muted">
                                                            $<?php echo number_format(($depto['costo_total'] ?? 0) / ($depto['total_horas'] ?? 1), 0, ',', '.'); ?>/hr
                                                        </small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <?php endif; ?>
                                                    <td>
                                                        <?php if ($depto['is_active']): ?>
                                                            <span class="badge badge-success-lighten">Activo</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-danger-lighten">Inactivo</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-info" onclick="verDetalles(<?php echo $depto['id']; ?>)" title="Ver detalles">
                                                            <i class="mdi mdi-eye"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-warning" onclick="editarDepartamento(<?php echo htmlspecialchars(json_encode($depto)); ?>)" title="Editar">
                                                            <i class="mdi mdi-pencil"></i>
                                                        </button>
                                                        <?php if ($depto['is_active']): ?>
                                                            <button class="btn btn-sm btn-danger" onclick="confirmarEliminar(<?php echo $depto['id']; ?>, '<?php echo htmlspecialchars($depto['nombre']); ?>', <?php echo $depto['total_usuarios']; ?>)" title="Desactivar">
                                                                <i class="mdi mdi-delete"></i>
                                                            </button>
                                                        <?php else: ?>
                                                            <button class="btn btn-sm btn-success" onclick="activarDepartamento(<?php echo $depto['id']; ?>, '<?php echo htmlspecialchars($depto['nombre']); ?>')" title="Activar">
                                                                <i class="mdi mdi-check-circle"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </td>
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
            </div>

            <?php include("includes/footer.php"); ?>
        </div>
    </div>

    <!-- Modal Crear/Editar Departamento -->
    <div class="modal fade" id="modalDepartamento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitulo">
                        <i class="mdi mdi-office-building-outline me-2"></i>
                        <span id="textoTitulo">Nuevo Departamento</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formDepartamento" method="POST" action="action/gestionar-departamento.php">
                    <div class="modal-body">
                        <input type="hidden" id="departamento_id" name="departamento_id">
                        <input type="hidden" id="accion" name="accion" value="crear">

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="nombre" class="form-label">Nombre del Departamento <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required 
                                       placeholder="Ej: Soldadura y Ensamble">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="codigo" class="form-label">Código</label>
                                <input type="text" class="form-control" id="codigo" name="codigo" 
                                       placeholder="Ej: SOLD-ENS" maxlength="20">
                                <small class="text-muted">Opcional, único</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" 
                                      placeholder="Describe las funciones y responsabilidades del departamento"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="responsable_id" class="form-label">Responsable del Departamento</label>
                            <select class="form-select" id="responsable_id" name="responsable_id">
                                <option value="">Sin asignar</option>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <option value="<?php echo $usuario['id']; ?>">
                                        <?php echo htmlspecialchars($usuario['nombre_completo']); ?> 
                                        (<?php echo htmlspecialchars($usuario['username']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Usuario encargado del departamento</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">
                                    <strong>Departamento Activo</strong>
                                    <small class="text-muted d-block">Los usuarios podrán ser asignados a este departamento</small>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save me-1"></i>
                            <span id="textoBoton">Crear Departamento</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Detalles -->
    <div class="modal fade" id="modalDetalles" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="mdi mdi-information-outline me-2"></i>
                        Detalles del Departamento
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="contenidoDetalles">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include("includes/js.php"); ?>
    <script src="assets/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="assets/vendor/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/vendor/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#tabla-departamentos').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                order: [[1, 'asc']],
                responsive: true
            });
        });

        function limpiarFormulario() {
            document.getElementById('formDepartamento').reset();
            document.getElementById('departamento_id').value = '';
            document.getElementById('accion').value = 'crear';
            document.getElementById('textoTitulo').textContent = 'Nuevo Departamento';
            document.getElementById('textoBoton').textContent = 'Crear Departamento';
            document.getElementById('is_active').checked = true;
        }

        function editarDepartamento(depto) {
            document.getElementById('departamento_id').value = depto.id;
            document.getElementById('accion').value = 'actualizar';
            document.getElementById('nombre').value = depto.nombre;
            document.getElementById('codigo').value = depto.codigo || '';
            document.getElementById('descripcion').value = depto.descripcion || '';
            document.getElementById('responsable_id').value = depto.responsable_id || '';
            document.getElementById('is_active').checked = depto.is_active == 1;
            document.getElementById('textoTitulo').textContent = 'Editar Departamento';
            document.getElementById('textoBoton').textContent = 'Actualizar Departamento';
            
            const modal = new bootstrap.Modal(document.getElementById('modalDepartamento'));
            modal.show();
        }

        function confirmarEliminar(id, nombre, totalUsuarios) {
            if (totalUsuarios > 0) {
                alert('No se puede desactivar el departamento "' + nombre + '" porque tiene ' + totalUsuarios + ' usuario(s) asignado(s).\n\nPrimero reasigna los usuarios a otro departamento.');
                return;
            }
            
            if (confirm('¿Estás seguro de que deseas desactivar el departamento "' + nombre + '"?\n\nLos usuarios no podrán ser asignados a este departamento.')) {
                window.location.href = 'action/gestionar-departamento.php?accion=eliminar&id=' + id;
            }
        }

        function activarDepartamento(id, nombre) {
            if (confirm('¿Deseas activar el departamento "' + nombre + '"?')) {
                window.location.href = 'action/gestionar-departamento.php?accion=activar&id=' + id;
            }
        }

        function verDetalles(id) {
            const modal = new bootstrap.Modal(document.getElementById('modalDetalles'));
            modal.show();
            
            const urlParams = new URLSearchParams(window.location.search);
            const fechaInicio = urlParams.get('fecha_inicio') || '<?php echo date('Y-m-01'); ?>';
            const fechaFin = urlParams.get('fecha_fin') || '<?php echo date('Y-m-t'); ?>';
            
            fetch(`action/obtener-detalles-departamento.php?id=${id}&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}&incluir_costos=<?php echo $mostrar_costos ? '1' : '0'; ?>`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const stats = data.estadisticas;
                        let html = `
                            <div class="mb-3">
                                <h5 class="text-primary">${data.departamento.nombre}</h5>
                                ${data.departamento.codigo ? `<p class="text-muted mb-2"><strong>Código:</strong> ${data.departamento.codigo}</p>` : ''}
                                ${data.departamento.descripcion ? `<p class="mb-2">${data.departamento.descripcion}</p>` : ''}
                            </div>
                            <hr>
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <div class="card border">
                                        <div class="card-body p-3">
                                            <i class="mdi mdi-account-multiple text-primary font-24"></i>
                                            <h3 class="mt-2 mb-1">${stats.total_usuarios}</h3>
                                            <p class="mb-0 text-muted">Total Usuarios</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="card border">
                                        <div class="card-body p-3">
                                            <i class="mdi mdi-account-check text-success font-24"></i>
                                            <h3 class="mt-2 mb-1">${stats.usuarios_activos}</h3>
                                            <p class="mb-0 text-muted">Activos</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card border">
                                        <div class="card-body p-3">
                                            <i class="mdi mdi-account-hard-hat text-info font-24"></i>
                                            <h3 class="mt-2 mb-1">${stats.trabajadores}</h3>
                                            <p class="mb-0 text-muted">Trabajadores</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card border">
                                        <div class="card-body p-3">
                                            <i class="mdi mdi-shield-account text-warning font-24"></i>
                                            <h3 class="mt-2 mb-1">${stats.administradores}</h3>
                                            <p class="mb-0 text-muted">Administradores</p>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                        
                        <?php if ($mostrar_costos): ?>
                        if (data.costos) {
                            html += `
                            <hr>
                            <div class="alert alert-light border">
                                <h6 class="text-muted mb-3">
                                    <i class="mdi mdi-currency-usd text-primary me-1"></i>
                                    Costos del Período
                                </h6>
                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <strong>Total Horas:</strong> ${data.costos.total_horas.toFixed(1)} hrs
                                    </div>
                                    <div class="col-6 mb-2">
                                        <strong>Hrs Normales:</strong> ${data.costos.horas_normales.toFixed(1)} hrs
                                    </div>
                                    <div class="col-6 mb-2">
                                        <strong>Hrs Extras:</strong> ${data.costos.horas_extras.toFixed(1)} hrs
                                    </div>
                                    <div class="col-6 mb-2">
                                        <strong class="text-primary">Costo Total:</strong> 
                                        <span class="text-primary">$${data.costos.costo_total.toLocaleString('es-CL')}</span>
                                    </div>
                                </div>
                            </div>`;
                        }
                        <?php endif; ?>
                        
                        html += `
                            ${data.departamento.responsable_nombre ? `
                            <hr>
                            <p class="mb-0">
                                <i class="mdi mdi-account-circle text-primary me-1"></i>
                                <strong>Responsable:</strong> ${data.departamento.responsable_nombre}
                            </p>
                            ` : ''}
                        `;
                        
                        document.getElementById('contenidoDetalles').innerHTML = html;
                    } else {
                        document.getElementById('contenidoDetalles').innerHTML = `
                            <div class="alert alert-danger">
                                <i class="mdi mdi-alert me-2"></i>
                                ${data.message || 'Error al cargar los detalles'}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    document.getElementById('contenidoDetalles').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="mdi mdi-alert me-2"></i>
                            Error al cargar los detalles del departamento
                        </div>
                    `;
                });
        }
    </script>

</body>
</html>
