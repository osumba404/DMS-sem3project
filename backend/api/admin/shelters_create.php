<?php
/**
 * Form Processing Script for Creating a Shelter (Updated)
 *
 * Receives data from a standard HTML POST form.
 * Assembles supply data into JSON.
 * Inserts data into the database and redirects the user.
 */

// Force PHP to display any errors for easy debugging.
ini_set('display_errors', 1);
error_reporting(E_ALL);

// The path to the db_connect file is three levels up from here.
require_once '../../config/db_connect.php'; 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Access Denied. Please submit the form.');
}

// 1. Get standard data directly from the $_POST array.
$name = $_POST['name'] ?? null;
$latitude = $_POST['latitude'] ?? null;
$longitude = $_POST['longitude'] ?? null;
$capacity = $_POST['capacity'] ?? null;

// 2. Get individual supply data.
$food_packs = $_POST['food_packs'] ?? 0;
$water_liters = $_POST['water_liters'] ?? 0;
$first_aid_kits = $_POST['first_aid_kits'] ?? 0;

// Validation
if (empty($name) || empty($latitude) || empty($longitude) || empty($capacity)) {
    header("Location: ../../admin_portal/index.php?page=shelters&status=error&msg=missingfields");
    exit();
}

// 3. --- THE KEY CHANGE: Assemble supplies into a PHP array ---
$supplies_array = [
    'food_packs' => (int)$food_packs,
    'water_liters' => (int)$water_liters,
    'first_aid_kits' => (int)$first_aid_kits
];

// 4. --- Convert the PHP array into a JSON string ---
$supplies_json = json_encode($supplies_array);

// 5. The SQL INSERT statement.
$sql = "INSERT INTO shelters (name, location, capacity, available_supplies, status) 
        VALUES (?, POINT(?, ?), ?, ?, 'Open')";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    exit("SQL Error: " . $conn->error);
}

// Bind parameters, including the new JSON string.
$stmt->bind_param("sddis", $name, $longitude, $latitude, $capacity, $supplies_json);

// 6. Execute and redirect.
if ($stmt->execute()) {
    header("Location: ../../admin_portal/index.php?page=shelters&status=success");
} else {
    header("Location: ../../admin_portal/index.php?page=shelters&status=error&msg=dberror");
}

$stmt->close();
$conn->close();
exit();
?>