<?php
function call_api($endpoint) {
    $api_url = "http://localhost/disaster_management_system/backend/api/admin/" . $endpoint;
    $response = @file_get_contents($api_url);
    if ($response === FALSE) return null;
    return json_decode($response, true);
}

$responders_data = call_api('responders_get_all.php');
?>

<div class="content-header">
    <h2>Manage Responders</h2>
    <button class="btn btn-primary">Create New Responder</button>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Username</th>
            <th>Team</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($responders_data && $responders_data['status'] === 'success' && !empty($responders_data['data'])): ?>
            <?php foreach ($responders_data['data'] as $responder): ?>
                <tr>
                    <td><?php echo htmlspecialchars($responder['id']); ?></td>
                    <td><?php echo htmlspecialchars($responder['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($responder['username']); ?></td>
                    <td><?php echo htmlspecialchars($responder['team']); ?></td>
                    <td><?php echo htmlspecialchars($responder['created_at']); ?></td>
                    <td>
                        <button class="btn btn-primary">Assign Task</button>
                        <button class="btn btn-primary">Edit</button>
                        <button class="btn btn-danger">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No responders found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>