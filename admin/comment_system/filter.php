<?php
include '../assets/includes/main.php';
// Default input filter values
$filter = [
    'word' => '',
    'replacement' => ''
];
// If editing an filter
if (isset($_GET['id'])) {
    // Get the filter from the database
    $stmt = $pdo->prepare('SELECT * FROM comment_filters WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $filter = $stmt->fetch(PDO::FETCH_ASSOC);
    // ID param exists, edit an existing filter
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Update the filter in the database
        $stmt = $pdo->prepare('UPDATE comment_filters SET word = ?, replacement = ? WHERE id = ?');
        $stmt->execute([ $_POST['word'], $_POST['replacement'], $_GET['id'] ]);
        header('Location: filters.php?success_msg=2');
        exit;
    }
    if (isset($_POST['delete'])) {
        // Redirect and delete the filter
        header('Location: filters.php?delete=' . $_GET['id']);
        exit;
    }
} else {
    // Create a new filter
    $page = 'Create';
    if (isset($_POST['submit'])) {
        // Get the words from the textarea
        $words = explode(PHP_EOL, $_POST['word']);
        foreach ($words as $word) {
            // Trim the word
            $word = trim($word);
            // Check if the word is empty
            if (empty($word)) {
                continue;
            }
            // Insert the word into the database
            $stmt = $pdo->prepare('INSERT INTO comment_filters (word, replacement) VALUES (?, ?)');
            $stmt->execute([ $word, $_POST['replacement'] ]);
        }
        header('Location: filters.php?success_msg=1');
        exit;
    }
}
?>
<?=template_admin_header($page . ' Filter', 'comments', 'filters')?>

<div class="content-title mb-4" id="main-filter-create-edit" role="banner" aria-label="<?=$page?> Filter Header">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M3.9 54.9C10.5 40.9 24.5 32 40 32H472c15.5 0 29.5 8.9 36.1 22.9s4.6 30.5-5.2 42.5L320 320.9V448c0 12.1-6.8 23.2-17.7 28.6s-23.8 4.3-33.5-3l-64-48c-8.1-6-12.8-15.5-12.8-25.6V320.9L9 97.3C-.7 85.4-2.8 68.8 3.9 54.9z"/></svg>
        </div>
        <div class="txt">
            <h2><?=$page?> Filter</h2>
            <p><?=$page == 'Edit' ? 'Edit word filter and replacement text.' : 'Create new word filters for comment moderation.'?></p>
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
    <a href="filters.php" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>
        Cancel
    </a>
    <button type="submit" name="submit" form="main-form" class="btn btn-success">
        <i class="fas fa-save me-1" aria-hidden="true"></i>
        Save Filter
    </button>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><?=$page?> Word Filter</h6>
    </div>
    <div class="card-body">
        <form method="post" enctype="multipart/form-data" id="main-form">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="word" class="form-label">
                        <span class="text-danger">*</span> 
                        <?php if ($page == 'Edit'): ?>Word<?php else: ?>Words<?php endif; ?>
                    </label>
                    <?php if ($page == 'Edit'): ?>
                    <input id="word" type="text" name="word" class="form-control" 
                           placeholder="Word to filter" value="<?= htmlspecialchars($filter['word'], ENT_QUOTES) ?>" required>
                    <div class="form-text">Enter the word or phrase to be filtered.</div>
                    <?php else: ?>
                    <textarea id="word" name="word" class="form-control" rows="4" 
                              placeholder="Word 1&#10;Word 2&#10;Word 3" required><?= htmlspecialchars($filter['word'], ENT_QUOTES) ?></textarea>
                    <div class="form-text">Enter one word per line to create multiple filters at once.</div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="replacement" class="form-label">
                        <span class="text-danger">*</span> Replacement Text
                    </label>
                    <input id="replacement" type="text" name="replacement" class="form-control" 
                           placeholder="Replacement text" value="<?= htmlspecialchars($filter['replacement'], ENT_QUOTES) ?>" required>
                    <div class="form-text">Text to replace the filtered word(s) with. Leave empty to remove entirely.</div>
                </div>
            </div>
        </form>
    </div>
    <div class="card-footer bg-light">
        <div class="d-flex gap-2">
            <a href="filters.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>
                Cancel
            </a>
            <button type="submit" name="submit" form="main-form" class="btn btn-success">
                <i class="fas fa-save me-1" aria-hidden="true"></i>
                Save Filter
            </button>
            <?php if ($page == 'Edit'): ?>
            <button type="submit" name="delete" form="main-form" class="btn btn-danger"
                onclick="return confirm('Are you sure you want to delete this filter?')"
                aria-label="Delete this filter permanently">
                <i class="fas fa-trash me-1" aria-hidden="true"></i>
                Delete
            </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<?=template_admin_footer()?>