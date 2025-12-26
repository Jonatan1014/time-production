<?php
// action/update-orden-produccion.php
session_start();
require_once '../includes/Class-usuario.php';
require_once '../includes/Class-ordenes-produccion.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    header("Location: ../login.php");
    exit;
}

// Verificar que sea administrador
$es_admin = isset($_SESSION['user_rol']) && ($_SESSION['user_rol'] === 'administrador' || $_SESSION['user_rol'] === 'Administrador');

if (!$es_admin) {
    $_SESSION['error'] = "No tiene permisos para realizar esta acción";
    header("Location: ../ordenes-produccion.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $codigo_op = $_POST['codigo_op'] ?? '';
    $nombre_producto = $_POST['nombre_producto'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $cliente = $_POST['cliente'] ?? '';
    $cantidad_objetivo = $_POST['cantidad_objetivo'] ?? 0;
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';
    $fecha_fin_estimada = $_POST['fecha_fin_estimada'] ?? '';
    $estado = $_POST['estado'] ?? 'activa';
    $prioridad = $_POST['prioridad'] ?? 'media';
    $fecha_fin_real = $_POST['fecha_fin_real'] ?? null;

    // Validaciones
    $errores = [];

    if (!$id) {
        $errores[] = "ID de orden no especificado";
    }

    if (empty($codigo_op)) {
        $errores[] = "El código de orden es requerido";
    }

    if (empty($nombre_producto)) {
        $errores[] = "El nombre del producto es requerido";
    }

    if (empty($cliente)) {
        $errores[] = "El cliente es requerido";
    }

    if (empty($cantidad_objetivo) || $cantidad_objetivo <= 0) {
        $errores[] = "La cantidad objetivo debe ser mayor a 0";
    }

    if (empty($fecha_inicio) || empty($fecha_fin_estimada)) {
        $errores[] = "Las fechas de inicio y fin son requeridas";
    }

    if (strtotime($fecha_fin_estimada) < strtotime($fecha_inicio)) {
        $errores[] = "La fecha de fin debe ser posterior a la fecha de inicio";
    }

    // Verificar que el código no exista (excepto la orden actual)
    if (empty($errores)) {
        $OrdenProduccion_class = new OrdenProduccion();
        
        if ($OrdenProduccion_class->verificarCodigoExistente($codigo_op, $id)) {
            $errores[] = "El código de orden ya existe";
        }
    }

    // Si hay errores, redirigir con mensaje
    if (!empty($errores)) {
        $_SESSION['error'] = implode('<br>', $errores);
        header("Location: ../update-orden-produccion.php?id=" . $id);
        exit;
    }

    // Actualizar la orden
    $datos = [
        'codigo_op' => $codigo_op,
        'nombre_producto' => $nombre_producto,
        'descripcion' => $descripcion,
        'cliente' => $cliente,
        'cantidad_objetivo' => $cantidad_objetivo,
        'fecha_inicio' => $fecha_inicio,
        'fecha_fin_estimada' => $fecha_fin_estimada,
        'estado' => $estado,
        'prioridad' => $prioridad
    ];

    // Agregar fecha fin real solo si está completada
    if ($estado === 'completada' && !empty($fecha_fin_real)) {
        $datos['fecha_fin_real'] = $fecha_fin_real;
    }

    $resultado = $OrdenProduccion_class->actualizarOrden($id, $datos);

    if ($resultado) {
        $_SESSION['success'] = "Orden de producción actualizada correctamente";
        header("Location: ../ordenes-produccion.php");
    } else {
        $_SESSION['error'] = "Error al actualizar la orden. Intente nuevamente";
        header("Location: ../update-orden-produccion.php?id=" . $id);
    }
    exit;
}

header("Location: ../ordenes-produccion.php");
exit;
?>
