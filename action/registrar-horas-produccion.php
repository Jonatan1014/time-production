<?php
// action/registrar-horas-produccion.php - Procesar registro de horas para producción
session_start();
require_once '../includes/Class-usuario.php';
require_once '../includes/Class-horas-produccion.php';
require_once '../includes/conn-db.php';

$database = new Database();
$conn = $database->getConnection();

$Usuario_class = new Usuario();
$HorasProduccion_class = new HorasProduccion($conn);

// Detectar si es petición AJAX
$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Alternativamente, verificar content-type JSON
$is_json = (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false);

if (!$Usuario_class->usuarioLogueado()) {
    if ($is_ajax || $is_json) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'No autenticado']);
        exit;
    }
    header("Location: ../login.php");
    exit;
}

$es_produccion = isset($_SESSION['user_rol']) && ($_SESSION['user_rol'] === 'produccion' || $_SESSION['user_rol'] === 'Produccion' || 
                  $_SESSION['user_rol'] === 'administrador' || $_SESSION['user_rol'] === 'Administrador' || $_SESSION['user_rol'] === 'root' || $_SESSION['user_rol'] === 'Root');

if (!$es_produccion) {
    if ($is_ajax || $is_json) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Sin permisos']);
        exit;
    }
    header("Location: ../index.php");
    exit;
}

// Obtener datos según el tipo de request
if ($is_json) {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    $registros_post = $data['registros'] ?? [];
} else {
    $registros_post = $_POST['registros'] ?? [];
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($registros_post)) {
    if ($is_ajax || $is_json) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Método no permitido o datos faltantes']);
        exit;
    }
    $_SESSION['error'] = 'Método no permitido o datos faltantes.';
    header("Location: ../registrar-horas-produccion.php");
    exit;
}

$registros_procesados = [];

// Obtener IDs de registros existentes antes de procesar
$ids_existentes_antes = [];
$resultado_existentes = $HorasProduccion_class->obtenerRegistros();
if ($resultado_existentes['success']) {
    $ids_existentes_antes = array_column($resultado_existentes['registros'], 'id');
}

// Procesar y validar cada registro
foreach ($registros_post as $registro) {
    // Saltar registros vacíos (sin fecha)
    if (empty($registro['fecha'])) {
        continue;
    }

    // Buscar orden_produccion_id por código OP
    $orden_produccion_id = null;
    if (!empty($registro['op'])) {
        $stmt = $conn->prepare("SELECT id FROM ordenes_produccion WHERE codigo_op = ? AND estado = 'activa' LIMIT 1");
        $stmt->execute([$registro['op']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $orden_produccion_id = $result['id'];
        }
    }

    // Preparar datos para la clase
    $registro_procesado = [
        'usuario_id' => (int)$registro['trabajador_id'],
        'orden_produccion_id' => $orden_produccion_id,
        'fecha' => $registro['fecha'],
        'descripcion' => $registro['descripcion'] ?? '',
        'maquina' => $registro['maquina'] ?? '',
        'hr' => (float)($registro['hr'] ?? 0),
        'hed' => (float)($registro['hed'] ?? 0),
        'hen' => (float)($registro['hen'] ?? 0),
        'hefd' => (float)($registro['hefd'] ?? 0),
        'hefn' => (float)($registro['hefn'] ?? 0),
        'permiso' => $registro['permiso'] ?? '',
        'comida' => isset($registro['comida']) ? 1 : 0,
        'total_horas' => (float)($registro['total_horas'] ?? 0),
        'observaciones' => $registro['observaciones'] ?? '',
        'horario' => $registro['horario'] ?? '7 am - 5 pm'
    ];

    // Agregar ID si existe (para actualizaciones)
    if (!empty($registro['id'])) {
        $registro_procesado['id'] = (int)$registro['id'];
    }

    // Solo agregar si tiene datos obligatorios: trabajador, OP, fecha y HR > 0
    if ($registro_procesado['usuario_id'] > 0 && 
        $registro_procesado['orden_produccion_id'] > 0 && 
        !empty($registro_procesado['fecha']) &&
        $registro_procesado['hr'] > 0) {
        $registros_procesados[] = $registro_procesado;
    }
}

// Determinar registros a eliminar: IDs existentes que no están en los registros enviados
$ids_enviados = array_filter(array_column($registros_procesados, 'id'));
$registros_a_eliminar = array_diff($ids_existentes_antes, $ids_enviados);

if (empty($registros_procesados) && empty($registros_a_eliminar)) {
    if ($is_ajax || $is_json) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'No se encontraron registros válidos para guardar o eliminar']);
        exit;
    }
    $_SESSION['error'] = 'No se encontraron registros válidos para guardar o eliminar.';
    header("Location: ../registrar-horas-produccion.php");
    exit;
}

