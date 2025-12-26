<?php
// action/obtener-detalles-departamento.php
header('Content-Type: application/json');
session_start();
require_once '../includes/Class-usuario.php';
require_once '../includes/Class-departamentos.php';

$Usuario_class = new Usuario();

// Verificar autenticación y permisos
if (!$Usuario_class->usuarioLogueado()) {
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

if (!$Usuario_class->verificarPermisos('Administrador')) {
    echo json_encode(['success' => false, 'message' => 'Sin permisos']);
    exit;
}

if (empty($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID no especificado']);
    exit;
}

try {
    $Departamento_class = new Departamento();
    $id = intval($_GET['id']);
    
    $departamento = $Departamento_class->obtenerPorId($id);
    $estadisticas = $Departamento_class->obtenerEstadisticas($id);
    
    if ($departamento && $estadisticas) {
        $response = [
            'success' => true,
            'departamento' => $departamento,
            'estadisticas' => $estadisticas
        ];
        
        // Si se solicitan costos y está habilitado
        $incluir_costos = isset($_GET['incluir_costos']) && $_GET['incluir_costos'] == '1';
        
        if ($incluir_costos) {
            require_once '../includes/Class-costos.php';
            require_once '../includes/Class-configuracion.php';
            
            $Costos_class = new Costos();
            $Config_class = new Configuracion();
            
            $mostrar_costos = $Config_class->obtenerValor('mostrar_costos', false);
            
            if ($mostrar_costos) {
                // Obtener fechas del período
                $fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
                $fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-t');
                
                // Obtener usuarios del departamento
                $usuarios_depto = $Usuario_class->obtenerUsuarios(['departamento_id' => $id]);
                
                // Calcular costos
                $total_costo_depto = 0;
                $total_horas_depto = 0;
                $total_horas_normales = 0;
                $total_horas_extras = 0;
                
                foreach ($usuarios_depto as $usuario) {
                    $costos_usuario = $Costos_class->calcularCostosReporte([
                        'usuario_id' => $usuario['id'],
                        'fecha_inicio' => $fecha_inicio,
                        'fecha_fin' => $fecha_fin
                    ]);
                    
                    $total_costo_depto += $costos_usuario['costo_total'];
                    $total_horas_normales += $costos_usuario['horas_normales'];
                    $total_horas_extras += $costos_usuario['horas_extras_diurnas'] + $costos_usuario['horas_extras_nocturnas'];
                }
                
                $total_horas_depto = $total_horas_normales + $total_horas_extras;
                
                $response['costos'] = [
                    'costo_total' => $total_costo_depto,
                    'total_horas' => $total_horas_depto,
                    'horas_normales' => $total_horas_normales,
                    'horas_extras' => $total_horas_extras,
                    'periodo' => [
                        'inicio' => $fecha_inicio,
                        'fin' => $fecha_fin
                    ]
                ];
            }
        }
        
        echo json_encode($response);
    } else {
        echo json_encode(['success' => false, 'message' => 'Departamento no encontrado']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
