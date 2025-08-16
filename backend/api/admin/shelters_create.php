<?php
/**
 * Form Processing Script for Creating a Shelter
 *
 * Receives data from a standard HTML POST form.
 * Inserts data into the database and redirects the user.
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
$name = $_POST['name'] ?? null;
$latitude = $_POST['latitude'] ?? null;
$longitude = $_POST['longitude'] ?? null;
$capacity = $_POST['capacity'] ?? null;
$supplies_json = $_POST['available_supplies'] ?? null;

// 3. Simple validation.
if (empty($name) || empty($latitude) || empty($longitude) || empty($capacity)) {
    // Redirect back to the shelters page with an error message.
    header("Location: ../../admin_portal/index.php?page=shelters&status=error&msg=missingfields");
    exit();
}

// Validate that the supplies field contains valid JSON.
json_decode($supplies_json);
if (json_last_error() !== JSON_ERROR_NONE) {
    header("Location: ../../admin_portal/index.php?page=shelters&status=error&msg=invalidjson");
    exit();
}

// 4. The SQL INSERT statement.
// We use the POINT() function to convert latitude and longitude into a GEOMETRY data type.
$sql = "INSERT INTO shelters (name, location, capacity, available_supplies, status) 
        VALUES (?, POINT(?, ?), ?, ?, 'Open')";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    // If prepare() fails, there's likely an SQL syntax error.
    exit("SQL Error: " . $conn->error);
}

// Note: MySQL's POINT() function takes longitude first, then latitude.
$stmt->bind_param("sddis", $name, $longitude, $latitude, $capacity, $supplies_json);

// 5. Execute the query and redirect based on the result.
if ($stmt->execute()) {
    // SUCCESS: Redirect back to the shelters page with a success flag.
    header("Location: ../../admin_portal/index.php?page=shelters&status=success");
} else {
    // FAILURE: Redirect back with an error flag.
    // For debugging, you can output the error: exit("Database Error: " . $stmt->error);
    header("Location: ../../admin_portal/index.php?page=shelters&status=error&msg=dberror");
}

// 6. Close the statement and connection.
$stmt->close();
$conn->close();
exit();
?>