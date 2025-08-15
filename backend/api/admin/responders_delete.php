<?php
/**
 * API Endpoint: Admin - Delete Responder
 * Method: POST
 * Body: JSON with responder_id
 */
header('Content-Type: application/json');
require_once '../../config/db_connect.php';

function send_response($status, $message) {
    http_response_code($status);
    echo json_encode(['status' => $status < 400 ? 'success' : 'error', 'message' => $message]);
    exit();
}
// Verify admin session...

$data = json_decode(file_get_contents("php://input"), true);
$responder_id = $data['responder_id'] ?? null;

if (empty($responder_id)) {
    send_response(400, 'Bad Request. responder_id is required.');
}

$stmt = $conn->prepare("DELETE FROM responders WHERE id = ?");
$stmt->bind_param("i", $responder_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        send_response(200, 'Responder deleted successfully.');
    } else {
        send_response(404, 'Responder not found.');
    }
} else {
    send_response(500, 'Internal Server Error.');
}
$stmt->close();
$conn->close();
?>