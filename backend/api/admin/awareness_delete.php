<?php
/**
 * API Endpoint: Admin - Delete Awareness Content
 * Method: POST
 * Body: JSON with id
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../config/db_connect.php';

function send_response($status, $message) {
    http_response_code($status);
    echo json_encode(['status' => $status < 400 ? 'success' : 'error', 'message' => $message]);
    exit();
}

// Admin auth check - TODO: Implement proper session validation
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_response(405, 'Method Not Allowed. Please use POST.');
}

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if (empty($id)) {
    send_response(400, 'Bad Request. ID is required.');
}

$stmt = $conn->prepare("DELETE FROM awareness_content WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        send_response(200, 'Awareness content deleted successfully.');
    } else {
        send_response(404, 'Awareness content not found.');
    }
} else {
    send_response(500, 'Internal Server Error. Could not delete awareness content.');
}

$stmt->close();
$conn->close();
?>
