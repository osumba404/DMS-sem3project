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
    <form id="create-disaster-form" action="../backend/api/admin/disasters_create.php" method="POST" novalidate>
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="type">Type</label>
                <select id="type" name="type" required>
                    <option value="" disabled selected>Select a disaster type</option>
                    <option value="Earthquake">Earthquake</option>
                    <option value="Flood">Flood</option>
                    <option value="Hurricane">Hurricane / Cyclone</option>
                    <option value="Wildfire">Wildfire</option>
                    <option value="Tornado">Tornado</option>
                    <option value="Tsunami">Tsunami</option>
                    <option value="Volcanic Eruption">Volcanic Eruption</option>
                    <option value="Landslide">Landslide / Mudslide</option>
                    <option value="Severe Storm">Severe Storm</option>
                    <option value="Industrial Accident">Industrial Accident</option>
                    <option value="Drought">Drought</option>
                    <option value="Pandemic">Pandemic</option>
                    <option value="Extreme Heat">Extreme Heat</option>
                    <option value="Chemical Spill">Chemical Spill</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <!-- The Interactive Map -->
            <div class="form-group">
                <label>Draw Affected Area on the Map</label>
                <div id="map-container" style="height: 400px; width: 100%; border: 1px solid #ccc;"></div>
            </div>

            <!-- This hidden input will store the WKT data from the map -->
            <input type="hidden" id="wkt-input" name="affected_area_wkt" required>
            
            <input type="hidden" name="admin_id" value="<?php echo htmlspecialchars($_SESSION['admin_id']); ?>">

            <button type="submit" class="btn btn-primary">Save Disaster</button>
            <p id="map-error" style="color: red; display: none;">Please draw a polygon on the map to define the affected area.</p>
        </form>
</div>


<!-- Section for Displaying Existing Disasters -->
<div class="content-header">
    <h2>Manage Disasters</h2>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Type</th>
                <th>Status</th>
                <th>Geography Area</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($disasters_data && $disasters_data['status'] === 'success' && !empty($disasters_data['data'])): ?>
                <?php foreach ($disasters_data['data'] as $disaster): ?>
                    <tr data-disaster-id="<?php echo htmlspecialchars($disaster['id']); ?>">
                        <td><?php echo htmlspecialchars($disaster['id']); ?></td>
                        <td><?php echo htmlspecialchars($disaster['name']); ?></td>
                        <td><?php echo htmlspecialchars($disaster['type']); ?></td>
                        <td>
                            <select class="status-dropdown" data-disaster-id="<?php echo htmlspecialchars($disaster['id']); ?>" data-original-status="<?php echo htmlspecialchars($disaster['status']); ?>">
                                <option value="Inactive" <?php echo $disaster['status'] === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                <option value="Prepare" <?php echo $disaster['status'] === 'Prepare' ? 'selected' : ''; ?>>Prepare</option>
                                <option value="Evacuate" <?php echo $disaster['status'] === 'Evacuate' ? 'selected' : ''; ?>>Evacuate</option>
                                <option value="All Clear" <?php echo $disaster['status'] === 'All Clear' ? 'selected' : ''; ?>>All Clear</option>
                            </select>
                        </td>
                        <td class="geography-cell">
                            <button class="btn btn-secondary btn-view-geography" data-geometry="<?php echo htmlspecialchars($disaster['affected_area'] ?? ''); ?>" title="View on Map">
                                <i class="fas fa-map"></i> View Area
                            </button>
                        </td>
                        <td><?php echo htmlspecialchars($disaster['created_at']); ?></td>
                        <td class="actions-cell">
                            <button class="btn btn-primary btn-edit-disaster" data-id="<?php echo htmlspecialchars($disaster['id']); ?>" data-name="<?php echo htmlspecialchars($disaster['name']); ?>" data-type="<?php echo htmlspecialchars($disaster['type']); ?>" data-geometry="<?php echo htmlspecialchars($disaster['affected_area'] ?? ''); ?>">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger btn-delete-disaster" data-id="<?php echo htmlspecialchars($disaster['id']); ?>" data-name="<?php echo htmlspecialchars($disaster['name']); ?>">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No disasters found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Edit Disaster Modal -->
