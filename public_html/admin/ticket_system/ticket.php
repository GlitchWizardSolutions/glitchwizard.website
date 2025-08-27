<?php
include 'main.php';

// Check if this is view mode
if (isset($_GET['view'])) {
    // View mode functionality similar to public view.php but for admin
    $ticket_id = $_GET['view'];
    
    // Retrieve the ticket from the database
    $stmt = $pdo->prepare('SELECT t.*, a.full_name AS a_name, a.email AS a_email, c.title AS category FROM tickets t LEFT JOIN tickets_categories c ON c.id = t.category_id LEFT JOIN accounts a ON a.id = t.account_id WHERE t.id = ?');
    $stmt->execute([ $ticket_id ]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Check if ticket exists
    if (!$ticket) {
        exit('Invalid ticket ID!');
    }
    
    // Retrieve ticket uploads from the database
    $stmt = $pdo->prepare('SELECT * FROM tickets_uploads WHERE ticket_id = ?');
    $stmt->execute([ $ticket_id ]);
    $ticket_uploads = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Update status
    if (isset($_GET['status']) && in_array($_GET['status'], ['closed', 'resolved']) && $ticket['ticket_status'] == 'open') {
        $stmt = $pdo->prepare('UPDATE tickets SET ticket_status = ? WHERE id = ?');
        $stmt->execute([ $_GET['status'], $ticket_id ]);
        header('Location: ticket.php?view=' . $ticket_id);
        exit;
    }
    
    // Check if the comment form has been submitted
    if (isset($_POST['msg']) && !empty($_POST['msg']) && $ticket['ticket_status'] == 'open') {
        // Insert the new comment into the "tickets_comments" table with reply tracking
        $stmt = $pdo->prepare('INSERT INTO tickets_comments (ticket_id, msg, account_id, reply) VALUES (?, ?, ?, ?)');
        $stmt->execute([ $ticket_id, $_POST['msg'], $_SESSION['account_id'], $_SESSION['account_id'] ]);
        header('Location: ticket.php?view=' . $ticket_id);
        exit;
    }
    
    // Retrieve the ticket comments from the database
    $stmt = $pdo->prepare('SELECT tc.*, a.full_name, a.role FROM tickets_comments tc LEFT JOIN accounts a ON a.id = tc.account_id WHERE tc.ticket_id = ? ORDER BY tc.created');
    $stmt->execute([ $ticket_id ]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $page = 'View';
} else {
    // Original edit/create functionality
    // Retrieve accounts from the database
    $accounts = $pdo->query('SELECT * FROM accounts')->fetchAll(PDO::FETCH_ASSOC);
    // Retrieve categories from the database
    $categories = $pdo->query('SELECT * FROM tickets_categories')->fetchAll(PDO::FETCH_ASSOC);
    
    // Default ticket values
    $ticket = [
        'title' => '',
        'msg' => '',
        'full_name' => '',
        'email' => '',
        'created' => date('Y-m-d H:i:s'),
        'ticket_status' => 'open',
        'priority' => 'low',
        'category_id' => 1,
        'private' => 0,
        'account_id' => 0,
        'approved' => 1
    ];
    
    // Check whether the ticket ID is specified
    if (isset($_GET['id'])) {
        // Retrieve the ticket from the database
    $stmt = $pdo->prepare('SELECT * FROM tickets WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    // ID param exists, edit an existing ticket
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Update the ticket
        $stmt = $pdo->prepare('UPDATE tickets SET title = ?, msg = ?, full_name = ?, email = ?, created = ?, ticket_status = ?, priority = ?, category_id = ?, private = ?, account_id = ?, approved = ? WHERE id = ?');
        $stmt->execute([ $_POST['title'], $_POST['msg'], $_POST['full_name'], $_POST['email'], date('Y-m-d H:i:s', strtotime($_POST['created'])), $_POST['ticket_status'], $_POST['priority'], $_POST['category_id'], $_POST['private'], $_POST['account_id'], $_POST['approved'], $_GET['id'] ]);
        header('Location: tickets.php?success_msg=2');
        exit;
    }
    if (isset($_POST['delete'])) {
        // Redirect and delete the ticket
        header('Location: tickets.php?delete=' . $_GET['id']);
        exit;
    }
} else {
    // Create a new ticket
    $page = 'Create';
    if (isset($_POST['submit'])) {
        $stmt = $pdo->prepare('INSERT INTO tickets (title, msg, full_name, email, created, ticket_status, priority, category_id, private, account_id, approved) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([ $_POST['title'], $_POST['msg'], $_POST['full_name'], $_POST['email'], date('Y-m-d H:i:s', strtotime($_POST['created'])), $_POST['ticket_status'], $_POST['priority'], $_POST['category_id'], $_POST['private'], $_POST['account_id'], $_POST['approved'] ]);
        header('Location: tickets.php?success_msg=1');
        exit;
    }
} // Close the main else block
} // Close the main if (view) else (edit/create) structure
?>
<?=template_admin_header($page . ' Ticket', 'tickets', 'view')?>

<?php if ($page == 'View'): ?>
<!-- View Mode Template -->
<div class="content-title mb-4" id="main-ticket-view" role="banner" aria-label="View Ticket Header">
    <div class="title">
        <div class="icon">
            <i class="bi bi-ticket-perforated" aria-hidden="true"></i>
        </div>
        <div class="txt">
            <h2><?=htmlspecialchars($ticket['title'], ENT_QUOTES)?> <span class="badge bg-<?=$ticket['ticket_status']=='resolved'?'success':($ticket['ticket_status']=='closed'?'danger':'secondary')?>"><?=ucwords($ticket['ticket_status'])?></span></h2>
            <p>View ticket details and manage status.</p>
        </div>
    </div>
</div>

<div class="mb-4">
</div>

<div class="d-flex gap-2 mb-4" role="region" aria-label="Page Actions">
    <a href="tickets.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
        Cancel
    </a>
    <a href="ticket.php?id=<?=$ticket['id']?>" class="btn btn-success">
        <i class="bi bi-pencil-square me-1" aria-hidden="true"></i>
        Edit Ticket
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <strong>User:</strong> <?=htmlspecialchars($ticket['a_name'] ?? $ticket['full_name'], ENT_QUOTES)?>
                <br><strong>Email:</strong> <?=$ticket['a_email'] ?? $ticket['email']?>
                <br><strong>Status:</strong> <span class="badge bg-<?=$ticket['ticket_status']=='resolved'?'success':($ticket['ticket_status']=='closed'?'danger':'secondary')?>"><?=ucwords($ticket['ticket_status'])?></span>
            </div>
            <div class="col-md-6">
                <strong>Priority:</strong> <span class="badge bg-<?=$ticket['priority']=='low'?'success':($ticket['priority']=='high'?'danger':'warning')?>"><?=ucwords($ticket['priority'])?></span>
                <br><strong>Category:</strong> <?=$ticket['category'] ?? 'None'?>
                <br><strong>Created:</strong> <?=date('F j, Y g:ia', strtotime($ticket['created']))?>
            </div>
        </div>
        <div class="mb-3">
            <strong>Message:</strong>
            <div class="mt-2 p-3 bg-light border rounded">
                <?=nl2br(htmlspecialchars($ticket['msg'], ENT_QUOTES))?>
            </div>
        </div>
        
        <?php if (!empty($ticket_uploads)): ?>
        <div class="mb-3">
            <strong>Attachments:</strong>
            <div class="row mt-2">
                <?php foreach($ticket_uploads as $upload): ?>
                <div class="col-md-3 mb-2">
                    <a href="<?=$upload['filepath']?>" target="_blank" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-file-earmark"></i> <?=basename($upload['filepath'])?>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Admin Actions -->
        <div class="row mb-3">
            <div class="col-md-6">
                <h6>Change Status:</h6>
                <?php if ($ticket['ticket_status'] == 'open'): ?>
                <a href="ticket.php?view=<?=$ticket['id']?>&status=resolved" class="btn btn-success btn-sm me-2" onclick="return confirm('Mark ticket as resolved?')">
                    <i class="bi bi-check-lg me-1" aria-hidden="true"></i>
                    Mark Resolved
                </a>
                <a href="ticket.php?view=<?=$ticket['id']?>&status=closed" class="btn btn-danger btn-sm" onclick="return confirm('Close ticket?')">
                    <i class="bi bi-x-lg me-1" aria-hidden="true"></i>
                    Close Ticket
                </a>
                <?php elseif ($ticket['ticket_status'] == 'resolved'): ?>
                <span class="text-success">✓ Ticket is resolved</span>
                <a href="ticket.php?view=<?=$ticket['id']?>&status=closed" class="btn btn-danger btn-sm ms-2" onclick="return confirm('Close ticket?')">
                    <i class="bi bi-x-lg me-1" aria-hidden="true"></i>
                    Close
                </a>
                <?php else: ?>
                <span class="text-muted">Ticket is closed</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Comments Section -->
<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Comments (<?=count($comments)?>)</h6>
        <?php if ($ticket['ticket_status'] == 'open'): ?>
        <small class="text-success">✓ Can add comments</small>
        <?php else: ?>
        <small class="text-muted">Read-only (ticket closed)</small>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <!-- Add Comment Form - Show at top for better visibility -->
        <?php if ($ticket['ticket_status'] == 'open'): ?>
        <div class="mb-4 p-3 bg-light border rounded">
            <form action="" method="post">
                <div class="mb-3">
                    <label for="msg" class="form-label"><strong>Add Admin Comment</strong></label>
                    <textarea id="msg" name="msg" class="form-control" rows="4" placeholder="Enter your comment as admin..." required></textarea>
                </div>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-send me-1" aria-hidden="true"></i>
                    Post Comment
                </button>
            </form>
        </div>
        <?php endif; ?>
        
        <!-- Existing Comments -->
        <h6>Comment History:</h6>
        <?php if (empty($comments)): ?>
        <p class="text-muted fst-italic">No comments yet. Be the first to comment!</p>
        <?php else: ?>
        <?php foreach($comments as $comment): ?>
        <div class="border-bottom mb-3 pb-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <strong class="<?=$comment['role'] == 'Admin' ? 'text-primary' : 'text-secondary'?>"><?=htmlspecialchars($comment['full_name'], ENT_QUOTES)?></strong>
                    <?php if ($comment['role'] == 'Admin'): ?><span class="badge bg-primary ms-1">Admin</span><?php endif; ?>
                    <br><small class="text-muted"><?=date('F j, Y g:ia', strtotime($comment['created']))?></small>
                </div>
            </div>
            <div class="mt-2"><?=nl2br(htmlspecialchars($comment['msg'], ENT_QUOTES))?></div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php else: ?>
<!-- Edit/Create Mode Template -->
<div class="content-title mb-4" id="main-ticket-form" role="banner" aria-label="<?=$page?> Ticket Header">
    <div class="title">
        <div class="icon">
            <i class="bi bi-ticket-perforated" aria-hidden="true"></i>
        </div>
        <div class="txt">
            <h2><?=$page?> Ticket</h2>
            <p><?=$page == 'Edit' ? 'Modify ticket details and update status.' : 'Create a new support ticket with details.'?></p>
        </div>
    </div>
</div>

<div class="mb-4">
</div>

<form action="" method="post" role="form" aria-labelledby="form-title">

<div class="d-flex gap-2 mb-4" role="region" aria-label="Page Actions">
    <a href="tickets.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
        Cancel
    </a>
    <button type="submit" name="submit" class="btn btn-success">
        <i class="bi bi-save me-1" aria-hidden="true"></i>
        <?=$page == 'Edit' ? 'Save Ticket' : 'Create Ticket'?>
    </button>
    <?php if ($page == 'Edit'): ?>
    <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this ticket?')">
        <i class="bi bi-trash me-1" aria-hidden="true"></i>
        Delete Ticket
    </button>
    <?php endif; ?>
</div>

    <div class="card">
        <h6 class="card-header"><?=$page == 'Edit' ? 'Edit Ticket' : 'Create Ticket'?></h6>
        <div class="card-body">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="title" class="form-label">
                            <span class="required" aria-hidden="true">*</span> Title
                            <span class="sr-only">(required)</span>
                        </label>
                        <input id="title" type="text" name="title" class="form-control" 
                               placeholder="Enter ticket title" 
                               value="<?=htmlspecialchars($ticket['title'], ENT_QUOTES)?>" 
                               required aria-required="true">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="created" class="form-label">
                            <span class="required" aria-hidden="true">*</span> Created
                            <span class="sr-only">(required)</span>
                        </label>
                        <input id="created" type="datetime-local" name="created" class="form-control"
                               value="<?=date('Y-m-d\TH:i', strtotime($ticket['created']))?>" 
                               required aria-required="true">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="msg" class="form-label">
                    <span class="required" aria-hidden="true">*</span> Message
                    <span class="sr-only">(required)</span>
                </label>
                <textarea id="msg" name="msg" class="form-control" rows="4"
                          placeholder="Enter the ticket message..." 
                          required aria-required="true"><?=htmlspecialchars($ticket['msg'], ENT_QUOTES)?></textarea>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">
                            <span class="required" aria-hidden="true">*</span> Full Name
                            <span class="sr-only">(required)</span>
                        </label>
                        <input id="full_name" type="text" name="full_name" class="form-control" 
                               placeholder="Enter full name" 
                               value="<?=htmlspecialchars($ticket['full_name'], ENT_QUOTES)?>" 
                               required aria-required="true">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <span class="required" aria-hidden="true">*</span> Email
                            <span class="sr-only">(required)</span>
                        </label>
                        <input id="email" type="email" name="email" class="form-control" 
                               placeholder="Enter email address" 
                               value="<?=htmlspecialchars($ticket['email'], ENT_QUOTES)?>" 
                               required aria-required="true">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="ticket_status" class="form-label">
                            <span class="required" aria-hidden="true">*</span> Status
                            <span class="sr-only">(required)</span>
                        </label>
                        <select id="ticket_status" name="ticket_status" class="form-select" required aria-required="true">
                            <option value="open"<?=$ticket['ticket_status']=='open'?' selected':''?>>Open</option>
                             
                            <option value="resolved"<?=$ticket['ticket_status']=='resolved'?' selected':''?>>Resolved</option>
                            <option value="closed"<?=$ticket['ticket_status']=='closed'?' selected':''?>>Closed</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="priority" class="form-label">
                            <span class="required" aria-hidden="true">*</span> Priority
                            <span class="sr-only">(required)</span>
                        </label>
                        <select id="priority" name="priority" class="form-select" required aria-required="true">
                            <option value="low"<?=$ticket['priority']=='low'?' selected':''?>>Low</option>
                            <option value="medium"<?=$ticket['priority']=='medium'?' selected':''?>>Medium</option>
                            <option value="high"<?=$ticket['priority']=='high'?' selected':''?>>High</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="category_id" class="form-label">
                            <span class="required" aria-hidden="true">*</span> Category
                            <span class="sr-only">(required)</span>
                        </label>
                        <select id="category_id" name="category_id" class="form-select" required aria-required="true">
                            <?php foreach ($categories as $category): ?>
                            <option value="<?=$category['id']?>"<?=$ticket['category_id']==$category['id']?' selected':''?>><?=$category['title']?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="private" class="form-label">
                            <span class="required" aria-hidden="true">*</span> Private
                            <span class="sr-only">(required)</span>
                        </label>
                        <select id="private" name="private" class="form-select" required aria-required="true">
                            <option value="0"<?=$ticket['private']==0?' selected':''?>>No</option>
                            <option value="1"<?=$ticket['private']==1?' selected':''?>>Yes</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="account_id" class="form-label">Account</label>
                        <select id="account_id" name="account_id" class="form-select">
                            <option value="0">(none)</option>
                            <?php foreach ($accounts as $account): ?>
                            <option value="<?=$account['id']?>"<?=$ticket['account_id']==$account['id']?' selected':''?>><?=$account['id']?> - <?=$account['email']?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="approved" class="form-label">
                            <span class="required" aria-hidden="true">*</span> Approved
                            <span class="sr-only">(required)</span>
                        </label>
                        <select id="approved" name="approved" class="form-select" required aria-required="true">
                            <option value="0"<?=$ticket['approved']==0?' selected':''?>>No</option>
                            <option value="1"<?=$ticket['approved']==1?' selected':''?>>Yes</option>
                        </select>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Bottom buttons -->
    <div class="d-flex gap-2 pt-3 border-top mt-4">
        <a href="tickets.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
            Cancel
        </a>
        <button type="submit" name="submit" class="btn btn-success">
            <i class="bi bi-save me-1" aria-hidden="true"></i>
            <?=$page == 'Edit' ? 'Save Ticket' : 'Create Ticket'?>
        </button>
        <?php if ($page == 'Edit'): ?>
        <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this ticket?')">
            <i class="bi bi-trash me-1" aria-hidden="true"></i>
            Delete Ticket
        </button>
        <?php endif; ?>
    </div>

</form>

<?php endif; ?>

<?=template_admin_footer()?>