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
$name = $data['name'] ?? null;
$capacity = $data['capacity'] ?? null;
$status = $data['status'] ?? null;

if (empty($shelter_id) || empty($name) || empty($capacity) || empty($status)) {
    send_response(400, 'Bad Request. shelter_id, name, capacity, and status are required.');
}

$stmt = $conn->prepare("UPDATE shelters SET name = ?, capacity = ?, status = ? WHERE id = ?");
$stmt->bind_param("sisi", $name, $capacity, $status, $shelter_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        send_response(200, 'Shelter updated successfully.');
    } else {
        send_response(404, 'Shelter not found or data is unchanged.');
    }
} else {
    send_response(500, 'Internal Server Error. Failed to update shelter.');
}

$stmt->close();
$conn->close();
?>