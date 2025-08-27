<?php
include 'main.php';

// Use simple triangle icons for sort direction, matching accounts.php
$table_icons = [
    'asc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9650;</span>', // ▲
    'desc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9660;</span>' // ▼
];

// Get sorting parameters
$order_by_whitelist = ['created', 'first_name', 'invoice_number', 'payment_amount', 'payment_status', 'due_date'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'created';
$order = isset($_GET['order']) && strtoupper($_GET['order']) == 'ASC' ? 'ASC' : 'DESC';

// Current date in MySQL DATETIME format
$date = date('Y-m-d H:i:s');

// Action Items Data - Things requiring attention
$stmt = $pdo->prepare('SELECT i.*, c.first_name, c.last_name, c.email 
                       FROM invoices i 
                       LEFT JOIN invoice_clients c ON c.id = i.client_id 
                       WHERE i.due_date < ? AND i.payment_status = "Unpaid"');
$stmt->execute([$date]);
$overdue_invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT i.*, c.first_name, c.last_name, c.email 
                       FROM invoices i 
                       LEFT JOIN invoice_clients c ON c.id = i.client_id 
                       WHERE i.due_date BETWEEN ? AND DATE_ADD(?, INTERVAL 7 DAY) AND i.payment_status = "Unpaid"');
$stmt->execute([$date, $date]);
$due_soon_invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT i.*, c.first_name, c.last_name, c.email 
                       FROM invoices i 
                       LEFT JOIN invoice_clients c ON c.id = i.client_id 
                       WHERE i.payment_status = "Draft"');
$stmt->execute();
$draft_invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

// SQL query that will get all invoices created today with account info for avatars
$stmt = $pdo->prepare('SELECT i.*, c.first_name, c.last_name, c.email, a.avatar, a.role, a.username, (SELECT COUNT(*) FROM invoice_items ii WHERE ii.invoice_number = i.invoice_number) AS total_items 
                       FROM invoices i 
                       LEFT JOIN invoice_clients c ON c.id = i.client_id 
                       LEFT JOIN accounts a ON a.email = c.email 
                       WHERE cast(i.created as DATE) = cast("' . $date . '" as DATE) 
                       ORDER BY i.' . $order_by . ' ' . $order);
$stmt->execute();
$invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Statistics Data
// Get earnings for last 7 days and 30 days
$stmt = $pdo->prepare('SELECT SUM(payment_amount+tax_total) AS earnings FROM invoices WHERE payment_status = "Paid" AND created >= DATE_SUB(cast("' . $date . '" as DATE), INTERVAL 30 DAY)');
$stmt->execute();
$earnings_30 = $stmt->fetchColumn();

$stmt = $pdo->prepare('SELECT SUM(payment_amount+tax_total) AS earnings FROM invoices WHERE payment_status = "Paid" AND created >= DATE_SUB(cast("' . $date . '" as DATE), INTERVAL 7 DAY)');
$stmt->execute();
$earnings_7 = $stmt->fetchColumn();

// Get the total number of invoices
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM invoices');
$stmt->execute();
$invoices_total = $stmt->fetchColumn();

// Get paid invoices count
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM invoices WHERE payment_status = "Paid"');
$stmt->execute();
$paid_invoices_total = $stmt->fetchColumn();

// Get unpaid invoices total amount
$stmt = $pdo->prepare('SELECT SUM(payment_amount+tax_total) AS total FROM invoices WHERE payment_status = "Unpaid"');
$stmt->execute();
$unpaid_total_amount = $stmt->fetchColumn();

// Get the total number of clients
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM invoice_clients');
$stmt->execute();
$clients_total = $stmt->fetchColumn();

// Get active clients (with invoices in last 90 days)
$stmt = $pdo->prepare('SELECT COUNT(DISTINCT client_id) AS total FROM invoices WHERE created >= DATE_SUB(?, INTERVAL 90 DAY)');
$stmt->execute([$date]);
$active_clients_total = $stmt->fetchColumn();

// Calculate action items total
$total_action_items = count($overdue_invoices) + count($due_soon_invoices) + count($draft_invoices);
?>
<?=template_admin_header('Invoice Dashboard', 'invoices')?>

<div class="content-title">
    <div class="title">
        <div class="icon">
            <i class="bi bi-speedometer2" aria-hidden="true"></i>
        </div>
        <div class="txt">
            <h2>Invoice Dashboard</h2>
            <p>View statistics, manage invoices, and track payments.</p>
        </div>
    </div>
</div>
 
<!-- Invoice Dashboard Cards Grid -->
<div class="dashboard-apps">
    <!-- Invoice Quick Actions Card -->
    <div class="app-card" role="region" aria-labelledby="invoice-actions-quick-title">
        <div class="app-header events-header" role="banner" aria-labelledby="invoice-actions-quick-title">
            <h3 id="invoice-actions-quick-title">Quick Actions</h3>
            <i class="bi bi-lightning-charge-fill header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="Invoice management actions">Manage</span>
        </div>
        <div class="app-body">
            <div class="quick-actions">
                <a href="invoice.php" class="quick-action primary">
                    <div class="action-icon">
                        <i class="bi bi-plus" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Create Invoice</h4>
                        <small class="text-muted">New invoice for client</small>
                    </div>
                </a>
                <a href="client.php" class="quick-action secondary">
                    <div class="action-icon">
                        <i class="bi bi-person-plus" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Add Client</h4>
                        <small class="text-muted">Register new client</small>
                    </div>
                </a>
                <a href="invoices.php" class="quick-action info">
                    <div class="action-icon">
                        <i class="bi bi-card-list" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>View All Invoices</h4>
                        <small class="text-muted">Manage existing invoices</small>
                    </div>
                </a>
                <a href="invoice_templates.php" class="quick-action success">
                    <div class="action-icon">
                        <i class="bi bi-file-earmark-text" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Templates</h4>
                        <small class="text-muted">Manage invoice templates</small>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Invoice Action Items Card -->
    <div class="app-card" role="region" aria-labelledby="invoice-actions-title">
        <div class="app-header blog-header" role="banner" aria-labelledby="invoice-actions-title">
            <h3 id="invoice-actions-title">Action Items</h3>
            <i class="bi bi-exclamation-triangle-fill header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="<?= $total_action_items ?> items requiring attention"><?= $total_action_items ?> items</span>
        </div>
        <div class="app-body">
            <?php if ($total_action_items > 0): ?>
                <div class="action-items">
                    <?php if (count($overdue_invoices) > 0): ?>
                        <a href="invoices.php?payment_status=Unpaid&overdue=1" class="action-item warning">
                            <div class="action-icon">
                                <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Overdue Invoices</h4>
                                <small class="text-muted">Past due date, payment needed</small>
                            </div>
                            <div class="action-count"><?= count($overdue_invoices) ?></div>
                        </a>
                    <?php endif; ?>
                    <?php if (count($due_soon_invoices) > 0): ?>
                        <a href="invoices.php?payment_status=Unpaid&due_soon=1" class="action-item info">
                            <div class="action-icon">
                                <i class="bi bi-clock" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Due Soon</h4>
                                <small class="text-muted">Payment due within 7 days</small>
                            </div>
                            <div class="action-count"><?= count($due_soon_invoices) ?></div>
                        </a>
                    <?php endif; ?>
                    <?php if (count($draft_invoices) > 0): ?>
                        <a href="invoices.php?payment_status=Draft" class="action-item secondary">
                            <div class="action-icon">
                                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Draft Invoices</h4>
                                <small class="text-muted">Ready to send to clients</small>
                            </div>
                            <div class="action-count"><?= count($draft_invoices) ?></div>
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="no-action-items">
                    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                    <p>All invoices up to date! No pending actions.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Invoice Statistics Card -->
    <div class="app-card" role="region" aria-labelledby="invoice-stats-title">
        <div class="app-header accounts-header" role="banner" aria-labelledby="invoice-stats-title">
            <h3 id="invoice-stats-title">Invoice Statistics</h3>
            <i class="bi bi-pie-chart-fill header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="<?= $invoices_total ?> total invoices"><?= $invoices_total ?> total</span>
        </div>
        <div class="app-body">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value"><?= number_format(count($invoices)) ?></div>
                    <div class="stat-label">New Today</div>
                    <div class="stat-progress">
                        <div class="progress-bar" style="width: <?= $invoices_total > 0 ? round((count($invoices) / $invoices_total) * 100) : 0 ?>%"></div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= currency_code . ($earnings_30 ? number_format($earnings_30, 2) : '0.00') ?></div>
                    <div class="stat-label">30-Day Revenue</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($paid_invoices_total) ?></div>
                    <div class="stat-label">Paid Invoices</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= currency_code . ($unpaid_total_amount ? number_format($unpaid_total_amount, 2) : '0.00') ?></div>
                    <div class="stat-label">Outstanding</div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<div class="content-title">
    <div class="title">
        <div class="icon alt">
            <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21 13.34C20.37 13.12 19.7 13 19 13V5H5V18.26L6 17.6L9 19.6L12 17.6L13.04 18.29C13 18.5 13 18.76 13 19C13 19.65 13.1 20.28 13.3 20.86L12 20L9 22L6 20L3 22V3H21V13.34M17 9V7H7V9H17M15 13V11H7V13H15M18 15V18H15V20H18V23H20V20H23V18H20V15H18Z" /></svg>
        </div>
        <div class="txt">
            <h2>New invoices</h2>
            <p>List of invoices created today.</p>
        </div>
    </div>
</div>
<br>
<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="card-title mb-0">New Invoices</h6>
            <small class="text-muted">Created today</small>
        </div>
        <div class="card-body">
            <div class="table" role="table" aria-label="New Invoices">
                <table role="grid">
                    <thead role="rowgroup">
                        <tr role="row">
                            <th style="text-align:center;" role="columnheader" scope="col">Avatar</th>
                            <th class="text-left" style="text-align: left;" role="columnheader" scope="col">
                                <?php $q = $_GET; $q['order_by'] = 'first_name'; $q['order'] = ($order_by == 'first_name' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                                <a href="?<?= http_build_query($q) ?>" class="sort-header">Client<?= $order_by == 'first_name' ? $table_icons[strtolower($order)] : '' ?></a>
                            </th>
                            <th class="responsive-hidden text-center" style="text-align: center;" role="columnheader" scope="col">
                                <?php $q = $_GET; $q['order_by'] = 'invoice_number'; $q['order'] = ($order_by == 'invoice_number' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                                <a href="?<?= http_build_query($q) ?>" class="sort-header">Invoice #<?= $order_by == 'invoice_number' ? $table_icons[strtolower($order)] : '' ?></a>
                            </th>
                            <th class="responsive-hidden text-center" style="text-align: center;" role="columnheader" scope="col">Items</th>
                            <th class="responsive-hidden text-center" style="text-align: center;" role="columnheader" scope="col">Payment Method(s)</th>
                            <th class="responsive-hidden text-center" style="text-align: center;" role="columnheader" scope="col">
                                <?php $q = $_GET; $q['order_by'] = 'payment_amount'; $q['order'] = ($order_by == 'payment_amount' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                                <a href="?<?= http_build_query($q) ?>" class="sort-header">Payment Amount<?= $order_by == 'payment_amount' ? $table_icons[strtolower($order)] : '' ?></a>
                            </th>
                            <th class="text-center" style="text-align: center;" role="columnheader" scope="col">
                                <?php $q = $_GET; $q['order_by'] = 'payment_status'; $q['order'] = ($order_by == 'payment_status' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                                <a href="?<?= http_build_query($q) ?>" class="sort-header">Status<?= $order_by == 'payment_status' ? $table_icons[strtolower($order)] : '' ?></a>
                            </th>
                            <th class="responsive-hidden text-center" style="text-align: center;" role="columnheader" scope="col">
                                <?php $q = $_GET; $q['order_by'] = 'due_date'; $q['order'] = ($order_by == 'due_date' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                                <a href="?<?= http_build_query($q) ?>" class="sort-header">Due Date<?= $order_by == 'due_date' ? $table_icons[strtolower($order)] : '' ?></a>
                            </th>
                            <th style="text-align: center;" role="columnheader" scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($invoices)): ?>
                        <tr>
                            <td colspan="9" class="no-results">There are no new invoices.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($invoices as $invoice): ?>
                        <tr>
                            <td style="text-align: center;">
                                <div class="profile-img">
                                    <?php if (!empty($invoice['username'])): ?>
                                        <!-- User has account - show avatar -->
                                        <img src="<?= getUserAvatar($invoice) ?>"
                                            alt="<?= htmlspecialchars($invoice['first_name'] . ' ' . $invoice['last_name'], ENT_QUOTES) ?> avatar"
                                            class="avatar-img"
                                            style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #dee2e6;" />
                                    <?php else: ?>
                                        <!-- No account - show default circle with first letter -->
                                        <span style="background-color:<?=color_from_string($invoice['first_name'])?>"><?=strtoupper(substr($invoice['first_name'], 0, 1))?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="fw-medium"><?=htmlspecialchars($invoice['first_name'] . ' ' . $invoice['last_name'], ENT_QUOTES)?></div>
                                <small class="text-muted"><?=htmlspecialchars($invoice['email'], ENT_QUOTES)?></small>
                            </td>
                            <td class="responsive-hidden" style="text-align: center;">
                                <span class="badge bg-light text-dark"><?=htmlspecialchars($invoice['invoice_number'], ENT_QUOTES)?></span>
                            </td>
                            <td class="responsive-hidden" style="text-align: center;">
                                <?=number_format($invoice['total_items'])?>
                            </td>
                            <td class="responsive-hidden" style="text-align: center;">
                                <?php if ($invoice['payment_methods']): ?>
                                <?php foreach (explode(',', $invoice['payment_methods']) as $method): ?>
                                <span class="badge bg-secondary me-1"><?=trim($method)?></span>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </td>
                            <td class="responsive-hidden" style="text-align:left;">
                                <?=currency_code . number_format($invoice['payment_amount']+$invoice['tax_total'], 2)?>
                            </td>
                            <td style="text-align: center;">
                                <?php if ($invoice['payment_status'] == 'Paid'): ?>
                                <span class="green">Paid</span>
                                <?php elseif ($invoice['payment_status'] == 'Cancelled'): ?>
                                <span class="red">Cancelled</span>
                                <?php elseif ($invoice['due_date'] < $date): ?>
                                <span class="red">Overdue</span>
                                <?php elseif ($invoice['payment_status'] == 'Pending'): ?>
                                <span class="orange">Pending</span>
                                <?php else: ?>
                                <span class="red">Unpaid</span>
                                <?php endif; ?>
                                <?php if ($invoice['viewed']): ?>
                                <i class="bi bi-eye ms-2 text-success" title="The client has viewed the invoice"></i>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="responsive-hidden">
                                <small class="text-muted"><?=date('m/d/Y', strtotime($invoice['due_date']))?></small>
                            </td>
                            <td class="actions" style="text-align:center;">
                                <div class="table-dropdown">
                                    <button class="actions-btn" aria-haspopup="true" aria-expanded="false"
                                        aria-label="Actions for <?= htmlspecialchars($invoice['first_name'] . ' ' . $invoice['last_name'], ENT_QUOTES) ?>">
                                        <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">
                                            <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z" />
                                        </svg>
                                    </button>
                                    <div class="table-dropdown-items">
                                        <a href="view_invoice.php?id=<?=$invoice['id']?>">
                                            <i class="bi bi-eye me-2"></i>View
                                        </a>
                                        <a href="invoice.php?id=<?=$invoice['id']?>">
                                            <i class="bi bi-pencil-square me-2"></i>Edit
                                        </a>
                                        <hr>
                                        <a href="invoices.php?delete=<?=$invoice['id']?>" onclick="return confirm('Are you sure you want to delete this invoice?')" class="text-danger">
                                            <i class="bi bi-trash me-2"></i>Delete
                                        </a>
                                        <?php if ($invoice['payment_status'] == 'Unpaid' && defined('mail_enabled') && mail_enabled): ?>
                                        <hr>
                                        <a href="invoices.php?reminder=<?=$invoice['id']?>">
                                            <i class="bi bi-envelope me-2"></i>Send Reminder
                                        </a>
                                        <?php endif; ?>                          
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
</div>

<div class="content-title" style="margin-top:40px">
    <div class="title">
        <div class="icon alt">
            <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M17 9H7V7H17V9M15 13V16.69L18.19 18.53L18.94 17.23L16.5 15.82V13H15M9 22L10.87 20.76C12.14 22.14 13.97 23 16 23C19.87 23 23 19.87 23 16C23 14.09 22.24 12.36 21 11.1V3H3V22L6 20L9 22M9 19.6L6 17.6L5 18.26V5H19V9.67C18.09 9.24 17.07 9 16 9C14.09 9 12.36 9.76 11.1 11H7V13H9.67C9.24 13.91 9 14.93 9 16C9 17.12 9.26 18.17 9.73 19.11L9 19.6M16 21C13.24 21 11 18.76 11 16C11 13.24 13.24 11 16 11C18.76 11 21 13.24 21 16C21 18.76 18.76 21 16 21Z" /></svg>
        </div>
        <div class="txt">
            <h2>Overdue Invoices</h2>
            <p>List of invoices that are overdue.</p>
        </div>
    </div>
</div>
<br>
<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="card-title mb-0">Overdue Invoices</h6>
            <small class="text-muted">Past due date</small>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="text-align:center;">Avatar</th>
                            <th style="text-align:left;">Client</th>
                            <th class="responsive-hidden" style="text-align:left;">Invoice #</th>
                            <th class="responsive-hidden" style="text-align:center;">Items</th>
                            <th class="responsive-hidden">Payment Method(s)</th>
                            <th class="responsive-hidden">Payment Amount</th>
                            <th>Status</th>
                            <th class="responsive-hidden">Due Date</th>
                            <th width="50" style="text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($invoices_overdue)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-check-circle-fill fs-2 mb-2 d-block text-success" aria-hidden="true"></i>
                                There are no overdue invoices.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($invoices_overdue as $invoice): ?>
                        <tr>
                            <td style="text-align:center;">
                                <img src="<?= getUserAvatar($invoice) ?>" alt="Avatar" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover;">
                            </td>
                            <td>
                                <div class="fw-medium"><?=htmlspecialchars($invoice['first_name'] . ' ' . $invoice['last_name'], ENT_QUOTES)?></div>
                                <small class="text-muted"><?=htmlspecialchars($invoice['email'], ENT_QUOTES)?></small>
                            </td>
                            <td class="responsive-hidden">
                                <span class="badge bg-light text-dark"><?=htmlspecialchars($invoice['invoice_number'], ENT_QUOTES)?></span>
                            </td>
                            <td class="responsive-hidden" style="text-align:center;">
                                <span class="text-muted small"><?=number_format($invoice['total_items'])?></span>
                            </td>
                            <td class="responsive-hidden">
                                <?php if ($invoice['payment_methods']): ?>
                                <?php foreach (explode(',', $invoice['payment_methods']) as $method): ?>
                                <span class="badge bg-secondary me-1"><?=trim($method)?></span>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </td>
                            <td class="responsive-hidden" style="text-align:left;">
                                <span class="fw-bold"><?=currency_code . number_format($invoice['payment_amount']+$invoice['tax_total'], 2)?></span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if ($invoice['payment_status'] == 'Paid'): ?>
                                    <span class="badge bg-success">Paid</span>
                                    <?php elseif ($invoice['payment_status'] == 'Cancelled'): ?>
                                    <span class="badge bg-danger">Cancelled</span>
                                    <?php elseif ($invoice['due_date'] < $date): ?>
                                    <span class="badge bg-danger">Overdue</span>
                                    <?php elseif ($invoice['payment_status'] == 'Pending'): ?>
                                    <span class="badge bg-warning">Pending</span>
                                    <?php else: ?>
                                    <span class="badge bg-danger">Unpaid</span>
                                    <?php endif; ?>
                                    <?php if ($invoice['viewed']): ?>
                                    <i class="bi bi-eye ms-2 text-success" title="The client has viewed the invoice"></i>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="responsive-hidden">
                                <small class="text-muted"><?=date('m/d/Y', strtotime($invoice['due_date']))?></small>
                            </td>
                            <td class="actions" style="text-align:center;">
                                <div class="table-dropdown">
                                    <button class="actions-btn" aria-haspopup="true" aria-expanded="false"
                                        aria-label="Actions for <?= htmlspecialchars($invoice['first_name'] . ' ' . $invoice['last_name'], ENT_QUOTES) ?>">
                                        <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">
                                            <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z" />
                                        </svg>
                                    </button>
                                    <div class="table-dropdown-items">
                                        <a href="view_invoice.php?id=<?=$invoice['id']?>">
                                            <i class="bi bi-eye me-2"></i>View
                                        </a>
                                        <a href="invoice.php?id=<?=$invoice['id']?>">
                                            <i class="bi bi-pencil-square me-2"></i>Edit
                                        </a>
                                        <hr>
                                        <a href="invoices.php?delete=<?=$invoice['id']?>" onclick="return confirm('Are you sure you want to delete this invoice?')" class="text-danger">
                                            <i class="bi bi-trash me-2"></i>Delete
                                        </a>
                                        <?php if ($invoice['payment_status'] == 'Unpaid' && mail_enabled): ?>
                                        <hr>
                                        <a href="invoices.php?reminder=<?=$invoice['id']?>">
                                            <i class="bi bi-envelope me-2"></i>Send Reminder
                                        </a>
                                        <?php endif; ?>                          
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
</div>

<?=template_admin_footer()?>