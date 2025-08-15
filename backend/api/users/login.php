<?php
/**
 * API Endpoint: User Login
 * 
 * Handles user authentication.
 * Method: POST
 * 
 * Required POST parameters (as JSON):
 * - email
 * - password
 */

// Set response header to JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // For development, allow all origins
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include the database connection script
require_once '../../config/db_connect.php';

// --- Function for response handling (Vanilla PHP helper) ---
function send_response($status, $message, $data = null) {
    http_response_code($status);
    $response = ['status' => $status < 400 ? 'success' : 'error', 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit();
}

// --- Main Logic ---

// 1. Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_response(405, 'Method Not Allowed. Please use POST.');
}

// 2. Get input data from the request body (JSON)
$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'] ?? null;
$password = $data['password'] ?? null;

// 3. Validate input
if (empty($email) || empty($password)) {
    send_response(400, 'Bad Request. Email and password are required.');
}

// 4. Prepare and execute the SELECT statement to find the user
$stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // User found, fetch the data
    $user = $result->fetch_assoc();
    
    // 5. Verify the password
    // password_verify() is the built-in PHP function to check a password against a hash
    if (password_verify($password, $user['password'])) {
        // Password is correct
        
        // For a real app, you would generate a session token or JWT here.
        // For now, we'll return a simple success message with user data.
        $user_data = [
            'user_id' => $user['id'],
            'full_name' => $user['full_name']
        ];
        
        send_response(200, 'Login successful.', $user_data);
        
    } else {
        // Incorrect password
        send_response(401, 'Unauthorized. Incorrect email or password.');
    }
} else {
    // User not found
    send_response(401, 'Unauthorized. Incorrect email or password.');
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>