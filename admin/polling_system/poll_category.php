<?php
include '../assets/includes/main.php';
// Default poll category values
$poll_category = [
    'title' => '',
    'created' => date('Y-m-d\TH:i')
];
// Check if the ID param exists
if (isset($_GET['id'])) {
    // Retrieve the poll_category from the database
    $stmt = $pdo->prepare('SELECT * FROM polls_categories WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $poll_category = $stmt->fetch(PDO::FETCH_ASSOC);
    // ID param exists, edit an existing poll category
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Update the poll category
        $stmt = $pdo->prepare('UPDATE polls_categories SET title = ?, created = ? WHERE id = ?');
        $stmt->execute([ $_POST['title'], $_POST['created'], $_GET['id'] ]);
        header('Location: poll_categories.php?success_msg=2');
        exit;
    }
    if (isset($_POST['delete'])) {
        // Redirect and delete poll category
        header('Location: poll_categories.php?delete=' . $_GET['id']);
        exit;
    }
} else {
    // Create a new poll category
    $page = 'Create';
    if (isset($_POST['submit'])) {
        $stmt = $pdo->prepare('INSERT INTO polls_categories (title, created) VALUES (?, ?)');
        $stmt->execute([ $_POST['title'], $_POST['created'] ]);
        header('Location: poll_categories.php?success_msg=1');
        exit;
    }
}
?>
<?=template_admin_header($page . ' Poll Category', 'polls', 'categories')?>

<div class="content-title" id="main-poll-category-form" role="banner" aria-label="<?=$page?> Poll Category Header">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M40 48C26.7 48 16 58.7 16 72v48c0 13.3 10.7 24 24 24H88c13.3 0 24-10.7 24-24V72c0-13.3-10.7-24-24-24H40zM192 64c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zm0 160c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zm0 160c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zM16 232v48c0 13.3 10.7 24 24 24H88c13.3 0 24-10.7 24-24V232c0-13.3-10.7-24-24-24H40c-13.3 0-24 10.7-24 24zM40 368c-13.3 0-24 10.7-24 24v48c0 13.3 10.7 24 24 24H88c13.3 0 24-10.7 24-24V392c0-13.3-10.7-24-24-24H40z"/></svg>
        </div>
        <div class="txt">
            <h2><?=$page?> Poll Category</h2>
            <p><?=$page == 'Create' ? 'Create a new poll category.' : 'Edit the selected poll category.'?></p>
        </div>
    </div>
</div>
<br>
<form action="" method="post">

    <div class="d-flex gap-2 pb-3 border-bottom mb-3">
        <a href="poll_categories.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Cancel</a>
        <?php if ($page == 'Edit'): ?>
        <input type="submit" name="delete" value="Delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this poll category?')">
        <?php endif; ?>
        <input type="submit" name="submit" value="Save" class="btn btn-success">
    </div>

    <h2 class="mb-3"><?=$page?> Poll Category</h2>

    <?php if (isset($error_msg)): ?>
    <div class="mar-top-4">
        <div class="msg error">
            <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zm0-384c13.3 0 24 10.7 24 24V264c0 13.3-10.7 24-24 24s-24-10.7-24-24V152c0-13.3 10.7-24 24-24zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"/></svg>
            <p><?=$error_msg?></p>
            <svg class="close" width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>
        </div>
    </div>
    <?php endif; ?>

    <div class="content-block">
        
        <div class="form responsive-width-100">

            <label for="title"><span class="required">*</span> Title</label>
            <input id="title" type="text" name="title" placeholder="Title" value="<?=htmlspecialchars($poll_category['title'], ENT_QUOTES)?>" required>

            <label for="created"><span class="required">*</span> Created</label>
            <input id="created" type="datetime-local" name="created" value="<?=date('Y-m-d\TH:i', strtotime($poll_category['created']))?>" required>

        </div>
    
    </div>

</form>

<?=template_admin_footer()?>