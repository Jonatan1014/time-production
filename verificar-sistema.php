<?php
/**
 * Script de verificación del sistema de registro de horas
 * Ejecutar este script para verificar que todo esté configurado correctamente
 */

session_start();
require_once 'includes/conn-db.php';

$errores = [];
$advertencias = [];
$exitos = [];

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Verificación del Sistema</title>
    <link href='assets/css/app-saas.min.css' rel='stylesheet'>
</head>
<body>
    <div class='container mt-5'>
        <div class='row'>
            <div class='col-12'>
                <h1 class='mb-4'>Verificación del Sistema de Registro de Horas</h1>";

// 1. Verificar conexión a base de datos
try {
    $database = new Database();
    $conn = $database->getConnection();
    $exitos[] = "✓ Conexión a base de datos establecida correctamente";
} catch (Exception $e) {
    $errores[] = "✗ Error de conexión a base de datos: " . $e->getMessage();
    echo "<div class='alert alert-danger'><strong>Error crítico:</strong> No se puede conectar a la base de datos</div>";
    echo "</div></div></body></html>";
    exit;
}

// 2. Verificar tabla registros_horas
try {
    $query = "SHOW TABLES LIKE 'registros_horas'";
    $stmt = $conn->query($query);
    if ($stmt->rowCount() > 0) {
        $exitos[] = "✓ Tabla 'registros_horas' existe";
        
        // Verificar estructura
        $query = "DESCRIBE registros_horas";
        $stmt = $conn->query($query);
        $campos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $exitos[] = "✓ Estructura de tabla 'registros_horas' verificada (" . count($campos) . " campos)";
    } else {
        $errores[] = "✗ Tabla 'registros_horas' no existe";
    }
} catch (Exception $e) {
    $errores[] = "✗ Error al verificar tabla 'registros_horas': " . $e->getMessage();
}

// 3. Verificar tabla configuracion_sistema
try {
    $query = "SHOW TABLES LIKE 'configuracion_sistema'";
    $stmt = $conn->query($query);
    if ($stmt->rowCount() > 0) {
        $exitos[] = "✓ Tabla 'configuracion_sistema' existe";
        
        // Verificar registros de configuración
        $query = "SELECT COUNT(*) as total FROM configuracion_sistema";
        $stmt = $conn->query($query);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($resultado['total'] > 0) {
            $exitos[] = "✓ Tabla 'configuracion_sistema' tiene " . $resultado['total'] . " registros";
            
            // Verificar configuraciones críticas
            $configs_criticas = [
                'horas_maximas_por_dia',
                'horas_minimas_por_registro',
                'incremento_horas'
            ];
            
            foreach ($configs_criticas as $config) {
                $query = "SELECT COUNT(*) as total FROM configuracion_sistema WHERE clave = ?";
                $stmt = $conn->prepare($query);
                $stmt->execute([$config]);
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($resultado['total'] > 0) {
                    $exitos[] = "✓ Configuración '$config' existe";
                } else {
                    $advertencias[] = "⚠ Configuración '$config' no existe (se usará valor por defecto)";
                }
            }
        } else {
            $advertencias[] = "⚠ Tabla 'configuracion_sistema' está vacía. Ejecute: includes/database/insert_configuracion_basica.sql";
        }
    } else {
        $errores[] = "✗ Tabla 'configuracion_sistema' no existe";
    }
} catch (Exception $e) {
    $errores[] = "✗ Error al verificar tabla 'configuracion_sistema': " . $e->getMessage();
}

// 4. Verificar tabla ordenes_produccion
try {
    $query = "SHOW TABLES LIKE 'ordenes_produccion'";
    $stmt = $conn->query($query);
    if ($stmt->rowCount() > 0) {
        $exitos[] = "✓ Tabla 'ordenes_produccion' existe";
        
        // Verificar órdenes activas
        $query = "SELECT COUNT(*) as total FROM ordenes_produccion WHERE estado IN ('activa', 'en_proceso')";
        $stmt = $conn->query($query);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($resultado['total'] > 0) {
            $exitos[] = "✓ Hay " . $resultado['total'] . " órdenes de producción activas";
        } else {
            $advertencias[] = "⚠ No hay órdenes de producción activas";
        }
    } else {
        $errores[] = "✗ Tabla 'ordenes_produccion' no existe";
    }
} catch (Exception $e) {
    $errores[] = "✗ Error al verificar tabla 'ordenes_produccion': " . $e->getMessage();
}

