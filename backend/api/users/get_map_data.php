<?php
/**
 * API Endpoint: Get Unified Map Data (Shelters + Disasters)
 * 
 * Fetches all open shelters near a location with detailed information
 * and all active disasters with their affected areas.
 * 
 * Method: GET
 * Required GET parameters:
 * - lat: Latitude of the center point
 * - lon: Longitude of the center point
 * 
 * Optional parameters:
 * - radius: Search radius in kilometers (default: 50)
 */

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set headers for CORS and JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../config/db_connect.php';

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Use GET.']);
    exit();
}

// Get and validate parameters
$latitude = filter_input(INPUT_GET, 'lat', FILTER_VALIDATE_FLOAT);
$longitude = filter_input(INPUT_GET, 'lon', FILTER_VALIDATE_FLOAT);
$radius = filter_input(INPUT_GET, 'radius', FILTER_VALIDATE_FLOAT, [
    'options' => ['default' => 50, 'min_range' => 1, 'max_range' => 500]
]);

if ($latitude === false || $longitude === false) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Valid lat and lon parameters are required.']);
    exit();
}

try {
    // --- Query 1: Get Nearby Shelters with Detailed Information ---
    $sql_shelters = "
        SELECT 
            s.id, 
            s.name, 
            s.capacity,
            s.current_occupancy,
            s.available_supplies,
            s.status,
            ST_X(s.location) as latitude, 
            ST_Y(s.location) as longitude,
            (6371 * acos( 
                cos(radians(?)) *
                cos(radians(ST_Y(s.location))) *
                cos(radians(ST_X(s.location)) - radians(?)) +
                sin(radians(?)) *
                sin(radians(ST_Y(s.location)))
            )) AS distance_km
        FROM shelters s
        WHERE s.status = 'Open'
        HAVING distance_km < ?
        ORDER BY distance_km
        LIMIT 100
    ";
    
    $stmt_shelters = $conn->prepare($sql_shelters);
    $stmt_shelters->bind_param("dddd", $latitude, $longitude, $latitude, $radius);
    $stmt_shelters->execute();
    $shelters = $stmt_shelters->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_shelters->close();
    
    // Format shelter data
    foreach ($shelters as &$shelter) {
        // Parse available_supplies JSON
        $supplies = json_decode($shelter['available_supplies'], true) ?? [];
        $shelter['food_supply'] = $supplies['food'] ?? 0;
        $shelter['water_supply'] = $supplies['water'] ?? 0;
        $shelter['medical_supply'] = $supplies['medical'] ?? 0;
        $shelter['blankets_available'] = $supplies['blankets'] ?? 0;
        
        // Calculate availability percentage
        $capacity = (int)$shelter['capacity'];
        $occupancy = (int)$shelter['current_occupancy'];
        $shelter['availability_percentage'] = $capacity > 0 ? 
            max(0, min(100, (($capacity - $occupancy) / $capacity) * 100)) : 0;
            
        // Add human-readable status
        $shelter['status_label'] = ucfirst(strtolower($shelter['status']));
        
        // Format distance to 2 decimal places
        $shelter['distance_km'] = round((float)$shelter['distance_km'], 2);
    }
    
    // --- Query 2: Get Active Disasters with Affected Areas ---
    $stmt_disasters = $conn->prepare(
        "SELECT 
            id, 
            name, 
            type, 
            status,
            created_at,
            ST_AsGeoJSON(affected_area_geometry) as affected_area_geojson
        FROM disasters
        WHERE status IN ('Prepare', 'Evacuate')
        AND (created_at > DATE_SUB(NOW(), INTERVAL 7 DAY))"
    );
    
    $stmt_disasters->execute();
    $disasters = $stmt_disasters->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_disasters->close();
    
    // Format disaster data
    foreach ($disasters as &$disaster) {
        if (!empty($disaster['affected_area_geojson'])) {
            $disaster['affected_area'] = json_decode($disaster['affected_area_geojson'], true);
        }
        unset($disaster['affected_area_geojson']);
        
        // Add human-readable status
        $disaster['status_label'] = ucfirst(strtolower($disaster['status']));
        
        // Add a default description based on disaster type and status
        $disaster['description'] = "{$disaster['type']} - Current status: {$disaster['status']}";
        
        // Format the date for display
        $disaster['start_date'] = date('Y-m-d H:i:s', strtotime($disaster['created_at']));
    }

    // --- Prepare final response ---
    $response = [
        'status' => 'success',
        'message' => '',
        'data' => [
            'shelters' => array_map(function($shelter) {
                return [
                    'id' => $shelter['id'],
                    'name' => $shelter['name'],
                    'address' => '', // Not in our DB, but required by app
                    'phone' => '',   // Not in our DB, but required by app
                    'latitude' => $shelter['latitude'],
                    'longitude' => $shelter['longitude'],
                    'capacity' => (int)$shelter['capacity'],
                    'current_occupancy' => (int)$shelter['current_occupancy'],
                    'food_supply' => (string)($shelter['food_supply'] ?? '0'),
                    'water_supply' => (string)($shelter['water_supply'] ?? '0'),
                    'medical_supply' => (string)($shelter['medical_supply'] ?? '0'),
                    'blankets_available' => (int)($shelter['blankets_available'] ?? 0),
                    'status' => $shelter['status'],
                    'status_label' => $shelter['status_label'],
                    'distance_km' => (float)$shelter['distance_km'],
                    'availability_percentage' => (float)$shelter['availability_percentage']
                ];
            }, $shelters),
            'disasters' => array_map(function($disaster) {
                return [
                    'id' => $disaster['id'],
                    'name' => $disaster['name'],
                    'type' => $disaster['type'],
                    'status' => $disaster['status'],
                    'affected_area' => json_encode($disaster['affected_area'] ?? []), // Convert to JSON string
                    'created_at' => $disaster['created_at'],
                    'location' => $disaster['name'] // Using name as location as a fallback
                ];
            }, $disasters)
        ]
    ];

    http_response_code(200);
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

} catch (Exception $e) {
    error_log('Map Data Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while fetching map data.',
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}

$conn->close();
?>