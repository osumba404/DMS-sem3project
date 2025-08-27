<?php
/**
 * API Endpoint: Get Broadcast History for Mobile App
 *
 * Retrieves a list of all broadcast messages, ordered from newest to oldest.
 * Method: GET
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../../config/db_connect.php';

try {
    // Select the necessary fields for the alert history display
    $sql = "SELECT 
                id, 
                title, 
                body, 
                target_audience,
                sent_at
            FROM broadcast_messages 
            ORDER BY sent_at DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch all messages into an array
    $messages = $result->fetch_all(MYSQLI_ASSOC);
    
    $stmt->close();
    $conn->close();

    // --- Success Response ---
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => count($messages) . ' messages found.',
        'data' => $messages
    ]);

} catch (Exception $e) {
    // --- Error Response ---
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal Server Error: ' . $e->getMessage()
    ]);
}

exit();
?>