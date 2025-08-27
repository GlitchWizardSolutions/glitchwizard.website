<?php
/**
 * Contact Form Messages Management
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: contact_messages.php
 * LOCATION: /public_html/admin/
 * PURPOSE: View and manage contact form submissions
 * 
 * CREATED: 2025-08-08
 * UPDATED: 2025-08-08
 * VERSION: 1.0
 * PRODUCTION: [IN DEVELOPMENT]
 * 
 * FEATURES:
 * - View all contact form submissions
 * - Mark messages as read/unread/replied
 * - Delete messages
 * - Search and filter functionality
 * - Responsive table design matching Table.php canonical format
 */

include_once 'assets/includes/main.php';

// Handle actions
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'update_status':
                    $id = (int)$_POST['id'];
                    $status = $_POST['status'];
                    
                    if (in_array($status, ['Unread', 'Read', 'Replied'])) {
                        $stmt = $pdo->prepare('UPDATE contact_form_messages SET status = ? WHERE id = ?');
                        $stmt->execute([$status, $id]);
                        $message = 'Message status updated successfully!';
                        $message_type = 'success';
                    }
                    break;
                    
                case 'delete':
                    $id = (int)$_POST['id'];
                    $stmt = $pdo->prepare('DELETE FROM contact_form_messages WHERE id = ?');
                    $stmt->execute([$id]);
                    $message = 'Message deleted successfully!';
                    $message_type = 'success';
                    break;
                    
                case 'bulk_delete':
                    if (!empty($_POST['selected_messages'])) {
                        $ids = array_map('intval', $_POST['selected_messages']);
                        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
                        $stmt = $pdo->prepare("DELETE FROM contact_form_messages WHERE id IN ($placeholders)");
                        $stmt->execute($ids);
                        $message = count($ids) . ' messages deleted successfully!';
                        $message_type = 'success';
                    }
                    break;
                    
                case 'bulk_status':
                    if (!empty($_POST['selected_messages']) && !empty($_POST['bulk_status'])) {
                        $ids = array_map('intval', $_POST['selected_messages']);
                        $status = $_POST['bulk_status'];
                        
                        if (in_array($status, ['Unread', 'Read', 'Replied'])) {
                            $placeholders = str_repeat('?,', count($ids) - 1) . '?';
                            $stmt = $pdo->prepare("UPDATE contact_form_messages SET status = ? WHERE id IN ($placeholders)");
                            $stmt->execute(array_merge([$status], $ids));
                            $message = count($ids) . ' messages updated successfully!';
                            $message_type = 'success';
                        }
                    }
                    break;
            }
        } catch (Exception $e) {
            $message = 'Error: ' . $e->getMessage();
            $message_type = 'danger';
        }
    }
}

// Pagination and filtering
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 25;
$offset = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';
$category_filter = $_GET['category'] ?? '';

// Build WHERE clause
$where_conditions = [];
$params = [];

if ($search) {
    $where_conditions[] = "(email LIKE ? OR subject LIKE ? OR msg LIKE ? OR JSON_EXTRACT(extra, '$.first_name') LIKE ? OR JSON_EXTRACT(extra, '$.last_name') LIKE ?)";
    $search_param = '%' . $search . '%';
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param, $search_param]);
}

if ($status_filter) {
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
}

if ($category_filter) {
    $where_conditions[] = "JSON_EXTRACT(extra, '$.category') = ?";
    $params[] = $category_filter;
}

$where_clause = $where_conditions ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total count
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM contact_form_messages $where_clause");
$count_stmt->execute($params);
$total_records = $count_stmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

