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
    
    <form id="create-shelter-form" action="../backend/api/admin/shelters_create.php" method="POST">
        <div class="form-group">
            <label for="shelter-name">Shelter Name</label>
            <input type="text" id="shelter-name" name="name" required>
        </div>
        <div class="form-group">
            <label for="shelter-capacity">Capacity (Number of People)</label>
            <input type="number" id="shelter-capacity" name="capacity" required>
        </div>

        <!-- The Interactive Map for placing a pin -->
        <div class="form-group">
            <label>Place a Pin on the Map to Set Shelter Location</label>
            <div id="shelter-map-container" style="height: 400px; width: 100%; border: 1px solid #ccc;"></div>
        </div>

        <!-- Hidden inputs to store the coordinates from the map -->
        <input type="hidden" id="latitude-input" name="latitude" required>
        <input type="hidden" id="longitude-input" name="longitude" required>
        
        <!-- Simplified Supply Inputs -->
        <div class="form-group">
            <label>Available Supplies</label>
            <div class="supply-inputs">
                <div>
                    <label for="food_packs">Food Packs</label>
                    <input type="number" id="food_packs" name="food_packs" value="0">
                </div>
                <div>
                    <label for="water_liters">Water (Liters)</label>
                    <input type="number" id="water_liters" name="water_liters" value="0">
                </div>
                <div>
                    <label for="first_aid_kits">First-Aid Kits</label>
                    <input type="number" id="first_aid_kits" name="first_aid_kits" value="0">
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Save Shelter</button>
        <p id="map-pin-error" style="color: red; display: none;">Please place a pin on the map.</p>
    </form>
</div>

<!-- Section for Displaying Existing Shelters -->
<div class="content-header">
    <h2>Manage Shelters</h2>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Capacity</th>
                <th>Occupancy</th>
                <th>Status</th>
                <th>Location</th>
                <th>Last Updated</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($shelters_data && $shelters_data['status'] === 'success' && !empty($shelters_data['data'])): ?>
                <?php foreach ($shelters_data['data'] as $shelter): ?>
                    <tr data-shelter-id="<?php echo htmlspecialchars($shelter['id']); ?>">
                        <td><?php echo htmlspecialchars($shelter['id']); ?></td>
                        <td><?php echo htmlspecialchars($shelter['name']); ?></td>
                        <td><?php echo htmlspecialchars($shelter['capacity']); ?></td>
                        <td><?php echo htmlspecialchars($shelter['current_occupancy']); ?></td>
                        <td>
                            <select class="status-dropdown" data-shelter-id="<?php echo htmlspecialchars($shelter['id']); ?>" data-original-status="<?php echo htmlspecialchars($shelter['status']); ?>">
                                <option value="Open" <?php echo $shelter['status'] === 'Open' ? 'selected' : ''; ?>>Open</option>
                                <option value="Full" <?php echo $shelter['status'] === 'Full' ? 'selected' : ''; ?>>Full</option>
                                <option value="Closed" <?php echo $shelter['status'] === 'Closed' ? 'selected' : ''; ?>>Closed</option>
                            </select>
                        </td>
                        <td class="location-cell">
                            <button class="btn btn-secondary btn-view-location" data-lat="<?php echo htmlspecialchars($shelter['latitude'] ?? ''); ?>" data-lng="<?php echo htmlspecialchars($shelter['longitude'] ?? ''); ?>" title="View on Map">
                                <i class="fas fa-map-marker-alt"></i> View Location
                            </button>
                        </td>
                        <td><?php echo htmlspecialchars($shelter['updated_at']); ?></td>
                        <td class="actions-cell">
                            <button class="btn btn-primary btn-edit-shelter" 
                                data-id="<?php echo htmlspecialchars($shelter['id']); ?>" 
                                data-name="<?php echo htmlspecialchars($shelter['name']); ?>" 
                                data-capacity="<?php echo htmlspecialchars($shelter['capacity']); ?>"
                                data-lat="<?php echo htmlspecialchars($shelter['latitude'] ?? ''); ?>"
                                data-lng="<?php echo htmlspecialchars($shelter['longitude'] ?? ''); ?>"
                                data-food="<?php echo htmlspecialchars($shelter['food_packs'] ?? 0); ?>"
                                data-water="<?php echo htmlspecialchars($shelter['water_liters'] ?? 0); ?>"
                                data-firstaid="<?php echo htmlspecialchars($shelter['first_aid_kits'] ?? 0); ?>">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger btn-delete-shelter" data-id="<?php echo htmlspecialchars($shelter['id']); ?>" data-name="<?php echo htmlspecialchars($shelter['name']); ?>">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8">No shelters found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Edit Shelter Modal -->
