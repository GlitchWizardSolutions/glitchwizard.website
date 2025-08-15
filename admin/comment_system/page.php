<?php
include '../assets/includes/main.php';

$page = [
    'page_id' => '',
    'title' => '',
    'description' => '',
    'url' => '',
    'page_status' => ''
];
// If editing an page
if (isset($_GET['id'])) {
    // Get the page from the database
    $stmt = $pdo->prepare('SELECT * FROM comment_page_details WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $page = $stmt->fetch(PDO::FETCH_ASSOC);
    // ID param exists, edit an existing page
    $p = 'Edit';
    if (isset($_POST['submit'])) {
        // Update the page in the database
        $stmt = $pdo->prepare('UPDATE comment_page_details SET page_id = ?, title = ?, `description` = ?, `url` = ?, page_status = ? WHERE id = ?');
        $stmt->execute([ $_POST['page_id'], $_POST['title'], $_POST['description'], $_POST['url'], $_POST['page_status'], $_GET['id'] ]);
        header('Location: pages.php?success_msg=2');
        exit;
    }
    if (isset($_POST['delete'])) {
        // Redirect and delete the page
        header('Location: pages.php?delete=' . $_GET['id']);
        exit;
    }
} else {
    // Create a new page
    $p = 'Create';
    if (isset($_POST['submit'])) {
        // Insert the page into the database
        $stmt = $pdo->prepare('INSERT INTO comment_page_details (page_id, title, `description`, `url`, page_status) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([ $_POST['page_id'], $_POST['title'], $_POST['description'], $_POST['url'], $_POST['page_status'] ]);
        header('Location: pages.php?success_msg=1');
        exit;
    }
}
?>
<?=template_admin_header($p . ' Page', 'comments', 'pages')?>

<div class="content-title mb-4" id="main-page-create-edit" role="banner" aria-label="<?=$p?> Page Header">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M64 464c-8.8 0-16-7.2-16-16V64c0-8.8 7.2-16 16-16H224v80c0 17.7 14.3 32 32 32h80V448c0 8.8-7.2 16-16 16H64zM64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V154.5c0-17-6.7-33.3-18.7-45.3L274.7 18.7C262.7 6.7 246.5 0 229.5 0H64zm56 256c-13.3 0-24 10.7-24 24s10.7 24 24 24H264c13.3 0 24-10.7 24-24s-10.7-24-24-24H120zm0 96c-13.3 0-24 10.7-24 24s10.7 24 24 24H264c13.3 0 24-10.7 24-24s-10.7-24-24-24H120z"/></svg>
        </div>
        <div class="txt">
            <h2><?=$p?> Comment Page</h2>
            <p><?=$p == 'Edit' ? 'Edit page details and settings.' : 'Create a new comment page for tracking comments.'?></p>
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
    <a href="pages.php" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>
        Cancel
    </a>
    <button type="submit" name="submit" form="main-form" class="btn btn-success">
        <i class="fas fa-save me-1" aria-hidden="true"></i>
        Save Page
    </button>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><?=$p?> Comment Page</h6>
    </div>
    <div class="card-body">
        <form method="post" enctype="multipart/form-data" id="main-form">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="page_id" class="form-label">
                        <span class="text-danger">*</span> Page ID
                    </label>
                    <input id="page_id" type="text" name="page_id" class="form-control" 
                           placeholder="Unique page identifier" value="<?= htmlspecialchars($page['page_id'], ENT_QUOTES) ?>" required>
                    <div class="form-text">Unique identifier for this comment page (e.g., blog-post-123).</div>
                </div>
                <div class="col-md-6">
                    <label for="page_status" class="form-label">
                        <span class="text-danger">*</span> Page Status
                    </label>
                    <select id="page_status" name="page_status" class="form-select" required>
                        <option value="1" <?= ($page['page_status'] == '1') ? 'selected' : '' ?>>Open</option>
                        <option value="0" <?= ($page['page_status'] == '0') ? 'selected' : '' ?>>Closed</option>
                    </select>
                    <div class="form-text">Open pages accept new comments, closed pages don't.</div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-12">
                    <label for="title" class="form-label">
                        <span class="text-danger">*</span> Page Title
                    </label>
                    <input id="title" type="text" name="title" class="form-control" 
                           placeholder="Page title or heading" value="<?= htmlspecialchars($page['title'], ENT_QUOTES) ?>" required>
                    <div class="form-text">The title or heading of the page where comments will appear.</div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-12">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="3" 
                              placeholder="Optional description of the page"><?= htmlspecialchars($page['description'], ENT_QUOTES) ?></textarea>
                    <div class="form-text">Optional description or summary of the page content.</div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-12">
                    <label for="url" class="form-label">Page URL</label>
                    <input id="url" type="url" name="url" class="form-control" 
                           placeholder="https://example.com/page" value="<?= htmlspecialchars($page['url'], ENT_QUOTES) ?>">
                    <div class="form-text">Full URL where this comment page can be accessed.</div>
                </div>
            </div>
        </form>
    </div>
    <div class="card-footer bg-light">
        <div class="d-flex gap-2">
            <a href="pages.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>
                Cancel
            </a>
            <button type="submit" name="submit" form="main-form" class="btn btn-success">
                <i class="fas fa-save me-1" aria-hidden="true"></i>
                Save Page
            </button>
            <?php if ($p == 'Edit'): ?>
            <button type="submit" name="delete" form="main-form" class="btn btn-danger"
                onclick="return confirm('Are you sure you want to delete this page?')"
                aria-label="Delete this page permanently">
                <i class="fas fa-trash me-1" aria-hidden="true"></i>
                Delete
            </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<?=template_admin_footer()?>