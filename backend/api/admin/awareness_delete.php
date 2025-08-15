<?php
/**
 * API Endpoint: Admin - Delete Awareness Content
 * Method: POST
 * Body: JSON with id
 */
header('Content-Type: application/json');
require_once '../../config/db_connect.php';
function send_response($status, $message) { /* ... same as above ... */ }
// Admin auth check...

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if (empty($id)) {
    send_response(400, 'Bad Request. ID is required.');
}

$stmt = $conn->prepare("DELETE FROM awareness_content WHERE id = ?");
$stmt->bind_param("i", $id);
// ... execute and check affected_rows ...
?>

