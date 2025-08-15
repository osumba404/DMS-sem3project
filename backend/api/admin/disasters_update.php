<?php
/**
 * API Endpoint: Admin - Update Disaster Details
 * Method: POST
 * Body: JSON with disaster_id, name, type, and affected_area_wkt
 */
header('Content-Type: application/json');
require_once '../../config/db_connect.php';

function send_response($status, $message) {
    http_response_code($status);
    echo json_encode(['status' => $status < 400 ? 'success' : 'error', 'message' => $message]);
    exit();
}
// Admin auth check...

$data = json_decode(file_get_contents("php://input"), true);
$disaster_id = $data['disaster_id'] ?? null;
$name = $data['name'] ?? null;
$type = $data['type'] ?? null;
$wkt = $data['affected_area_wkt'] ?? null;

if (empty($disaster_id) || empty($name) || empty($type) || empty($wkt)) {
    send_response(400, 'Bad Request. All fields are required.');
}

$stmt = $conn->prepare("UPDATE disasters SET name = ?, type = ?, affected_area_geometry = ST_GeomFromText(?) WHERE id = ?");
$stmt->bind_param("sssi", $name, $type, $wkt, $disaster_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        send_response(200, 'Disaster details updated successfully.');
    } else {
        send_response(404, 'Disaster not found or data is unchanged.');
    }
} else {
    send_response(500, 'Internal Server Error. Failed to update disaster.');
}
$stmt->close();
$conn->close();
?>