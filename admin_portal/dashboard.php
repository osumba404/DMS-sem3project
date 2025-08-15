<?php
// Helper function to call our own API
function call_api($endpoint) {
    // IMPORTANT: Replace with the actual URL of your backend in a real deployment
    $api_url = "http://localhost/disaster_management_system/backend/api/admin/" . $endpoint;
    $response = @file_get_contents($api_url);
    if ($response === FALSE) {
        return null;
    }
    return json_decode($response, true);
}

$stats_data = call_api('get_dashboard_stats.php');

if ($stats_data && $stats_data['status'] === 'success') {
    $stats = $stats_data['data'];
} else {
    // Set default values if API call fails
    $stats = [
        'total_users' => 'N/A',
        'active_disasters' => 'N/A',
        'open_shelters' => 'N/A',
        'total_occupancy' => 'N/A',
        'total_responders' => 'N/A'
    ];
}
?>

<div class="dashboard-container">
    <h2>System Overview</h2>
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Users</h3>
            <p><?php echo htmlspecialchars($stats['total_users']); ?></p>
        </div>
        <div class="stat-card">
            <h3>Active Disasters</h3>
            <p><?php echo htmlspecialchars($stats['active_disasters']); ?></p>
        </div>
        <div class="stat-card">
            <h3>Open Shelters</h3>
            <p><?php echo htmlspecialchars($stats['open_shelters']); ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Occupancy</h3>
            <p><?php echo htmlspecialchars($stats['total_occupancy']); ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Responders</h3>
            <p><?php echo htmlspecialchars($stats['total_responders']); ?></p>
        </div>
    </div>
</div>

<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    .stat-card {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        text-align: center;
    }
    .stat-card h3 {
        margin-top: 0;
        color: #555;
    }
    .stat-card p {
        font-size: 2.5em;
        font-weight: bold;
        color: #1abc9c;
        margin: 10px 0 0;
    }
</style>