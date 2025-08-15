$(document).ready(function() {

    // Function to load content into the main area
    function loadContent(page) {
        // Show a loading indicator
        $('#content-loader').html('<p>Loading...</p>').show();

        $.ajax({
            url: page + '.php', // The PHP file to fetch (e.g., 'dashboard.php')
            type: 'GET',
            success: function(response) {
                // Load the response HTML into the content area
                $('#content-loader').html(response);
            },
            error: function() {
                // Show an error message if the page fails to load
                $('#content-loader').html('<p>Error: Could not load content. Please try again.</p>');
            }
        });
    }

    // Handle clicks on the navigation links
    $('.sidebar-nav .nav-link').on('click', function(e) {
        e.preventDefault(); // Prevent the default link behavior

        // Get the page to load from the 'data-page' attribute
        const page = $(this).data('page');

        // Remove the 'active' class from all links and add it to the clicked one
        $('.sidebar-nav .nav-link').removeClass('active');
        $(this).addClass('active');

        // Load the new content
        loadContent(page);
    });

    // Load the default page (dashboard) when the application starts
    loadContent('dashboard');

});