<?php
/* 
 * Blog Comments Management
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: comments.php
 * LOCATION: /public_html/admin/blog/
 * PURPOSE: Manage and moderate blog comments
 * DETAILED DESCRIPTION:
 * This file provides a comprehensive interface for managing blog comments,
 * including moderation, approval, editing, and deletion. It features
 * filtering, sorting, pagination, and accessibility-compliant forms for
 * comment management and moderation.
 * REQUIRED FILES: 
 * - /public_html/assets/includes/main.php
 * - /public_html/assets/includes/settings/blog_config.php
 * - /public_html/assets/includes/settings/comments_config.php
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: [IN DEVELOPMENT]
 * 
 * FEATURES:
 * - Comment moderation
 * - Filtering and sorting
 * - Pagination system
 * - Accessibility compliance
 * - Security measures
 */

include_once "header.php";

// CSRF token generation
if (empty($_SESSION['csrf_token']))
{
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
/**
 * Helper to get author name and avatar URL for a comment row
 * @param array $row Blog comment row
 * @return array ['author' => string, 'avatar_url' => string, 'badge' => string]
 */
function getAuthorAndAvatar($row)
{
    global $pdo;
    $author = '';
    $avatar = 'avatar.png';
    $badge = '';
    if (isset($row['guest']) && $row['guest'] == 'Yes')
    {
        $author = $row['username'];
        $badge = ' <span class="badge bg-info"><i class="fas fa-user"></i> Guest</span>';
    } else
    {
        if (!empty($row['account_id']))
        {
            $stmtAcc = $pdo->prepare('SELECT username, avatar FROM accounts WHERE id = ? LIMIT 1');
            $stmtAcc->execute([$row['account_id']]);
            if ($acc = $stmtAcc->fetch(PDO::FETCH_ASSOC))
            {
                $author = $acc['username'];
                if (!empty($acc['avatar']))
                    $avatar = $acc['avatar'];
            }
        }
        if (empty($author) && !empty($row['user_id']))
        {
            $stmtUser = $pdo->prepare('SELECT username, avatar FROM accounts WHERE id = ? LIMIT 1');
            $stmtUser->execute([$row['user_id']]);
            if ($usr = $stmtUser->fetch(PDO::FETCH_ASSOC))
            {
                $author = $usr['username'];
                if (!empty($usr['avatar']))
                    $avatar = $usr['avatar'];
            }
        }
        if (empty($author))
            $author = 'Unknown';
    }
    $avatar_account = [
        'avatar' => ($avatar != 'avatar.png') ? $avatar : '',
        'role' => (isset($row['guest']) && $row['guest'] == 'Yes') ? 'Guest' : 'Member'
    ];
    $avatar_url = getUserAvatar($avatar_account);
    return [
        'author' => $author,
        'avatar_url' => $avatar_url,
        'badge' => $badge
    ];
}
include_once '../assets/includes/main.php';
// Handle success messages from dropdown menu choices.
if (isset($_GET['success_msg']))
{
    if ($_GET['success_msg'] == 2)
    {
        $success_msg = 'Comment updated successfully!';
    }
    if ($_GET['success_msg'] == 3)
    {
        $success_msg = 'Comment deleted successfully!';
    }
}
// Approve comment from dropdown menu
if (isset($_GET['approve']))
{
    $stmt = $pdo->prepare("UPDATE blog_comments SET approved = 'Yes' WHERE id = ?");
    $stmt->execute([$_GET['approve']]);
    header('Location: comments.php?success_msg=2');
    exit;
}
// Reject comment from dropdown menu
if (isset($_GET['reject']))
{
    $stmt = $pdo->prepare("UPDATE blog_comments SET approved = 'No' WHERE id = ?");
    $stmt->execute([$_GET['reject']]);
    header('Location: comments.php?success_msg=2');
    exit;
}
// Delete comment from dropdown menu
if (isset($_GET['delete']))
{
    $stmt = $pdo->prepare('DELETE FROM blog_comments WHERE id = ?');
    $stmt->execute([$_GET['delete']]);
    header('Location: comments.php?success_msg=3');
    exit;
}
// Edit comment from dropdown menu

// Handle edit form submission
if (isset($_POST['edit']))
{
    // CSRF token validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token'])
    {
        die('Invalid CSRF token');
    }
    $edit_id = (int) $_POST['edit_comment_id'];
    $edit_author = trim($_POST['edit_author'] ?? '');
    $edit_comment = trim($_POST['edit_comment'] ?? '');
    $edit_approved = $_POST['edit_approved'] ?? 'No';
    $stmt = $pdo->prepare('UPDATE blog_comments SET username = ?, comment = ?, approved = ? WHERE id = ?');
    $stmt->execute([$edit_author, $edit_comment, $edit_approved, $edit_id]);
    unset($_SESSION['csrf_token']); // Regenerate after use
    header('Location: comments.php?success_msg=2');
    exit;
}

// Use simple triangle icons for sort direction, matching Table.php format
$table_icons = [
    'asc' => '<span style="font-size:13px;color:#666;margin-left:4px;">&#9650;</span>', // ▲
    'desc' => '<span style="font-size:13px;color:#666;margin-left:4px;">&#9660;</span>' // ▼
];
// Retrieve the GET request parameters (if specified)
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$search = isset($_GET['search_query']) ? $_GET['search_query'] : '';
// Filters parameters
$status = isset($_GET['status']) ? $_GET['status'] : '';
$post_id = isset($_GET['post_id']) ? $_GET['post_id'] : '';
$approved = isset($_GET['approved']) ? $_GET['approved'] : '';
$username = isset($_GET['username']) ? $_GET['username'] : '';
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array
// Add 'title' to allow sorting by post title
$order_by_whitelist = ['id', 'author', 'date', 'approved', 'post_id', 'title'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'id';
// Number of results per pagination page
$results_per_page = 20;
// Comments array
$comments = [];
// Declare query param variables
$param1 = ($page - 1) * $results_per_page;
$param2 = $results_per_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE (author LIKE :search OR comment LIKE :search) ' : '';
// Add filters
if ($status == 'Pending')
{
    $where .= ($where ? 'AND ' : 'WHERE ') . "approved = 'No' ";
} else if ($status == 'Approved')
{
    $where .= ($where ? 'AND ' : 'WHERE ') . "approved = 'Yes' ";
}
if ($approved == 'No')
{
    $where .= ($where ? 'AND ' : 'WHERE ') . "approved = 'No' ";
} else if ($approved == 'Yes')
{
    $where .= ($where ? 'AND ' : 'WHERE ') . "approved = 'Yes' ";
}
if ($post_id)
{
    $where .= ($where ? 'AND ' : 'WHERE ') . 'post_id = :post_id ';
}
// Retrieve the total number of comments
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM blog_comments ' . $where);
if ($search)
    $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($post_id)
    $stmt->bindParam('post_id', $post_id, PDO::PARAM_INT);
$stmt->execute();
$total_comments = $stmt->fetchColumn();
// Prepare comments query with join to blog_posts for title
$order_by_sql = $order_by;
if ($order_by === 'title')
{
    $order_by_sql = 'blog_posts.title';
} elseif ($order_by === 'post_id')
{
    $order_by_sql = 'blog_comments.post_id';
} elseif ($order_by === 'author')
{
    $order_by_sql = 'blog_comments.username';
} elseif ($order_by === 'date')
{
    $order_by_sql = 'blog_comments.date';
} elseif ($order_by === 'approved')
{
    $order_by_sql = 'blog_comments.approved';
} else
{
    $order_by_sql = 'blog_comments.id';
}
$sql = 'SELECT blog_comments.*, blog_posts.title FROM blog_comments LEFT JOIN blog_posts ON blog_comments.post_id = blog_posts.id ' . $where . ' ORDER BY ' . $order_by_sql . ' ' . $order . ' LIMIT :start_results,:num_results';
$stmt = $pdo->prepare($sql);
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search)
    $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($post_id)
    $stmt->bindParam('post_id', $post_id, PDO::PARAM_INT);
$stmt->execute();
// Retrieve query results
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Create URL
$url = 'comments.php?search_query=' . $search . '&status=' . $status . '&approved=' . $approved . '&post_id=' . $post_id . '&username=' . $username;
?>
<?= template_admin_header('Blog Comments', 'blog', 'comments') ?>

<div class="content-title mb-4" id="main-blog-comments" role="banner" aria-label="Blog Comments Management Header">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                <path d="M96 96c0-35.3 28.7-64 64-64H448c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H80c-44.2 0-80-35.8-80-80V128c0-17.7 14.3-32 32-32s32 14.3 32 32V400c0 8.8 7.2 16 16 16s16-7.2 16-16V96zm64 24v80c0 13.3 10.7 24 24 24H296c13.3 0 24-10.7 24-24V120c0-13.3-10.7-24-24-24H184c-13.3 0-24 10.7-24 24zm208-8c0 8.8 7.2 16 16 16h48c8.8 0 16-7.2 16-16s-7.2-16-16-16H384c-8.8 0-16 7.2-16 16zm0 96c0 8.8 7.2 16 16 16h48c8.8 0 16-7.2 16-16s-7.2-16-16-16H384c-8.8 0-16 7.2-16 16zM160 304c0 8.8 7.2 16 16 16H432c8.8 0 16-7.2 16-16s-7.2-16-16-16H176c-8.8 0-16 7.2-16 16zm0 96c0 8.8 7.2 16 16 16H432c8.8 0 16-7.2 16-16s-7.2-16-16-16H176c-8.8 0-16 7.2-16 16z"/>
            </svg>
        </div>
        <div class="txt">
            <h2>Blog Comments Management</h2>
            <p>Moderate, approve, edit, and manage all blog comments from users and guests.</p>
        </div>
    </div>
</div>

<div style="height: 20px;"></div>

<?php
// Show edit form only in edit mode.  Do not change this block.
if (isset($_GET['edit']))
{
    $edit_id = (int) $_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM blog_comments WHERE id = ?');
    $stmt->execute([$edit_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row)
    {
        // Restore original author and avatar logic for edit form
        $author = $row['username'];
        $avatar = 'avatar.png';
        if (!empty($row['account_id']))
        {
            $stmtAcc = $pdo->prepare('SELECT username, avatar FROM accounts WHERE id = ? LIMIT 1');
            $stmtAcc->execute([$row['account_id']]);
            if ($acc = $stmtAcc->fetch(PDO::FETCH_ASSOC))
            {
                $author = $acc['username'];
                if (!empty($acc['avatar']))
                    $avatar = $acc['avatar'];
            }
        }
        if (empty($author) && !empty($row['user_id']))
        {
            $stmtUser = $pdo->prepare('SELECT username, avatar FROM accounts WHERE id = ? LIMIT 1');
            $stmtUser->execute([$row['user_id']]);
            if ($usr = $stmtUser->fetch(PDO::FETCH_ASSOC))
            {
                $author = $usr['username'];
                if (!empty($usr['avatar']))
                    $avatar = $usr['avatar'];
            }
        }
        if (empty($author))
            $author = 'Unknown';
        $avatar_account = [
            'avatar' => ($avatar != 'avatar.png') ? $avatar : '',
            'role' => 'member'
        ];
        $avatar_url = getUserAvatar($avatar_account);
        ?>
        <div class="card mb-3" style="max-width:600px;margin:32px auto 0 auto;">
            <h6 class="card-header">Edit Comment</h6>
            <div class="card-body">
                <form action="comments.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <input type="hidden" name="edit_comment_id" value="<?= $row['id'] ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Author</label>
                                <input class="form-control" name="edit_author" type="text"
                                    value="<?= htmlspecialchars($author) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Avatar</label><br>
                                <img src="<?= $avatar_url ?>" width="50" height="50" class="rounded-circle border"
                                    alt="Avatar for <?= htmlspecialchars($author) ?>">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Approval Status</label>
                        <select class="form-select" name="edit_approved" required>
                            <option value="Yes" <?= $row['approved'] == 'Yes' ? ' selected' : '' ?>>Approved</option>
                            <option value="No" <?= $row['approved'] == 'No' ? ' selected' : '' ?>>Pending Review</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Comment Content</label>
                        <textarea name="edit_comment" class="form-control" rows="6"
                            required><?= htmlspecialchars($row['comment']) ?></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="comments.php" class="btn btn-outline-secondary btn-sm"
                            aria-label="Cancel and return to comments list">
                            <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>
                            Cancel
                        </a>
                        <button type="submit" name="edit" class="btn btn-success btn-sm"
                            aria-label="Save comment changes">
                            <i class="fas fa-save me-1" aria-hidden="true"></i>
                            Save
                        </button>
                        <a href="comments.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                            onclick="return confirm('Are you sure you want to delete this comment?')"
                            aria-label="Delete this comment permanently">
                            <i class="fas fa-trash me-1" aria-hidden="true"></i>
                            Delete
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <?php
        // Close the edit mode block and exit to prevent main content from displaying
    } else
    {
        // Graceful error message for missing comment
        ?>
        <div class="msg error" style="max-width:600px;margin:32px auto 0 auto;">
            <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                <path
                    d="M256 48C141.1 48 48 141.1 48 256s93.1 208 208 208 208-93.1 208-208S370.9 48 256 48zm0 368c-88.2 0-160-71.8-160-160S167.8 96 256 96s160 71.8 160 160-71.8 160-160 160zm-16-112c0-8.8 7.2-16 16-16s16 7.2 16 16v48c0 8.8-7.2 16-16 16s-16-7.2-16-16v-48zm16-144c-13.3 0-24 10.7-24 24s10.7 24 24 24 24-10.7 24-24-10.7-24-24-24z" />
            </svg>
            <p>Sorry, the comment you are trying to edit could not be found. It may have been deleted or does not exist.</p>
            <a href="comments.php" class="btn btn-outline-secondary btn-sm" aria-label="Return to comments list">
                <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>
                Return to Comments List
            </a>
        </div>
        <?php
    }
    exit; // Prevent main content from displaying in edit mode
}
?>

<?php
if (isset($success_msg)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" aria-live="assertive">
        <i class="fas fa-check-circle me-2"></i>
        <?= $success_msg ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Blog Comments Management</h6>
        <small class="text-muted"><?=number_format($total_comments)?> total comments</small>
    </div>
    <div class="card-body">
        <form action="" method="get" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="search_query" class="form-label">Search</label>
                    <input id="search_query" type="text" name="search_query" class="form-control"
                        placeholder="Search comments..." 
                        value="<?=htmlspecialchars($search, ENT_QUOTES)?>">
                </div>
                <div class="col-md-2">
                    <label for="approved" class="form-label">Status</label>
                    <select name="approved" id="approved" class="form-select">
                        <option value="" <?= $approved == '' ? 'selected' : '' ?>>All</option>
                        <option value="Yes" <?= $approved == 'Yes' ? 'selected' : '' ?>>Approved</option>
                        <option value="No" <?= $approved == 'No' ? 'selected' : '' ?>>Pending</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="post_id" class="form-label">Post</label>
                    <select name="post_id" id="post_id" class="form-select">
                        <option value="">All Posts</option>
                        <?php
                        $stmt = $pdo->query('SELECT id, title FROM blog_posts ORDER BY title');
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
                        {
                            echo '<option value="' . $row['id'] . '"' . ($post_id == $row['id'] ? ' selected' : '') . '>' . htmlspecialchars($row['title'], ENT_QUOTES) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="username" class="form-label">Author</label>
                    <select name="username" id="username" class="form-select">
                        <option value="">All Authors</option>
                        <?php
                        $stmt = $pdo->query('SELECT id, username FROM accounts ORDER BY username');
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
                        {
                            echo '<option value="' . $row['id'] . '"' . ($username == $row['id'] ? ' selected' : '') . '>' . htmlspecialchars($row['username'], ENT_QUOTES) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-search me-1" aria-hidden="true"></i>
                        Apply Filters
                    </button>
                    <?php if ($search || $approved || $post_id || $username): ?>
                    <a href="comments.php" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-times me-1" aria-hidden="true"></i>
                        Clear
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <div class="table-responsive" role="table" aria-label="Blog Comments">
            <table class="table table-hover mb-0" role="grid">
                <thead role="rowgroup">
                    <tr role="row">
                        <th class="text-center" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'username'; $q['order'] = ($order_by == 'username' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="sort-header">Author<?= $order_by == 'username' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th class="text-center" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'title'; $q['order'] = ($order_by == 'title' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="sort-header">Post<?= $order_by == 'title' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th class="text-center" role="columnheader" scope="col">Comment</th>
                        <th class="text-center" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'date'; $q['order'] = ($order_by == 'date' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="sort-header">Date<?= $order_by == 'date' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th class="text-center" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'approved'; $q['order'] = ($order_by == 'approved' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="sort-header">Status<?= $order_by == 'approved' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th class="text-center" role="columnheader" scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody role="rowgroup">
                    <?php
                    // Use the more functional, filterable, paginated $comments array from the top of the file for table output.
                    foreach ($comments as $row)
                    {
                        $authorData = getAuthorAndAvatar($row);
                        echo '
                    <tr role="row">
                        <td class="text-center" role="gridcell">
                            <div class="d-flex align-items-center justify-content-center">
                                <img src="' . $authorData['avatar_url'] . '" width="32" height="32" class="rounded-circle me-2" alt="Avatar for ' . htmlspecialchars($authorData['author']) . '">
                                <div>
                                    <div class="fw-bold">' . htmlspecialchars($authorData['author']) . '</div>
                                    ' . $authorData['badge'] . '
                                </div>
                            </div>
                        </td>
                        <td class="text-center" role="gridcell">' . (function () use ($row, $pdo) {
                                $post_title = isset($row['title']) ? $row['title'] : '';
                                if (!$post_title && !empty($row['post_id'])) {
                                    $stmt2 = $pdo->prepare("SELECT title FROM blog_posts WHERE id = ?");
                                    $stmt2->execute([$row['post_id']]);
                                    $sql2 = $stmt2->fetch(PDO::FETCH_ASSOC);
                                    $post_title = $sql2 ? $sql2['title'] : '';
                                }
                                return htmlspecialchars($post_title);
                            })() . '</td>
                        <td class="text-center" role="gridcell">
                            <div style="max-width:300px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="' . htmlspecialchars($row['comment']) . '">' . (function () use ($row) {
                                $comment = strip_tags($row['comment']);
                                $maxLen = 80;
                                if (mb_strlen($comment) > $maxLen) {
                                    $comment = mb_substr($comment, 0, $maxLen - 3) . '...';
                                }
                                return htmlspecialchars($comment);
                            })() . '</div>
                        </td>
                        <td class="text-center" role="gridcell" data-sort="' . strtotime($row['date']) . '">' . date('m-d-Y', strtotime($row['date'])) . '<br><small class="text-muted">' . strtolower(date('h:i a', strtotime($row['time']))) . '</small></td>
                        <td class="text-center" role="gridcell">';
                        if ($row['approved'] == "Yes") {
                            echo '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Approved</span>';
                        } else {
                            echo '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pending</span>';
                        }
                        echo '</td>
                        <td class="actions text-center" role="gridcell">
                            <div class="table-dropdown">
                                <button class="actions-btn" aria-haspopup="true" aria-expanded="false" aria-label="Actions for comment by ' . htmlspecialchars($authorData['author']) . '">
                                    <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">
                                        <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/>
                                    </svg>
                                </button>
                                <div class="table-dropdown-items" role="menu" aria-label="Comment Actions">
                                    <div role="menuitem">
                                        <a href="?edit=' . $row['id'] . '" class="green" tabindex="-1" aria-label="Edit comment by ' . htmlspecialchars($authorData['author']) . '">
                                            <i class="fas fa-edit" aria-hidden="true"></i>
                                            <span>Edit</span>
                                        </a>
                                    </div>';

                        // Show approve/reject button based on current status
                        if ($row['approved'] == 'No') {
                            echo '<div role="menuitem">
                                        <a href="?approve=' . $row['id'] . '" class="green" tabindex="-1" onclick="return confirm(\'Are you sure you want to approve this comment?\')" aria-label="Approve comment by ' . htmlspecialchars($authorData['author']) . '">
                                            <i class="fas fa-check" aria-hidden="true"></i>
                                            <span>&nbsp;Approve</span>
                                        </a>
                                    </div>';
                        } else {
                            echo '<div role="menuitem">
                                        <a href="?reject=' . $row['id'] . '" class="black" tabindex="-1" onclick="return confirm(\'Are you sure you want to reject this comment?\')" aria-label="Reject comment by ' . htmlspecialchars($authorData['author']) . '">
                                            <i class="fas fa-times" aria-hidden="true"></i>
                                            <span>&nbsp;Reject</span>
                                        </a>
                                    </div>';
                        }

                        echo '<div role="menuitem">
                                        <a href="?delete=' . $row['id'] . '" class="red" tabindex="-1" onclick="return confirm(\'Are you sure you want to delete this comment?\')" aria-label="Delete comment by ' . htmlspecialchars($authorData['author']) . '">
                                            <i class="fas fa-trash" aria-hidden="true"></i>
                                            <span>&nbsp;Delete</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-light">
        <div class="small">
            Showing <?= count($comments) ?> of <?= $total_comments ?> comments
        </div>
    </div>
</div>

<!-- Pagination -->
<?php $totalPages = ceil($total_comments / $results_per_page); ?>
<?php if ($totalPages > 1): ?>
<nav aria-label="Comments Pagination" class="mt-3">
    <ul class="pagination justify-content-center pagination-sm">
        <!-- Previous page link -->
        <?php if ($page > 1): ?>
        <li class="page-item">
            <a class="page-link" href="<?= $url ?>&page=<?= $page - 1 ?>&order=<?= $order ?>&order_by=<?= $order_by ?>" aria-label="Previous page">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
        <?php else: ?>
        <li class="page-item disabled">
            <span class="page-link" aria-label="Previous page"><span aria-hidden="true">&laquo;</span></span>
        </li>
        <?php endif; ?>

        <?php
        // Calculate page range
        $start = max(1, $page - 2);
        $end = min($totalPages, $page + 2);

        if ($start > 1) {
            echo '<li class="page-item"><a class="page-link" href="' . $url . '&page=1&order=' . $order . '&order_by=' . $order_by . '">1</a></li>';
            if ($start > 2) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        for ($i = $start; $i <= $end; $i++) {
            if ($i == $page) {
                echo '<li class="page-item active" aria-current="page"><span class="page-link">' . $i . '</span></li>';
            } else {
                echo '<li class="page-item"><a class="page-link" href="' . $url . '&page=' . $i . '&order=' . $order . '&order_by=' . $order_by . '">' . $i . '</a></li>';
            }
        }

        if ($end < $totalPages) {
            if ($end < $totalPages - 1) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            echo '<li class="page-item"><a class="page-link" href="' . $url . '&page=' . $totalPages . '&order=' . $order . '&order_by=' . $order_by . '">' . $totalPages . '</a></li>';
        }
        ?>

        <!-- Next page link -->
        <?php if ($page < $totalPages): ?>
        <li class="page-item">
            <a class="page-link" href="<?= $url ?>&page=<?= $page + 1 ?>&order=<?= $order ?>&order_by=<?= $order_by ?>" aria-label="Next page">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
        <?php else: ?>
        <li class="page-item disabled">
            <span class="page-link" aria-label="Next page"><span aria-hidden="true">&raquo;</span></span>
        </li>
        <?php endif; ?>
    </ul>
</nav>
<?php endif; ?>
</div><!-- End of content-header -->
<?= template_admin_footer(); ?>