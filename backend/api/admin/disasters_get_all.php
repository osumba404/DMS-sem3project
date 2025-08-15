<?php
/**
 * API Endpoint: Admin - List All Disasters
 * Method: GET
 */
header('Content-Type: application/json');
require_once '../../config/db_connect.php';

// In a real app, verify admin session/token here.

$query = "SELECT 
            id, 
            name, 
            type, 
            status, 
            ST_AsText(affected_area_geometry) as affected_area,
            created_at 
          FROM disasters 
          ORDER BY created_at DESC";

$result = $conn->query($query);
$disasters = [];
while ($row = $result->fetch_assoc()) {
    $disasters[] = $row;
}

http_response_code(200);
echo json_encode(['status' => 'success', 'data' => $disasters]);

$conn->close();
?>