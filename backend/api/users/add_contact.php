<?php
/**
 * API Endpoint: Add Emergency Contact
 * 
 * Adds a new emergency contact for a specified user.
 * Method: POST
 * 
 * Required POST parameters (as JSON):
 * - user_id
 * - name
 * - phone_number
 * - relationship (optional)
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

$user_id = $data['user_id'] ?? null;
$name = $data['name'] ?? null;
$phone_number = $data['phone_number'] ?? null;
$relationship = $data['relationship'] ?? ''; // Optional field

// 2. Validate the input
if (empty($user_id) || empty($name) || empty($phone_number)) {
    send_response(400, 'Bad Request. user_id, name, and phone_number are required.');
}

if (!is_numeric($user_id)) {
    send_response(400, 'Bad Request. user_id must be a numeric value.');
}

// 3. Prepare and execute the INSERT statement
$stmt = $conn->prepare(
    "INSERT INTO emergency_contacts (user_id, name, phone_number, relationship) VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("isss", $user_id, $name, $phone_number, $relationship);

if ($stmt->execute()) {
    $contact_id = $stmt->insert_id;
    send_response(201, 'Emergency contact added successfully.', ['contact_id' => $contact_id]);
} else {
    // Check for specific errors, like a non-existent user_id
    if ($conn->errno == 1452) { // Foreign key constraint fails
        send_response(404, 'User not found.');
    }
    send_response(500, 'Internal Server Error. Failed to add contact.');
}

$stmt->close();
$conn->close();
?>```
**How It Works:**
*   Receives `user_id`, `name`, and `phone_number` in a JSON `POST` request.
*   Performs basic validation to ensure required data is present.
*   Inserts the new contact into the `emergency_contacts` table, linking it to the `user_id`.
*   Returns the ID of the newly created contact.

---

### 5. Endpoint to Get a User's Emergency Contacts

This endpoint fetches the list of all emergency contacts for a specific user.

**File Location:** `backend/api/users/get_contacts.php`

```php
