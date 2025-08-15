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
    <i class="fas fa-check-circle me-2"></i>
    <?=$success_msg?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="d-flex gap-2 mb-4">
    <a href="invoice.php" class="btn btn-outline-secondary">
        <i class="fas fa-plus me-1"></i>Create Invoice
    </a>
</div>

<!-- Search and Filter Form -->
<form method="get" class="mb-3">
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label for="search_query" class="form-label">Search Invoices</label>
            <input type="text" id="search_query" name="search_query" class="form-control" placeholder="Search invoices..." value="<?=htmlspecialchars($search, ENT_QUOTES)?>">
        </div>
        <div class="col-md-2">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select">
                <option value=""<?=$status==''?' selected':''?>>All</option>
                <option value="paid"<?=$status=='paid'?' selected':''?>>Paid</option>
                <option value="unpaid"<?=$status=='unpaid'?' selected':''?>>Unpaid</option>
                <option value="pending"<?=$status=='pending'?' selected':''?>>Pending</option>
                <option value="overdue"<?=$status=='overdue'?' selected':''?>>Overdue</option>
                <option value="cancelled"<?=$status=='cancelled'?' selected':''?>>Cancelled</option>
            </select>
        </div>
        <div class="col-md-2">
            <label for="payment_method" class="form-label">Payment Method</label>
            <select name="payment_method" id="payment_method" class="form-select">
                <option value=""<?=$payment_method==''?' selected':''?>>All</option>
                <option value="Cash"<?=$payment_method=='Cash'?' selected':''?>>Cash</option>
                <option value="Bank Transfer"<?=$payment_method=='Bank Transfer'?' selected':''?>>Bank Transfer</option>
                <option value="PayPal"<?=$payment_method=='PayPal'?' selected':''?>>PayPal</option>
                <option value="Stripe"<?=$payment_method=='Stripe'?' selected':''?>>Stripe</option>
            </select>
        </div>
        <div class="col-md-2">
            <label for="datestart" class="form-label">Due Date From</label>
            <input type="date" name="datestart" id="datestart" value="<?=htmlspecialchars($datestart, ENT_QUOTES)?>" class="form-control">
        </div>
        <div class="col-md-2">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-filter me-1" aria-hidden="true"></i>Apply Filters
                </button>
                <?php if ($search || $datestart || $dateend || $status || $payment_method || $client_id): ?>
                <a href="invoices.php" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1" aria-hidden="true"></i>Clear
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php if ($dateend): ?>
    <input type="hidden" name="dateend" value="<?=htmlspecialchars($dateend, ENT_QUOTES)?>">
    <?php endif; ?>
    <?php if ($client_id): ?>
    <input type="hidden" name="client_id" value="<?=htmlspecialchars($client_id, ENT_QUOTES)?>">
    <?php endif; ?>
</form>


