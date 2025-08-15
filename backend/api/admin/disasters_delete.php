<?php
/**
 * API Endpoint: Admin - Delete Disaster
 * Method: POST
 * Body: JSON with disaster_id
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

if (empty($disaster_id)) {
    send_response(400, 'Bad Request. disaster_id is required.');
}

$stmt = $conn->prepare("DELETE FROM disasters WHERE id = ?");
$stmt->bind_param("i", $disaster_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        send_response(200, 'Disaster deleted successfully.');
    } else {
        send_response(404, 'Disaster not found.');
    }
} else {
    // Handle foreign key constraint errors if any records depend on this disaster
    if ($conn->errno == 1451) {
         send_response(409, 'Conflict: Cannot delete this disaster as other records (e.g., reports, assignments) depend on it.');
    }
    send_response(500, 'Internal Server Error.');
}
$stmt->close();
$conn->close();
?>