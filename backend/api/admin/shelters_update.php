<?php
/**
 * API Endpoint: Admin - Update Shelter
 * 
 * Updates details for an existing shelter.
 * Method: POST (or PUT)
 * 
 * Required POST parameters (as JSON):
 * - shelter_id
 * - name
 * - capacity
 * - status ('Open', 'Full', 'Closed')
 * - latitude
 * - longitude
 * - supplies
 */
 
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');
require_once '../../config/db_connect.php';

function send_response($status, $message) {
    http_response_code($status);
    echo json_encode(['status' => $status < 400 ? 'success' : 'error', 'message' => $message]);
    exit();
}

// Admin authorization check...

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_response(405, 'Method Not Allowed.');
}

$data = json_decode(file_get_contents("php://input"), true);

$shelter_id = $data['shelter_id'] ?? null;
$name = $data['name'] ?? null;
$capacity = $data['capacity'] ?? null;
$status = $data['status'] ?? null;
$latitude = $data['latitude'] ?? null;
$longitude = $data['longitude'] ?? null;
$supplies = $data['supplies'] ?? null;

if (empty($shelter_id) || empty($name) || empty($capacity) || empty($status)) {
    send_response(400, 'Bad Request. shelter_id, name, capacity, and status are required.');
}

try {
    $stmt = $conn->prepare("UPDATE shelters SET name = ?, capacity = ?, status = ?, latitude = ?, longitude = ?, supplies = ? WHERE id = ?");
    $stmt->bind_param("sissis", $name, $capacity, $status, $latitude, $longitude, $supplies, $shelter_id);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        send_response(200, 'Shelter updated successfully.');
    } else {
        send_response(404, 'Shelter not found or no changes made');
    }
} catch (Exception $e) {
    send_response(500, 'Internal Server Error. Failed to update shelter: ' . $e->getMessage());
}

$stmt->close();
$conn->close();
?>