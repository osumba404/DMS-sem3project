<?php
/**
 * API Proxy Endpoint for OpenWeatherMap (Using FREE TIER Endpoints) - DEBUG VERSION
 */

// FORCE PHP to display any and all errors. This is crucial for debugging.
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// --- IMPORTANT: PASTE YOUR API KEY HERE ---
$apiKey = "c78a1e989bd44139451a0e956acdfbc7";

if ($_SERVER['REQUEST_METHOD'] !== 'GET') { exit('Method Not Allowed'); }

$latitude = $_GET['lat'] ?? null;
$longitude = $_GET['lon'] ?? null;

if (empty($latitude) || empty($longitude)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'lat and lon parameters are required.']);
    exit();
}

try {
    // --- API Call 1: Get Current Weather Data ---
    $currentWeatherUrl = "https://api.openweathermap.org/data/2.5/weather?lat={$latitude}&lon={$longitude}&appid={$apiKey}&units=metric";
    $currentWeatherResponse = @file_get_contents($currentWeatherUrl);
    if ($currentWeatherResponse === false) {
        throw new Exception("Failed to fetch current weather data from OpenWeatherMap server.");
    }
    $currentWeatherData = json_decode($currentWeatherResponse, true);

    // --- DEBUG: Check if the JSON decoding worked ---
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($currentWeatherData) || !isset($currentWeatherData['sys'])) {
        throw new Exception("Invalid JSON or unexpected format received from Current Weather API. Response: " . $currentWeatherResponse);
    }

    // --- API Call 2: Get 5-Day / 3-Hour Forecast Data ---
    $forecastUrl = "https://api.openweathermap.org/data/2.5/forecast?lat={$latitude}&lon={$longitude}&appid={$apiKey}&units=metric";
    $forecastResponse = @file_get_contents($forecastUrl);
    if ($forecastResponse === false) {
        throw new Exception("Failed to fetch forecast data from OpenWeatherMap server.");
    }
    $forecastData = json_decode($forecastResponse, true);
    
    // --- DEBUG: Check if the JSON decoding worked ---
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($forecastData) || !isset($forecastData['list'])) {
        throw new Exception("Invalid JSON or unexpected format received from Forecast API. Response: " . $forecastResponse);
    }

    // --- Manually Combine the results to mimic the One Call API structure ---
    $finalResponse = [
        // The 'timezone' value from the current weather response is an offset in seconds, not a string. We'll handle this on the client.
        // For simplicity, we can pass the offset. Or just use UTC as a fallback.
        'timezone' => $currentWeatherData['timezone'] ?? 'UTC', 
        'current' => [
            'sunrise' => $currentWeatherData['sys']['sunrise'],
            'sunset' => $currentWeatherData['sys']['sunset'],
            'temp' => $currentWeatherData['main']['temp'],
            'pressure' => $currentWeatherData['main']['pressure'],
            'humidity' => $currentWeatherData['main']['humidity'],
            'uvi' => null, // Not available in this free endpoint
            'visibility' => $currentWeatherData['visibility'],
            'weather' => $currentWeatherData['weather']
        ],
        'hourly' => []
    ];

    // Process the hourly forecast list
    foreach ($forecastData['list'] as $forecastItem) {
        // We only need the next ~24 hours (8 items * 3 hours = 24 hours)
        if (count($finalResponse['hourly']) >= 8) {
            break;
        }
        $finalResponse['hourly'][] = [
            'dt' => $forecastItem['dt'],
            'temp' => $forecastItem['main']['temp'],
            'weather' => $forecastItem['weather']
        ];
    }
    
    http_response_code(200);
    echo json_encode($finalResponse);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

exit();
?>