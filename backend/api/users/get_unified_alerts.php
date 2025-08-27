<?php
/**
 * API Endpoint: Get Unified Alerts (Broadcasts + Notifications)
 *
 * Fetches all public broadcast messages and all personal notifications for a user,
 * merges them into a single list, and sorts them by timestamp.
 *
 * Method: GET
 * Required GET parameters:
 * - user_id: The ID of the user fetching their alerts.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed.']);
    exit();
}

$user_id = $_GET['user_id'] ?? 0;

if (empty($user_id) || !is_numeric($user_id)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'A valid user_id is required.']);
    exit();
}

try {
    // --- Query 1: Get all public broadcast messages ---
    // We add a 'type' column so the app knows how to display it.
    // We alias columns like 'sent_at' to 'timestamp' for consistent sorting.
    $sql_broadcasts = "SELECT 
                            'broadcast' as type, 
                            id, 
                            title, 
                            body, 
                            sent_at as timestamp 
                       FROM broadcast_messages";
    
    $broadcast_result = $conn->query($sql_broadcasts);
    $broadcasts = $broadcast_result->fetch_all(MYSQLI_ASSOC);


    // --- Query 2: Get all personal notifications for the logged-in user ---
    // We also add a 'type' and alias 'created_at' to 'timestamp'.
    // We get the sender's name for a more descriptive message.
    $sql_notifications = "SELECT 
                                'notification' as type, 
                                n.id, 
                                n.title, 
                                n.message as body, 
                                n.created_at as timestamp,
                                u.full_name as sender_name
                          FROM notifications n
                          LEFT JOIN users u ON n.sender_user_id = u.id
                          WHERE n.recipient_user_id = ?";
                          
    $stmt_notifications = $conn->prepare($sql_notifications);
    $stmt_notifications->bind_param("i", $user_id);
    $stmt_notifications->execute();
    $notifications_result = $stmt_notifications->get_result();
    $notifications = $notifications_result->fetch_all(MYSQLI_ASSOC);
    $stmt_notifications->close();


    // --- Step 3: Merge and Sort ---
    $all_alerts = array_merge($broadcasts, $notifications);

    // Sort the combined array by the 'timestamp' key in descending order (newest first).
    // usort sorts the array in-place.
    usort($all_alerts, function($a, $b) {
        // strtotime converts the MySQL timestamp string into a Unix timestamp for comparison.
        return strtotime($b['timestamp']) - strtotime($a['timestamp']);
    });
    
    $conn->close();

    // --- Success Response ---
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => count($all_alerts) . ' alerts found.',
        'data' => $all_alerts
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal Server Error: ' . $e->getMessage()
    ]);
}

exit();
?>