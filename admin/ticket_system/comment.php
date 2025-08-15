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
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                <path d="M123.6 391.3c12.9-9.4 29.6-11.8 44.6-6.4c26.5 9.6 56.2 15.1 87.8 15.1c124.7 0 208-80.5 208-160s-83.3-160-208-160S48 160.5 48 240c0 32 12.4 62.8 35.7 89.2c8.6 9.7 12.8 22.5 11.8 35.5c-1.4 18.1-5.7 34.7-11.3 49.4c17-7.9 31.1-16.7 39.4-22.7zM21.2 431.9c1.8-2.7 3.5-5.4 5.1-8.1c10-16.6 19.5-38.4 21.4-62.9C17.7 326.8 0 285.1 0 240C0 125.1 114.6 32 256 32s256 93.1 256 208s-114.6 208-256 208c-37.1 0-72.3-6.4-104.1-17.9c-11.9 8.7-31.3 20.6-54.3 30.6c-15.1 6.6-32.3 12.6-50.1 16.1c-.8 .2-1.6 .3-2.4 .5c-4.4 .8-8.7 1.5-13.2 1.9c-.2 0-.5 .1-.7 .1c-5.1 .5-10.2 .8-15.3 .8c-6.5 0-12.3-3.9-14.8-9.9c-2.5-6-1.1-12.8 3.4-17.4c4.1-4.2 7.8-8.7 11.3-13.5c1.7-2.3 3.3-4.6 4.8-6.9c.1-.2 .2-.3 .3-.5z"/>
            </svg>
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
        <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>
        Cancel
    </a>
    <?php if ($page == 'Edit'): ?>
    <button type="submit" name="delete" form="comment-form" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this comment?')">
        <i class="fas fa-trash me-1" aria-hidden="true"></i>
        Delete
    </button>
    <?php endif; ?>
    <button type="submit" name="submit" form="comment-form" class="btn btn-success">
        <i class="fas fa-save me-1" aria-hidden="true"></i>
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