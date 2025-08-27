<?php
/**
 * SYSTEM: Blog System
 * LOCATION: public_html/admin/blog/
 * LOG:
 * 2025-07-04 - Original Development
 * PRODUCTION:
 */
include "header.php";

if (isset($_POST['add']))
{
    $category = $_POST['category'];
    $slug = generateSeoURL($category, 0);

    $stmt = $pdo->prepare("SELECT * FROM `categories` WHERE category = ? LIMIT 1");
    $stmt->execute([$category]);
    $validator = $stmt->rowCount();
    if ($validator > 0)
    {
        echo '<br />
            <div class="alert alert-warning">
                <i class="fas fa-info-circle"></i> Category with this name has already been added.
            </div>';

    } else
    {
        $stmt = $pdo->prepare("INSERT INTO categories (category, slug) VALUES (?, ?)");
        $stmt->execute([$category, $slug]);
        echo '<meta http-equiv="refresh" content="0; url=categories.php">';
    }
}
?>

<?= template_admin_header('Add Blog Category', 'blog', 'categories') ?>

<div class="content-title mb-4">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" aria-hidden="true"
                focusable="false">
                <path
                    d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z" />
            </svg>
        </div>
        <div class="txt">
            <h2>Add a Blog Category</h2>
            <p>Add a new category for the blog system to use.</p>
        </div>
    </div>
</div>

<div style="height: 20px;"></div>

<div class="mb-3">
    <a href="categories.php" class="btn btn-outline-secondary" aria-label="Cancel and return to categories list">
        <i class="fa fa-arrow-left" aria-hidden="true"></i> Cancel
    </a>
</div>
<div class="card">
    <h6 class="card-header">Add Category</h6>
    <div class="card-body">
        <form action="" method="post">
            <p>
                <label>Title</label>
                <input class="form-control" name="category" value="" type="text" required>
            </p>
            <div class="form-actions">
                <button type="submit" name="add" class="btn btn-success">Save Category</button>
            </div>
        </form>
    </div>
</div>

<?= template_admin_footer(); ?>