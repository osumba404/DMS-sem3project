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


<!-- Leaflet.js and Leaflet-Draw library files -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

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



<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- 1. Initialize the Map ---
        const map = L.map('shelter-map-container').setView([-1.286389, 36.817223], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        let shelterMarker; // Variable to hold the marker

        // --- 2. Handle Map Clicks to Place/Move the Pin ---
        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;

            // If a marker already exists, move it. Otherwise, create a new one.
            if (shelterMarker) {
                shelterMarker.setLatLng(e.latlng);
            } else {
                shelterMarker = L.marker(e.latlng).addTo(map);
            }

            // Update the hidden input fields with the new coordinates
            document.getElementById('latitude-input').value = lat;
            document.getElementById('longitude-input').value = lng;
            
            // Hide the validation error message
            document.getElementById('map-pin-error').style.display = 'none';
        });

        // --- 3. Handle Form Submission ---
        document.getElementById('create-shelter-form').addEventListener('submit', function(e) {
            const latInput = document.getElementById('latitude-input');
            const mapError = document.getElementById('map-pin-error');
            
            // Check if the latitude input is empty (meaning no pin has been placed)
            if (latInput.value.trim() === '') {
                e.preventDefault(); // Stop the form from submitting
                mapError.style.display = 'block';
                alert('Please click on the map to place a pin for the shelter location.');
            }
        });
    });
</script>

<style>
    .supply-inputs {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 16px;
    }
</style>