<div id="editDisasterModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit Disaster</h3>
            <button class="modal-close" onclick="closeEditModal()">&times;</button>
        </div>
        <form id="edit-disaster-form">
            <input type="hidden" id="edit-disaster-id">
            <div class="form-group">
                <label for="edit-name">Name</label>
                <input type="text" id="edit-name" name="name" required>
            </div>
            <div class="form-group">
                <label for="edit-type">Type</label>
                <select id="edit-type" name="type" required>
                    <option value="Earthquake">Earthquake</option>
                    <option value="Flood">Flood</option>
                    <option value="Hurricane">Hurricane / Cyclone</option>
                    <option value="Wildfire">Wildfire</option>
                    <option value="Tornado">Tornado</option>
                    <option value="Tsunami">Tsunami</option>
                    <option value="Volcanic Eruption">Volcanic Eruption</option>
                    <option value="Landslide">Landslide / Mudslide</option>
                    <option value="Severe Storm">Severe Storm</option>
                    <option value="Industrial Accident">Industrial Accident</option>
                    <option value="Drought">Drought</option>
                    <option value="Pandemic">Pandemic</option>
                    <option value="Extreme Heat">Extreme Heat</option>
                    <option value="Chemical Spill">Chemical Spill</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label>Update Affected Area on Map</label>
                <div id="edit-map-container" style="height: 300px; width: 100%; border: 1px solid #ccc;"></div>
            </div>
            <input type="hidden" id="edit-wkt-input" name="affected_area_wkt">
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Disaster</button>
            </div>
        </form>
    </div>
</div>

<!-- Geography View Modal -->
<div id="geographyModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-map"></i> Geography Area</h3>
            <button class="modal-close" onclick="closeGeographyModal()">&times;</button>
        </div>
        <div id="geography-map-container" style="height: 400px; width: 100%;"></div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteConfirmModal" class="modal-overlay" style="display: none;">
    <div class="modal-content" style="max-width: 400px;">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h3>
            <button class="modal-close" onclick="closeDeleteModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete the disaster "<span id="delete-disaster-name"></span>"?</p>
            <p class="text-danger"><strong>This action cannot be undone.</strong></p>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
            <button type="button" class="btn btn-danger" id="confirm-delete-btn">Delete</button>
        </div>
    </div>
</div>

<!-- Leaflet.js and Leaflet-Draw library files -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

<script>
let map, editMap, geographyMap;
let drawnItems, editDrawnItems;
let currentDeleteId = null;

