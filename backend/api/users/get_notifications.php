<?php
/**
 * API Endpoint: Get Unread Notifications
 *
 * Retrieves all unread notifications for the logged-in user.
 * Method: GET
 * Required GET parameters:
 * - user_id: The ID of the user fetching their notifications.
 */

header('Content-Type: application/json');
require_once '../../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    exit();
}

$user_id = $_GET['user_id'] ?? null;
if (empty($user_id) || !is_numeric($user_id)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Valid user_id is required.']);
    exit();
}

try {
    $sql = "SELECT 
                id, 
                title, 
                message, 
                created_at 
            FROM notifications 
            WHERE recipient_user_id = ? AND is_read = FALSE 
            ORDER BY created_at DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $notifications = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Notifications retrieved.',
        'data' => $notifications
    ]);

} catch (Exception $e) { /* ... error handling ... */ }

$conn->close();
exit();
?>