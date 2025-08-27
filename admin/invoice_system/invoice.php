<?php
include 'main.php';
// Default invoice values
$invoice = [
    'client_id' => '',
    'invoice_number' => invoice_prefix . substr(str_shuffle(str_repeat('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', 8)), 0, 8),
    'payment_amount' => '',
    'payment_status' => '',
    'payment_methods' => '',
    'notes' => '',
    'viewed' => 0,
    'due_date' => date('Y-m-d\TH:i'),
    'created' => date('Y-m-d\TH:i'),
    'tax' => '',
    'tax_total' => '',
    'invoice_template' => 'default',
    'recurrence' => 0,
    'recurrence_period' => 1,
    'recurrence_period_type' => 'day',
    'payment_ref' => '',
    'paid_with' => '',
    'paid_total' => 0
];
// Get template names in templates folder, only display folders
$templates = array_filter(glob(base_path . 'templates/*'), 'is_dir');
// Retrieve accounts
$accounts = $pdo->query('SELECT * FROM invoice_clients ORDER BY first_name ASC')->fetchAll();
// Invoice items
$invoice_items = [];
// Calculate payment amount
$payment_amount = 0;
if (isset($_POST['item_id']) && is_array($_POST['item_id']) && count($_POST['item_id']) > 0) {
    foreach ($_POST['item_id'] as $i => $item_id) {
        $payment_amount += $_POST['item_price'][$i] * $_POST['item_quantity'][$i];
    }
}
// Calculate tax
$tax_total = 0;
$tax = 'fixed';
if (isset($_POST['tax'])) {
    if (strpos($_POST['tax'], '%') !== false) {
        $tax_total = $payment_amount * (floatval(str_replace('%', '', $_POST['tax'])) / 100);
        $tax = $_POST['tax'];
    } else {
        $tax_total = floatval($_POST['tax']);
    }
}
// Check if the ID param exists
if (isset($_GET['id'])) {
    // Retrieve the invoice from the database
    $stmt = $pdo->prepare('SELECT * FROM invoices WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $invoice = $stmt->fetch(PDO::FETCH_ASSOC);
    // Get items
    $stmt = $pdo->prepare('SELECT * FROM invoice_items WHERE invoice_number = ?');
    $stmt->execute([ $invoice['invoice_number'] ]);
    $invoice_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // ID param exists, edit an existing invoice
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Get the payment methods as a comma separated string
        $payment_methods = isset($_POST['payment_methods']) ? implode(', ', $_POST['payment_methods']) : '';
        // Update the invoice
        $stmt = $pdo->prepare('UPDATE invoices SET client_id = ?, invoice_number = ?, payment_amount = ?, payment_status = ?, payment_methods = ?, notes = ?, due_date = ?, created = ?, tax = ?, tax_total = ?, invoice_template = ?, recurrence = ?, recurrence_period = ?, recurrence_period_type = ?, paid_total = ? WHERE id = ?');
        $stmt->execute([ $_POST['client_id'], $_POST['invoice_number'], $payment_amount, $_POST['payment_status'], $payment_methods, $_POST['notes'], $_POST['due_date'], $_POST['created'], $tax, $tax_total, $_POST['template'], $_POST['recurrence'], $_POST['recurrence_period'], $_POST['recurrence_period_type'], $_POST['paid_total'], $_GET['id'] ]);
        // add items
        addItems($pdo, $_POST['invoice_number']);
        // Create PDF
        $stmt = $pdo->prepare('SELECT * FROM invoices WHERE invoice_number = ?');
        $stmt->execute([ $_POST['invoice_number'] ]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);
        // Get invoice items
        $stmt = $pdo->prepare('SELECT * FROM invoice_items WHERE invoice_number = ?');
        $stmt->execute([ $invoice['invoice_number'] ]);
        $invoice_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Get client details
        $stmt = $pdo->prepare('SELECT * FROM invoice_clients WHERE id = ?');
        $stmt->execute([ $invoice['client_id'] ]);
        $client = $stmt->fetch(PDO::FETCH_ASSOC);
        // Generate pdf
        create_invoice_pdf($invoice, $invoice_items, $client);
        // Redirect to the invoices page
        header('Location: invoices.php?success_msg=2');
        exit;
    }
    if (isset($_POST['delete'])) {
        // Redirect and delete invoice
        header('Location: invoices.php?delete=' . $_GET['id']);
        exit;
    }
} else {
    // Create a new invoice
    $page = 'Create';
    if (isset($_POST['submit'])) {
        // Get the payment methods as a comma separated string
        $payment_methods = isset($_POST['payment_methods']) ? implode(', ', $_POST['payment_methods']) : ''; 
        // Insert the invoice
        $stmt = $pdo->prepare('INSERT INTO invoices (client_id, invoice_number, payment_amount, payment_status, payment_methods, notes, viewed, due_date, created, tax, tax_total, invoice_template, recurrence, recurrence_period, recurrence_period_type) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $stmt->execute([ $_POST['client_id'], $_POST['invoice_number'], $payment_amount, $_POST['payment_status'], $payment_methods, $_POST['notes'], 0, $_POST['due_date'], $_POST['created'], $tax, $tax_total, $_POST['template'], $_POST['recurrence'], $_POST['recurrence_period'], $_POST['recurrence_period_type'] ]);
        // add items
        addItems($pdo, $_POST['invoice_number']);
        // Create PDF
        $stmt = $pdo->prepare('SELECT * FROM invoices WHERE invoice_number = ?');
        $stmt->execute([ $_POST['invoice_number'] ]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);
        // Get invoice items
        $stmt = $pdo->prepare('SELECT * FROM invoice_items WHERE invoice_number = ?');
        $stmt->execute([ $invoice['invoice_number'] ]);
        $invoice_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Get client details
        $stmt = $pdo->prepare('SELECT * FROM invoice_clients WHERE id = ?');
        $stmt->execute([ $invoice['client_id'] ]);
        $client = $stmt->fetch(PDO::FETCH_ASSOC);
        // Generate pdf
        create_invoice_pdf($invoice, $invoice_items, $client);
        // Send email
        if (isset($_POST['send_email'])) {
            send_client_invoice_email($invoice, $client);
        }
        // Redirect to the invoices page
        header('Location: invoices.php?success_msg=1');
        exit;
    }
}
?>
<?=template_admin_header($page . ' Invoice', 'invoices', 'manage')?>

<div class="content-title mb-4">
    <div class="title">
        <div class="icon">
            <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M17 2H2V17H4V4H17V2M21 22L18.5 20.32L16 22L13.5 20.32L11 22L8.5 20.32L6 22V6H21V22M10 10V12H17V10H10M15 14H10V16H15V14Z" /></svg>
        </div>
        <div class="txt">
            <h2><?=$page?> Invoice</h2>
            <p>Manage invoice details, client information, and line items.</p>
        </div>
    </div>
     
         
    </div>
</div>

<!-- Action Buttons under content title -->
<div class="mb-4">
    <div class="d-flex gap-2">
        <a href="invoices.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Cancel
        </a>
        <button type="submit" name="submit" class="btn btn-success" form="invoice-form">
            <i class="bi bi-save me-1" aria-hidden="true"></i>Save Invoice
        </button>
        <?php if ($page == 'Edit'): ?>
        <button type="submit" name="delete" class="btn btn-danger" form="invoice-form" onclick="return confirm('Are you sure you want to delete this invoice?')">
            <i class="bi bi-trash me-1" aria-hidden="true"></i>Delete
        </button>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0"><?=$page?> Invoice</h6>
    </div>
    <div class="card-body p-0">
        <form action="" method="post" id="invoice-form">

            <!-- Tab Navigation -->
            <div class="tab-nav" role="tablist" aria-label="Invoice form sections">
                <button class="tab-btn active" 
                    role="tab"
                    aria-selected="true"
                    aria-controls="details"
                    id="details-tab-btn"
                    onclick="openTab(event, 'details')">
                    <i class="bi bi-info-circle me-1" aria-hidden="true"></i>Details
                </button>
                <button class="tab-btn" 
                    role="tab"
                    aria-selected="false"
                    aria-controls="items"
                    id="items-tab-btn"
                    onclick="openTab(event, 'items')">
                    <i class="bi bi-card-list me-1" aria-hidden="true"></i>Items
                </button>
            </div>

            <!-- Tab Content -->
            <div id="details" class="tab-content active" role="tabpanel" aria-labelledby="details-tab-btn">
                <!-- Details Tab -->
                    <!-- Client Information -->
                    <fieldset class="mb-4">
                        <legend class="h6 text-primary border-bottom pb-1">Client Information</legend>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                                    <select class="form-select" id="client_id" name="client_id" required>
                                        <option value="">Choose client...</option>
                                        <?php foreach ($accounts as $account): ?>
                                        <option value="<?=$account['id']?>"<?=$invoice['client_id']==$account['id']?' selected':''?>><?=$account['first_name']?> <?=$account['last_name']?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <a href="client.php" class="btn btn-outline-primary btn-sm add-client">
                                            <i class="bi bi-plus me-1"></i>Add New Client
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if ($page == 'Create'): ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="send_email" name="send_email" value="1" checked>
                                    <label class="form-check-label" for="send_email">
                                        Send email to client
                                    </label>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </fieldset>

                    <!-- Invoice Details -->
                    <fieldset class="mb-4">
                        <legend class="h6 text-primary border-bottom pb-1">Invoice Details</legend>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="invoice_number" class="form-label">Invoice Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="invoice_number" name="invoice_number" placeholder="Invoice Number" value="<?=htmlspecialchars($invoice['invoice_number'], ENT_QUOTES)?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_status" class="form-label">Payment Status</label>
                                    <select class="form-select" id="payment_status" name="payment_status">
                                        <option value="Unpaid"<?=$invoice['payment_status']=='Unpaid'?' selected':''?>>Unpaid</option>
                                        <option value="Paid"<?=$invoice['payment_status']=='Paid'?' selected':''?>>Paid</option>
                                        <option value="Pending"<?=$invoice['payment_status']=='Pending'?' selected':''?>>Pending</option>
                                        <option value="Cancelled"<?=$invoice['payment_status']=='Cancelled'?' selected':''?>>Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="due_date" name="due_date" value="<?=date('Y-m-d\TH:i', strtotime($invoice['due_date']))?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="created" class="form-label">Created <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="created" name="created" value="<?=date('Y-m-d\TH:i', strtotime($invoice['created']))?>" required>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <!-- Payment Information -->
                    <fieldset class="mb-4">
                        <legend class="h6 text-primary border-bottom pb-1">Payment Information</legend>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_methods" class="form-label">Payment Methods</label>
                                    <div class="multiselect" data-name="payment_methods[]">
                                        <?php foreach (array_filter(explode(', ', $invoice['payment_methods'])) as $m): ?>
                                        <span class="item" data-value="<?=$m?>">
                                            <i class="remove">&times;</i><?=$m?>
                                            <input type="hidden" name="payment_methods[]" value="<?=$m?>">
                                        </span>
                                        <?php endforeach; ?>
                                        <input type="text" class="search form-control" id="payment_method" placeholder="Payment Methods">
                                        <div class="list">
                                            <span data-value="Cash">Cash</span>
                                            <span data-value="Bank Transfer">Bank Transfer</span>
                                            <span data-value="PayPal">PayPal</span>
                                            <span data-value="Stripe">Stripe</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tax" class="form-label">Tax</label>
                                    <input type="text" class="form-control" id="tax" name="tax" placeholder="% or fixed amount" value="<?=$invoice['tax'] == 'fixed' ? $invoice['tax_total'] : $invoice['tax']?>" step=".01">
                                    <div class="form-text">Enter percentage (e.g., 10%) or fixed amount</div>
                                </div>
                            </div>
                        </div>
                        <?php if ($page == 'Edit'): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="paid_total" class="form-label">Paid Total</label>
                                    <input type="number" class="form-control" id="paid_total" name="paid_total" placeholder="Paid Total" value="<?=$invoice['paid_total']?>" step=".01">
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </fieldset>

                    <!-- Template and Recurrence -->
                    <fieldset class="mb-4">
                        <legend class="h6 text-primary border-bottom pb-1">Template and Recurrence</legend>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="template" class="form-label">Template</label>
                                    <select class="form-select" id="template" name="template">
                                        <?php foreach ($templates as $template): ?>
                                        <option value="<?=basename($template)?>"<?=$invoice['invoice_template']==basename($template)?' selected':''?>><?=basename($template)?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="recurrence" class="form-label">Recurrence</label>
                                    <select class="form-select" id="recurrence" name="recurrence">
                                        <option value="0"<?=$invoice['recurrence']==0?' selected':''?>>No</option>
                                        <option value="1"<?=$invoice['recurrence']==1?' selected':''?>>Yes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="recurrence-options"<?=$invoice['recurrence']==0 ? ' style="display:none"' : ''?>>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="recurrence_period" class="form-label">Recurrence Period</label>
                                        <input type="number" class="form-control" id="recurrence_period" name="recurrence_period" placeholder="Recurrence Period" min="1" value="<?=$invoice['recurrence_period']?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="recurrence_period_type" class="form-label">Recurrence Type</label>
                                        <select class="form-select" id="recurrence_period_type" name="recurrence_period_type">
                                            <option value="day"<?=$invoice['recurrence_period_type']=='day'?' selected':''?>>Day</option>
                                            <option value="week"<?=$invoice['recurrence_period_type']=='week'?' selected':''?>>Week</option>
                                            <option value="month"<?=$invoice['recurrence_period_type']=='month'?' selected':''?>>Month</option>
                                            <option value="year"<?=$invoice['recurrence_period_type']=='year'?' selected':''?>>Year</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <!-- Notes -->
                    <fieldset class="mb-4">
                        <legend class="h6 text-primary border-bottom pb-1">Notes</legend>
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="Additional notes or comments"><?=htmlspecialchars($invoice['notes'], ENT_QUOTES)?></textarea>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>

                <!-- Items Tab -->
                <div id="items" class="tab-content" role="tabpanel" aria-labelledby="items-tab-btn">
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Invoice Items</h6>
                        <button type="button" class="btn btn-success btn-sm add-item">
                            <i class="bi bi-plus me-1" aria-hidden="true"></i>Add Item
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover manage-invoice-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th class="text-center" style="width: 80px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($invoice_items)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="bi bi-inbox-fill fs-2 mb-2 d-block" aria-hidden="true"></i>
                                        There are no invoice items.
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($invoice_items as $item): ?>
                                <tr>
                                    <td>
                                        <input type="hidden" name="item_id[]" value="<?=$item['id']?>">
                                        <input type="text" class="form-control form-control-sm" name="item_name[]" placeholder="Name" value="<?=htmlspecialchars($item['item_name'], ENT_QUOTES)?>">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="item_description[]" placeholder="Description" value="<?=htmlspecialchars($item['item_description'], ENT_QUOTES)?>">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm" name="item_price[]" placeholder="Price" value="<?=$item['item_price']?>" step=".01">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm" name="item_quantity[]" placeholder="Quantity" value="<?=$item['item_quantity']?>">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm delete-item" title="Delete Item">
                                            <i class="bi bi-trash" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Bottom Action Buttons -->
    <div class="mt-4">
        <div class="d-flex gap-2">
            <a href="invoices.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Cancel
            </a>
            <button type="submit" name="submit" class="btn btn-success" form="invoice-form">
                <i class="bi bi-save me-1" aria-hidden="true"></i>Save Invoice
            </button>
            <?php if ($page == 'Edit'): ?>
            <button type="submit" name="delete" class="btn btn-danger" form="invoice-form" onclick="return confirm('Are you sure you want to delete this invoice?')">
                <i class="bi bi-trash me-1" aria-hidden="true"></i>Delete
            </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    /* Tab Navigation */
    .tab-nav {
        display: flex;
        border-bottom: 2px solid #dee2e6;
        margin-bottom: 0;
        position: relative;
        background-color: transparent;
        padding: 1rem 0 0 0;
    }

    .tab-btn {
        background: #f8f9fa;
        border: 2px solid #dee2e6;
        border-bottom: 2px solid #dee2e6;
        padding: 12px 24px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        color: #6c757d;
        transition: all 0.3s ease;
        border-radius: 8px 8px 0 0;
        margin-right: 4px;
        position: relative;
        outline: none;
    }

    .tab-btn:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25);
    }

    .tab-btn:hover {
        color: #495057;
        background-color: #e9ecef;
        border-color: #adb5bd;
        border-bottom-color: #adb5bd;
    }

    .tab-btn.active {
        color: #0d6efd;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 transparent;
        position: relative;
        z-index: 2;
        font-weight: 600;
        border-bottom: 2px solid #fff;
        margin-bottom: -2px;
    }

    .tab-btn[aria-selected="true"] {
        color: #0d6efd;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 transparent;
        border-bottom: 2px solid #fff;
        margin-bottom: -2px;
    }

    /* Tab Content */
    .tab-content {
        display: none;
        padding: 30px;
        background-color: #fff;
        border: 2px solid #dee2e6;
        border-top: none;
        border-radius: 0 8px 8px 8px;
        margin-top: 0;
        margin-left: 0;
    }

    .tab-content.active {
        display: block;
    }
