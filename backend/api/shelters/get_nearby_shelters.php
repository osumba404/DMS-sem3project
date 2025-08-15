<?php
/**
 * API Endpoint: Fetch Nearby Shelters
 * 
 * Retrieves open shelters near a given GPS coordinate, ordered by distance.
 * Method: GET
 * 
 * Required GET parameters:
 * - lat (user's latitude)
 * - lon (user's longitude)
 * Optional GET parameters:
 * - radius (search radius in kilometers, defaults to 25)
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

// 1. Get and validate latitude and longitude from the query string
$latitude = $_GET['lat'] ?? null;
$longitude = $_GET['lon'] ?? null;
$radius = $_GET['radius'] ?? 25; // Default search radius of 25 km

if ($latitude === null || $longitude === null) {
    send_response(400, 'Bad Request. Latitude (lat) and longitude (lon) are required parameters.');
}

if (!is_numeric($latitude) || !is_numeric($longitude) || !is_numeric($radius)) {
    send_response(400, 'Bad Request. Latitude, longitude, and radius must be numeric values.');
}


// 2. Prepare the geospatial query
// ST_Distance_Sphere calculates the distance in meters between two points on a sphere.
// We use it to find shelters within the specified radius.
$stmt = $conn->prepare(
    "SELECT
        id,
        name,
        ST_X(location) as latitude,  -- Extract latitude from POINT
        ST_Y(location) as longitude, -- Extract longitude from POINT
        capacity,
        current_occupancy,
        available_supplies,
        status,
        (ST_Distance_Sphere(location, POINT(?, ?)) / 1000) AS distance_in_km
    FROM shelters
    WHERE status = 'Open'
    HAVING distance_in_km <= ?
    ORDER BY distance_in_km ASC"
);

$stmt->bind_param("ddd", $longitude, $latitude, $radius);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    $shelters = [];
    while ($row = $result->fetch_assoc()) {
        // Decode the JSON string for supplies into a PHP array
        if ($row['available_supplies']) {
            $row['available_supplies'] = json_decode($row['available_supplies']);
        }
        $shelters[] = $row;
    }
    
    send_response(200, 'Nearby shelters fetched successfully.', $shelters);
} else {
    send_response(500, 'Internal Server Error. Failed to fetch shelters.');
}

$stmt->close();
$conn->close();
?>