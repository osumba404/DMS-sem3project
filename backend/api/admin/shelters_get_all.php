<?php
/**
 * API Endpoint: Admin - List All Shelters
 * Method: GET
 */
header('Content-Type: application/json');
require_once '../../config/db_connect.php';

// Verify admin session...

$query = "SELECT 
            id, 
            name, 
            ST_Y(location) as latitude, 
            ST_X(location) as longitude, 
            capacity, 
            current_occupancy, 
            status, 
            updated_at 
          FROM shelters 
          ORDER BY name ASC";

$result = $conn->query($query);
$shelters = [];
while ($row = $result->fetch_assoc()) {
    $shelters[] = $row;
}

http_response_code(200);
echo json_encode(['status' => 'success', 'data' => $shelters]);

$conn->close();
?>