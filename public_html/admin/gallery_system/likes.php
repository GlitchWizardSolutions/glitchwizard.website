<?php
include '../assets/includes/main.php';

// Use simple triangle icons for sort direction, matching accounts.php
$table_icons = [
    'asc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9650;</span>', // ▲
    'desc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9660;</span>' // ▼
];

// Retrieve the GET request parameters (if specified)
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$search = isset($_GET['search_query']) ? $_GET['search_query'] : '';
// Filters parameters
$media_id = isset($_GET['media_id']) ? $_GET['media_id'] : '';
$account_id = isset($_GET['account_id']) ? $_GET['account_id'] : '';
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['id','title','username'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'id';
// Number of results per pagination page
$results_per_page = 15;
// Declare query param variables
$param1 = ($page - 1) * $results_per_page;
$param2 = $results_per_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE (m.title LIKE :search OR m.account_id = :search OR a.username LIKE :search) ' : '';
// Media filter
if ($media_id) {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'ml.media_id = :media_id ';
}
// Account filter
if ($account_id) {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'ml.account_id = :account_id ';
}

// Retrieve accounts for the filter dropdown
$stmt = $pdo->prepare('SELECT id, username FROM accounts ORDER BY username');
$stmt->execute();
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Retrieve the total number of likes
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM gallery_media_likes ml JOIN gallery_media m ON m.id = ml.media_id LEFT JOIN accounts a ON a.id = ml.account_id ' . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($account_id) $stmt->bindParam('account_id', $account_id, PDO::PARAM_INT);
if ($media_id) $stmt->bindParam('media_id', $media_id, PDO::PARAM_INT);
$stmt->execute();
$total_results = $stmt->fetchColumn();
// Prepare likes query
$stmt = $pdo->prepare('SELECT ml.*, m.filepath, m.title, m.media_type, m.description_text, m.is_public, a.email, a.username FROM gallery_media_likes ml JOIN gallery_media m ON m.id = ml.media_id LEFT JOIN accounts a ON a.id = ml.account_id ' . $where . ' ORDER BY ' . $order_by . ' ' . $order . ' LIMIT :start_results,:num_results');
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($account_id) $stmt->bindParam('account_id', $account_id, PDO::PARAM_INT);
if ($media_id) $stmt->bindParam('media_id', $media_id, PDO::PARAM_INT);
$stmt->execute();
// Retrieve query results
$media_likes = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Delete the like
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM media_likes WHERE id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    header('Location: likes.php?success_msg=3');
    exit;
}
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Like created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Like updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Like deleted successfully!';
    }
}
// Create URL
$url = 'likes.php?search_query=' . $search . '&media_id=' . $media_id . '&account_id=' . $account_id;
?>
<?=template_admin_header('Media Likes', 'gallery', 'likes')?>

<div class="content-title mb-4" id="main-gallery-likes" role="banner" aria-label="Gallery Media Likes Header">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12,21.35L10.55,20.03C5.4,15.36 2,12.27 2,8.5C2,5.41 4.42,3 7.5,3C9.24,3 10.91,3.81 12,5.08C13.09,3.81 14.76,3 16.5,3C19.58,3 22,5.41 22,8.5C22,12.27 18.6,15.36 13.45,20.03L12,21.35Z" /></svg>
        </div>
        <div class="txt">
            <h2>Media Likes</h2>
            <p>View and manage media likes from users.</p>
        </div>
    </div>
</div>

<?php if (isset($success_msg)): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>
    <?=$success_msg?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="d-flex gap-2 mb-4">
    <a href="like.php" class="btn btn-success">
        <i class="bi bi-plus me-1"></i>Add Like
    </a>
</div>

