<?php
/**
 * One-Time Admin Creation Script
 *
 * This script will create your first administrator account.
 * After running it successfully, you should DELETE this file from your server.
 */

// --- Step 1: Configure Your New Admin's Details ---
$admin_username = 'myadmin';
$admin_password = 'pass'; // Choose a strong password
$admin_full_name = 'DMS Administrator';
$admin_role = 'SuperAdmin'; // You can define different roles later if you wish

// --- Step 2: Include the Database Connection ---
// The path needs to be relative to this script's location.
require_once 'backend/config/db_connect.php';

echo "Script started...\n";

// --- Step 3: Check if the Admin Already Exists ---
try {
    $stmt_check = $conn->prepare("SELECT id FROM admins WHERE username = ?");
    if ($stmt_check === false) {
        die("Error preparing statement (check): " . $conn->error);
    }
    $stmt_check->bind_param("s", $admin_username);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        die("Error: An admin with the username '{$admin_username}' already exists. Please choose a different username or delete the existing one.\n");
    }
    echo "Username '{$admin_username}' is available. Proceeding...\n";
    $stmt_check->close();

    // --- Step 4: Securely Hash the Password ---
    // PASSWORD_BCRYPT is the current standard for secure password hashing.
    $hashed_password = password_hash($admin_password, PASSWORD_BCRYPT);
    if ($hashed_password === false) {
        die("Error: Could not hash the password.\n");
    }
    echo "Password hashed successfully.\n";

    // --- Step 5: Insert the New Admin into the Database ---
    $sql = "INSERT INTO admins (username, password, full_name, role) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql);
    if ($stmt_insert === false) {
        die("Error preparing statement (insert): " . $conn->error);
    }
    $stmt_insert->bind_param("ssss", $admin_username, $hashed_password, $admin_full_name, $admin_role);

    if ($stmt_insert->execute()) {
        echo "SUCCESS: Admin '{$admin_username}' was created successfully!\n\n";
        echo "You can now log in to the admin portal with:\n";
        echo "Username: " . $admin_username . "\n";
        echo "Password: " . $admin_password . "\n\n";
        echo "IMPORTANT: Please DELETE this file (create_admin.php) from your server now.\n";
    } else {
        echo "Error: Could not execute the insert statement. Reason: " . $stmt_insert->error . "\n";
    }
    
    $stmt_insert->close();
    $conn->close();

} catch (Exception $e) {
    die("An exception occurred: " . $e->getMessage() . "\n");
}
?>