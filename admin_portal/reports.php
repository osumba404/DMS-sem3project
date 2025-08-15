<?php
function call_api($endpoint) {
    $api_url = "http://localhost/disaster_management_system/backend/api/admin/" . $endpoint;
    $response = @file_get_contents($api_url);
    if ($response === FALSE) return null;
    return json_decode($response, true);
}

$broadcast_history = call_api('get_broadcast_history.php');
$field_reports = call_api('get_field_reports.php');
?>

<div class="content-header">
    <h2>System Reports</h2>
</div>

<!-- Broadcast History Section -->
<h3>Broadcast History</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Target</th>
            <th>Sent By</th>
            <th>Sent At</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($broadcast_history && $broadcast_history['status'] === 'success' && !empty($broadcast_history['data'])): ?>
            <?php foreach ($broadcast_history['data'] as $msg): ?>
                <tr>
                    <td><?php echo htmlspecialchars($msg['id']); ?></td>
                    <td><?php echo htmlspecialchars($msg['title']); ?></td>
                    <td><?php echo htmlspecialchars($msg['target_audience']); ?></td>
                    <td><?php echo htmlspecialchars($msg['admin_name']); ?></td>
                    <td><?php echo htmlspecialchars($msg['sent_at']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5">No broadcast messages found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Field Reports Section -->
<h3 style="margin-top: 40px;">Field Reports from Responders</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Report Content</th>
            <th>Responder</th>
            <th>Disaster</th>
            <th>Submitted At</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($field_reports && $field_reports['status'] === 'success' && !empty($field_reports['data'])): ?>
            <?php foreach ($field_reports['data'] as $report): ?>
                <tr>
                    <td><?php echo htmlspecialchars($report['id']); ?></td>
                    <td><?php echo htmlspecialchars($report['report_content']); ?></td>
                    <td><?php echo htmlspecialchars($report['responder_name']); ?></td>
                    <td><?php echo htmlspecialchars($report['disaster_name']); ?></td>
                    <td><?php echo htmlspecialchars($report['created_at']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5">No field reports found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>