<!-- Search and Filter Form -->
<form method="get" class="mb-3">
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label for="search_query" class="form-label">Search Media Likes</label>
            <input type="text" id="search_query" name="search_query" class="form-control" placeholder="Search by media title, username..." value="<?=htmlspecialchars($search, ENT_QUOTES)?>">
        </div>
        <div class="col-md-2">
            <label for="account_id" class="form-label">Account</label>
            <select name="account_id" id="account_id" class="form-select">
                <option value=""<?=$account_id==''?' selected':''?>>All Accounts</option>
                <?php foreach ($accounts as $account): ?>
                <option value="<?=$account['id']?>"<?=$account_id==$account['id']?' selected':''?>><?=htmlspecialchars($account['username'], ENT_QUOTES)?> (<?=$account['id']?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label for="media_id" class="form-label">Media ID</label>
            <input type="text" name="media_id" id="media_id" class="form-control" placeholder="Media ID..." value="<?=htmlspecialchars($media_id, ENT_QUOTES)?>">
        </div>
        <div class="col-md-4">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-funnel me-1" aria-hidden="true"></i>
                    Apply Filters
                </button>
                <?php if ($search || $account_id || $media_id): ?>
                <a href="likes.php" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg me-1" aria-hidden="true"></i>
                    Clear
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Media Likes</h6>
        <span class="badge bg-secondary"><?= number_format($total_results) ?> Total</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=id'?>" class="sort-header">#<?=$order_by=='id' ? $table_icons[strtolower($order)] : ''?></a></th>
                        <th colspan="2"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=title'?>" class="sort-header">Media<?=$order_by=='title' ? $table_icons[strtolower($order)] : ''?></a></th>
                        <th><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=username'?>" class="sort-header">Account<?=$order_by=='username' ? $table_icons[strtolower($order)] : ''?></a></th>
                        <th style="text-align: center;" role="columnheader" scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$media_likes): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <svg width="24" height="24" class="mb-2 opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12,21.35L10.55,20.03C5.4,15.36 2,12.27 2,8.5C2,5.41 4.42,3 7.5,3C9.24,3 10.91,3.81 12,5.08C13.09,3.81 14.76,3 16.5,3C19.58,3 22,5.41 22,8.5C22,12.27 18.6,15.36 13.45,20.03L12,21.35Z" /></svg>
                            <div>No media likes found</div>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php foreach ($media_likes as $m): ?>
                    <tr>
                        <td class="text-muted fw-bold"><?=$m['id']?></td>
                        <td style="width: 60px;">
                            <a href="#" class="media-img open-media-modal d-block border rounded overflow-hidden" data-type="<?=$m['media_type']?>" data-filepath="<?=htmlspecialchars('../' . $m['filepath'], ENT_QUOTES)?>" title="View Media">
                                <?php if ($m['media_type'] == 'image' && file_exists('../' . $m['filepath'])): ?>
                                <img src="../<?=$m['filepath']?>" alt="<?=htmlspecialchars($m['title'], ENT_QUOTES)?>" class="img-fluid" style="width: 40px; height: 40px; object-fit: cover;">
                                <?php elseif (!empty($m['thumbnail']) && file_exists('../' . $m['thumbnail'])): ?>
                                <img src="../<?=$m['thumbnail']?>" alt="<?=htmlspecialchars($m['title'], ENT_QUOTES)?>" class="img-fluid" style="width: 40px; height: 40px; object-fit: cover;">
                                <?php elseif ($m['media_type'] == 'video'): ?>
                                <div class="d-flex align-items-center justify-content-center bg-light text-muted" style="width: 40px; height: 40px;">
                                    <i class="bi bi-play-btn" aria-hidden="true"></i>
                                </div>
                                <?php elseif ($m['media_type'] == 'audio'): ?>
                                <div class="d-flex align-items-center justify-content-center bg-light text-muted" style="width: 40px; height: 40px;">
                                    <i class="bi bi-volume-up" aria-hidden="true"></i>
                                </div>
                                <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center bg-danger text-white" style="width: 40px; height: 40px;" title="File not found">
                                    <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" /></svg>
                                </div>
                                <?php endif; ?>
                            </a>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <a href="#" class="open-media-modal text-decoration-none fw-medium<?=!file_exists('../' . $m['filepath']) ? ' text-danger' : ''?>" data-type="<?=$m['media_type']?>" data-filepath="<?=htmlspecialchars('../' . $m['filepath'], ENT_QUOTES)?>" title="View Media">
                                    <?=htmlspecialchars($m['title'], ENT_QUOTES)?>
                                    <?php if (!$m['is_public']): ?>
                                    <i class="bi bi-lock-fill small text-muted ms-1" title="Private Media" aria-hidden="true"></i>
                                    <?php endif; ?>
                                </a>                        
                                <?php if (file_exists('../' . $m['filepath'])): ?>
                                <small class="text-muted"><?=mime_content_type('../' . $m['filepath'])?>, <?=convert_filesize(filesize('../' . $m['filepath']))?></small>
                                <?php else: ?>
                                <small class="text-danger">(File not found)</small>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <?php if ($m['account_id']): ?>
                            <div class="d-flex flex-column">
                                <span class="fw-medium"><?=htmlspecialchars($m['username'], ENT_QUOTES)?></span>
                                <a class="text-decoration-none small" href="account.php?id=<?=$m['account_id']?>"><?=htmlspecialchars($m['email'], ENT_QUOTES)?> [<?=htmlspecialchars($m['account_id'], ENT_QUOTES)?>]</a>
                            </div>
                            <?php else: ?>
                            <span class="text-muted">--</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions" style="text-align: center;">
                            <div class="table-dropdown">
                                <button class="actions-btn" aria-haspopup="true" aria-expanded="false"
                                    aria-label="Actions for <?= htmlspecialchars($m['title'], ENT_QUOTES) ?>">
                                    <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">
                                        <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z" />
                                    </svg>
                                </button>
                                <div class="table-dropdown-items" role="menu" aria-label="Like Actions">
                                    <div role="menuitem">
                                        <a href="like.php?id=<?=$m['id']?>" 
                                           class="green" 
                                           tabindex="-1"
                                           aria-label="Edit like for <?= htmlspecialchars($m['title'], ENT_QUOTES) ?>">
                                            <span class="icon" aria-hidden="true">
                                                <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                    <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 171.4 242.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z" />
                                                </svg>
                                            </span>
                                            <span>Edit</span>
                                        </a>
                                    </div>
                                    <div role="menuitem">
                                        <a href="likes.php?delete=<?=$m['id']?>" 
                                           class="red" 
                                           tabindex="-1"
                                           onclick="return confirm('Are you sure you want to delete this like?')"
                                           aria-label="Delete like for <?= htmlspecialchars($m['title'], ENT_QUOTES) ?>">
                                            <span class="icon" aria-hidden="true">
                                                <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                    <path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z" />
                                                </svg>
                                            </span>
                                            <span>Delete</span>
                                        </a>
                                    </div>
                                </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php
    // Calculate pagination variables
    $total_pages = $total_results > 0 ? ceil($total_results / $results_per_page) : 1;
    $offset = ($page - 1) * $results_per_page;
    ?>
    
    <?php if ($total_results <= 20): ?>
    <div class="card-footer bg-light">
        <div class="small">
            <span>Total likes: <?= $total_results ?></span>
        </div>
    </div>
    <?php elseif ($total_results <= 100): ?>
    <div class="card-footer bg-light">
        <div class="small">
            <span>Total likes: <?= $total_results ?></span>
            <?php if ($total_pages > 1): ?>
                | <span>Page <?= $page ?> of <?= $total_pages ?></span>
                <?php if ($page > 1): ?>
                    | <a href="?page=<?= $page - 1 ?><?= !empty($_SERVER['QUERY_STRING']) ? '&' . str_replace('page=' . $page, '', $_SERVER['QUERY_STRING']) : '' ?>">Previous</a>
                <?php endif; ?>
                <?php if ($page < $total_pages): ?>
                    | <a href="?page=<?= $page + 1 ?><?= !empty($_SERVER['QUERY_STRING']) ? '&' . str_replace('page=' . $page, '', $_SERVER['QUERY_STRING']) : '' ?>">Next</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="card-footer bg-light">
        <div class="small">
            <span>Page <?= $page ?> of <?= $total_pages ?></span>
            <?php if ($page > 1): ?>
                | <a href="?page=<?= $page - 1 ?><?= !empty($_SERVER['QUERY_STRING']) ? '&' . str_replace('page=' . $page, '', $_SERVER['QUERY_STRING']) : '' ?>">Previous</a>
            <?php endif; ?>
            <?php if ($page < $total_pages): ?>
                | <a href="?page=<?= $page + 1 ?><?= !empty($_SERVER['QUERY_STRING']) ? '&' . str_replace('page=' . $page, '', $_SERVER['QUERY_STRING']) : '' ?>">Next</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?=template_admin_footer()?>