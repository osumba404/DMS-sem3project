<?php
/**
 * API Endpoint: Admin - Create Awareness Content
 * Method: POST
 * Body: JSON with title, content, type, language
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
// Admin auth check...

$data = json_decode(file_get_contents("php://input"), true);
$title = $data['title'] ?? null;
$content = $data['content'] ?? null;
$type = $data['type'] ?? null;
$language = $data['language'] ?? 'en';

if (empty($title) || empty($content) || empty($type)) {
    send_response(400, 'Bad Request. Title, content, and type are required.');
}

$stmt = $conn->prepare("INSERT INTO awareness_content (title, content, type, language) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $title, $content, $type, $language);

if ($stmt->execute()) {
    $content_id = $stmt->insert_id;
    send_response(201, 'Awareness content created.', ['id' => $content_id]);
} else {
    send_response(500, 'Internal Server Error.');
}
$stmt->close();
$conn->close();
?>