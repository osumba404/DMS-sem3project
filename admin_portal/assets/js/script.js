$(document).ready(function() {
    const API_BASE_URL = 'http://localhost/DMS-sem3project/backend/api/admin/';
    let currentPage = 'dashboard'; // Keep track of the current page

    // --- Core Content Loading ---
    function loadContent(page) {
        currentPage = page;
        $('#content-loader').html('<p>Loading...</p>').load(page + '.php', function(response, status, xhr) {
            if (status == "error") {
                $('#content-loader').html('<p>Error: Could not load content. Please try again.</p>');
            }
        });
    }

    $('.sidebar-nav .nav-link').on('click', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        $('.sidebar-nav .nav-link').removeClass('active');
        $(this).addClass('active');
        loadContent(page);
    });

    loadContent('dashboard');

    // --- Modal Handling ---
    function showModal(title, body) {
        $('#modal-title').text(title);
        $('#modal-body').html(body);
        $('#form-modal').fadeIn();
    }

    function hideModal() {
        $('#form-modal').fadeOut();
        $('#modal-body').empty(); // Clear previous form
    }

    $('#modal-close-btn').on('click', hideModal);

    // --- Generic API Call Function ---
    function callApi(endpoint, method, data) {
        return $.ajax({
            url: API_BASE_URL + endpoint,
            type: method,
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function(response) {
                alert(response.message);
                hideModal();
                loadContent(currentPage); // Refresh the current view
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'An unknown error occurred.';
                alert('Error: ' + errorMsg);
            }
        });
    }

    // --- Event Delegation for All Dynamic Buttons ---
    const contentLoader = $('#content-loader');
    const modalBody = $('#modal-body');

    // ======================================================
    // DISASTER MANAGEMENT
    // ======================================================

    // Delete Disaster
    contentLoader.on('click', '.btn-delete-disaster', function() {
        const disasterId = $(this).data('id');
        if (confirm('Are you sure you want to delete this disaster?')) {
            callApi('disasters_delete.php', 'POST', { disaster_id: disasterId });
        }
    });

    // Show Create Disaster Form
    contentLoader.on('click', '#btn-create-disaster', function() {
        const formHtml = `
            <form id="form-disaster">
                <input type="hidden" name="disaster_id" value="">
                
                <!-- FIX: Added 'for' to label, 'id' and 'autocomplete' to input -->
                <div class="form-group">
                    <label for="disasterName">Name</label>
                    <input type="text" id="disasterName" name="name" autocomplete="off" required>
                </div>
                
                <!-- FIX: Added 'for' to label and 'id' to select -->
                <div class="form-group">
                    <label for="disasterType">Type</label>
                    <select id="disasterType" name="type" required>
                        <option value="Flood">Flood</option>
                        <option value="Earthquake">Earthquake</option>
                        <option value="Hurricane">Hurricane</option>
                        <option value="Wildfire">Wildfire</option>
                        <option value="Tsunami">Tsunami</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <!-- FIX: Added 'for' to label and 'id' to textarea -->
                <div class="form-group">
                    <label for="disasterWkt">Affected Area (WKT Polygon)</label>
                    <textarea id="disasterWkt" name="affected_area_wkt" rows="4" placeholder="e.g., POLYGON((...))" required></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Save Disaster</button>
            </form>`;
        showModal('Create New Disaster', formHtml);
    });

    // Submit Disaster Form (Create/Update)
    modalBody.on('submit', '#form-disaster', function(e) {
        e.preventDefault();
        const formData = {
            disaster_id: $(this).find('[name="disaster_id"]').val(),
            name: $(this).find('[name="name"]').val(),
            type: $(this).find('[name="type"]').val(),
            affected_area_wkt: $(this).find('[name="affected_area_wkt"]').val(),
            admin_id: 1 // Hardcoded for example; get from session in real app
        };
        const endpoint = formData.disaster_id ? 'disasters_update.php' : 'disasters_create.php';
        callApi(endpoint, 'POST', formData);
    });

    // ======================================================
    // SHELTER MANAGEMENT
    // ======================================================

    // Delete Shelter
    contentLoader.on('click', '.btn-delete-shelter', function() {
        const shelterId = $(this).data('id');
        if (confirm('Are you sure you want to delete this shelter?')) {
            callApi('shelters_delete.php', 'POST', { shelter_id: shelterId });
        }
    });

    // Show Create Shelter Form
    contentLoader.on('click', '#btn-create-shelter', function() {
        const formHtml = `
            <form id="form-shelter">
                <input type="hidden" name="shelter_id" value="">
                <div class="form-group"><label>Shelter Name</label><input type="text" name="name" required></div>
                <div class="form-group"><label>Latitude</label><input type="text" name="latitude" required></div>
                <div class="form-group"><label>Longitude</label><input type="text" name="longitude" required></div>
                <div class="form-group"><label>Capacity</label><input type="number" name="capacity" required></div>
                <div class="form-group"><label>Available Supplies (JSON)</label><textarea name="available_supplies" rows="3">{"food_packs": 0, "water_liters": 0}</textarea></div>
                <button type="submit" class="btn btn-primary">Save Shelter</button>
            </form>`;
        showModal('Create New Shelter', formHtml);
    });
    
    // Submit Shelter Form (Create/Update)
    modalBody.on('submit', '#form-shelter', function(e) {
        e.preventDefault();
        let supplies;
        try {
            supplies = JSON.parse($(this).find('[name="available_supplies"]').val());
        } catch (e) {
            alert('Invalid JSON format for supplies.');
            return;
        }
        const formData = {
            shelter_id: $(this).find('[name="shelter_id"]').val(),
            name: $(this).find('[name="name"]').val(),
            latitude: $(this).find('[name="latitude"]').val(),
            longitude: $(this).find('[name="longitude"]').val(),
            capacity: $(this).find('[name="capacity"]').val(),
            available_supplies: supplies
        };
        const endpoint = formData.shelter_id ? 'shelters_update.php' : 'shelters_create.php';
        callApi(endpoint, 'POST', formData);
    });

    // ======================================================
    // RESPONDER MANAGEMENT
    // ======================================================

    // Delete Responder
    contentLoader.on('click', '.btn-delete-responder', function() {
        const responderId = $(this).data('id');
        if (confirm('Are you sure you want to delete this responder?')) {
            callApi('responders_delete.php', 'POST', { responder_id: responderId });
        }
    });

    // Show Create Responder Form
    contentLoader.on('click', '#btn-create-responder', function() {
        const formHtml = `
            <form id="form-responder">
                <input type="hidden" name="responder_id" value="">
                <div class="form-group"><label>Full Name</label><input type="text" name="full_name" required></div>
                <div class="form-group"><label>Username</label><input type="text" name="username" required></div>
                <div class="form-group"><label>Password</label><input type="password" name="password" placeholder="Leave blank to keep unchanged on edit" required></div>
                <div class="form-group"><label>Team</label><input type="text" name="team" required></div>
                <button type="submit" class="btn btn-primary">Save Responder</button>
            </form>`;
        showModal('Create New Responder', formHtml);
    });

    // Submit Responder Form (Create/Update)
    modalBody.on('submit', '#form-responder', function(e) {
        e.preventDefault();
        const formData = {
            responder_id: $(this).find('[name="responder_id"]').val(),
            full_name: $(this).find('[name="full_name"]').val(),
            username: $(this).find('[name="username"]').val(),
            password: $(this).find('[name="password"]').val(),
            team: $(this).find('[name="team"]').val()
        };
        
        // For 'create' endpoint, password is required
        if (!formData.responder_id && !formData.password) {
            alert('Password is required for new responders.');
            return;
        }

        const endpoint = formData.responder_id ? 'responders_update.php' : 'responders_create.php';
        callApi(endpoint, 'POST', formData);
    });

    // NOTE: The 'Edit' functionality for all modules is simplified. A full implementation
    // would involve creating API endpoints like 'disasters_get_details.php?id=X', fetching
    // the data, and then populating the same form used for 'Create'. The generic submit
    // handler is already designed to call the correct 'update' endpoint if an ID is present.
});