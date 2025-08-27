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
            <i class="bi bi-card-checklist" aria-hidden="true"></i>
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
    <a href="poll_categories.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Cancel</a>
        <?php if ($page == 'Edit'): ?>
        <input type="submit" name="delete" value="Delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this poll category?')">
        <?php endif; ?>
        <input type="submit" name="submit" value="Save" class="btn btn-success">
    </div>

    <h2 class="mb-3"><?=$page?> Poll Category</h2>

    <?php if (isset($error_msg)): ?>
    <div class="mar-top-4">
        <div class="msg error">
            <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
            <p><?=$error_msg?></p>
            <i class="bi bi-x-lg close" role="button" tabindex="0" aria-label="Close"></i>
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