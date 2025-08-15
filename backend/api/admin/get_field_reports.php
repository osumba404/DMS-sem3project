<?php
/**
 * API Endpoint: Admin - Get Field Reports
 * Method: GET
 * Parameter: ?disaster_id= (optional)
 */
header('Content-Type: application/json');
require_once '../../config/db_connect.php';
// Admin auth check...

$disaster_id = $_GET['disaster_id'] ?? null;

$sql = "SELECT fr.id, fr.report_content, fr.created_at, r.full_name as responder_name, d.name as disaster_name
        FROM field_reports fr
        JOIN responders r ON fr.responder_id = r.id
        JOIN disasters d ON fr.disaster_id = d.id";

if (!empty($disaster_id)) {
    $sql .= " WHERE fr.disaster_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $disaster_id);
} else {
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();
$reports = [];
while ($row = $result->fetch_assoc()) {
    $reports[] = $row;
}
$stmt->close();

http_response_code(200);
echo json_encode(['status' => 'success', 'data' => $reports]);
$conn->close();
?>