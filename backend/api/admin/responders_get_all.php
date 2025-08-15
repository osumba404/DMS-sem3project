<?php
/**
 * API Endpoint: Admin - List All Responders
 * Method: GET
 */
header('Content-Type: application/json');
require_once '../../config/db_connect.php';

// Verify admin session...

$query = "SELECT 
            id, 
            username, 
            full_name, 
            team,
            created_at
          FROM responders 
          ORDER BY full_name ASC";

$result = $conn->query($query);
$responders = [];
while ($row = $result->fetch_assoc()) {
    $responders[] = $row;
}

http_response_code(200);
echo json_encode(['status' => 'success', 'data' => $responders]);

$conn->close();
?>