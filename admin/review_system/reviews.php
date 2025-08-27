<?php
// Phase 2 Template Integration - Using unified admin template system
include_once '../assets/includes/main.php';

// Review system configuration constants
if (!defined('max_stars')) {
    define('max_stars', 5);
}

// CANONICAL TABLE SORTING: Use triangle icons to match accounts.php standard
$table_icons = [
    'asc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9650;</span>', // ▲
    'desc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9660;</span>' // ▼
];

// Delete review
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE r, ri FROM reviews r LEFT JOIN review_images ri ON ri.review_id = r.id WHERE r.id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    header('Location: reviews.php?success_msg=3');
    exit;
}
// Approve review
if (isset($_GET['approve'])) {
    $stmt = $pdo->prepare('UPDATE reviews SET approved = 1 WHERE id = ?');
    $stmt->execute([ $_GET['approve'] ]);
    header('Location: reviews.php?success_msg=2');
    exit;
}

// PHASE 2 STANDARDIZATION: Implement unified admin search/filter interface
// Retrieve the GET request parameters (if specified)
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';
$search = $search_query; // For compatibility with existing logic

// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['id','page_id','display_name','content','submit_date','likes','approved','account_id','rating'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'id';

// PHASE 2 STANDARDIZATION: Adaptive pagination logic
$results_per_page = 15;
// Declare query param variables
$param1 = ($page - 1) * $results_per_page;
$param2 = $results_per_page;
$param3 = '%' . $search . '%';

// SQL where clause
$where = '';
$where .= $search ? 'WHERE (r.display_name LIKE :search OR r.content LIKE :search) ' : '';
if (isset($_GET['page_id'])) {
    $where .= $where ? ' AND r.page_id = :page_id ' : ' WHERE r.page_id = :page_id ';
} 
if (isset($_GET['account_id'])) {
    $where .= $where ? ' AND r.account_id = :account_id ' : ' WHERE r.account_id = :account_id ';
} 
if (isset($_GET['status'])) {
    $where .= $where ? ' AND r.approved = :status ' : ' WHERE r.approved = :status ';
} 
// Retrieve the total number of reviews
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM reviews r ' . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if (isset($_GET['page_id'])) $stmt->bindParam('page_id', $_GET['page_id'], PDO::PARAM_INT);
if (isset($_GET['account_id'])) $stmt->bindParam('account_id', $_GET['account_id'], PDO::PARAM_INT);
if (isset($_GET['status'])) $stmt->bindParam('status', $_GET['status'], PDO::PARAM_INT);
$stmt->execute();
$local_reviews_total = $stmt->fetchColumn();

// SQL query to get all reviews from the "reviews" table
$stmt = $pdo->prepare('SELECT r.*, (SELECT GROUP_CONCAT(i.file_path) FROM review_images i WHERE i.review_id = r.id) AS imgs, rpd.url, a.id AS account_id, a.email FROM reviews r LEFT JOIN accounts a ON r.account_id = a.id LEFT JOIN review_page_details rpd ON rpd.page_id = r.page_id ' . $where . ' ORDER BY ' . $order_by . ' ' . $order . ' LIMIT :start_results,:num_results');
// Bind params
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if (isset($_GET['page_id'])) $stmt->bindParam('page_id', $_GET['page_id'], PDO::PARAM_INT);
if (isset($_GET['account_id'])) $stmt->bindParam('account_id', $_GET['account_id'], PDO::PARAM_INT);
if (isset($_GET['status'])) $stmt->bindParam('status', $_GET['status'], PDO::PARAM_INT);
$stmt->execute();
// Retrieve query results
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Review created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Review updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Review deleted successfully!';
    }
    if ($_GET['success_msg'] == 4) {
        $success_msg = $_GET['imported'] . ' review(s) imported successfully!';
    }
}

// PHASE 2 STANDARDIZATION: URL building for pagination with unified parameters
$url = 'reviews.php?search_query=' . urlencode($search_query) . (isset($_GET['page_id']) ? '&page_id=' . $_GET['page_id'] : '');

