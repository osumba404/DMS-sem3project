<?php
/**
 * API Endpoint: Admin - Update User Account
 * Method: POST
 * Body: JSON with user_id, full_name, phone_number, is_safe, is_tourist
 */
header('Content-Type: application/json');
require_once '../../config/db_connect.php';
function send_response($status, $message) { /* ... same as above ... */ }
// Admin auth check...

$data = json_decode(file_get_contents("php://input"), true);
$user_id = $data['user_id'] ?? null;
$full_name = $data['full_name'] ?? null;
$phone_number = $data['phone_number'] ?? null;
$is_safe = $data['is_safe']; // Assuming it comes as true/false
$is_tourist = $data['is_tourist'];

if (empty($user_id) || empty($full_name) || empty($phone_number) || !isset($is_safe) || !isset($is_tourist)) {
    send_response(400, 'Bad Request. All fields are required.');
}

$stmt = $conn->prepare("UPDATE users SET full_name=?, phone_number=?, is_safe=?, is_tourist=? WHERE id=?");
$stmt->bind_param("ssiii", $full_name, $phone_number, $is_safe, $is_tourist, $user_id);

// ... execute and check affected_rows ...
?>