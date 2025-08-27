<?php
// Start the session
session_start();

// Include necessary files
require_once '../../private/gws-universal-config.php';
require_once '../../private/gws-universal-functions.php';

// Initialize admin template parameters
$selected = 'events';
$selected_child = 'dashboard';
$title = 'Events System Dashboard';

// Check if user is logged in and has permission
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../login.php');
    exit;
}

// Check admin role permissions
if (!in_array($_SESSION['role'], ['Admin', 'Super Admin', 'Manager', 'Editor'])) {
    header('Location: ../index.php');
    exit;
}

// Database connection
try {
    $stmt = $pdo->prepare('SELECT * FROM accounts WHERE id = ?');
    $stmt->execute([$_SESSION['id']]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$account) {
        header('Location: ../login.php');
        exit;
    }
} catch(PDOException $e) {
    error_log("Events System Error: " . $e->getMessage());
    $error_message = "Database connection error.";
}

// Get events system statistics
try {
    // Get total events
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM events");
    $stmt->execute();
    $total_events = $stmt->fetchColumn() ?: 0;
    
    // Get upcoming events
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM events 
        WHERE datestart > NOW() 
        AND status = 'published'
    ");
    $stmt->execute();
    $upcoming_events = $stmt->fetchColumn() ?: 0;
    
    // Get events today
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM events 
        WHERE DATE(datestart) = CURDATE() 
        AND status = 'published'
    ");
    $stmt->execute();
    $events_today = $stmt->fetchColumn() ?: 0;
    
    // Get total registrations
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM event_registrations 
        WHERE status IN ('registered', 'confirmed')
    ");
    $stmt->execute();
    $total_registrations = $stmt->fetchColumn() ?: 0;
    
} catch(PDOException $e) {
    // If tables don't exist, set defaults
    $total_events = 0;
    $upcoming_events = 0;
    $events_today = 0;
    $total_registrations = 0;
    $tables_exist = false;
}

