<?php
/**
 * API Endpoint: Submit Field Report
 * 
 * Allows a responder to submit a field report.
 * Method: POST
 * 
 * Required POST parameters (as JSON):
 * - responder_id
 * - disaster_id
 * - report_content
 * - latitude (optional)
 * - longitude (optional)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_response(405, 'Method Not Allowed. Please use POST.');
}

$data = json_decode(file_get_contents("php://input"), true);

$responder_id = $data['responder_id'] ?? null;
$disaster_id = $data['disaster_id'] ?? null;
$report_content = $data['report_content'] ?? null;
$latitude = $data['latitude'] ?? null;
$longitude = $data['longitude'] ?? null;

if (empty($responder_id) || empty($disaster_id) || empty($report_content)) {
    send_response(400, 'Bad Request. responder_id, disaster_id, and report_content are required.');
}

// Prepare the INSERT statement
$sql = "INSERT INTO field_reports (responder_id, disaster_id, report_content, location) VALUES (?, ?, ?, POINT(?, ?))";
if ($latitude === null || $longitude === null) {
    // If location is not provided, insert NULL
    $sql = "INSERT INTO field_reports (responder_id, disaster_id, report_content, location) VALUES (?, ?, ?, NULL)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $responder_id, $disaster_id, $report_content);
} else {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisdd", $responder_id, $disaster_id, $report_content, $longitude, $latitude);
}

// Execute and send response
if ($stmt->execute()) {
    send_response(201, 'Field report submitted successfully.');
} else {
    send_response(500, 'Internal Server Error. Failed to submit report.');
}

$stmt->close();
$conn->close();
?>