<?php
include 'main.php';
// Default input comment values
$comment = [
    'msg' => '',
    'ticket_id' => 0,
    'account_id' => 0,
    'created' => date('Y-m-d\TH:i:s')
];
// Retrieve all accounts from the database
$accounts = $pdo->query('SELECT * FROM accounts')->fetchAll(PDO::FETCH_ASSOC);
// Retrieve all tickets from the database
$tickets = $pdo->query('SELECT * FROM tickets')->fetchAll(PDO::FETCH_ASSOC);
// Check whether the comment ID is specified
if (isset($_GET['id'])) {
    // Retrieve the comment from the database
    $stmt = $pdo->prepare('SELECT * FROM tickets_comments WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);
    // ID param exists, edit an existing comment
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Update the comment
        $stmt = $pdo->prepare('UPDATE tickets_comments SET msg = ?, ticket_id = ?, account_id = ?, created = ? WHERE id = ?');
        $stmt->execute([ $_POST['msg'], $_POST['ticket_id'], $_POST['account_id'], date('Y-m-d H:i:s', strtotime($_POST['created'])), $_GET['id'] ]);
        header('Location: comments.php?success_msg=2');
        exit;
    }
    if (isset($_POST['delete'])) {
        // Delete the comment
        header('Location: comments.php?delete=' . $_GET['id']);
        exit;
    }
} else {
    // Create a new comment
    $page = 'Create';
    if (isset($_POST['submit'])) {
        $stmt = $pdo->prepare('INSERT INTO tickets_comments (msg,ticket_id,account_id,created) VALUES (?,?,?,?)');
        $stmt->execute([ $_POST['msg'], $_POST['ticket_id'], $_POST['account_id'], date('Y-m-d H:i:s', strtotime($_POST['created'])) ]);
        header('Location: comments.php?success_msg=1');
        exit;
    }
}
?>
<?=template_admin_header($page . ' Comment', 'tickets', 'manage')?>

<div class="content-title mb-4">
    <div class="title">
        <div class="icon">
            <i class="bi bi-chat-dots" aria-hidden="true"></i>
        </div>
        <div class="txt">
            <h2><?=$page?> Comment</h2>
            <p><?=$page == 'Edit' ? 'Modify the comment details below.' : 'Add a new comment to the system.'?></p>
        </div>
    </div>
</div>

<!-- Top page actions -->
<div class="d-flex gap-2 mb-4" role="region" aria-label="Page Actions">
    <a href="comments.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
        Cancel
    </a>
    <?php if ($page == 'Edit'): ?>
    <button type="submit" name="delete" form="comment-form" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this comment?')">
        <i class="bi bi-trash me-1" aria-hidden="true"></i>
        Delete
    </button>
    <?php endif; ?>
    <button type="submit" name="submit" form="comment-form" class="btn btn-success">
        <i class="bi bi-save me-1" aria-hidden="true"></i>
        <?=$page == 'Edit' ? 'Update Comment' : 'Create Comment'?>
    </button>
</div>

<form action="" method="post" id="comment-form">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Comment Details</h6>
            <small class="text-muted"><?=$page == 'Edit' ? 'Edit existing comment' : 'Create new comment'?></small>
        </div>
        <div class="card-body">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="msg" class="form-label">
                            <span class="required" aria-hidden="true">*</span> 
                            Message
                            <span class="sr-only">(required)</span>
                        </label>
                        <textarea id="msg" 
                            name="msg" 
                            class="form-control" 
                            rows="4"
                            placeholder="Enter your message..." 
                            required 
                            aria-required="true"><?=htmlspecialchars($comment['msg'], ENT_QUOTES)?></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label for="ticket_id" class="form-label">Ticket</label>
                        <select id="ticket_id" name="ticket_id" class="form-select">
                            <option value="0">(none)</option>
                            <?php foreach ($tickets as $t): ?>
                            <option value="<?=$t['id']?>"<?=$t['id']==$comment['ticket_id']?' selected':''?>><?=$t['id']?> - <?=htmlspecialchars($t['title'], ENT_QUOTES)?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="account_id" class="form-label">Account</label>
                        <select id="account_id" name="account_id" class="form-select">
                            <option value="0">(none)</option>
                            <?php foreach ($accounts as $a): ?>
                            <option value="<?=$a['id']?>"<?=$a['id']==$comment['account_id']?' selected':''?>><?=$a['id']?> - <?=htmlspecialchars($a['email'], ENT_QUOTES)?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="created" class="form-label">
                            <span class="required" aria-hidden="true">*</span> 
                            Created
                            <span class="sr-only">(required)</span>
                        </label>
                        <input id="created" 
                            type="datetime-local" 
                            name="created" 
                            class="form-control"
                            value="<?=date('Y-m-d\TH:i', strtotime($comment['created']))?>" 
                            required 
                            aria-required="true">
                    </div>
                </div>
            </div>

        </div>
    </div>

</form>

<?=template_admin_footer()?>