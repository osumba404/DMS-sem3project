<?php
/**
 * API Endpoint: Admin - List All Awareness Content
 * Method: GET
 */
header('Content-Type: application/json');
require_once '../../config/db_connect.php';
// Admin auth check...

$result = $conn->query("SELECT id, title, type, language, created_at FROM awareness_content ORDER BY created_at DESC");
$content_list = [];
while ($row = $result->fetch_assoc()) {
    $content_list[] = $row;
}

http_response_code(200);
echo json_encode(['status' => 'success', 'data' => $content_list]);

$conn->close();
?>