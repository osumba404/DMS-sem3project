<?php
/**
 * API Endpoint: Search for Users
 * 
 * Searches for registered users by name, email, or phone number.
 * If the search query is empty, it returns all users.
 * Excludes the user who is currently performing the search.
 *
 * Method: GET
 * Required GET parameters:
 * - user_id: The ID of the user performing the search (to exclude them from results)
 * Optional GET parameters:
 * - query: The search term. If empty or not provided, returns all users.
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

// Get the search term and the current user's ID from the URL
$search_query = $_GET['query'] ?? '';
$current_user_id = $_GET['user_id'] ?? 0;

// --- Validation ---
if (empty($current_user_id) || !is_numeric($current_user_id)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Bad Request. A valid user_id is required.']);
    exit();
}

try {
    // We only select the public information needed for the search result display.
    // NEVER send back sensitive information like passwords.
    
    // --- DYNAMIC SQL QUERY LOGIC ---

    if (empty(trim($search_query))) {
        // SCENARIO 1: The search query is empty. Fetch all users.
        $sql = "SELECT 
                    id, 
                    full_name, 
                    email 
                FROM users 
                WHERE id != ?"; // Exclude the current user

        $stmt = $conn->prepare($sql);
        // Bind only the current user's ID
        $stmt->bind_param("i", $current_user_id);

    } else {
        // SCENARIO 2: A search query is provided. Perform the filtered search.
        $sql = "SELECT 
                    id, 
                    full_name, 
                    email 
                FROM users 
                WHERE 
                    (full_name LIKE ? OR email = ? OR phone_number = ?) 
                AND 
                    id != ?";

        $stmt = $conn->prepare($sql);
        // The '%' are wildcard characters for the LIKE search.
        $search_like = '%' . $search_query . '%';
        // Bind all four parameters
        $stmt->bind_param("sssi", $search_like, $search_query, $search_query, $current_user_id);
    }
    
    // --- EXECUTION & RESPONSE (Same for both scenarios) ---
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch all matching users into an array
    $users = $result->fetch_all(MYSQLI_ASSOC);
    
    $stmt->close();
    $conn->close();

    // --- Success Response ---
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => count($users) . ' user(s) found.',
        'data' => $users
    ]);

} catch (Exception $e) {
    // --- Error Response ---
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal Server Error: ' . $e->getMessage()
    ]);
}

exit();
?>