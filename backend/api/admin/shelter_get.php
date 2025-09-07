<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../../config/db_connect.php';

try {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'Shelter ID is required']);
        exit;
    }

    $shelter_id = intval($_GET['id']);
    
    $stmt = $pdo->prepare("SELECT id, name, capacity, current_occupancy, available_supplies, status, ST_Y(location) as latitude, ST_X(location) as longitude, updated_at FROM shelters WHERE id = ?");
    $stmt->execute([$shelter_id]);
    $shelter = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$shelter) {
        echo json_encode(['success' => false, 'message' => 'Shelter not found']);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'shelter' => $shelter
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
