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
    // fetch_assoc() returns an associative array for each row
    while ($row = $result->fetch_assoc()) {
        // We explicitly add each row to our array.
        // This ensures $alerts is always a numerically indexed array.
        $alerts[] = $row;
    }
    
    // THE FIX: We pass the $alerts array directly as the data.
    // json_encode will now correctly convert an empty PHP array []
    // into an empty JSON array [], and a populated one into [{...}, {...}].
    send_response(200, 'Active disaster alerts fetched successfully.', $alerts);

} else {
    // Handle potential query errors
    send_response(500, 'Internal Server Error. Could not fetch alerts.');
}

$conn->close();
?>