<?php
// Start a session to store user data after login
session_start();

require_once '../../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Access Denied.');
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    header("Location: ../../admin_portal/login.php?error=empty");
    exit();
}

$stmt = $conn->prepare("SELECT id, full_name, password FROM admins WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();
    
    // Verify the submitted password against the hashed password from the database
    if (password_verify($password, $admin['password'])) {
        // Password is correct, login successful.
        // Store admin info in the session.
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['full_name'];
        
        // Redirect to the main admin dashboard
        header("Location: ../../admin_portal/index.php");
        exit();
    }
}

// If we reach here, login failed (wrong username or password).
header("Location: ../../admin_portal/login.php?error=invalid");
exit();
?>