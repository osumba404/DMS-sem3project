<?php
/**
 * Form Processing Script for Creating a Disaster
 *
 * Receives data from a standard HTML POST form.
 * Inserts data into the database and redirects the user.
 */

// This will force PHP to show us any errors instead of hiding them.
ini_set('display_errors', 1);
error_reporting(E_ALL);

// The path to the db_connect file from this script's location
require_once '../../config/db_connect.php'; 

// --- Main Logic ---

// 1. Check if the form was submitted using the POST method.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // If not, just exit.
    exit('Access Denied. Please submit the form.');
}

// 2. Get data directly from the $_POST superglobal array.
// This is the traditional way to get form data.
$admin_id = $_POST['admin_id'] ?? null;
$name = $_POST['name'] ?? null;
$type = $_POST['type'] ?? null;
$wkt = $_POST['affected_area_wkt'] ?? null;

// 3. Simple validation.
if (empty($admin_id) || empty($name) || empty($type) || empty($wkt)) {
    // Redirect back with an error message.
    header("Location: ../../admin_portal/index.php?page=disasters&status=error&msg=missingfields");
    exit();
}

// 4. The SQL INSERT statement (this is the same as before).
$sql = "INSERT INTO disasters (name, type, status, affected_area_geometry, created_by_admin_id) 
        VALUES (?, ?, 'Prepare', ST_GeomFromText(?), ?)";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    // If prepare() fails, there's an SQL syntax error.
    exit("SQL Error: " . $conn->error);
}

$stmt->bind_param("sssi", $name, $type, $wkt, $admin_id);

// 5. Execute the query and redirect based on the result.
if ($stmt->execute()) {
    // SUCCESS: Redirect back to the disasters page with a success flag.
    header("Location: ../../admin_portal/index.php?page=disasters&status=success");
} else {
    // FAILURE: Redirect back with an error flag.
    // You can also include the specific error for debugging: urlencode($stmt->error)
    header("Location: ../../admin_portal/index.php?page=disasters&status=error&msg=dberror");
}

// 6. Close the statement and connection.
$stmt->close();
$conn->close();
exit(); // Ensure no other code runs after the redirect.
?>