// Use unified admin header with reviews navigation
echo template_admin_header('Reviews', 'reviews', 'view');
?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-4 px-4 branding-settings-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-white">
                    <span class="header-icon"><i class="bi bi-star-fill" aria-hidden="true"></i></span>
                    Reviews Management
                </h6>
                <span class="text-white" style="font-size: 0.875rem;"><?=number_format($local_reviews_total)?> total reviews</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="container-fluid py-3 px-4">

<?php if (isset($success_msg)): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    <?=$success_msg?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="d-flex gap-2 mb-4">
    <a href="review.php" class="btn btn-success">
        <i class="fas fa-plus me-1"></i>Create Review
    </a>
</div>

<!-- Search and Filter Form -->
<form method="get" class="mb-3">
    <div class="row g-3 align-items-end">
        <div class="col-md-6">
            <label for="search_query" class="form-label">Search Reviews</label>
            <input type="text" id="search_query" name="search_query" class="form-control" placeholder="Search by name or content..." value="<?=htmlspecialchars($search_query, ENT_QUOTES)?>">
        </div>
        <div class="col-md-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select">
                <option value=""<?=!isset($_GET['status']) || $_GET['status'] == '' ? ' selected' : ''?>>All</option>
                <option value="1"<?=isset($_GET['status']) && $_GET['status'] == '1' ? ' selected' : ''?>>Approved</option>
                <option value="0"<?=isset($_GET['status']) && $_GET['status'] == '0' ? ' selected' : ''?>>Disapproved</option>
                <option value="-1"<?=isset($_GET['status']) && $_GET['status'] == '-1' ? ' selected' : ''?>>Pending</option>
            </select>
        </div>
        <div class="col-md-3">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-filter me-1" aria-hidden="true"></i>
                    Apply Filters
                </button>
                <?php if ($search_query || isset($_GET['status']) || isset($_GET['page_id']) || isset($_GET['account_id'])): ?>
                <a href="reviews.php" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1" aria-hidden="true"></i>
                    Clear
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</form>

