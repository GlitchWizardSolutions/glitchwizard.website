<?php
// Check if being called from admin index.php or directly
if (!defined('shoppingcart_admin')) {
    // Include admin authentication directly
    include '../assets/includes/main.php';
    include '../../shop_system/functions.php';
    
    // Get shop system totals if not already set
    if (!isset($orders_total)) {
        try {
            $orders_total = $pdo->query('SELECT COUNT(*) AS total FROM shop_transactions')->fetchColumn();
        } catch (Exception $e) {
            $orders_total = 0;
        }
    }
}

// Current date in MySQL DATETIME format
$date = date('Y-m-d H:i:s');
// SQL query that will get all orders and sort by the date created
$orders = $pdo->query('SELECT t.*, COUNT(ti.id) AS total_products FROM shop_transactions t JOIN shop_transaction_items ti ON ti.txn_id = t.txn_id WHERE cast(t.created as DATE) = cast("' . $date . '" as DATE) GROUP BY t.id, t.txn_id, t.payment_amount, t.payment_status, t.created, t.payer_email, t.first_name, t.last_name, t.address_street, t.address_city, t.address_state, t.address_zip, t.address_country, t.account_id, t.payment_method, t.discount_code, t.shipping_method, t.shipping_amount, t.tax_amount ORDER BY t.created DESC')->fetchAll(PDO::FETCH_ASSOC);
// SQL query that will get all accounts created today
$accounts = $pdo->query('SELECT a.*, (SELECT COUNT(*) FROM shop_transactions t WHERE t.account_id = a.id) AS orders FROM accounts a WHERE cast(registered as DATE) = cast("' . $date . '" as DATE)')->fetchAll(PDO::FETCH_ASSOC);
// Get the orders statistics
$order_stats = $pdo->query('SELECT SUM(payment_amount) AS earnings FROM shop_transactions WHERE (payment_status = "Completed" OR payment_status = "Subscribed") AND cast(created as DATE) = cast("' . $date . '" as DATE)')->fetch(PDO::FETCH_ASSOC);
// Get the total number of accounts
$total_accounts = $pdo->query('SELECT COUNT(*) AS total FROM accounts')->fetchColumn();
// Get the total number of products
$total_products = $pdo->query('SELECT COUNT(*) AS total FROM shop_products')->fetchColumn();
// Get the top selling products
$top_products = $pdo->query('SELECT p.title, p.sku, SUM(ti.item_quantity) AS total_sales, SUM(ti.item_quantity * ti.item_price) AS total_earnings, (SELECT m.full_path FROM shop_product_media_map pm JOIN shop_product_media m ON m.id = pm.media_id WHERE pm.product_id = p.id ORDER BY pm.position ASC LIMIT 1) AS img FROM shop_products p JOIN shop_transaction_items ti ON ti.item_id = p.id GROUP BY p.id, p.title, p.sku ORDER BY total_sales DESC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
// Get sales analytics
if (isset($_GET['date_start'], $_GET['date_end'])) {
    $start = new DateTime($_GET['date_start']);
    $end = new DateTime($_GET['date_end']);
    if ($end <= $start) {
        $end = new DateTime($_GET['date_start']);
    }
} else {
    $start = new DateTime();         
    $start->modify('-6 days'); 
    $end = new DateTime();
}
$end->modify('+1 day');
$period = new DatePeriod($start, new DateInterval('P1D'), $end);
$allDates = [];
foreach ($period as $dt) {
    $allDates[$dt->format('Y-m-d')] = 0;
}
$sale_analytics_total_sales = 0;
$sale_analytics_total_earnings = 0;
$sale_results = $pdo->query('SELECT DATE(created) AS date, COUNT(*) AS sales, SUM(payment_amount) AS earnings FROM shop_transactions WHERE created BETWEEN "' . $start->format('Y-m-d 00:00:00') . '" AND "' . $end->format('Y-m-d 23:59:59') . '" GROUP BY DATE(created)')->fetchAll(PDO::FETCH_ASSOC);
foreach ($sale_results as $row) {
    $allDates[$row['date']] = (int)$row['sales'];
    $sale_analytics_total_sales += (int)$row['sales'];
    $sale_analytics_total_earnings += (float)$row['earnings'];
}
$stats = [];
foreach ($allDates as $date => $sales) {
    $stats[] = ['date' => $date, 'sales' => $sales];
}
$max_sales = max(array_column($stats, 'sales'));
$max_sales = $max_sales < 15 ? 15 : $max_sales;
$num_increments = 5;
$raw_step = $max_sales / $num_increments;
if ($raw_step <= 5) {
    $step = 5;
} elseif ($raw_step <= 10) {
    $step = 10;
} elseif ($raw_step <= 20) {
    $step = 10 * ceil($raw_step / 10);
} else {
    $step = 10 * ceil($raw_step / 10);
}
$increments = [];
for ($i = 0; $i <= $max_sales; $i += $step) {
    $increments[] = $i;
}
if (end($increments) < $max_sales) {
    $increments[] = end($increments) + $step;
}
?>
<?=template_admin_header('Shop Dashboard', 'shop', 'dashboard')?>

<div class="content-title" id="main-shop-dashboard" role="banner" aria-label="Shop Dashboard Header">
    <div class="title">
        <div class="icon">
            <i class="bi bi-cart" aria-hidden="true"></i>
        </div>
        <div class="txt">
            <h2>Shop Dashboard</h2>
            <p>View sales statistics, today's orders, and store analytics.</p>
        </div>
    </div>
</div>
<br>

<div class="dashboard">
    <div class="content-block stat">
        <div class="data">
            <h3>New Orders <span>today</span></h3>
            <p><?=num_format(count($orders))?></p>
        </div>
        <div class="icon">
            <i class="bi bi-cart" aria-hidden="true"></i>
        </div>    
        <div class="footer">
            <i class="bi bi-info-circle" aria-hidden="true"></i>
            Total orders for today
        </div>
    </div>

    <div class="content-block stat green">
        <div class="data">
            <h3>New Sales <span>today</span></h3>
            <p><?=currency_code?><?=num_format($order_stats['earnings'], 2)?></p>
        </div>
        <div class="icon">
            <i class="bi bi-currency-dollar" aria-hidden="true"></i>
        </div>    
        <div class="footer">
            <i class="bi bi-info-circle" aria-hidden="true"></i>
            Total earnings for today
        </div>
    </div>

    <div class="content-block stat cyan">
        <div class="data">
            <h3>Total Accounts</h3>
            <p><?=num_format($total_accounts)?></p>
        </div>
        <div class="icon">
            <i class="bi bi-people" aria-hidden="true"></i>
        </div>
        <div class="footer">
            <i class="bi bi-info-circle" aria-hidden="true"></i>
            Total accounts
        </div>
    </div>

    <div class="content-block stat red">
        <div class="data">
            <h3>Total Products</h3>
            <p><?=num_format($total_products)?></p>
        </div>
        <div class="icon">
            <i class="bi bi-box-seam" aria-hidden="true"></i>
        </div>    
        <div class="footer">
            <i class="bi bi-info-circle" aria-hidden="true"></i>
            Total products
        </div>
    </div>
</div>

<div class="content-block-wrapper" style="padding:0;">
    <div class="content-block" style="min-width:65%">
        <div class="block-header">
            <div class="content-left">
                <div class="icon">
                    <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M3,22V8H7V22H3M10,22V2H14V22H10M17,22V14H21V22H17Z" /></svg>
                </div>
                Sales Analytics
            </div>
            <div class="content-right">
                <form action="index.php?page=dashboard" method="get" class="date-range">
                    <input type="hidden" name="page" value="dashboard">
                    <input type="date" name="date_start" value="<?=$start->format('Y-m-d')?>" onchange="this.form.submit()">
                    <span>to</span>
                    <input type="date" name="date_end" value="<?=$end->format('Y-m-d')?>" onchange="this.form.submit()">
                </form>
            </div>
        </div>
        <div class="sales-chart bar-chart">
            <div class="chart-stats">
                <div class="data">
                    <h3>Orders</h3>
                    <p><?=num_format($sale_analytics_total_sales)?>
                </div>
                <div class="data">
                    <h3>Earnings</h3>
                    <p>&dollar;<?=num_format($sale_analytics_total_earnings, 2)?>
                </div>
            </div>
            <div class="chart-wrapper">
                <div class="chart-scale">
                    <?php foreach ($increments as $inc): ?>
                    <?php $pos = $inc ? ($inc / end($increments)) * 100 : 0; ?>
                    <?php if ($pos == 0) continue; ?>
                    <div style="bottom:calc(<?=$pos?>% - 19px)"><?=$inc?></div>
                    <?php endforeach; ?>
                </div>
                <div class="chart-container">
                    <?php foreach ($increments as $inc): ?>
                    <?php $pos = $inc ? ($inc / end($increments)) * 100 : 0; ?>
                    <?php if ($pos == 0) continue; ?>
                    <div class="chart-line" style="bottom:<?=$pos?>%"></div>
                    <?php endforeach; ?>
                    <?php foreach ($stats as $day): ?>
                    <?php $height_percentage = $day['sales'] ? ($day['sales'] / end($increments)) * 100 : 0; ?>
                    <div class="chart-bar" style="height:<?=$height_percentage?>%;">
                        <?php if ($day['sales']): ?>
                        <span><?=date('M j', strtotime($day['date']))?>: <?=$day['sales']?> order<?=$day['sales']==1?'':'s'?></span>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php if (count($stats) <= 7): ?>
            <div class="chart-wrapper">
                <div class="chart-labels">
                    <?php foreach ($stats as $day): ?>
                    <div><?=date('M j', strtotime($day['date']))?></div>
                    <?php endforeach; ?>
                </div>
            </div>  
            <?php endif; ?>
        </div>
    </div>
    <div class="content-block">
        <div class="block-header" style="margin-bottom:0">
            <div class="content-left">
                <div class="icon">
                    <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M16,6L18.29,8.29L13.41,13.17L9.41,9.17L2,16.59L3.41,18L9.41,12L13.41,16L19.71,9.71L22,12V6H16Z" /></svg>
                </div>
                Top Selling 
            </div>
        </div>
        <div class="top-sellers">
            <div class="table">
                <table>
                    <thead>
                        <tr>
                            <td colspan="2">Product</td>
                            <td>Sales</td>
                            <td class="right">Earnings</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($top_products)): ?>
                        <tr>
                            <td colspan="20" class="no-results">There are no top selling products.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($top_products as $product): ?>
                        <tr>
                            <td class="img">
                                <div class="profile-img">
                                    <?php if (!empty($product['img'])): ?>
                                    <?php if (file_exists('../'.$product['img'])): ?>
                                    <img src="../<?=$product['img']?>" width="32" height="32" alt="<?=$product['img']?>">
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="title-caption">
                                <?=$product['title']?>
                                <?php if ($product['sku']): ?>
                                <span><?=$product['sku']?></span>
                                <?php endif; ?>
                            </td>
                            <td><span class="grey small"><?=$product['total_sales']?></span></td>
                            <td class="strong right"><?=currency_code?><?=num_format($product['total_earnings'], 2)?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="content-title" style="margin-top:40px">
    <div class="title">
        <div class="icon alt">
            <i class="bi bi-display" aria-hidden="true"></i>
        </div>
        <div class="txt">
            <h2>Today's Orders</h2>
            <p>List of all orders placed today</p>
        </div>
    </div>