<div id="editShelterModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit Shelter</h3>
            <button class="modal-close" onclick="closeEditModal()">&times;</button>
        </div>
        <form id="edit-shelter-form">
            <input type="hidden" id="edit-shelter-id">
            <div class="form-group">
                <label for="edit-name">Shelter Name</label>
                <input type="text" id="edit-name" name="name" required>
            </div>
            <div class="form-group">
                <label for="edit-capacity">Capacity (Number of People)</label>
                <input type="number" id="edit-capacity" name="capacity" required>
            </div>
            <div class="form-group">
                <label>Update Shelter Location on Map</label>
                <div id="edit-map-container" style="height: 300px; width: 100%; border: 1px solid #ccc;"></div>
            </div>
            <input type="hidden" id="edit-latitude-input" name="latitude">
            <input type="hidden" id="edit-longitude-input" name="longitude">
            
            <div class="form-group">
                <label>Available Supplies</label>
                <div class="supply-inputs">
                    <div>
                        <label for="edit-food-packs">Food Packs</label>
                        <input type="number" id="edit-food-packs" name="food_packs" value="0">
                    </div>
                    <div>
                        <label for="edit-water-liters">Water (Liters)</label>
                        <input type="number" id="edit-water-liters" name="water_liters" value="0">
                    </div>
                    <div>
                        <label for="edit-first-aid-kits">First-Aid Kits</label>
                        <input type="number" id="edit-first-aid-kits" name="first_aid_kits" value="0">
                    </div>
                </div>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Shelter</button>
            </div>
        </form>
    </div>
</div>

<!-- Location View Modal -->
<div id="locationModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-map-marker-alt"></i> Shelter Location</h3>
            <button class="modal-close" onclick="closeLocationModal()">&times;</button>
        </div>
        <div id="location-map-container" style="height: 400px; width: 100%;"></div>
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
            <p>Are you sure you want to delete the shelter "<span id="delete-shelter-name"></span>"?</p>
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
let map, editMap, locationMap;
let shelterMarker, editMarker;
let currentDeleteId = null;

document.addEventListener('DOMContentLoaded', function () {
    // --- 1. Initialize the Main Map ---
    map = L.map('shelter-map-container').setView([-1.286389, 36.817223], 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // --- 2. Handle Map Clicks to Place/Move the Pin ---
    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        if (shelterMarker) {
            shelterMarker.setLatLng(e.latlng);
        } else {
            shelterMarker = L.marker(e.latlng).addTo(map);
        }

        document.getElementById('latitude-input').value = lat;
        document.getElementById('longitude-input').value = lng;
        document.getElementById('map-pin-error').style.display = 'none';
    });

    // --- 3. Handle Form Submission ---
    document.getElementById('create-shelter-form').addEventListener('submit', function(e) {
        const latInput = document.getElementById('latitude-input');
        const mapError = document.getElementById('map-pin-error');
        
        if (latInput.value.trim() === '') {
            e.preventDefault();
            mapError.style.display = 'block';
            alert('Please click on the map to place a pin for the shelter location.');
        }
    });

    // --- 4. Status Dropdown Change Handler ---
    document.querySelectorAll('.status-dropdown').forEach(dropdown => {
        dropdown.addEventListener('change', function() {
            const shelterId = this.dataset.shelterId;
            const newStatus = this.value;
            const originalStatus = this.dataset.originalStatus;
            
            if (newStatus !== originalStatus) {
                updateShelterStatus(shelterId, newStatus, this);
            }
        });
    });

    // --- 5. Edit Button Handler ---
    document.querySelectorAll('.btn-edit-shelter').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const capacity = this.dataset.capacity;
            const lat = this.dataset.lat;
            const lng = this.dataset.lng;
            const food = this.dataset.food;
            const water = this.dataset.water;
            const firstaid = this.dataset.firstaid;
            
            openEditModal(id, name, capacity, lat, lng, food, water, firstaid);
        });
    });

    // --- 6. Delete Button Handler ---
    document.querySelectorAll('.btn-delete-shelter').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            
            openDeleteModal(id, name);
        });
    });

    // --- 7. Location View Button Handler ---
    document.querySelectorAll('.btn-view-location').forEach(btn => {
        btn.addEventListener('click', function() {
            const lat = this.dataset.lat;
            const lng = this.dataset.lng;
            openLocationModal(lat, lng);
        });
    });

    // --- 8. Edit Form Submission ---
    document.getElementById('edit-shelter-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const id = document.getElementById('edit-shelter-id').value;
        const name = document.getElementById('edit-name').value;
        const capacity = document.getElementById('edit-capacity').value;
        const lat = document.getElementById('edit-latitude-input').value;
        const lng = document.getElementById('edit-longitude-input').value;
        const food = document.getElementById('edit-food-packs').value;
        const water = document.getElementById('edit-water-liters').value;
        const firstaid = document.getElementById('edit-first-aid-kits').value;
        
        if (!lat || !lng) {
            alert('Please click on the map to set the shelter location.');
            return;
        }
        
        const formData = new FormData();
        formData.append('shelter_id', id);
        formData.append('name', name);
        formData.append('capacity', capacity);
        formData.append('latitude', lat);
        formData.append('longitude', lng);
        formData.append('food_packs', food);
        formData.append('water_liters', water);
        formData.append('first_aid_kits', firstaid);

        fetch('../backend/api/admin/shelters_update.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showAlert('Shelter updated successfully', 'success');
                closeEditModal();
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert('Failed to update shelter: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showAlert('Network error: ' + error.message, 'error');
        });
    });

    // --- 9. Delete Confirmation ---
    document.getElementById('confirm-delete-btn').addEventListener('click', function() {
        if (currentDeleteId) {
            deleteShelter(currentDeleteId);
        }
    });
});

