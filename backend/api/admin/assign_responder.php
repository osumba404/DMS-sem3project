<?php
/**
 * API Endpoint: Admin - Assign Responder
 * Method: POST
 * Body: JSON with responder_id, admin_id, assignment_details, and EITHER disaster_id OR shelter_id (or both)
 */
header('Content-Type: application/json');
require_once '../../config/db_connect.php';

function send_response($status, $message, $data = null) {
    http_response_code($status);
    $response = ['status' => $status < 400 ? 'success' : 'error', 'message' => $message];
    if ($data !== null) $response['data'] = $data;
    echo json_encode($response);
    exit();
}
// Verify admin session...

$data = json_decode(file_get_contents("php://input"), true);
$responder_id = $data['responder_id'] ?? null;
$admin_id = $data['admin_id'] ?? null;
$disaster_id = $data['disaster_id'] ?? null;
$shelter_id = $data['shelter_id'] ?? null;
$details = $data['assignment_details'] ?? '';

if (empty($responder_id) || empty($admin_id) || (empty($disaster_id) && empty($shelter_id))) {
    send_response(400, 'Bad Request. responder_id, admin_id, and at least one of disaster_id or shelter_id are required.');
}

$stmt = $conn->prepare(
    "INSERT INTO responder_assignments (responder_id, disaster_id, shelter_id, assigned_by_admin_id, assignment_details) VALUES (?, ?, ?, ?, ?)"
);
$stmt->bind_param("iiiis", $responder_id, $disaster_id, $shelter_id, $admin_id, $details);

if ($stmt->execute()) {
    $assignment_id = $stmt->insert_id;
    send_response(201, 'Responder assigned successfully.', ['assignment_id' => $assignment_id]);
} else {
    send_response(500, 'Internal Server Error. Failed to create assignment.');
}
$stmt->close();
$conn->close();
?>