</div>

<div class="content-block no-pad">
    <div class="table">
        <table>
            <thead>
                <tr>
                    <td colspan="2">Customer</td>
                    <td class="responsive-hidden">Email</td>
                    <td class="responsive-hidden">Products</td>
                    <td>Total</td>
                    <td class="responsive-hidden">Method</td>
                    <td class="responsive-hidden">Status</td>
                    <td class="responsive-hidden">Date</td>
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="20" class="no-results">There are no recent orders.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td class="img">
                        <div class="profile-img">
                            <span style="background-color:<?=color_from_string($order['first_name'])?>"><?=strtoupper(substr($order['first_name'], 0, 1))?></span>
                        </div>
                    </td>
                    <td><a href="#" class="view-customer-details link1" data-transaction-id="<?=$order['id']?>"><?=htmlspecialchars($order['first_name'], ENT_QUOTES)?> <?=htmlspecialchars($order['last_name'], ENT_QUOTES)?></a></td>
                    <td class="responsive-hidden alt"><?=htmlspecialchars($order['payer_email'], ENT_QUOTES)?></td>
                    <td class="responsive-hidden"><span class="grey small"><?=$order['total_products']?></span></td>
                    <td class="strong"><?=currency_code?><?=num_format($order['payment_amount'], 2)?></td>
                    <td class="responsive-hidden alt"><span class="grey"><?=$order['payment_method']?></span></td>
                    <td class="responsive-hidden"><span class="<?=str_replace(['completed','pending','cancelled','reversed','failed','refunded','shipped','unsubscribed','subscribed'],['green','orange','red','red','red','red','green','red','blue'], strtolower($order['payment_status']))?>"><?=$order['payment_status']?></span></td>
                    <td class="responsive-hidden alt"><?=date('F j, Y', strtotime($order['created']))?></td>
                    <td class="actions">
                        <div class="table-dropdown">
                            <i class="bi bi-three-dots-vertical" aria-hidden="true"></i>
                            <div class="table-dropdown-items">
                                <a href="index.php?page=order&id=<?=$order['id']?>">
                                    <span class="icon"><i class="bi bi-eye" aria-hidden="true"></i></span>
                                    View
                                </a>
                                <a href="index.php?page=order_manage&id=<?=$order['id']?>">
                                    <span class="icon"><i class="bi bi-pencil" aria-hidden="true"></i></span>
                                    Edit
                                </a>
                                <a class="red" href="index.php?page=orders&delete=<?=$order['id']?>" onclick="return confirm('Are you sure you want to delete this order?')">
                                    <span class="icon"><i class="bi bi-trash" aria-hidden="true"></i></span>    
                                    Delete
                                </a>
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

