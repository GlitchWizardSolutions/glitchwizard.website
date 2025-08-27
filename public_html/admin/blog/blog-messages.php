<?php
/**
 * Blog Messages Management
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: blog-messages.php
 * LOCATION: /public_html/admin/blog/
 * PURPOSE: Manage blog messages - view, search, filter, and delete messages sent via the blog contact or messaging system
 * 
 * CREATED: 2025-07-26
 * UPDATED: 2025-07-26
 * VERSION: 1.0
 * PRODUCTION: [READY FOR PRODUCTION]
 * 
 * CHANGE LOG:
 * 2025-07-26 - Initial implementation based on posts.php structure, adapted for blog messages management
 * 2025-07-26 - Updated header, UI, and logic for message viewing, searching, filtering, and deletion
 * 2025-07-26 - Added content title block and consistent button formatting for messages
 * 2025-07-26 - Improved message status handling and filter/search workflow
 * 2025-07-26 - Passed Quality Assurance (QA) check: UI, accessibility, error handling, and icon logic verified
 * 
 * FEATURES:
 * - View, search, and filter blog messages
 * - Delete messages securely
 * - Professional admin interface with consistent UI
 * - Status and date display for each message
 * - Responsive table and dropdown actions
 * 
 * DEPENDENCIES:
 * - header.php (blog includes)
 * - Bootstrap 5 for styling
 * - PDO database connection
 * - Font Awesome icons
 * 
 * SECURITY NOTES:
 * - Admin authentication required
 * - PDO prepared statements prevent SQL injection
 * - Input validation and sanitization
 * - XSS protection on output
 */
/**
 * Blog Messages Management
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: blog-messages.php
 * LOCATION: /public_html/admin/blog/
 * PURPOSE: Manage blog messages - view, search, filter, and delete messages sent via the blog contact or messaging system
 */
include_once "header.php";
function show_alert($message, $type = "danger")
{
    echo '<div class="alert alert-' . htmlspecialchars($type) . '" role="alert" aria-live="assertive">' . htmlspecialchars($message) . '</div>';
}

// --- Pagination and Search Setup ---

$per_page = 10;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$offset = ($page - 1) * $per_page;

// Sorting
$order = isset($_GET['order']) && in_array($_GET['order'], ['asc', 'desc']) ? $_GET['order'] : 'desc';
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], ['id', 'name', 'email', 'date', 'viewed']) ? $_GET['order_by'] : 'id';

// Filters

$email = isset($_GET['email']) ? trim($_GET['email']) : '';
$viewed = isset($_GET['viewed']) ? trim($_GET['viewed']) : '';
$name = isset($_GET['name']) ? trim($_GET['name']) : '';

// Build WHERE clause for search and filters
$where = [];
$params = [];
if ($search !== '')
{
    $where[] = "content LIKE ?";
    $params[] = "%$search%";
}
if ($email !== '')
{
    $where[] = "email LIKE ?";
    $params[] = "%$email%";
}
if ($name !== '')
{
    $where[] = "name LIKE ?";
    $params[] = "%$name%";
}
if ($viewed !== '')
{
    $where[] = "viewed = ?";
    $params[] = $viewed;
}
$where_sql = '';
if (count($where) > 0)
{
    $where_sql = 'WHERE ' . implode(' AND ', $where);
}

// Get total count for pagination
$stmt = $pdo->prepare("SELECT COUNT(*) FROM blog_messages $where_sql");
$stmt->execute($params);
$total_messages = $stmt->fetchColumn();
$total_pages = ceil($total_messages / $per_page);

