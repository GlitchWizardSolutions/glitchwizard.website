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
$public = isset($_GET['public']) ? $_GET['public'] : '';
$account_id = isset($_GET['account_id']) ? $_GET['account_id'] : '';
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['id','title','description_text','account_id','is_public','total_media','username'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'id';
// Number of results per pagination page
$results_per_page = 15;
// Declare query param variables
$param1 = ($page - 1) * $results_per_page;
$param2 = $results_per_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE (c.title LIKE :search OR c.account_id = :search) ' : '';
// Public filter
if ($public == 'Yes') {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'is_public = 1 ';
}
if ($public == 'No') {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'is_public = 0 ';
}
// Account filter
if ($account_id) {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'c.account_id = :account_id ';
}
// Retrieve the total number of collections
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM gallery_collections c ' . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($account_id) $stmt->bindParam('account_id', $account_id, PDO::PARAM_INT);
$stmt->execute();
$total_results = $stmt->fetchColumn();
// Prepare collections query
$stmt = $pdo->prepare('SELECT c.*, a.email, a.username, (SELECT COUNT(*) FROM gallery_media_collections mc JOIN gallery_media m ON m.id = mc.media_id WHERE mc.collection_id = c.id) AS total_media FROM gallery_collections c LEFT JOIN accounts a ON a.id = c.account_id ' . $where . ' ORDER BY ' . $order_by . ' ' . $order . ' LIMIT :start_results,:num_results');
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($account_id) $stmt->bindParam('account_id', $account_id, PDO::PARAM_INT);
$stmt->execute();
// Retrieve query results
$collections = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Delete the collection
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE c, mc FROM gallery_collections c LEFT JOIN gallery_media_collections mc ON mc.collection_id = c.id WHERE c.id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    header('Location: collections.php?success_msg=3');
    exit;
}
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Collection created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Collection updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Collection deleted successfully!';
    }
    if ($_GET['success_msg'] == 4) {
        $success_msg = 'Collections imported successfully! ' . $_GET['imported'] . ' collections were imported.';
    }
}
// Create URL
$url = 'collections.php?search_query=' . $search . '&public=' . $public . '&account_id=' . $account_id;
?>
<?=template_admin_header('Collections', 'gallery', 'collections_view')?>

<div class="content-title" id="main-gallery-collections" role="banner" aria-label="Gallery Collections Management Header">
    <div class="title mb-4">
    <div class="icon"><i class="bi bi-collection-fill" aria-hidden="true"></i></div>
        <div class="txt">
            <h2>Collections</h2>
            <p>View, edit, and create collections.</p>
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
    <a href="collection.php" class="btn btn-success">
    <i class="bi bi-plus me-1"></i>Create Collection
    </a>
</div>

<!-- Search and Filter Form -->
<form method="get" class="mb-3">
    <div class="row g-3 align-items-end">
        <div class="col-md-6">
            <label for="search_query" class="form-label">Search Collections</label>
            <input type="text" id="search_query" name="search_query" class="form-control" placeholder="Search by title or account ID..." value="<?=htmlspecialchars($search, ENT_QUOTES)?>">
        </div>
        <div class="col-md-3">
            <label for="public" class="form-label">Public Status</label>
            <select name="public" id="public" class="form-select">
                <option value=""<?=$public==''?' selected':''?>>All</option>
                <option value="Yes"<?=$public=='Yes'?' selected':''?>>Yes</option>
                <option value="No"<?=$public=='No'?' selected':''?>>No</option>
            </select>
        </div>
        <div class="col-md-3">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-funnel me-1" aria-hidden="true"></i>
                    Apply Filters
                </button>
                <?php if ($search || $public !== '' || $account_id): ?>
                <a href="collections.php" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg me-1" aria-hidden="true"></i>
                    Clear
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</form>

