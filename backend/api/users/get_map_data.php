<?php
/**
 * API Endpoint: Get Unified Map Data (Shelters + Disasters) - CORRECTED VERSION
 *
 * Fetches all open shelters near a location AND all active disasters.
 * Method: GET
 * Required GET parameters:
 * - lat, lon
 */

// Force error reporting for clear debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
require_once '../../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') { exit('Method Not Allowed.'); }

$latitude = $_GET['lat'] ?? null;
$longitude = $_GET['lon'] ?? null;
$radius = $_GET['radius'] ?? 50; // Search radius in km

if ($latitude === null || $longitude === null || !is_numeric($latitude) || !is_numeric($longitude)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Valid lat and lon are required.']);
    exit();
}

try {
    // --- Query 1: Get Nearby Shelters (Corrected SQL) ---
    // This query uses the Haversine formula concept to efficiently find points
    // within a certain distance. It's a standard and reliable way to do this.
    $sql_shelters = "
        SELECT id, name, ST_X(location) as latitude, ST_Y(location) as longitude, status,
               ( 6371 * acos( cos( radians(?) ) *
                 cos( radians( ST_Y(location) ) )
                 * cos( radians( ST_X(location) ) - radians(?)
                 ) + sin( radians(?) ) *
                 sin( radians( ST_Y(location) ) ) )
               ) AS distance
        FROM shelters
        WHERE status = 'Open'
        HAVING distance < ?
        ORDER BY distance
    ";
    
    $stmt_shelters = $conn->prepare($sql_shelters);
    // Bind parameters: lat, lon, lat, radius
    $stmt_shelters->bind_param("dddi", $latitude, $longitude, $latitude, $radius);
    $stmt_shelters->execute();
    $shelters = $stmt_shelters->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_shelters->close();
    
    
    // --- Query 2: Get ALL Active Disasters ---
    $stmt_disasters = $conn->prepare(
        "SELECT id, name, type, ST_AsText(affected_area_geometry) as affected_area
        FROM disasters
        WHERE status IN ('Prepare', 'Evacuate')"
    );
    $stmt_disasters->execute();
    $disasters = $stmt_disasters->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_disasters->close();

    // --- Combine into a single response object ---
    $map_data = [
        'shelters' => $shelters,
        'disasters' => $disasters
    ];

    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Map data retrieved successfully.',
        'data' => $map_data
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Server Error: ' . $e->getMessage()]);
}

$conn->close();
exit();
?>