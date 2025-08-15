<?php
/**
 * API Endpoint: Admin - Create Disaster
 * 
 * Creates a new disaster event.
 * Method: POST
 * 
 * Required POST parameters (as JSON):
 * - admin_id (should be from a secure session in a real app)
 * - name (e.g., "Coastal Flood Event")
 * - type ('Flood', 'Earthquake', etc.)
 * - affected_area_wkt (A Well-Known Text string for the geometry, e.g., "POLYGON((...))")
 */

header('Content-Type: application/json');
require_once '../../config/db_connect.php';

function send_response($status, $message, $data = null) {
    http_response_code($status);
    $response = ['status' => $status < 400 ? 'success' : 'error', 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit();
}

// In a real application, you would verify the admin's session/token here to ensure they are authorized.

$data = json_decode(file_get_contents("php://input"), true);

$admin_id = $data['admin_id'] ?? null;
$name = $data['name'] ?? null;
$type = $data['type'] ?? null;
$wkt = $data['affected_area_wkt'] ?? null; // WKT = Well-Known Text

if (empty($admin_id) || empty($name) || empty($type) || empty($wkt)) {
    send_response(400, 'Bad Request. admin_id, name, type, and affected_area_wkt are required.');
}

// ST_GeomFromText() converts the WKT string into a proper GEOMETRY type for MySQL.
$sql = "INSERT INTO disasters (name, type, status, affected_area_geometry, created_by_admin_id) 
        VALUES (?, ?, 'Prepare', ST_GeomFromText(?), ?)";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $name, $type, $wkt, $admin_id);

if ($stmt->execute()) {
    $disaster_id = $stmt->insert_id;
    send_response(201, 'Disaster event created successfully.', ['disaster_id' => $disaster_id]);
} else {
    // Provide a more specific error if the WKT is invalid
    if (strpos($conn->error, 'Invalid GIS data') !== false) {
        send_response(400, 'Invalid Well-Known Text (WKT) format for the affected area.');
    }
    send_response(500, 'Internal Server Error. Failed to create disaster.');
}

$stmt->close();
$conn->close();
?>