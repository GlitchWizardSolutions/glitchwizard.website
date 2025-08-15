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
// Filters parameters
$status = isset($_GET['status']) ? $_GET['status'] : '';
$featured = isset($_GET['featured']) ? $_GET['featured'] : '';
$page_id = isset($_GET['page_id']) ? $_GET['page_id'] : '';
$account = isset($_GET['account']) ? $_GET['account'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['c.id','c.page_id','c.username','c.parent_id','c.content','c.submit_date','c.edited_date','c.votes','c.approved','c.account_id','c.featured','c.top_parent_id'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'c.id';
// Number of results per pagination page
$results_per_page = 10;
// Declare query param variables
$param1 = ($page - 1) * $results_per_page;
$param2 = $results_per_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE (c.username LIKE :search OR c.content LIKE :search) ' : '';
// Status filter
if ($status !== '') {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'c.approved = :status ';
}
// Featured filter
if ($featured !== '') {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'c.featured = :featured ';
}
// Page ID filter
if ($page_id !== '') {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'c.page_id = :page_id ';
}
// Account filter
if ($account !== '') {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'c.account_id = :account ';
}
// Date filters
if ($date_from !== '') {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'c.submit_date >= :date_from ';
}
if ($date_to !== '') {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'c.submit_date <= :date_to ';
}
// Retrieve the total number of comments
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM comments c ' . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($status !== '') $stmt->bindParam('status', $status, PDO::PARAM_INT);
if ($featured !== '') $stmt->bindParam('featured', $featured, PDO::PARAM_INT);
if ($page_id !== '') $stmt->bindParam('page_id', $page_id, PDO::PARAM_INT);
if ($account !== '') $stmt->bindParam('account', $account, PDO::PARAM_INT);
if ($date_from !== '') $stmt->bindParam('date_from', $date_from, PDO::PARAM_STR);
if ($date_to !== '') $stmt->bindParam('date_to', $date_to, PDO::PARAM_STR);
$stmt->execute();
$total_comments = $stmt->fetchColumn();
// Prepare comments query
$stmt = $pdo->prepare('SELECT c.*, a.email, p.url, a.avatar, a.username AS account_username, a.banned FROM comments c LEFT JOIN accounts a ON a.id = c.account_id LEFT JOIN comment_page_details p ON p.page_id = c.page_id ' . $where . ' ORDER BY ' . $order_by . ' ' . $order . ' LIMIT :start_results,:num_results');
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($status !== '') $stmt->bindParam('status', $status, PDO::PARAM_INT);
if ($featured !== '') $stmt->bindParam('featured', $featured, PDO::PARAM_INT);
if ($page_id !== '') $stmt->bindParam('page_id', $page_id, PDO::PARAM_INT);
if ($account !== '') $stmt->bindParam('account', $account, PDO::PARAM_INT);
if ($date_from !== '') $stmt->bindParam('date_from', $date_from, PDO::PARAM_STR);
if ($date_to !== '') $stmt->bindParam('date_to', $date_to, PDO::PARAM_STR);
$stmt->execute();
// Retrieve query results
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Get all accounts for the account filter dropdown
$accounts = $pdo->query('SELECT id, username FROM accounts ORDER BY username ASC')->fetchAll(PDO::FETCH_ASSOC);
// Delete the comment data
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM comments a WHERE a.id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    header('Location: comments.php?success_msg=3');
    exit;
}
// Ban the comment
if (isset($_GET['ban'])) {
    $stmt = $pdo->prepare('UPDATE accounts SET banned = 1 WHERE id = ?');
    $stmt->execute([ $_GET['ban'] ]);
    header('Location: comments.php?success_msg=5');
    exit;
}
// Unban the comment
if (isset($_GET['unban'])) {
    $stmt = $pdo->prepare('UPDATE accounts SET banned = 0 WHERE id = ?');
    $stmt->execute([ $_GET['unban'] ]);
    header('Location: comments.php?success_msg=6');
    exit;
}
// Approve the comment
if (isset($_GET['approve'])) {
    $stmt = $pdo->prepare('UPDATE comments SET approved = 1 WHERE id = ?');
    $stmt->execute([ $_GET['approve'] ]);
    header('Location: comments.php?success_msg=2');
    exit;
}
// Unapprove the comment
if (isset($_GET['unapprove'])) {
    $stmt = $pdo->prepare('UPDATE comments SET approved = 0 WHERE id = ?');
    $stmt->execute([ $_GET['unapprove'] ]);
    header('Location: comments.php?success_msg=2');
    exit;
}
// Feature the comment
if (isset($_GET['feature'])) {
    $stmt = $pdo->prepare('UPDATE comments SET featured = 1 WHERE id = ?');
    $stmt->execute([ $_GET['feature'] ]);
    header('Location: comments.php?success_msg=2');
    exit;
}
// Unfeature the comment
if (isset($_GET['unfeature'])) {
    $stmt = $pdo->prepare('UPDATE comments SET featured = 0 WHERE id = ?');
    $stmt->execute([ $_GET['unfeature'] ]);
    header('Location: comments.php?success_msg=2');
    exit;
}
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Comment created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Comment updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Comment deleted successfully!';
    }
    if ($_GET['success_msg'] == 4) {
        $success_msg = 'Comment imported successfully! ' . $_GET['imported'] . ' comments were imported.';
    }
    if ($_GET['success_msg'] == 5) {
        $success_msg = 'User banned successfully!';
    }
    if ($_GET['success_msg'] == 6) {
        $success_msg = 'User unbanned successfully!';
    }
}
// Create URL
$url = 'comments.php?search_query=' . $search . '&status=' . $status . '&featured=' . $featured . '&page_id=' . $page_id . '&account=' . $account . '&date_from=' . $date_from . '&date_to=' . $date_to;
?>
<?=template_admin_header('Comments', 'comments', 'view')?>