</style>

<script>
    function openTab(evt, tabName) {
        // Declare variables
        var i, tabcontent, tablinks;

        // Get all elements with class="tab-content" and hide them
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].classList.remove("active");
        }

        // Get all elements with class="tab-btn" and remove the class "active"
        tablinks = document.getElementsByClassName("tab-btn");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove("active");
            tablinks[i].setAttribute("aria-selected", "false");
        }

        // Show the current tab, and add an "active" class to the button that opened the tab
        document.getElementById(tabName).classList.add("active");
        evt.currentTarget.classList.add("active");
        evt.currentTarget.setAttribute("aria-selected", "true");
    }

    // Keyboard navigation for tabs
    document.addEventListener('keydown', function(e) {
        const target = e.target;
        if (target.classList.contains('tab-btn')) {
            const tabs = Array.from(document.querySelectorAll('.tab-btn'));
            const currentIndex = tabs.indexOf(target);
            
            if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
                e.preventDefault();
                let nextIndex;
                
                if (e.key === 'ArrowRight') {
                    nextIndex = (currentIndex + 1) % tabs.length;
                } else {
                    nextIndex = (currentIndex - 1 + tabs.length) % tabs.length;
                }
                
                tabs[nextIndex].focus();
                tabs[nextIndex].click();
            }
        }
    });
</script>

<?=template_admin_footer('<script>initManageInvoiceItems()</script>')?>