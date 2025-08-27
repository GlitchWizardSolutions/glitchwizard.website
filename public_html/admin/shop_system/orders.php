<?php
// Check if being called from admin index.php or directly


if (!defined('shoppingcart_admin')) {
    // Include admin authentication directly
    include '../assets/includes/main.php';
    // Include shop settings
    include '../../assets/includes/settings/shop_settings.php';
    // Include shop functions
    include '../../shop_system/functions.php';
}

// Retrieve the GET request parameters (if specified)
$pagination_page = isset($_GET['pagination_page']) ? $_GET['pagination_page'] : 1;
$search = isset($_GET['search_query']) ? $_GET['search_query'] : '';
// Filters parameters
$status = isset($_GET['status']) ? $_GET['status'] : '';
$method = isset($_GET['method']) ? $_GET['method'] : '';
$account_id = isset($_GET['account_id']) ? $_GET['account_id'] : '';
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['id','first_name','total_products','payment_amount','payment_method','payment_status','created','payer_email'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'id';
// Number of results per pagination pagination_page
$results_per_pagination_page = 15;
// orders array
$orders = [];
// Declare query param variables
$param1 = ($pagination_page - 1) * $results_per_pagination_page;
$param2 = $results_per_pagination_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE (t.first_name LIKE :search OR t.last_name LIKE :search OR t.id LIKE :search OR t.txn_id LIKE :search OR t.payer_email LIKE :search)  ' : '';
// Add filters
// Status filter
if ($status) {
    $where .= ($where ? 'AND ' : 'WHERE ') . 't.payment_status = :status ';
}
// Method filter
if ($method) {
    $where .= ($where ? 'AND ' : 'WHERE ') . 't.payment_method = :method ';
}
// Account ID filter
if ($account_id) {
    $where .= ($where ? 'AND ' : 'WHERE ') . 't.account_id = :account_id ';
}
// Retrieve the total number of orders
$stmt = $pdo->prepare('SELECT COUNT(DISTINCT t.id) AS total FROM shop_transactions t LEFT JOIN shop_transaction_items ti ON ti.txn_id = t.txn_id ' . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($status) $stmt->bindParam('status', $status, PDO::PARAM_STR);
if ($method) $stmt->bindParam('method', $method, PDO::PARAM_STR);
if ($account_id) $stmt->bindParam('account_id', $account_id, PDO::PARAM_INT);
$stmt->execute();
$total_orders = $stmt->fetchColumn();
// Prepare orders query
$stmt = $pdo->prepare('SELECT t.*, COUNT(ti.id) AS total_products FROM shop_transactions t LEFT JOIN shop_transaction_items ti ON ti.txn_id = t.txn_id ' . $where . ' GROUP BY t.id, t.txn_id, t.payment_amount, t.payment_status, t.created, t.payer_email, t.first_name, t.last_name, t.address_street, t.address_city, t.address_state, t.address_zip, t.address_country, t.account_id, t.payment_method, t.discount_code, t.shipping_method, t.shipping_amount, t.tax_amount ORDER BY ' . $order_by . ' ' . $order . ' LIMIT :start_results,:num_results');
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($status) $stmt->bindParam('status', $status, PDO::PARAM_STR);
if ($method) $stmt->bindParam('method', $method, PDO::PARAM_STR);
if ($account_id) $stmt->bindParam('account_id', $account_id, PDO::PARAM_INT);
$stmt->execute();
// Retrieve query results
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Delete order
if (isset($_GET['delete'])) {
    // Delete the order
    $stmt = $pdo->prepare('DELETE t, ti FROM shop_transactions t LEFT JOIN shop_transaction_items ti ON ti.txn_id = t.txn_id WHERE t.id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    header('Location: orders.php?success_msg=3');
    exit;
}
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Order created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Order updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Order deleted successfully!';
    }
}
// Create URL
$url = 'orders.php?search_query=' . $search . '&status=' . $status . '&method=' . $method . '&account_id=' . $account_id;
?>
<?=template_admin_header('Orders', 'shop', 'orders')?>

<div class="content-title" id="main-shop-orders" role="banner" aria-label="Shop Orders Management Header">
    <div class="title">
        <div class="icon">
            <i class="bi bi-cart" aria-hidden="true"></i>
        </div>
        <div class="txt">
            <h2>Orders</h2>
            <p>View, edit, and create orders</p>
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
    <a href="order_manage.php" class="btn btn-outline-secondary">
        <i class="bi bi-plus-lg me-1"></i>Create Order
    </a>
    
</div>
     
<div class="card mb-3 order-table-card">
    <div class="card-header order-table-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Order Management</h6>
        <small class="text-muted"><?= number_format($total_orders) ?> total orders</small>
    </div>
    <div class="card-body p-0">
        <div class="table-filters-wrapper p-3">
            <form action="" method="get" class="mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="search_query" class="form-label">Search</label>
                        <input id="search_query" type="text" name="search_query" class="form-control" placeholder="Search orders..." value="<?= htmlspecialchars($search, ENT_QUOTES) ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value=""<?= $status == '' ? ' selected' : '' ?>>All</option>
                            <option value="Completed"<?= $status == 'Completed' ? ' selected' : '' ?>>Completed</option>
                            <option value="Pending"<?= $status == 'Pending' ? ' selected' : '' ?>>Pending</option>
                            <option value="Cancelled"<?= $status == 'Cancelled' ? ' selected' : '' ?>>Cancelled</option>
                            <option value="Reversed"<?= $status == 'Reversed' ? ' selected' : '' ?>>Reversed</option>
                            <option value="Failed"<?= $status == 'Failed' ? ' selected' : '' ?>>Failed</option>
                            <option value="Refunded"<?= $status == 'Refunded' ? ' selected' : '' ?>>Refunded</option>
                            <option value="Shipped"<?= $status == 'Shipped' ? ' selected' : '' ?>>Shipped</option>
                            <option value="Subscribed"<?= $status == 'Subscribed' ? ' selected' : '' ?>>Subscribed</option>
                            <option value="Unsubscribed"<?= $status == 'Unsubscribed' ? ' selected' : '' ?>>Unsubscribed</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="method" class="form-label">Method</label>
                        <select name="method" id="method" class="form-select">
                            <option value=""<?= $method == '' ? ' selected' : '' ?>>All</option>
                            <option value="website"<?= $method == 'website' ? ' selected' : '' ?>>Website</option>
                            <option value="paypal"<?= $method == 'paypal' ? ' selected' : '' ?>>PayPal</option>
                            <option value="stripe"<?= $method == 'stripe' ? ' selected' : '' ?>>Stripe</option>
                            <option value="coinbase"<?= $method == 'coinbase' ? ' selected' : '' ?>>Coinbase</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-funnel me-1"></i>
                            Apply Filters
                        </button>
                    </div>
                </div>
            </form>
            <?php if ($status || $method || $account_id || $search): ?>
            <div class="mb-3">
                <h6 class="mb-2">Active Filters:</h6>
                <div class="d-flex flex-wrap gap-2">
                    <?php if ($status): ?>
                        <span class="badge bg-secondary">
                            Status: <?= htmlspecialchars($status, ENT_QUOTES) ?>
                            <a href="<?= remove_url_param($url, 'status') ?>" class="text-white ms-1" aria-label="Remove status filter">×</a>
                        </span>
                    <?php endif; ?>
                    <?php if ($method): ?>
                        <span class="badge bg-secondary">
                            Method: <?= htmlspecialchars($method, ENT_QUOTES) ?>
                            <a href="<?= remove_url_param($url, 'method') ?>" class="text-white ms-1" aria-label="Remove method filter">×</a>
                        </span>
                    <?php endif; ?>
                    <?php if ($account_id): ?>
                        <span class="badge bg-secondary">
                            Account: <?= htmlspecialchars($account_id, ENT_QUOTES) ?>
                            <a href="<?= remove_url_param($url, 'account_id') ?>" class="text-white ms-1" aria-label="Remove account filter">×</a>
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
        </div>
        <div class="table-responsive order-table-wrapper">
            <table class="table table-hover align-middle mb-0 order-table">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th colspan="2">Customer</th>
                        <th class="responsive-hidden">Email</th>
                        <th class="responsive-hidden">Products</th>
                        <th>Total</th>
                        <th class="responsive-hidden">Method</th>
                        <th class="responsive-hidden">Status</th>
                        <th class="responsive-hidden">Date</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$orders): ?>
                    <tr>
                        <td colspan="10" class="no-results">There are no orders.</td>
                    </tr>
                    <?php endif; ?>
                    <?php foreach ($orders as $o): ?>
                    <tr>
                        <td class="alt"><?=$o['id']?></td>
                        <td class="img">
                            <div class="profile-img">
                                <span style="background-color:<?=color_from_string($o['first_name'])?>"><?=strtoupper(substr($o['first_name'], 0, 1))?></span>
                            </div>
                        </td>
                        <td><a href="#" class="view-customer-details link1" data-transaction-id="<?=$o['id']?>"><?=htmlspecialchars($o['first_name'], ENT_QUOTES)?> <?=htmlspecialchars($o['last_name'], ENT_QUOTES)?></a></td>
                        <td class="responsive-hidden alt"><?=htmlspecialchars($o['payer_email'], ENT_QUOTES)?></td>
                        <td class="responsive-hidden"><span class="grey small"><?=$o['total_products']?></span></td>
                        <td class="strong"><?=getShopConfig('shop_currency_code', '$')?><?=num_format($o['payment_amount'], 2)?></td>
                        <td class="responsive-hidden"><span class="grey"><?=$o['payment_method']?></span></td>
                        <td class="responsive-hidden"><span class="<?=str_replace(['completed','pending','cancelled','reversed','failed','refunded','shipped','unsubscribed','subscribed'],['green','orange','red','red','red','red','green','red','blue'], strtolower($o['payment_status']))?>"><?=$o['payment_status']?></span></td>
                        <td class="responsive-hidden alt"><?=date('n/j/Y', strtotime($o['created']))?></td>
                        <td class="actions text-center">
                            <div class="table-dropdown">
                                <i class="bi bi-three-dots-vertical" style="font-size: 1.5rem; cursor: pointer;"></i>
                                <div class="table-dropdown-items" role="menu" aria-label="Order Actions">
                                    <div role="menuitem">
                                        <a href="order.php?id=<?=$o['id']?>">
                                            <span class="icon"><i class="bi bi-eye"></i></span>
                                            View
                                        </a>
                                    </div>
                                    <div role="menuitem">
                                        <a href="order_manage.php?id=<?=$o['id']?>">
                                            <span class="icon"><i class="bi bi-pencil-square"></i></span>
                                            Edit
                                        </a>
                                    </div>
                                    <div role="menuitem">
                                        <a class="red" href="orders.php?delete=<?=$o['id']?>" onclick="return confirm('Are you sure you want to delete this order?')">
                                            <span class="icon"><i class="bi bi-trash"></i></span>
                                            Delete
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
    <div class="card-footer order-table-footer bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Showing <?= count($orders) ?> of <?= $total_orders ?> orders
            </small>
            <nav aria-label="Orders pagination">
                <div class="d-flex gap-2">
                    <?php if ($pagination_page > 1): ?>
                        <a href="<?= $url ?>&pagination_page=<?= $pagination_page - 1 ?>&order=<?= $order ?>&order_by=<?= $order_by ?>"
                           class="btn btn-sm btn-outline-secondary">Previous</a>
                    <?php endif; ?>
                    <span class="btn btn-sm btn-secondary disabled">
                        Page <?= $pagination_page ?> of <?= ceil($total_orders / $results_per_pagination_page) == 0 ? 1 : ceil($total_orders / $results_per_pagination_page) ?>
                    </span>
                    <?php if ($pagination_page * $results_per_pagination_page < $total_orders): ?>
                        <a href="<?= $url ?>&pagination_page=<?= $pagination_page + 1 ?>&order=<?= $order ?>&order_by=<?= $order_by ?>"
                           class="btn btn-sm btn-outline-secondary">Next</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </div>
</div>

<?=template_admin_footer()?>