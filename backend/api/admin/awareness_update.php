<?php
/**
 * API Endpoint: Admin - Update Awareness Content
 * Method: POST
 * Body: JSON with id, title, content, type, language
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
$title = $data['title'] ?? null;
$content = $data['content'] ?? null;
$type = $data['type'] ?? null;
$language = $data['language'] ?? 'en';

if (empty($id) || empty($title) || empty($content) || empty($type)) {
    send_response(400, 'Bad Request. ID, title, content, and type are required.');
}

$stmt = $conn->prepare("UPDATE awareness_content SET title=?, content=?, type=?, language=? WHERE id=?");
$stmt->bind_param("ssssi", $title, $content, $type, $language, $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        send_response(200, 'Awareness content updated successfully.');
    } else {
        send_response(404, 'Awareness content not found or data is unchanged.');
    }
} else {
    send_response(500, 'Internal Server Error. Failed to update awareness content.');
}

$stmt->close();
$conn->close();
?>