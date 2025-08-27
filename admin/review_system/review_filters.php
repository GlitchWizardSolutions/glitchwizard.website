<?php
include_once '../assets/includes/main.php';
// Retrieve the GET request parameters (if specified)
$pagination_page = isset($_GET['pagination_page']) ? $_GET['pagination_page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['id','word','replacement'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'id';
// Number of results per pagination page
$results_per_page = 15;
// Declare query param variables
$param1 = ($pagination_page - 1) * $results_per_page;
$param2 = $results_per_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE (word LIKE :search OR replacement LIKE :search) ' : '';
// Retrieve the total number of filters
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM review_filters ' . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
$stmt->execute();
$filters_total = $stmt->fetchColumn();
// SQL query to get all filters from the "filters" table
$stmt = $pdo->prepare('SELECT * FROM review_filters ' . $where . ' ORDER BY ' . $order_by . ' ' . $order . ' LIMIT :start_results,:num_results');
// Bind params
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
$stmt->execute();
// Retrieve query results
$filters = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
}
// Determine the URL
$url = 'review_filters.php?search=' . $search;
// CANONICAL TABLE SORTING: Use triangle icons to match accounts.php standard
$table_icons = [
    'asc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9650;</span>', // ▲
    'desc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9660;</span>' // ▼
];
?>
<?=template_admin_header('Review Filters', 'reviews', 'filters')?>

<!-- PHASE 2 STANDARDIZATION: Unified Content Title Block -->
<div class="content-title" id="main-filters-list" role="banner" aria-label="Review Filters List Header">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                <path d="M3.9 54.9C10.5 40.9 24.5 32 40 32H472c15.5 0 29.5 8.9 36.1 22.9s4.6 30.5-5.2 43.5L320 320.9V448c0 12.1-6.8 23.2-17.7 28.6s-23.8 4.3-33.5-3l-64-48c-8.1-6-12.8-15.5-12.8-25.6V320.9L9 98.4c-9.8-13-10.8-29.5-5.2-43.5z"/>
            </svg>
        </div>
        <div class="txt">
            <h2>Review Filters</h2>
            <p>View, manage, and search review filters. Total filters: <?=number_format($filters_total)?></p>
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
    <a href="review_filter.php" class="btn btn-success">
        <i class="fas fa-plus me-1"></i>Create Filter
    </a>
</div>

<!-- Search Form -->
<form action="" method="get" class="mb-3">
    <div class="row g-3 align-items-end">
        <div class="col-md-8">
            <label for="search" class="form-label">Search Filters</label>
            <input 
                id="search" 
                type="text" 
                name="search" 
                placeholder="Search filters..." 
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
                <a href="review_filters.php" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1" aria-hidden="true"></i>Clear
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</form>

<!-- Filters Management Card -->
<div class="row">
    <div class="col-12">
        <div class="card" role="region" aria-labelledby="filters-heading">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                     
                    <h6 id="filters-heading" class="card-title mb-0">Review Filters</h6>
                    <small class="text-muted ms-2"><?=number_format($filters_total)?> total</small>
                </div>
            </div>
            
            <!-- Table -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="text-start" style="text-align: left !important;">
                                    <?php $q = $_GET; $q['order_by'] = 'word'; $q['order'] = ($order_by == 'word' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                                    <a href="?<?= http_build_query($q) ?>" class="text-decoration-none text-dark">Word<?= $order_by == 'word' ? $table_icons[strtolower($order)] : '' ?></a>
                                </th>
                                <th scope="col" class="text-start" style="text-align: left !important;">
                                    <?php $q = $_GET; $q['order_by'] = 'replacement'; $q['order'] = ($order_by == 'replacement' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                                    <a href="?<?= http_build_query($q) ?>" class="text-decoration-none text-dark">Replacement<?= $order_by == 'replacement' ? $table_icons[strtolower($order)] : '' ?></a>
                                </th>
                                <th scope="col" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($filters)): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    There are no filters.
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($filters as $filter): ?>
                            <tr>
                                <td class="text-start"><?=htmlspecialchars($filter['word'], ENT_QUOTES)?></td>
                                <td class="text-start"><?=htmlspecialchars($filter['replacement'], ENT_QUOTES)?></td>
                                <td class="actions text-center">
                                    <div class="table-dropdown">
                                        <button class="actions-btn" aria-haspopup="true" aria-expanded="false"
                                            aria-label="Actions for filter: <?= htmlspecialchars($filter['word'], ENT_QUOTES) ?>">
                                            <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">
                                                <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z" />
                                            </svg>
                                        </button>
                                        <div class="table-dropdown-items" role="menu" aria-label="Filter Actions">
                                            <div role="menuitem">
                                                <a href="review_filter.php?id=<?=$filter['id']?>" class="green" tabindex="-1" 
                                                   aria-label="Edit filter: <?= htmlspecialchars($filter['word'], ENT_QUOTES) ?>">
                                                    <span class="icon" aria-hidden="true">
                                                        <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                            <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z" />
                                                        </svg>
                                                    </span>
                                                    <span>Edit</span>
                                                </a>
                                            </div>
                                            <div role="menuitem">
                                                <a href="review_filters.php?delete=<?=$filter['id']?>" class="red" tabindex="-1"
                                                   onclick="return confirm('Are you sure you want to delete this filter?')"
                                                   aria-label="Delete filter: <?= htmlspecialchars($filter['word'], ENT_QUOTES) ?>">
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
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <?php if ($filters_total > 0): ?>
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Showing <?= count($filters) ?> of <?= number_format($filters_total) ?> filters
                    </small>
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Filters automatically apply to new reviews
                    </small>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?=template_admin_footer()?>