<?php
/**
 * API Endpoint: Update Shelter Status (for Responders)
 * 
 * Updates a shelter's details and logs the change.
 * Method: POST
 * 
 * Required POST parameters (as JSON):
 * - shelter_id
 * - responder_id
 * - current_occupancy
 * - status ('Open', 'Full', 'Closed')
 * - available_supplies (optional JSON object, e.g., {"food": true, "water": 500, "blankets": 200})
 * - notes (optional text)
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

// 1. Get input from the JSON request body
$data = json_decode(file_get_contents("php://input"), true);

$shelter_id = $data['shelter_id'] ?? null;
$responder_id = $data['responder_id'] ?? null;
$occupancy = $data['current_occupancy'] ?? null;
$status = $data['status'] ?? null;
$notes = $data['notes'] ?? '';
$supplies_json = isset($data['available_supplies']) ? json_encode($data['available_supplies']) : null;

// 2. Validate input
if (empty($shelter_id) || empty($responder_id) || $occupancy === null || empty($status)) {
    send_response(400, 'Bad Request. shelter_id, responder_id, current_occupancy, and status are required.');
}
$valid_statuses = ['Open', 'Full', 'Closed'];
if (!in_array($status, $valid_statuses)) {
    send_response(400, 'Bad Request. Status must be one of: Open, Full, Closed.');
}

// 3. Use a transaction to ensure both tables are updated correctly
$conn->begin_transaction();

try {
    // 4. Update the main shelters table
    $stmt1 = $conn->prepare(
        "UPDATE shelters SET 
            current_occupancy = ?,
            status = ?,
            available_supplies = ?,
            last_updated_by_responder_id = ?
         WHERE id = ?"
    );
    $stmt1->bind_param("issii", $occupancy, $status, $supplies_json, $responder_id, $shelter_id);
    $stmt1->execute();

    if ($stmt1->affected_rows === 0) {
        // This could mean the shelter ID doesn't exist.
        throw new Exception('Shelter not found or data is unchanged.');
    }
    $stmt1->close();

    // 5. Insert a record into the shelter_updates log table
    $stmt2 = $conn->prepare(
        "INSERT INTO shelter_updates (shelter_id, responder_id, occupancy, status, notes) 
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt2->bind_param("iiiss", $shelter_id, $responder_id, $occupancy, $status, $notes);
    $stmt2->execute();
    $stmt2->close();

    // 6. If everything succeeded, commit the transaction
    $conn->commit();
    send_response(200, 'Shelter status updated successfully.');

} catch (Exception $e) {
    // If anything fails, roll back all changes
    $conn->rollback();
    send_response(500, 'Database error: ' . $e->getMessage());
}

$conn->close();
?>