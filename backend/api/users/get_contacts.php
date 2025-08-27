<?php
/**
 * API Endpoint: Get All Emergency Contacts (New System)
 * 
 * Retrieves three lists for a given user:
 * 1. Accepted contacts (people the user can notify).
 * 2. Pending requests the user has sent.
 * 3. Pending requests the user has received.
 *
 * Method: GET
 * Required GET parameters:
 * - user_id
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../../config/db_connect.php';

// --- Main Logic ---

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Please use GET.']);
    exit();
}

$user_id = $_GET['user_id'] ?? null;

if (empty($user_id) || !is_numeric($user_id)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Bad Request. A valid user_id is required.']);
    exit();
}

try {
    // --- 1. Fetch ACCEPTED Contacts ---
    // These are people that `user_id` has added and who have accepted.
    $sql_accepted = "SELECT 
                        ec.id as request_id,
                        u.id as user_id,
                        u.full_name,
                        u.phone_number,
                        ec.relationship
                    FROM emergency_contacts ec
                    JOIN users u ON ec.contact_user_id = u.id
                    WHERE ec.user_id = ? AND ec.status = 'accepted'";
    
    $stmt_accepted = $conn->prepare($sql_accepted);
    $stmt_accepted->bind_param("i", $user_id);
    $stmt_accepted->execute();
    $result_accepted = $stmt_accepted->get_result();
    $accepted_contacts = $result_accepted->fetch_all(MYSQLI_ASSOC);
    $stmt_accepted->close();


    // --- 2. Fetch PENDING requests the user has SENT ---
    // These are people `user_id` has invited.
    $sql_pending_sent = "SELECT 
                            ec.id as request_id,
                            u.id as user_id,
                            u.full_name,
                            u.phone_number,
                            ec.relationship
                         FROM emergency_contacts ec
                         JOIN users u ON ec.contact_user_id = u.id
                         WHERE ec.user_id = ? AND ec.status = 'pending'";

    $stmt_pending_sent = $conn->prepare($sql_pending_sent);
    $stmt_pending_sent->bind_param("i", $user_id);
    $stmt_pending_sent->execute();
    $result_pending_sent = $stmt_pending_sent->get_result();
    $pending_sent_requests = $result_pending_sent->fetch_all(MYSQLI_ASSOC);
    $stmt_pending_sent->close();


    // --- 3. Fetch PENDING requests the user has RECEIVED ---
    // These are people who have invited `user_id`.
    $sql_pending_received = "SELECT 
                                ec.id as request_id,
                                u.id as user_id,
                                u.full_name,
                                u.phone_number,
                                ec.relationship
                             FROM emergency_contacts ec
                             JOIN users u ON ec.user_id = u.id
                             WHERE ec.contact_user_id = ? AND ec.status = 'pending'";

    $stmt_pending_received = $conn->prepare($sql_pending_received);
    $stmt_pending_received->bind_param("i", $user_id);
    $stmt_pending_received->execute();
    $result_pending_received = $stmt_pending_received->get_result();
    $pending_received_requests = $result_pending_received->fetch_all(MYSQLI_ASSOC);
    $stmt_pending_received->close();
    
    // --- Combine all results into a single response object ---
    $response_data = [
        'accepted_contacts' => $accepted_contacts,
        'pending_sent_requests' => $pending_sent_requests,
        'pending_received_requests' => $pending_received_requests
    ];

    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Contacts retrieved successfully.',
        'data' => $response_data
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal Server Error: ' . $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
exit();
?>