<?php
require_once 'includes/auth_check.php';
?>

<div class="page-header">
    <h1><i class="fas fa-file-alt"></i> User Reports Management</h1>
    <p>Review and manage reports submitted by mobile app users</p>
</div>

<!-- Filter Controls -->
<div class="filter-controls">
    <div class="filter-group">
        <label for="status-filter">Status:</label>
        <select id="status-filter">
            <option value="">All Statuses</option>
            <option value="Submitted">Submitted</option>
            <option value="Under Review">Under Review</option>
            <option value="Investigating">Investigating</option>
            <option value="Resolved">Resolved</option>
            <option value="Closed">Closed</option>
        </select>
    </div>
    <div class="filter-group">
        <label for="priority-filter">Priority:</label>
        <select id="priority-filter">
            <option value="">All Priorities</option>
            <option value="Critical">Critical</option>
            <option value="High">High</option>
            <option value="Medium">Medium</option>
            <option value="Low">Low</option>
        </select>
    </div>
    <div class="filter-group">
        <label for="category-filter">Category:</label>
        <select id="category-filter">
            <option value="">All Categories</option>
            <option value="Incident">Incident</option>
            <option value="Hazard">Hazard</option>
            <option value="Infrastructure">Infrastructure</option>
            <option value="Safety">Safety</option>
            <option value="Other">Other</option>
        </select>
    </div>
    <button id="apply-filters" class="btn btn-primary">Apply Filters</button>
    <button id="clear-filters" class="btn btn-secondary">Clear</button>
</div>

<!-- Reports Table -->
<div class="table-container">
    <table class="data-table" id="reports-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Reporter</th>
                <th>Category</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Location</th>
                <th>Submitted</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="reports-tbody">
            <!-- Reports will be loaded here -->
        </tbody>
    </table>
</div>

<!-- View Report Modal -->
<div id="viewReportModal" class="modal">
    <div class="modal-content large">
        <div class="modal-header">
            <h2>Report Details</h2>
            <span class="close" onclick="closeViewModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div class="report-details">
                <div class="detail-section">
                    <h3>Report Information</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Title:</label>
                            <span id="detail-title"></span>
                        </div>
                        <div class="detail-item">
                            <label>Category:</label>
                            <span id="detail-category"></span>
                        </div>
                        <div class="detail-item">
                            <label>Priority:</label>
                            <span id="detail-priority" class="priority-badge"></span>
                        </div>
                        <div class="detail-item">
                            <label>Status:</label>
                            <span id="detail-status" class="status-badge"></span>
                        </div>
                    </div>
                    <div class="detail-item full-width">
                        <label>Description:</label>
                        <p id="detail-description"></p>
                    </div>
                </div>

                <div class="detail-section">
                    <h3>Reporter Information</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Name:</label>
                            <span id="detail-reporter-name"></span>
                        </div>
                        <div class="detail-item">
                            <label>Email:</label>
                            <span id="detail-reporter-email"></span>
                        </div>
                        <div class="detail-item">
                            <label>Phone:</label>
                            <span id="detail-reporter-phone"></span>
                        </div>
                        <div class="detail-item">
                            <label>Submitted:</label>
                            <span id="detail-submitted-time"></span>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <h3>Location Information</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Address:</label>
                            <span id="detail-address"></span>
                        </div>
                        <div class="detail-item">
                            <label>Coordinates:</label>
                            <span id="detail-coordinates"></span>
                        </div>
                    </div>
                    <div id="report-location-map" style="height: 300px; margin-top: 10px;"></div>
                </div>

                <div class="detail-section">
                    <h3>Admin Actions</h3>
                    <div class="admin-actions">
                        <div class="form-group">
                            <label for="update-status">Update Status:</label>
                            <select id="update-status">
                                <option value="Submitted">Submitted</option>
                                <option value="Under Review">Under Review</option>
                                <option value="Investigating">Investigating</option>
                                <option value="Resolved">Resolved</option>
                                <option value="Closed">Closed</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="update-priority">Update Priority:</label>
                            <select id="update-priority">
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                                <option value="Critical">Critical</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="admin-notes">Admin Notes:</label>
                            <textarea id="admin-notes" rows="4" placeholder="Add notes about this report..."></textarea>
                        </div>
                        <div class="form-actions">
                            <button id="update-report-btn" class="btn btn-primary">Update Report</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentReportId = null;
let reportLocationMap = null;

document.addEventListener('DOMContentLoaded', function() {
    loadReports();
    
    // Filter controls
    document.getElementById('apply-filters').addEventListener('click', loadReports);
    document.getElementById('clear-filters').addEventListener('click', clearFilters);
    
    // Update report button
    document.getElementById('update-report-btn').addEventListener('click', updateReport);
});

function loadReports() {
    const statusFilter = document.getElementById('status-filter').value;
    const priorityFilter = document.getElementById('priority-filter').value;
    const categoryFilter = document.getElementById('category-filter').value;
    
    fetch('../backend/api/reports/get_all.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let reports = data.reports;
                
                // Apply filters
                if (statusFilter) reports = reports.filter(r => r.status === statusFilter);
                if (priorityFilter) reports = reports.filter(r => r.priority === priorityFilter);
                if (categoryFilter) reports = reports.filter(r => r.category === categoryFilter);
                
                displayReports(reports);
            } else {
                alert('Error loading reports: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading reports');
        });
}

