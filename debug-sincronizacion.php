<?php
// debug-sincronizacion.php
require_once 'includes/conn-db.php';

$database = new Database();
$conn = $database->getConnection();

echo "<h2>Debug: Tabla de Sincronización</h2>";

// Ver últimos registros
echo "<h3>Últimos 10 registros de sincronización:</h3>";
$query = "SELECT * FROM sincronizacion_projectdashboard ORDER BY fecha_sincronizacion DESC LIMIT 10";
$stmt = $conn->prepare($query);
$stmt->execute();
$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
print_r($registros);
echo "</pre>";

// Ver registros de producción
echo "<h3>Registros de tipo 'horas_produccion':</h3>";
$query = "SELECT * FROM sincronizacion_projectdashboard WHERE tipo_registro = 'horas_produccion' ORDER BY fecha_sincronizacion DESC LIMIT 10";
$stmt = $conn->prepare($query);
$stmt->execute();
$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
print_r($registros);
echo "</pre>";

// Ver estructura de la tabla
echo "<h3>Estructura de la tabla:</h3>";
$query = "DESCRIBE sincronizacion_projectdashboard";
$stmt = $conn->prepare($query);
$stmt->execute();
$estructura = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
print_r($estructura);
echo "</pre>";
