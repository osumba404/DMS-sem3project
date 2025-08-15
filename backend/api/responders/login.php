<?php
/**
 * API Endpoint: Responder Login
 * 
 * Authenticates an emergency responder.
 * Method: POST
 * 
 * Required POST parameters (as JSON):
 * - username
 * - password
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

$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'] ?? null;
$password = $data['password'] ?? null;

if (empty($username) || empty($password)) {
    send_response(400, 'Bad Request. Username and password are required.');
}

// Prepare and execute the SELECT statement
$stmt = $conn->prepare("SELECT id, full_name, team, password FROM responders WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $responder = $result->fetch_assoc();
    
    // Verify the password against the stored hash
    if (password_verify($password, $responder['password'])) {
        // Password is correct
        $responder_data = [
            'responder_id' => $responder['id'],
            'full_name' => $responder['full_name'],
            'team' => $responder['team']
        ];
        send_response(200, 'Login successful.', $responder_data);
    } else {
        send_response(401, 'Unauthorized. Incorrect username or password.');
    }
} else {
    send_response(401, 'Unauthorized. Incorrect username or password.');
}

$stmt->close();
$conn->close();
?>