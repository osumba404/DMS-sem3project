<?php
/**
 * API Endpoint: Get Responder's Assignments
 * 
 * Fetches all active assignments for a specific responder, joining
 * with disaster and shelter tables to provide comprehensive details.
 * Method: GET
 * 
 * Required GET parameters:
 * - responder_id
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

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

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    send_response(405, 'Method Not Allowed. Please use GET.');
}

// 1. Get and validate responder_id from the query string
$responder_id = $_GET['responder_id'] ?? null;

if (empty($responder_id)) {
    send_response(400, 'Bad Request. responder_id is a required parameter.');
}
if (!is_numeric($responder_id)) {
    send_response(400, 'Bad Request. responder_id must be a numeric value.');
}

// 2. Prepare the SQL query
// We use LEFT JOIN because an assignment might be to a disaster, a shelter, or both.
// We also filter to show only assignments related to active disasters.
$stmt = $conn->prepare(
    "SELECT 
        ra.id AS assignment_id,
        ra.assignment_details,
        ra.assigned_at,
        d.id AS disaster_id,
        d.name AS disaster_name,
        d.type AS disaster_type,
        d.status AS disaster_status,
        s.id AS shelter_id,
        s.name AS shelter_name,
        s.status AS shelter_status,
        ST_X(s.location) AS shelter_latitude,
        ST_Y(s.location) AS shelter_longitude
    FROM 
        responder_assignments AS ra
    LEFT JOIN 
        disasters AS d ON ra.disaster_id = d.id
    LEFT JOIN 
        shelters AS s ON ra.shelter_id = s.id
    WHERE 
        ra.responder_id = ?
    AND 
        (d.status IS NULL OR d.status IN ('Prepare', 'Evacuate'))
    ORDER BY 
        ra.assigned_at DESC"
);

$stmt->bind_param("i", $responder_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    $assignments = [];
    while ($row = $result->fetch_assoc()) {
        $assignments[] = $row;
    }
    
    // 3. Send the response
    send_response(200, 'Assignments fetched successfully.', $assignments);
} else {
    send_response(500, 'Internal Server Error. Failed to fetch assignments.');
}

$stmt->close();
$conn->close();
?>