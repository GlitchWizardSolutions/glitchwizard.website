<?php
/* 
 * Shop Products Management
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: products.php
 * LOCATION: /public_html/admin/shop_system/
 * PURPOSE: Standalone products management page with admin integration
 * 
 * CREATED: 2025-08-18
 * VERSION: 2.0 (Standalone)
 */

// Include admin authentication and dependencies
include '../assets/includes/main.php';
include '../../assets/includes/settings/shop_settings.php';
include '../../shop_system/functions.php';

// Retrieve the GET request parameters (if specified)
$pagination_page = isset($_GET['pagination_page']) ? $_GET['pagination_page'] : 1;
$search = isset($_GET['search_query']) ? $_GET['search_query'] : '';
// Filters parameters
$status = isset($_GET['status']) ? $_GET['status'] : '';
$availability = isset($_GET['availability']) ? $_GET['availability'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['id','title','price','quantity','created','product_status','sku','subscription'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'id';
// Number of results per pagination pagination_page
$results_per_pagination_page = 15;
// products array
$products = [];
// Declare query param variables
$param1 = ($pagination_page - 1) * $results_per_pagination_page;
$param2 = $results_per_pagination_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE (p.title LIKE :search OR p.sku LIKE :search OR p.price LIKE :search OR p.created LIKE :search) ' : '';
// Add filters
// Status filter
if ($status == 'Enabled') {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'p.product_status = 1 ';
}
if ($status == 'Disabled') {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'p.product_status = 0 ';
}
if ($availability == 'unavailable') {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'p.quantity = 0 ';
}
if ($availability == 'available') {
    $where .= ($where ? 'AND ' : 'WHERE ') . '(p.quantity > 0 OR p.quantity = -1) ';
}
if ($type == 'subscription') {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'p.subscription = 1 ';
}
if ($type == 'normal') {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'p.subscription = 0 ';
}
if ($category) {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'p.id IN (SELECT pc.product_id FROM shop_product_category pc WHERE pc.category_id = :category) ';
}
// Get all categories
$categories = $pdo->query('SELECT * FROM shop_product_categories ORDER BY title ASC')->fetchAll(PDO::FETCH_ASSOC);
// Retrieve the total number of products
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM shop_products p ' . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($category) $stmt->bindParam('category', $category, PDO::PARAM_INT);
$stmt->execute();
$total_products = $stmt->fetchColumn();
// Prepare products query
$stmt = $pdo->prepare('SELECT p.*, GROUP_CONCAT(m2.full_path) AS imgs FROM shop_products p LEFT JOIN (SELECT pm.id, pm.product_id, m.full_path FROM shop_product_media_map pm JOIN shop_product_media m ON m.id = pm.media_id GROUP BY pm.id, pm.product_id, m.full_path) m2 ON m2.product_id = p.id ' . $where . ' GROUP BY p.id, p.title, p.description, p.price, p.rrp, p.quantity, p.created, p.weight, p.url_slug, p.product_status, p.sku, p.subscription, p.subscription_period, p.subscription_period_type ORDER BY ' . $order_by . ' ' . $order . ' LIMIT :start_results,:num_results');
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($category) $stmt->bindParam('category', $category, PDO::PARAM_INT);
$stmt->execute();
// Retrieve query results
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Delete product
if (isset($_GET['delete'])) {
    // Delete the product
    $stmt = $pdo->prepare('DELETE p, pm, po, pc FROM shop_products p LEFT JOIN shop_product_media_map pm ON pm.product_id = p.id LEFT JOIN shop_product_options po ON po.product_id = p.id LEFT JOIN shop_product_category pc ON pc.product_id = p.id WHERE p.id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    // Clear session cart
    if (isset($_SESSION['cart'])) {
        unset($_SESSION['cart']);
    }
    header('Location: products.php?success_msg=3');
    exit;
}
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Product created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Product updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Product deleted successfully!';
    }
    if ($_GET['success_msg'] == 4) {
        $success_msg = 'Product(s) imported successfully! ' . $_GET['imported'] . ' product(s) were imported.';
    }
}
// Create URL
$url = 'products.php?search_query=' . $search . '&status=' . $status . '&availability=' . $availability . '&type=' . $type;
?>
<?=template_admin_header('Products', 'shop', 'products')?>


<div class="content-title mb-4">
    <div class="title">
        <div class="icon">
            <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M0 24C0 10.7 10.7 0 24 0H69.5c22 0 41.5 12.8 50.6 32h411c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3H170.7l5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5H488c13.3 0 24 10.7 24 24s-10.7 24-24 24H199.7c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5H24C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z"/></svg>
        </div>
        <div class="txt">
            <h2>Products</h2>
            <p>View, edit, and create products.</p>
        </div>
    </div>
</div>


<?php if (isset($success_msg)): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>
    <?=$success_msg?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>


<div class="d-flex gap-2 mb-4">
    <a href="product.php" class="btn btn-outline-secondary">
        <i class="bi bi-plus-lg me-1"></i>Create Product
    </a>
</div>

<div class="card mb-3 product-table-card">
    <div class="card-header product-table-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Product Management</h6>
        <small class="text-muted"><?= number_format($total_products) ?> total products</small>
    </div>
    <div class="card-body p-0">
        <div class="table-filters-wrapper p-3">
        <form action="" method="get" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label for="search_query" class="form-label">Search</label>
                    <input id="search_query" type="text" name="search_query" class="form-control"
                        placeholder="Search products..." 
                        value="<?= htmlspecialchars($search, ENT_QUOTES) ?>">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="" <?= $status == '' ? ' selected' : '' ?>>All</option>
                        <option value="Enabled" <?= $status == 'Enabled' ? ' selected' : '' ?>>Enabled</option>
                        <option value="Disabled" <?= $status == 'Disabled' ? ' selected' : '' ?>>Disabled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="availability" class="form-label">Availability</label>
                    <select name="availability" id="availability" class="form-select">
                        <option value="" <?= $availability == '' ? ' selected' : '' ?>>All</option>
                        <option value="available" <?= $availability == 'available' ? ' selected' : '' ?>>In Stock</option>
                        <option value="unavailable" <?= $availability == 'unavailable' ? ' selected' : '' ?>>Out of Stock</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="type" class="form-label">Type</label>
                    <select name="type" id="type" class="form-select">
                        <option value="" <?= $type == '' ? ' selected' : '' ?>>All</option>
                        <option value="subscription" <?= $type == 'subscription' ? ' selected' : '' ?>>Subscription</option>
                        <option value="normal" <?= $type == 'normal' ? ' selected' : '' ?>>Normal</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="category" class="form-label">Category</label>
                    <select name="category" id="category" class="form-select">
                        <option value="" <?= $category == '' ? ' selected' : '' ?>>All</option>
                        <?php foreach ($categories as $c): ?>
                        <option value="<?=$c['id']?>"<?=$c['id']==$category?' selected':''?>><?=$c['title']?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filters" class="form-label">Filters</label>
                    <button type="submit" class="btn btn-success w-100 d-flex align-items-center justify-content-center">
                        <i class="bi bi-funnel me-2" aria-hidden="true"></i>
                        Apply
                    </button>
                </div>
            </div>
        </form>

        <!-- Active Filters -->
        <?php if ($search || $status || $availability || $type || $category): ?>
            <div class="mb-3">
                <h6 class="mb-2">Active Filters:</h6>
                <div class="d-flex flex-wrap gap-2">
                    <?php if ($search): ?>
                        <span class="badge bg-secondary">
                            Search: <?= htmlspecialchars($search, ENT_QUOTES) ?>
                            <a href="<?= remove_url_param($url, 'search_query') ?>" class="text-white ms-1" aria-label="Remove search filter">×</a>
                        </span>
                    <?php endif; ?>
                    <?php if ($status): ?>
                        <span class="badge bg-secondary">
                            Status: <?= htmlspecialchars($status, ENT_QUOTES) ?>
                            <a href="<?= remove_url_param($url, 'status') ?>" class="text-white ms-1" aria-label="Remove status filter">×</a>
                        </span>
                    <?php endif; ?>
                    <?php if ($availability): ?>
                        <span class="badge bg-secondary">
                            Availability: <?= htmlspecialchars($availability, ENT_QUOTES) ?>
                            <a href="<?= remove_url_param($url, 'availability') ?>" class="text-white ms-1" aria-label="Remove availability filter">×</a>
                        </span>
                    <?php endif; ?>
                    <?php if ($type): ?>
                        <span class="badge bg-secondary">
                            Type: <?= htmlspecialchars($type, ENT_QUOTES) ?>
                            <a href="<?= remove_url_param($url, 'type') ?>" class="text-white ms-1" aria-label="Remove type filter">×</a>
                        </span>
                    <?php endif; ?>
                    <?php if ($category): ?>
                        <span class="badge bg-secondary">
                            Category: <?php foreach ($categories as $c) { if ($c['id'] == $category) { echo htmlspecialchars($c['title'], ENT_QUOTES); } } ?>
                            <a href="<?= remove_url_param($url, 'category') ?>" class="text-white ms-1" aria-label="Remove category filter">×</a>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        </div>

        <div class="table-responsive product-table-wrapper">
            <table class="table table-hover mb-0 product-table">
                <thead class="table-light">
                    <tr>
                        <th style="min-width: 120px; text-align:left;">Title
                            <?php $q = $_GET; $q['order_by'] = 'title'; $q['order'] = ($order_by == 'title' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>#products-table" class="text-decoration-none text-dark"><?= $order_by == 'title' ? ($order == 'ASC' ? ' ↑' : ' ↓') : '' ?></a>
                        </th>
                        <th class="responsive-hidden" style="min-width: 80px; text-align:left;">SKU
                            <?php $q = $_GET; $q['order_by'] = 'sku'; $q['order'] = ($order_by == 'sku' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>#products-table" class="text-decoration-none text-dark"><?= $order_by == 'sku' ? ($order == 'ASC' ? ' ↑' : ' ↓') : '' ?></a>
                        </th>
                        <th style="min-width: 80px;">Price
                            <?php $q = $_GET; $q['order_by'] = 'price'; $q['order'] = ($order_by == 'price' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>#products-table" class="text-decoration-none text-dark"><?= $order_by == 'price' ? ($order == 'ASC' ? ' ↑' : ' ↓') : '' ?></a>
                        </th>
                        <th style="min-width: 80px; text-align:left;">Quantity
                            <?php $q = $_GET; $q['order_by'] = 'quantity'; $q['order'] = ($order_by == 'quantity' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>#products-table" class="text-decoration-none text-dark"><?= $order_by == 'quantity' ? ($order == 'ASC' ? ' ↑' : ' ↓') : '' ?></a>
                        </th>
                        <th class="responsive-hidden" style="min-width: 100px;">Images</th>
                        <th class="responsive-hidden" style="min-width: 80px;">Type
                            <?php $q = $_GET; $q['order_by'] = 'subscription'; $q['order'] = ($order_by == 'subscription' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>#products-table" class="text-decoration-none text-dark"><?= $order_by == 'subscription' ? ($order == 'ASC' ? ' ↑' : ' ↓') : '' ?></a>
                        </th>
                        <th class="responsive-hidden" style="min-width: 80px;">Status
                            <?php $q = $_GET; $q['order_by'] = 'product_status'; $q['order'] = ($order_by == 'product_status' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>#products-table" class="text-decoration-none text-dark"><?= $order_by == 'product_status' ? ($order == 'ASC' ? ' ↑' : ' ↓') : '' ?></a>
                        </th>
                        <th class="responsive-hidden" style="min-width: 120px; text-align:right;">Date
                            <?php $q = $_GET; $q['order_by'] = 'created'; $q['order'] = ($order_by == 'created' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>#products-table" class="text-decoration-none text-dark"><?= $order_by == 'created' ? ($order == 'ASC' ? ' ↑' : ' ↓') : '' ?></a>
                        </th>
                        <th class="text-center" style="min-width: 80px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$products): ?>
                    <tr>
                        <td colspan="10" class="no-results">There are no products.</td>
                    </tr>
                    <?php endif; ?>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td style="text-align:left;"><?=$product['title']?></td>
                        <td class="responsive-hidden alt" style="text-align:left;"><?=$product['sku']?></td>
                        <?php if ($product['rrp'] == 0.00): ?>
                        <td class="strong"><?=getShopConfig('shop_currency_code', '$')?><?=num_format($product['price'], 2)?></td>
                        <?php else: ?>
                        <td class="strong"><span class="rrp"><?=getShopConfig('shop_currency_code', '$')?><?=num_format($product['price'], 2)?></span> <s><?=getShopConfig('shop_currency_code', '$') . num_format($product['rrp'], 2)?></s></td>
                        <?php endif; ?>
                        <td style="text-align:left;"><?=$product['quantity']==-1?'<span class="alt">--</span>':num_format($product['quantity'])?></td>
                        <td class="responsive-hidden">
                            <div class="images">
                            <?php if (!empty($product['imgs'])): ?>
                            <?php foreach (array_reverse(explode(',',$product['imgs'])) as $img): ?>
                            <?php if ($img): ?>
                            <img src="../../shop_system/<?=$img?>" width="32" height="32" alt="<?=$img?>" title="<?=$img?>">
                            <?php endif; ?>
                            <?php endforeach; ?>
                            <?php endif; ?>
                            </div>
                        </td>
                        <td class="responsive-hidden"><?=$product['subscription'] ? '<span class="blue">Subscription</span>' : '<span class="grey">Normal</span>'?></td>
                        <td class="responsive-hidden"><?=$product['product_status'] ? '<span class="green">Enabled</span>' : '<span class="grey">Disabled</span>'?></td>
                        <td class="responsive-hidden alt" style="text-align:right;"><?=date('n/j/Y', strtotime($product['created']))?></td>
                        <td class="actions" style="text-align: center;">
                            <div class="table-dropdown">
                                <svg width="32" height="32" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                    <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/>
                                </svg>
                                <div class="table-dropdown-items" role="menu" aria-label="Product Actions">
                                    <div role="menuitem">
                                        <a href="product.php?id=<?=$product['id']?>" class="green" aria-label="Edit product <?=$product['title']?>">
                                            <span class="icon" aria-hidden="true">
                                                <i class="bi bi-pencil-square"></i>
                                            </span>
                                            <span>Edit</span>
                                        </a>
                                    </div>
                                    <div role="menuitem">
                                        <a class="red" href="products.php?delete=<?=$product['id']?>" onclick="return confirm('Are you sure you want to delete this product?')" aria-label="Delete product <?=$product['title']?>">
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
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer product-table-footer bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Showing <?= count($products) ?> of <?= $total_products ?> products
            </small>
            <nav aria-label="Products pagination">
                <div class="d-flex gap-2">
                    <?php if ($pagination_page > 1): ?>
                        <a href="<?= $url ?>&pagination_page=<?= $pagination_page - 1 ?>&order=<?= $order ?>&order_by=<?= $order_by ?>" 
                           class="btn btn-sm btn-outline-secondary">Previous</a>
                    <?php endif; ?>
                    <span class="btn btn-sm btn-secondary disabled">
                        Page <?= $pagination_page ?> of <?= ceil($total_products / $results_per_pagination_page) == 0 ? 1 : ceil($total_products / $results_per_pagination_page) ?>
                    </span>
                    <?php if ($pagination_page * $results_per_pagination_page < $total_products): ?>
                        <a href="<?= $url ?>&pagination_page=<?= $pagination_page + 1 ?>&order=<?= $order ?>&order_by=<?= $order_by ?>" 
                           class="btn btn-sm btn-outline-secondary">Next</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </div>
</div>



<?=template_admin_footer()?>