<?php
/**
 * Script para identificar y limpiar registros duplicados de horas extras
 * 
 * Este script encuentra registros en 'registros_horas' que fueron creados
 * cuando se aprobaron horas extras y que ya existen en 'solicitudes_horas_extras'
 * 
 * IMPORTANTE: Ejecutar este script UNA SOLA VEZ para limpiar datos históricos
 */

session_start();
require_once 'includes/Class-usuario.php';
require_once 'includes/conn-db.php';

$Usuario_class = new Usuario();

// Verificar que sea administrador
if (!$Usuario_class->usuarioLogueado() || !in_array($_SESSION['user_rol'], ['administrador', 'Administrador'])) {
    die("Acceso denegado. Solo administradores pueden ejecutar este script.");
}

$database = new Database();
$conn = $database->getConnection();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Limpiar Duplicados de Horas Extras</title>
    <link href="assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h3 class="mb-0"><i class="ri-alert-line"></i> Limpiar Duplicados de Horas Extras</h3>
                    </div>
                    <div class="card-body">
                        
                        <div class="alert alert-warning">
                            <h5><i class="ri-error-warning-line"></i> Importante:</h5>
                            <p>Este script identifica y elimina registros DUPLICADOS de horas extras que se crearon incorrectamente en la tabla <code>registros_horas</code> cuando se aprobaban horas extras.</p>
                            <p><strong>Las horas extras deben estar SOLO en <code>solicitudes_horas_extras</code></strong></p>
                        </div>

                        <?php
                        if (isset($_POST['ejecutar_limpieza'])) {
                            try {
                                echo '<div class="alert alert-info"><i class="ri-information-line"></i> Iniciando análisis...</div>';
                                
                                // 1. Encontrar registros en registros_horas que coinciden con solicitudes aprobadas
                                $query_duplicados = "
                                    SELECT 
                                        rh.id as registro_id,
                                        rh.usuario_id,
                                        rh.fecha,
                                        rh.horas_trabajadas,
                                        rh.orden_produccion_id,
                                        she.id as solicitud_id,
                                        she.total_horas_extras,
                                        she.estado,
                                        u.nombre_completo,
                                        op.codigo_op
                                    FROM registros_horas rh
                                    INNER JOIN solicitudes_horas_extras she ON (
                                        rh.usuario_id = she.usuario_id 
                                        AND rh.fecha = she.fecha 
                                        AND rh.orden_produccion_id = she.orden_produccion_id
                                        AND ABS(rh.horas_trabajadas - she.total_horas_extras) < 0.01
                                        AND she.estado = 'aprobada'
                                    )
                                    INNER JOIN usuarios u ON rh.usuario_id = u.id
                                    INNER JOIN ordenes_produccion op ON rh.orden_produccion_id = op.id
                                    ORDER BY rh.fecha DESC
                                ";
                                
                                $stmt = $conn->query($query_duplicados);
                                $duplicados = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if (count($duplicados) > 0) {
                                    echo '<div class="alert alert-warning">';
                                    echo '<h5>Se encontraron ' . count($duplicados) . ' registros duplicados:</h5>';
                                    echo '<div class="table-responsive">';
                                    echo '<table class="table table-sm table-striped">';
                                    echo '<thead><tr>';
                                    echo '<th>Registro ID</th><th>Usuario</th><th>Fecha</th><th>Horas</th><th>Orden</th><th>Solicitud ID</th>';
                                    echo '</tr></thead><tbody>';
                                    
                                    foreach ($duplicados as $dup) {
                                        echo '<tr>';
                                        echo '<td>' . $dup['registro_id'] . '</td>';
                                        echo '<td>' . htmlspecialchars($dup['nombre_completo']) . '</td>';
                                        echo '<td>' . $dup['fecha'] . '</td>';
                                        echo '<td>' . $dup['horas_trabajadas'] . ' hrs</td>';
                                        echo '<td>' . htmlspecialchars($dup['codigo_op']) . '</td>';
                                        echo '<td>' . $dup['solicitud_id'] . '</td>';
                                        echo '</tr>';
                                    }
                                    
                                    echo '</tbody></table>';
                                    echo '</div></div>';
                                    
                                    // Confirmar eliminación
                                    if (isset($_POST['confirmar_eliminacion'])) {
                                        $conn->beginTransaction();
                                        
                                        $ids_a_eliminar = array_column($duplicados, 'registro_id');
                                        $placeholders = implode(',', array_fill(0, count($ids_a_eliminar), '?'));
                                        
                                        $query_delete = "DELETE FROM registros_horas WHERE id IN ($placeholders)";
                                        $stmt_delete = $conn->prepare($query_delete);
                                        
                                        foreach ($ids_a_eliminar as $index => $id) {
                                            $stmt_delete->bindValue(($index + 1), $id, PDO::PARAM_INT);
                                        }
                                        
                                        $stmt_delete->execute();
                                        $eliminados = $stmt_delete->rowCount();
                                        
                                        $conn->commit();
                                        
                                        echo '<div class="alert alert-success">';
                                        echo '<h5><i class="ri-check-line"></i> ¡Limpieza completada!</h5>';
                                        echo '<p>Se eliminaron <strong>' . $eliminados . '</strong> registros duplicados de la tabla <code>registros_horas</code>.</p>';
                                        echo '<p>Las horas extras permanecen correctamente en <code>solicitudes_horas_extras</code>.</p>';
                                        echo '</div>';
                                        
                                        echo '<a href="sincronizar-projectdashboard.php" class="btn btn-primary">Ir a Sincronización</a> ';
                                        echo '<a href="horas-extras.php" class="btn btn-info">Ver Horas Extras</a>';
                                        
                                    } else {
                                        echo '<form method="POST">';
                                        echo '<input type="hidden" name="ejecutar_limpieza" value="1">';
                                        echo '<input type="hidden" name="confirmar_eliminacion" value="1">';
                                        echo '<div class="alert alert-danger">';
                                        echo '<h5><i class="ri-delete-bin-line"></i> ¿Está seguro?</h5>';
                                        echo '<p>Se eliminarán <strong>' . count($duplicados) . '</strong> registros de la tabla <code>registros_horas</code>.</p>';
                                        echo '<p>Las horas extras seguirán existiendo en <code>solicitudes_horas_extras</code> donde deben estar.</p>';
                                        echo '</div>';
                                        echo '<button type="submit" class="btn btn-danger btn-lg">Sí, eliminar duplicados</button> ';
                                        echo '<a href="index.php" class="btn btn-secondary btn-lg">Cancelar</a>';
                                        echo '</form>';
                                    }
                                    
                                } else {
                                    echo '<div class="alert alert-success">';
                                    echo '<h5><i class="ri-check-line"></i> ¡No se encontraron duplicados!</h5>';
                                    echo '<p>La base de datos está limpia. No hay registros duplicados de horas extras.</p>';
                                    echo '</div>';
                                    
                                    echo '<a href="index.php" class="btn btn-primary">Volver al inicio</a>';
                                }
                                
                            } catch (Exception $e) {
                                if ($conn->inTransaction()) {
                                    $conn->rollBack();
                                }
                                echo '<div class="alert alert-danger">';
                                echo '<h5><i class="ri-error-warning-line"></i> Error</h5>';
                                echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
                                echo '</div>';
                            }
                            
                        } else {
                            // Formulario inicial
                            ?>
                            <h5>¿Qué hace este script?</h5>
                            <ol>
                                <li>Busca registros en <code>registros_horas</code> que coincidan exactamente con solicitudes aprobadas en <code>solicitudes_horas_extras</code></li>
                                <li>Compara: usuario, fecha, orden de producción y horas</li>
                                <li>Elimina los duplicados de <code>registros_horas</code></li>
                                <li>Mantiene las horas extras correctamente en <code>solicitudes_horas_extras</code></li>
                            </ol>

                            <div class="alert alert-info">
                                <h6>¿Por qué existían duplicados?</h6>
                                <p>Había un error en el código que al aprobar una hora extra, la insertaba también en <code>registros_horas</code>. 
                                Esto causaba que en la sincronización aparecieran como horas normales + horas extras, duplicando los costos.</p>
                                <p><strong>Este error ya fue corregido en el código.</strong> Este script limpia los datos históricos.</p>
                            </div>

                            <form method="POST">
                                <button type="submit" name="ejecutar_limpieza" value="1" class="btn btn-primary btn-lg">
                                    <i class="ri-search-line"></i> Buscar duplicados
                                </button>
                                <a href="index.php" class="btn btn-secondary btn-lg">Cancelar</a>
                            </form>
                            <?php
                        }
                        ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
