<?php
/**
 * Admin Login Processing Script
 *
 * Handles authentication for the admin portal.
 * Verifies credentials, starts a session, and redirects.
 */

// We must start the session to store login state.
// This should be at the very top of the script.
session_start();

// The path is relative to this file's location.
require_once '../../config/db_connect.php';

// --- Main Logic ---

// 1. Check if the form was submitted.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // If someone tries to access this URL directly, deny them.
    exit('Access Denied. Please use the login form.');
}

// 2. Get username and password from the form submission.
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// 3. Basic validation.
if (empty($username) || empty($password)) {
    // If fields are empty, redirect back to login with an error flag.
    header("Location: ../../admin_portal/login.php?error=empty");
    exit();
}

try {
    // 4. Find the admin in the database by their username.
    $stmt = $conn->prepare("SELECT id, full_name, password FROM admins WHERE username = ? LIMIT 1");
    if ($stmt === false) {
        throw new Exception("SQL prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // --- User Found ---
        $admin = $result->fetch_assoc();
        
        // 5. Securely verify the password.
        // password_verify() compares the plain-text password from the form
        // with the secure hash stored in the database.
        if (password_verify($password, $admin['password'])) {
            // --- SUCCESSFUL LOGIN ---
            
            // 6. Regenerate session ID for security.
            session_regenerate_id(true);

            // 7. Store admin information in the session.
            // This is how the app "remembers" who is logged in.
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['full_name'];
            
            // 8. Redirect to the main admin dashboard.
            header("Location: ../../../admin_portal/index.php");
            exit();
        }
    }

    // --- FAILED LOGIN ---
    // If we reach this point, it means either the user was not found
    // or the password was incorrect. For security, we give a generic error.
    header("Location: ../../admin_portal/login.php?error=invalid");
    exit();

} catch (Exception $e) {
    // Redirect on any other critical error.
    header("Location: ../../admin_portal/login.php?error=dberror");
    exit();
}
?>