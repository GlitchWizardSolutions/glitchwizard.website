<?php
include 'main.php';

// Retrieve the GET request parameters (if specified)
$pagination_page = isset($_GET['pagination_page']) ? $_GET['pagination_page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
// Filters
$status = isset($_GET['status']) ? $_GET['status'] : '';
$priority = isset($_GET['priority']) ? $_GET['priority'] : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : ''; // Only filter if explicitly set
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : ''; // Only filter if explicitly set

// Delete ticket
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE t, tc, tu FROM tickets t LEFT JOIN tickets_comments tc ON tc.ticket_id = t.id LEFT JOIN tickets_uploads tu ON tu.ticket_id = t.id WHERE t.id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    header('Location: tickets.php?success_msg=3');
    exit;
}
// Approve ticket
if (isset($_GET['approve'])) {
    $stmt = $pdo->prepare('UPDATE tickets SET approved = 1 WHERE id = ?');
    $stmt->execute([ $_GET['approve'] ]);
    header('Location: tickets.php?success_msg=2');
    exit;
}
// Toggle private status
if (isset($_GET['toggle_private'])) {
    $stmt = $pdo->prepare('UPDATE tickets SET private = 1 - private WHERE id = ?');
    $stmt->execute([ $_GET['toggle_private'] ]);
    header('Location: tickets.php?success_msg=4');
    exit;
}
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['id','title','msg','full_name','email','created','ticket_status','priority','category_id','category','approved','private','account_id','num_comments','reply'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'id';
// Number of results per pagination page
$results_per_page = 15;
// Declare query param variables
$param1 = ($pagination_page - 1) * $results_per_page;
$param2 = $results_per_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE (t.full_name LIKE :search OR t.email LIKE :search OR t.id LIKE :search) ' : '';
if (isset($_GET['acc_id'])) {
    $where .= $where ? ' AND t.account_id = :acc_id ' : ' WHERE t.account_id = :acc_id ';
} 
if ($status) {
    $where .= $where ? ' AND t.ticket_status = :status ' : ' WHERE t.ticket_status = :status ';
}
if ($priority) {
    $where .= $where ? ' AND t.priority = :priority ' : ' WHERE t.priority = :priority ';
}
// Retrieve the total number of tickets from the database
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM tickets t ' . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if (isset($_GET['acc_id'])) $stmt->bindParam('acc_id', $_GET['acc_id'], PDO::PARAM_INT);
if ($status) $stmt->bindParam('status', $status, PDO::PARAM_STR);
if ($priority) $stmt->bindParam('priority', $priority, PDO::PARAM_STR);
$stmt->execute();
$tickets_total = $stmt->fetchColumn();
// SQL query to get all tickets from the "tickets" table
$stmt = $pdo->prepare('SELECT t.*, (SELECT COUNT(tc.id) FROM tickets_comments tc WHERE tc.ticket_id = t.id) AS num_comments, (SELECT GROUP_CONCAT(tu.filepath) FROM tickets_uploads tu WHERE tu.ticket_id = t.id) AS imgs, (SELECT tc.reply FROM tickets_comments tc WHERE tc.ticket_id = t.id ORDER BY tc.created DESC LIMIT 1) AS reply, c.title AS category, a.full_name AS p_full_name, a.email AS a_email FROM tickets t LEFT JOIN tickets_categories c ON c.id = t.category_id LEFT JOIN accounts a ON t.account_id = a.id ' . $where . ' ORDER BY ' . $order_by . ' ' . $order . ' LIMIT :start_results,:num_results');
// Bind params
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if (isset($_GET['acc_id'])) $stmt->bindParam('acc_id', $_GET['acc_id'], PDO::PARAM_INT);
if ($status) $stmt->bindParam('status', $status, PDO::PARAM_STR);
if ($priority) $stmt->bindParam('priority', $priority, PDO::PARAM_STR);
if ($start_date && $end_date) {
    $stmt->bindParam('start_date', $start_date, PDO::PARAM_STR);
    $stmt->bindParam('end_date', $end_date, PDO::PARAM_STR);
}
$stmt->execute();
// Retrieve query results
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Ticket created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Ticket updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Ticket deleted successfully!';
    }
    if ($_GET['success_msg'] == 4) {
        $success_msg = $_GET['imported'] . ' ticket(s) imported successfully!';
    }
}
// Determine the URL
$url = 'tickets.php?search=' . $search . (isset($_GET['page_id']) ? '&page_id=' . $_GET['page_id'] : '') . (isset($_GET['acc_id']) ? '&acc_id=' . $_GET['acc_id'] : '') . (isset($_GET['status']) ? '&status=' . $_GET['status'] : '') . '&start_date=' . $start_date . '&end_date=' . $end_date;
?>
<?=template_admin_header('Tickets', 'tickets', 'view')?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-4 px-4 branding-settings-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-white">
                    <span class="header-icon"><i class="bi bi-ticket-perforated" aria-hidden="true"></i></span>
                    Tickets Management
                </h6>
                <span class="text-white" style="font-size: 0.875rem;">Support System</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="container-fluid py-3 px-4">

