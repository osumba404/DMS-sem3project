<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$report_id = $data['report_id'] ?? null;
$status = $data['status'] ?? null;
$priority = $data['priority'] ?? null;
$admin_notes = $data['admin_notes'] ?? null;
$admin_id = $data['admin_id'] ?? null;

if (empty($report_id)) {
    echo json_encode(['success' => false, 'message' => 'report_id is required']);
    exit;
}

try {
    $updateFields = [];
    $params = [];
    
    if ($status) {
        $updateFields[] = "status = ?";
        $params[] = $status;
    }
    
    if ($priority) {
        $updateFields[] = "priority = ?";
        $params[] = $priority;
    }
    
    if ($admin_notes !== null) {
        $updateFields[] = "admin_notes = ?";
        $params[] = $admin_notes;
    }
    
    if ($admin_id) {
        $updateFields[] = "reviewed_by_admin_id = ?";
        $params[] = $admin_id;
    }
    
    if (empty($updateFields)) {
        echo json_encode(['success' => false, 'message' => 'No fields to update']);
        exit;
    }
    
    $params[] = $report_id;
    
    $sql = "UPDATE user_reports SET " . implode(", ", $updateFields) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Report updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Report not found or no changes made']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
