<?php
/**
 * API Endpoint: Admin - Delete Shelter
 * 
 * Deletes a shelter from the system.
 * Method: POST (or DELETE)
 * 
 * Required POST parameters (as JSON):
 * - shelter_id
 */
 
header('Content-Type: application/json');
require_once '../../config/db_connect.php';

function send_response($status, $message) {
    http_response_code($status);
    echo json_encode(['status' => $status < 400 ? 'success' : 'error', 'message' => $message]);
    exit();
}

// Admin authorization check...

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_response(405, 'Method Not Allowed.');
}

$data = json_decode(file_get_contents("php://input"), true);
$shelter_id = $data['shelter_id'] ?? null;

if (empty($shelter_id)) {
    send_response(400, 'Bad Request. shelter_id is required.');
}

$stmt = $conn->prepare("DELETE FROM shelters WHERE id = ?");
$stmt->bind_param("i", $shelter_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        send_response(200, 'Shelter deleted successfully.');
    } else {
        send_response(404, 'Shelter not found.');
    }
} else {
    send_response(500, 'Internal Server Error. Could not delete shelter.');
}

$stmt->close();
$conn->close();
?>