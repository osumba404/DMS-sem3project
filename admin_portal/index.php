<?php
// This line protects the entire dashboard and starts the session.
require_once 'includes/auth_check.php';

// Determine which page to display based on a URL parameter.
// Default to the dashboard if no page is specified.
$page = $_GET['page'] ?? 'dashboard';

// Create a whitelist of allowed pages to prevent security issues.
$allowed_pages = ['dashboard', 'disasters', 'shelters', 'responders', 'users', 'broadcast', 'reports'];

// If the requested page is not in our whitelist, default to the dashboard.
if (!in_array($page, $allowed_pages)) {
    $page = 'dashboard';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - Disaster Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Premium Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>🌪️ DMS Admin</h3>
                <p>Disaster Management System</p>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="index.php?page=dashboard" class="<?php echo ($page === 'dashboard') ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a></li>
                    <li><a href="index.php?page=disasters" class="<?php echo ($page === 'disasters') ? 'active' : ''; ?>">
                        <i class="fas fa-exclamation-triangle"></i> Disasters
                    </a></li>
                    <li><a href="#" onclick="loadPage('shelters.php')">
                        <i class="fas fa-home"></i> Shelters
                    </a></li>
                    <li><a href="#" onclick="loadPage('responders.php')">
                        <i class="fas fa-users"></i> Responders
                    </a></li>
                    <li><a href="#" onclick="loadPage('reports.php')">
                        <i class="fas fa-file-alt"></i> Reports
                    </a></li>
                    <li><a href="#" onclick="loadPage('broadcast.php')">
                        <i class="fas fa-bullhorn"></i> Broadcast
                    </a></li>
                    <li><a href="index.php?page=users" class="<?php echo ($page === 'users') ? 'active' : ''; ?>">
                        <i class="fas fa-users"></i> Public Users
                    </a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="main-header">
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></h1>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </header>
            
            <div id="content-loader">
                <?php
                // Include the PHP file for the selected page.
                // This ensures the included file has access to the $_SESSION.
                include $page . '.php';
                ?>
            </div>
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Enhanced admin portal interactions
        $(document).ready(function() {
            // Smooth transitions for sidebar links
            $('.sidebar-nav a').on('click', function(e) {
                if (!$(this).hasClass('active')) {
                    $('.sidebar-nav a').removeClass('active');
                    $(this).addClass('active');
                }
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });
    </script>
</body>
</html>