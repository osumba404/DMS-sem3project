<?php
/**
 * API Endpoint: Update User Profile
 *
 * Updates a user's full_name, phone_number, and optionally password.
 * Method: POST
 * Body: JSON with user_id, full_name, phone_number, and optional 'password'
 */

header('Content-Type: application/json');
require_once '../../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

$user_id = $data['user_id'] ?? 0;
$full_name = $data['full_name'] ?? '';
$phone_number = $data['phone_number'] ?? '';
$password = $data['password'] ?? null; // Password is optional

if (empty($user_id) || empty($full_name) || empty($phone_number)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'User ID, full name, and phone number are required.']);
    exit();
}

try {
    if (!empty($password)) {
        // SCENARIO 1: Password is being changed
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone_number = ?, password = ? WHERE id = ?");
        $stmt->bind_param("sssi", $full_name, $phone_number, $hashed_password, $user_id);
    } else {
        // SCENARIO 2: Only updating name and phone
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone_number = ? WHERE id = ?");
        $stmt->bind_param("ssi", $full_name, $phone_number, $user_id);
    }

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully.']);
        } else {
            http_response_code(200); // Not an error if nothing changed
            echo json_encode(['status' => 'success', 'message' => 'No changes were made to the profile.']);
        }
    } else {
        // Check for duplicate phone number error
        if ($conn->errno == 1062) {
             http_response_code(409); // Conflict
             echo json_encode(['status' => 'error', 'message' => 'This phone number is already taken by another account.']);
        } else {
             http_response_code(500);
             echo json_encode(['status' => 'error', 'message' => 'Failed to update profile.']);
        }
    }
    
    $stmt->close();
    $conn->close();

} catch (Exception $e) { /* ... error handling ... */ }

exit();
?>