<?php
/**
 * API Endpoint: Accept Contact Request
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

// In a real app, you MUST verify that the logged-in user is the `contact_user_id`
// of this request, so they can't accept requests on behalf of others.

// The data is coming from @FormUrlEncoded, so read from $_POST
$request_id = $_POST['request_id'] ?? null;

if (empty($request_id)) {
    send_response(400, 'Bad Request. request_id is required.');
}

$stmt = $conn->prepare("UPDATE emergency_contacts SET status = 'accepted' WHERE id = ?");
if (!$stmt) { send_response(500, 'SQL Prepare Error'); }

$stmt->bind_param("i", $request_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        send_response(200, 'Contact request accepted.');
    } else {
        send_response(404, 'Request not found or already accepted.');
    }
} else {
    send_response(500, 'Failed to execute request: ' . $stmt->error);
}
?>