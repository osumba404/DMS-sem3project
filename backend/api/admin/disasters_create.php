<?php
/**
 * Form Processing Script for Creating a Disaster
 *
 * Receives data from a standard HTML POST form.
 * Inserts data into the database and redirects the user back to the disasters page.
 */

// We must start the session to check the logged-in admin's ID.
session_start();

// Force PHP to display any errors for easy debugging.
ini_set('display_errors', 1);
error_reporting(E_ALL);

// The path to the db_connect file from this script's location
require_once '../../../config/db_connect.php'; 

// --- Main Logic ---

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Access Denied. Please submit the form.');
}

// Get data directly from the $_POST superglobal array.
$admin_id = $_POST['admin_id'] ?? null;
$name = $_POST['name'] ?? null;
$type = $_POST['type'] ?? null;
$wkt = $_POST['affected_area_wkt'] ?? null;

// Simple validation.
if (empty($admin_id) || empty($name) || empty($type) || empty($wkt)) {
    // Redirect back with an error message.
    header("Location: ../../admin_portal/index.php?page=disasters&status=error&msg=missingfields");
    exit();
}

// Security check: Ensure the submitted admin_id matches the logged-in admin.
if ($admin_id != $_SESSION['admin_id']) {
    exit('Authorization Error.');
}

// The SQL INSERT statement.
$sql = "INSERT INTO disasters (name, type, status, affected_area_geometry, created_by_admin_id) 
        VALUES (?, ?, 'Prepare', ST_GeomFromText(?), ?)";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    exit("SQL Error: " . $conn->error);
}

$stmt->bind_param("sssi", $name, $type, $wkt, $admin_id);

// --- THE CHANGE IS HERE: The Redirect Logic ---
if ($stmt->execute()) {
    // SUCCESS: Redirect back to the disasters page with a success flag.
    // This will cause the page to reload, showing the new disaster in the list.
    header("Location: ../../admin_portal/index.php?page=disasters&status=success");
} else {
    // FAILURE: Redirect back with an error flag.
    // You can also include the specific error for debugging.
    $errorMessage = urlencode($stmt->error);
    header("Location: ../../admin_portal/index.php?page=disasters&status=error&msg=" . $errorMessage);
}

$stmt->close();
$conn->close();
exit(); // Ensure no other code runs after the redirect.
?>