<div class="content-title mb-4" id="main-comments-view" role="banner" aria-label="Comments List Header">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M123.6 391.3c12.9-9.4 29.6-11.8 44.6-6.4c26.5 9.6 56.2 15.1 87.8 15.1c124.7 0 208-80.5 208-160s-83.3-160-208-160S48 160.5 48 240c0 32 12.4 62.8 35.7 89.2c8.6 9.7 12.8 22.5 11.8 35.5c-1.4 18.1-5.7 34.7-11.3 49.4c17-7.9 31.1-16.7 39.4-22.7zM21.2 431.9c1.8-2.7 3.5-5.4 5.1-8.1c10-16.6 19.5-38.4 21.4-62.9C17.7 326.8 0 285.1 0 240C0 125.1 114.6 32 256 32s256 93.1 256 208s-114.6 208-256 208c-37.1 0-72.3-6.4-104.1-17.9c-11.9 8.7-31.3 20.6-54.3 30.6c-15.1 6.6-32.3 12.6-50.1 16.1c-.8 .2-1.6 .3-2.4 .5c-4.4 .8-8.7 1.5-13.2 1.9c-.2 0-.5 .1-.7 .1c-5.1 .5-10.2 .8-15.3 .8c-6.5 0-12.3-3.9-14.8-9.9c-2.5-6-1.1-12.8 3.4-17.4c4.1-4.2 7.8-8.7 11.3-13.5c1.7-2.3 3.3-4.6 4.8-6.9c.1-.2 .2-.3 .3-.5z"/></svg>
        </div>
        <div class="txt">
            <h2>Comments</h2>
            <p>View, edit, and manage comments.</p>
        </div>
    </div>
</div>

<?php if (isset($success_msg)): ?>
    <div class="mb-4" role="region" aria-label="Success Message">
        <div class="msg success" role="alert" aria-live="polite">
            <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                <path
                    d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
            </svg>
            <p><?= $success_msg ?></p>
            <button type="button" class="close-success" aria-label="Dismiss success message" onclick="this.parentElement.parentElement.style.display='none'">
                <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 384 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                    <path
                        d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z" />
                </svg>
            </button>
        </div>
    </div>
<?php endif; ?>

<!-- Top page actions -->
<div class="d-flex gap-2 mb-4" role="region" aria-label="Page Actions">
    <a href="comment.php" class="btn btn-outline-secondary">
        <i class="fas fa-plus me-1" aria-hidden="true"></i>
        Add Comment
    </a>
</div>

