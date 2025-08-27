<?php
/**
 * Shop Category Management (Individual Category)
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: category.php
 * LOCATION: /public_html/admin/shop_system/
 * PURPOSE: Standalone category edit/create page with admin integration
 * 
 * CREATED: 2025-08-18
 * VERSION: 2.0 (Standalone)
 */

// Include admin authentication and dependencies
include '../assets/includes/main.php';
include '../../shop_system/functions.php';
// Default input category values
$category = [
    'title' => '',
    'parent_id' => 0
];
if (isset($_GET['id'])) {
    // Retrieve all the categories
    $stmt = $pdo->prepare('SELECT * FROM shop_product_categories WHERE id != ?');
    $stmt->execute([ $_GET['id'] ]);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // ID param exists, edit an existing category
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Update the category
        $stmt = $pdo->prepare('UPDATE shop_product_categories SET title = ?, parent_id = ? WHERE id = ?');
        $stmt->execute([ $_POST['title'], $_POST['parent_id'], $_GET['id'] ]);
        header('Location: categories.php?success_msg=2');
        exit;
    }
    if (isset($_POST['delete'])) {
        // Delete the category
        header('Location: categories.php?delete=' . $_GET['id']);
        exit;
    }
    // Get the category from the database
    $stmt = $pdo->prepare('SELECT * FROM shop_product_categories WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    // Retrieve all the categories
    $stmt = $pdo->prepare('SELECT * FROM shop_product_categories');
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Create a new category
    $page = 'Create';
    if (isset($_POST['submit'])) {
        $stmt = $pdo->prepare('INSERT INTO shop_product_categories (title,parent_id) VALUES (?,?)');
        $stmt->execute([ $_POST['title'], $_POST['parent_id'] ]);
        header('Location: categories.php?success_msg=1');
        exit;
    }
}
?>
<?=template_admin_header($page . ' Category', 'categories', 'manage')?>

<form method="post">

    <div class="content-title">
        <h2><?=$page?> Category</h2>
        <div class="btns">
            <a href="categories.php" class="btn alt mar-right-1">Cancel</a>
            <?php if ($page == 'Edit'): ?>
            <input type="submit" name="delete" value="Delete" class="btn red mar-right-1" onclick="return confirm('Are you sure you want to delete this category?')">
            <?php endif; ?>
            <input type="submit" name="submit" value="Save" class="btn">
        </div>
    </div>

    <div class="content-block">
        <div class="form responsive-width-100">

            <label for="title"><span class="required">*</span> Title</label>
            <input id="title" type="text" name="title" placeholder="Title" value="<?=$category['title']?>" required>

            <label for="parent_id">Parent</label>
            <select id="parent_id" name="parent_id">
                <option value="0">(none)</option>
                <?php foreach ($categories as $c): ?>
                <option value="<?=$c['id']?>"<?=$c['id']==$category['parent_id']||isset($_GET['parent_id']) && $c['id']==$_GET['parent_id']?' selected':''?>><?=$c['title']?></option>
                <?php endforeach; ?>
            </select>

        </div>
    </div>

</form>

<?=template_admin_footer()?>