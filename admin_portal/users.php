<?php
function call_api($endpoint) {
    $api_url = "http://localhost/disaster_management_system/backend/api/admin/" . $endpoint;
    $response = @file_get_contents($api_url);
    if ($response === FALSE) return null;
    return json_decode($response, true);
}

$users_data = call_api('users_get_all.php');
?>

<div class="content-header">
    <h2>View Public Users</h2>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Safe Status</th>
            <th>Tourist</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($users_data && $users_data['status'] === 'success' && !empty($users_data['data'])): ?>
            <?php foreach ($users_data['data'] as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone_number']); ?></td>
                    <td><?php echo $user['is_safe'] ? 'Yes' : 'No'; ?></td>
                    <td><?php echo $user['is_tourist'] ? 'Yes' : 'No'; ?></td>
                    <td>
                        <button class="btn btn-primary">View Details</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">No users found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>