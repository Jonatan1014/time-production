<?php
// verificar-conexion.php - Valida si hay conexión a la base de datos
header('Content-Type: application/json');

require_once 'includes/settings-db.php';

try {
    // Intentar conectar a la base de datos
    $conn = new PDO(DB_DSN, DB_USER, DB_PASS, DB_OPTIONS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Si llegamos aquí, la conexión fue exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Conexión establecida',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch(PDOException $e) {
    // Error de conexión - aún en mantenimiento
    echo json_encode([
        'success' => false,
        'message' => 'Sin conexión a la base de datos',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
