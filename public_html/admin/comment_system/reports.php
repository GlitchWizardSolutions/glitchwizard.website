<?php
include '../assets/includes/main.php';

// Use simple triangle icons for sort direction, matching canonical admin pattern
$table_icons = [
    'asc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9650;</span>', // ▲
    'desc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9660;</span>' // ▼
];

// Retrieve the GET request parameters (if specified)
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$search = isset($_GET['search_query']) ? $_GET['search_query'] : '';
$reason_filter = isset($_GET['reason_filter']) ? $_GET['reason_filter'] : '';
$reporter_filter = isset($_GET['reporter_filter']) ? $_GET['reporter_filter'] : '';
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'ASC' ? 'ASC' : 'DESC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['cr.id','cr.reason','c.display_name','reporter_display_name','comment_author_display_name'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'cr.id';
// Number of results per pagination page
$results_per_page = 10;
// Declare query param variables
$param1 = ($page - 1) * $results_per_page;
$param2 = $results_per_page;
$param3 = '%' . $search . '%';
$param4 = '%' . $reason_filter . '%';
$param5 = '%' . $reporter_filter . '%';
// SQL where clause
$where = '';
$conditions = [];
if ($search) $conditions[] = '(cr.reason LIKE :search)';
if ($reason_filter) $conditions[] = '(cr.reason LIKE :reason_filter)';
if ($reporter_filter) $conditions[] = '(a2.username LIKE :reporter_filter)';
if (!empty($conditions)) $where = 'WHERE ' . implode(' AND ', $conditions);
// Retrieve the total number of reports
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM comment_reports cr LEFT JOIN accounts a2 ON a2.id = cr.account_id ' . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($reason_filter) $stmt->bindParam('reason_filter', $param4, PDO::PARAM_STR);
if ($reporter_filter) $stmt->bindParam('reporter_filter', $param5, PDO::PARAM_STR);
$stmt->execute();
$total_reports = $stmt->fetchColumn();
// Prepare reports query
$stmt = $pdo->prepare('SELECT c.*, cr.*, a.username AS comment_author_display_name, a.avatar AS comment_author_profile_photo, a.banned AS comment_author_banned, a.id AS comment_author_id, a2.username AS reporter_display_name, a2.avatar AS reporter_profile_photo, a2.banned AS reporter_banned FROM comment_reports cr JOIN comments c ON c.id = cr.comment_id LEFT JOIN accounts a ON a.id = c.account_id LEFT JOIN accounts a2 ON a2.id = cr.account_id ' . $where . ' ORDER BY ' . $order_by . ' ' . $order . ' LIMIT :start_results,:num_results');
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($reason_filter) $stmt->bindParam('reason_filter', $param4, PDO::PARAM_STR);
if ($reporter_filter) $stmt->bindParam('reporter_filter', $param5, PDO::PARAM_STR);
$stmt->execute();
// Retrieve query results
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Delete the report data
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM comment_reports WHERE id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    header('Location: reports.php?success_msg=3');
    exit;
}
// Delete report data and comments
if (isset($_GET['delete_all'])) {
    $stmt = $pdo->prepare('DELETE cr, c FROM comment_reports cr LEFT JOIN comments c ON c.id = cr.comment_id WHERE cr.id = ?');
    $stmt->execute([ $_GET['delete_all'] ]);
    header('Location: reports.php?success_msg=3');
    exit;
}
// Ban user
if (isset($_GET['ban'])) {
    $stmt = $pdo->prepare('UPDATE accounts SET banned = 1 WHERE id = ?');
    $stmt->execute([ $_GET['ban'] ]);
    header('Location: reports.php?success_msg=5');
    exit;
}
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Report created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Report updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Report deleted successfully!';
    }
    if ($_GET['success_msg'] == 4) {
        $success_msg = 'Reports imported successfully! ' . $_GET['imported'] . ' reports were imported.';
    }
    if ($_GET['success_msg'] == 5) {
        $success_msg = 'User banned successfully!';
    }
}
// Create URL
$url = 'reports.php?search_query=' . $search;
?>
<?=template_admin_header('Reports', 'comments', 'reports')?>

<div class="content-title mb-4">
    <div class="title">
        <div class="icon">
            <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M11,15H13V17H11V15M11,7H13V13H11V7M12,2C6.47,2 2,6.5 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20Z" /></svg>
        </div>
        <div class="txt">
            <h2>Reports</h2>
            <p>View, edit, and create reports.</p>
        </div>
    </div>
</div>
<?php if (isset($success_msg)): ?>
<div class="msg success">
    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
    <p><?=$success_msg?></p>
    <i class="bi bi-x-circle-fill close" aria-hidden="true"></i>