<!-- Invoices Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Invoices</h6>
        <span class="badge bg-secondary"><?= number_format($total_invoices) ?> Total</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="text-align:center;" role="columnheader" scope="col">Avatar</th>
                        <th style="text-align:left;" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'first_name'; $q['order'] = ($order_by == 'first_name' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none text-dark">Client<?= $order_by == 'first_name' ? ($order == 'ASC' ? ' ↑' : ' ↓') : '' ?></a>
                        </th>
                        <th class="responsive-hidden" style="text-align:left;" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'invoice_number'; $q['order'] = ($order_by == 'invoice_number' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none text-dark">Invoice #<?= $order_by == 'invoice_number' ? ($order == 'ASC' ? ' ↑' : ' ↓') : '' ?></a>
                        </th>
                        <th class="responsive-hidden" style="text-align:left;" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'payment_methods'; $q['order'] = ($order_by == 'payment_methods' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none text-dark">Payment Method<?= $order_by == 'payment_methods' ? ($order == 'ASC' ? ' ↑' : ' ↓') : '' ?></a>
                        </th>
                        <th class="responsive-hidden text-end" style="text-align:right;" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'payment_amount'; $q['order'] = ($order_by == 'payment_amount' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none text-dark">Amount<?= $order_by == 'payment_amount' ? ($order == 'ASC' ? ' ↑' : ' ↓') : '' ?></a>
                        </th>
                        <th class="text-center" style="text-align:center;" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'viewed'; $q['order'] = ($order_by == 'viewed' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none text-dark">Viewed<?= $order_by == 'viewed' ? ($order == 'ASC' ? ' ↑' : ' ↓') : '' ?></a>
                        </th>
                        <th class="text-center" style="text-align:center;" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'payment_status'; $q['order'] = ($order_by == 'payment_status' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none text-dark">Status<?= $order_by == 'payment_status' ? ($order == 'ASC' ? ' ↑' : ' ↓') : '' ?></a>
                        </th>
                        <th class="responsive-hidden text-center" style="text-align:center;" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'due_date'; $q['order'] = ($order_by == 'due_date' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none text-dark">Due Date<?= $order_by == 'due_date' ? ($order == 'ASC' ? ' ↑' : ' ↓') : '' ?></a>
                        </th>
                        <th class="text-center" style="text-align:center;" role="columnheader" scope="col">Actions</th>
                    </tr>
                </thead>
            <tbody>
                <?php if (!$invoices): ?>
                <tr>
                    <td colspan="9" class="no-results">There are no invoices.</td>
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
                    <td class="text-center">
                        <?php if ($invoice['viewed']): ?>
                        <div class="viewed">
                            <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><title>The client has viewed the invoice.</title><path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z" /></svg>
                        </div>
                        <?php else: ?>
                        <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="status">
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
                    <td class="alt responsive-hidden"><?=date('n/j/Y', strtotime($invoice['due_date']))?></td>
                    <td class="actions" style="text-align: center;">
                        <div class="table-dropdown">
                            <button class="actions-btn" aria-haspopup="true" aria-expanded="false"
                                aria-label="Actions for Invoice <?= htmlspecialchars($invoice['invoice_number'], ENT_QUOTES) ?>">
                                <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">
                                    <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z" />
                                </svg>
                            </button>
                            <div class="table-dropdown-items" role="menu" aria-label="Invoice Actions">
                                <div role="menuitem">
                                    <a href="view_invoice.php?id=<?=$invoice['id']?>" 
                                       class="blue" 
                                       tabindex="-1" 
                                       aria-label="View invoice <?= htmlspecialchars($invoice['invoice_number'], ENT_QUOTES) ?>">
                                        <span class="icon" aria-hidden="true">
                                            <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                                <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z"/>
                                            </svg>
                                        </span>
                                        <span>View</span>
                                    </a>
                                </div>
                                <div role="menuitem">
                                    <a href="invoice.php?id=<?=$invoice['id']?>" 
                                       class="green" 
                                       tabindex="-1"
                                       aria-label="Edit invoice <?= htmlspecialchars($invoice['invoice_number'], ENT_QUOTES) ?>">
                                        <span class="icon" aria-hidden="true">
                                            <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z"/>
                                            </svg>
                                        </span>
                                        <span>Edit</span>
                                    </a>
                                </div>
                                <div role="menuitem">
                                    <a href="invoices.php?delete=<?=$invoice['id']?>" 
                                       class="red" 
                                       tabindex="-1" 
                                       onclick="return confirm('Are you sure you want to delete this invoice?')"
                                       aria-label="Delete invoice <?= htmlspecialchars($invoice['invoice_number'], ENT_QUOTES) ?>">
                                        <span class="icon" aria-hidden="true">
                                            <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                <path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/>
                                            </svg>
                                        </span>    
                                        <span>Delete</span>
                                    </a>
                                </div>
                                <?php if ($invoice['payment_status'] == 'Unpaid' && mail_enabled): ?>
                                <div role="menuitem">
                                    <a href="invoices.php?reminder=<?=$invoice['id']?>" 
                                       class="orange" 
                                       tabindex="-1"
                                       aria-label="Send reminder for invoice <?= htmlspecialchars($invoice['invoice_number'], ENT_QUOTES) ?>">
                                        <span class="icon" aria-hidden="true">
                                            <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                                <path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z" />
                                            </svg>
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

<div class="card-body">
    <div class="pagination mt-3">
        <?php if ($pagination_page > 1): ?>
        <a href="<?=$url?>&pagination_page=<?=$pagination_page-1?>&order=<?=$order?>&order_by=<?=$order_by?>">Prev</a>
        <?php endif; ?>
        <span>Page <?=$pagination_page?> of <?=ceil($total_invoices / $results_per_pagination_page) == 0 ? 1 : ceil($total_invoices / $results_per_pagination_page)?></span>
        <?php if ($pagination_page * $results_per_pagination_page < $total_invoices): ?>
        <a href="<?=$url?>&pagination_page=<?=$pagination_page+1?>&order=<?=$order?>&order_by=<?=$order_by?>">Next</a>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleFilters() {
    const filtersDiv = document.getElementById('advanced-filters');
    if (filtersDiv.style.display === 'none' || filtersDiv.style.display === '') {
        filtersDiv.style.display = 'block';
    } else {
        filtersDiv.style.display = 'none';
    }
}

// Show filters by default if any filters are active
document.addEventListener('DOMContentLoaded', function() {
    const hasActiveFilters = <?= json_encode(!empty($datestart) || !empty($dateend) || !empty($status) || !empty($payment_method) || !empty($client_id)) ?>;
    if (hasActiveFilters) {
        document.getElementById('advanced-filters').style.display = 'block';
    }
});
</script>

<?=template_admin_footer()?>