// Status update function
function updateShelterStatus(shelterId, newStatus, dropdown) {
    fetch('../backend/api/admin/shelters_update.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            shelter_id: shelterId,
            name: dropdown.closest('tr').children[1].textContent,
            capacity: dropdown.closest('tr').children[2].textContent,
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
function openEditModal(id, name, capacity, lat, lng, food, water, firstaid) {
    document.getElementById('edit-shelter-id').value = id;
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-capacity').value = capacity;
    document.getElementById('edit-food-packs').value = food || 0;
    document.getElementById('edit-water-liters').value = water || 0;
    document.getElementById('edit-first-aid-kits').value = firstaid || 0;
    
    document.getElementById('editShelterModal').style.display = 'flex';
    
    setTimeout(() => {
        if (!editMap) {
            editMap = L.map('edit-map-container').setView([-1.286389, 36.817223], 6);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(editMap);
            
            editMap.on('click', function(e) {
                const newLat = e.latlng.lat;
                const newLng = e.latlng.lng;

                if (editMarker) {
                    editMarker.setLatLng(e.latlng);
                } else {
                    editMarker = L.marker(e.latlng).addTo(editMap);
                }

                document.getElementById('edit-latitude-input').value = newLat;
                document.getElementById('edit-longitude-input').value = newLng;
            });
        }
        
        editMap.invalidateSize();
        
        // Load existing location if available
        if (lat && lng && lat !== '' && lng !== '') {
            const latNum = parseFloat(lat);
            const lngNum = parseFloat(lng);
            
            if (!isNaN(latNum) && !isNaN(lngNum)) {
                editMap.setView([latNum, lngNum], 12);
                
                if (editMarker) {
                    editMarker.setLatLng([latNum, lngNum]);
                } else {
                    editMarker = L.marker([latNum, lngNum]).addTo(editMap);
                }
                
                document.getElementById('edit-latitude-input').value = latNum;
                document.getElementById('edit-longitude-input').value = lngNum;
            }
        }
    }, 100);
}

function closeEditModal() {
    document.getElementById('editShelterModal').style.display = 'none';
    if (editMarker) {
        editMap.removeLayer(editMarker);
        editMarker = null;
    }
    document.getElementById('edit-latitude-input').value = '';
    document.getElementById('edit-longitude-input').value = '';
}

// Location modal functions
function openLocationModal(lat, lng) {
    document.getElementById('locationModal').style.display = 'flex';
    
    setTimeout(() => {
        if (!locationMap) {
            locationMap = L.map('location-map-container').setView([-1.286389, 36.817223], 6);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(locationMap);
        }
        
        locationMap.invalidateSize();
        
        if (lat && lng && lat !== '' && lng !== '') {
            const latNum = parseFloat(lat);
            const lngNum = parseFloat(lng);
            
            if (!isNaN(latNum) && !isNaN(lngNum)) {
                locationMap.setView([latNum, lngNum], 15);
                L.marker([latNum, lngNum]).addTo(locationMap);
            }
        }
    }, 100);
}

function closeLocationModal() {
    document.getElementById('locationModal').style.display = 'none';
    if (locationMap) {
        locationMap.eachLayer(layer => {
            if (layer instanceof L.Marker) {
                locationMap.removeLayer(layer);
            }
        });
    }
}

// Delete modal functions
function openDeleteModal(id, name) {
    currentDeleteId = id;
    document.getElementById('delete-shelter-name').textContent = name;
    document.getElementById('deleteConfirmModal').style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('deleteConfirmModal').style.display = 'none';
    currentDeleteId = null;
}

// API functions
function updateShelter(id, name, capacity, lat, lng, food, water, firstaid) {
    const formData = new FormData();
    formData.append('shelter_id', id);
    formData.append('name', name);
    formData.append('capacity', capacity);
    formData.append('latitude', lat);
    formData.append('longitude', lng);
    formData.append('food_packs', food);
    formData.append('water_liters', water);
    formData.append('first_aid_kits', firstaid);

    fetch('../backend/api/admin/shelters_update.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showAlert('Shelter updated successfully', 'success');
            closeEditModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert('Failed to update shelter: ' + data.message, 'error');
        }
    })
    .catch(error => {
        showAlert('Network error: ' + error.message, 'error');
    });
}

function deleteShelter(id) {
    fetch('../backend/api/admin/shelters_delete.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            shelter_id: id
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showAlert('Shelter deleted successfully', 'success');
            closeDeleteModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert('Failed to delete shelter: ' + data.message, 'error');
        }
    })
    .catch(error => {
        showAlert('Network error: ' + error.message, 'error');
    });
}

// Utility functions
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
.supply-inputs {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 16px;
}

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

.location-cell, .actions-cell {
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