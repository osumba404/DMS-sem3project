<?php
/**
 * Coordinate Testing Script
 * Tests API endpoints to verify coordinate fixes are working correctly
 */

echo "<h1>DMS Coordinate System Test Results</h1>\n";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
    .pass { color: green; font-weight: bold; }
    .fail { color: red; font-weight: bold; }
    .info { color: blue; }
    pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
</style>";

// Test 1: Shelters API
echo "<div class='test-section'>";
echo "<h2>Test 1: Shelters API (shelters_get_all.php)</h2>";

$shelters_url = "http://localhost/DMS-sem3project/backend/api/admin/shelters_get_all.php";
$shelters_response = @file_get_contents($shelters_url);

if ($shelters_response === FALSE) {
    echo "<p class='fail'>❌ FAIL: Could not connect to shelters API</p>";
} else {
    $shelters_data = json_decode($shelters_response, true);
    
    if ($shelters_data && $shelters_data['status'] === 'success') {
        echo "<p class='pass'>✅ PASS: Shelters API responding successfully</p>";
        
        if (!empty($shelters_data['data'])) {
            $shelter_count = count($shelters_data['data']);
            echo "<p class='info'>📊 Found {$shelter_count} shelters</p>";
            
            // Check first shelter for coordinate format
            $first_shelter = $shelters_data['data'][0];
            if (isset($first_shelter['latitude']) && isset($first_shelter['longitude'])) {
                $lat = $first_shelter['latitude'];
                $lng = $first_shelter['longitude'];
                
                // Basic coordinate validation
                if (is_numeric($lat) && is_numeric($lng) && 
                    $lat >= -90 && $lat <= 90 && 
                    $lng >= -180 && $lng <= 180) {
                    echo "<p class='pass'>✅ PASS: Coordinates are valid numbers</p>";
                    echo "<p class='info'>📍 Sample coordinates: Lat={$lat}, Lng={$lng}</p>";
                } else {
                    echo "<p class='fail'>❌ FAIL: Invalid coordinate values</p>";
                }
            } else {
                echo "<p class='fail'>❌ FAIL: Missing latitude/longitude fields</p>";
            }
        } else {
            echo "<p class='info'>ℹ️ No shelters found in database</p>";
        }
        
        echo "<h3>Raw Response Sample:</h3>";
        echo "<pre>" . htmlspecialchars(substr($shelters_response, 0, 500)) . "...</pre>";
    } else {
        echo "<p class='fail'>❌ FAIL: Invalid API response format</p>";
    }
}
echo "</div>";

// Test 2: Mobile App Map Data API
echo "<div class='test-section'>";
echo "<h2>Test 2: Mobile App Map Data API (get_map_data.php)</h2>";

// Use Nairobi coordinates as test location
$test_lat = -1.286389;
$test_lng = 36.817223;
$map_data_url = "http://localhost/DMS-sem3project/backend/api/users/get_map_data.php?latitude={$test_lat}&longitude={$test_lng}";
$map_response = @file_get_contents($map_data_url);

if ($map_response === FALSE) {
    echo "<p class='fail'>❌ FAIL: Could not connect to map data API</p>";
} else {
    $map_data = json_decode($map_response, true);
    
    if ($map_data && $map_data['status'] === 'success') {
        echo "<p class='pass'>✅ PASS: Map data API responding successfully</p>";
        
        $shelters = $map_data['data']['shelters'] ?? [];
        $disasters = $map_data['data']['disasters'] ?? [];
        
        echo "<p class='info'>📊 Found " . count($shelters) . " shelters and " . count($disasters) . " disasters</p>";
        
        // Test shelter coordinates
        if (!empty($shelters)) {
            $shelter = $shelters[0];
            if (isset($shelter['latitude']) && isset($shelter['longitude'])) {
                $lat = $shelter['latitude'];
                $lng = $shelter['longitude'];
                
                if (is_numeric($lat) && is_numeric($lng)) {
                    echo "<p class='pass'>✅ PASS: Shelter coordinates are numeric</p>";
                    echo "<p class='info'>📍 Sample shelter: Lat={$lat}, Lng={$lng}</p>";
                } else {
                    echo "<p class='fail'>❌ FAIL: Shelter coordinates are not numeric</p>";
                }
            } else {
                echo "<p class='fail'>❌ FAIL: Missing shelter coordinate fields</p>";
            }
        }
        
        // Test disaster coordinates
        if (!empty($disasters)) {
            $disaster = $disasters[0];
            if (isset($disaster['affected_area'])) {
                $affected_area = $disaster['affected_area'];
                
                if (is_array($affected_area) && isset($affected_area['coordinates'])) {
                    echo "<p class='pass'>✅ PASS: Disaster has GeoJSON coordinates</p>";
                    echo "<p class='info'>🗺️ Disaster geometry type: " . ($affected_area['type'] ?? 'Unknown') . "</p>";
                } else {
                    echo "<p class='fail'>❌ FAIL: Invalid disaster geometry format</p>";
                }
            } else {
                echo "<p class='fail'>❌ FAIL: Missing disaster affected_area field</p>";
            }
        }
        
        echo "<h3>Raw Response Sample:</h3>";
        echo "<pre>" . htmlspecialchars(substr($map_response, 0, 800)) . "...</pre>";
    } else {
        echo "<p class='fail'>❌ FAIL: Invalid map data API response</p>";
    }
}
echo "</div>";

