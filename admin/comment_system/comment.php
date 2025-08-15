<?php
include '../assets/includes/main.php';

// Define template editor if not already defined
if (!defined('template_editor')) {
    define('template_editor', 'summernote');
}

// Default input comment values
$comment = [
    'page_id' => '',
    'parent_id' => '',
    'top_parent_id' => '',
    'display_name' => $_SESSION['name'] ?? '',
    'content' => '',
    'votes' => '',
    'approved' => 1,
    'acc_id' => $_SESSION['id'] ?? 0,
    'featured' => 0,
    'submit_date' => date('Y-m-d H:i:s'),
    'edited_date' => date('Y-m-d H:i:s')
];
// Get all accounts
$accounts = $pdo->query('SELECT id, username FROM accounts ORDER BY username')->fetchAll(PDO::FETCH_ASSOC);
// Get all comments
$comments = $pdo->query('SELECT * FROM comments ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
// If reply
if (isset($_GET['reply'])) {
    // Get the parent comment
    $stmt = $pdo->prepare('SELECT * FROM comments WHERE id = ?');
    $stmt->execute([ $_GET['reply'] ]);
    $parent_comment = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($parent_comment) {
        // Update the comment array with parent comment details
        $comment['parent_id'] = $parent_comment['id'];
        $comment['top_parent_id'] = $parent_comment['top_parent_id'];
        $comment['page_id'] = $parent_comment['page_id'];
    }
}
// If editing an comment
if (isset($_GET['id'])) {
    // Get the comment from the database
    $stmt = $pdo->prepare('SELECT * FROM comments WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);
    // ID param exists, edit an existing comment
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Update the comment in the database
        $votes = $_POST['votes'] ? $_POST['votes'] : 0;
        $top_parent_id = $_POST['top_parent_id'] ? $_POST['top_parent_id'] : $comment['id'];
        $stmt = $pdo->prepare('UPDATE comments SET page_id = ?, parent_id = ?, top_parent_id = ?, display_name = ?, content = ?, votes = ?, approved = ?, acc_id = ?, featured = ?, submit_date = ?, edited_date = ? WHERE id = ?');
        $stmt->execute([ $_POST['page_id'], $_POST['parent_id'], $top_parent_id, $_POST['display_name'], $_POST['content'], $votes, $_POST['approved'], $_POST['acc_id'], $_POST['featured'], $_POST['submit_date'], $_POST['edited_date'], $_GET['id'] ]);
        header('Location: comments.php?success_msg=2');
        exit;
}
    if (isset($_POST['delete'])) {
        // Redirect and delete the comment
        header('Location: comments.php?delete=' . $_GET['id']);
        exit;
    }
} else {
    // Create a new comment
    $page = 'Create';
    if (isset($_POST['submit'])) {
        $votes = $_POST['votes'] ? $_POST['votes'] : 0; // Default votes to 0 if not set
        $top_parent_id = $_POST['top_parent_id'] ? $_POST['top_parent_id'] : 0;
        $stmt = $pdo->prepare('INSERT INTO comments (page_id, parent_id, top_parent_id, display_name, content, votes, approved, acc_id, featured, submit_date, edited_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([ $_POST['page_id'], $_POST['parent_id'], $top_parent_id, $_POST['display_name'], $_POST['content'], $votes, $_POST['approved'], $_POST['acc_id'], $_POST['featured'], $_POST['submit_date'], $_POST['edited_date'] ]);
        // If top_parent_id is not set, set it to the new comment's ID
        if (!$top_parent_id) {
            $insert_id = $pdo->lastInsertId();
            $stmt = $pdo->prepare('UPDATE comments SET top_parent_id = ? WHERE id = ?');
            $stmt->execute([ $insert_id, $insert_id ]);
        }
        header('Location: comments.php?success_msg=1');
        exit;
    }
}
?>
<?=template_admin_header($page . ' Comment', 'comments', 'view')?>

<div class="content-title mb-4" id="main-comment-create-edit" role="banner" aria-label="<?=$page?> Comment Header">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M123.6 391.3c12.9-9.4 29.6-11.8 44.6-6.4c26.5 9.6 56.2 15.1 87.8 15.1c124.7 0 208-80.5 208-160s-83.3-160-208-160S48 160.5 48 240c0 32 12.4 62.8 35.7 89.2c8.6 9.7 12.8 22.5 11.8 35.5c-1.4 18.1-5.7 34.7-11.3 49.4c17-7.9 31.1-16.7 39.4-22.7zM21.2 431.9c1.8-2.7 3.5-5.4 5.1-8.1c10-16.6 19.5-38.4 21.4-62.9C17.7 326.8 0 285.1 0 240C0 125.1 114.6 32 256 32s256 93.1 256 208s-114.6 208-256 208c-37.1 0-72.3-6.4-104.1-17.9c-11.9 8.7-31.3 20.6-54.3 30.6c-15.1 6.6-32.3 12.6-50.1 16.1c-.8 .2-1.6 .3-2.4 .5c-4.4 .8-8.7 1.5-13.2 1.9c-.2 0-.5 .1-.7 .1c-5.1 .5-10.2 .8-15.3 .8c-6.5 0-12.3-3.9-14.8-9.9c-2.5-6-1.1-12.8 3.4-17.4c4.1-4.2 7.8-8.7 11.3-13.5c1.7-2.3 3.3-4.6 4.8-6.9c.1-.2 .2-.3 .3-.5z"/></svg>
        </div>
        <div class="txt">
            <h2><?=$page?> Comment</h2>
            <p><?=$page == 'Edit' ? 'Edit comment details and content.' : 'Create a new comment in the system.'?></p>
        </div>
    </div>
</div>

<?php if (isset($error_msg)): ?>
<div class="mb-4" role="region" aria-label="Error Message">
    <div class="msg error" role="alert" aria-live="polite">
        <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zm0-384c13.3 0 24 10.7 24 24V264c0 13.3-10.7 24-24 24s-24-10.7-24-24V152c0-13.3 10.7-24 24-24zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"/></svg>
        <p><?=$error_msg?></p>
        <button type="button" class="close-error" aria-label="Dismiss error message" onclick="this.parentElement.parentElement.style.display='none'">
            <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>
        </button>
    </div>
</div>
<?php endif; ?>

<!-- Top page actions -->
<div class="d-flex gap-2 mb-4" role="region" aria-label="Page Actions">
    <a href="comments.php" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>
        Cancel
    </a>
    <button type="submit" name="submit" form="main-form" class="btn btn-success">
        <i class="fas fa-save me-1" aria-hidden="true"></i>
        Save Comment
    </button>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><?= isset($_GET['id']) ? 'Edit Comment' : 'Add Comment' ?></h6>
    </div>
    <div class="card-body">
        <form method="post" enctype="multipart/form-data" id="main-form">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="page_id" class="form-label">
                        <span class="text-danger">*</span> Page ID
                    </label>
                    <input type="text" id="page_id" name="page_id" class="form-control" 
                           placeholder="Page ID" value="<?= htmlspecialchars($comment['page_id'], ENT_QUOTES) ?>" required>
                </div>
                <div class="col-md-2">
                    <label for="votes" class="form-label">Votes</label>
                    <input type="number" id="votes" name="votes" class="form-control" 
                           placeholder="0" value="<?= htmlspecialchars($comment['votes'], ENT_QUOTES) ?>">
                </div>
                <div class="col-md-2">
                    <label for="approved" class="form-label">Approved</label>
                    <select id="approved" name="approved" class="form-select">
                        <option value="1" <?= ($comment['approved'] == 1) ? 'selected' : '' ?>>Yes</option>
                        <option value="0" <?= ($comment['approved'] == 0) ? 'selected' : '' ?>>No</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="featured" class="form-label">Featured</label>
                    <select id="featured" name="featured" class="form-select">
                        <option value="0" <?= ($comment['featured'] == 0) ? 'selected' : '' ?>>No</option>
                        <option value="1" <?= ($comment['featured'] == 1) ? 'selected' : '' ?>>Yes</option>
                    </select>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <label for="acc_id" class="form-label">Account</label>
                    <select id="acc_id" name="acc_id" class="form-select">
                        <option value="">No Account</option>
                        <?php foreach ($accounts as $a): ?>
                        <option value="<?= $a['id'] ?>" <?= ($a['id'] == $comment['acc_id']) ? 'selected' : '' ?>>
                            [<?= $a['id'] ?>] <?= htmlspecialchars($a['username'], ENT_QUOTES) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="parent_id" class="form-label">Parent Comment</label>
                    <select id="parent_id" name="parent_id" class="form-select">
                        <option value="">No Parent</option>
                        <?php foreach ($comments as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($c['id'] == $comment['parent_id']) ? 'selected' : '' ?>>
                            [<?= $c['id'] ?>] <?= htmlspecialchars($c['display_name'], ENT_QUOTES) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="top_parent_id" class="form-label">Top Parent Comment</label>
                    <select id="top_parent_id" name="top_parent_id" class="form-select">
                        <option value="">No Top Parent</option>
                        <?php foreach ($comments as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($c['id'] == $comment['top_parent_id']) ? 'selected' : '' ?>>
                            [<?= $c['id'] ?>] <?= htmlspecialchars($c['display_name'], ENT_QUOTES) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="display_name" class="form-label">
                        <span class="text-danger">*</span> Display Name
                    </label>
                    <input type="text" id="display_name" name="display_name" class="form-control" 
                           placeholder="Display Name" value="<?= htmlspecialchars($comment['display_name'], ENT_QUOTES) ?>" required>
                </div>
                <div class="col-md-3">
                    <label for="submit_date" class="form-label">Submit Date</label>
                    <input type="datetime-local" id="submit_date" name="submit_date" class="form-control" 
                           value="<?= date('Y-m-d\TH:i', strtotime($comment['submit_date'])) ?>">
                </div>
                <div class="col-md-3">
                    <label for="edited_date" class="form-label">Edited Date</label>
                    <input type="datetime-local" id="edited_date" name="edited_date" class="form-control" 
                           value="<?= date('Y-m-d\TH:i', strtotime($comment['edited_date'])) ?>">
                </div>
            </div>

            <div class="row g-3">
                <div class="col-12">
                    <label for="content" class="form-label">
                        <span class="text-danger">*</span> Content
                    </label>
                    <?php if (template_editor == 'summernote'): ?>
                    <textarea id="content" name="content" class="form-control"><?= htmlspecialchars($comment['content'], ENT_QUOTES) ?></textarea>
                    <?php else: ?>
                    <textarea id="content" name="content" class="form-control" placeholder="Enter comment content..." rows="8"><?= htmlspecialchars($comment['content'], ENT_QUOTES) ?></textarea>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
    <div class="card-footer bg-light">
        <div class="d-flex gap-2">
            <a href="comments.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>
                Cancel
            </a>
            <button type="submit" name="submit" form="main-form" class="btn btn-success">
                <i class="fas fa-save me-1" aria-hidden="true"></i>
                Save Comment
            </button>
            <?php if ($page == 'Edit'): ?>
            <button type="submit" name="delete" form="main-form" class="btn btn-danger"
                onclick="return confirm('Are you sure you want to delete this comment? This action cannot be undone.')"
                aria-label="Delete this comment permanently">
                <i class="fas fa-trash me-1" aria-hidden="true"></i>
                Delete
            </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (template_editor == 'summernote'): ?>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script>
$(document).ready(function() {
    $('#content').summernote({
        height: 400,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'strikethrough']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture']],
            ['view', ['codeview']]
        ]
    });
});
</script>
<?php endif; ?>

<?=template_admin_footer()?>