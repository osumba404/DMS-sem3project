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
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>DMS Admin</h3>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <!-- The links now point to index.php with a ?page= parameter -->
                    <li><a href="index.php?page=dashboard" class="<?php echo ($page === 'dashboard') ? 'active' : ''; ?>">Dashboard</a></li>
                    <li><a href="index.php?page=disasters" class="<?php echo ($page === 'disasters') ? 'active' : ''; ?>">Disasters</a></li>
                    <li><a href="index.php?page=shelters" class="<?php echo ($page === 'shelters') ? 'active' : ''; ?>">Shelters</a></li>
                    <li><a href="index.php?page=responders" class="<?php echo ($page === 'responders') ? 'active' : ''; ?>">Responders</a></li>
                    <li><a href="index.php?page=users" class="<?php echo ($page === 'users') ? 'active' : ''; ?>">Public Users</a></li>
                    <li><a href="index.php?page=broadcast" class="<?php echo ($page === 'broadcast') ? 'active' : ''; ?>">Broadcast Message</a></li>
                    <li><a href="index.php?page=reports" class="<?php echo ($page === 'reports') ? 'active' : ''; ?>">Reports</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="main-header">
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></h1>
                <a href="logout.php" class="logout-btn">Logout</a>
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

    <!-- We no longer need the JavaScript for page loading, but we can keep it for modals later -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- <script src="assets/js/script.js"></script> --> <!-- Commented out for now -->
</body>
</html>