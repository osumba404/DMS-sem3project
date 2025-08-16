<?php
// This function will still be used to display the list of existing disasters
function call_api($endpoint) {
    $api_url = "http://localhost/DMS-sem3project/backend/api/admin/" . $endpoint;
    $response = @file_get_contents($api_url);
    if ($response === FALSE) return null;
    return json_decode($response, true);
}

$disasters_data = call_api('disasters_get_all.php');
?>

<!-- Section for Creating a New Disaster -->
<div class="form-container" style="margin-bottom: 40px;">
    <h2>Create New Disaster</h2>
    
    <!-- This is a standard HTML form that submits directly to a PHP file -->
    <form action="../backend/api/admin/disasters_create.php" method="POST">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required autocomplete="off">
        </div>
        <div class="form-group">
            <label for="type">Type</label>
            <select id="type" name="type" required>
                <option value="Flood">Flood</option>
                <option value="Earthquake">Earthquake</option>
                <option value="Hurricane">Hurricane</option>
                <option value="Wildfire">Wildfire</option>
                <option value="Tsunami">Tsunami</option>
                <option value="Other">Other</option>
            </select>
        </div>
        <div class="form-group">
            <label for="wkt">Affected Area (WKT Polygon)</label>
            <textarea id="wkt" name="affected_area_wkt" rows="4" placeholder="e.g., POLYGON((lng lat, lng lat, ...))" required></textarea>
        </div>
        
        <input type="hidden" name="admin_id" value="<?php echo htmlspecialchars($_SESSION['admin_id']); ?>">

        <button type="submit" class="btn btn-primary">Save Disaster</button>
    </form>
</div>


<!-- Section for Displaying Existing Disasters -->
<div class="content-header">
    <h2>Manage Disasters</h2>
    <!-- The "Create" button in the header is no longer needed as the form is always visible -->
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
                        <button class="btn btn-primary btn-edit-disaster" data-id="<?php echo htmlspecialchars($disaster['id']); ?>">Edit</button>
                        <button class="btn btn-danger btn-delete-disaster" data-id="<?php echo htmlspecialchars($disaster['id']); ?>">Delete</button>
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