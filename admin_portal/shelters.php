<?php
function call_api($endpoint) {
    $api_url = "http://localhost/disaster_management_system/backend/api/admin/" . $endpoint;
    $response = @file_get_contents($api_url);
    if ($response === FALSE) return null;
    return json_decode($response, true);
}

$shelters_data = call_api('shelters_get_all.php');
?>

<div class="content-header">
    <h2>Manage Shelters</h2>
    <button class="btn btn-primary">Create New Shelter</button>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Capacity</th>
            <th>Occupancy</th>
            <th>Status</th>
            <th>Last Updated</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($shelters_data && $shelters_data['status'] === 'success' && !empty($shelters_data['data'])): ?>
            <?php foreach ($shelters_data['data'] as $shelter): ?>
                <tr>
                    <td><?php echo htmlspecialchars($shelter['id']); ?></td>
                    <td><?php echo htmlspecialchars($shelter['name']); ?></td>
                    <td><?php echo htmlspecialchars($shelter['capacity']); ?></td>
                    <td><?php echo htmlspecialchars($shelter['current_occupancy']); ?></td>
                    <td><?php echo htmlspecialchars($shelter['status']); ?></td>
                    <td><?php echo htmlspecialchars($shelter['updated_at']); ?></td>
                    <td>
                        <button class="btn btn-primary">Edit</button>
                        <button class="btn btn-danger">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">No shelters found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>