// Test 3: Database Direct Query
echo "<div class='test-section'>";
echo "<h2>Test 3: Database Direct Coordinate Verification</h2>";

try {
    require_once 'backend/config/db_connect.php';
    
    // Test shelter coordinates in database
    $shelter_query = "SELECT id, name, ST_Y(location) as latitude, ST_X(location) as longitude FROM shelters LIMIT 1";
    $shelter_result = $conn->query($shelter_query);
    
    if ($shelter_result && $shelter_result->num_rows > 0) {
        $shelter_row = $shelter_result->fetch_assoc();
        echo "<p class='pass'>✅ PASS: Can extract shelter coordinates from database</p>";
        echo "<p class='info'>📍 DB Shelter: ID={$shelter_row['id']}, Lat={$shelter_row['latitude']}, Lng={$shelter_row['longitude']}</p>";
    } else {
        echo "<p class='info'>ℹ️ No shelters found in database</p>";
    }
    
    // Test disaster geometry in database
    $disaster_query = "SELECT id, name, ST_AsText(affected_area_geometry) as wkt_geometry FROM disasters LIMIT 1";
    $disaster_result = $conn->query($disaster_query);
    
    if ($disaster_result && $disaster_result->num_rows > 0) {
        $disaster_row = $disaster_result->fetch_assoc();
        echo "<p class='pass'>✅ PASS: Can extract disaster geometry from database</p>";
        echo "<p class='info'>🗺️ DB Disaster: ID={$disaster_row['id']}, Geometry=" . substr($disaster_row['wkt_geometry'], 0, 100) . "...</p>";
    } else {
        echo "<p class='info'>ℹ️ No disasters found in database</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='fail'>❌ FAIL: Database connection error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Test 4: Coordinate Parsing Simulation
echo "<div class='test-section'>";
echo "<h2>Test 4: Mobile App Coordinate Parsing Simulation</h2>";

// Simulate the DisasterAlert coordinate parsing logic
function parseGeoJSONCoordinates($geoJsonString) {
    try {
        $geoJson = json_decode($geoJsonString, true);
        if (isset($geoJson['coordinates']) && is_array($geoJson['coordinates'])) {
            $coords = $geoJson['coordinates'];
            if (count($coords) > 0 && is_array($coords[0]) && count($coords[0]) > 0) {
                $firstCoord = $coords[0][0];
                if (is_array($firstCoord) && count($firstCoord) >= 2) {
                    return [
                        'longitude' => $firstCoord[0],
                        'latitude' => $firstCoord[1]
                    ];
                }
            }
        }
    } catch (Exception $e) {
        return null;
    }
    return null;
}

// Test with sample GeoJSON polygon
$sample_geojson = '{"type":"Polygon","coordinates":[[[36.8172,1.2864],[36.8200,1.2864],[36.8200,1.2890],[36.8172,1.2890],[36.8172,1.2864]]]}';
$parsed_coords = parseGeoJSONCoordinates($sample_geojson);

if ($parsed_coords) {
    echo "<p class='pass'>✅ PASS: GeoJSON polygon parsing works correctly</p>";
    echo "<p class='info'>📍 Parsed coordinates: Lat={$parsed_coords['latitude']}, Lng={$parsed_coords['longitude']}</p>";
} else {
    echo "<p class='fail'>❌ FAIL: GeoJSON polygon parsing failed</p>";
}

echo "</div>";

echo "<div class='test-section'>";
echo "<h2>📋 Test Summary</h2>";
echo "<p>Test completed at: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>Review the results above to identify any remaining coordinate issues.</p>";
echo "</div>";
?>
