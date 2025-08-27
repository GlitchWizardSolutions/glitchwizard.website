<?php
// Start the session
session_start();

// Include necessary files
require_once '../../private/gws-universal-config.php';
require_once '../../private/gws-universal-functions.php';

// Initialize admin template parameters
$selected = 'chat';
$selected_child = 'sessions';
$title = 'Chat Sessions';

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

// Pagination settings
$results_per_page = 20;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $results_per_page;

// Search and filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Build WHERE clause
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(customer_name LIKE ? OR customer_email LIKE ? OR id LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($status_filter)) {
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
}

if (!empty($date_from)) {
    $where_conditions[] = "DATE(created_at) >= ?";
    $params[] = $date_from;
}

if (!empty($date_to)) {
    $where_conditions[] = "DATE(created_at) <= ?";
    $params[] = $date_to;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

try {
    // Get total count for pagination
    $count_query = "SELECT COUNT(*) FROM chat_sessions $where_clause";
    $stmt = $pdo->prepare($count_query);
    $stmt->execute($params);
    $total_results = $stmt->fetchColumn();
    $total_pages = ceil($total_results / $results_per_page);
    
    // Get chat sessions with message counts
    $query = "
        SELECT cs.*, 
               COUNT(cm.id) as message_count,
               SUM(CASE WHEN cm.is_read = 0 AND cm.sender_type = 'customer' THEN 1 ELSE 0 END) as unread_count,
               MAX(cm.created_at) as last_message_time
        FROM chat_sessions cs 
        LEFT JOIN chat_messages cm ON cs.id = cm.session_id 
        $where_clause
        GROUP BY cs.id 
        ORDER BY cs.last_activity DESC 
        LIMIT $results_per_page OFFSET $offset
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    $error_message = "Error retrieving chat sessions: " . $e->getMessage();
    $sessions = [];
    $total_results = 0;
    $total_pages = 0;
}

// Include the admin template
include '../assets/includes/main.php';
?>

<div class="content">
    <h2>Chat Sessions</h2>
    <p>Manage and monitor customer chat sessions.</p>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <span class="icon">⚠️</span>
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>
    
    <!-- Search and Filter Form -->
    <div class="filters-section">
        <form method="GET" class="filters-form">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="search">Search:</label>
                    <input type="text" id="search" name="search" value="<?= htmlspecialchars($search) ?>" 
                           placeholder="Search by name, email, or session ID...">
                </div>
                
                <div class="filter-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="active" <?= $status_filter === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="waiting" <?= $status_filter === 'waiting' ? 'selected' : '' ?>>Waiting</option>
                        <option value="ended" <?= $status_filter === 'ended' ? 'selected' : '' ?>>Ended</option>
                        <option value="transferred" <?= $status_filter === 'transferred' ? 'selected' : '' ?>>Transferred</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="date_from">From:</label>
                    <input type="date" id="date_from" name="date_from" value="<?= htmlspecialchars($date_from) ?>">
                </div>
                
                <div class="filter-group">
                    <label for="date_to">To:</label>
                    <input type="date" id="date_to" name="date_to" value="<?= htmlspecialchars($date_to) ?>">
                </div>
                
                <div class="filter-group">
                    <button type="submit" class="button">Search</button>
                    <a href="chat_sessions.php" class="button button-secondary">Clear</a>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Results Summary -->
    <div class="results-summary">
        <p>Showing <?= count($sessions) ?> of <?= number_format($total_results) ?> chat sessions</p>
    </div>
    
    <!-- Chat Sessions Table -->
    <?php if (!empty($sessions)): ?>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Session ID</th>
                        <th>Customer Info</th>
                        <th>Status</th>
                        <th>Operator</th>
                        <th>Messages</th>
                        <th>Started</th>
                        <th>Last Activity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sessions as $session): ?>
                        <tr>
                            <td>
                                <strong>#<?= $session['id'] ?></strong>
                                <?php if ($session['unread_count'] > 0): ?>
                                    <span class="unread-badge"><?= $session['unread_count'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="customer-info">
                                    <strong><?= htmlspecialchars($session['customer_name'] ?? 'Anonymous') ?></strong><br>
                                    <small><?= htmlspecialchars($session['customer_email'] ?? 'No email provided') ?></small>
                                    <?php if (!empty($session['customer_ip'])): ?>
                                        <br><small class="ip-address">IP: <?= htmlspecialchars($session['customer_ip']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge status-<?= strtolower($session['status']) ?>">
                                    <?= ucfirst($session['status']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($session['operator_id'])): ?>
                                    <?= htmlspecialchars($session['operator_name'] ?? 'Operator #' . $session['operator_id']) ?>
                                <?php else: ?>
                                    <span class="no-operator">Unassigned</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="message-count"><?= $session['message_count'] ?: 0 ?></span>
                                <?php if ($session['last_message_time']): ?>
                                    <br><small>Last: <?= time_elapsed_string($session['last_message_time']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= date('M j, Y', strtotime($session['created_at'])) ?><br>
                                <small><?= date('g:i A', strtotime($session['created_at'])) ?></small>
                            </td>
                            <td>
                                <?= time_elapsed_string($session['last_activity']) ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="chat_session.php?id=<?= $session['id'] ?>" class="button button-small">View</a>
                                    <?php if ($session['status'] === 'active'): ?>
                                        <a href="chat_session.php?id=<?= $session['id'] ?>&action=join" class="button button-small button-primary">Join</a>
                                    <?php endif; ?>
                                    <?php if ($session['status'] !== 'ended'): ?>
                                        <a href="?action=end&id=<?= $session['id'] ?>" class="button button-small button-danger" 
                                           onclick="return confirm('End this chat session?')">End</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php
                $query_params = $_GET;
                unset($query_params['page']);
                $base_url = 'chat_sessions.php?' . http_build_query($query_params);
                ?>
                
                <?php if ($page > 1): ?>
                    <a href="<?= $base_url ?>&page=1" class="page-link">First</a>
                    <a href="<?= $base_url ?>&page=<?= $page - 1 ?>" class="page-link">Previous</a>
                <?php endif; ?>
                
                <?php
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);
                
                for ($i = $start_page; $i <= $end_page; $i++):
                ?>
                    <a href="<?= $base_url ?>&page=<?= $i ?>" 
                       class="page-link <?= $i === $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="<?= $base_url ?>&page=<?= $page + 1 ?>" class="page-link">Next</a>
                    <a href="<?= $base_url ?>&page=<?= $total_pages ?>" class="page-link">Last</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
    <?php else: ?>
        <div class="no-data">
            <span class="icon">💬</span>
            <h3>No Chat Sessions Found</h3>
            <p>No chat sessions match your current filters.</p>
            <?php if (!empty($search) || !empty($status_filter) || !empty($date_from) || !empty($date_to)): ?>
                <a href="chat_sessions.php" class="button">Clear Filters</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.filters-section {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.filter-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    align-items: end;
}

.filter-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}

.filter-group input,
.filter-group select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.results-summary {
    margin-bottom: 15px;
    color: #666;
}

.customer-info {
    line-height: 1.4;
}

.ip-address {
    color: #888;
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
.status-transferred { background: #d1ecf1; color: #0c5460; }

.unread-badge {
    background: #e74c3c;
    color: white;
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 0.7em;
    margin-left: 5px;
}

.no-operator {
    color: #999;
    font-style: italic;
}

.message-count {
    font-weight: bold;
    color: #3498db;
}

.action-buttons {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 5px;
    margin-top: 20px;
}

.page-link {
    padding: 8px 12px;
    border: 1px solid #ddd;
    text-decoration: none;
    color: #333;
    border-radius: 4px;
}

.page-link:hover {
    background: #f8f9fa;
}

.page-link.active {
    background: #3498db;
    color: white;
    border-color: #3498db;
}

.no-data {
    text-align: center;
    padding: 60px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>

<?php include '../assets/includes/footer.php'; ?>
