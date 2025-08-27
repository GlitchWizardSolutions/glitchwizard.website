<?php
include '../assets/includes/main.php';
// Retrieve the GET request parameters (if specified)
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$search = isset($_GET['search_query']) ? $_GET['search_query'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['cpd.id', 'cpd.title', 'cpd.description', 'cpd.url', 'cpd.page_status', 'cpd.page_id', 'num_comments'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'cpd.id';
// Number of results per pagination page
$results_per_page = 10;
// Declare query param variables
$param1 = ($page - 1) * $results_per_page;
$param2 = $results_per_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE (cpd.title LIKE :search OR cpd.description LIKE :search OR cpd.url LIKE :search) ' : '';
// Status filter
if ($status !== '') {
    $where .= ($search ? 'AND ' : 'WHERE ') . 'cpd.page_status = :status ';
}
// Retrieve the total number of pages
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM comment_page_details cpd ' . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($status !== '') $stmt->bindParam('status', $status, PDO::PARAM_INT);
$stmt->execute();
$total_pages = $stmt->fetchColumn();
// Prepare pages query
$stmt = $pdo->prepare('SELECT cpd.*, (SELECT COUNT(*) FROM comments WHERE page_id = cpd.page_id) AS num_comments FROM comment_page_details cpd ' . $where . ' ORDER BY ' . $order_by . ' ' . $order . ' LIMIT :start_results,:num_results');
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($status !== '') $stmt->bindParam('status', $status, PDO::PARAM_INT);
$stmt->execute();
// Retrieve query results
$pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Delete the page data
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM comment_page_details WHERE id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    header('Location: pages.php?success_msg=3');
    exit;
}
// Open the page
if (isset($_GET['open'])) {
    $stmt = $pdo->prepare('UPDATE comment_page_details SET page_status = 1 WHERE id = ?');
    $stmt->execute([ $_GET['open'] ]);
    header('Location: pages.php?success_msg=2');
    exit;
}
// Close the page
if (isset($_GET['close'])) {
    $stmt = $pdo->prepare('UPDATE comment_page_details SET page_status = 0 WHERE id = ?');
    $stmt->execute([ $_GET['close'] ]);
    header('Location: pages.php?success_msg=2');
    exit;
}
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Page created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Page updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Page deleted successfully!';
    }
    if ($_GET['success_msg'] == 4) {
        $success_msg = 'Pages imported successfully! ' . $_GET['imported'] . ' pages were imported.';
    }
}
// Create URL
$url = 'pages.php?search_query=' . $search . '&status=' . $status;
?>
<?=template_admin_header('Pages', 'comments', 'pages')?>

<?php
// Table sorting icons
$table_icons = [
    'asc' => '&#9650;',
    'desc' => '&#9660;'
];
?>

<div class="content-title mb-4">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M11 15H17V17H11V15M9 7H7V9H9V7M11 13H17V11H11V13M11 9H17V7H11V9M9 11H7V13H9V11M21 5V19C21 20.1 20.1 21 19 21H5C3.9 21 3 20.1 3 19V5C3 3.9 3.9 3 5 3H19C20.1 3 21 3.9 21 5M19 5H5V19H19V5M9 15H7V17H9V15Z" /></svg>
        </div>
        <div class="txt">
            <h2>Pages</h2>
            <p>View, edit, and create pages. Pages are created automatically.</p>
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
        <a href="page.php" class="btn btn-outline-secondary">
            <i class="bi bi-plus me-2"></i>Create Page
        </a>
    </div>
