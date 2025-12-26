<?php
/**
 * Script de depuración de sesión
 * Muestra información de la sesión actual para diagnosticar problemas
 */

session_start();
require_once 'includes/conn-db.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Depuración de Sesión</title>
    <link href="assets/css/app-saas.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">Depuración de Sesión de Usuario</h1>
                
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Datos de Sesión Actual</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($_SESSION)): ?>
                            <div class="alert alert-warning">
                                <strong>⚠ No hay sesión activa</strong>
                                <p class="mb-0">Por favor, <a href="login.php">inicie sesión</a></p>
                            </div>
                        <?php else: ?>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Variable de Sesión</th>
                                        <th>Valor</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $variables_criticas = ['user_id', 'username', 'nombre_completo', 'rol'];
                                    
                                    foreach ($variables_criticas as $var) {
                                        $existe = isset($_SESSION[$var]);
                                        $valor = $existe ? $_SESSION[$var] : 'NO DEFINIDO';
                                        $estado = $existe ? '<span class="badge bg-success">✓ OK</span>' : '<span class="badge bg-danger">✗ Falta</span>';
                                        
                                        echo "<tr>";
                                        echo "<td><code>\$_SESSION['$var']</code></td>";
                                        echo "<td><strong>" . htmlspecialchars($valor) . "</strong></td>";
                                        echo "<td>$estado</td>";
                                        echo "</tr>";
                                    }
                                    
                                    // Mostrar todas las variables de sesión
                                    echo "<tr><td colspan='3' class='bg-light'><strong>Otras variables:</strong></td></tr>";
                                    foreach ($_SESSION as $key => $value) {
                                        if (!in_array($key, $variables_criticas)) {
                                            echo "<tr>";
                                            echo "<td><code>\$_SESSION['$key']</code></td>";
                                            echo "<td>" . htmlspecialchars(print_r($value, true)) . "</td>";
                                            echo "<td><span class='badge bg-info'>Info</span></td>";
                                            echo "</tr>";
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (isset($_SESSION['user_id'])): ?>
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Verificación en Base de Datos</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        try {
                            $database = new Database();
                            $conn = $database->getConnection();
                            
                            $user_id = $_SESSION['user_id'];
                            $query = "SELECT id, nombre_completo, username, email, rol, is_active, 
                                            fecha_ingreso, departamento, created_at 
                                      FROM usuarios WHERE id = ?";
                            $stmt = $conn->prepare($query);
                            $stmt->execute([$user_id]);
                            
                            if ($stmt->rowCount() > 0) {
                                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                                
                                echo '<div class="alert alert-success">';
                                echo '<strong>✓ Usuario encontrado en la base de datos</strong>';
                                echo '</div>';
                                
                                echo '<table class="table table-bordered">';
                                echo '<tr><th>Campo</th><th>Valor</th></tr>';
                                
                                foreach ($usuario as $campo => $valor) {
                                    echo "<tr>";
                                    echo "<td><strong>" . htmlspecialchars($campo) . "</strong></td>";
                                    echo "<td>" . htmlspecialchars($valor) . "</td>";
                                    echo "</tr>";
                                }
                                
                                echo '</table>';
                                
                                // Verificar estado
                                if ($usuario['is_active'] == 1) {
                                    echo '<div class="alert alert-success">✓ Usuario activo</div>';
                                } else {
                                    echo '<div class="alert alert-danger">✗ Usuario INACTIVO - No puede registrar horas</div>';
                                }
                                
                            } else {
                                echo '<div class="alert alert-danger">';
                                echo '<strong>✗ ERROR: Usuario NO existe en la base de datos</strong>';
                                echo '<p>El ID de sesión (' . htmlspecialchars($user_id) . ') no corresponde a ningún usuario.</p>';
                                echo '<p><strong>Solución:</strong> Cierre sesión y vuelva a iniciar sesión.</p>';
                                echo '</div>';
                            }
                            
                        } catch (Exception $e) {
                            echo '<div class="alert alert-danger">';
                            echo '<strong>Error al consultar base de datos:</strong><br>';
                            echo htmlspecialchars($e->getMessage());
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Verificación de Foreign Keys</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        try {
                            $database = new Database();
                            $conn = $database->getConnection();
                            
                            // Verificar foreign key de registros_horas
                            $query = "SELECT 
                                        CONSTRAINT_NAME,
                                        TABLE_NAME,
                                        COLUMN_NAME,
                                        REFERENCED_TABLE_NAME,
                                        REFERENCED_COLUMN_NAME
                                      FROM information_schema.KEY_COLUMN_USAGE
                                      WHERE TABLE_SCHEMA = 'horas_produccion_db'
                                        AND TABLE_NAME = 'registros_horas'
                                        AND REFERENCED_TABLE_NAME IS NOT NULL";
                            
                            $stmt = $conn->query($query);
                            $foreign_keys = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            echo '<table class="table table-sm">';
                            echo '<tr><th>Constraint</th><th>Columna</th><th>Referencia</th></tr>';
                            
                            foreach ($foreign_keys as $fk) {
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($fk['CONSTRAINT_NAME']) . '</td>';
                                echo '<td>' . htmlspecialchars($fk['COLUMN_NAME']) . '</td>';
                                echo '<td>' . htmlspecialchars($fk['REFERENCED_TABLE_NAME']) . '.' . htmlspecialchars($fk['REFERENCED_COLUMN_NAME']) . '</td>';
                                echo '</tr>';
                            }
                            
                            echo '</table>';
                            
                        } catch (Exception $e) {
                            echo '<div class="alert alert-danger">' . htmlspecialchars($e->getMessage()) . '</div>';
                        }
                        ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0">Acciones</h5>
                    </div>
                    <div class="card-body">
                        <a href="login.php" class="btn btn-primary">
                            <i class="ri-login-box-line"></i> Ir a Login
                        </a>
                        <a href="action/session-destroy.php" class="btn btn-danger">
                            <i class="ri-logout-box-line"></i> Cerrar Sesión
                        </a>
                        <a href="registrar-horas.php" class="btn btn-success">
                            <i class="ri-time-line"></i> Registrar Horas
                        </a>
                        <a href="verificar-sistema.php" class="btn btn-info">
                            <i class="ri-settings-3-line"></i> Verificar Sistema
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
