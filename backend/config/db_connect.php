<?php
/**
 * Database Connection Handler
 * 
 * This script connects to the MySQL database and provides a connection
 * object for use by other scripts.
 */

// --- Database Configuration ---
define('DB_HOST', 'localhost');    // Your database host (e.g., 'localhost' or an IP)
define('DB_USER', 'root'); // Your database username
define('DB_PASS', ''); // Your database password
define('DB_NAME', 'disaster_management_system'); // The name of your database

// --- Create Connection ---
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// --- Check Connection ---
if ($conn->connect_error) {
    // If connection fails, stop the script and report the error.
    header('Content-Type: application/json');
    http_response_code(500); // Internal Server Error
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]);
    exit();
}

// Set the character set to utf8mb4 for full Unicode support
$conn->set_charset("utf8mb4");

// Note: We don't close the connection here. 
// It will be closed automatically when the script that includes it finishes execution.
?>