// Get messages
$stmt = $pdo->prepare("
    SELECT id, email, subject, msg, extra, submit_date, status 
    FROM contact_form_messages 
    $where_clause 
    ORDER BY submit_date DESC 
    LIMIT $limit OFFSET $offset
");
$stmt->execute($params);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get status counts for badges
$status_counts = [];
$status_stmt = $pdo->query("SELECT status, COUNT(*) as count FROM contact_form_messages GROUP BY status");
while ($row = $status_stmt->fetch(PDO::FETCH_ASSOC)) {
    $status_counts[$row['status']] = $row['count'];
}

// Page title
$page_title = 'Contact Messages';
?>

<?= template_admin_header($page_title, 'contact', 'messages') ?>

<div class="professional-card-header">
    <div class="title">
        <div class="icon">
            <i class="fas fa-envelope"></i>
        </div>
        <div class="txt">
            <h2>Contact Messages</h2>
            <p>Manage contact form submissions and responses.</p>
        </div>
    </div>
</div>

<?php if ($message): ?>
<div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
    <i class="fas fa-<?= $message_type === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
    <?= htmlspecialchars($message) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card">
    <div class="professional-card-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex gap-2 mb-2 mb-md-0">
                <span class="badge bg-primary">Total: <?= $total_records ?></span>
                <span class="badge bg-warning">Unread: <?= $status_counts['Unread'] ?? 0 ?></span>
                <span class="badge bg-info">Read: <?= $status_counts['Read'] ?? 0 ?></span>
                <span class="badge bg-success">Replied: <?= $status_counts['Replied'] ?? 0 ?></span>
            </div>
            <div class="d-flex gap-2">
                <a href="settings/contact_settings.php" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        <!-- Search and Filter Form -->
        <div class="p-3 border-bottom bg-light">
            <form method="get" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control form-control-sm" name="search" 
                        placeholder="Search messages..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="status">
                        <option value="">All Status</option>
                        <option value="Unread" <?= $status_filter === 'Unread' ? 'selected' : '' ?>>Unread</option>
                        <option value="Read" <?= $status_filter === 'Read' ? 'selected' : '' ?>>Read</option>
                        <option value="Replied" <?= $status_filter === 'Replied' ? 'selected' : '' ?>>Replied</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="category">
                        <option value="">All Categories</option>
                        <option value="general" <?= $category_filter === 'general' ? 'selected' : '' ?>>General</option>
                        <option value="technical" <?= $category_filter === 'technical' ? 'selected' : '' ?>>Technical</option>
                        <option value="business" <?= $category_filter === 'business' ? 'selected' : '' ?>>Business</option>
                        <option value="feedback" <?= $category_filter === 'feedback' ? 'selected' : '' ?>>Feedback</option>
                        <option value="other" <?= $category_filter === 'other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <a href="contact_messages.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Bulk Actions -->
        <div class="p-3 border-bottom">
            <form method="post" id="bulkForm">
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <label class="form-label mb-0">Bulk Actions:</label>
                    <select class="form-select form-select-sm" name="bulk_status" style="width: auto;">
                        <option value="">Update Status</option>
                        <option value="Read">Mark as Read</option>
                        <option value="Replied">Mark as Replied</option>
                        <option value="Unread">Mark as Unread</option>
                    </select>
                    <button type="submit" name="action" value="bulk_status" class="btn btn-sm btn-outline-primary" disabled id="bulkStatusBtn">
                        <i class="fas fa-edit"></i> Update
                    </button>
                    <button type="submit" name="action" value="bulk_delete" class="btn btn-sm btn-outline-danger" disabled id="bulkDeleteBtn"
                        onclick="return confirm('Are you sure you want to delete the selected messages?')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                    <span class="text-muted small" id="selectedCount">No messages selected</span>
                </div>
            </form>
        </div>

        <!-- Messages Table -->
        <?php if (empty($messages)): ?>
        <div class="p-4 text-center text-muted">
            <i class="fas fa-inbox fa-3x mb-3"></i>
            <p>No contact messages found.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0" role="table" aria-label="Contact Messages">
                <thead class="table-light">
                    <tr>
                        <th scope="col" style="width: 50px;">
                            <input type="checkbox" class="form-check-input" id="selectAll" aria-label="Select all messages">
                        </th>
                        <th scope="col" class="text-start">From</th>
                        <th scope="col" class="text-start">Subject</th>
                        <th scope="col" class="text-start">Category</th>
                        <th scope="col">Date</th>
                        <th scope="col">Status</th>
                        <th scope="col" style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $msg): 
                        $extra = json_decode($msg['extra'], true) ?: [];
                        $full_name = ($extra['first_name'] ?? '') . ' ' . ($extra['last_name'] ?? '');
                        $category = $extra['category'] ?? 'general';
                        $status_class = [
                            'Unread' => 'warning',
                            'Read' => 'info', 
                            'Replied' => 'success'
                        ][$msg['status']] ?? 'secondary';
                    ?>
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input message-checkbox" 
                                name="selected_messages[]" value="<?= $msg['id'] ?>" form="bulkForm">
                        </td>
                        <td class="text-start">
                            <div>
                                <strong><?= htmlspecialchars(trim($full_name)) ?></strong><br>
                                <small class="text-muted"><?= htmlspecialchars($msg['email']) ?></small>
                            </div>
                        </td>
                        <td class="text-start">
                            <div class="text-truncate" style="max-width: 250px;" title="<?= htmlspecialchars($msg['subject']) ?>">
                                <?= htmlspecialchars($msg['subject']) ?>
                            </div>
                            <small class="text-muted">
                                <?= htmlspecialchars(substr($msg['msg'], 0, 100)) ?><?= strlen($msg['msg']) > 100 ? '...' : '' ?>
                            </small>
                        </td>
                        <td class="text-start">
                            <span class="badge bg-secondary"><?= htmlspecialchars(ucfirst($category)) ?></span>
                        </td>
                        <td>
                            <small><?= date('M j, Y', strtotime($msg['submit_date'])) ?><br>
                            <?= date('g:i A', strtotime($msg['submit_date'])) ?></small>
                        </td>
                        <td>
                            <form method="post" class="d-inline">
                                <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="Unread" <?= $msg['status'] === 'Unread' ? 'selected' : '' ?>>Unread</option>
                                    <option value="Read" <?= $msg['status'] === 'Read' ? 'selected' : '' ?>>Read</option>
                                    <option value="Replied" <?= $msg['status'] === 'Replied' ? 'selected' : '' ?>>Replied</option>
                                </select>
                                <input type="hidden" name="action" value="update_status">
                            </form>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                    onclick="viewMessage(<?= htmlspecialchars(json_encode($msg)) ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                        onclick="return confirm('Are you sure you want to delete this message?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Pagination Footer -->
    <?php if ($total_pages > 1): ?>
    <div class="professional-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Showing <?= ($offset + 1) ?> to <?= min($offset + $limit, $total_records) ?> of <?= $total_records ?> messages
            </div>
            <nav aria-label="Contact messages pagination">
                <ul class="pagination pagination-sm mb-0">
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page - 1 ?>&<?= http_build_query($_GET) ?>">Previous</a>
                    </li>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&<?= http_build_query($_GET) ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page + 1 ?>&<?= http_build_query($_GET) ?>">Next</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Message View Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel">View Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="messageContent">
                <!-- Content will be populated by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const messageCheckboxes = document.querySelectorAll('.message-checkbox');
    const bulkStatusBtn = document.getElementById('bulkStatusBtn');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const selectedCount = document.getElementById('selectedCount');
    
    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.message-checkbox:checked');
        const count = checkedBoxes.length;
        
        bulkStatusBtn.disabled = count === 0;
        bulkDeleteBtn.disabled = count === 0;
        
        if (count === 0) {
            selectedCount.textContent = 'No messages selected';
        } else if (count === 1) {
            selectedCount.textContent = '1 message selected';
        } else {
            selectedCount.textContent = count + ' messages selected';
        }
    }
    
    selectAllCheckbox.addEventListener('change', function() {
        messageCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActions();
    });
    
    messageCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
    
    // Initial state
    updateBulkActions();
});

