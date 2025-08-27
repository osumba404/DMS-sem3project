<?php
// ... (headers, require_once, etc)
header('Content-Type: application/json');
require_once '../../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { /* ... error ... */ }

// Data comes from @FormUrlEncoded in the Android app
$user_id = $_POST['user_id'] ?? 0; // The person sending the request (e.g., Alice)
$contact_user_id = $_POST['contact_user_id'] ?? 0; // The person being added (e.g., Bob)
$relationship = $_POST['relationship'] ?? '';

if (empty($user_id) || empty($contact_user_id) || empty($relationship)) {
    // ... handle bad request ...
}

// Check if a request already exists
$stmt_check = $conn->prepare("SELECT id FROM emergency_contacts WHERE (user_id = ? AND contact_user_id = ?) OR (user_id = ? AND contact_user_id = ?)");
$stmt_check->bind_param("iiii", $user_id, $contact_user_id, $contact_user_id, $user_id);
$stmt_check->execute();
if ($stmt_check->get_result()->num_rows > 0) {
    http_response_code(409); // Conflict
    echo json_encode(['status' => 'error', 'message' => 'A contact or pending request already exists between these users.']);
    exit();
}
$stmt_check->close();


// Insert the new pending request
$stmt = $conn->prepare("INSERT INTO emergency_contacts (user_id, contact_user_id, relationship, status) VALUES (?, ?, ?, 'pending')");
$stmt->bind_param("iis", $user_id, $contact_user_id, $relationship);

if ($stmt->execute()) {
    http_response_code(201);
    echo json_encode(['status' => 'success', 'message' => 'Contact request sent successfully.']);
} else {
    // ... handle server error ...
}

$stmt->close();
$conn->close();
?>