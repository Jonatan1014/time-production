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

if (!$Usuario_class->usuarioLogueado()) {
    header("Location: ../login.php");
    exit;
}

$es_produccion = isset($_SESSION['user_rol']) && ($_SESSION['user_rol'] === 'produccion' || $_SESSION['user_rol'] === 'Produccion');

if (!$es_produccion) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['registros'])) {
    $_SESSION['error'] = 'Método no permitido o datos faltantes.';
    header("Location: ../registrar-horas-produccion.php");
    exit;
}

$registros_post = $_POST['registros'];
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

    // Solo agregar si tiene datos mínimos
    if ($registro_procesado['usuario_id'] > 0 && $registro_procesado['orden_produccion_id'] > 0) {
        $registros_procesados[] = $registro_procesado;
    }
}

// Determinar registros a eliminar: IDs existentes que no están en los registros enviados
$ids_enviados = array_filter(array_column($registros_procesados, 'id'));
$registros_a_eliminar = array_diff($ids_existentes_antes, $ids_enviados);

if (empty($registros_procesados) && empty($registros_a_eliminar)) {
    $_SESSION['error'] = 'No se encontraron registros válidos para guardar o eliminar.';
    header("Location: ../registrar-horas-produccion.php");
    exit;
}

// Procesar eliminaciones primero
$eliminados = 0;
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
$errores = [];
$actualizados = 0;
$creados = 0;

foreach ($registros_procesados as $registro) {
    if (isset($registro['id'])) {
        // Actualizar
        $resultado = $HorasProduccion_class->actualizarRegistro($registro['id'], $registro);
        if ($resultado['success']) {
            $actualizados++;
            $resultados[] = "Registro ID {$registro['id']}: Actualizado";
        } else {
            $errores[] = "Registro ID {$registro['id']}: " . $resultado['mensaje'];
        }
    } else {
        // Crear
        $resultado = $HorasProduccion_class->crearRegistro($registro);
        if ($resultado['success']) {
            $creados++;
            $resultados[] = "Nuevo registro: Creado (ID: {$resultado['id']})";
        } else {
            $errores[] = "Nuevo registro: " . $resultado['mensaje'];
        }
    }
}

if (empty($errores)) {
    $mensaje = '';
    if ($creados > 0) $mensaje .= "Creados: {$creados} ";
    if ($actualizados > 0) $mensaje .= "Actualizados: {$actualizados} ";
    if ($eliminados > 0) $mensaje .= "Eliminados: {$eliminados}";
    $_SESSION['success'] = 'Operación completada exitosamente. ' . trim($mensaje);
} else {
    $_SESSION['error'] = 'Errores encontrados: ' . implode('; ', $errores);
}

header("Location: ../registrar-horas-produccion.php");
exit;