<?php if (isset($success_msg)): ?>
<div class="mb-4" role="region" aria-label="Success Message">
    <div class="msg success" role="alert" aria-live="polite">
    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
        <p><?=$success_msg?></p>
        <button type="button" class="close-success" aria-label="Dismiss success message" onclick="this.parentElement.parentElement.style.display='none'">
            <i class="bi bi-x-lg" aria-hidden="true"></i>
        </button>
    </div>
</div>
<?php endif; ?>

<!-- Top page actions -->
<div class="d-flex gap-2 mb-4" role="region" aria-label="Page Actions">
    <a href="ticket.php" class="btn btn-outline-secondary">
        <i class="bi bi-plus me-1" aria-hidden="true"></i>
        Add Ticket
    </a>
    <a href="tickets_table_transfer.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left-right me-1" aria-hidden="true"></i>
        Import/Export
    </a>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Ticket Management</h6>
        <small class="text-muted"><?= number_format($tickets_total) ?> total tickets</small>
    </div>
    <div class="card-body p-0">
        <div class="table-filters-wrapper p-3">
            <form action="" method="get" class="mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input id="search" type="text" name="search" class="form-control" placeholder="Search tickets..." value="<?= htmlspecialchars($search, ENT_QUOTES) ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="" <?= $status == '' ? ' selected' : '' ?>>All</option>
                            <option value="open" <?= $status == 'open' ? ' selected' : '' ?>>Open</option>
                            <option value="in progress" <?= $status == 'in progress' ? ' selected' : '' ?>>In Progress</option>
                            <option value="resolved" <?= $status == 'resolved' ? ' selected' : '' ?>>Resolved</option>
                            <option value="closed" <?= $status == 'closed' ? ' selected' : '' ?>>Closed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="priority" class="form-label">Priority</label>
                        <select name="priority" id="priority" class="form-select">
                            <option value="" <?= !isset($_GET['priority']) || $_GET['priority'] == '' ? ' selected' : '' ?>>All</option>
                            <option value="high" <?= isset($_GET['priority']) && $_GET['priority'] == 'high' ? ' selected' : '' ?>>High</option>
                            <option value="medium" <?= isset($_GET['priority']) && $_GET['priority'] == 'medium' ? ' selected' : '' ?>>Medium</option>
                            <option value="low" <?= isset($_GET['priority']) && $_GET['priority'] == 'low' ? ' selected' : '' ?>>Low</option>
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
            <?php if ($status || $priority || $search): ?>
            <div class="mb-3">
                <h6 class="mb-2">Active Filters:</h6>
                <div class="d-flex flex-wrap gap-2">
                    <?php if ($status): ?>
                        <span class="badge bg-secondary">
                            Status: <?= htmlspecialchars($status, ENT_QUOTES) ?>
                            <a href="<?= remove_url_param($url, 'status') ?>" class="text-white ms-1" aria-label="Remove status filter">×</a>
                        </span>
                    <?php endif; ?>
                    <?php if ($priority): ?>
                        <span class="badge bg-secondary">
                            Priority: <?= htmlspecialchars($priority, ENT_QUOTES) ?>
                            <a href="<?= remove_url_param($url, 'priority') ?>" class="text-white ms-1" aria-label="Remove priority filter">×</a>
                        </span>
                    <?php endif; ?>
                    <?php if ($search): ?>
                        <span class="badge bg-secondary">
                            Search: <?= htmlspecialchars($search, ENT_QUOTES) ?>
                            <a href="<?= remove_url_param($url, 'search') ?>" class="text-white ms-1" aria-label="Remove search filter">×</a>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-start" style="width: 80px; padding-right: 20px;"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=id'?>" class="text-decoration-none">#<?php if ($order_by=='id'): ?><span aria-label="Sorted <?= $order == 'ASC' ? 'ascending' : 'descending' ?>"><?= $order == 'ASC' ? '▲' : '▼' ?></span><?php endif; ?></a></th>
                        <th class="text-start"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=full_name'?>" class="text-decoration-none">User<?php if ($order_by=='full_name'): ?><span aria-label="Sorted <?= $order == 'ASC' ? 'ascending' : 'descending' ?>"><?= $order == 'ASC' ? '▲' : '▼' ?></span><?php endif; ?></a></th>
                        <th class="text-start" style="padding-right: 10px;"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=title'?>" class="text-decoration-none">Title<?php if ($order_by=='title'): ?><span aria-label="Sorted <?= $order == 'ASC' ? 'ascending' : 'descending' ?>"><?= $order == 'ASC' ? '▲' : '▼' ?></span><?php endif; ?></a></th>
                        <th class="text-start"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=ticket_status'?>" class="text-decoration-none">Status<?php if ($order_by=='ticket_status'): ?><span aria-label="Sorted <?= $order == 'ASC' ? 'ascending' : 'descending' ?>"><?= $order == 'ASC' ? '▲' : '▼' ?></span><?php endif; ?></a></th>
                        <th class="text-center d-none d-md-table-cell"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=reply'?>" class="text-decoration-none">Reply<?php if ($order_by=='reply'): ?><span aria-label="Sorted <?= $order == 'ASC' ? 'ascending' : 'descending' ?>"><?= $order == 'ASC' ? '▲' : '▼' ?></span><?php endif; ?></a></th>
                        <th class="text-center d-none d-lg-table-cell"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=priority'?>" class="text-decoration-none">Priority<?php if ($order_by=='priority'): ?><span aria-label="Sorted <?= $order == 'ASC' ? 'ascending' : 'descending' ?>"><?= $order == 'ASC' ? '▲' : '▼' ?></span><?php endif; ?></a></th>
                        <th class="text-center d-none d-lg-table-cell"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=category'?>" class="text-decoration-none">Category<?php if ($order_by=='category'): ?><span aria-label="Sorted <?= $order == 'ASC' ? 'ascending' : 'descending' ?>"><?= $order == 'ASC' ? '▲' : '▼' ?></span><?php endif; ?></a></th>
                        <th class="text-center"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=approved'?>" class="text-decoration-none">Approved<?php if ($order_by=='approved'): ?><span aria-label="Sorted <?= $order == 'ASC' ? 'ascending' : 'descending' ?>"><?= $order == 'ASC' ? '▲' : '▼' ?></span><?php endif; ?></a></th>
                        <th class="text-center d-none d-md-table-cell"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=created'?>" class="text-decoration-none">Date<?php if ($order_by=='created'): ?><span aria-label="Sorted <?= $order == 'ASC' ? 'ascending' : 'descending' ?>"><?= $order == 'ASC' ? '▲' : '▼' ?></span><?php endif; ?></a></th>
                        <th class="text-center" style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($tickets)): ?>
                <tr>
                    <td colspan="10" class="text-center py-4">There are no tickets.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($tickets as $ticket): ?>
                <tr role="row">
                    <td><?=$ticket['id']?></td>
                    <td><?=htmlspecialchars($ticket['p_full_name'] ?? $ticket['full_name'], ENT_QUOTES)?></td>
                    <td><?=htmlspecialchars($ticket['title'], ENT_QUOTES)?></td>
                    <td>
                        <?php
                        // Status with CSS classes like accounts.php
                        if ($ticket['ticket_status'] == 'resolved') {
                            echo '<span class="grey">Resolved</span>';
                        } elseif ($ticket['ticket_status'] == 'closed') {
                            echo '<span class="red">Closed</span>';
                        } elseif ($ticket['ticket_status'] == 'open') {
                            echo '<span class="green">Open</span>';
                        } elseif ($ticket['ticket_status'] == 'in progress') {
                            echo '<span class="orange">In Progress</span>';
                        } else {
                            echo '<span class="orange">'. ucwords($ticket['ticket_status']) .'</span>';
                        }
                        ?>
                    </td>
                    <td class="text-center d-none d-md-table-cell">
                        <?php
                        // Reply status: null/1 = no activity, admin account_id = admin responded, other = customer responded
                        if (!isset($ticket['reply']) || $ticket['reply'] == 1) {
                            echo '<i class="bi bi-circle text-muted" title="No activity"></i>';
                        } elseif ($ticket['reply'] == $_SESSION['account_id']) {
                            echo '<i class="bi bi-person-gear text-success" title="Admin responded last"></i>';
                        } else {
                            echo '<i class="bi bi-person text-warning" title="Waiting for admin response"></i>';
                        }
                        ?>
                    </td>
                    <td class="text-center d-none d-lg-table-cell">
                        <?php
                        // Priority with CSS classes like accounts.php
                        if ($ticket['priority'] == 'high') {
                            echo '<span class="red">High</span>';
                        } elseif ($ticket['priority'] == 'medium') {
                            echo '<span class="orange">Medium</span>';
                        } elseif ($ticket['priority'] == 'low') {
                            echo '<span class="green">Low</span>';
                        } else {
                            echo '<span class="grey">'. ucwords($ticket['priority']) .'</span>';
                        }
                        ?>
                    </td>
                    <td class="d-none d-lg-table-cell"><?=$ticket['category'] ? htmlspecialchars($ticket['category'], ENT_QUOTES) : 'None'?></td>
                    <td class="text-center"><?=$ticket['approved'] ? '<i class="bi bi-check text-success"></i>' : '<i class="bi bi-x text-danger"></i>'?></td>
                    <td class="d-none d-md-table-cell"><?=date('m/d/Y', strtotime($ticket['created']))?></td>
                    <td class="actions" style="text-align: center;">
                        <div class="table-dropdown">
                            <button class="actions-btn" aria-haspopup="true" aria-expanded="false" aria-label="Actions for ticket #<?=$ticket['id']?>">
                                <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">
                                    <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z" />
                                </svg>
                            </button>
                            <div class="table-dropdown-items" role="menu" aria-label="Ticket Actions">
                                <div role="menuitem">
                                    <a href="ticket.php?view=<?=$ticket['id']?>" class="blue" tabindex="-1" aria-label="View ticket #<?=$ticket['id']?>">
                                        <span class="icon" aria-hidden="true">
                                            <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                                <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z" />
                                            </svg>
                                        </span>
                                        <span>View</span>
                                    </a>
                                </div>
                                <div role="menuitem">
                                    <a href="ticket.php?id=<?=$ticket['id']?>" class="green" tabindex="-1" aria-label="Edit ticket #<?=$ticket['id']?>">
                                        <span class="icon" aria-hidden="true">
                                            <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z" />
                                            </svg>
                                        </span>
                                        <span>Edit</span>
                                    </a>
                                </div>
                                <?php if ($ticket['approved'] != 1): ?>
                                <div role="menuitem">
                                    <a class="green" href="tickets.php?approve=<?=$ticket['id']?>" onclick="return confirm('Are you sure you want to approve this ticket?')" tabindex="-1" aria-label="Approve ticket #<?=$ticket['id']?>">
                                        <span class="icon" aria-hidden="true">
                                            <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                                                <path d="M96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM0 482.3C0 383.8 79.8 304 178.3 304h91.4C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7H29.7C13.3 512 0 498.7 0 482.3zM625 177L497 305c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L591 143c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
                                            </svg>
                                        </span>
                                        <span>Approve</span>
                                    </a>
                                </div>
                                <?php endif; ?>
                                <div role="menuitem">
                                    <a class="black" href="tickets.php?toggle_private=<?=$ticket['id']?>" onclick="return confirm('Are you sure you want to <?=$ticket['private'] ? 'make this ticket public' : 'make this ticket private'?>?')" tabindex="-1" aria-label="<?=$ticket['private'] ? 'Make ticket public' : 'Make ticket private'?> #<?=$ticket['id']?>">
                                        <span class="icon" aria-hidden="true">
                                            <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                                                <path d="M38.8 5.1C28.4-3.1 13.3-1.2 5.1 9.2S-1.2 34.7 9.2 42.9l592 464c10.4 8.2 25.5 6.3 33.7-4.1s6.3-25.5-4.1-33.7L353.3 251.6C407.9 237 448 187.2 448 128C448 57.3 390.7 0 320 0C250.2 0 193.5 55.8 192 125.2L38.8 5.1zM264.3 304.3C170.5 309.4 96 387.2 96 482.3c0 16.4 13.3 29.7 29.7 29.7H514.3c3.9 0 7.6-.7 11-2.1l-261-205.6z" />
                                            </svg>
                                        </span>
                                        <span><?=$ticket['private'] ? 'Make Public' : 'Make Private'?></span>
                                    </a>
                                </div>
                                <div role="menuitem">
                                    <a class="red" href="tickets.php?delete=<?=$ticket['id']?>" onclick="return confirm('Are you sure you want to delete this ticket?')" tabindex="-1" aria-label="Delete ticket #<?=$ticket['id']?>">
                                        <span class="icon" aria-hidden="true">
                                            <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                <path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z" />
                                            </svg>
                                        </span>
                                        <span>Delete</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    <div class="card-footer bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Showing <?= count($tickets) ?> of <?= $tickets_total ?> tickets
            </small>
            <nav aria-label="Tickets pagination">
                <div class="d-flex gap-2">
                    <?php if ($pagination_page > 1): ?>
                        <a href="<?= $url ?>&pagination_page=<?= $pagination_page - 1 ?>&order=<?= $order ?>&order_by=<?= $order_by ?>"
                           class="btn btn-sm btn-outline-secondary">Previous</a>
                    <?php endif; ?>
                    <span class="btn btn-sm btn-secondary disabled">
                        Page <?= $pagination_page ?> of <?= ceil($tickets_total / $results_per_page) == 0 ? 1 : ceil($tickets_total / $results_per_page) ?>
                    </span>
                    <?php if ($pagination_page * $results_per_page < $tickets_total): ?>
                        <a href="<?= $url ?>&pagination_page=<?= $pagination_page + 1 ?>&order=<?= $order ?>&order_by=<?= $order_by ?>"
                           class="btn btn-sm btn-outline-secondary">Next</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </div>
            </div>
        </div>
    </div>
</div>

<?=template_admin_footer()?>