</div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="report.php" class="btn btn-outline-secondary">
            <i class="bi bi-plus-lg me-2" aria-hidden="true"></i>Create Report
        </a>
    </div>
</div>

<form method="get" class="mb-3">
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label for="search_query" class="form-label">Search Reports</label>
            <div class="input-group">
                <input type="text" class="form-control" id="search_query" name="search_query" 
                       placeholder="Search report reason..." 
                       value="<?= htmlspecialchars($search, ENT_QUOTES) ?>">
                <button type="submit" class="btn btn-outline-secondary">
                    <i class="bi bi-search" aria-hidden="true"></i>
                </button>
            </div>
        </div>
        <div class="col-md-3">
            <label for="reason_filter" class="form-label">Filter by Reason</label>
            <select class="form-select" id="reason_filter" name="reason_filter">
                <option value="">All Reasons</option>
                <option value="spam" <?= $reason_filter === 'spam' ? 'selected' : '' ?>>Spam</option>
                <option value="harassment" <?= $reason_filter === 'harassment' ? 'selected' : '' ?>>Harassment</option>
                <option value="inappropriate" <?= $reason_filter === 'inappropriate' ? 'selected' : '' ?>>Inappropriate</option>
                <option value="offensive" <?= $reason_filter === 'offensive' ? 'selected' : '' ?>>Offensive</option>
                <option value="misinformation" <?= $reason_filter === 'misinformation' ? 'selected' : '' ?>>Misinformation</option>
                <option value="off-topic" <?= $reason_filter === 'off-topic' ? 'selected' : '' ?>>Off-topic</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="reporter_filter" class="form-label">Filter by Reporter</label>
            <input type="text" class="form-control" id="reporter_filter" name="reporter_filter" 
                   placeholder="Reporter username..." 
                   value="<?= htmlspecialchars($reporter_filter, ENT_QUOTES) ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-success w-100">Apply Filters</button>
            <?php if ($search || $reason_filter || $reporter_filter): ?>
            <a href="reports.php" class="btn btn-outline-secondary w-100 mt-2">Clear</a>
            <?php endif; ?>
        </div>
    </div>
</form>