// Procesar eliminaciones primero
$eliminados = 0;
$errores = [];
foreach ($registros_a_eliminar as $id) {
    $resultado = $HorasProduccion_class->eliminarRegistro($id);
    if ($resultado['success']) {
        $eliminados++;
    } else {
        $errores[] = "Error al eliminar registro ID {$id}: " . $resultado['mensaje'];
    }
}

// Procesar cada registro: actualizar si tiene ID, crear si no
$resultados = [];
$actualizados = 0;
$creados = 0;
$registros_guardados = []; // Para devolver en AJAX

foreach ($registros_procesados as $registro) {
    if (isset($registro['id'])) {
        // Actualizar
        $resultado = $HorasProduccion_class->actualizarRegistro($registro['id'], $registro);
        if ($resultado['success']) {
            $actualizados++;
            $resultados[] = "Registro ID {$registro['id']}: Actualizado";
            $registros_guardados[] = [
                'id' => $registro['id'],
                'fecha' => $registro['fecha'],
                'trabajador_id' => $registro['usuario_id'],
                'op' => $registro['orden_produccion_id']
            ];
        } else {
            $errores[] = "Registro ID {$registro['id']}: " . $resultado['mensaje'];
        }
    } else {
        // Crear
        $resultado = $HorasProduccion_class->crearRegistro($registro);
        if ($resultado['success']) {
            $creados++;
            $resultados[] = "Nuevo registro: Creado (ID: {$resultado['id']})";
            $registros_guardados[] = [
                'id' => $resultado['id'],
                'fecha' => $registro['fecha'],
                'trabajador_id' => $registro['usuario_id'],
                'op' => $registro['orden_produccion_id']
            ];
        } else {
            $errores[] = "Nuevo registro: " . $resultado['mensaje'];
        }
    }
}

// Preparar mensaje de respuesta
$mensaje = '';
if ($creados > 0) $mensaje .= "Creados: {$creados} ";
if ($actualizados > 0) $mensaje .= "Actualizados: {$actualizados} ";
if ($eliminados > 0) $mensaje .= "Eliminados: {$eliminados}";

if ($is_ajax || $is_json) {
    header('Content-Type: application/json');
    if (empty($errores)) {
        echo json_encode([
            'success' => true,
            'message' => 'Operación completada exitosamente. ' . trim($mensaje),
            'creados' => $creados,
            'actualizados' => $actualizados,
            'eliminados' => $eliminados,
            'registros_guardados' => $registros_guardados
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Errores encontrados: ' . implode('; ', $errores),
            'errores' => $errores,
            'creados' => $creados,
            'actualizados' => $actualizados,
            'eliminados' => $eliminados,
            'registros_guardados' => $registros_guardados
        ]);
    }
    exit;
}

// Respuesta tradicional (redirect)
if (empty($errores)) {
    $_SESSION['success'] = 'Operación completada exitosamente. ' . trim($mensaje);
} else {
    $_SESSION['error'] = 'Errores encontrados: ' . implode('; ', $errores);
}

header("Location: ../registrar-horas-produccion.php");
exit;