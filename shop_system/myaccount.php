<?php
// Prevent direct access to file
defined('shoppingcart') or exit;

// Check if user is logged in using main site authentication
global $logged_in, $rowusers;

if (!$logged_in) {
    // User not logged in, redirect to main site auth
    header('Location: ../auth.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Get user account information from main site
$account = $rowusers;
if (!$account) {
    // Account data not available, redirect to auth
    header('Location: ../auth.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Determine the current tab page
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'orders';
// User is logged in (already verified above)
// Determine the current date filter
$date = isset($_GET['date']) ? $_GET['date'] : 'all';
$date_sql = '';
if ($date == 'last30days') {
    $date_sql = 'AND created >= DATE_SUB("' . date('Y-m-d') . '", INTERVAL 30 DAY)';
} else if ($date == 'last6months') {
    $date_sql = 'AND created >= DATE_SUB("' . date('Y-m-d') . '", INTERVAL 6 MONTH)';
} else if (substr($date, 0, 4) == 'year' && is_numeric(substr($date, 4))) {
    $date_sql = 'AND YEAR(created) = :yr';
}
// Determine the current status filter
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$status_sql = '';
if ($status != 'all') {
    $status_sql = 'AND payment_status = :status';
}
    // Select all the users transations, which will appear under "My Orders"
    $stmt = $pdo->prepare('SELECT * FROM shop_transactions  WHERE account_id = :account_id ' . $date_sql . ' ' . $status_sql . ' ORDER BY created DESC');
    $params = [ 'account_id' => $account['id'] ];
    if (substr($date, 0, 4) == 'year' && is_numeric(substr($date, 4))) {
        $params['yr'] = substr($date, 4);
    }
    if ($status != 'all') {
        $params['status'] = $status;
    }
    $stmt->execute($params);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Select all the users transations, which will appear under "My Orders"
    $stmt = $pdo->prepare('SELECT
        p.title,
        p.id AS product_id,
        t.txn_id,
        t.payment_status,
        t.created AS transaction_date,
        ti.item_price AS price,
        ti.item_quantity AS quantity,
        ti.item_id,
        ti.item_options,
        (SELECT m.full_path FROM shop_product_media_map pm JOIN shop_product_media m ON m.id = pm.media_id WHERE pm.product_id = p.id ORDER BY pm.position ASC LIMIT 1) AS img 
        FROM shop_transactions t
        JOIN shop_transaction_items ti ON ti.txn_id = t.txn_id
        JOIN accounts a ON a.id = t.account_id
        JOIN shop_products p ON p.id = ti.item_id
        WHERE t.account_id = ?
        ORDER BY t.created DESC');
    $stmt->execute([ $account['id'] ]);
    $transactions_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Retrieve the digital downloads
    $transactions_ids = array_column($transactions_items, 'product_id');
    $downloads = [];
    if ($transactions_ids) {
        $stmt = $pdo->prepare('SELECT product_id, file_path, id FROM shop_product_downloads WHERE product_id IN (' . trim(str_repeat('?,',count($transactions_ids)),',') . ') ORDER BY position ASC');
        $stmt->execute($transactions_ids);
        $downloads = $stmt->fetchAll(PDO::FETCH_GROUP);
    }
    // Retrieve account details (we already have this in $account = $rowusers from main site auth)
    // The account details are already available from the main site authentication
    // Update settings
    if (isset($_POST['save_details'], $_POST['email'], $_POST['password'])) {
        // Assign and validate input data
        $first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
        $last_name = isset($_POST['last_name']) ? $_POST['last_name'] : '';
        $address_street = isset($_POST['address_street']) ? $_POST['address_street'] : '';
        $address_city = isset($_POST['address_city']) ? $_POST['address_city'] : '';
        $address_state = isset($_POST['address_state']) ? $_POST['address_state'] : '';
        $address_zip = isset($_POST['address_zip']) ? $_POST['address_zip'] : '';
        $address_country = isset($_POST['address_country']) ? $_POST['address_country'] : '';
        // Check if account exists with captured email
        $stmt = $pdo->prepare('SELECT * FROM accounts WHERE email = ?');
        $stmt->execute([ $_POST['email'] ]);
        // Validation
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
            header('Location: ' . url('myaccount.php?tab=settings'));
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
<?=template_header('My Account')?>

<div class="myaccount content-wrapper">

    <h1 class="page-title">My Account</h1>
    <p>Welcome back, <?=htmlspecialchars($account['first_name'] ? $account['first_name'] : explode('@', $account['email'])[0])?>!</p>

    <div class="menu">

        <h2>Menu</h2>
        
        <div class="menu-items">
            <a href="<?=url('myaccount.php')?>">Orders</a>
            <a href="<?=url('myaccount.php?tab=downloads')?>">Downloads (<?=count($downloads)?>)</a>
            <a href="<?=url('myaccount.php?tab=wishlist')?>">Wishlist (<?=$wishlist_count?>)</a>
            <a href="<?=url('myaccount.php?tab=settings')?>">Settings</a>
        </div>

    </div>

    <?php if ($tab == 'orders'): ?>
    <div class="myorders">

        <h2>My Orders</h2>

        <form action="<?=url('index.php?page=myaccount')?>" method="get" class="form pad-top-2">
            <?php if (!rewrite_url): ?>
            <input type="hidden" name="page" value="myaccount">
            <input type="hidden" name="tab" value="orders">
            <?php endif; ?>
            <label class="form-select mar-right-2" for="status">
                Status:
                <select name="status" id="status" onchange="this.form.submit()">
                    <option value="all"<?=($status == 'all' ? ' selected' : '')?>>All Orders</option>
                    <option value="Completed"<?=$status=='Completed'?' selected':''?>>Completed</option>
                    <option value="Pending"<?=$status=='Pending'?' selected':''?>>Pending</option>
                    <option value="Failed"<?=$status=='Failed'?' selected':''?>>Failed</option>
                    <option value="Cancelled"<?=$status=='Cancelled'?' selected':''?>>Cancelled</option>
                    <option value="Refunded"<?=$status=='Refunded'?' selected':''?>>Refunded</option>
                    <option value="Shipped"<?=$status=='Shipped'?' selected':''?>>Shipped</option>
                    <option value="Subscribed"<?=$status=='Subscribed'?' selected':''?>>Subscribed</option>
                    <option value="Unsubscribed"<?=$status=='Unsubscribed'?' selected':''?>>Unsubscribed</option>
                </select>
            </label>
            <label class="form-select" for="date">
                Date:
                <select name="date" id="date" onchange="this.form.submit()">
                    <option value="all"<?=($date == 'all' ? ' selected' : '')?>>All Time</option>
                    <option value="last30days"<?=($date == 'last30days' ? ' selected' : '')?>>Last 30 Days</option>
                    <option value="last6months"<?=($date == 'last6months' ? ' selected' : '')?>>Last 6 Months</option>
                    <option value="year<?=date('Y')?>"<?=($date == 'year' . date('Y') ? ' selected' : '')?>><?=date('Y')?></option>
                    <option value="year<?=date('Y')-1?>"<?=($date == 'year' . (date('Y')-1) ? ' selected' : '')?>><?=date('Y')-1?></option>
                    <option value="year<?=date('Y')-2?>"<?=($date == 'year' . (date('Y')-2) ? ' selected' : '')?>><?=date('Y')-2?></option>
                </select>
            </label>
        </form>

        <?php if (empty($transactions)): ?>
        <p class="pad-y-5">You have no orders.</p>
        <?php endif; ?>

        <?php foreach ($transactions as $transaction): ?>
        <div class="order">
            <div class="order-header">
                <div>
                    <div><span>Order</span># <?=$transaction['id']?></div>
                    <div class="rhide"><span>Date</span><?=date('F j, Y', strtotime($transaction['created']))?></div>
                    <div><span>Status</span><?=$transaction['payment_status']?></div>
                </div>
                <div>
                    <div class="rhide"><span>Shipping</span><?=currency_code?><?=num_format($transaction['shipping_amount'],2)?></div>
                    <div><span>Total</span><?=currency_code?><?=num_format($transaction['payment_amount'],2)?></div>
                </div>
            </div>
            <div class="order-items">
                <table>
                    <tbody>
                        <?php foreach ($transactions_items as $transaction_item): ?>
                        <?php if ($transaction_item['txn_id'] != $transaction['txn_id']) continue; ?>
                        <tr>
                            <td class="img">
                                <?php if (!empty($transaction_item['img']) && file_exists($transaction_item['img'])): ?>
                                <img src="<?=base_url?><?=$transaction_item['img']?>" width="50" height="50" alt="<?=$transaction_item['title']?>">
                                <?php endif; ?>
                            </td>
                            <td class="name">
                                <?=$transaction_item['quantity']?> x <?=$transaction_item['title']?><br>
                                <?php if ($transaction_item['item_options']): ?>
                                <span class="options"><?=str_replace(',', '<br>', htmlspecialchars($transaction_item['item_options'], ENT_QUOTES))?></span>
                                <?php endif; ?>
                            </td>
                            <td class="price"><?=currency_code?><?=num_format($transaction_item['price'] * $transaction_item['quantity'],2)?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>                
            </div>
        </div>
        <?php endforeach; ?>

    </div>
    <?php elseif ($tab == 'downloads'): ?>
    <div class="mydownloads">

        <h2>My Downloads</h2>

        <?php if (empty($downloads)): ?>
        <p class="pad-y-5">You have no digital downloads.</p>
        <?php endif; ?>

        <?php if ($downloads): ?>
        <table>
            <thead>
                <tr>
                    <td colspan="2">Product</td>
                    <td></td>
                </tr>
            </thead>
            <tbody>
                <?php $download_products_ids = []; ?>
                <?php foreach ($transactions_items as $item): ?>
                <?php if (isset($downloads[$item['product_id']]) && !in_array($item['product_id'], $download_products_ids)): ?>
                <tr>
                    <td class="img">
                        <?php if (!empty($item['img']) && file_exists($item['img'])): ?>
                        <img src="<?=base_url?><?=$item['img']?>" width="50" height="50" alt="<?=$item['title']?>">
                        <?php endif; ?>
                    </td>
                    <td class="name"><?=$item['title']?></td>
                    <td>
                        <?php foreach ($downloads[$item['product_id']] as $download): ?>
                        <a href="<?=url('index.php?page=download&id=' . md5($item['txn_id'] . $download['id']))?>" download>
                            <div class="icon">
                                <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M5,20H19V18H5M19,9H15V3H9V9H5L12,16L19,9Z" /></svg>
                            </div>
                            <?=basename($download['file_path'])?>
                        </a>
                        <?php endforeach; ?>
                    </td>
                </tr>
                <?php $download_products_ids[] = $item['product_id']; ?>
                <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

    </div>
    <?php elseif ($tab == 'wishlist'): ?>
    <div class="wishlist">

        <h2>Wishlist</h2>

        <?php if (empty($wishlist)): ?>
        <p class="pad-y-5">You have no items in your wishlist.</p>
        <?php endif; ?>

        <div class="products">
            <div class="products-wrapper">
                <?php foreach ($wishlist as $product): ?>
                <a href="<?=url('index.php?page=product&id=' . ($product['url_slug'] ? $product['url_slug']  : $product['id']))?>" class="product">
                    <?php if (!empty($product['img']) && file_exists($product['img'])): ?>
                    <div class="img small">
                        <img src="<?=base_url?><?=$product['img']?>" width="150" height="150" alt="<?=$product['title']?>">
                    </div>
                    <?php endif; ?>
                    <span class="name"><?=$product['title']?></span>
                    <span class="price">
                        <?=currency_code?><?=num_format($product['price'],2)?>
                        <?php if ($product['rrp'] > 0): ?>
                        <span class="rrp"><?=currency_code?><?=num_format($product['rrp'],2)?></span>
                        <?php endif; ?>
                    </span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
    <?php elseif ($tab == 'settings'): ?>
    <div class="settings">

        <h2>Settings</h2>

        <form action="<?=url('index.php?page=myaccount&tab=settings')?>" method="post" class="form">

            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" placeholder="Email" value="<?=htmlspecialchars($account['email'], ENT_QUOTES)?>" class="form-input expand" required>

            <label for="password" class="form-label">New Password</label>
            <input type="password" id="password" name="password" placeholder="New Password" value="" autocomplete="new-password" class="form-input expand">

            <div class="form-group">
                <div class="col pad-right-2">
                    <label for="first_name" class="form-label">First Name</label>
                    <input id="first_name" type="text" name="first_name" placeholder="Joe" value="<?=htmlspecialchars($account['first_name'], ENT_QUOTES)?>" class="form-input expand">
                </div>
                <div class="col pad-left-2">
                    <label for="last_name" class="form-label">Last Name</label>
                     <input id="last_name" type="text" name="last_name" placeholder="Bloggs" value="<?=htmlspecialchars($account['last_name'], ENT_QUOTES)?>" class="form-input expand">
                </div>
            </div>

            <label for="address_street" class="form-label">Address Street</label>
            <input id="address_street" type="text" name="address_street" placeholder="24 High Street" value="<?=htmlspecialchars($account['address_street'], ENT_QUOTES)?>" class="form-input expand">

            <label for="address_city" class="form-label">Address City</label>
            <input id="address_city" type="text" name="address_city" placeholder="New York" value="<?=htmlspecialchars($account['address_city'], ENT_QUOTES)?>" class="form-input expand">

            <div class="form-group">
                <div class="col pad-right-2">
                    <label for="address_state" class="form-label">Address State</label>
                    <input id="address_state" type="text" name="address_state" placeholder="NY" value="<?=htmlspecialchars($account['address_state'], ENT_QUOTES)?>" class="form-input expand">
                </div>
                <div class="col pad-left-2">
                    <label for="address_zip" class="form-label">Address Zip</label>
                    <input id="address_zip" type="text" name="address_zip" placeholder="10001" value="<?=htmlspecialchars($account['address_zip'], ENT_QUOTES)?>" class="form-input expand">
                </div>
            </div>

            <label for="address_country" class="form-label">Country</label>
            <select id="address_country" name="address_country" required class="form-input expand">
                <?php foreach(get_countries() as $country): ?>
                <option value="<?=$country?>"<?=$country==$account['address_country']?' selected':''?>><?=$country?></option>
                <?php endforeach; ?>
            </select>

            <button name="save_details" type="submit" class="btn">Save</button>

            <?php if ($error): ?>
            <p class="error pad-top-2"><?=$error?></p>
            <?php endif; ?>

        </form>

    </div>

    <?php endif; ?>

</div>

<?=template_footer()?>