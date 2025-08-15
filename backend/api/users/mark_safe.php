<?php
/**
 * API Endpoint: Mark User as Safe
 * 
 * Updates the user's status to 'safe' and records their last known location.
 * Method: POST
 * 
 * Required POST parameters (as JSON):
 * - user_id
 * - latitude
 * - longitude
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../config/db_connect.php';

function send_response($status, $message, $data = null) {
    http_response_code($status);
    $response = ['status' => $status < 400 ? 'success' : 'error', 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_response(405, 'Method Not Allowed. Please use POST.');
}

// 1. Get and validate input from the JSON request body
$data = json_decode(file_get_contents("php://input"), true);

$user_id = $data['user_id'] ?? null;
$latitude = $data['latitude'] ?? null;
$longitude = $data['longitude'] ?? null;

if (empty($user_id) || $latitude === null || $longitude === null) {
    send_response(400, 'Bad Request. user_id, latitude, and longitude are required.');
}

if (!is_numeric($user_id) || !is_numeric($latitude) || !is_numeric($longitude)) {
     send_response(400, 'Bad Request. All input values must be numeric.');
}

// 2. Start a transaction to ensure both updates succeed or fail together
$conn->begin_transaction();

try {
    // 3. First SQL statement: Update the user's main status and location
    $stmt1 = $conn->prepare(
        "UPDATE users SET 
            is_safe = TRUE, 
            last_known_latitude = ?, 
            last_known_longitude = ? 
        WHERE id = ?"
    );
    $stmt1->bind_param("ddi", $latitude, $longitude, $user_id);
    $stmt1->execute();

    // Check if the update affected any row
    if ($stmt1->affected_rows === 0) {
        throw new Exception('User not found or status already safe.');
    }
    $stmt1->close();

    // 4. Second SQL statement: Log this location update in the history table
    $stmt2 = $conn->prepare(
        "INSERT INTO user_locations (user_id, location) VALUES (?, POINT(?, ?))"
    );
    $stmt2->bind_param("idd", $user_id, $longitude, $latitude);
    $stmt2->execute();
    $stmt2->close();
    
    // 5. If both queries were successful, commit the transaction
    $conn->commit();
    
    send_response(200, 'User status updated to safe successfully.');

} catch (Exception $e) {
    // If any query fails, roll back the transaction
    $conn->rollback();
    // Use the exception message or a generic one
    send_response(500, 'Failed to update user status. ' . $e->getMessage());
}

$conn->close();
?>