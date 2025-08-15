<div class="content-header">
    <h2>Broadcast a New Message</h2>
</div>

<div class="form-container">
    <form id="broadcast-form">
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
    }
</style>