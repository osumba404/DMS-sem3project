<?php
/**
 * API Endpoint: Admin - Get Broadcast History
 * Method: GET
 */
header('Content-Type: application/json');
require_once '../../config/db_connect.php';
// Admin auth check...

$sql = "SELECT bm.id, bm.title, bm.body, bm.target_audience, bm.sent_at, a.full_name as admin_name
        FROM broadcast_messages bm
        JOIN admins a ON bm.admin_id = a.id
        ORDER BY bm.sent_at DESC";

$result = $conn->query($sql);
$history = [];
while ($row = $result->fetch_assoc()) {
    $history[] = $row;
}

http_response_code(200);
echo json_encode(['status' => 'success', 'data' => $history]);

$conn->close();
?>