document.addEventListener('DOMContentLoaded', function () {
    // --- 1. Initialize the Main Map ---
    map = L.map('map-container').setView([-1.286389, 36.817223], 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // --- 2. Initialize the Drawing Feature ---
    drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);

    const drawControl = new L.Control.Draw({
        draw: {
            polygon: true,
            polyline: false,
            rectangle: false,
            circle: false,
            marker: false,
            circlemarker: false
        },
        edit: {
            featureGroup: drawnItems
        }
    });
    map.addControl(drawControl);

    // --- 3. Capture the Drawn Polygon ---
    map.on(L.Draw.Event.CREATED, function (event) {
        const layer = event.layer;
        drawnItems.clearLayers();
        drawnItems.addLayer(layer);

        const geoJSON = layer.toGeoJSON();
        const coordinates = geoJSON.geometry.coordinates[0];
        
        let wktString = 'POLYGON((';
        coordinates.forEach(function(coord, index) {
            wktString += coord[0] + ' ' + coord[1];
            if (index < coordinates.length - 1) {
                wktString += ', ';
            }
        });
        wktString += '))';

        document.getElementById('wkt-input').value = wktString;
        document.getElementById('map-error').style.display = 'none';
    });

    // --- 4. Handle Form Submission ---
    document.getElementById('create-disaster-form').addEventListener('submit', function(e) {
        const wktInput = document.getElementById('wkt-input');
        const mapError = document.getElementById('map-error');
        
        if (wktInput.value.trim() === '') {
            e.preventDefault();
            mapError.style.display = 'block';
            alert('Please draw the affected area on the map before saving.');
        } else {
            mapError.style.display = 'none';
        }
    });

    // --- 5. Status Dropdown Change Handler ---
    document.querySelectorAll('.status-dropdown').forEach(dropdown => {
        dropdown.addEventListener('change', function() {
            const disasterId = this.dataset.disasterId;
            const newStatus = this.value;
            const originalStatus = this.dataset.originalStatus;
            
            if (newStatus !== originalStatus) {
                updateDisasterStatus(disasterId, newStatus, this);
            }
        });
    });

    // --- 6. Edit Button Handler ---
    document.querySelectorAll('.btn-edit-disaster').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const type = this.dataset.type;
            const geometry = this.dataset.geometry;
            
            openEditModal(id, name, type, geometry);
        });
    });

    // --- 7. Delete Button Handler ---
    document.querySelectorAll('.btn-delete-disaster').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            
            openDeleteModal(id, name);
        });
    });

    // --- 8. Geography View Button Handler ---
    document.querySelectorAll('.btn-view-geography').forEach(btn => {
        btn.addEventListener('click', function() {
            const geometry = this.dataset.geometry;
            openGeographyModal(geometry);
        });
    });

    // --- 9. Edit Form Submission ---
    document.getElementById('edit-disaster-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const id = document.getElementById('edit-disaster-id').value;
        const name = document.getElementById('edit-name').value;
        const type = document.getElementById('edit-type').value;
        const wkt = document.getElementById('edit-wkt-input').value;
        
        if (!wkt) {
            alert('Please draw the affected area on the map.');
            return;
        }
        
        updateDisaster(id, name, type, wkt);
    });

    // --- 10. Delete Confirmation ---
    document.getElementById('confirm-delete-btn').addEventListener('click', function() {
        if (currentDeleteId) {
            deleteDisaster(currentDeleteId);
        }
    });
});

// Status update function
function updateDisasterStatus(disasterId, newStatus, dropdown) {
    fetch('../backend/api/admin/disasters_update_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            disaster_id: disasterId,
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            dropdown.dataset.originalStatus = newStatus;
            showAlert('Status updated successfully', 'success');
        } else {
            dropdown.value = dropdown.dataset.originalStatus;
            showAlert('Failed to update status: ' + data.message, 'error');
        }
    })
    .catch(error => {
        dropdown.value = dropdown.dataset.originalStatus;
        showAlert('Network error: ' + error.message, 'error');
    });
}

// Edit modal functions
function openEditModal(id, name, type, geometry) {
    document.getElementById('edit-disaster-id').value = id;
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-type').value = type;
    
    document.getElementById('editDisasterModal').style.display = 'flex';
    
    setTimeout(() => {
        if (!editMap) {
            editMap = L.map('edit-map-container').setView([-1.286389, 36.817223], 6);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(editMap);
            
            editDrawnItems = new L.FeatureGroup();
            editMap.addLayer(editDrawnItems);
            
            const editDrawControl = new L.Control.Draw({
                draw: {
                    polygon: true,
                    polyline: false,
                    rectangle: false,
                    circle: false,
                    marker: false,
                    circlemarker: false
                },
                edit: {
                    featureGroup: editDrawnItems
                }
            });
            editMap.addControl(editDrawControl);
            
            editMap.on(L.Draw.Event.CREATED, function (event) {
                const layer = event.layer;
                editDrawnItems.clearLayers();
                editDrawnItems.addLayer(layer);
                
                const geoJSON = layer.toGeoJSON();
                const coordinates = geoJSON.geometry.coordinates[0];
                
                let wktString = 'POLYGON((';
                coordinates.forEach(function(coord, index) {
                    wktString += coord[0] + ' ' + coord[1];
                    if (index < coordinates.length - 1) {
                        wktString += ', ';
                    }
                });
                wktString += '))';
                
                document.getElementById('edit-wkt-input').value = wktString;
            });
        }
        
        editMap.invalidateSize();
        
        // Load existing geometry if available
        if (geometry) {
            loadGeometryOnMap(editMap, editDrawnItems, geometry);
        }
    }, 100);
}

