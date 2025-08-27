<?php
/**
 * API Endpoint: Fetch Nearby Shelters
 */

// We will keep error reporting on for now to catch any other issues.
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set the header at the very top.
header('Content-Type: application/json');

require_once '../../config/db_connect.php';

// --- Main Logic ---

// We only proceed if the request is GET.
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed.']);
    exit();
}

$latitude = $_GET['lat'] ?? null;
$longitude = $_GET['lon'] ?? null;
$radius = $_GET['radius'] ?? 25;

// --- Validation ---
if ($latitude === null || $longitude === null) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Bad Request. Latitude (lat) and longitude (lon) are required.']);
    exit();
}
if (!is_numeric($latitude) || $latitude < -90 || $latitude > 90 || !is_numeric($longitude) || $longitude < -180 || $longitude > 180) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Bad Request. Invalid coordinates provided.']);
    exit();
}

// --- Database Query ---
try {
    $stmt = $conn->prepare(
        "SELECT
            id, name, ST_X(location) as latitude, ST_Y(location) as longitude,
            capacity, current_occupancy, available_supplies, status,
            (ST_Distance_Sphere(location, POINT(?, ?)) / 1000) AS distance_in_km
        FROM shelters
        WHERE status = 'Open'
        HAVING distance_in_km <= ?
        ORDER BY distance_in_km ASC"
    );

    $stmt->bind_param("ddd", $longitude, $latitude, $radius);
    $stmt->execute();
    $result = $stmt->get_result();

    $shelters = [];
    while ($row = $result->fetch_assoc()) {
        if (!empty($row['available_supplies'])) {
            $row['available_supplies'] = json_decode($row['available_supplies'], true);
        }
        $shelters[] = $row;
    }
    
    $stmt->close();
    $conn->close();

    // --- Success Response ---
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Nearby shelters fetched successfully.',
        'data' => $shelters
    ]);

} catch (Exception $e) {
    // --- Error Response ---
    // If anything in the 'try' block fails, this will catch the error.
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal Server Error: ' . $e->getMessage()
    ]);
}

exit();
?>