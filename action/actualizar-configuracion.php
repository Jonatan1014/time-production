<?php
// action/actualizar-configuracion.php
session_start();
require_once '../includes/Class-usuario.php';
require_once '../includes/conn-db.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    header("Location: ../login.php");
    exit;
}

// Verificar que sea administrador
$es_admin = isset($_SESSION['user_rol']) && ($_SESSION['user_rol'] === 'administrador' || $_SESSION['user_rol'] === 'Administrador');

if (!$es_admin) {
    $_SESSION['error'] = "No tiene permisos para realizar esta acción";
    header("Location: ../configuracion.php");
    exit;
}

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'] ?? '';

    try {
        $conn->beginTransaction();

        if ($tipo === 'horarios') {
            // Actualizar horarios laborales
            $horarios = $_POST['horarios'] ?? [];
            
            foreach ($horarios as $id => $datos) {
                $es_laborable = isset($datos['es_laborable']) ? 1 : 0;
                $hora_inicio_manana = $es_laborable ? ($datos['hora_inicio_manana'] ?? '00:00:00') : '00:00:00';
                $hora_fin_manana = $es_laborable ? ($datos['hora_fin_manana'] ?? '00:00:00') : '00:00:00';
                $hora_inicio_tarde = $es_laborable ? ($datos['hora_inicio_tarde'] ?? null) : null;
                $hora_fin_tarde = $es_laborable ? ($datos['hora_fin_tarde'] ?? null) : null;
                $horas_totales = $es_laborable ? ($datos['horas_totales'] ?? 0) : 0;

                $query = "UPDATE horarios_laborales 
                          SET es_laborable = :es_laborable,
                              hora_inicio_manana = :hora_inicio_manana,
                              hora_fin_manana = :hora_fin_manana,
                              hora_inicio_tarde = :hora_inicio_tarde,
                              hora_fin_tarde = :hora_fin_tarde,
                              horas_totales = :horas_totales
                          WHERE id = :id";

                $stmt = $conn->prepare($query);
                $stmt->bindParam(':es_laborable', $es_laborable);
                $stmt->bindParam(':hora_inicio_manana', $hora_inicio_manana);
                $stmt->bindParam(':hora_fin_manana', $hora_fin_manana);
                $stmt->bindParam(':hora_inicio_tarde', $hora_inicio_tarde);
                $stmt->bindParam(':hora_fin_tarde', $hora_fin_tarde);
                $stmt->bindParam(':horas_totales', $horas_totales);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
            }

            $conn->commit();
            $_SESSION['success'] = "Horarios laborales actualizados correctamente";
            
        } elseif ($tipo === 'sistema') {
            // Actualizar configuraciones del sistema
            $configs = $_POST['configs'] ?? [];
            
            foreach ($configs as $id => $datos) {
                $valor = $datos['valor'] ?? '';
                
                // Para booleanos, si no está marcado, el valor es 0
                if (!isset($datos['valor'])) {
                    // Verificar si es un campo booleano
                    $query_check = "SELECT tipo FROM configuracion_sistema WHERE id = :id";
                    $stmt_check = $conn->prepare($query_check);
                    $stmt_check->bindParam(':id', $id);
                    $stmt_check->execute();
                    $config = $stmt_check->fetch(PDO::FETCH_ASSOC);
                    
                    if ($config && $config['tipo'] === 'booleano') {
                        $valor = '0';
                    }
                }

                $query = "UPDATE configuracion_sistema 
                          SET valor = :valor
                          WHERE id = :id";

                $stmt = $conn->prepare($query);
                $stmt->bindParam(':valor', $valor);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
            }

            $conn->commit();
            $_SESSION['success'] = "Configuración del sistema actualizada correctamente";
            
        } elseif ($tipo === 'costos') {
            // Actualizar configuraciones de costos
            $campos_costos = [
                'configs_hora_diurna_inicio' => 'hora_diurna_inicio',
                'configs_hora_diurna_fin' => 'hora_diurna_fin',
                'configs_porcentaje_extra_diurna' => 'porcentaje_extra_diurna',
                'configs_porcentaje_extra_nocturna' => 'porcentaje_extra_nocturna'
            ];
            
            foreach ($campos_costos as $campo_post => $clave_config) {
                if (isset($_POST[$campo_post])) {
                    $valor = $_POST[$campo_post];
                    
                    $query = "UPDATE configuracion_sistema 
                              SET valor = :valor
                              WHERE clave = :clave";
                    
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':valor', $valor);
                    $stmt->bindParam(':clave', $clave_config);
                    $stmt->execute();
                }
            }
            
            $conn->commit();
            $_SESSION['success'] = "Configuración de costos actualizada correctamente";
            
        } elseif ($tipo === 'integraciones') {
            // Actualizar configuraciones de integraciones
            $campos_integraciones = [
                'projectdashboard_habilitado' => isset($_POST['projectdashboard_habilitado']) ? '1' : '0',
                'projectdashboard_url' => $_POST['projectdashboard_url'] ?? '',
                'projectdashboard_webhook_token' => $_POST['projectdashboard_webhook_token'] ?? '',
                'projectdashboard_sincronizacion_automatica' => isset($_POST['projectdashboard_sincronizacion_automatica']) ? '1' : '0'
            ];
            
            foreach ($campos_integraciones as $clave => $valor) {
                // Verificar si la configuración existe, si no, crearla
                $query_check = "SELECT id FROM configuracion_sistema WHERE clave = :clave";
                $stmt_check = $conn->prepare($query_check);
                $stmt_check->bindParam(':clave', $clave);
                $stmt_check->execute();
                
                if ($stmt_check->rowCount() > 0) {
                    // Actualizar
                    $query = "UPDATE configuracion_sistema 
                              SET valor = :valor
                              WHERE clave = :clave";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':valor', $valor);
                    $stmt->bindParam(':clave', $clave);
                    $stmt->execute();
                } else {
                    // Insertar
                    $tipo_campo = ($clave === 'projectdashboard_habilitado' || $clave === 'projectdashboard_sincronizacion_automatica') ? 'booleano' : 'texto';
                    $query = "INSERT INTO configuracion_sistema (clave, valor, tipo, categoria, descripcion) 
                              VALUES (:clave, :valor, :tipo, 'integraciones', :descripcion)";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':clave', $clave);
                    $stmt->bindParam(':valor', $valor);
                    $stmt->bindParam(':tipo', $tipo_campo);
                    
                    $descripciones = [
                        'projectdashboard_habilitado' => 'Habilitar sincronización con ProjectDashboard',
                        'projectdashboard_url' => 'URL webhook del sistema ProjectDashboard',
                        'projectdashboard_webhook_token' => 'Token de autenticación para webhook',
                        'projectdashboard_sincronizacion_automatica' => 'Enviar automáticamente via webhook al aprobar'
                    ];
                    $descripcion = $descripciones[$clave];
                    $stmt->bindParam(':descripcion', $descripcion);
                    $stmt->execute();
                }
            }
            
            $conn->commit();
            $_SESSION['success'] = "Configuración de integraciones actualizada correctamente";
            
        } else {
            throw new Exception("Tipo de configuración no válido");
        }

    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['error'] = "Error al actualizar la configuración: " . $e->getMessage();
    }
}

header("Location: ../configuracion.php");
exit;
?>
