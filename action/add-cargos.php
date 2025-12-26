<?php
// action/add-cargos.php
require_once '../includes/Class-cargos.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['nombre'])) {

        $nombre = trim($_POST['nombre']);
        $descripcion = !empty($_POST['descripcion']) ? trim($_POST['descripcion']) : null;

        $Cargos_class = new Cargos();

        // Verificar si el cargo ya existe
        if ($Cargos_class->cargoExiste($nombre)) {
            $_SESSION['error'] = 'Ya existe un cargo con ese nombre.';
            header("Location: ../add-cargos.php");
            exit;
        }

        // Crear el cargo
        $resultado = $Cargos_class->crearCargo([
            'nombre' => $nombre,
            'descripcion' => $descripcion
        ]);

        if ($resultado['success']) {
            $_SESSION['exito'] = 'Cargo creado exitosamente.';
            header("Location: ../cargos.php");
            exit;
        } else {
            $_SESSION['error'] = $resultado['message'];
            header("Location: ../add-cargos.php");
            exit;
        }

    } else {
        $_SESSION['error'] = 'Por favor, ingresa el nombre del cargo.';
        header("Location: ../add-cargos.php");
        exit;
    }
} else {
    $_SESSION['error'] = 'Método no permitido';
    header("Location: ../cargos.php");
    exit;
}
?>