function viewMessage(message) {
    const extra = JSON.parse(message.extra || '{}');
    const fullName = (extra.first_name || '') + ' ' + (extra.last_name || '');
    const category = extra.category || 'general';
    
    const content = `
        <div class="row">
            <div class="col-md-6">
                <strong>From:</strong> ${fullName}<br>
                <strong>Email:</strong> ${message.email}<br>
                <strong>Category:</strong> ${category.charAt(0).toUpperCase() + category.slice(1)}
            </div>
            <div class="col-md-6">
                <strong>Date:</strong> ${new Date(message.submit_date).toLocaleString()}<br>
                <strong>Status:</strong> <span class="badge bg-info">${message.status}</span><br>
                <strong>IP:</strong> ${extra.ip_address || 'Unknown'}
            </div>
        </div>
        <hr>
        <div>
            <strong>Subject:</strong> ${message.subject}
        </div>
        <hr>
        <div>
            <strong>Message:</strong><br>
            <div class="border p-3 bg-light rounded" style="white-space: pre-wrap; max-height: 300px; overflow-y: auto;">
${message.msg}
            </div>
        </div>
    `;
    
    document.getElementById('messageContent').innerHTML = content;
    
    const modal = new bootstrap.Modal(document.getElementById('messageModal'));
    modal.show();
}
</script>

<?= template_admin_footer() ?>
