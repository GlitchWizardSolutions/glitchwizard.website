<?php
include_once '../assets/includes/main.php';
// Retrieve the GET request parameters (if specified)
$pagination_page = isset($_GET['pagination_page']) ? $_GET['pagination_page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array (removed page_id)
$order_by_whitelist = ['title','description','url','num_reviews'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'title';
// Number of results per pagination page
$results_per_page = 15;
// Declare query param variables
$param1 = ($pagination_page - 1) * $results_per_page;
$param2 = $results_per_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE (r.page_id LIKE :search OR rpd.title LIKE :search OR rpd.description LIKE :search OR rpd.url LIKE :search) ' : '';
// Retrieve the total number of pages (count distinct page_ids)
$stmt = $pdo->prepare('SELECT COUNT(DISTINCT r.page_id) AS total FROM reviews r LEFT JOIN review_page_details rpd ON rpd.page_id = r.page_id ' . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
$stmt->execute();
$pages_total = $stmt->fetchColumn();
// SQL query to get all pages
$stmt = $pdo->prepare('SELECT COUNT(r.id) AS num_reviews, r.page_id, rpd.title, rpd.description, rpd.url FROM reviews r LEFT JOIN review_page_details rpd ON rpd.page_id = r.page_id ' . $where . ' GROUP BY r.page_id ORDER BY ' . $order_by . ' ' . $order . ' LIMIT :start_results,:num_results');
// Bind params
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
$stmt->execute();
// Retrieve query results
$pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Page details updated successfully!';
    }
}
// Table icons for sorting (matching accounts.php)
$table_icons = [
    'asc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9650;</span>', // ▲
    'desc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9660;</span>' // ▼
];
// Determine the URL
$url = 'review_pages.php?search=' . $search;
?>
<?=template_admin_header('Review Pages', 'reviews', 'pages')?>

<div class="content-title" id="main-pages-list" role="banner" aria-label="Review Pages List Header">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                <path d="M64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V160H256c-17.7 0-32-14.3-32-32V0H64zM256 0V128H384L256 0z"/>
            </svg>
        </div>
        <div class="txt">
            <h2>Review Pages</h2>
            <p>View, manage, and search review pages. Total pages: <?=number_format($pages_total)?></p>
        </div>
    </div>
</div>
<br>

<?php if (isset($success_msg)): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    <?=$success_msg?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="d-flex gap-2 mb-4">
    <a href="review_page.php" class="btn btn-outline-secondary">
        <i class="fas fa-plus me-1"></i>Create Page
    </a>
</div>

<!-- Search Form -->
<form action="" method="get" class="mb-3">
    <div class="row g-3 align-items-end">
        <div class="col-md-8">
            <label for="search" class="form-label">Search Pages</label>
            <input 
                id="search" 
                type="text" 
                name="search" 
                placeholder="Search pages..." 
                value="<?=htmlspecialchars($search, ENT_QUOTES)?>" 
                class="form-control"
            >
        </div>
        <div class="col-md-4">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-filter me-1" aria-hidden="true"></i>Apply Filters
                </button>
                <?php if ($search): ?>
                <a href="review_pages.php" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1" aria-hidden="true"></i>Clear
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</form>

<div class="card">
    <h6 class="card-header">Review Pages</h6>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0" role="grid">
                <thead class="table-light" role="rowgroup">
                    <tr role="row">
                        <th class="text-left" style="text-align: left !important;" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'title'; $q['order'] = ($order_by == 'title' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none text-dark">Title<?= $order_by == 'title' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th class="text-left responsive-hidden" style="text-align: left !important;" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'description'; $q['order'] = ($order_by == 'description' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none text-dark">Description<?= $order_by == 'description' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th class="text-left responsive-hidden" style="text-align: left !important;" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'url'; $q['order'] = ($order_by == 'url' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none text-dark">URL<?= $order_by == 'url' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th class="text-center" style="text-align: center !important;" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'num_reviews'; $q['order'] = ($order_by == 'num_reviews' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none text-dark">Total Reviews<?= $order_by == 'num_reviews' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th class="text-center" style="text-align: center !important;" role="columnheader" scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody role="rowgroup">
                    <?php if (empty($pages)): ?>
                    <tr role="row">
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            There are no pages.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($pages as $page): ?>
                    <tr role="row">
                        <td class="text-left"><?=htmlspecialchars($page['title'] ? $page['title'] : '--', ENT_QUOTES)?></td>
                        <td class="text-left responsive-hidden"><?=htmlspecialchars($page['description'] ? $page['description'] : '--', ENT_QUOTES)?></td>
                        <td class="text-left responsive-hidden"><?php if ($page['url']): ?><a href="<?=htmlspecialchars($page['url'], ENT_QUOTES)?>" class="link1" target="_blank"><?=htmlspecialchars($page['url'], ENT_QUOTES)?></a><?php else: ?>--<?php endif; ?></td>
                        <td class="text-center"><a href="reviews.php?page_id=<?=$page['page_id']?>" class="link1"><?=number_format($page['num_reviews'])?></a></td>
                        <td class="text-center">
                            <div class="table-dropdown">
                                <button class="actions-btn" aria-haspopup="true" aria-expanded="false"
                                    aria-label="Actions for page: <?= htmlspecialchars($page['title'] ? $page['title'] : 'Page ' . $page['page_id'], ENT_QUOTES) ?>">
                                    <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">
                                        <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z" />
                                    </svg>
                                </button>
                                <div class="table-dropdown-items" role="menu" aria-label="Page Actions">
                                    <div role="menuitem">
                                        <a href="review_page.php?id=<?=$page['page_id']?>" class="green" tabindex="-1" 
                                           aria-label="Edit details for page: <?= htmlspecialchars($page['title'] ? $page['title'] : 'Page ' . $page['page_id'], ENT_QUOTES) ?>">
                                            <span class="icon" aria-hidden="true">
                                                <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                    <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z" />
                                                </svg>
                                            </span>
                                            <span>Edit Details</span>
                                        </a>
                                    </div>
                                    <div role="menuitem">
                                        <a href="reviews.php?page_id=<?=$page['page_id']?>" class="blue" tabindex="-1"
                                           aria-label="View reviews for page: <?= htmlspecialchars($page['title'] ? $page['title'] : 'Page ' . $page['page_id'], ENT_QUOTES) ?>">
                                            <span class="icon" aria-hidden="true">
                                                <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                                    <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-92.7-69.4z" />
                                                </svg>
                                            </span>
                                            <span>View Reviews</span>
                                        </a>
                                    </div>
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
    
    <?php if ($pages_total > 0): ?>
    <div class="card-footer bg-light">
        <div class="small">
            <span>Page <?=$pagination_page?> of <?=ceil($pages_total / $results_per_page) == 0 ? 1 : ceil($pages_total / $results_per_page)?></span>
            <?php if ($pagination_page > 1): ?>
            | <a href="<?=$url?>&pagination_page=<?=$pagination_page-1?>&order=<?=$order?>&order_by=<?=$order_by?>">Previous</a>
            <?php endif; ?>
            <?php if ($pagination_page * $results_per_page < $pages_total): ?>
            | <a href="<?=$url?>&pagination_page=<?=$pagination_page+1?>&order=<?=$order?>&order_by=<?=$order_by?>">Next</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?=template_admin_footer()?>