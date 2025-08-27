<?php
// Start the session
session_start();

// Include necessary files
require_once '../../private/gws-universal-config.php';
require_once '../../private/gws-universal-functions.php';

// Initialize admin template parameters
$selected = 'chat';
$selected_child = 'operators';
$title = 'Chat Operators';

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

$success_message = '';
$error_message = '';

// Handle actions
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'toggle_chat':
            if (isset($_GET['user_id'])) {
                try {
                    $user_id = (int)$_GET['user_id'];
                    
                    // Check current chat operator status
                    $stmt = $pdo->prepare("SELECT chat_operator FROM accounts WHERE id = ?");
                    $stmt->execute([$user_id]);
                    $current_status = $stmt->fetchColumn();
                    
                    // Toggle status
                    $new_status = $current_status ? 0 : 1;
                    
                    $stmt = $pdo->prepare("UPDATE accounts SET chat_operator = ? WHERE id = ?");
                    $stmt->execute([$new_status, $user_id]);
                    
                    $success_message = $new_status ? "User enabled as chat operator." : "User disabled as chat operator.";
                    
                } catch(PDOException $e) {
                    $error_message = "Error updating operator status: " . $e->getMessage();
                }
            }
            break;
    }
}

// Get all users with their chat operator status
try {
    $stmt = $pdo->prepare("
        SELECT id, username, email, role, 
               COALESCE(chat_operator, 0) as chat_operator,
               last_seen_date,
               registration_date
        FROM accounts 
        WHERE role IN ('Admin', 'Super Admin', 'Manager', 'Operator', 'Support')
        ORDER BY role, username
    ");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get chat operator statistics
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_operators,
            COUNT(CASE WHEN last_seen_date > DATE_SUB(NOW(), INTERVAL 1 HOUR) THEN 1 END) as online_operators
        FROM accounts 
        WHERE chat_operator = 1
    ");
    $stmt->execute();
    $operator_stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    $error_message = "Error loading operators: " . $e->getMessage();
    $users = [];
    $operator_stats = ['total_operators' => 0, 'online_operators' => 0];
}

// Include the admin template
include '../assets/includes/main.php';
?>

<div class="content">
    <h2>Chat Operators</h2>
    <p>Manage which users can handle live chat sessions.</p>
    
    <?php if ($success_message): ?>
        <div class="alert alert-success">
            <span class="icon">✅</span>
            <?= htmlspecialchars($success_message) ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
        <div class="alert alert-danger">
            <span class="icon">⚠️</span>
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>
    
    <!-- Operator Statistics -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-content">
                <h3><?= number_format($operator_stats['total_operators']) ?></h3>
                <p>Total Operators</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">🟢</div>
            <div class="stat-content">
                <h3><?= number_format($operator_stats['online_operators']) ?></h3>
                <p>Online Now</p>
            </div>
        </div>
    </div>
    
    <!-- Operators Table -->
    <div class="operators-section">
        <h3>User Accounts</h3>
        <p>Enable or disable chat operator permissions for existing users.</p>
        
        <?php if (!empty($users)): ?>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Last Seen</th>
                            <th>Chat Operator</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <strong><?= htmlspecialchars($user['username']) ?></strong><br>
                                        <small><?= htmlspecialchars($user['email']) ?></small>
                                    </div>
                                </td>
                                <td>
                                    <span class="role-badge role-<?= strtolower(str_replace(' ', '-', $user['role'])) ?>">
                                        <?= htmlspecialchars($user['role']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($user['last_seen_date']): ?>
                                        <?= time_elapsed_string($user['last_seen_date']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Never</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="operator-status <?= $user['chat_operator'] ? 'enabled' : 'disabled' ?>">
                                        <?= $user['chat_operator'] ? 'Enabled' : 'Disabled' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $is_online = $user['last_seen_date'] && strtotime($user['last_seen_date']) > (time() - 3600);
                                    ?>
                                    <span class="status-indicator <?= $is_online ? 'online' : 'offline' ?>">
                                        <?= $is_online ? 'Online' : 'Offline' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="?action=toggle_chat&user_id=<?= $user['id'] ?>" 
                                           class="button button-small <?= $user['chat_operator'] ? 'button-danger' : 'button-success' ?>"
                                           onclick="return confirm('<?= $user['chat_operator'] ? 'Disable' : 'Enable' ?> chat operator for <?= htmlspecialchars($user['username']) ?>?')">
                                            <?= $user['chat_operator'] ? 'Disable' : 'Enable' ?>
                                        </a>
                                        
                                        <a href="../accounts/account.php?id=<?= $user['id'] ?>" 
                                           class="button button-small">Edit User</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-data">
                <span class="icon">👥</span>
                <h3>No Users Found</h3>
                <p>No eligible users found for chat operator assignment.</p>
                <a href="../accounts/account.php" class="button">Create New User</a>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Help Information -->
    <div class="help-section">
        <h3>Chat Operator Information</h3>
        <div class="help-content">
            <div class="help-item">
                <strong>Operator Permissions:</strong>
                <p>Only users with Admin, Manager, Operator, or Support roles can be assigned as chat operators.</p>
            </div>
            
            <div class="help-item">
                <strong>Online Status:</strong>
                <p>Users are considered online if they have been active within the last hour.</p>
            </div>
            
            <div class="help-item">
                <strong>Auto-Assignment:</strong>
                <p>When enabled in settings, new chats will be automatically assigned to available operators.</p>
            </div>
        </div>
    </div>
</div>

<style>
.stats-cards {
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

.operators-section, .help-section {
    background: white;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 25px;
}

.user-info {
    line-height: 1.4;
}

.role-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8em;
    font-weight: bold;
    text-transform: uppercase;
}

.role-admin, .role-super-admin { background: #e74c3c; color: white; }
.role-manager { background: #f39c12; color: white; }
.role-operator, .role-support { background: #3498db; color: white; }

.operator-status {
    font-weight: bold;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8em;
}

.operator-status.enabled {
    background: #d4edda;
    color: #155724;
}

.operator-status.disabled {
    background: #f8d7da;
    color: #721c24;
}

.status-indicator {
    font-weight: bold;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8em;
}

.status-indicator.online {
    background: #d4edda;
    color: #155724;
}

.status-indicator.offline {
    background: #f8d7da;
    color: #721c24;
}

.action-buttons {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}

.text-muted {
    color: #6c757d;
    font-style: italic;
}

.help-content {
    display: grid;
    gap: 20px;
}

.help-item {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 6px;
    border-left: 4px solid #3498db;
}

.help-item strong {
    color: #2c3e50;
    display: block;
    margin-bottom: 5px;
}

.help-item p {
    margin: 0;
    color: #5a6c7d;
}

.no-data {
    text-align: center;
    padding: 60px;
    color: #7f8c8d;
}

.no-data .icon {
    font-size: 4em;
    display: block;
    margin-bottom: 20px;
    opacity: 0.5;
}

.alert {
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>

<?php include '../assets/includes/footer.php'; ?>
