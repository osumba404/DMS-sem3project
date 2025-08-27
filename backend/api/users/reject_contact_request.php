<?php
/**
 * API Endpoint: Reject/Delete Contact Request
 * Method: POST
 * Body: JSON with request_id
 */
header('Content-Type: application/json');
require_once '../../config/db_connect.php';

// --- THE FIX: Add the missing function definition ---
function send_response($status, $message) {
    http_response_code($status);
    echo json_encode(['status' => $status < 400 ? 'success' : 'error', 'message' => $message]);
    exit();
}
// --- END FIX ---

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_response(405, 'Method Not Allowed.');
}

// Again, verify the user is authorized to delete this specific request.

// The data is coming from @FormUrlEncoded, so read from $_POST
$request_id = $_POST['request_id'] ?? null;

if (empty($request_id)) {
    send_response(400, 'Bad Request. request_id is required.');
}

$stmt = $conn->prepare("DELETE FROM emergency_contacts WHERE id = ?");
if (!$stmt) { send_response(500, 'SQL Prepare Error'); }

$stmt->bind_param("i", $request_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        send_response(200, 'Contact request removed.');
    } else {
        send_response(404, 'Request not found.');
    }
} else {
    send_response(500, 'Failed to execute request: ' . $stmt->error);
}
?>