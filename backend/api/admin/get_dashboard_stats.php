<?php
/**
 * API Endpoint: Admin - Get Dashboard Statistics
 * Method: GET
 */
header('Content-Type: application/json');
require_once '../../config/db_connect.php';

// Verify admin session...

$stats = [];

// 1. Total users
$result = $conn->query("SELECT COUNT(id) as total_users FROM users");
$stats['total_users'] = $result->fetch_assoc()['total_users'];

// 2. Active disasters
$result = $conn->query("SELECT COUNT(id) as active_disasters FROM disasters WHERE status IN ('Prepare', 'Evacuate')");
$stats['active_disasters'] = $result->fetch_assoc()['active_disasters'];

// 3. Open shelters
$result = $conn->query("SELECT COUNT(id) as open_shelters FROM shelters WHERE status = 'Open'");
$stats['open_shelters'] = $result->fetch_assoc()['open_shelters'];

// 4. Total current occupancy in shelters
$result = $conn->query("SELECT SUM(current_occupancy) as total_occupancy FROM shelters");
$stats['total_occupancy'] = (int) $result->fetch_assoc()['total_occupancy']; // Cast to int, SUM can be NULL

// 5. Users who marked as safe today
$result = $conn->query("SELECT COUNT(id) as safe_today FROM users WHERE is_safe = TRUE AND updated_at >= CURDATE()");
// Note: This query assumes you add an 'updated_at' column to the 'users' table.
// ALTER TABLE `users` ADD `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
$stats['safe_today'] = $result->fetch_assoc()['safe_today'] ?? 0; // Fallback if column doesn't exist yet

// 6. Total responders
$result = $conn->query("SELECT COUNT(id) as total_responders FROM responders");
$stats['total_responders'] = $result->fetch_assoc()['total_responders'];

http_response_code(200);
echo json_encode(['status' => 'success', 'data' => $stats]);

$conn->close();
?>