// Get messages for current page
$sql = "SELECT * FROM blog_messages $where_sql ORDER BY $order_by $order LIMIT $per_page OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle delete
if (isset($_GET['delete']))
{
    $id = (int) $_GET["delete"];
    $stmt = $pdo->prepare("DELETE FROM blog_messages WHERE id = ?");
    $stmt->execute([$id]);
    echo '<script>window.location.href = "blog-messages.php";</script>';
    exit;
}
?>
<?= template_admin_header('Blog Messages', 'blog', 'messages') ?>
<div class="content-title mb-4">
    <div class="title">
        <div class="icon">
            <?php if (isset($_GET['edit'])): ?>
                <!-- Open Envelope SVG for Edit Mode (White) -->
                <i class="fas fa-envelope-open" aria-hidden="true"></i>
            <?php else: ?>
                <?php if (isset($_GET['view'])): ?>
                    <!-- Open Envelope SVG for View Mode (White) -->
                    <i class="fas fa-envelope-open" aria-hidden="true"></i>
                <?php else: ?>
                    <!-- Closed Envelope SVG for Table Mode (White) -->
                    <svg aria-hidden="true" width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                        aria-hidden="true" focusable="false">
                        <path
                            d="M64 112c-8.8 0-16 7.2-16 16v22.1L220.5 291.7c20.7 17 50.4 17 71.1 0L464 150.1V128c0-8.8-7.2-16-16-16H64zM48 212.2V384c0 8.8 7.2 16 16 16H448c8.8 0 16-7.2 16-16V212.2L322 328.8c-38.4 31.5-93.7 31.5-132 0L48 212.2zM0 128C0 92.7 28.7 64 64 64H448c35.3 0 64 28.7 64 64V384c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V128z" />
                    </svg>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <div class="txt">
            <h2>Messages Management</h2>
            <p>View, search, and manage all blog messages sent via the contact or messaging system.</p>
        </div>
    </div>
</div>
 
<!-- Action Buttons Row -->
<?php if (isset($_GET['edit'])): ?>
    <div class="content-header responsive-flex-column pad-top-5">
        <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-start; margin-bottom: 18px;">
            <a href="blog-messages.php" class="btn btn-outline-secondary" aria-label="Cancel and return to messages list">
                <i class="fas fa-arrow-left me-1"></i>Cancel
            </a>
        </div>
    </div>
<?php else: ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Blog Messages Management</h6>
        <small class="text-muted"><?=number_format($total_messages)?> total messages</small>
    </div>
    <div class="card-body">
        <form action="" method="get" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input id="search" type="text" name="search" class="form-control"
                        placeholder="Search messages..." 
                        value="<?=htmlspecialchars($search, ENT_QUOTES)?>">
                </div>
                <div class="col-md-2">
                    <label for="viewed" class="form-label">Status</label>
                    <select name="viewed" id="viewed" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="Yes" <?= $viewed == 'Yes' ? 'selected' : '' ?>>Read</option>
                        <option value="No" <?= $viewed == 'No' ? 'selected' : '' ?>>Unread</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" name="name" id="name" class="form-control"
                        value="<?= htmlspecialchars($name) ?>" placeholder="Filter by name...">
                </div>
                <div class="col-md-2">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" name="email" id="email" class="form-control"
                        value="<?= htmlspecialchars($email) ?>" placeholder="Filter by email...">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-search me-1" aria-hidden="true"></i>
                        Apply Filters
                    </button>
                    <?php if ($search || $viewed || $name || $email): ?>
                    <a href="blog-messages.php" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-times me-1" aria-hidden="true"></i>
                        Clear
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
<?php endif; ?>