<div class="filter-list">
    <?php if ($account_id != ''): ?>
    <div class="filter">
        <a href="<?=remove_url_param($url, 'account_id')?>"><i class="bi bi-x-circle" aria-hidden="true"></i></a>
        Account ID : <?=htmlspecialchars($account_id, ENT_QUOTES)?>
    </div>
    <?php endif; ?>
    <?php if ($public != ''): ?>
    <div class="filter">
        <a href="<?=remove_url_param($url, 'public')?>"><i class="bi bi-x-circle" aria-hidden="true"></i></a>
        Public : <?=htmlspecialchars($public, ENT_QUOTES)?>
    </div>
    <?php endif; ?>
    <?php if ($search != ''): ?>
    <div class="filter">
        <a href="<?=remove_url_param($url, 'search_query')?>"><i class="bi bi-x-circle" aria-hidden="true"></i></a>
        Search : <?=htmlspecialchars($search, ENT_QUOTES)?>
    </div>
    <?php endif; ?>   
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Gallery Collections</h6>
        <span class="badge bg-secondary"><?= number_format($total_results) ?> Total</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="text-align:left;"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=id'?>" class="sort-header">#<?=$order_by=='id' ? $table_icons[strtolower($order)] : ''?></a></th>
                        <th style="text-align:left;"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=title'?>" class="sort-header">Title<?=$order_by=='title' ? $table_icons[strtolower($order)] : ''?></a></th>
                        <th class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=total_media'?>" class="sort-header"># Media<?=$order_by=='total_media' ? $table_icons[strtolower($order)] : ''?></a></th>
                        <th class="responsive-hidden" style="text-align:left;"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=username'?>" class="sort-header">Account<?=$order_by=='username' ? $table_icons[strtolower($order)] : ''?></a></th>
                        <th class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=is_public'?>" class="sort-header">Public<?=$order_by=='is_public' ? $table_icons[strtolower($order)] : ''?></a></th>
                        <th style="text-align: center;" role="columnheader" scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$collections): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-folder2-open mb-2" style="font-size: 2rem; opacity: 0.5;"></i>
                            <br>There are no collections.
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php foreach ($collections as $collection): ?>
                    <tr>
                        <td><span class="badge bg-light text-dark"><?=$collection['id']?></span></td>
                        <td>
                            <div class="fw-bold"><?=htmlspecialchars($collection['title'], ENT_QUOTES)?></div>
                            <?php if (!empty($collection['description_text'])): ?>
                            <small class="text-muted"><?=htmlspecialchars(substr($collection['description_text'], 0, 80), ENT_QUOTES)?><?=strlen($collection['description_text']) > 80 ? '...' : ''?></small>
                            <?php endif; ?>
                        </td>
                        <td class="responsive-hidden">
                            <a href="allmedia.php?collection_id=<?=$collection['id']?>" class="text-decoration-none">
                                <span class="badge bg-info"><?=$collection['total_media'] ? number_format($collection['total_media']) : 0?></span>
                            </a>
                        </td>
                        <td class="responsive-hidden">
                            <?php if ($collection['account_id']): ?>
                            <div><?=htmlspecialchars($collection['username'], ENT_QUOTES)?></div>
                            <small class="text-muted"><?=htmlspecialchars($collection['email'], ENT_QUOTES)?></small>
                            <?php else: ?>
                            <span class="text-muted">--</span>
                            <?php endif; ?>
                        </td>
                        <td class="responsive-hidden">
                            <?=$collection['is_public']?'<span class="badge bg-success">Yes</span>':'<span class="badge bg-danger">No</span>'?>
                        </td>
                        <td class="actions" style="text-align: center;">
                            <div class="table-dropdown">
                                <button class="actions-btn" aria-haspopup="true" aria-expanded="false"
                                    aria-label="Actions for <?= htmlspecialchars($collection['title'], ENT_QUOTES) ?>">
                                    <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">
                                        <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z" />
                                    </svg>
                                </button>
                                <div class="table-dropdown-items" role="menu" aria-label="Collection Actions">
                                    <div role="menuitem">
                                        <a href="collection.php?id=<?=$collection['id']?>" 
                                           class="green" 
                                           tabindex="-1"
                                           aria-label="Edit collection <?= htmlspecialchars($collection['title'], ENT_QUOTES) ?>">
                                            <span class="icon" aria-hidden="true">
                                                <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                    <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 171.4 242.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z" />
                                                </svg>
                                            </span>
                                            <span>Edit</span>
                                        </a>
                                    </div>
                                    <div role="menuitem">
                                        <a href="collections.php?delete=<?=$collection['id']?>" 
                                           class="red" 
                                           tabindex="-1"
                                           onclick="return confirm('Are you sure you want to delete this collection?')"
                                           aria-label="Delete collection <?= htmlspecialchars($collection['title'], ENT_QUOTES) ?>">
                                            <span class="icon" aria-hidden="true">
                                                <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                    <path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z" />
                                                </svg>
                                            </span>
                                            <span>Delete</span>
                                        </a>
                                    </div>
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
            <span>Total collections: <?= $total_results ?></span>
        </div>
    </div>
    <?php elseif ($total_results <= 100): ?>
    <div class="card-footer bg-light">
        <div class="small">
            <span>Total collections: <?= $total_results ?></span>
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