</div>

    <form method="get" class="mb-3">
        <div class="row g-3 align-items-end">
            <div class="col-md-6">
                <label for="search_query" class="form-label">Search Pages</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="search_query" name="search_query" 
                           placeholder="Search page title, description, or URL..." 
                           value="<?= htmlspecialchars($search, ENT_QUOTES) ?>">
                    <button type="submit" class="btn btn-outline-secondary">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value=""<?= $status == '' ? ' selected' : '' ?>>All Status</option>
                    <option value="1"<?= $status == '1' ? ' selected' : '' ?>>Open</option>
                    <option value="0"<?= $status == '0' ? ' selected' : '' ?>>Closed</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-success">Apply Filters</button>
                <?php if ($search || $status !== ''): ?>
                <a href="pages.php" class="btn btn-outline-secondary ms-2">Clear</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

    <?php if ($status !== '' || $search): ?>
    <div class="mb-3">
        <h6 class="text-muted mb-2">Active Filters:</h6>
        <?php if ($status !== ''): ?>
        <span class="badge bg-primary me-2">
            Status: <?= $status == '1' ? 'Open' : 'Closed' ?>
            <a href="<?= remove_url_param($url, 'status') ?>" class="text-white ms-1 text-decoration-none">×</a>
        </span>
        <?php endif; ?>
        <?php if ($search): ?>
        <span class="badge bg-primary me-2">
            Search: "<?= htmlspecialchars($search, ENT_QUOTES) ?>"
            <a href="<?= remove_url_param($url, 'search_query') ?>" class="text-white ms-1 text-decoration-none">×</a>
        </span>
        <?php endif; ?>
    </div>
    <?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Comment Pages</h5>
        <div class="d-flex gap-2 align-items-center ms-auto">
            <small class="text-muted"><?= number_format($total_pages) ?> total pages</small>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=cpd.title'?>" class="text-decoration-none">Title<?=$order_by=='cpd.title' ? $table_icons[strtolower($order)] : ''?></a></th>
                        <th><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=cpd.page_id'?>" class="text-decoration-none">Page ID<?=$order_by=='cpd.page_id' ? $table_icons[strtolower($order)] : ''?></a></th>
                        <th class="d-none d-md-table-cell"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=cpd.url'?>" class="text-decoration-none">URL<?=$order_by=='cpd.url' ? $table_icons[strtolower($order)] : ''?></a></th>
                        <th><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=num_comments'?>" class="text-decoration-none"># Comments<?=$order_by=='num_comments' ? $table_icons[strtolower($order)] : ''?></a></th>
                        <th><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=cpd.page_status'?>" class="text-decoration-none">Status<?=$order_by=='cpd.page_status' ? $table_icons[strtolower($order)] : ''?></a></th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$pages): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-file-earmark-text fa-3x mb-3 text-muted"></i>
                            <p class="mb-0">No pages found</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php foreach ($pages as $p): ?>
                    <tr>
                        <td>
                            <div>
                                <strong><?= htmlspecialchars($p['title'], ENT_QUOTES) ?></strong>
                                <?php if ($p['description']): ?>
                                <br><small class="text-muted"><?= htmlspecialchars($p['description'], ENT_QUOTES) ?></small>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="text-muted"><?= $p['page_id'] ?></td>
                        <td class="d-none d-md-table-cell">
                            <?php if ($p['url']): ?>
                            <a href="<?= htmlspecialchars($p['url'], ENT_QUOTES) ?>" target="_blank" class="text-decoration-none">
                                <?= htmlspecialchars($p['url'], ENT_QUOTES) ?>
                            </a>
                            <?php else: ?>
                            <span class="text-muted">--</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="comments.php?page_id=<?= $p['page_id'] ?>" class="text-decoration-none">
                                <?= $p['num_comments'] ? number_format($p['num_comments']) : 0 ?>
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-<?= $p['page_status'] ? 'success' : 'danger' ?>">
                                <?= $p['page_status'] ? 'Open' : 'Closed' ?>
                            </span>
                        </td>
                        <td>
                            <div class="table-dropdown">
                                <button class="actions-btn btn btn-sm btn-outline-secondary">Actions</button>
                                <div class="table-dropdown-items">
                                    <a href="page.php?id=<?= $p['id'] ?>">Edit</a>
                                    <?php if (!$p['page_status']): ?>
                                    <a href="pages.php?open=<?= $p['id'] ?>" onclick="return confirm('Are you sure you want to open this page?')">Open</a>
                                    <?php else: ?>
                                    <a href="pages.php?close=<?= $p['id'] ?>" onclick="return confirm('Are you sure you want to close this page?')">Close</a>
                                    <?php endif; ?>
                                    <a href="pages.php?delete=<?= $p['id'] ?>" onclick="return confirm('Are you sure you want to delete this page?')">Delete</a>
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
                        <i class="bi bi-chevron-left"></i> Previous
                    </a>
                </li>
                <?php endif; ?>
                <li class="page-item disabled">
                    <span class="page-link">
                        Page <?= $page ?> of <?= ceil($total_pages / $results_per_page) == 0 ? 1 : ceil($total_pages / $results_per_page) ?>
                    </span>
                </li>
                <?php if ($page * $results_per_page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $url ?>&page=<?= $page+1 ?>&order=<?= $order ?>&order_by=<?= $order_by ?>">
                        Next <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>

<?=template_admin_footer()?>