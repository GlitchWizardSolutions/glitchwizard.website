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
            <i class="bi bi-chat-dots-fill" aria-hidden="true"></i>
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
        <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
        <p><?=$error_msg?></p>
        <button type="button" class="close-error" aria-label="Dismiss error message" onclick="this.parentElement.parentElement.style.display='none'">
            <i class="bi bi-x-circle-fill" aria-hidden="true"></i>
        </button>
    </div>
</div>
<?php endif; ?>

<!-- Top page actions -->
<div class="d-flex gap-2 mb-4" role="region" aria-label="Page Actions">
    <a href="comments.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
        Cancel
    </a>
    <button type="submit" name="submit" form="main-form" class="btn btn-success">
        <i class="bi bi-save me-1" aria-hidden="true"></i>
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
                <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
                Cancel
            </a>
            <button type="submit" name="submit" form="main-form" class="btn btn-success">
                <i class="bi bi-save me-1" aria-hidden="true"></i>
                Save Comment
            </button>
            <?php if ($page == 'Edit'): ?>
            <button type="submit" name="delete" form="main-form" class="btn btn-danger"
                onclick="return confirm('Are you sure you want to delete this comment? This action cannot be undone.')"
                aria-label="Delete this comment permanently">
                <i class="bi bi-trash me-1" aria-hidden="true"></i>
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