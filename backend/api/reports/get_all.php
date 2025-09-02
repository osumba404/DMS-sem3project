<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../../config/db_connect.php';

try {
    $user_id = $_GET['user_id'] ?? null;
    
    if ($user_id) {
        // Get reports for specific user
        $stmt = $pdo->prepare("
            SELECT ur.*, u.full_name as reporter_name, u.email as reporter_email 
            FROM user_reports ur 
            JOIN users u ON ur.user_id = u.id 
            WHERE ur.user_id = ? 
            ORDER BY ur.created_at DESC
        ");
        $stmt->execute([$user_id]);
    } else {
        // Get all reports (for admin)
        $stmt = $pdo->prepare("
            SELECT ur.*, u.full_name as reporter_name, u.email as reporter_email,
                   a.full_name as reviewed_by_name
            FROM user_reports ur 
            JOIN users u ON ur.user_id = u.id 
            LEFT JOIN admins a ON ur.reviewed_by_admin_id = a.id
            ORDER BY ur.created_at DESC
        ");
        $stmt->execute();
    }
    
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Add relative time for each report
    foreach ($reports as &$report) {
        $report['relative_time'] = getRelativeTime($report['created_at']);
    }
    
    echo json_encode([
        'success' => true,
        'reports' => $reports
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

function getRelativeTime($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'Just now';
    if ($time < 3600) return floor($time/60) . ' minutes ago';
    if ($time < 86400) return floor($time/3600) . ' hours ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    if ($time < 31536000) return floor($time/2592000) . ' months ago';
    return floor($time/31536000) . ' years ago';
}
?>
