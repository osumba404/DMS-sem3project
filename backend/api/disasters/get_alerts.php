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
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit();
}

// --- Main Logic ---

// 1. Ensure the request method is GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    send_response(405, 'Method Not Allowed. Please use GET.');
}

// 2. Prepare and execute the SQL query
// We select only the active disasters.
$query = "SELECT 
            id, 
            name, 
            type, 
            status,
            -- Use ST_AsText to convert the GEOMETRY type to a readable string format (e.g., 'POLYGON(...)')
            ST_AsText(affected_area_geometry) as affected_area, 
            created_at 
          FROM disasters 
          WHERE status = 'Prepare' OR status = 'Evacuate'
          ORDER BY created_at DESC";

$result = $conn->query($query);

if ($result) {
    $alerts = [];
    // Fetch all results into an associative array
    while ($row = $result->fetch_assoc()) {
        $alerts[] = $row;
    }
    
    // 3. Send the response
    //send_response(200, 'Active disaster alerts fetched successfully.', $alerts);
    // {"status":"success", "message":"...", "data": {"alerts": [...]}}
    $responseData = ['alerts' => $alerts];
    send_response(200, 'Active disaster alerts fetched successfully.', $responseData);
} else {
    // Handle potential query errors
    send_response(500, 'Internal Server Error. Could not fetch alerts.');
}

// Close the connection
$conn->close();
?>