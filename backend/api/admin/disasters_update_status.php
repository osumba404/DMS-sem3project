<?php
/**
 * API Endpoint: Admin - Update Disaster Status
 * 
 * Updates the status of an existing disaster.
 * Method: POST (or PUT)
 * 
 * Required POST parameters (as JSON):
 * - disaster_id
 * - new_status ('Prepare', 'Evacuate', 'All Clear', 'Inactive')
 */

header('Content-Type: application/json');
require_once '../../config/db_connect.php';

function send_response($status, $message, $data = null) {
    http_response_code($status);
    $response = ['status' => $status < 400 ? 'success' : 'error', 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit();
}

// Again, admin authorization should be checked here.

$data = json_decode(file_get_contents("php://input"), true);

$disaster_id = $data['disaster_id'] ?? null;
$new_status = $data['new_status'] ?? null;

if (empty($disaster_id) || empty($new_status)) {
    send_response(400, 'Bad Request. disaster_id and new_status are required.');
}

$valid_statuses = ['Prepare', 'Evacuate', 'All Clear', 'Inactive'];
if (!in_array($new_status, $valid_statuses)) {
    send_response(400, 'Invalid status. Must be one of: Prepare, Evacuate, All Clear, Inactive.');
}

$stmt = $conn->prepare("UPDATE disasters SET status = ? WHERE id = ?");
$stmt->bind_param("si", $new_status, $disaster_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        send_response(200, 'Disaster status updated successfully.');
    } else {
        send_response(404, 'Disaster not found or status is already set to the provided value.');
    }
} else {
    send_response(500, 'Internal Server Error. Failed to update status.');
}

$stmt->close();
$conn->close();
?>