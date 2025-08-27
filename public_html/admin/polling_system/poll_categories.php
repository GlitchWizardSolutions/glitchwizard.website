<?php
/* 
 * User Poll Categories Management Interface
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: poll_categories.php
 * LOCATION: /public_html/admin/polling_system/
 * PURPOSE: Provides a comprehensive interface for managing all user poll categories with filtering,
 *         sorting, and pagination capabilities.
 * 
 * FILE RELATIONSHIP:
 * This file integrates with:
 * - ../assets/includes/main.php: Core functionality and database connection
 * - User authentication system
 * - Role management system
 * - Account settings configuration
 * 
 * HOW IT WORKS:
 * 1. Loads poll category data with configurable filters (status, role, last seen)
 * 2. Implements pagination for large datasets
 * 3. Provides sorting capabilities for all columns
 * 4. Supports search functionality across category names
 * 5. Displays poll category information in an accessible, sortable table
 * 
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: YES
 */

include '../assets/includes/main.php';
// Retrieve the GET request parameters (if specified)
$pagination_page = isset($_GET['pagination_page']) ? $_GET['pagination_page'] : 1;
$search = isset($_GET['search_query']) ? $_GET['search_query'] : '';
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['id','title','created','num_polls'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'id';
// Number of results per pagination page
$results_per_pagination_page = 20;
// poll categories array
$poll_categories = [];
// Declare query param variables
$param1 = ($pagination_page - 1) * $results_per_pagination_page;
$param2 = $results_per_pagination_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE c.title LIKE :search ' : '';
// Retrieve the total number of poll_categories
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM polls_categories c ' . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
$stmt->execute();
$total_poll_categories = $stmt->fetchColumn();
// Prepare poll categories query
$stmt = $pdo->prepare('SELECT c.*, (SELECT COUNT(*) FROM poll_categories pc WHERE pc.category_id = c.id) AS num_polls FROM polls_categories c ' . $where . 'ORDER BY ' . $order_by . ' ' . $order . ' LIMIT :start_results, :num_results');
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
$stmt->execute();
// Retrieve query results
$poll_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Delete poll category
if (isset($_GET['delete'])) {
    // Delete the poll category
    $stmt = $pdo->prepare('DELETE c, pc FROM polls_categories c LEFT JOIN poll_categories pc ON pc.category_id = c.id WHERE c.id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    header('Location: poll_categories.php?success_msg=3');
    exit;
}
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
    if ($_GET['success_msg'] == 4) {
        $success_msg = 'Categories imported successfully! ' . $_GET['imported'] . ' categories were imported.';
    }
}
// Create URL
$url = 'poll_categories.php?search_query=' . $search;
?>
<?=template_admin_header('Poll Categories', 'polls', 'categories')?>

<div class="content-title mb-4">
    <div class="title">
        <div class="icon">
                    <i class="bi bi-card-checklist" aria-hidden="true"></i>
        </div>
        <div class="txt">
            <h2>Poll Categories</h2>
            <p>View, edit, and create poll categories.</p>
        </div>
    </div>
</div>
<?php if (isset($success_msg)): ?>
<div class="msg success">
    <i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>
    <div><?=$success_msg?></div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<!-- Top page actions -->
<div class="d-flex gap-2 mb-4" role="region" aria-label="Page Actions">
    <a href="poll_category.php" class="btn btn-outline-secondary">
    <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>
        Add Category
    </a>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Poll Categories</h6>
        <small class="text-muted"><?=number_format($total_poll_categories)?> total categories</small>
    </div>
    <div class="card-body">
        <form action="" method="get" class="mb-4">
            <input type="hidden" name="page" value="poll_categories">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="search_query" class="form-label">Search</label>
                    <input id="search_query" type="text" name="search_query" class="form-control"
                        placeholder="Search categories..." 
                        value="<?=htmlspecialchars($search, ENT_QUOTES)?>">
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-search me-1" aria-hidden="true"></i>
                        Apply Filters
                    </button>
                    <?php if ($search): ?>
                    <a href="poll_categories.php" class="btn btn-outline-secondary ms-2">
                        <i class="bi bi-x-lg me-1" aria-hidden="true"></i>
                        Clear
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover mb-0" role="grid" aria-label="Poll Categories">
            <table class="table table-hover mb-0" role="grid" aria-label="Poll Categories">
                <thead class="table-light" role="rowgroup">
                    <tr role="row">
                        <th class="text-start" role="columnheader" scope="col">
                            <a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=title'?>" class="text-decoration-none">
                                Title <?=$order_by=='title' ? ($order=='ASC' ? '▲' : '▼') : ''?>
                            </a>
                        </th>
                        <th class="text-center d-none d-md-table-cell" role="columnheader" scope="col">
                            <a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=num_polls'?>" class="text-decoration-none">
                                # Polls <?=$order_by=='num_polls' ? ($order=='ASC' ? '▲' : '▼') : ''?>
                            </a>
                        </th>
                        <th class="text-center d-none d-lg-table-cell" role="columnheader" scope="col">
                            <a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=created'?>" class="text-decoration-none">
                                Created <?=$order_by=='created' ? ($order=='ASC' ? '▲' : '▼') : ''?>
                            </a>
                        </th>
                        <th class="text-center" role="columnheader" scope="col" style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody role="rowgroup">
                <tbody role="rowgroup">
                    <?php if (empty($poll_categories)): ?>
                    <tr role="row">
                        <td colspan="4" class="text-center py-4">There are no poll categories.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($poll_categories as $poll_category): ?>
                    <tr role="row">
                        <td role="gridcell" class="fw-medium"><?=htmlspecialchars($poll_category['title'], ENT_QUOTES)?></td>
                        <td role="gridcell" class="text-center d-none d-md-table-cell">
                            <a href="polls.php?category=<?=$poll_category['id']?>" class="text-primary text-decoration-none">
                                <?=$poll_category['num_polls'] ? number_format($poll_category['num_polls']) : 0?>
                            </a>
                        </td>
                        <td role="gridcell" class="text-center d-none d-lg-table-cell"><?=date('m/d/Y', strtotime($poll_category['created']))?></td>
                        <td role="gridcell" class="text-center">
                            <div class="table-dropdown">
                                <button class="actions-btn" aria-haspopup="true" aria-expanded="false" aria-label="Actions for category <?=htmlspecialchars($poll_category['title'])?>">
                                    <i class="bi bi-three-dots-vertical" aria-hidden="true"></i>
                                </button>
                                <div class="table-dropdown-items" role="menu" aria-label="Category Actions">
                                    <div role="menuitem">
                                        <a href="poll_category.php?id=<?=$poll_category['id']?>" class="green" tabindex="-1" aria-label="Edit category <?=htmlspecialchars($poll_category['title'])?>">
                                            <span class="icon" aria-hidden="true">
                                                <i class="bi bi-pencil-square"></i>
                                            </span>
                                            <span>Edit</span>
                                        </a>
                                    </div>
                                    <div role="menuitem">
                                        <a class="black" href="poll_categories.php?delete=<?=$poll_category['id']?>" onclick="return confirm('Are you sure you want to delete this category?')" tabindex="-1" aria-label="Delete category <?=htmlspecialchars($poll_category['title'])?>">
                                            <span class="icon" aria-hidden="true">
                                                <i class="bi bi-trash"></i>
                                            </span>
                                            <span>Delete</span>
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
    <div class="card-footer bg-light">
        <!-- Bootstrap Pagination -->
        <nav aria-label="Poll Categories pagination">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Showing page <?=$pagination_page?> of <?=ceil($total_poll_categories / $results_per_pagination_page) == 0 ? 1 : ceil($total_poll_categories / $results_per_pagination_page)?> 
                    (<?=$total_poll_categories?> total categories)
                </div>
                <ul class="pagination pagination-sm mb-0">
                    <?php if ($pagination_page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?=$url?>&pagination_page=<?=$pagination_page-1?>&order=<?=$order?>&order_by=<?=$order_by?>" aria-label="Previous page">
                            <i class="bi bi-chevron-left" aria-hidden="true"></i>
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link"><i class="bi bi-chevron-left" aria-hidden="true"></i></span>
                    </li>
                    <?php endif; ?>
                    
                    <li class="page-item active">
                        <span class="page-link"><?=$pagination_page?></span>
                    </li>
                    
                    <?php if ($pagination_page * $results_per_pagination_page < $total_poll_categories): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?=$url?>&pagination_page=<?=$pagination_page+1?>&order=<?=$order?>&order_by=<?=$order_by?>" aria-label="Next page">
                            <i class="bi bi-chevron-right" aria-hidden="true"></i>
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link"><i class="bi bi-chevron-right" aria-hidden="true"></i></span>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </div>
</div>

<?=template_admin_footer()?>