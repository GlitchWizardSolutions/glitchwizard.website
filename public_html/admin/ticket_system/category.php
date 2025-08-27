<?php
include 'main.php';
// Default category values
$category = [
    'title' => ''
];
if (isset($_GET['id'])) {
    // Retrieve the category from the database
    $stmt = $pdo->prepare('SELECT * FROM tickets_categories WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    // ID param exists, edit an existing category
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Update the category
        $stmt = $pdo->prepare('UPDATE tickets_categories SET title = ? WHERE id = ?');
        $stmt->execute([ $_POST['title'], $_GET['id'] ]);
        header('Location: categories.php?success_msg=2');
        exit;
    }
    if (isset($_POST['delete'])) {
        // Delete the category
        $stmt = $pdo->prepare('DELETE FROM tickets_categories WHERE id = ?');
        $stmt->execute([ $_GET['id'] ]);
        header('Location: categories.php?success_msg=3');
        exit;
    }
} else {
    // Create a new category
    $page = 'Create';
    if (isset($_POST['submit'])) {
        $stmt = $pdo->prepare('INSERT INTO tickets_categories (title) VALUES (?)');
        $stmt->execute([ $_POST['title'] ]);
        header('Location: categories.php?success_msg=1');
        exit;
    }
}
?>
<?=template_admin_header($page . ' Category', 'tickets', 'category')?>

<div class="content-title" id="main-dashboard" role="banner" aria-label="Account Dashboard Header">
    <div class="title">
        <div class="icon">         
            <i class="bi bi-list-task"></i>
        </div>
        <div class="txt">
            <h2>Categories</h2>
            <p>Add a Category.</p>
        </div>
    </div>
</div>

<div class="mb-4">
</div>

<form action="" method="post" role="form" aria-labelledby="form-title">

    <!-- Top form actions -->
    <div class="d-flex gap-2 pb-3 border-bottom mb-4" role="region" aria-label="Form Actions">
        <a href="categories.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
            Cancel
        </a>
        <button type="submit" name="submit" class="btn btn-success">
            <i class="bi bi-save me-1" aria-hidden="true"></i>
            Save Category
        </button>
        <?php if ($page == 'Edit'): ?>
        <button type="submit" name="delete" class="btn btn-danger" 
               onclick="return confirm('Are you sure you want to delete this category?')"
               aria-label="Delete category">
            <i class="bi bi-trash me-1" aria-hidden="true"></i>
            Delete Category
        </button>
        <?php endif; ?>
    </div>
    <br>

    <div class="card">
        <h6 class="card-header"><?=$page == 'Edit' ? 'Edit Category' : 'Create Category'?></h6>
        <div class="card-body">
            <div class="mb-3">
                <label for="title" class="form-label">
                    <span class="required" aria-hidden="true">*</span> Title
                    <span class="sr-only">(required)</span>
                </label>
                <input id="title" type="text" name="title" class="form-control" 
                       placeholder="Enter category title" 
                       value="<?=htmlspecialchars($category['title'], ENT_QUOTES)?>" 
                       required
                       aria-required="true"
                       aria-describedby="title-hint">
                <div id="title-hint" class="form-text">
                    Enter a descriptive title for this ticket category.
                </div>
            </div>
        </div>
    </div>

</form>

<?=template_admin_footer()?>