<?php
/**
 * API Endpoint: Get User Profile
 *
 * Fetches non-sensitive profile details for a specific user.
 * Method: GET
 * Required GET parameters:
 * - user_id
 */

header('Content-Type: application/json');
require_once '../../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    exit();
}

$user_id = $_GET['user_id'] ?? 0;

if (empty($user_id) || !is_numeric($user_id)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'A valid user_id is required.']);
    exit();
}

try {
    // Select only the data that is safe to display and edit
    $stmt = $conn->prepare("SELECT full_name, email, phone_number FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'message' => 'Profile retrieved successfully.',
            'data' => $user
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'User not found.']);
    }
    
    $stmt->close();
    $conn->close();

} catch (Exception $e) { /* ... error handling ... */ }

exit();
?>