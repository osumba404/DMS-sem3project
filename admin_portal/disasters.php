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

<!-- Leaflet.js and Leaflet-Draw library files -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- 1. Initialize the Map ---
        // Centered on a default location (e.g., Nairobi)
        const map = L.map('map-container').setView([-1.286389, 36.817223], 6);

        // Use OpenStreetMap as the tile layer (free and open-source)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // --- 2. Initialize the Drawing Feature ---
        const drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        const drawControl = new L.Control.Draw({
            draw: {
                polygon: true, // Only allow drawing polygons
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
            
            // Clear any previously drawn layers
            drawnItems.clearLayers();
            drawnItems.addLayer(layer);

            // Convert the Leaflet layer to GeoJSON, then to WKT format
            const geoJSON = layer.toGeoJSON();
            const coordinates = geoJSON.geometry.coordinates[0]; // Get the array of coordinates
            
            // Format for WKT: "POLYGON((lng1 lat1, lng2 lat2, ...))"
            let wktString = 'POLYGON((';
            coordinates.forEach(function(coord, index) {
                // GeoJSON is [lng, lat], which is perfect for us
                wktString += coord[0] + ' ' + coord[1];
                if (index < coordinates.length - 1) {
                    wktString += ', ';
                }
            });
            wktString += '))';

            // Set the value of our hidden input field
            document.getElementById('wkt-input').value = wktString;
            
            // Hide the validation error message if it was visible
            document.getElementById('map-error').style.display = 'none';

            console.log('Generated WKT:', wktString); // For debugging
        });

        // --- 4. Handle Form Submission ---
        document.getElementById('create-disaster-form').addEventListener('submit', function(e) {
            const wktInput = document.getElementById('wkt-input');
            const mapError = document.getElementById('map-error');
            
            // Check if the WKT input is empty
            if (wktInput.value.trim() === '') {
                // If it's empty, prevent the form from submitting
                e.preventDefault();
                // Show the error message
                mapError.style.display = 'block';
                alert('Please draw the affected area on the map before saving.');
            } else {
                // If it's not empty, hide the error and let the form submit
                mapError.style.display = 'none';
            }
        });
    });
</script>