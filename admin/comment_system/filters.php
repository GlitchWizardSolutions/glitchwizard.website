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
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['word', 'replacement', 'id'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'id';
// Number of results per pagination page
$results_per_page = 10;
// Declare query param variables
$param1 = ($page - 1) * $results_per_page;
$param2 = $results_per_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE (word LIKE :search OR replacement LIKE :search) ' : '';
// Retrieve the total number of filters
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM comment_filters ' . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
$stmt->execute();
$total_filters = $stmt->fetchColumn();
// Prepare filters query
$stmt = $pdo->prepare('SELECT * FROM comment_filters ' . $where . ' ORDER BY ' . $order_by . ' ' . $order . ' LIMIT :start_results,:num_results');
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
$stmt->execute();
// Retrieve query results
$filters = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Delete the filter data
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM comment_filters WHERE id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    header('Location: filters.php?success_msg=3');
    exit;
}
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Filter created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Filter updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Filter deleted successfully!';
    }
    if ($_GET['success_msg'] == 4) {
        $success_msg = 'Filters imported successfully! ' . $_GET['imported'] . ' filters were imported.';
    }
}
// Create URL
$url = 'filters.php?search_query=' . $search;
?>
<?=template_admin_header('Filters', 'comments', 'filters')?>

<div class="content-title mb-4" id="main-filters-view" role="banner" aria-label="Filters List Header">
    <div class="title">
        <div class="icon">
            <i class="bi bi-funnel-fill" aria-hidden="true"></i>
        </div>
        <div class="txt">
            <h2>Filters</h2>
            <p>View, edit, and create word filters.</p>
        </div>
    </div>
</div>

<?php if (isset($success_msg)): ?>
    <div class="mb-4" role="region" aria-label="Success Message">
        <div class="msg success" role="alert" aria-live="polite">
            <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
            <p><?= $success_msg ?></p>
            <button type="button" class="close-success" aria-label="Dismiss success message" onclick="this.parentElement.parentElement.style.display='none'">
                <i class="bi bi-x-circle-fill" aria-hidden="true"></i>
            </button>
        </div>
    </div>
<?php endif; ?>

<!-- Top page actions -->
<div class="d-flex gap-2 mb-4" role="region" aria-label="Page Actions">
    <a href="filter.php" class="btn btn-outline-secondary">
    <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>
        Add Filter
    </a>
</div>

<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Filter Management</h6>
        <small class="text-muted"><?= number_format($total_filters) ?> total filters</small>
    </div>
    <div class="card-body">
        <form action="" method="get" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="search_query" class="form-label">Search</label>
                    <input id="search_query" type="text" name="search_query" class="form-control"
                        placeholder="Search filters..." 
                        value="<?= htmlspecialchars($search, ENT_QUOTES) ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-search me-1" aria-hidden="true"></i>
                        Search
                    </button>
                </div>
            </div>
        </form>

        <!-- Active Filters -->
        <?php if ($search): ?>
            <div class="mb-3">
                <h6 class="mb-2">Active Filters:</h6>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-secondary">
                        Search: <?= htmlspecialchars($search, ENT_QUOTES) ?>
                        <a href="<?= remove_url_param($url, 'search_query') ?>" class="text-white ms-1" aria-label="Remove search filter">×</a>
                    </span>
                </div>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover" role="grid" aria-label="Word Filters">
                <thead class="table-light" role="rowgroup">
                    <tr role="row">
                        <th class="text-start" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'word'; $q['order'] = ($order_by == 'word' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none">
                                Word <?= $order_by == 'word' ? $table_icons[strtolower($order)] : '' ?>
                            </a>
                        </th>
                        <th class="text-start" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'replacement'; $q['order'] = ($order_by == 'replacement' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none">
                                Replacement <?= $order_by == 'replacement' ? $table_icons[strtolower($order)] : '' ?>
                            </a>
                        </th>
                        <th class="text-center" role="columnheader" scope="col" style="width: 80px;">Actions</th>
                    </tr>
                </thead>
                <tbody role="rowgroup">
                    <?php if (empty($filters)): ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">
                            <i class="bi bi-funnel display-6 mb-2 text-muted" aria-hidden="true"></i>
                            <br>
                            No filters found.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($filters as $filter): ?>
                    <tr role="row">
                        <td>
                            <code class="bg-light px-2 py-1 rounded"><?= htmlspecialchars($filter['word'], ENT_QUOTES) ?></code>
                        </td>
                        <td>
                            <?php if (!empty($filter['replacement'])): ?>
                            <code class="bg-light px-2 py-1 rounded"><?= htmlspecialchars($filter['replacement'], ENT_QUOTES) ?></code>
                            <?php else: ?>
                            <span class="text-muted fst-italic">[removed]</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="table-dropdown">
                                <button class="actions-btn" aria-haspopup="true" aria-expanded="false" 
                                        aria-label="Actions for filter: <?= htmlspecialchars($filter['word'], ENT_QUOTES) ?>">
                                    <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">
                                        <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/>
                                    </svg>
                                </button>
                                <div class="table-dropdown-items">
                                    <a href="filter.php?id=<?= $filter['id'] ?>" class="text-decoration-none">
                                        <span class="icon">
                                            <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                        </span>
                                        Edit
                                    </a>
                                    <a href="filters.php?delete=<?= $filter['id'] ?>" class="text-decoration-none red" 
                                       onclick="return confirm('Are you sure you want to delete this filter?')">
                                        <span class="icon">
                                            <i class="bi bi-trash" aria-hidden="true"></i>
                                        </span>
                                        Delete
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($total_filters > $results_per_page): ?>
    <div class="card-footer">
        <nav aria-label="Filters pagination">
            <ul class="pagination pagination-sm justify-content-center mb-0">
                <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $url ?>&page=<?= $page - 1 ?>&order=<?= $order ?>&order_by=<?= $order_by ?>" 
                       aria-label="Previous page">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php endif; ?>
                
                <li class="page-item active">
                    <span class="page-link"><?= $page ?></span>
                </li>
                
                <?php if ($page * $results_per_page < $total_filters): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $url ?>&page=<?= $page + 1 ?>&order=<?= $order ?>&order_by=<?= $order_by ?>" 
                       aria-label="Next page">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="text-center mt-2">
            <small class="text-muted">
                Page <?= $page ?> of <?= ceil($total_filters / $results_per_page) ?> 
                (<?= number_format($total_filters) ?> total filters)
            </small>
        </div>
    </div>
    <?php endif; ?>
</div>

<?=template_admin_footer()?>