<?php
/*
FILE NAME  : shop_dash.php
LOCATION   : admin/shop_system/shop_dash.php
DESCRIPTION: Shop system dashboard with configuration status
FUNCTION   : Main dashboard for shop management with settings integration
CREATED    : August 2025 - Shop Integration Step 3
*/

// Include admin authentication and dependencies
include '../assets/includes/main.php';
include '../../assets/includes/settings/shop_settings.php';
include '../../shop_system/functions.php';

// Get shop configuration status
$config_status = getShopConfigStatus();

// Get shop system statistics
try {
    $orders_total = $pdo->query('SELECT COUNT(*) FROM shop_transactions')->fetchColumn();
    $orders_today = $pdo->query('SELECT COUNT(*) FROM shop_transactions WHERE DATE(created) = CURDATE()')->fetchColumn();
    $products_total = $pdo->query('SELECT COUNT(*) FROM shop_products')->fetchColumn();
    $categories_total = $pdo->query('SELECT COUNT(*) FROM shop_categories')->fetchColumn();
    
    // Get today's earnings
    $earnings_today = $pdo->query('
        SELECT COALESCE(SUM(payment_amount), 0) 
        FROM shop_transactions 
        WHERE (payment_status = "Completed" OR payment_status = "Subscribed") 
        AND DATE(created) = CURDATE()
    ')->fetchColumn();
    
    // Get recent orders
    $recent_orders = $pdo->query('
        SELECT t.*, COUNT(ti.id) AS total_products 
        FROM shop_transactions t 
        LEFT JOIN shop_transaction_items ti ON ti.txn_id = t.txn_id 
        GROUP BY t.id 
        ORDER BY t.created DESC 
        LIMIT 5
    ')->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $orders_total = $orders_today = $products_total = $categories_total = $earnings_today = 0;
    $recent_orders = [];
}

echo template_admin_header('Shop Dashboard', 'shop', 'dashboard');
?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-4 px-4 branding-settings-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-white">
                    <span class="header-icon"><i class="bi bi-shop" aria-hidden="true"></i></span>
                    Shop Dashboard
                </h6>
                <span class="text-white" style="font-size: 0.875rem;">E-commerce Management</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="container-fluid py-3 px-4">
<br>

<!-- Shop Dashboard Cards Grid -->
<div class="dashboard-apps">
    <!-- Shop Quick Actions Card -->
    <div class="app-card" role="region" aria-labelledby="shop-quick-actions-title">
        <div class="app-header events-header" role="banner" aria-labelledby="shop-quick-actions-title">
            <h3 id="shop-quick-actions-title">Quick Actions</h3>
            <i class="bi bi-lightning" aria-hidden="true"></i>
            <span class="badge" aria-label="Shop management actions">Manage</span>
        </div>
        <div class="app-body">
            <div class="quick-actions">
                <a href="products.php" class="quick-action primary">
                    <div class="action-icon">
                        <i class="bi bi-plus-circle" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Add Product</h4>
                        <small class="text-muted">Create new shop product</small>
                    </div>
                </a>
                <a href="orders.php" class="quick-action info">
                    <div class="action-icon">
                        <i class="bi bi-bag" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>View Orders</h4>
                        <small class="text-muted">Manage customer orders</small>
                    </div>
                </a>
                <a href="../settings/shop_settings.php" class="quick-action secondary">
                    <div class="action-icon">
                        <i class="bi bi-gear" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Shop Settings</h4>
                        <small class="text-muted">Configure shop options</small>
                    </div>
                </a>
                <a href="categories.php" class="quick-action warning">
                    <div class="action-icon">
                        <i class="bi bi-tags" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Categories</h4>
                        <small class="text-muted">Manage product categories</small>
                    </div>
                </a>
                <a href="coupons.php" class="quick-action success">
                    <div class="action-icon">
                        <i class="bi bi-percent" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Coupons</h4>
                        <small class="text-muted">Discount codes & promotions</small>
                    </div>
                </a>
                <a href="../index.php" class="quick-action purple">
                    <div class="action-icon">
                        <i class="bi bi-arrow-left" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Admin Dashboard</h4>
                        <small class="text-muted">Return to main admin</small>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Action Items Card -->
    <div class="app-card" role="region" aria-labelledby="shop-actions-title">
        <div class="app-header blog-header" role="banner" aria-labelledby="shop-actions-title">
            <h3 id="shop-actions-title">Action Items</h3>
            <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
            <?php 
            $action_items_count = 0;
            if ($config_status['overall_percent'] < 100) $action_items_count++;
            if ($orders_today > 0) $action_items_count++; // New orders to process
            ?>
            <span class="badge" aria-label="<?= $action_items_count ?> items requiring attention"><?= $action_items_count ?> items</span>
        </div>
        <div class="app-body">
            <?php if ($action_items_count > 0): ?>
                <div class="action-items">
                    <?php if ($config_status['overall_percent'] < 100): ?>
                        <a href="../settings/shop_settings.php" class="action-item warning">
                            <div class="action-icon">
                                <i class="bi bi-gear" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Complete Shop Setup</h4>
                                <small class="text-muted"><?= $config_status['overall_percent'] ?>% configured</small>
                            </div>
                            <div class="action-count"><?= 100 - $config_status['overall_percent'] ?>%</div>
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($orders_today > 0): ?>
                        <a href="orders.php?date=today" class="action-item info">
                            <div class="action-icon">
                                <i class="bi bi-bag-check" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>New Orders Today</h4>
                                <small class="text-muted">Orders need processing</small>
                            </div>
                            <div class="action-count"><?= $orders_today ?></div>
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="no-action-items">
                    <i class="bi bi-check-circle" aria-hidden="true"></i>
                    <p>All shop items are up to date! No pending actions.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Shop Statistics Card -->
    <div class="app-card" role="region" aria-labelledby="shop-stats-title">
        <div class="app-header accounts-header" role="banner" aria-labelledby="shop-stats-title">
            <h3 id="shop-stats-title">Shop Statistics</h3>
            <i class="bi bi-bar-chart" aria-hidden="true"></i>
            <span class="badge" aria-label="<?= number_format($orders_total) ?> total orders"><?= number_format($orders_total) ?> orders</span>
        </div>
        <div class="app-body">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($orders_total) ?></div>
                    <div class="stat-label">Total Orders</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($orders_today) ?></div>
                    <div class="stat-label">Orders Today</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($products_total) ?></div>
                    <div class="stat-label">Total Products</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($categories_total) ?></div>
                    <div class="stat-label">Categories</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= currency_code . number_format($earnings_today, 2) ?></div>
                    <div class="stat-label">Earnings Today</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= $config_status['overall_percent'] ?>%</div>
                    <div class="stat-label">Configuration Complete</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= $config_status['general'] ? 'Yes' : 'No' ?></div>
                    <div class="stat-label">General Settings</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= $config_status['paypal'] ? 'Yes' : 'No' ?></div>
                    <div class="stat-label">PayPal Configured</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Configuration Status Details -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Configuration Status</h6>
                <small class="text-muted"><?= $config_status['overall_percent'] ?>% Complete</small>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span>General Settings</span>
                        <span class="<?= $config_status['general'] ? 'text-success' : 'text-warning' ?>">
                            <i class="bi bi-<?= $config_status['general'] ? 'check' : 'exclamation-triangle' ?>"></i>
                            <?= $config_status['general'] ? 'Complete' : 'Pending' ?>
                        </span>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar <?= $config_status['general'] ? 'bg-success' : 'bg-warning' ?>" 
                             style="width: <?= $config_status['general'] ? '100' : '0' ?>%"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span>PayPal Setup</span>
                        <span class="<?= $config_status['paypal'] ? 'text-success' : 'text-warning' ?>">
                            <i class="bi bi-<?= $config_status['paypal'] ? 'check' : 'exclamation-triangle' ?>"></i>
                            <?= $config_status['paypal'] ? 'Complete' : 'Pending' ?>
                        </span>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar <?= $config_status['paypal'] ? 'bg-success' : 'bg-warning' ?>" 
                             style="width: <?= $config_status['paypal'] ? '100' : '0' ?>%"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span>Stripe Setup</span>
                        <span class="<?= $config_status['stripe'] ? 'text-success' : 'text-muted' ?>">
                            <i class="bi bi-<?= $config_status['stripe'] ? 'check' : 'dash' ?>"></i>
                            <?= $config_status['stripe'] ? 'Complete' : 'Optional' ?>
                        </span>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar <?= $config_status['stripe'] ? 'bg-success' : 'bg-light' ?>" 
                             style="width: <?= $config_status['stripe'] ? '100' : '0' ?>%"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span>Coinbase Setup</span>
                        <span class="<?= $config_status['coinbase'] ? 'text-success' : 'text-muted' ?>">
                            <i class="bi bi-<?= $config_status['coinbase'] ? 'check' : 'dash' ?>"></i>
                            <?= $config_status['coinbase'] ? 'Complete' : 'Optional' ?>
                        </span>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar <?= $config_status['coinbase'] ? 'bg-success' : 'bg-light' ?>" 
                             style="width: <?= $config_status['coinbase'] ? '100' : '0' ?>%"></div>
                    </div>
                </div>
                
                <div class="d-grid">
                    <a href="../settings/shop_settings.php" class="btn btn-outline-primary">
                        <i class="bi bi-gear me-1"></i>Configure Shop Settings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<?php if (!empty($recent_orders)): ?>
<div class="card">
    <div class="card-header">
        <h6 class="card-title mb-0">Recent Orders</h6>
        <small class="text-muted">Latest shop transactions</small>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="text-align:left;">Order ID</th>
                        <th style="text-align:left;">Customer</th>
                        <th style="text-align:center;">Products</th>
                        <th style="text-align:center;">Amount</th>
                        <th style="text-align:center;">Status</th>
                        <th style="text-align:center;">Date</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_orders as $order): ?>
                    <tr>
                        <td class="order-number" style="font-family: monospace;"><?= htmlspecialchars($order['txn_id']) ?></td>
                        <td>
                            <div class="fw-semibold"><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></div>
                            <small class="text-muted"><?= htmlspecialchars($order['payer_email']) ?></small>
                        </td>
                        <td style="text-align:center;"><?= (int)$order['total_products'] ?></td>
                        <td style="text-align:center;">
                            <span class="fw-semibold"><?= currency_code . number_format($order['payment_amount'], 2) ?></span>
                        </td>
                        <td style="text-align:center;">
                            <span class="badge <?= $order['payment_status'] === 'Completed' ? 'bg-success' : ($order['payment_status'] === 'Pending' ? 'bg-warning' : 'bg-secondary') ?>">
                                <?= htmlspecialchars($order['payment_status']) ?>
                            </span>
                        </td>
                        <td style="text-align:center;"><?= date('m/d/Y', strtotime($order['created'])) ?></td>
                        <td style="text-align:center;">
                            <a href="order.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary" title="View Order">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-light">
            <div class="small">
                <a href="orders.php" class="text-decoration-none">
                    <i class="bi bi-arrow-right me-1"></i>View All Orders
                </a>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-header">
        <h6 class="card-title mb-0">Recent Orders</h6>
        <small class="text-muted">Latest shop transactions</small>
    </div>
    <div class="card-body text-center py-5">
        <i class="bi bi-bag fa-3x text-muted mb-3"></i>
        <h5>No Orders Yet</h5>
        <p class="text-muted">Orders will appear here once customers start purchasing.</p>
        <a href="../../shop_system/" class="btn btn-outline-primary" target="_blank">
            <i class="bi bi-box-arrow-up-right me-1"></i>Visit Shop
        </a>
    </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php echo template_admin_footer(); ?>