// Get recent events
try {
    $stmt = $pdo->prepare("
        SELECT e.*, 
               ec.name as category_name,
               COUNT(er.id) as registration_count
        FROM events e 
        LEFT JOIN event_categories ec ON e.category_id = ec.id
        LEFT JOIN event_registrations er ON e.id = er.event_id 
            AND er.status IN ('registered', 'confirmed')
        WHERE e.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY e.id 
        ORDER BY e.submit_date DESC 
        LIMIT 10
    ");
    $stmt->execute();
    $recent_events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $recent_events = [];
}

// Get upcoming events
try {
    $stmt = $pdo->prepare("
        SELECT e.*, 
               ec.name as category_name,
               COUNT(er.id) as registration_count
        FROM events e 
        LEFT JOIN event_categories ec ON e.category_id = ec.id
        LEFT JOIN event_registrations er ON e.id = er.event_id 
            AND er.status IN ('registered', 'confirmed')
        WHERE e.datestart > NOW() 
        AND e.status = 'published'
        GROUP BY e.id 
        ORDER BY e.datestart ASC 
        LIMIT 5
    ");
    $stmt->execute();
    $upcoming_events_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $upcoming_events_list = [];
}

// Include the admin template
include '../assets/includes/main.php';
?>

<div class="content">
    <h2>Events System Dashboard</h2>
    <p>Manage events, registrations, and calendar scheduling for your organization.</p>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <span class="icon">⚠️</span>
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>
    
    <!-- Stats Cards -->
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-icon">📅</div>
            <div class="stat-content">
                <h3><?= number_format($total_events) ?></h3>
                <p>Total Events</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">🔮</div>
            <div class="stat-content">
                <h3><?= number_format($upcoming_events) ?></h3>
                <p>Upcoming Events</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">📍</div>
            <div class="stat-content">
                <h3><?= number_format($events_today) ?></h3>
                <p>Events Today</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-content">
                <h3><?= number_format($total_registrations) ?></h3>
                <p>Total Registrations</p>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="actions">
        <h3>Quick Actions</h3>
        <div class="action-buttons">
            <a href="event.php" class="button">
                <span class="icon">➕</span>
                Create Event
            </a>
            <a href="events.php" class="button">
                <span class="icon">📋</span>
                View All Events
            </a>
            <a href="registrations.php" class="button">
                <span class="icon">📝</span>
                Manage Registrations
            </a>
            <a href="calendar.php" class="button">
                <span class="icon">📅</span>
                Calendar View
            </a>
            <a href="categories.php" class="button">
                <span class="icon">🏷️</span>
                Event Categories
            </a>
            <a href="settings.php" class="button">
                <span class="icon">⚙️</span>
                Events Settings
            </a>
        </div>
    </div>
    
    <div class="dashboard-grid">
        <!-- Upcoming Events -->
        <div class="dashboard-section">
            <h3>Upcoming Events</h3>
            <?php if (!empty($upcoming_events_list)): ?>
                <div class="events-list">
                    <?php foreach ($upcoming_events_list as $event): ?>
                        <div class="event-item">
                            <div class="event-date">
                                <span class="day"><?= date('d', strtotime($event['datestart'])) ?></span>
                                <span class="month"><?= date('M', strtotime($event['datestart'])) ?></span>
                            </div>
                            <div class="event-details">
                                <h4><?= htmlspecialchars($event['title']) ?></h4>
                                <p class="event-time">
                                    <?= date('g:i A', strtotime($event['datestart'])) ?>
                                    <?php if ($event['location']): ?>
                                        • <?= htmlspecialchars($event['location']) ?>
                                    <?php endif; ?>
                                </p>
                                <?php if ($event['category_name']): ?>
                                    <span class="category-tag"><?= htmlspecialchars($event['category_name']) ?></span>
                                <?php endif; ?>
                                <p class="registration-count">
                                    <?= $event['registration_count'] ?> registered
                                </p>
                            </div>
                            <div class="event-actions">
                                <a href="event.php?id=<?= $event['id'] ?>" class="button button-small">Edit</a>
                                <a href="event_view.php?id=<?= $event['id'] ?>" class="button button-small">View</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-data">
                    <span class="icon">📅</span>
                    <p>No upcoming events scheduled.</p>
                    <a href="event.php" class="button">Create Your First Event</a>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Recent Activity -->
        <div class="dashboard-section">
            <h3>Recent Events</h3>
            <?php if (!empty($recent_events)): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Registrations</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_events as $event): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($event['title']) ?></strong><br>
                                        <small><?= htmlspecialchars($event['category_name'] ?? 'No category') ?></small>
                                    </td>
                                    <td>
                                        <?= date('M j, Y', strtotime($event['datestart'])) ?><br>
                                        <small><?= date('g:i A', strtotime($event['datestart'])) ?></small>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?= strtolower($event['status']) ?>">
                                            <?= ucfirst($event['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= $event['registration_count'] ?>
                                        <?php if ($event['max_attendees']): ?>
                                            / <?= $event['max_attendees'] ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="event.php?id=<?= $event['id'] ?>" class="button button-small">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-data">
                    <span class="icon">📋</span>
                    <p>No recent events found.</p>
                    <small>Events will appear here as they are created.</small>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- System Status -->
    <div class="system-status">
        <h3>System Status</h3>
        <div class="status-grid">
            <div class="status-item">
                <span class="status-dot <?= isset($tables_exist) && $tables_exist === false ? 'red' : 'green' ?>"></span>
                <span>Database Tables</span>
                <span class="status-text">
                    <?= isset($tables_exist) && $tables_exist === false ? 'Need Setup' : 'Ready' ?>
                </span>
            </div>
            
            <div class="status-item">
                <span class="status-dot green"></span>
                <span>Event Registration</span>
                <span class="status-text">Active</span>
            </div>
            
            <div class="status-item">
                <span class="status-dot green"></span>
                <span>Email Notifications</span>
                <span class="status-text">Ready</span>
            </div>
            
            <div class="status-item">
                <span class="status-dot green"></span>
                <span>Calendar Integration</span>
                <span class="status-text">Available</span>
            </div>
        </div>
    </div>
</div>

<style>
.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-icon {
    font-size: 2em;
    opacity: 0.8;
}

.stat-content h3 {
    margin: 0;
    font-size: 1.8em;
    color: #2c3e50;
}

.stat-content p {
    margin: 5px 0 0 0;
    color: #7f8c8d;
    font-size: 0.9em;
}

.actions {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.action-buttons {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.action-buttons .button {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 20px;
    background: #3498db;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    transition: background 0.3s;
}

.action-buttons .button:hover {
    background: #2980b9;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 30px;
}

.dashboard-section {
    background: white;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.events-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.event-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    border: 1px solid #eee;
    border-radius: 8px;
    transition: box-shadow 0.3s;
}

.event-item:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.event-date {
    text-align: center;
    min-width: 50px;
}

.event-date .day {
    display: block;
    font-size: 1.5em;
    font-weight: bold;
    color: #2c3e50;
}

.event-date .month {
    display: block;
    font-size: 0.8em;
    color: #7f8c8d;
    text-transform: uppercase;
}

.event-details {
    flex: 1;
}

.event-details h4 {
    margin: 0 0 5px 0;
    color: #2c3e50;
}

.event-time {
    margin: 5px 0;
    color: #666;
    font-size: 0.9em;
}

.category-tag {
    background: #ecf0f1;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8em;
    color: #2c3e50;
}

.registration-count {
    margin: 5px 0 0 0;
    font-size: 0.9em;
    color: #27ae60;
    font-weight: 500;
}

.event-actions {
    display: flex;
    gap: 5px;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8em;
    font-weight: bold;
    text-transform: uppercase;
}

.status-draft { background: #f8d7da; color: #721c24; }
.status-published { background: #d4edda; color: #155724; }
.status-cancelled { background: #f8d7da; color: #721c24; }
.status-postponed { background: #fff3cd; color: #856404; }

.system-status {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.status-item {
    display: flex;
    align-items: center;
    gap: 10px;
}

.status-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.status-dot.green { background: #27ae60; }
.status-dot.red { background: #e74c3c; }

.no-data {
    text-align: center;
    padding: 40px;
    color: #7f8c8d;
}

.no-data .icon {
    font-size: 3em;
    display: block;
    margin-bottom: 15px;
}

.alert {
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

@media (max-width: 768px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        grid-template-columns: 1fr;
    }
    
    .event-item {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<?php include '../assets/includes/footer.php'; ?>
