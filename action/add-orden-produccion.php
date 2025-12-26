<?php
// action/add-orden-produccion.php - Crear nueva orden de producción
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
    $codigo_op = $_POST['codigo_op'] ?? '';
    $nombre_producto = $_POST['nombre_producto'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $cliente = $_POST['cliente'] ?? '';
    $cantidad_objetivo = $_POST['cantidad_objetivo'] ?? 0;
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';
    $fecha_fin_estimada = $_POST['fecha_fin_estimada'] ?? '';
    $prioridad = $_POST['prioridad'] ?? 'media';

    // Validaciones
    $errores = [];

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

    // Verificar que el código no exista
    if (empty($errores)) {
        $OrdenProduccion_class = new OrdenProduccion();
        
        if ($OrdenProduccion_class->verificarCodigoExistente($codigo_op)) {
            $errores[] = "El código de orden ya existe";
        }
    }

    // Si hay errores, redirigir con mensaje
    if (!empty($errores)) {
        $_SESSION['error'] = implode('<br>', $errores);
        header("Location: ../add-orden-produccion.php");
        exit;
    }

    // Crear la orden
    $datos = [
        'codigo_op' => $codigo_op,
        'nombre_producto' => $nombre_producto,
        'descripcion' => $descripcion,
        'cliente' => $cliente,
        'cantidad_objetivo' => $cantidad_objetivo,
        'fecha_inicio' => $fecha_inicio,
        'fecha_fin_estimada' => $fecha_fin_estimada,
        'estado' => 'activa',
        'prioridad' => $prioridad
    ];

    $resultado = $OrdenProduccion_class->crearOrden($datos);

    if ($resultado) {
        $_SESSION['success'] = "Orden de producción creada correctamente";
        header("Location: ../ordenes-produccion.php");
    } else {
        $_SESSION['error'] = "Error al crear la orden. Intente nuevamente";
        header("Location: ../add-orden-produccion.php");
    }
    exit;
}

header("Location: ../add-orden-produccion.php");
exit;
?>
