<?php
/**
 * API Endpoint: Admin - List All Public Users
 * Method: GET
 */
header('Content-Type: application/json');
require_once '../../config/db_connect.php';

// Verify admin session...

$query = "SELECT 
            id, 
            full_name, 
            email, 
            phone_number,
            is_safe,
            is_tourist,
            created_at
          FROM users 
          ORDER BY created_at DESC";

$result = $conn->query($query);
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

http_response_code(200);
echo json_encode(['status' => 'success', 'data' => $users]);

$conn->close();
?>