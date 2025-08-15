<?php
/**
 * API Endpoint: Admin - Broadcast Message
 * 
 * Creates a new broadcast message to be sent as an alert.
 * Method: POST
 * 
 * Required POST parameters (as JSON):
 * - admin_id
 * - title
 * - body
 * - target_audience ('All', 'Affected Zone', 'Tourists')
 * - disaster_id (optional, to link the message to an event)
 */

header('Content-Type: application/json');
require_once '../../config/db_connect.php';

function send_response($status, $message) {
    http_response_code($status);
    echo json_encode(['status' => $status < 400 ? 'success' : 'error', 'message' => $message]);
    exit();
}

// Admin authorization check...

$data = json_decode(file_get_contents("php://input"), true);

$admin_id = $data['admin_id'] ?? null;
$title = $data['title'] ?? null;
$body = $data['body'] ?? null;
$target_audience = $data['target_audience'] ?? null;
$disaster_id = $data['disaster_id'] ?? null; // Can be null

if (empty($admin_id) || empty($title) || empty($body) || empty($target_audience)) {
    send_response(400, 'Bad Request. admin_id, title, body, and target_audience are required.');
}

$stmt = $conn->prepare(
    "INSERT INTO broadcast_messages (admin_id, disaster_id, title, body, target_audience) VALUES (?, ?, ?, ?, ?)"
);
$stmt->bind_param("iisss", $admin_id, $disaster_id, $title, $body, $target_audience);

if ($stmt->execute()) {
    send_response(201, 'Broadcast message queued for sending.');
} else {
    send_response(500, 'Internal Server Error. Could not queue the message.');
}

$stmt->close();
$conn->close();
?>