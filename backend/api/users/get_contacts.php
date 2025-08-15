
<?php
/**
 * API Endpoint: Get Emergency Contacts
 * 
 * Retrieves all emergency contacts for a given user.
 * Method: GET
 * 
 * Required GET parameters:
 * - user_id
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

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

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    send_response(405, 'Method Not Allowed. Please use GET.');
}

// 1. Get and validate user_id from the query string
$user_id = $_GET['user_id'] ?? null;

if (empty($user_id)) {
    send_response(400, 'Bad Request. user_id is a required parameter.');
}
if (!is_numeric($user_id)) {
    send_response(400, 'Bad Request. user_id must be a numeric value.');
}

// 2. Prepare and execute the SELECT statement
$stmt = $conn->prepare(
    "SELECT id, name, phone_number, relationship FROM emergency_contacts WHERE user_id = ?"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$contacts = [];
while ($row = $result->fetch_assoc()) {
    $contacts[] = $row;
}

// 3. Send the response
send_response(200, 'Contacts fetched successfully.', $contacts);

$stmt->close();
$conn->close();
?>