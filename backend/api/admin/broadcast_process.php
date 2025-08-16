<?php
/**
 * Form Processing Script for Sending a Broadcast Message
 *
 * Receives data from a standard HTML POST form.
 * Inserts the message into the database and redirects the user.
 */

// We need to access session variables to get the admin_id.
session_start();

// Force PHP to display any errors for easy debugging.
ini_set('display_errors', 1);
error_reporting(E_ALL);

// The path to the db_connect file is three levels up from here.
require_once '../../config/db_connect.php'; 

// --- Main Logic ---

// 1. Check if the form was submitted using the POST method.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Access Denied. Please submit the form.');
}

// 2. Get data directly from the $_POST superglobal array.
$admin_id = $_POST['admin_id'] ?? null;
$title = $_POST['title'] ?? null;
$body = $_POST['body'] ?? null;
$target_audience = $_POST['target_audience'] ?? null;

// 3. Simple validation.
if (empty($admin_id) || empty($title) || empty($body) || empty($target_audience)) {
    header("Location: ../../admin_portal/index.php?page=broadcast&status=error&msg=missingfields");
    exit();
}

// Security check: Make sure the admin_id from the form matches the one in the session.
if ($admin_id != $_SESSION['admin_id']) {
    exit('Authorization Error.');
}

// 4. The SQL INSERT statement.
$sql = "INSERT INTO broadcast_messages (admin_id, title, body, target_audience) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    exit("SQL Error: " . $conn->error);
}

$stmt->bind_param("isss", $admin_id, $title, $body, $target_audience);

// 5. Execute the query and redirect based on the result.
if ($stmt->execute()) {
    // SUCCESS: Redirect back to the broadcast page with a success flag.
    // In a real system, this is where you would also trigger the push notification service.
    header("Location: ../../admin_portal/index.php?page=broadcast&status=success");
} else {
    // FAILURE: Redirect back with an error flag.
    header("Location: ../../admin_portal/index.php?page=broadcast&status=error&msg=dberror");
}

// 6. Close the statement and connection.
$stmt->close();
$conn->close();
exit();
?>