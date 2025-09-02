<?php
/**
 * API Endpoint: Fetch Active Disaster Alerts
 * 
 * Retrieves all disasters with a status of 'Prepare' or 'Evacuate'.
 * Method: GET
 */

// Set common headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Allow access from any origin

// Include database connection
require_once '../../config/db_connect.php';

// --- Function for response handling ---
function send_response($status, $message, $data = null) {
    http_response_code($status);
    $response = ['status' => $status < 400 ? 'success' : 'error', 'message' => $message];
    // Only add the 'data' key if data is not null
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit();
}

// --- Helper function to get location name from coordinates ---
function getLocationFromCoordinates($geometryText) {
    // Extract coordinates from POLYGON or POINT geometry
    // For now, return a placeholder - you can implement reverse geocoding here
    if (strpos($geometryText, 'POLYGON') !== false) {
        // Extract first coordinate pair from polygon
        preg_match('/POLYGON\(\(([^)]+)\)\)/', $geometryText, $matches);
        if (isset($matches[1])) {
            $coords = explode(',', $matches[1]);
            $firstCoord = trim($coords[0]);
            $latLng = explode(' ', $firstCoord);
            // For demo purposes, return sample locations based on coordinates
            return "Tassia - Embakasi, Nairobi"; // You can implement actual reverse geocoding here
        }
    }
    return "Location unavailable";
}

// --- Helper function to format relative time ---
function getRelativeTime($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'Just now';
    if ($time < 3600) return floor($time/60) . ' minutes ago';
    if ($time < 86400) return floor($time/3600) . ' hours ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    if ($time < 31536000) return floor($time/2592000) . ' months ago';
    return floor($time/31536000) . ' years ago';
}

// --- Main Logic ---

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    send_response(405, 'Method Not Allowed. Please use GET.');
}

$query = "SELECT 
            id, 
            name, 
            type, 
            status,
            ST_AsText(affected_area_geometry) as affected_area, 
            created_at 
          FROM disasters 
          WHERE status = 'Prepare' OR status = 'Evacuate'
          ORDER BY created_at DESC";

$result = $conn->query($query);

if ($result) {
    $alerts = [];
    while ($row = $result->fetch_assoc()) {
        // Add location and relative time to each alert
        $row['location'] = getLocationFromCoordinates($row['affected_area']);
        $row['relative_time'] = getRelativeTime($row['created_at']);
        $alerts[] = $row;
    }
    
    send_response(200, 'Active disaster alerts fetched successfully.', $alerts);

} else {
    send_response(500, 'Internal Server Error. Could not fetch alerts.');
}

$conn->close();
?>