<!-- Reviews Management Card -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Reviews</h6>
        <span class="badge bg-secondary"><?= number_format($local_reviews_total) ?> Total</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" role="table" aria-label="Reviews list">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="text-center">Avatar</th>
                                <th scope="col" class="text-left">
                                    <?php $q = $_GET; $q['order_by'] = 'display_name'; $q['order'] = ($order_by == 'display_name' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                                    <a href="?<?= http_build_query($q) ?>" class="text-decoration-none text-dark">Name<?= $order_by == 'display_name' ? $table_icons[strtolower($order)] : '' ?></a>
                                </th>
                                <th scope="col" class="responsive-hidden text-left">Content</th>
                                <th scope="col" class="text-center">
                                    <?php $q = $_GET; $q['order_by'] = 'rating'; $q['order'] = ($order_by == 'rating' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                                    <a href="?<?= http_build_query($q) ?>" class="text-decoration-none text-dark">Rating<?= $order_by == 'rating' ? $table_icons[strtolower($order)] : '' ?></a>
                                </th>
                                <th scope="col" class="responsive-hidden text-center">
                                    <?php $q = $_GET; $q['order_by'] = 'likes'; $q['order'] = ($order_by == 'likes' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                                    <a href="?<?= http_build_query($q) ?>" class="text-decoration-none text-dark">Likes<?= $order_by == 'likes' ? $table_icons[strtolower($order)] : '' ?></a>
                                </th>
                                <th scope="col" class="responsive-hidden text-center">
                                    <?php $q = $_GET; $q['order_by'] = 'page_id'; $q['order'] = ($order_by == 'page_id' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                                    <a href="?<?= http_build_query($q) ?>" class="text-decoration-none text-dark">Page<?= $order_by == 'page_id' ? $table_icons[strtolower($order)] : '' ?></a>
                                </th>
                                <th scope="col" class="text-center">
                                    <?php $q = $_GET; $q['order_by'] = 'approved'; $q['order'] = ($order_by == 'approved' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                                    <a href="?<?= http_build_query($q) ?>" class="text-decoration-none text-dark">Status<?= $order_by == 'approved' ? $table_icons[strtolower($order)] : '' ?></a>
                                </th>
                                <th scope="col" class="responsive-hidden text-center">
                                    <?php $q = $_GET; $q['order_by'] = 'submit_date'; $q['order'] = ($order_by == 'submit_date' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                                    <a href="?<?= http_build_query($q) ?>" class="text-decoration-none text-dark">Date<?= $order_by == 'submit_date' ? $table_icons[strtolower($order)] : '' ?></a>
                                </th>
                                <th scope="col" style="text-align: center;">Actions</th>
                            </tr>
                        </thead>
                            <tbody>
                                <?php if (empty($reviews)): ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        There are no reviews.
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($reviews as $review): ?>
                <tr>
                    <td class="text-center" style="text-align: center;">
                        <?php 
                        // Create account array for avatar generation
                        $avatar_account = [
                            'avatar' => '', // Default empty - will use role-based default
                            'role' => 'Member' // Default role for review authors
                        ];
                        
                        // If we have account_id, try to get the actual avatar from accounts table
                        if (!empty($review['account_id'])) {
                            $stmt_avatar = $pdo->prepare('SELECT avatar, role FROM accounts WHERE id = ? LIMIT 1');
                            $stmt_avatar->execute([$review['account_id']]);
                            $account_data = $stmt_avatar->fetch(PDO::FETCH_ASSOC);
                            if ($account_data) {
                                $avatar_account['avatar'] = $account_data['avatar'] ?? '';
                                $avatar_account['role'] = $account_data['role'] ?? 'Member';
                            }
                        }
                        
                        $avatar_url = getUserAvatar($avatar_account);
                        ?>
                        <img src="<?= $avatar_url ?>" 
                             alt="<?= htmlspecialchars($review['display_name'], ENT_QUOTES) ?> avatar" 
                             style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 2px solid #dee2e6;" />
                    </td>
                    <td class="text-left" style="text-align: left;">
                        <?=htmlspecialchars($review['display_name'], ENT_QUOTES)?>
                       
                    </td>
                    <td class="responsive-hidden" style="text-align: left;">
                        <div>
                            <?php 
                            $content = nl2br(htmlspecialchars($review['content'], ENT_QUOTES));
                            $content_length = strlen($review['content']);
                            $show_expand = $content_length > 50;
                            
                            if ($show_expand): ?>
                                <div class="content-preview"><?= substr($content, 0, 50) ?>...</div>
                                <div class="content-full" style="display: none;"><?= $content ?></div>
                                <button type="button" class="btn btn-outline-secondary expand-btn mt-1" onclick="toggleContent(this)" style="--bs-btn-padding-y: .125rem; --bs-btn-padding-x: .375rem; --bs-btn-font-size: .625rem;">
                                    <span class="expand-text">Show More</span>
                                </button>
                            <?php else: ?>
                                <?= $content ?>
                            <?php endif; ?>
                            
                            <?php if ($review['imgs']): ?>
                            <div class="imgs mt-2">
                                <?php foreach (explode(',', $review['imgs']) as $img): ?>
                                <a href="../<?=htmlspecialchars($img, ENT_QUOTES)?>" target="_blank"><img src="../<?=htmlspecialchars($img, ENT_QUOTES)?>" alt="Review Image" width="32" height="32" class="me-1"></a>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="rating" title="<?=$review['rating']?> star">                    
                            <?=str_repeat('<i class="fas fa-star star"></i>', $review['rating'])?>
                            <?php if (max_stars-$review['rating'] > 0): ?>
                            <?=str_repeat('<i class="fas fa-star star-alt"></i>', max_stars-$review['rating'])?>
                            <?php endif; ?>
                        </span>
                    </td>
                    <td class="responsive-hidden text-center"><span class="badge bg-light text-dark"><?=number_format($review['likes'])?></span></td>
                    <td class="responsive-hidden text-center"><?=$review['url'] ? '<a href="' . htmlspecialchars($review['url'], ENT_QUOTES) . '" target="_blank" class="text-decoration-none">' . $review['page_id'] . '</a>' : $review['page_id']?></td>
                    <td class="text-center">
                        <?php if ($review['approved'] == 1): ?>
                        <span class="badge bg-success">Approved</span>
                        <?php elseif ($review['approved'] == 0): ?>
                        <span class="badge bg-danger">Disapproved</span>
                        <?php else: ?>
                        <span class="badge bg-secondary">Pending</span>
                        <?php endif; ?>
                    </td>
                    <td class="responsive-hidden text-center"><small class="text-muted"><?=date('M j, Y', strtotime($review['submit_date']))?></small></td>
                    <td class="actions" style="text-align: center;">
                        <div class="table-dropdown">
                            <button class="actions-btn" aria-haspopup="true" aria-expanded="false"
                                aria-label="Actions for <?= htmlspecialchars($review['display_name'], ENT_QUOTES) ?>">
                                <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">
                                    <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z" />
                                </svg>
                            </button>
                            <div class="table-dropdown-items" role="menu" aria-label="Review Actions">
                                <div role="menuitem">
                                    <a href="review.php?id=<?=$review['id']?>" class="green" tabindex="-1" 
                                       aria-label="Edit review for <?= htmlspecialchars($review['display_name'], ENT_QUOTES) ?>">
                                        <span class="icon" aria-hidden="true">
                                            <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z" />
                                            </svg>
                                        </span>
                                        <span>Edit</span>
                                    </a>
                                </div>
                                <div role="menuitem">
                                    <a href="reviews.php?delete=<?=$review['id']?>" class="red" tabindex="-1"
                                       onclick="return confirm('Are you sure you want to delete this review?')"
                                       aria-label="Delete review for <?= htmlspecialchars($review['display_name'], ENT_QUOTES) ?>">
                                        <span class="icon" aria-hidden="true">
                                            <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                <path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z" />
                                            </svg>
                                        </span>
                                        <span>Delete</span>
                                    </a>
                                </div>
                                <?php if ($review['approved'] != 1): ?>
                                <div role="menuitem">
                                    <a href="reviews.php?approve=<?=$review['id']?>" class="green" tabindex="-1"
                                       onclick="return confirm('Are you sure you want to approve this review?')"
                                       aria-label="Approve review for <?= htmlspecialchars($review['display_name'], ENT_QUOTES) ?>">
                                        <span class="icon" aria-hidden="true">
                                            <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                <path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z" />
                                            </svg>
                                        </span>
                                        <span>Approve</span>
                                    </a>
                                </div>
                                <?php endif; ?>
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
    
    <?php
    // Calculate pagination variables
    $total_pages = $local_reviews_total > 0 ? ceil($local_reviews_total / $results_per_page) : 1;
    $offset = ($page - 1) * $results_per_page;
    ?>
    
    <?php if ($local_reviews_total <= 20): ?>
    <div class="card-footer bg-light">
        <div class="small">
            <span>Total reviews: <?= $local_reviews_total ?></span>
        </div>
    </div>
    <?php elseif ($local_reviews_total <= 100): ?>
    <div class="card-footer bg-light">
        <div class="small">
            <span>Total reviews: <?= $local_reviews_total ?></span>
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
        </div>
    </div>
</div>

<?php echo template_admin_footer(); ?>

<script>
function toggleContent(button) {
    const container = button.closest('div');
    const preview = container.querySelector('.content-preview');
    const full = container.querySelector('.content-full');
    const expandText = button.querySelector('.expand-text');
    
    if (full.style.display === 'none') {
        preview.style.display = 'none';
        full.style.display = 'block';
        expandText.textContent = 'Show Less';
    } else {
        preview.style.display = 'block';
        full.style.display = 'none';
        expandText.textContent = 'Show More';
    }
}
</script>