<?php
// Helper function to call our own API
function call_api($endpoint) {
    // IMPORTANT: Replace with the actual URL of your backend in a real deployment
    $api_url = "http://localhost/DMS-sem3project/backend/api/admin/" . $endpoint;
    $response = @file_get_contents($api_url);
    if ($response === FALSE) {
        return null;
    }
    return json_decode($response, true);
}

$stats_data = call_api('get_dashboard_stats.php');

if ($stats_data && $stats_data['status'] === 'success') {
    $stats = $stats_data['data'];
} else {
    // Set default values if API call fails
    $stats = [
        'total_users' => 'N/A',
        'active_disasters' => 'N/A',
        'open_shelters' => 'N/A',
        'total_occupancy' => 'N/A',
        'total_responders' => 'N/A'
    ];
}
?>

<div class="dashboard-container">
    <h2><i class="fas fa-chart-pie"></i> System Overview</h2>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3>Total Users</h3>
                <p><?php echo htmlspecialchars($stats['total_users']); ?></p>
                <span class="stat-trend positive">
                    <i class="fas fa-arrow-up"></i> +12% this month
                </span>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon disaster">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <h3>Active Disasters</h3>
                <p><?php echo htmlspecialchars($stats['active_disasters']); ?></p>
                <span class="stat-trend <?php echo $stats['active_disasters'] > 0 ? 'negative' : 'neutral'; ?>">
                    <i class="fas fa-<?php echo $stats['active_disasters'] > 0 ? 'exclamation' : 'check'; ?>"></i> 
                    <?php echo $stats['active_disasters'] > 0 ? 'Requires attention' : 'All clear'; ?>
                </span>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon shelter">
                <i class="fas fa-home"></i>
            </div>
            <div class="stat-content">
                <h3>Open Shelters</h3>
                <p><?php echo htmlspecialchars($stats['open_shelters']); ?></p>
                <span class="stat-trend positive">
                    <i class="fas fa-shield-alt"></i> Ready for emergencies
                </span>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon occupancy">
                <i class="fas fa-bed"></i>
            </div>
            <div class="stat-content">
                <h3>Total Occupancy</h3>
                <p><?php echo htmlspecialchars($stats['total_occupancy']); ?></p>
                <span class="stat-trend neutral">
                    <i class="fas fa-info-circle"></i> Current capacity
                </span>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon responders">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="stat-content">
                <h3>Total Responders</h3>
                <p><?php echo htmlspecialchars($stats['total_responders']); ?></p>
                <span class="stat-trend positive">
                    <i class="fas fa-check-circle"></i> Active & ready
                </span>
            </div>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="quick-actions">
        <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
        <div class="action-grid">
            <a href="index.php?page=disasters" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <div class="action-content">
                    <h4>Create Disaster Alert</h4>
                    <p>Issue new emergency alerts</p>
                </div>
            </a>
            
            <a href="index.php?page=broadcast" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-broadcast-tower"></i>
                </div>
                <div class="action-content">
                    <h4>Send Broadcast</h4>
                    <p>Mass communication to users</p>
                </div>
            </a>
            
            <a href="index.php?page=shelters" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-home"></i>
                </div>
                <div class="action-content">
                    <h4>Manage Shelters</h4>
                    <p>Update shelter capacity & status</p>
                </div>
            </a>
            
            <a href="index.php?page=responders" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="action-content">
                    <h4>Add Responder</h4>
                    <p>Register new emergency responders</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="recent-activity">
        <h3><i class="fas fa-clock"></i> Recent Activity</h3>
        <div class="activity-list">
            <div class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="activity-content">
                    <h4>New disaster alert created</h4>
                    <p>Flood warning issued for Embakasi area</p>
                    <span class="activity-time">2 hours ago</span>
                </div>
            </div>
            
            <div class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-broadcast-tower"></i>
                </div>
                <div class="activity-content">
                    <h4>Emergency broadcast sent</h4>
                    <p>Evacuation notice to 1,250 users</p>
                    <span class="activity-time">4 hours ago</span>
                </div>
            </div>
            
            <div class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="activity-content">
                    <h4>New responder registered</h4>
                    <p>John Doe added to Red Cross team</p>
                    <span class="activity-time">6 hours ago</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Enhanced Dashboard Styles */
.dashboard-container h2 {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 2rem;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.stat-card {
    background: var(--white);
    padding: 2rem;
    border-radius: 1rem;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--border-light);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--primary-gradient);
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.stat-icon {
    width: 4rem;
    height: 4rem;
    border-radius: 1rem;
    background: var(--primary-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-size: 1.5rem;
    flex-shrink: 0;
}

.stat-icon.disaster { background: var(--danger-gradient); }
.stat-icon.shelter { background: var(--success-gradient); }
.stat-icon.occupancy { background: var(--warning-gradient); }
.stat-icon.responders { background: var(--secondary-gradient); }

.stat-content {
    flex: 1;
}

.stat-content h3 {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.5rem;
}

.stat-content p {
    font-size: 2.5rem;
    font-weight: 800;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin: 0 0 0.5rem 0;
    line-height: 1;
}

.stat-trend {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.stat-trend.positive { color: #38a169; }
.stat-trend.negative { color: #e53e3e; }
.stat-trend.neutral { color: var(--text-secondary); }

/* Quick Actions */
.quick-actions {
    margin-bottom: 3rem;
}

.quick-actions h3 {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 1.5rem;
}

.action-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

.action-card {
    background: var(--white);
    padding: 1.5rem;
    border-radius: 1rem;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-light);
    transition: all 0.3s ease;
    text-decoration: none;
    color: inherit;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.action-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    text-decoration: none;
    color: inherit;
}

.action-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 0.75rem;
    background: var(--primary-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-size: 1.25rem;
    flex-shrink: 0;
}

.action-content h4 {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 0.25rem 0;
}

.action-content p {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin: 0;
}

/* Recent Activity */
.recent-activity h3 {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 1.5rem;
}

.activity-list {
    background: var(--white);
    border-radius: 1rem;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-light);
    overflow: hidden;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-light);
    transition: all 0.2s ease;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-item:hover {
    background: var(--bg-secondary);
}

.activity-item .activity-icon {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.5rem;
    background: var(--bg-secondary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-secondary);
    flex-shrink: 0;
}

.activity-content h4 {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 0.25rem 0;
}

.activity-content p {
    font-size: 0.75rem;
    color: var(--text-secondary);
    margin: 0 0 0.25rem 0;
}

.activity-time {
    font-size: 0.75rem;
    color: var(--text-light);
    font-weight: 500;
}
</style>