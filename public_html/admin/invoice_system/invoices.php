<?php
include 'main.php';
// Get current date
$current_date = date('Y-m-d H:i:s');
// Retrieve the GET request parameters (if specified)
$pagination_page = isset($_GET['pagination_page']) ? $_GET['pagination_page'] : 1;
$search = isset($_GET['search_query']) ? $_GET['search_query'] : '';
// Filters parameters
$datestart = isset($_GET['datestart']) ? $_GET['datestart'] : '';
$dateend = isset($_GET['dateend']) ? $_GET['dateend'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$payment_method = isset($_GET['payment_method']) ? $_GET['payment_method'] : '';
$payment_method_str = '%' . $payment_method . '%';
$client_id = isset($_GET['client_id']) ? $_GET['client_id'] : '';
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['id','client_id','invoice_number','payment_amount','payment_status','payment_methods','due_date','created','viewed','first_name','last_name'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'id';
// Number of results per pagination pagination_page
$results_per_pagination_page = 20;
// Declare query param variables
$param1 = ($pagination_page - 1) * $results_per_pagination_page;
$param2 = $results_per_pagination_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE (i.invoice_number LIKE :search OR CONCAT(c.first_name, " ", c.last_name) LIKE :search) ' : '';
// Add filters
// Date start filter
if ($datestart) {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'i.due_date >= :datestart ';
}
// Date end filter
if ($dateend) {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'i.due_date <= :dateend ';
}
// Status filter
if ($status) {
    if ($status == 'paid') {
        $where .= ($where ? 'AND ' : 'WHERE ') . 'i.payment_status = "Paid" ';
    } elseif ($status == 'unpaid') {
        $where .= ($where ? 'AND ' : 'WHERE ') . 'i.payment_status = "Unpaid" ';
    } elseif ($status == 'pending') {
        $where .= ($where ? 'AND ' : 'WHERE ') . 'i.payment_status = "Pending" ';
    } elseif ($status == 'overdue') {
        $where .= ($where ? 'AND ' : 'WHERE ') . 'i.due_date < :current_date AND i.payment_status = "Unpaid" ';
    } elseif ($status == 'cancelled') {
        $where .= ($where ? 'AND ' : 'WHERE ') . 'i.payment_status = "Cancelled" ';
    }
}
// Payment method filter
if ($payment_method) {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'i.payment_methods LIKE :payment_method ';
}
// Client ID filter
if ($client_id) {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'i.client_id = :client_id ';
}
// Retrieve the total number of invoices
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM invoices i LEFT JOIN invoice_clients c ON c.id = i.client_id ' . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($datestart) $stmt->bindParam('datestart', $datestart, PDO::PARAM_STR);
if ($dateend) $stmt->bindParam('dateend', $dateend, PDO::PARAM_STR);
if ($status && $status == 'overdue') $stmt->bindParam('current_date', $current_date, PDO::PARAM_STR);
if ($payment_method) $stmt->bindParam('payment_method', $payment_method_str, PDO::PARAM_STR);
if ($client_id) $stmt->bindParam('client_id', $client_id, PDO::PARAM_INT);
$stmt->execute();
$total_invoices = $stmt->fetchColumn();
// Prepare invoices query with accounts table join for avatar
$stmt = $pdo->prepare('SELECT i.*, c.first_name, c.last_name, c.email, c.account_id, a.avatar, a.role FROM invoices i LEFT JOIN invoice_clients c ON c.id = i.client_id LEFT JOIN accounts a ON a.id = c.account_id ' . $where . ' ORDER BY ' . $order_by . ' ' . $order . ' LIMIT :start_results,:num_results');
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($datestart) $stmt->bindParam('datestart', $datestart, PDO::PARAM_STR);
if ($dateend) $stmt->bindParam('dateend', $dateend, PDO::PARAM_STR);
if ($status && $status == 'overdue') $stmt->bindParam('current_date', $current_date, PDO::PARAM_STR);
if ($payment_method) $stmt->bindParam('payment_method', $payment_method_str, PDO::PARAM_STR);
if ($client_id) $stmt->bindParam('client_id', $client_id, PDO::PARAM_INT);
$stmt->execute();
// Retrieve query results
$invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Delete invoice
if (isset($_GET['delete'])) {
    // Get invoice
    $stmt = $pdo->prepare('SELECT * FROM invoices WHERE id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    $invoice = $stmt->fetch(PDO::FETCH_ASSOC);
    // Delete the invoice
    $stmt = $pdo->prepare('DELETE i, ii FROM invoices i LEFT JOIN invoice_items ii ON ii.invoice_number = i.invoice_number WHERE i.id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    // Check if PDF exists
    if (file_exists('../pdfs/' . $invoice['invoice_number'] . '.pdf')) {
        unlink('../pdfs/' . $invoice['invoice_number'] . '.pdf');
    }
    header('Location: invoices.php?success_msg=3');
    exit;
}
// Send reminder
if (isset($_GET['reminder'])) {
    // Get invoice
    $stmt = $pdo->prepare('SELECT * FROM invoices WHERE id = ?');
    $stmt->execute([ $_GET['reminder'] ]);
    $invoice = $stmt->fetch(PDO::FETCH_ASSOC);
    // Get client details
    $stmt = $pdo->prepare('SELECT * FROM invoice_clients WHERE id = ?');
    $stmt->execute([ $invoice['client_id'] ]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    // Send email
    send_client_invoice_email($invoice, $client, 'Payment Reminder');
    header('Location: invoices.php?success_msg=5');
    exit;
}
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Invoice created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Invoice updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Invoice deleted successfully!';
    }
    if ($_GET['success_msg'] == 4) {
        $success_msg = 'Invoice(s) imported successfully! ' . $_GET['imported'] . ' invoice(s) were imported.';
    }
    if ($_GET['success_msg'] == 5) {
        $success_msg = 'Payment reminder sent successfully!';
    }
}
// Create URL
$url = 'invoices.php?search_query=' . $search . '&datestart=' . $datestart . '&dateend=' . $dateend . '&status=' . $status . '&payment_method=' . $payment_method . '&client_id=' . $client_id;
?>
<?=template_admin_header('Invoices', 'invoices', 'view')?>

<div class="content-title mb-4">
    <div class="title">
        <div class="icon">
            <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M17 2H2V17H4V4H17V2M21 22L18.5 20.32L16 22L13.5 20.32L11 22L8.5 20.32L6 22V6H21V22M10 10V12H17V10H10M15 14H10V16H15V14Z" /></svg>
        </div>
        <div class="txt">
            <h2>Invoices</h2>
            <p>View, edit, and create invoices.</p>
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
    <a href="invoice.php" class="btn btn-outline-secondary">
        <i class="bi bi-plus-lg me-1"></i>Create Invoice
    </a>
</div>

<div class="card mb-3 invoice-table-card">
    <div class="card-header invoice-table-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Invoice Management</h6>
        <small class="text-muted"><?= number_format($total_invoices) ?> total invoices</small>
    </div>
    <div class="card-body p-0">
        <div class="table-filters-wrapper p-3">
        <form action="" method="get" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label for="search_query" class="form-label">Search</label>
                    <input id="search_query" type="text" name="search_query" class="form-control"
                        placeholder="Search invoices..." 
                        value="<?= htmlspecialchars($search, ENT_QUOTES) ?>">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="" <?= $status == '' ? ' selected' : '' ?>>All</option>
                        <option value="paid" <?= $status == 'paid' ? ' selected' : '' ?>>Paid</option>
                        <option value="unpaid" <?= $status == 'unpaid' ? ' selected' : '' ?>>Unpaid</option>
                        <option value="pending" <?= $status == 'pending' ? ' selected' : '' ?>>Pending</option>
                        <option value="overdue" <?= $status == 'overdue' ? ' selected' : '' ?>>Overdue</option>
                        <option value="cancelled" <?= $status == 'cancelled' ? ' selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="payment_method" class="form-label">Payment</label>
                    <select name="payment_method" id="payment_method" class="form-select">
                        <option value="" <?= $payment_method == '' ? ' selected' : '' ?>>All</option>
                        <option value="Cash" <?= $payment_method == 'Cash' ? ' selected' : '' ?>>Cash</option>
                        <option value="Bank Transfer" <?= $payment_method == 'Bank Transfer' ? ' selected' : '' ?>>Bank</option>
                        <option value="PayPal" <?= $payment_method == 'PayPal' ? ' selected' : '' ?>>PayPal</option>
                        <option value="Stripe" <?= $payment_method == 'Stripe' ? ' selected' : '' ?>>Stripe</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="datestart" class="form-label">Date From</label>
                    <input type="date" name="datestart" id="datestart" 
                        value="<?= htmlspecialchars($datestart ?: date('Y-m-d', strtotime('-30 days')), ENT_QUOTES) ?>" 
                        class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="dateend" class="form-label">Date To</label>
                    <input type="date" name="dateend" id="dateend" 
                        value="<?= htmlspecialchars($dateend ?: date('Y-m-d'), ENT_QUOTES) ?>" 
                        class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="filters" class="form-label">Filters</label>
                    <button type="submit" class="btn btn-success w-100 d-flex align-items-center justify-content-center">
                        <i class="bi bi-funnel me-2" aria-hidden="true"></i>
                        Apply
                    </button>
                </div>
            </div>
            <?php if ($client_id): ?>
            <input type="hidden" name="client_id" value="<?= htmlspecialchars($client_id, ENT_QUOTES) ?>">
            <?php endif; ?>
        </form>

        <!-- Active Filters -->
        <?php if ($search || $datestart || $dateend || $status || $payment_method || $client_id): ?>
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
                            Status: <?= ucfirst(htmlspecialchars($status, ENT_QUOTES)) ?>
                            <a href="<?= remove_url_param($url, 'status') ?>" class="text-white ms-1" aria-label="Remove status filter">×</a>
                        </span>
                    <?php endif; ?>
                    <?php if ($payment_method): ?>
                        <span class="badge bg-secondary">
                            Method: <?= htmlspecialchars($payment_method, ENT_QUOTES) ?>
                            <a href="<?= remove_url_param($url, 'payment_method') ?>" class="text-white ms-1" aria-label="Remove payment method filter">×</a>
                        </span>
                    <?php endif; ?>
                    <?php if ($datestart): ?>
                        <span class="badge bg-secondary">
                            From: <?= htmlspecialchars($datestart, ENT_QUOTES) ?>
                            <a href="<?= remove_url_param($url, 'datestart') ?>" class="text-white ms-1" aria-label="Remove start date filter">×</a>
                        </span>
                    <?php endif; ?>
                    <?php if ($dateend): ?>
                        <span class="badge bg-secondary">
                            To: <?= htmlspecialchars($dateend, ENT_QUOTES) ?>
                            <a href="<?= remove_url_param($url, 'dateend') ?>" class="text-white ms-1" aria-label="Remove end date filter">×</a>
                        </span>
                    <?php endif; ?>
                    <?php if ($client_id): ?>
                        <span class="badge bg-secondary">
                            Client Filter Active
                            <a href="<?= remove_url_param($url, 'client_id') ?>" class="text-white ms-1" aria-label="Remove client filter">×</a>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        </div>

        <div class="table-responsive invoice-table-wrapper">
            <table class="table table-hover mb-0 invoice-table">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="text-align:center;" role="columnheader" scope="col">Avatar</th>
                        <th class="text-start" style="text-align:left; min-width: 120px;" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'first_name'; $q['order'] = ($order_by == 'first_name' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>#invoices-table" class="text-decoration-none text-dark">Client<?= $order_by == 'first_name' ? ($order == 'ASC' ? ' ↑' : ' ↓') : '' ?></a>
                        </th>
                        <th class="responsive-hidden text-start" style="text-align:left; min-width: 140px;" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'invoice_number'; $q['order'] = ($order_by == 'invoice_number' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>#invoices-table" class="text-decoration-none text-dark">Invoice #<?= $order_by == 'invoice_number' ? ($order == 'ASC' ? ' ↑' : ' ↓') : '' ?></a>
                        </th>
                        <th class="responsive-hidden text-start" style="text-align:left; min-width: 100px; padding-right: 20px;" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'payment_methods'; $q['order'] = ($order_by == 'payment_methods' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>#invoices-table" class="text-decoration-none text-dark">Method<?= $order_by == 'payment_methods' ? ($order == 'ASC' ? ' ↑' : ' ↓') : '' ?></a>
                        </th>
                        <th class="responsive-hidden text-start" style="text-align:left; min-width: 100px; padding-right: 20px;" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'payment_amount'; $q['order'] = ($order_by == 'payment_amount' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>#invoices-table" class="text-decoration-none text-dark">Amount<?= $order_by == 'payment_amount' ? ($order == 'ASC' ? ' ↑' : ' ↓') : '' ?></a>
                        </th>
                        <th class="text-start" style="text-align:left; min-width: 100px; padding-right: 20px;" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'payment_status'; $q['order'] = ($order_by == 'payment_status' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>#invoices-table" class="text-decoration-none text-dark">Status<?= $order_by == 'payment_status' ? ($order == 'ASC' ? ' ↑' : ' ↓') : '' ?></a>
                        </th>
                        <th class="responsive-hidden text-end" style="text-align:right; min-width: 120px;" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'due_date'; $q['order'] = ($order_by == 'due_date' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>#invoices-table" class="text-decoration-none text-dark">Due Date<?= $order_by == 'due_date' ? ($order == 'ASC' ? ' ↑' : ' ↓') : '' ?></a>
                        </th>
                        <th class="text-center" style="text-align:center;" role="columnheader" scope="col">Actions</th>
                    </tr>
                </thead>
            <tbody>
                <?php if (!$invoices): ?>
                <tr>
                    <td colspan="8" class="no-results">There are no invoices.</td>
                </tr>
                <?php endif; ?>
                <?php foreach ($invoices as $invoice): ?>
                <tr>
                    
                    <td style="text-align: center;">
                        <div class="profile-img">
                            <?php 
                            // Use getUserAvatar function with proper account data
                            $account_data = array(
                                'avatar' => $invoice['avatar'] ?? '',
                                'role' => $invoice['role'] ?? 'Member'
                            );
                            $avatar_url = getUserAvatar($account_data);
                            ?>
                            <img src="<?= $avatar_url ?>" 
                                 alt="<?= htmlspecialchars($invoice['first_name'], ENT_QUOTES) ?> avatar" 
                                 class="avatar-img"
                                 style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover; border: 2px solid #dee2e6;" />
                        </div>
                    </td>
                    <td><?=htmlspecialchars($invoice['first_name'] . ' ' . $invoice['last_name'], ENT_QUOTES)?></td>
                    <td class="alt responsive-hidden"><?=htmlspecialchars($invoice['invoice_number'], ENT_QUOTES)?></td>
                    <td class="alt responsive-hidden">
                        <?php if ($invoice['payment_methods']): ?>
                        <?php foreach (explode(',', $invoice['payment_methods']) as $method): ?>
                        <span class="grey"><?=$method?></span>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </td>
                    <td class="strong responsive-hidden"><?=currency_code . number_format($invoice['payment_amount']+$invoice['tax_total'], 2)?></td>
                    <td>
                        <div class="status d-flex align-items-center gap-1">
                            <?php if ($invoice['viewed']): ?>
                            <i class="bi bi-eye-fill text-success" title="Invoice has been viewed by client"></i>
                            <?php else: ?>
                            <i class="bi bi-eye-slash text-muted" title="Invoice has not been viewed by client"></i>
                            <?php endif; ?>
                            <?php if ($invoice['payment_status'] == 'Paid'): ?>
                            <span class="green">Paid</span>
                            <?php elseif ($invoice['payment_status'] == 'Cancelled'): ?>
                            <span class="grey">Cancelled</span>
                            <?php elseif ($invoice['due_date'] < $current_date): ?>
                            <span class="red">Overdue</span>
                            <?php elseif ($invoice['payment_status'] == 'Pending'): ?>
                            <span class="orange">Pending</span>
                            <?php else: ?>
                            <span class="red">Unpaid</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="alt responsive-hidden text-end"><?=date('n/j/Y', strtotime($invoice['due_date']))?></td>
                    <td class="actions" style="text-align: center;">
                        <div class="table-dropdown">
                            <svg width="32" height="32" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/>
                            </svg>
                            <div class="table-dropdown-items" role="menu" aria-label="Invoice Actions">
                                <div role="menuitem">
                                    <a href="view_invoice.php?id=<?=$invoice['id']?>" 
                                       class="blue" 
                                       aria-label="View invoice <?= htmlspecialchars($invoice['invoice_number'], ENT_QUOTES) ?>">
                                        <span class="icon" aria-hidden="true">
                                            <i class="bi bi-eye"></i>
                                        </span>
                                        <span>View</span>
                                    </a>
                                </div>
                                <div role="menuitem">
                                    <a href="invoice.php?id=<?=$invoice['id']?>" 
                                       class="green" 
                                       aria-label="Edit invoice <?= htmlspecialchars($invoice['invoice_number'], ENT_QUOTES) ?>">
                                        <span class="icon" aria-hidden="true">
                                            <i class="bi bi-pencil-square"></i>
                                        </span>
                                        <span>Edit</span>
                                    </a>
                                </div>
                                <div role="menuitem">
                                    <a href="invoices.php?delete=<?=$invoice['id']?>" 
                                       class="red" 
                                       onclick="return confirm('Are you sure you want to delete this invoice?')"
                                       aria-label="Delete invoice <?= htmlspecialchars($invoice['invoice_number'], ENT_QUOTES) ?>">
                                        <span class="icon" aria-hidden="true">
                                            <i class="bi bi-trash"></i>
                                        </span>    
                                        <span>Delete</span>
                                    </a>
                                </div>
                                <?php if ($invoice['payment_status'] == 'Unpaid' && defined('mail_enabled') && mail_enabled): ?>
                                <div role="menuitem">
                                    <a href="invoices.php?reminder=<?=$invoice['id']?>" 
                                       class="orange" 
                                       aria-label="Send reminder for invoice <?= htmlspecialchars($invoice['invoice_number'], ENT_QUOTES) ?>">
                                        <span class="icon" aria-hidden="true">
                                            <i class="bi bi-envelope"></i>
                                        </span>
                                        <span>Send Reminder</span>
                                    </a>     
                                </div>
                                <?php endif; ?>                          
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
    <div class="card-footer invoice-table-footer bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Showing <?= count($invoices) ?> of <?= $total_invoices ?> invoices
            </small>
            <nav aria-label="Invoices pagination">
                <div class="d-flex gap-2">
                    <?php if ($pagination_page > 1): ?>
                        <a href="<?= $url ?>&pagination_page=<?= $pagination_page - 1 ?>&order=<?= $order ?>&order_by=<?= $order_by ?>" 
                           class="btn btn-sm btn-outline-secondary">Previous</a>
                    <?php endif; ?>
                    <span class="btn btn-sm btn-secondary disabled">
                        Page <?= $pagination_page ?> of <?= ceil($total_invoices / $results_per_pagination_page) == 0 ? 1 : ceil($total_invoices / $results_per_pagination_page) ?>
                    </span>
                    <?php if ($pagination_page * $results_per_pagination_page < $total_invoices): ?>
                        <a href="<?= $url ?>&pagination_page=<?= $pagination_page + 1 ?>&order=<?= $order ?>&order_by=<?= $order_by ?>" 
                           class="btn btn-sm btn-outline-secondary">Next</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </div>
</div>

<?= template_admin_footer() ?>