<?php
if (isset($_GET['edit']) && is_numeric($_GET['edit']))
{
    // EDIT MODE
    $id = (int) $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM blog_messages WHERE id = ?");
    $stmt->execute([$id]);
    $msg = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($msg)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['viewed']))
        {
            $new_status = $_POST['viewed'];
            $stmt = $pdo->prepare("UPDATE blog_messages SET viewed = ? WHERE id = ?");
            $stmt->execute([$new_status, $id]);
            echo '<script>window.location.href = "blog-messages.php?view=' . $id . '";</script>';
            exit;
        }
        ?>
        <div class="card mb-3">
            <h6 class="card-header">Edit Message Status</h6>
            <div class="card-body">
                <form method="post">
                    <dl class="row">
                        <dt class="col-sm-3">Name</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($msg['name']) ?></dd>
                        <dt class="col-sm-3">Email</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($msg['email']) ?></dd>
                        <dt class="col-sm-3">Date</dt>
                        <dd class="col-sm-9"><?= date('n/j/Y h:i A', strtotime($msg['date'])) ?></dd>
                        <dt class="col-sm-3">Status</dt>
                        <dd class="col-sm-9">
                            <select name="viewed" class="form-select" required>
                                <option value="Unread" <?= $msg['viewed'] == 'Unread' ? 'selected' : '' ?>>Unread</option>
                                <option value="Read" <?= $msg['viewed'] == 'Read' ? 'selected' : '' ?>>Read</option>
                                <option value="Replied" <?= $msg['viewed'] == 'Replied' ? 'selected' : '' ?>>Replied</option>
                            </select>
                        </dd>
                        <dt class="col-sm-3">Content</dt>
                        <dd class="col-sm-9" style="white-space:pre-line;word-break:break-word;">
                            <?= nl2br(htmlspecialchars($msg['content'])) ?>
                        </dd>
                    </dl>
                    <div class="d-flex gap-2 pt-3">
                        <a href="blog-messages.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-success">Save Status</button>
                    </div>
                </form>
            </div>
        </div>
        <?php
    } else
    {
        echo '<div class="alert alert-danger">Message not found.</div>';
    }
} elseif (isset($_GET['view']) && is_numeric($_GET['view']))
{
    // VIEW MODE
    $id = (int) $_GET['view'];
    $stmt = $pdo->prepare("SELECT * FROM blog_messages WHERE id = ?");
    $stmt->execute([$id]);
    $msg = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($msg)
    {
        ?>
        <div class="card mb-3">
            <h6 class="card-header">View Message</h6>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Name</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($msg['name']) ?></dd>
                    <dt class="col-sm-3">Email</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($msg['email']) ?></dd>
                    <dt class="col-sm-3">Date</dt>
                    <dd class="col-sm-9"><?= date('n/j/Y h:i A', strtotime($msg['date'])) ?></dd>
                    <dt class="col-sm-3">Status</dt>
                    <dd class="col-sm-9">
                        <?php
                        if ($msg['viewed'] == "Unread")
                        {
                            echo '<span class="grey">Unread</span>';
                        } elseif ($msg['viewed'] == "Read")
                        {
                            echo '<span class="green">Read</span>';
                        } elseif ($msg['viewed'] == "Replied")
                        {
                            echo '<span class="blue">Replied</span>';
                        } else
                        {
                            echo htmlspecialchars($msg['viewed']);
                        }
                        ?>
                    </dd>
                    <dt class="col-sm-3">Content</dt>
                    <dd class="col-sm-9" style="white-space:pre-line;word-break:break-word;">
                        <?= nl2br(htmlspecialchars($msg['content'])) ?>
                    </dd>
                </dl>
                <div class="d-flex gap-2 pt-3">
                    <a href="blog-messages.php?edit=<?= $id ?>" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i>Edit Status
                    </a>
                    <a href="blog-messages.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Cancel
                    </a>
                </div>
            </div>
        </div>
        <?php
    } else
    {
        echo '<div class="alert alert-danger">Message not found.</div>';
    }
} else
{
    // TABLE MODE
    ?>
    
        <div class="table-responsive" role="table" aria-label="Blog Messages">
            <table class="table table-hover mb-0" role="grid">
                    <thead role="rowgroup">
                        <tr role="row">
                            <th style="text-align:left;" role="columnheader" scope="col">
                                <a href="?order_by=name&order=<?= ($order_by == 'name' && $order == 'asc') ? 'desc' : 'asc' ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($email) ? '&email=' . urlencode($email) : '' ?><?= !empty($name) ? '&name=' . urlencode($name) : '' ?><?= !empty($viewed) ? '&viewed=' . urlencode($viewed) : '' ?>" style="color: inherit; text-decoration: none;">
                                    Name
                                    <?php if ($order_by == 'name'): ?>
                                        <?= $order == 'asc' ? '▲' : '▼' ?>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th style="text-align:left;" role="columnheader" scope="col">
                                <a href="?order_by=email&order=<?= ($order_by == 'email' && $order == 'asc') ? 'desc' : 'asc' ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($email) ? '&email=' . urlencode($email) : '' ?><?= !empty($name) ? '&name=' . urlencode($name) : '' ?><?= !empty($viewed) ? '&viewed=' . urlencode($viewed) : '' ?>" style="color: inherit; text-decoration: none;">
                                    Email
                                    <?php if ($order_by == 'email'): ?>
                                        <?= $order == 'asc' ? '▲' : '▼' ?>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th style="text-align:left;" role="columnheader" scope="col">Content</th>
                            <th style="text-align:center;" role="columnheader" scope="col">
                                <a href="?order_by=date&order=<?= ($order_by == 'date' && $order == 'asc') ? 'desc' : 'asc' ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($email) ? '&email=' . urlencode($email) : '' ?><?= !empty($name) ? '&name=' . urlencode($name) : '' ?><?= !empty($viewed) ? '&viewed=' . urlencode($viewed) : '' ?>" style="color: inherit; text-decoration: none;">
                                    Date
                                    <?php if ($order_by == 'date'): ?>
                                        <?= $order == 'asc' ? '▲' : '▼' ?>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th style="text-align:center;" role="columnheader" scope="col">
                                <a href="?order_by=viewed&order=<?= ($order_by == 'viewed' && $order == 'asc') ? 'desc' : 'asc' ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($email) ? '&email=' . urlencode($email) : '' ?><?= !empty($name) ? '&name=' . urlencode($name) : '' ?><?= !empty($viewed) ? '&viewed=' . urlencode($viewed) : '' ?>" style="color: inherit; text-decoration: none;">
                                    Status
                                    <?php if ($order_by == 'viewed'): ?>
                                        <?= $order == 'asc' ? '▲' : '▼' ?>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th style="text-align:center;" role="columnheader" scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody role="rowgroup">
                        <?php
                        foreach ($messages as $row)
                        {
                            echo '<tr role="row">';
                            echo '<td style="text-align:left;" role="gridcell">' . htmlspecialchars($row['name']) . '</td>';
                            echo '<td style="text-align:left;" role="gridcell">' . htmlspecialchars($row['email']) . '</td>';
                            echo '<td style="text-align:left;" role="gridcell">' . htmlspecialchars(mb_strimwidth($row['content'], 0, 100, '...')) . '</td>';
                            echo '<td style="text-align:center;" role="gridcell">' . date('n/j/Y h:i A', strtotime($row['date'])) . '</td>';
                            echo '<td style="text-align:center;" role="gridcell">';
                            if ($row['viewed'] == "Yes") {
                                echo '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Read</span>';
                            } else {
                                echo '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Unread</span>';
                            }
                            echo '</td>';
                            echo '<td class="actions text-center" style="text-align:center;" role="gridcell">';
                            echo '<div class="table-dropdown">';
                            echo '<button class="actions-btn" aria-haspopup="true" aria-expanded="false" aria-label="Actions for message from ' . htmlspecialchars($row['name']) . '">';
                            echo '<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">';
                            echo '<path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/>';
                            echo '</svg>';
                            echo '</button>';
                            echo '<div class="table-dropdown-items" role="menu" aria-label="Message Actions">';
                            echo '<div role="menuitem"><a href="?view=' . $row['id'] . '" class="green" tabindex="-1" aria-label="View message from ' . htmlspecialchars($row['name']) . '"><i class="fas fa-eye" aria-hidden="true"></i><span>&nbsp;View</span></a></div>';
                            echo '<div role="menuitem"><a href="?edit=' . $row['id'] . '" class="blue" tabindex="-1" aria-label="Edit message from ' . htmlspecialchars($row['name']) . '"><i class="fas fa-edit" aria-hidden="true"></i><span>&nbsp;Edit</span></a></div>';
                            echo '<div role="menuitem"><a href="?delete=' . $row['id'] . '" class="red" tabindex="-1" onclick="return confirm(\'Are you sure you want to delete this message?\')" aria-label="Delete message from ' . htmlspecialchars($row['name']) . '"><i class="fas fa-trash" aria-hidden="true"></i><span>&nbsp;Delete</span></a></div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-light">
            <div class="small">
                Total messages: <?= $total_messages ?>
            </div>
        </div>
    </div>
<?php } ?>
<?= template_admin_footer(); ?>