<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Comment Management</h6>
        <small class="text-muted"><?= number_format($total_comments) ?> total comments</small>
    </div>
    <div class="card-body">
        <form action="" method="get" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="search_query" class="form-label">Search</label>
                    <input id="search_query" type="text" name="search_query" class="form-control"
                        placeholder="Search comments..." 
                        value="<?= htmlspecialchars($search, ENT_QUOTES) ?>">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="" <?= $status == '' ? ' selected' : '' ?>>All</option>
                        <option value="1" <?= $status == '1' ? ' selected' : '' ?>>Approved</option>
                        <option value="0" <?= $status == '0' ? ' selected' : '' ?>>Pending</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="featured" class="form-label">Featured</label>
                    <select name="featured" id="featured" class="form-select">
                        <option value="" <?= $featured == '' ? ' selected' : '' ?>>All</option>
                        <option value="1" <?= $featured == '1' ? ' selected' : '' ?>>Featured</option>
                        <option value="0" <?= $featured == '0' ? ' selected' : '' ?>>Not Featured</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="page_id" class="form-label">Page ID</label>
                    <input type="text" name="page_id" id="page_id" class="form-control" 
                        placeholder="Page ID" value="<?= htmlspecialchars($page_id, ENT_QUOTES) ?>">
                </div>
                <div class="col-md-3">
                    <label for="account" class="form-label">Account</label>
                    <select name="account" id="account" class="form-select">
                        <option value="" <?= $account == '' ? ' selected' : '' ?>>All</option>
                        <?php foreach ($accounts as $acc): ?>
                        <option value="<?= $acc['id'] ?>" <?= $acc['id'] == $account ? ' selected' : '' ?>>[<?= $acc['id'] ?>] <?= $acc['username'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row g-3 align-items-end mt-2">
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" 
                        value="<?= htmlspecialchars($date_from, ENT_QUOTES) ?>">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" 
                        value="<?= htmlspecialchars($date_to, ENT_QUOTES) ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-filter me-1" aria-hidden="true"></i>
                        Apply Filters
                    </button>
                </div>
            </div>
        </form>

        <!-- Active Filters -->
        <?php if ($status !== '' || $featured !== '' || $page_id !== '' || $account !== '' || $date_from !== '' || $date_to !== '' || $search): ?>
            <div class="mb-3">
                <h6 class="mb-2">Active Filters:</h6>
                <div class="d-flex flex-wrap gap-2">
                    <?php if ($status !== ''): ?>
                        <span class="badge bg-secondary">
                            Status: <?= $status == '1' ? 'Approved' : ($status == '0' ? 'Pending' : 'All') ?>
                            <a href="<?= remove_url_param($url, 'status') ?>" class="text-white ms-1" aria-label="Remove status filter">×</a>
                        </span>
                    <?php endif; ?>
                    <?php if ($featured !== ''): ?>
                        <span class="badge bg-secondary">
                            Featured: <?= $featured == '1' ? 'Featured' : ($featured == '0' ? 'Not Featured' : 'All') ?>
                            <a href="<?= remove_url_param($url, 'featured') ?>" class="text-white ms-1" aria-label="Remove featured filter">×</a>
                        </span>
                    <?php endif; ?>
                    <?php if ($page_id !== ''): ?>
                        <span class="badge bg-secondary">
                            Page ID: <?= htmlspecialchars($page_id, ENT_QUOTES) ?>
                            <a href="<?= remove_url_param($url, 'page_id') ?>" class="text-white ms-1" aria-label="Remove page ID filter">×</a>
                        </span>
                    <?php endif; ?>
                    <?php if ($account !== ''): ?>
                        <span class="badge bg-secondary">
                            Account: <?= htmlspecialchars($account, ENT_QUOTES) ?>
                            <a href="<?= remove_url_param($url, 'account') ?>" class="text-white ms-1" aria-label="Remove account filter">×</a>
                        </span>
                    <?php endif; ?>
                    <?php if ($date_from !== ''): ?>
                        <span class="badge bg-secondary">
                            Date From: <?= htmlspecialchars($date_from, ENT_QUOTES) ?>
                            <a href="<?= remove_url_param($url, 'date_from') ?>" class="text-white ms-1" aria-label="Remove date from filter">×</a>
                        </span>
                    <?php endif; ?>
                    <?php if ($date_to !== ''): ?>
                        <span class="badge bg-secondary">
                            Date To: <?= htmlspecialchars($date_to, ENT_QUOTES) ?>
                            <a href="<?= remove_url_param($url, 'date_to') ?>" class="text-white ms-1" aria-label="Remove date to filter">×</a>
                        </span>
                    <?php endif; ?>
                    <?php if ($search): ?>
                        <span class="badge bg-secondary">
                            Search: <?= htmlspecialchars($search, ENT_QUOTES) ?>
                            <a href="<?= remove_url_param($url, 'search_query') ?>" class="text-white ms-1" aria-label="Remove search filter">×</a>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover" role="grid" aria-label="Comments">
                <thead class="table-light" role="rowgroup">
                    <tr role="row">
                        <th class="text-center" role="columnheader" scope="col" style="width: 60px;">Avatar</th>
                        <th class="text-start" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'c.username'; $q['order'] = ($order_by == 'c.username' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none">
                                Comment <?= $order_by == 'c.username' ? $table_icons[strtolower($order)] : '' ?>
                            </a>
                        </th>
                        <th class="text-center d-none d-md-table-cell" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'c.votes'; $q['order'] = ($order_by == 'c.votes' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none">
                                Votes <?= $order_by == 'c.votes' ? $table_icons[strtolower($order)] : '' ?>
                            </a>
                        </th>
                        <th class="text-center d-none d-lg-table-cell" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'c.page_id'; $q['order'] = ($order_by == 'c.page_id' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none">
                                Page ID <?= $order_by == 'c.page_id' ? $table_icons[strtolower($order)] : '' ?>
                            </a>
                        </th>
                        <th class="text-center d-none d-lg-table-cell" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'c.featured'; $q['order'] = ($order_by == 'c.featured' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none">
                                Featured <?= $order_by == 'c.featured' ? $table_icons[strtolower($order)] : '' ?>
                            </a>
                        </th>
                        <th class="text-center" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'c.approved'; $q['order'] = ($order_by == 'c.approved' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none">
                                Status <?= $order_by == 'c.approved' ? $table_icons[strtolower($order)] : '' ?>
                            </a>
                        </th>
                        <th class="text-center d-none d-lg-table-cell" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'c.submit_date'; $q['order'] = ($order_by == 'c.submit_date' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none">
                                Date <?= $order_by == 'c.submit_date' ? $table_icons[strtolower($order)] : '' ?>
                            </a>
                        </th>
                        <th class="text-center" role="columnheader" scope="col" style="width: 80px;">Actions</th>
                    </tr>
                </thead>
                <tbody role="rowgroup">
                    <?php if (empty($comments)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-comments fa-3x mb-2 text-muted"></i>
                            <br>
                            No comments found.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($comments as $c): ?>
                    <tr role="row">
                        <td class="text-center">
                            <div class="profile-img">
                                <?php if (!empty($c['avatar']) && file_exists('../' . $c['avatar'])): ?>
                                <img src="../<?= $c['avatar'] ?>" alt="<?= htmlspecialchars($c['account_username'], ENT_QUOTES) ?>" 
                                     width="40" height="40" class="rounded-circle">
                                <?php else: ?>
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px; background-color: <?= color_from_string($c['account_username'] ? $c['account_username'] : $c['username']) ?>; color: white; font-weight: bold;">
                                    <?= strtoupper(substr($c['account_username'] ? $c['account_username'] : $c['username'], 0, 1)) ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <a href="comment.php?id=<?= $c['id'] ?>" class="fw-medium text-decoration-none <?= $c['banned'] ? 'text-danger' : '' ?>" 
                                   title="Edit Comment">
                                    <?= htmlspecialchars($c['account_username'] ? $c['account_username'] : $c['username'], ENT_QUOTES) ?>
                                    <?php if ($c['banned']): ?>
                                    <i class="fas fa-ban text-danger ms-1" title="Banned User"></i>
                                    <?php endif; ?>
                                </a>
                                <small class="text-muted">
                                    <?= mb_strimwidth(strip_tags(str_replace('<br>', ' ', $c['content'])), 0, 80, "...") ?>
                                </small>
                            </div>
                        </td>
                        <td class="text-center d-none d-md-table-cell">
                            <span class="badge bg-light text-dark"><?= number_format($c['votes']) ?></span>
                        </td>
                        <td class="text-center d-none d-lg-table-cell">
                            <?php if ($c['url']): ?>
                            <a href="<?= htmlspecialchars($c['url'], ENT_QUOTES) ?>" target="_blank" 
                               class="text-decoration-none" title="<?= $c['url'] ?>">
                                <i class="fas fa-external-link-alt me-1"></i><?= $c['page_id'] ?>
                            </a>
                            <?php else: ?>
                            <?= $c['page_id'] ?>
                            <?php endif; ?>
                        </td>
                        <td class="text-center d-none d-lg-table-cell">
                            <?php if ($c['featured']): ?>
                            <i class="fas fa-star text-warning" title="Featured"></i>
                            <?php else: ?>
                            <i class="far fa-star text-muted" title="Not Featured"></i>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($c['approved']): ?>
                            <span class="green">Approved</span>
                            <?php else: ?>
                            <span class="orange">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center d-none d-lg-table-cell">
                            <small class="text-muted"><?= date('M j, Y', strtotime($c['submit_date'])) ?></small>
                        </td>
                        <td class="text-center">
                            <div class="table-dropdown">
                                <button class="actions-btn" aria-haspopup="true" aria-expanded="false" 
                                        aria-label="Actions for comment by <?= htmlspecialchars($c['account_username'] ? $c['account_username'] : $c['username'], ENT_QUOTES) ?>">
                                    <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">
                                        <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/>
                                    </svg>
                                </button>
                                <div class="table-dropdown-items">
                                    <a href="comment.php?id=<?= $c['id'] ?>" class="text-decoration-none">
                                        <span class="icon">
                                            <i class="fas fa-edit"></i>
                                        </span>
                                        Edit
                                    </a>
                                    <a href="comment.php?reply=<?= $c['id'] ?>" class="text-decoration-none">
                                        <span class="icon">
                                            <i class="fas fa-reply"></i>
                                        </span>
                                        Reply
                                    </a>
                                    <?php if (!$c['featured']): ?>
                                    <a href="comments.php?feature=<?= $c['id'] ?>" class="text-decoration-none green" 
                                       onclick="return confirm('Are you sure you want to feature this comment?')">
                                        <span class="icon">
                                            <i class="fas fa-star"></i>
                                        </span>
                                        Feature
                                    </a>
                                    <?php else: ?>
                                    <a href="comments.php?unfeature=<?= $c['id'] ?>" class="text-decoration-none red" 
                                       onclick="return confirm('Are you sure you want to unfeature this comment?')">
                                        <span class="icon">
                                            <i class="far fa-star"></i>
                                        </span>
                                        Unfeature
                                    </a>
                                    <?php endif; ?>
                                    <?php if (!$c['approved']): ?>
                                    <a href="comments.php?approve=<?= $c['id'] ?>" class="text-decoration-none green" 
                                       onclick="return confirm('Are you sure you want to approve this comment?')">
                                        <span class="icon">
                                            <i class="fas fa-check"></i>
                                        </span>
                                        Approve
                                    </a>
                                    <?php else: ?>
                                    <a href="comments.php?unapprove=<?= $c['id'] ?>" class="text-decoration-none orange" 
                                       onclick="return confirm('Are you sure you want to unapprove this comment?')">
                                        <span class="icon">
                                            <i class="fas fa-times"></i>
                                        </span>
                                        Unapprove
                                    </a>
                                    <?php endif; ?>
                                    <?php if ($c['account_id'] > 0 && !$c['banned']): ?>
                                    <a href="comments.php?ban=<?= $c['account_id'] ?>" class="text-decoration-none red" 
                                       onclick="return confirm('Are you sure you want to ban this user?')">
                                        <span class="icon">
                                            <i class="fas fa-ban"></i>
                                        </span>
                                        Ban User
                                    </a>
                                    <?php elseif ($c['account_id'] > 0 && $c['banned']): ?>
                                    <a href="comments.php?unban=<?= $c['account_id'] ?>" class="text-decoration-none green" 
                                       onclick="return confirm('Are you sure you want to unban this user?')">
                                        <span class="icon">
                                            <i class="fas fa-user-check"></i>
                                        </span>
                                        Unban User
                                    </a>
                                    <?php endif; ?>
                                    <a href="comments.php?delete=<?= $c['id'] ?>" class="text-decoration-none red" 
                                       onclick="return confirm('Are you sure you want to delete this comment?')">
                                        <span class="icon">
                                            <i class="fas fa-trash"></i>
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
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($total_comments > $results_per_page): ?>
    <div class="card-footer">
        <nav aria-label="Comments pagination">
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
                
                <?php if ($page * $results_per_page < $total_comments): ?>
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
                Page <?= $page ?> of <?= ceil($total_comments / $results_per_page) ?> 
                (<?= number_format($total_comments) ?> total comments)
            </small>
        </div>
    </div>
    <?php endif; ?>
</div>

<?=template_admin_footer()?>