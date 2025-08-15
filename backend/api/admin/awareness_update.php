<?php
/**
 * API Endpoint: Admin - Update Awareness Content
 * Method: POST
 * Body: JSON with id, title, content, type, language
 */
header('Content-Type: application/json');
require_once '../../config/db_connect.php';

function send_response($status, $message) { /* ... same as above ... */ }
// Admin auth check...

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;
// ... get title, content, type, language ...

if (empty($id) || empty($title) || empty($content) || empty($type)) {
    send_response(400, 'Bad Request. All fields are required.');
}

$stmt = $conn->prepare("UPDATE awareness_content SET title=?, content=?, type=?, language=? WHERE id=?");
$stmt->bind_param("ssssi", $title, $content, $type, $language, $id);
// ... execute and check affected_rows ...
?>