<div class="content-title" style="margin-top:40px">
    <div class="title">
        <div class="icon alt">
            <i class="bi bi-person-plus" aria-hidden="true"></i>
        </div>
        <div class="txt">
            <h2>New Accounts</h2>
            <p>Accounts created today</p>
        </div>
    </div>
</div>

<div class="content-block no-pad">
    <div class="table">
        <table>
            <thead>
                <tr>
                    <td colspan="2">Name</td>
                    <td class="responsive-hidden">Email</td>
                    <td class="responsive-hidden">Role</td>
                    <td class="responsive-hidden"># Orders</td>
                    <td class="responsive-hidden">Registered Date</td>
                    <td class="align-center">Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (!$accounts): ?>
                <tr>
                    <td colspan="20" class="no-results">There are no new accounts.</td>
                </tr>
                <?php endif; ?>
                <?php foreach ($accounts as $account): ?>
                <tr>
                    <td class="img">
                        <div class="profile-img">
                            <span style="background-color:<?=color_from_string($account['first_name'])?>"><?=strtoupper(substr($account['first_name'], 0, 1))?></span>
                        </div>
                    </td>
                    <td><?=htmlspecialchars($account['first_name'] . ' ' . $account['last_name'], ENT_QUOTES)?></td>
                    <td class="responsive-hidden alt"><?=htmlspecialchars($account['email'], ENT_QUOTES)?></td>
                    <td class="responsive-hidden"><span class="<?=str_replace(['Admin', 'Member'], ['red', 'blue'], $account['role'])?> small"><?=$account['role']?></span></td>
                    <td class="responsive-hidden"><a href="index.php?page=orders&account_id=<?=$account['id']?>" class="link1"><?=num_format($account['orders'])?></a></td>
                    <td class="responsive-hidden alt"><?=date('Y-m-d H:ia', strtotime($account['registered']))?></td>
                    <td class="actions">
                        <div class="table-dropdown">
                            <i class="bi bi-three-dots-vertical" aria-hidden="true"></i>
                            <div class="table-dropdown-items">
                                <a href="index.php?page=account&id=<?=$account['id']?>">
                                    <span class="icon"><i class="bi bi-pencil" aria-hidden="true"></i></span>
                                    Edit
                                </a>
                                <a class="red" href="index.php?page=accounts&delete=<?=$account['id']?>" onclick="return confirm('Are you sure you want to delete this account?')">
                                    <span class="icon"><i class="bi bi-trash" aria-hidden="true"></i></span>    
                                    Delete
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?=template_admin_footer()?>