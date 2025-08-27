<?php
// Start the session
session_start();

// Include necessary files
require_once '../../private/gws-universal-config.php';
require_once '../../private/gws-universal-functions.php';

// Initialize admin template parameters
$selected = 'chat';
$selected_child = 'dashboard';
$title = 'Chat System Dashboard';

// Check if user is logged in and has permission
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../login.php');
    exit;
}

// Check admin role permissions
if (!in_array($_SESSION['role'], ['Admin', 'Super Admin', 'Manager'])) {
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
    error_log("Chat System Error: " . $e->getMessage());
    $error_message = "Database connection error.";
}

// Get chat system statistics
try {
    // Get total chat sessions today
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM chat_sessions 
        WHERE DATE(created_at) = CURDATE()
    ");
    $stmt->execute();
    $sessions_today = $stmt->fetchColumn() ?: 0;
    
    // Get active chat sessions
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM chat_sessions 
        WHERE status = 'active' 
        AND last_activity > DATE_SUB(NOW(), INTERVAL 30 MINUTE)
    ");
    $stmt->execute();
    $active_sessions = $stmt->fetchColumn() ?: 0;
    
    // Get total messages today
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM chat_messages 
        WHERE DATE(created_at) = CURDATE()
    ");
    $stmt->execute();
    $messages_today = $stmt->fetchColumn() ?: 0;
    
    // Get unread messages
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM chat_messages 
        WHERE is_read = 0 
        AND sender_type = 'customer'
    ");
    $stmt->execute();
    $unread_messages = $stmt->fetchColumn() ?: 0;
    
} catch(PDOException $e) {
    // If tables don't exist, set defaults
    $sessions_today = 0;
    $active_sessions = 0;
    $messages_today = 0;
    $unread_messages = 0;
    $tables_exist = false;
}

// Get recent chat sessions
try {
    $stmt = $pdo->prepare("
        SELECT cs.*, 
               COUNT(cm.id) as message_count,
               MAX(cm.created_at) as last_message
        FROM chat_sessions cs 
        LEFT JOIN chat_messages cm ON cs.id = cm.session_id 
        WHERE cs.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        GROUP BY cs.id 
        ORDER BY cs.last_activity DESC 
        LIMIT 10
    ");
    $stmt->execute();
    $recent_sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $recent_sessions = [];
}

// Include the admin template
include '../assets/includes/main.php';
?>

<div class="content">
    <h2>Chat System Dashboard</h2>
    <p>Manage live chat sessions, messages, and customer support interactions.</p>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <span class="icon">⚠️</span>
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>
    
    <!-- Stats Cards -->
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-icon">💬</div>
            <div class="stat-content">
                <h3><?= number_format($sessions_today) ?></h3>
                <p>Sessions Today</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">🟢</div>
            <div class="stat-content">
                <h3><?= number_format($active_sessions) ?></h3>
                <p>Active Sessions</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">📝</div>
            <div class="stat-content">
                <h3><?= number_format($messages_today) ?></h3>
                <p>Messages Today</p>
            </div>
        </div>
        
        <div class="stat-card urgent">
            <div class="stat-icon">🔔</div>
            <div class="stat-content">
                <h3><?= number_format($unread_messages) ?></h3>
                <p>Unread Messages</p>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="actions">
        <h3>Quick Actions</h3>
        <div class="action-buttons">
            <a href="chat_sessions.php" class="button">
                <span class="icon">👥</span>
                View All Sessions
            </a>
            <a href="chat_messages.php" class="button">
                <span class="icon">💬</span>
                View Messages
            </a>
            <a href="settings.php" class="button">
                <span class="icon">⚙️</span>
                Chat Settings
            </a>
            <a href="operators.php" class="button">
                <span class="icon">👨‍💼</span>
                Manage Operators
            </a>
        </div>
    </div>
    
    <!-- Recent Chat Sessions -->
    <div class="recent-activity">
        <h3>Recent Chat Sessions</h3>
        <?php if (!empty($recent_sessions)): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Session ID</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Messages</th>
                            <th>Last Activity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_sessions as $session): ?>
                            <tr>
                                <td>#<?= $session['id'] ?></td>
                                <td>
                                    <?= htmlspecialchars($session['customer_name'] ?? 'Anonymous') ?><br>
                                    <small><?= htmlspecialchars($session['customer_email'] ?? 'No email') ?></small>
                                </td>
                                <td>
                                    <span class="status-badge status-<?= strtolower($session['status']) ?>">
                                        <?= ucfirst($session['status']) ?>
                                    </span>
                                </td>
                                <td><?= $session['message_count'] ?: 0 ?></td>
                                <td>
                                    <?= $session['last_activity'] ? time_elapsed_string($session['last_activity']) : 'Never' ?>
                                </td>
                                <td>
                                    <a href="chat_session.php?id=<?= $session['id'] ?>" class="button button-small">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-data">
                <span class="icon">💬</span>
                <p>No recent chat sessions found.</p>
                <small>Chat sessions will appear here when customers start conversations.</small>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Chat System Status -->
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
                <span>Chat Widget</span>
                <span class="status-text">Ready</span>
            </div>
            
            <div class="status-item">
                <span class="status-dot green"></span>
                <span>Admin Interface</span>
                <span class="status-text">Active</span>
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

.stat-card.urgent {
    border-left: 4px solid #e74c3c;
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
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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

.recent-activity, .system-status {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8em;
    font-weight: bold;
    text-transform: uppercase;
}

.status-active { background: #d4edda; color: #155724; }
.status-waiting { background: #fff3cd; color: #856404; }
.status-ended { background: #f8d7da; color: #721c24; }

.status-grid {
    display: grid;
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
</style>

<?php include '../assets/includes/footer.php'; ?>
