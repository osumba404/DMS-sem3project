<div class="content-header">
    <h2>Broadcast a New Message</h2>
</div>

<div class="content-header">
    <h2>Broadcast a New Message</h2>
</div>

<div class="form-container">
    <form action="../backend/api/admin/broadcast_process.php" method="POST">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="body">Message Body</label>
            <textarea id="body" name="body" rows="5" required></textarea>
        </div>
        <div class="form-group">
            <label for="target_audience">Target Audience</label>
            <select id="target_audience" name="target_audience">
                <option value="All">All Users</option>
                <option value="Affected Zone">Users in Affected Zones</option>
                <option value="Tourists">Tourists</option>
            </select>
        </div>
        
        <?php
        // A robust check to ensure the session variable exists before using it.
        // This confirms that our session handling in index.php is working as expected.
        if (isset($_SESSION['admin_id'])) {
            echo '<input type="hidden" name="admin_id" value="' . htmlspecialchars($_SESSION['admin_id']) . '">';
        } else {
            // If the session is somehow lost, show an error instead of a broken form.
            echo '<p style="color: red;">Error: Admin session not found. Please log in again.</p>';
        }
        ?>
        
        <button type="submit" class="btn btn-primary">Send Broadcast</button>
    </form>
</div>

<!-- Basic styling for the form -->
<style>
    .form-container { max-width: 600px; background: #fff; padding: 20px; border-radius: 8px; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
    .form-group input, .form-group textarea, .form-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
    }
</style>