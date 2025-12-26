<?php
// action/update-cargos.php
require_once '../includes/Class-cargos.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['id']) && !empty($_POST['nombre'])) {

        $id = intval($_POST['id']);
        $nombre = trim($_POST['nombre']);
        $descripcion = !empty($_POST['descripcion']) ? trim($_POST['descripcion']) : null;
        $is_active = isset($_POST['is_active']) && $_POST['is_active'] == '1' ? 1 : 0;

        $Cargos_class = new Cargos();

        // Verificar si cambió el nombre y si ya existe otro cargo con ese nombre
        $cargo_actual = $Cargos_class->obtenerCargoPorId($id);
        if (!$cargo_actual) {
            $_SESSION['error'] = 'Cargo no encontrado.';
            header("Location: ../cargos.php");
            exit;
        }

        // Verificar si el nombre cambió y ya existe
        if ($nombre !== $cargo_actual['nombre']) {
            if ($Cargos_class->cargoExiste($nombre, $id)) {
                $_SESSION['error'] = 'Ya existe otro cargo con ese nombre.';
                header("Location: ../update-cargos.php?id=" . $id);
                exit;
            }
        }

        // Actualizar el cargo
        $resultado = $Cargos_class->actualizarCargo($id, [
            'nombre' => $nombre,
            'descripcion' => $descripcion
        ]);

        if ($resultado['success']) {
            // Actualizar estado si cambió
            if ($is_active != $cargo_actual['is_active']) {
                $Cargos_class->cambiarEstadoCargo($id, $is_active);
            }

            $_SESSION['exito'] = 'Cargo actualizado exitosamente.';
            header("Location: ../cargos.php");
            exit;
        } else {
            $_SESSION['error'] = $resultado['message'];
            header("Location: ../update-cargos.php?id=" . $id);
            exit;
        }

    } else {
        $_SESSION['error'] = 'Por favor, ingresa el nombre del cargo.';
        header("Location: ../update-cargos.php?id=" . ($_POST['id'] ?? ''));
        exit;
    }
} else {
    $_SESSION['error'] = 'Método no permitido';
    header("Location: ../cargos.php");
    exit;
}
?>