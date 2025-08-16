<?php
// Start the session on every page.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the admin_id session variable is not set.
// If it's not, the user is not logged in.
if (!isset($_SESSION['admin_id'])) {
    // Redirect them to the login page.
    header("Location: login.php");
    exit();
}
?>