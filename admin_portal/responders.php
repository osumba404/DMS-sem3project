<?php
// Helper function to fetch the list of responders for the table below.
function call_api($endpoint) {
    $api_url = "http://localhost/DMS-sem3project/backend/api/admin/" . $endpoint;
    $response = @file_get_contents($api_url);
    if ($response === FALSE) return null;
    return json_decode($response, true);
}

$responders_data = call_api('responders_get_all.php');
?>

<!-- Section for Creating a New Responder -->
<div class="form-container" style="margin-bottom: 40px;">
    <h2>Create New Responder Account</h2>
    
    <!-- This form submits directly to our new PHP processing script -->
    <form action="../backend/api/admin/responders_create.php" method="POST">
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" required autocomplete="off">
        </div>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required autocomplete="off">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="team">Team</label>
            <input type="text" id="team" name="team" placeholder="e.g., Medical Unit A" required>
        </div>

        <button type="submit" class="btn btn-primary">Save Responder</button>
    </form>
</div>


<!-- Section for Displaying Existing Responders -->
<div class="content-header">
    <h2>Manage Responders</h2>
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
                        <button class="btn btn-primary btn-edit-responder" data-id="<?php echo htmlspecialchars($responder['id']); ?>">Edit</button>
                        <button class="btn btn-danger btn-delete-responder" data-id="<?php echo htmlspecialchars($responder['id']); ?>">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6">No responders found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>