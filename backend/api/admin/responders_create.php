<?php
/**
 * Form Processing Script for Creating a Responder
 *
 * Receives data from a standard HTML POST form.
 * Hashes the password and inserts the new responder into the database.
 */

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
$full_name = $_POST['full_name'] ?? null;
$username = $_POST['username'] ?? null;
$password = $_POST['password'] ?? null;
$team = $_POST['team'] ?? null;

// 3. Simple validation.
if (empty($full_name) || empty($username) || empty($password) || empty($team)) {
    header("Location: ../../admin_portal/index.php");
    exit();
}

// 4. Check if the username is already taken to prevent duplicates.
$stmt = $conn->prepare("SELECT id FROM responders WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    header("Location: ../../admin_portal/index.php?page=responders&status=error&msg=username_exists");
    exit();
}
$stmt->close();

// 5. Hash the password for security. NEVER store plain-text passwords.
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// 6. The SQL INSERT statement.
$sql = "INSERT INTO responders (full_name, username, password, team) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    exit("SQL Error: " . $conn->error);
}

$stmt->bind_param("ssss", $full_name, $username, $hashed_password, $team);

// 7. Execute the query and redirect based on the result.
if ($stmt->execute()) {
    // SUCCESS: Redirect back to the responders page with a success flag.
    header("Location: ../../admin_portal/index.php?page=responders&status=success");
} else {
    // FAILURE: Redirect back with an error flag.
    header("Location: ../../admin_portal/index.php?page=responders&status=error&msg=dberror");
}

// 8. Close the statement and connection.
$stmt->close();
$conn->close();
exit();
?>