<?php
// action/obtener-registros-mes.php - Obtener registros filtrados por mes
session_start();
require_once '../includes/Class-usuario.php';
require_once '../includes/Class-horas-produccion.php';
require_once '../includes/conn-db.php';

$database = new Database();
$conn = $database->getConnection();

$Usuario_class = new Usuario();
$HorasProduccion_class = new HorasProduccion($conn);

header('Content-Type: application/json');

if (!$Usuario_class->usuarioLogueado()) {
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

$mes = $_GET['mes'] ?? ''; // formato: YYYY-MM

if (empty($mes)) {
    echo json_encode(['success' => false, 'message' => 'Mes no especificado']);
    exit;
}

// Validar formato de mes
if (!preg_match('/^\d{4}-\d{2}$/', $mes)) {
    echo json_encode(['success' => false, 'message' => 'Formato de mes inválido']);
    exit;
}

try {
    // Obtener primer y último día del mes
    $primerDia = $mes . '-01';
    $ultimoDia = date('Y-m-t', strtotime($primerDia));
    
    // Consultar registros del mes
    $query = "SELECT rhp.*, 
                     u.nombre_completo,
                     u.id as usuario_id,
                     op.codigo_op,
                     op.nombre_producto
              FROM registros_horas_produccion rhp
              INNER JOIN usuarios u ON rhp.usuario_id = u.id
              LEFT JOIN ordenes_produccion op ON rhp.orden_produccion_id = op.id
              WHERE rhp.fecha >= :primer_dia AND rhp.fecha <= :ultimo_dia
              ORDER BY rhp.fecha ASC, u.nombre_completo ASC";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':primer_dia', $primerDia);
    $stmt->bindParam(':ultimo_dia', $ultimoDia);
    $stmt->execute();
    
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'registros' => $registros,
        'mes' => $mes
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al consultar registros: ' . $e->getMessage()
    ]);
}
?>