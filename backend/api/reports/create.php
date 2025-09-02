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

$user_id = $data['user_id'] ?? null;
$title = $data['title'] ?? null;
$description = $data['description'] ?? null;
$category = $data['category'] ?? 'Other';
$priority = $data['priority'] ?? 'Medium';
$latitude = $data['latitude'] ?? null;
$longitude = $data['longitude'] ?? null;
$address = $data['address'] ?? null;
$image_url = $data['image_url'] ?? null;

if (empty($user_id) || empty($title) || empty($description)) {
    echo json_encode(['success' => false, 'message' => 'user_id, title, and description are required']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO user_reports (user_id, title, description, category, priority, latitude, longitude, address, image_url) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $user_id, $title, $description, $category, $priority, 
        $latitude, $longitude, $address, $image_url
    ]);
    
    $report_id = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Report submitted successfully',
        'report_id' => $report_id
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
