<?php
// This is a partial file loaded via AJAX.
// It assumes the helper function is available or defined elsewhere,
// but for simplicity, we can define it here.

function call_api($endpoint) {
    $api_url = "http://localhost/disaster_management_system/backend/api/admin/" . $endpoint;
    $response = @file_get_contents($api_url);
    if ($response === FALSE) return null;
    return json_decode($response, true);
}

$disasters_data = call_api('disasters_get_all.php');
?>

<div class="content-header">
    <h2>Manage Disasters</h2>
    <button class="btn btn-primary">Create New Disaster</button>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Type</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($disasters_data && $disasters_data['status'] === 'success' && !empty($disasters_data['data'])): ?>
            <?php foreach ($disasters_data['data'] as $disaster): ?>
                <tr>
                    <td><?php echo htmlspecialchars($disaster['id']); ?></td>
                    <td><?php echo htmlspecialchars($disaster['name']); ?></td>
                    <td><?php echo htmlspecialchars($disaster['type']); ?></td>
                    <td><?php echo htmlspecialchars($disaster['status']); ?></td>
                    <td><?php echo htmlspecialchars($disaster['created_at']); ?></td>
                    <td>
                        <button class="btn btn-primary">Edit</button>
                        <button class="btn btn-danger">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No disasters found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>