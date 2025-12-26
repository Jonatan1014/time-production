<?php
// action/gestionar-departamento.php
session_start();
require_once '../includes/Class-usuario.php';
require_once '../includes/Class-departamentos.php';

$Usuario_class = new Usuario();

// Verificar autenticación y permisos
if (!$Usuario_class->usuarioLogueado()) {
    $_SESSION['error'] = 'Debes iniciar sesión';
    header('Location: ../login.php');
    exit;
}

if (!$Usuario_class->verificarPermisos('Administrador')) {
    $_SESSION['error'] = 'No tienes permisos para realizar esta acción';
    header('Location: ../index.php');
    exit;
}

$Departamento_class = new Departamento();

// Determinar la acción
$accion = $_POST['accion'] ?? $_GET['accion'] ?? null;

if (!$accion) {
    $_SESSION['error'] = 'Acción no especificada';
    header('Location: ../departamentos.php');
    exit;
}

try {
    switch ($accion) {
        case 'crear':
            // Validar campos requeridos
            if (empty($_POST['nombre'])) {
                $_SESSION['error'] = 'El nombre del departamento es obligatorio';
                header('Location: ../departamentos.php');
                exit;
            }

            // Verificar si el nombre ya existe
            if ($Departamento_class->verificarNombreExistente($_POST['nombre'])) {
                $_SESSION['error'] = 'Ya existe un departamento con ese nombre';
                header('Location: ../departamentos.php');
                exit;
            }

            // Verificar si el código ya existe (si se proporcionó)
            if (!empty($_POST['codigo']) && $Departamento_class->verificarCodigoExistente($_POST['codigo'])) {
                $_SESSION['error'] = 'Ya existe un departamento con ese código';
                header('Location: ../departamentos.php');
                exit;
            }

            $datos = [
                'nombre' => trim($_POST['nombre']),
                'descripcion' => trim($_POST['descripcion']) ?? null,
                'codigo' => !empty(trim($_POST['codigo'])) ? strtoupper(trim($_POST['codigo'])) : null,
                'responsable_id' => !empty($_POST['responsable_id']) ? intval($_POST['responsable_id']) : null,
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            $resultado = $Departamento_class->crear($datos);

            if ($resultado) {
                $_SESSION['exito'] = 'Departamento creado correctamente';
            } else {
                $_SESSION['error'] = 'Error al crear el departamento';
            }
            break;

        case 'actualizar':
            // Validar campos requeridos
            if (empty($_POST['departamento_id']) || empty($_POST['nombre'])) {
                $_SESSION['error'] = 'Datos incompletos';
                header('Location: ../departamentos.php');
                exit;
            }

            $id = intval($_POST['departamento_id']);

            // Verificar si el nombre ya existe (excluyendo el actual)
            if ($Departamento_class->verificarNombreExistente($_POST['nombre'], $id)) {
                $_SESSION['error'] = 'Ya existe otro departamento con ese nombre';
                header('Location: ../departamentos.php');
                exit;
            }

            // Verificar si el código ya existe (si se proporcionó)
            if (!empty($_POST['codigo']) && $Departamento_class->verificarCodigoExistente($_POST['codigo'], $id)) {
                $_SESSION['error'] = 'Ya existe otro departamento con ese código';
                header('Location: ../departamentos.php');
                exit;
            }

            $datos = [
                'nombre' => trim($_POST['nombre']),
                'descripcion' => trim($_POST['descripcion']) ?? null,
                'codigo' => !empty(trim($_POST['codigo'])) ? strtoupper(trim($_POST['codigo'])) : null,
                'responsable_id' => !empty($_POST['responsable_id']) ? intval($_POST['responsable_id']) : null,
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            $resultado = $Departamento_class->actualizar($id, $datos);

            if ($resultado) {
                $_SESSION['exito'] = 'Departamento actualizado correctamente';
            } else {
                $_SESSION['error'] = 'Error al actualizar el departamento';
            }
            break;

        case 'eliminar':
            if (empty($_GET['id'])) {
                $_SESSION['error'] = 'ID no especificado';
                header('Location: ../departamentos.php');
                exit;
            }

            $id = intval($_GET['id']);
            $resultado = $Departamento_class->eliminar($id);

            if ($resultado['success']) {
                $_SESSION['exito'] = $resultado['message'];
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
            break;

        case 'activar':
            if (empty($_GET['id'])) {
                $_SESSION['error'] = 'ID no especificado';
                header('Location: ../departamentos.php');
                exit;
            }

            $id = intval($_GET['id']);
            $resultado = $Departamento_class->activar($id);

            if ($resultado) {
                $_SESSION['exito'] = 'Departamento activado correctamente';
            } else {
                $_SESSION['error'] = 'Error al activar el departamento';
            }
            break;

        default:
            $_SESSION['error'] = 'Acción no válida';
    }

} catch (Exception $e) {
    $_SESSION['error'] = 'Error del sistema: ' . $e->getMessage();
}

header('Location: ../departamentos.php');
exit;
?>
