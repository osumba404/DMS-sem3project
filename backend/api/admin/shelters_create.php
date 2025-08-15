<?php
/**
 * API Endpoint: Admin - Create Shelter
 * 
 * Adds a new shelter to the database.
 * Method: POST
 * 
 * Required POST parameters (as JSON):
 * - name
 * - latitude
 * - longitude
 * - capacity
 * - available_supplies (JSON object, e.g., {"food_packs": 100, "water_liters": 500})
 */

header('Content-Type: application/json');
require_once '../../config/db_connect.php';

function send_response($status, $message, $data = null) {
    http_response_code($status);
    $response = ['status' => $status < 400 ? 'success' : 'error', 'message' => $message];
    if ($data !== null) $response['data'] = $data;
    echo json_encode($response);
    exit();
}

// Admin authorization should be verified here in a real application.

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_response(405, 'Method Not Allowed.');
}

$data = json_decode(file_get_contents("php://input"), true);

$name = $data['name'] ?? null;
$latitude = $data['latitude'] ?? null;
$longitude = $data['longitude'] ?? null;
$capacity = $data['capacity'] ?? null;
$supplies = $data['available_supplies'] ?? null;

if (empty($name) || $latitude === null || $longitude === null || empty($capacity)) {
    send_response(400, 'Bad Request. Name, latitude, longitude, and capacity are required.');
}

$supplies_json = json_encode($supplies);

// Use POINT() to create the geometry data from latitude and longitude
$sql = "INSERT INTO shelters (name, location, capacity, available_supplies, status) 
        VALUES (?, POINT(?, ?), ?, ?, 'Open')";
        
$stmt = $conn->prepare($sql);
// Note the order: longitude first for POINT(), then latitude
$stmt->bind_param("sddis", $name, $longitude, $latitude, $capacity, $supplies_json);

if ($stmt->execute()) {
    $shelter_id = $stmt->insert_id;
    send_response(201, 'Shelter created successfully.', ['shelter_id' => $shelter_id]);
} else {
    send_response(500, 'Internal Server Error. Failed to create shelter.');
}

$stmt->close();
$conn->close();
?>