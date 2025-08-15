<?php
// We can add session start and authentication checks here later.
// For example: include 'includes/auth_check.php';
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
                    <li><a href="#" class="nav-link active" data-page="dashboard">Dashboard</a></li>
                    <li><a href="#" class="nav-link" data-page="disasters">Disasters</a></li>
                    <li><a href="#" class="nav-link" data-page="shelters">Shelters</a></li>
                    <li><a href="#" class="nav-link" data-page="responders">Responders</a></li>
                    <li><a href="#" class="nav-link" data-page="users">Public Users</a></li>
                    <li><a href="#" class="nav-link" data-page="broadcast">Broadcast Message</a></li>
                    <li><a href="#" class="nav-link" data-page="reports">Reports</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="main-header">
                <h1>Welcome, Admin</h1>
                <a href="logout.php" class="logout-btn">Logout</a>
            </header>
            
            <!-- Dynamic content will be loaded here -->
            <div id="content-loader">
                <p>Loading...</p>
            </div>
        </main>
    </div>

    <!-- Include jQuery from a CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include your custom script file -->
    <script src="assets/js/script.js"></script>
</body>
</html>