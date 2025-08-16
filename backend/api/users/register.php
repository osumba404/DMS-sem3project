
<?php
/**
 * API Endpoint: User Registration
 * 
 * Handles new user registration from the mobile app.
 * Method: POST
 * 
 * Required POST parameters:
 * - full_name
 * - email
 * - password
 * - phone_number
 * - is_tourist (optional, defaults to false)
 */

// Set response header to JSON
header('Content-Type: application/json');

// Include the database connection script
require_once '../../config/db_connect.php';

// --- Functions for response handling ---
function send_response($status, $message, $data = null) {
    http_response_code($status);
    $response = ['status' => $status < 400 ? 'success' : 'error', 'message' => $message];
    if ($data) {
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

// 2. Get and validate input data from the POST request
$data = json_decode(file_get_contents('php://input'), true);

$full_name = $data['full_name'] ?? null;
$email = $data['email'] ?? null;
$password = $data['password'] ?? null;
$phone_number = $data['phone_number'] ?? null;
$is_tourist = isset($data['is_tourist']) ? (bool)$data['is_tourist'] : false;

if (empty($full_name) || empty($email) || empty($password) || empty($phone_number)) {
    send_response(400, 'Bad Request. All required fields (full_name, email, password, phone_number) must be provided.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    send_response(400, 'Invalid email format.');
}

// 3. Check if email or phone number already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR phone_number = ?");
$stmt->bind_param("ss", $email, $phone_number);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    send_response(409, 'Conflict. A user with this email or phone number already exists.');
}
$stmt->close();

// 4. Hash the password for security
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// 5. Prepare and execute the SQL INSERT statement
$stmt = $conn->prepare(
    "INSERT INTO users (full_name, email, password, phone_number, is_tourist) VALUES (?, ?, ?, ?, ?)"
);
$stmt->bind_param("ssssi", $full_name, $email, $hashed_password, $phone_number, $is_tourist);

if ($stmt->execute()) {
    // On successful registration
    $user_id = $stmt->insert_id;
    send_response(201, 'User registered successfully.', ['user_id' => $user_id]);
} else {
    // On failure
    send_response(500, 'Internal Server Error. Failed to register user.');
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