// 5. Verificar tabla usuarios
try {
    $query = "SHOW TABLES LIKE 'usuarios'";
    $stmt = $conn->query($query);
    if ($stmt->rowCount() > 0) {
        $exitos[] = "✓ Tabla 'usuarios' existe";
        
        // Verificar usuarios activos
        $query = "SELECT COUNT(*) as total FROM usuarios WHERE is_active = 1";
        $stmt = $conn->query($query);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($resultado['total'] > 0) {
            $exitos[] = "✓ Hay " . $resultado['total'] . " usuarios activos";
        } else {
            $advertencias[] = "⚠ No hay usuarios activos";
        }
    } else {
        $errores[] = "✗ Tabla 'usuarios' no existe";
    }
} catch (Exception $e) {
    $errores[] = "✗ Error al verificar tabla 'usuarios': " . $e->getMessage();
}

// 6. Verificar archivos de clases
$archivos_criticos = [
    'includes/Class-usuario.php',
    'includes/Class-registros-horas.php',
    'includes/Class-configuracion.php',
    'includes/Class-ordenes-produccion.php',
    'includes/conn-db.php'
];

foreach ($archivos_criticos as $archivo) {
    if (file_exists($archivo)) {
        $exitos[] = "✓ Archivo '$archivo' existe";
    } else {
        $errores[] = "✗ Archivo '$archivo' no encontrado";
    }
}

// Mostrar resultados
if (!empty($exitos)) {
    echo "<div class='card border-success mb-3'>
            <div class='card-header bg-success text-white'><h5 class='mb-0'>✓ Verificaciones Exitosas</h5></div>
            <div class='card-body'><ul class='mb-0'>";
    foreach ($exitos as $exito) {
        echo "<li>$exito</li>";
    }
    echo "</ul></div></div>";
}

if (!empty($advertencias)) {
    echo "<div class='card border-warning mb-3'>
            <div class='card-header bg-warning text-dark'><h5 class='mb-0'>⚠ Advertencias</h5></div>
            <div class='card-body'><ul class='mb-0'>";
    foreach ($advertencias as $advertencia) {
        echo "<li>$advertencia</li>";
    }
    echo "</ul></div></div>";
}

if (!empty($errores)) {
    echo "<div class='card border-danger mb-3'>
            <div class='card-header bg-danger text-white'><h5 class='mb-0'>✗ Errores Críticos</h5></div>
            <div class='card-body'><ul class='mb-0'>";
    foreach ($errores as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul></div></div>";
}

// Conclusión
if (empty($errores)) {
    if (empty($advertencias)) {
        echo "<div class='alert alert-success'>
                <h4 class='alert-heading'>¡Sistema completamente funcional!</h4>
                <p>Todas las verificaciones pasaron exitosamente. El sistema está listo para registrar horas.</p>
                <hr>
                <p class='mb-0'><a href='registrar-horas.php' class='btn btn-success'>Registrar Horas</a></p>
              </div>";
    } else {
        echo "<div class='alert alert-warning'>
                <h4 class='alert-heading'>Sistema funcional con advertencias</h4>
                <p>El sistema puede funcionar, pero hay algunas advertencias que deberías revisar.</p>
                <hr>
                <p class='mb-0'><strong>Recomendación:</strong> Ejecuta el script SQL: <code>includes/database/insert_configuracion_basica.sql</code></p>
              </div>";
    }
} else {
    echo "<div class='alert alert-danger'>
            <h4 class='alert-heading'>El sistema tiene errores críticos</h4>
            <p>Debes corregir los errores antes de usar el sistema.</p>
            <hr>
            <p class='mb-0'><strong>Acción recomendada:</strong> Verifica que hayas ejecutado el script SQL: <code>includes/database/structure.sql</code></p>
          </div>";
}

echo "      </div>
        </div>
    </div>
</body>
</html>";
?>
