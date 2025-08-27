<?php
/*
PAGE NAME  : myaccount.php
LOCATION   : public_html/myaccount.php
DESCRIPTION: User account page for viewing orders, wishlist, and account settings
FUNCTION   : Display user orders, downloads, wishlist, and allow account updates
CHANGE LOG : Integrated from shop_system to main site structure
*/

// Include necessary files
include_once "assets/includes/doctype.php";
include_once "assets/includes/header.php";

// Load shop system functionality
include_once "shop_system/shop_load.php";
include_once "shop_system/functions.php";

// Include shop navigation
include_once "shop_system/shop_nav.php";

// Check if user is logged in
if (!$logged_in || !$rowusers) {
    header('Location: auth.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Get user account information from main site
$account = $rowusers;

// Determine the current tab page
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'orders';

// Date filters
$date = isset($_GET['date']) ? $_GET['date'] : 'all';
$date_sql = '';
if ($date == 'last30days') {
    $date_sql = 'AND created >= DATE_SUB("' . date('Y-m-d') . '", INTERVAL 30 DAY)';
} else if ($date == 'last6months') {
    $date_sql = 'AND created >= DATE_SUB("' . date('Y-m-d') . '", INTERVAL 6 MONTH)';
}

// Status filters
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$status_sql = '';
if ($status != 'all') {
    $status_sql = 'AND payment_status = :status';
}

// Get orders
$stmt = $pdo->prepare('SELECT * FROM shop_transactions WHERE account_id = :account_id ' . $date_sql . ' ' . $status_sql . ' ORDER BY created DESC');
$params = [ 'account_id' => $account['id'] ];
if ($status != 'all') {
    $params['status'] = $status;
}
$stmt->execute($params);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get downloads
$downloads = [];
if ($transactions) {
    $transactions_ids = array_column($transactions, 'txn_id');
    if ($transactions_ids) {
        $stmt = $pdo->prepare('SELECT product_id, file_path, id FROM shop_product_downloads WHERE product_id IN (SELECT item_id FROM shop_transaction_items WHERE txn_id IN (' . trim(str_repeat('?,',count($transactions_ids)),',') . ')) ORDER BY position ASC');
        $stmt->execute($transactions_ids);
        $downloads = $stmt->fetchAll(PDO::FETCH_GROUP);
    }
}

// Update account settings
$error = '';
if (isset($_POST['save_details'])) {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $address_street = $_POST['address_street'] ?? '';
    $address_city = $_POST['address_city'] ?? '';
    $address_state = $_POST['address_state'] ?? '';
    $address_zip = $_POST['address_zip'] ?? '';
    $address_country = $_POST['address_country'] ?? '';
    
    // Check if email already exists
    $stmt = $pdo->prepare('SELECT * FROM accounts WHERE email = ? AND id != ?');
    $stmt->execute([ $_POST['email'], $account['id'] ]);
    
    if ($_POST['email'] != $account['email'] && $stmt->fetch(PDO::FETCH_ASSOC)) {
        $error = 'Account already exists with that email!';
    } else if ($_POST['password'] && (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5)) {
        $error = 'Password must be between 5 and 20 characters long!';
    } else {
        // Update account details in database
        $password = $_POST['password'] ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $account['password'];
        $stmt = $pdo->prepare('UPDATE accounts SET email = ?, password = ?, first_name = ?, last_name = ?, address_street = ?, address_city = ?, address_state = ?, address_zip = ?, address_country = ? WHERE id = ?');
        $stmt->execute([ $_POST['email'], $password, $first_name, $last_name, $address_street, $address_city, $address_state, $address_zip, $address_country, $rowusers['id'] ]);
        // Redirect to settings page
        header('Location: myaccount.php?tab=settings&success=1');
        exit;           
    }
}

// Count the number of items in the users wishlist
$stmt = $pdo->prepare('SELECT COUNT(*) FROM shop_wishlist WHERE account_id = ?');
$stmt->execute([ $rowusers['id'] ]);
$wishlist_count = $stmt->fetchColumn(); 

// If the user is viewing their wishlist
if ($tab == 'wishlist') {
    // Select the users wishlist items
    $stmt = $pdo->prepare('SELECT p.id, p.title, p.price, p.rrp, p.url_slug, (SELECT m.full_path FROM shop_product_media_map pm JOIN shop_product_media m ON m.id = pm.media_id WHERE pm.product_id = p.id ORDER BY pm.position ASC LIMIT 1) AS img FROM shop_wishlist w JOIN shop_products p ON p.id = w.product_id WHERE w.account_id = ?');
    $stmt->execute([ $rowusers['id'] ]);
    $wishlist = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!-- My Account -->
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header accent-background">
                    <i class="bi bi-person" aria-hidden="true"></i> My Account
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <!-- Account Navigation -->
                            <div class="list-group mb-4">
                                <a href="myaccount.php" class="list-group-item list-group-item-action <?=$tab == 'orders' ? 'active' : ''?>">
                                    <i class="bi bi-receipt" aria-hidden="true"></i> Orders
                                </a>
                                <a href="myaccount.php?tab=downloads" class="list-group-item list-group-item-action <?=$tab == 'downloads' ? 'active' : ''?>">
                                    <i class="bi bi-download" aria-hidden="true"></i> Downloads (<?=count($downloads)?>)
                                </a>
                                <a href="myaccount.php?tab=wishlist" class="list-group-item list-group-item-action <?=$tab == 'wishlist' ? 'active' : ''?>">
                                    <i class="bi bi-heart" aria-hidden="true"></i> Wishlist (<?=$wishlist_count?>)
                                </a>
                                <a href="myaccount.php?tab=settings" class="list-group-item list-group-item-action <?=$tab == 'settings' ? 'active' : ''?>">
                                    <i class="bi bi-gear" aria-hidden="true"></i> Settings
                                </a>
                            </div>
                        </div>

                        <div class="col-md-9">
                            <!-- Welcome Message -->
                            <div class="mb-4">
                                <h4>Welcome back, <?=htmlspecialchars($account['first_name'] ? $account['first_name'] : explode('@', $account['email'])[0], ENT_QUOTES)?>!</h4>
                                <p class="text-muted">Manage your orders, downloads, wishlist, and account settings.</p>
                            </div>

                            <?php if ($tab == 'orders'): ?>
                                <!-- Orders Tab -->
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5><i class="bi bi-receipt" aria-hidden="true"></i> Order History</h5>
                                    <div class="btn-group">
                                        <select class="form-select" onchange="window.location='myaccount.php?date='+this.value">
                                            <option value="all" <?=$date == 'all' ? 'selected' : ''?>>All Time</option>
                                            <option value="last30days" <?=$date == 'last30days' ? 'selected' : ''?>>Last 30 Days</option>
                                            <option value="last6months" <?=$date == 'last6months' ? 'selected' : ''?>>Last 6 Months</option>
                                        </select>
                                    </div>
                                </div>

                                <?php if ($transactions): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th>Date</th>
                                                    <th>Status</th>
                                                    <th>Total</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($transactions as $transaction): ?>
                                                    <tr>
                                                        <td>#<?=$transaction['txn_id']?></td>
                                                        <td><?=date('M j, Y', strtotime($transaction['created']))?></td>
                                                        <td>
                                                            <?php
                                                            $status_class = 'secondary';
                                                            if ($transaction['payment_status'] == 'Completed') $status_class = 'success';
                                                            elseif ($transaction['payment_status'] == 'Pending') $status_class = 'warning';
                                                            elseif ($transaction['payment_status'] == 'Cancelled') $status_class = 'danger';
                                                            ?>
                                                            <span class="badge bg-<?=$status_class?>"><?=$transaction['payment_status']?></span>
                                                        </td>
                                                        <td><?=currency_code?><?=num_format($transaction['payment_amount'], 2)?></td>
                                                        <td>
                                                            <button class="btn btn-outline-primary btn-sm" onclick="viewOrder('<?=$transaction['txn_id']?>')">
                                                                <i class="bi bi-eye" aria-hidden="true"></i> View
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="bi bi-receipt fa-3x text-muted mb-3" aria-hidden="true"></i>
                                        <h5>No orders found</h5>
                                        <p class="text-muted">You haven't placed any orders yet.</p>
                                        <a href="products.php" class="btn btn-primary">Start Shopping</a>
                                    </div>
                                <?php endif; ?>

                            <?php elseif ($tab == 'wishlist'): ?>
                                <!-- Wishlist Tab -->
                                <h5><i class="bi bi-heart" aria-hidden="true"></i> My Wishlist</h5>
                                
                                <?php if (isset($wishlist) && $wishlist): ?>
                                    <div class="row">
                                        <?php foreach ($wishlist as $product): ?>
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="card">
                                                    <?php if ($product['img'] && file_exists($product['img'])): ?>
                                                        <img src="<?=$product['img']?>" class="card-img-top" style="height: 150px; object-fit: cover;">
                                                    <?php endif; ?>
                                                    <div class="card-body">
                                                        <h6 class="card-title">
                                                            <a href="product.php?id=<?=$product['id']?>" class="text-decoration-none">
                                                                <?=htmlspecialchars($product['title'], ENT_QUOTES)?>
                                                            </a>
                                                        </h6>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="text-primary fw-bold"><?=currency_code?><?=num_format($product['price'], 2)?></span>
                                                            <a href="product.php?id=<?=$product['id']?>" class="btn btn-outline-primary btn-sm">View</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="bi bi-heart fa-3x text-muted mb-3" aria-hidden="true"></i>
                                        <h5>Your wishlist is empty</h5>
                                        <p class="text-muted">Save products to your wishlist to keep track of items you love.</p>
                                        <a href="products.php" class="btn btn-primary">Browse Products</a>
                                    </div>
                                <?php endif; ?>

                            <?php elseif ($tab == 'settings'): ?>
                                <!-- Settings Tab -->
                                <h5><i class="bi bi-gear" aria-hidden="true"></i> Account Settings</h5>
                                
                                <?php if (isset($_GET['success'])): ?>
                                    <div class="alert alert-success">Account settings updated successfully!</div>
                                <?php endif; ?>
                                
                                <?php if ($error): ?>
                                    <div class="alert alert-danger"><?=$error?></div>
                                <?php endif; ?>

                                <form action="myaccount.php?tab=settings" method="post">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">First Name</label>
                                            <input type="text" name="first_name" class="form-control" value="<?=htmlspecialchars($account['first_name'] ?? '', ENT_QUOTES)?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Last Name</label>
                                            <input type="text" name="last_name" class="form-control" value="<?=htmlspecialchars($account['last_name'] ?? '', ENT_QUOTES)?>">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Email Address</label>
                                        <input type="email" name="email" class="form-control" value="<?=htmlspecialchars($account['email'], ENT_QUOTES)?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">New Password <small class="text-muted">(leave blank to keep current)</small></label>
                                        <input type="password" name="password" class="form-control">
                                    </div>

                                    <h6>Address Information</h6>
                                    <hr>

                                    <div class="mb-3">
                                        <label class="form-label">Street Address</label>
                                        <input type="text" name="address_street" class="form-control" value="<?=htmlspecialchars($account['address_street'] ?? '', ENT_QUOTES)?>">
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">City</label>
                                            <input type="text" name="address_city" class="form-control" value="<?=htmlspecialchars($account['address_city'] ?? '', ENT_QUOTES)?>">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">State</label>
                                            <input type="text" name="address_state" class="form-control" value="<?=htmlspecialchars($account['address_state'] ?? '', ENT_QUOTES)?>">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">ZIP Code</label>
                                            <input type="text" name="address_zip" class="form-control" value="<?=htmlspecialchars($account['address_zip'] ?? '', ENT_QUOTES)?>">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Country</label>
                                        <select name="address_country" class="form-select">
                                            <option value="United States" <?=($account['address_country'] ?? '') == 'United States' ? 'selected' : ''?>>United States</option>
                                            <option value="Canada" <?=($account['address_country'] ?? '') == 'Canada' ? 'selected' : ''?>>Canada</option>
                                            <option value="United Kingdom" <?=($account['address_country'] ?? '') == 'United Kingdom' ? 'selected' : ''?>>United Kingdom</option>
                                        </select>
                                    </div>

                                    <button type="submit" name="save_details" class="btn btn-primary">
                                        <i class="bi bi-save" aria-hidden="true"></i> Save Changes
                                    </button>
                                </form>

                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function viewOrder(orderId) {
    // For now, just show an alert. Later this could open a modal or navigate to order details
    alert('Order details for #' + orderId + ' - This feature will be implemented in the next phase.');
}
</script>

<?php include_once "assets/includes/footer.php"; ?>
