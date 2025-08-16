<?php
// Helper function to fetch the list of shelters for the table below.
function call_api($endpoint) {
    $api_url = "http://localhost/DMS-sem3project/backend/api/admin/" . $endpoint;
    $response = @file_get_contents($api_url);
    if ($response === FALSE) return null;
    return json_decode($response, true);
}

$shelters_data = call_api('shelters_get_all.php');
?>

<!-- Section for Creating a New Shelter -->
<div class="form-container" style="margin-bottom: 40px;">
    <h2>Create New Shelter</h2>
    
    <!-- This form submits directly to our new PHP processing script -->
    <form action="../backend/api/admin/shelters_create.php" method="POST">
        <div class="form-group">
            <label for="name">Shelter Name</label>
            <input type="text" id="name" name="name" required autocomplete="off">
        </div>
        <div class="form-group">
            <label for="latitude">Latitude</label>
            <input type="text" id="latitude" name="latitude" placeholder="e.g., 34.0522" required>
        </div>
        <div class="form-group">
            <label for="longitude">Longitude</label>
            <input type="text" id="longitude" name="longitude" placeholder="e.g., -118.2437" required>
        </div>
        <div class="form-group">
            <label for="capacity">Capacity</label>
            <input type="number" id="capacity" name="capacity" required>
        </div>
        <div class="form-group">
            <label for="supplies">Available Supplies (JSON format)</label>
            <textarea id="supplies" name="available_supplies" rows="3">{"food_packs": 100, "water_liters": 500, "first_aid_kits": 20}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Save Shelter</button>
    </form>
</div>

<!-- Section for Displaying Existing Shelters -->
<div class="content-header">
    <h2>Manage Shelters</h2>
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
                        <button class="btn btn-primary btn-edit-shelter" data-id="<?php echo htmlspecialchars($shelter['id']); ?>">Edit</button>
                        <button class="btn btn-danger btn-delete-shelter" data-id="<?php echo htmlspecialchars($shelter['id']); ?>">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7">No shelters found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>