function closeEditModal() {
    document.getElementById('editDisasterModal').style.display = 'none';
    if (editDrawnItems) {
        editDrawnItems.clearLayers();
    }
    document.getElementById('edit-wkt-input').value = '';
}

// Geography modal functions
function openGeographyModal(geometry) {
    document.getElementById('geographyModal').style.display = 'flex';
    
    setTimeout(() => {
        if (!geographyMap) {
            geographyMap = L.map('geography-map-container').setView([-1.286389, 36.817223], 6);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(geographyMap);
        }
        
        geographyMap.invalidateSize();
        
        if (geometry) {
            loadGeometryOnMap(geographyMap, null, geometry, true);
        }
    }, 100);
}

function closeGeographyModal() {
    document.getElementById('geographyModal').style.display = 'none';
    if (geographyMap) {
        geographyMap.eachLayer(layer => {
            if (layer instanceof L.Polygon) {
                geographyMap.removeLayer(layer);
            }
        });
    }
}

// Delete modal functions
function openDeleteModal(id, name) {
    currentDeleteId = id;
    document.getElementById('delete-disaster-name').textContent = name;
    document.getElementById('deleteConfirmModal').style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('deleteConfirmModal').style.display = 'none';
    currentDeleteId = null;
}

// API functions
function updateDisaster(id, name, type, wkt) {
    fetch('../backend/api/admin/disasters_update.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            disaster_id: id,
            name: name,
            type: type,
            affected_area_wkt: wkt
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showAlert('Disaster updated successfully', 'success');
            closeEditModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert('Failed to update disaster: ' + data.message, 'error');
        }
    })
    .catch(error => {
        showAlert('Network error: ' + error.message, 'error');
    });
}

function deleteDisaster(id) {
    fetch('../backend/api/admin/disasters_delete.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            disaster_id: id
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showAlert('Disaster deleted successfully', 'success');
            closeDeleteModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert('Failed to delete disaster: ' + data.message, 'error');
        }
    })
    .catch(error => {
        showAlert('Network error: ' + error.message, 'error');
    });
}

// Utility functions
function loadGeometryOnMap(targetMap, targetDrawnItems, geometry, readOnly = false) {
    try {
        // Parse WKT geometry (simplified for POLYGON)
        if (geometry.startsWith('POLYGON')) {
            const coordsMatch = geometry.match(/POLYGON\(\(([^)]+)\)\)/);
            if (coordsMatch) {
                const coordsString = coordsMatch[1];
                const coords = coordsString.split(',').map(coord => {
                    const [lng, lat] = coord.trim().split(' ');
                    return [parseFloat(lat), parseFloat(lng)];
                });
                
                const polygon = L.polygon(coords, {
                    color: readOnly ? '#e74c3c' : '#3498db',
                    fillOpacity: 0.3
                }).addTo(targetMap);
                
                if (targetDrawnItems && !readOnly) {
                    targetDrawnItems.addLayer(polygon);
                    // Set WKT value for edit form
                    document.getElementById('edit-wkt-input').value = geometry;
                }
                
                targetMap.fitBounds(polygon.getBounds());
            }
        }
    } catch (error) {
        console.error('Error loading geometry:', error);
    }
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        z-index: 10000;
        max-width: 300px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        background: ${type === 'success' ? 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)' : 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)'};
    `;
    alertDiv.textContent = message;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
</script>

<style>
.status-dropdown {
    padding: 0.5rem;
    border: 1px solid var(--border-light);
    border-radius: 0.375rem;
    background: var(--white);
    font-size: 0.875rem;
    min-width: 120px;
}

.status-dropdown:focus {
    outline: none;
    border-color: var(--accent-blue);
    box-shadow: 0 0 0 2px rgba(66, 153, 225, 0.2);
}

.geography-cell, .actions-cell {
    white-space: nowrap;
}

.btn {
    margin: 0.125rem;
}

.modal-body {
    padding: 1rem 0;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border-light);
}

.text-danger {
    color: #e53e3e;
}

.alert {
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>