function displayReports(reports) {
    const tbody = document.getElementById('reports-tbody');
    tbody.innerHTML = '';
    
    reports.forEach(report => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${report.id}</td>
            <td class="report-title">${report.title}</td>
            <td>${report.reporter_name}</td>
            <td><span class="category-badge">${report.category}</span></td>
            <td><span class="priority-badge priority-${report.priority.toLowerCase()}">${report.priority}</span></td>
            <td><span class="status-badge status-${report.status.toLowerCase().replace(' ', '-')}">${report.status}</span></td>
            <td>${report.address || (report.latitude ? `${report.latitude}, ${report.longitude}` : 'N/A')}</td>
            <td>${report.relative_time}</td>
            <td>
                <button class="btn btn-info btn-sm" onclick="viewReport(${report.id})">View</button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function viewReport(reportId) {
    fetch(`../backend/api/reports/get_single.php?id=${reportId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const report = data.report;
                currentReportId = reportId;
                
                // Populate modal with report data
                document.getElementById('detail-title').textContent = report.title;
                document.getElementById('detail-category').textContent = report.category;
                document.getElementById('detail-priority').textContent = report.priority;
                document.getElementById('detail-priority').className = `priority-badge priority-${report.priority.toLowerCase()}`;
                document.getElementById('detail-status').textContent = report.status;
                document.getElementById('detail-status').className = `status-badge status-${report.status.toLowerCase().replace(' ', '-')}`;
                document.getElementById('detail-description').textContent = report.description;
                
                document.getElementById('detail-reporter-name').textContent = report.reporter_name;
                document.getElementById('detail-reporter-email').textContent = report.reporter_email;
                document.getElementById('detail-reporter-phone').textContent = report.reporter_phone || 'N/A';
                document.getElementById('detail-submitted-time').textContent = report.created_at;
                
                document.getElementById('detail-address').textContent = report.address || 'N/A';
                document.getElementById('detail-coordinates').textContent = report.latitude ? 
                    `${report.latitude}, ${report.longitude}` : 'N/A';
                
                // Set current values in update form
                document.getElementById('update-status').value = report.status;
                document.getElementById('update-priority').value = report.priority;
                document.getElementById('admin-notes').value = report.admin_notes || '';
                
                // Show modal
                document.getElementById('viewReportModal').style.display = 'block';
                
                // Initialize map if coordinates available
                if (report.latitude && report.longitude) {
                    setTimeout(() => {
                        initReportLocationMap(parseFloat(report.latitude), parseFloat(report.longitude), report.title);
                    }, 100);
                }
            } else {
                alert('Error loading report: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading report');
        });
}

function initReportLocationMap(lat, lng, title) {
    if (reportLocationMap) {
        reportLocationMap.remove();
    }
    
    reportLocationMap = L.map('report-location-map').setView([lat, lng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(reportLocationMap);
    
    L.marker([lat, lng]).addTo(reportLocationMap)
        .bindPopup(`<b>${title}</b><br>Lat: ${lat.toFixed(6)}<br>Lng: ${lng.toFixed(6)}`)
        .openPopup();
}

function updateReport() {
    if (!currentReportId) return;
    
    const status = document.getElementById('update-status').value;
    const priority = document.getElementById('update-priority').value;
    const adminNotes = document.getElementById('admin-notes').value;
    
    const data = {
        report_id: currentReportId,
        status: status,
        priority: priority,
        admin_notes: adminNotes,
        admin_id: 1 // TODO: Get from session
    };
    
    fetch('../backend/api/admin/reports_update.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Report updated successfully!');
            closeViewModal();
            loadReports();
        } else {
            alert('Error updating report: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating report');
    });
}

function closeViewModal() {
    document.getElementById('viewReportModal').style.display = 'none';
    if (reportLocationMap) {
        reportLocationMap.remove();
        reportLocationMap = null;
    }
    currentReportId = null;
}

function clearFilters() {
    document.getElementById('status-filter').value = '';
    document.getElementById('priority-filter').value = '';
    document.getElementById('category-filter').value = '';
    loadReports();
}

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const modal = document.getElementById('viewReportModal');
    if (event.target === modal) {
        closeViewModal();
    }
});
</script>

<style>
.filter-controls {
    background: var(--card-bg);
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 24px;
    display: flex;
    gap: 20px;
    align-items: end;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-group label {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 14px;
}

.filter-group select {
    padding: 8px 12px;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    background: var(--input-bg);
    color: var(--text-primary);
    min-width: 150px;
}

.report-title {
    font-weight: 600;
    color: var(--primary-color);
    cursor: pointer;
}

.category-badge {
    background: var(--info-color);
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

.priority-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.priority-critical { background: #dc3545; color: white; }
.priority-high { background: #fd7e14; color: white; }
.priority-medium { background: #ffc107; color: #000; }
.priority-low { background: #28a745; color: white; }

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

.status-submitted { background: #6c757d; color: white; }
.status-under-review { background: #17a2b8; color: white; }
.status-investigating { background: #ffc107; color: #000; }
.status-resolved { background: #28a745; color: white; }
.status-closed { background: #343a40; color: white; }

.modal-content.large {
    width: 90%;
    max-width: 1000px;
}

.report-details {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.detail-section {
    background: var(--card-bg);
    padding: 20px;
    border-radius: 12px;
    border: 1px solid var(--border-color);
}

.detail-section h3 {
    margin: 0 0 16px 0;
    color: var(--primary-color);
    font-size: 18px;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 16px;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.detail-item.full-width {
    grid-column: 1 / -1;
}

.detail-item label {
    font-weight: 600;
    color: var(--text-secondary);
    font-size: 14px;
}

.detail-item span, .detail-item p {
    color: var(--text-primary);
    font-size: 16px;
}

.admin-actions {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 16px;
}

@media (max-width: 768px) {
    .filter-controls {
        flex-direction: column;
        align-items: stretch;
    }
    
    .detail-grid {
        grid-template-columns: 1fr;
    }
}
</style>