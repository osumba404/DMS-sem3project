<?php
/**
 * API Endpoint: Mark User as Safe and Notify Contacts
 * Method: POST
 * Body: JSON with user_id, latitude, longitude
 */

header('Content-Type: application/json');
require_once '../../config/db_connect.php';

// --- Main Logic ---

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed.']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$user_id = $data['user_id'] ?? null;
$latitude = $data['latitude'] ?? null;
$longitude = $data['longitude'] ?? null;

if (empty($user_id) || !is_numeric($user_id) || $latitude === null || $longitude === null) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Bad Request: user_id and location are required.']);
    exit();
}

// Start a transaction
$conn->begin_transaction();

try {
    // --- Step 1: Update the user's own status and location ---
    $stmt_user_update = $conn->prepare("UPDATE users SET is_safe = TRUE, last_known_latitude = ?, last_known_longitude = ? WHERE id = ?");
    $stmt_user_update->bind_param("ddi", $latitude, $longitude, $user_id);
    $stmt_user_update->execute();
    $stmt_user_update->close();

    // Get the user's full name to use in the notification message
    $stmt_user_name = $conn->prepare("SELECT full_name FROM users WHERE id = ?");
    $stmt_user_name->bind_param("i", $user_id);
    $stmt_user_name->execute();
    $user_name = $stmt_user_name->get_result()->fetch_assoc()['full_name'];
    $stmt_user_name->close();

    if (!$user_name) {
        throw new Exception("Could not find user name.");
    }
    
    // --- Step 2: Find all accepted emergency contacts for this user ---
    // We use the same UNION query from get_contacts.php to find all relationships.
    $sql_contacts = "
        (SELECT contact_user_id as contact_id FROM emergency_contacts WHERE user_id = ? AND status = 'accepted')
        UNION
        (SELECT user_id as contact_id FROM emergency_contacts WHERE contact_user_id = ? AND status = 'accepted')
    ";
    $stmt_contacts = $conn->prepare($sql_contacts);
    $stmt_contacts->bind_param("ii", $user_id, $user_id);
    $stmt_contacts->execute();
    $result_contacts = $stmt_contacts->get_result();
    $contacts = $result_contacts->fetch_all(MYSQLI_ASSOC);
    $stmt_contacts->close();

    // --- Step 3: Insert a notification for each contact ---
    $notification_title = "Safety Alert";
    $notification_message = "$user_name has marked themselves as safe.";

    $stmt_notify = $conn->prepare("INSERT INTO notifications (recipient_user_id, sender_user_id, title, message) VALUES (?, ?, ?, ?)");

    foreach ($contacts as $contact) {
        $recipient_id = $contact['contact_id'];
        $stmt_notify->bind_param("iiss", $recipient_id, $user_id, $notification_title, $notification_message);
        $stmt_notify->execute();
    }
    $stmt_notify->close();

    // If all steps succeeded, commit the transaction
    $conn->commit();

    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Status updated to safe. Contacts have been notified.']);

} catch (Exception $e) {
    // If any step failed, roll back all database changes
    $conn->rollback();
    
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal Server Error: ' . $e->getMessage()
    ]);
}

$conn->close();
exit();
?>