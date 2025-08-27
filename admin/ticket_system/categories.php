<?php
include 'main.php';
// Retrieve the GET request parameters (if specified)
$pagination_page = isset($_GET['pagination_page']) ? $_GET['pagination_page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['id','title'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'id';
// Number of results per pagination page
$results_per_page = 15;
// Declare query param variables
$param1 = ($pagination_page - 1) * $results_per_page;
$param2 = $results_per_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE (title LIKE :search) ' : '';
// Retrieve the total number of categories
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM tickets_categories ' . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
$stmt->execute();
$categories_total = $stmt->fetchColumn();
// SQL query to get all categories
$stmt = $pdo->prepare('SELECT * FROM tickets_categories ' . $where . ' ORDER BY ' . $order_by . ' ' . $order . ' LIMIT :start_results,:num_results');
// Bind params
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
$stmt->execute();
// Retrieve query results
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Category created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Category updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Category deleted successfully!';
    }
}
// Determine the URL
$url = 'categories.php?search=' . $search;
?>
<?=template_admin_header('Categories', 'tickets', 'category')?>

<div class="content-title mb-4" id="main-categories" role="banner" aria-label="Categories Header">
    <div class="title">
        <div class="icon">
            <i class="bi bi-tags" aria-hidden="true"></i>
        </div>
        <div class="txt">
            <h2>Categories</h2>
            <p>View, manage, and search ticket categories.</p>
        </div>
    </div>
</div>

<?php if (isset($success_msg)): ?>
<div class="mb-4" role="region" aria-label="Success Message">
    <div class="msg success" role="alert" aria-live="polite">
    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
        <p><?=$success_msg?></p>
        <button type="button" class="close-success" aria-label="Dismiss success message" onclick="this.parentElement.parentElement.style.display='none'">
            <i class="bi bi-x-lg" aria-hidden="true"></i>
        </button>
    </div>
</div>
<?php endif; ?>

<!-- Top page actions -->
<div class="d-flex gap-2 mb-4" role="region" aria-label="Page Actions">
    <a href="category.php" class="btn btn-outline-secondary">
        <i class="bi bi-plus me-1" aria-hidden="true"></i>
        Add Category
    </a>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Category Management</h6>
        <small class="text-muted"><?=number_format($categories_total)?> total categories</small>
    </div>
    <div class="card-body">
        <form action="" method="get" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label">Search</label>
                    <input id="search" type="text" name="search" class="form-control"
                        placeholder="Search categories..." 
                        value="<?=htmlspecialchars($search, ENT_QUOTES)?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-search me-1" aria-hidden="true"></i>
                        Apply Filters
                    </button>
                    <?php if ($search): ?>
                    <a href="categories.php" class="btn btn-outline-secondary ms-2">
                        <i class="bi bi-x-lg me-1" aria-hidden="true"></i>
                        Clear
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <div class="table" role="table" aria-label="Category List">
            <table role="grid">
                <thead role="rowgroup">
                    <tr role="row">
                        <th role="columnheader" scope="col" style="text-align:left; padding-left:0;">
                            <?php $q = $_GET; $q['order_by'] = 'title'; $q['order'] = ($order_by == 'title' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="<?= http_build_query($q) ? '?' . http_build_query($q) : '' ?>" style="text-decoration:none; padding:0; margin:0; display:block; text-align:left;">
                                Title
                                <?php if ($order_by == 'title'): ?>
                                    <span aria-label="Sorted <?= $order == 'ASC' ? 'ascending' : 'descending' ?>">
                                        <?= $order == 'ASC' ? '▲' : '▼' ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th role="columnheader" scope="col" style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody role="rowgroup">
                    <?php if (empty($categories)): ?>
                    <tr role="row">
                        <td colspan="2" style="text-align:center;">There are no categories</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($categories as $category): ?>
                    <tr role="row">
                        <td style="text-align:left;"><?=htmlspecialchars($category['title'], ENT_QUOTES)?></td>
                        <td class="actions" style="text-align: center;">
                            <div class="table-dropdown">
                                <button class="actions-btn" aria-haspopup="true" aria-expanded="false"
                                    aria-label="Actions for category <?= htmlspecialchars($category['title'], ENT_QUOTES) ?>">
                                    <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">
                                        <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z" />
                                    </svg>
                                </button>
                                <div class="table-dropdown-items" role="menu" aria-label="Category Actions">
                                    <div role="menuitem">
                                        <a href="category.php?id=<?=$category['id']?>" 
                                           class="green" 
                                           tabindex="-1"
                                           aria-label="Edit category <?= htmlspecialchars($category['title'], ENT_QUOTES) ?>">
                                            <span class="icon" aria-hidden="true">
                                                <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                    <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z" />
                                                </svg>
                                            </span>
                                            <span>Edit</span>
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
</div>

<div class="pagination">
    <?php if ($pagination_page > 1): ?>
    <a href="<?=$url?>&pagination_page=<?=$pagination_page-1?>&order=<?=$order?>&order_by=<?=$order_by?>">Prev</a>
    <?php endif; ?>
    <span>Page <?=$pagination_page?> of <?=ceil($categories_total / $results_per_page) == 0 ? 1 : ceil($categories_total / $results_per_page)?></span>
    <?php if ($pagination_page * $results_per_page < $categories_total): ?>
    <a href="<?=$url?>&pagination_page=<?=$pagination_page+1?>&order=<?=$order?>&order_by=<?=$order_by?>">Next</a>
    <?php endif; ?>
</div>

<?=template_admin_footer()?>