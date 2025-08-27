<?php
/**
 * API Endpoint: Delete Emergency Contact
 * Method: POST
 * Body: JSON with contact_id
 */
header('Content-Type: application/json');
require_once '../../config/db_connect.php';

function send_response($status, $message) {
    http_response_code($status);
    echo json_encode(['status' => $status < 400 ? 'success' : 'error', 'message' => $message]);
    exit();
}

// In a real app, you would also verify that the user trying to delete this
// contact is the actual owner of the contact. For now, we'll keep it simple.

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_response(405, 'Method Not Allowed.');
}

$data = json_decode(file_get_contents("php://input"), true);
$contact_id = $data['contact_id'] ?? null;

if (empty($contact_id)) {
    send_response(400, 'Bad Request. contact_id is required.');
}

$stmt = $conn->prepare("DELETE FROM emergency_contacts WHERE id = ?");
$stmt->bind_param("i", $contact_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        send_response(200, 'Contact deleted successfully.');
    } else {
        send_response(404, 'Contact not found.');
    }
} else {
    send_response(500, 'Internal Server Error. Could not delete contact.');
}

$stmt->close();
$conn->close();
?>