<?php if ($search || $reason_filter || $reporter_filter): ?>
<div class="mb-3">
    <h6 class="text-muted mb-2">Active Filters:</h6>
    <?php if ($search): ?>
    <span class="badge bg-primary me-2">
        Search: "<?= htmlspecialchars($search, ENT_QUOTES) ?>"
        <a href="<?= remove_url_param($url, 'search_query') ?>" class="text-white ms-1 text-decoration-none">×</a>
    </span>
    <?php endif; ?>
    <?php if ($reason_filter): ?>
    <span class="badge bg-info me-2">
        Reason: "<?= htmlspecialchars($reason_filter, ENT_QUOTES) ?>"
        <a href="<?= remove_url_param($url, 'reason_filter') ?>" class="text-white ms-1 text-decoration-none">×</a>
    </span>
    <?php endif; ?>
    <?php if ($reporter_filter): ?>
    <span class="badge bg-warning me-2">
        Reporter: "<?= htmlspecialchars($reporter_filter, ENT_QUOTES) ?>"
        <a href="<?= remove_url_param($url, 'reporter_filter') ?>" class="text-white ms-1 text-decoration-none">×</a>
    </span>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Comment Reports</h5>
        <div class="d-flex gap-2 align-items-center ms-auto">
            <small class="text-muted"><?= number_format($total_reports) ?> total reports</small>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">Author</th>
                        <th><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=c.display_name'?>" class="text-decoration-none">Reported Comment<?=$order_by=='c.display_name' ? $table_icons[strtolower($order)] : ''?></a></th>
                        <th style="width: 60px;" class="d-none d-md-table-cell">Reporter</th>
                        <th class="d-none d-md-table-cell"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=reporter_display_name'?>" class="text-decoration-none">Reported By<?=$order_by=='reporter_display_name' ? $table_icons[strtolower($order)] : ''?></a></th>
                        <th><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=cr.reason'?>" class="text-decoration-none">Reason<?=$order_by=='cr.reason' ? $table_icons[strtolower($order)] : ''?></a></th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$reports): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-exclamation-triangle-fill fs-1 mb-3 text-muted" aria-hidden="true"></i>
                            <p class="mb-0">No reports found</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php foreach ($reports as $report): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if (!empty($report['comment_author_profile_photo']) && file_exists('../' . $report['comment_author_profile_photo'])): ?>
                                <img src="../<?= $report['comment_author_profile_photo'] ?>" alt="<?= htmlspecialchars($report['comment_author_display_name'], ENT_QUOTES) ?>" 
                                     class="rounded-circle me-2" width="40" height="40">
                                <?php else: ?>
                                <div class="avatar-circle me-2" style="background-color:<?= color_from_string($report['comment_author_display_name'] ? $report['comment_author_display_name'] : $report['display_name']) ?>">
                                    <?= strtoupper(substr($report['comment_author_display_name'] ? $report['comment_author_display_name'] : $report['display_name'], 0, 1)) ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <div>
                                <a href="comment.php?id=<?= $report['comment_id'] ?>" class="text-decoration-none fw-bold <?= $report['comment_author_banned'] ? 'text-danger' : '' ?>">
                                    <?= htmlspecialchars($report['comment_author_display_name'] ? $report['comment_author_display_name'] : $report['display_name'], ENT_QUOTES) ?>
                                    <?php if ($report['comment_author_banned']): ?>
                                    <i class="bi bi-slash-circle-fill text-danger ms-1" title="Banned User" aria-hidden="true"></i>
                                    <?php endif; ?>
                                </a>
                                <div class="mt-1">
                                    <small class="text-muted">
                                        <?= mb_strimwidth(strip_tags(str_replace('<br>', ' ', $report['content'])), 0, 80, "...") ?>
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td class="d-none d-md-table-cell">
                            <?php if ($report['reporter_display_name']): ?>
                            <div class="d-flex align-items-center">
                                <?php if (!empty($report['reporter_profile_photo']) && file_exists('../' . $report['reporter_profile_photo'])): ?>
                                <img src="../<?= $report['reporter_profile_photo'] ?>" alt="<?= htmlspecialchars($report['reporter_display_name'], ENT_QUOTES) ?>" 
                                     class="rounded-circle" width="32" height="32">
                                <?php else: ?>
                                <div class="avatar-circle-sm" style="background-color:<?= color_from_string($report['reporter_display_name']) ?>">
                                    <?= strtoupper(substr($report['reporter_display_name'], 0, 1)) ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php else: ?>
                            <span class="text-muted">--</span>
                            <?php endif; ?>
                        </td>
                        <td class="d-none d-md-table-cell">
                            <?php if ($report['reporter_display_name']): ?>
                            <span class="<?= $report['reporter_banned'] ? 'text-danger' : '' ?>">
                                <?= htmlspecialchars($report['reporter_display_name'], ENT_QUOTES) ?>
                                <?php if ($report['reporter_banned']): ?>
                                <i class="bi bi-slash-circle-fill text-danger ms-1" title="Banned User" aria-hidden="true"></i>
                                <?php endif; ?>
                            </span>
                            <?php else: ?>
                            <span class="text-muted">--</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-warning text-dark">
                                <?= htmlspecialchars($report['reason'], ENT_QUOTES) ?>
                            </span>
                        </td>
                        <td>
                            <div class="table-dropdown">
                                <button class="actions-btn btn btn-sm btn-outline-secondary">Actions</button>
                                <div class="table-dropdown-items">
                                    <a href="report.php?id=<?= $report['id'] ?>">Edit</a>
                                    <?php if (!empty($report['comment_author_display_name']) && !$report['comment_author_banned']): ?>
                                    <a href="reports.php?ban=<?= $report['comment_author_id'] ?>" onclick="return confirm('Are you sure you want to ban this user?')">Ban User</a>
                                    <?php endif; ?>
                                    <a href="reports.php?delete=<?= $report['id'] ?>" onclick="return confirm('Are you sure you want to delete this report?')">Delete</a>
                                    <a href="reports.php?delete_all=<?= $report['id'] ?>" onclick="return confirm('Are you sure you want to delete this report + comments?')">Delete + Comments</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm mb-0">
                <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $url ?>&page=<?= $page-1 ?>&order=<?= $order ?>&order_by=<?= $order_by ?>">
                        <i class="bi bi-chevron-left" aria-hidden="true"></i> Previous
                    </a>
                </li>
                <?php endif; ?>
                <li class="page-item disabled">
                    <span class="page-link">
                        Page <?= $page ?> of <?= ceil($total_reports / $results_per_page) == 0 ? 1 : ceil($total_reports / $results_per_page) ?>
                    </span>
                </li>
                <?php if ($page * $results_per_page < $total_reports): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $url ?>&page=<?= $page+1 ?>&order=<?= $order ?>&order_by=<?= $order_by ?>">
                        Next <i class="bi bi-chevron-right" aria-hidden="true"></i>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>

<?=template_admin_footer()?>