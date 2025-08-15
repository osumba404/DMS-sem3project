<?php
/**
 * API Endpoint: Admin - Update Responder
 * Method: POST
 * Body: JSON with responder_id, full_name, team, and optional password
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
$full_name = $data['full_name'] ?? null;
$team = $data['team'] ?? null;
$password = $data['password'] ?? null;

if (empty($responder_id) || empty($full_name) || empty($team)) {
    send_response(400, 'Bad Request. responder_id, full_name, and team are required.');
}

// Check if a new password is being set
if (!empty($password)) {
    // A new password is provided, so update it
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE responders SET full_name = ?, team = ?, password = ? WHERE id = ?");
    $stmt->bind_param("sssi", $full_name, $team, $hashed_password, $responder_id);
} else {
    // No new password, update other details only
    $stmt = $conn->prepare("UPDATE responders SET full_name = ?, team = ? WHERE id = ?");
    $stmt->bind_param("ssi", $full_name, $team, $responder_id);
}

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        send_response(200, 'Responder updated successfully.');
    } else {
        send_response(404, 'Responder not found or data is unchanged.');
    }
} else {
    send_response(500, 'Internal Server Error.');
}
$stmt->close();
$conn->close();
?>