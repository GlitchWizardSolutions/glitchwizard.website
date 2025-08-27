<?php
/**
 * Shop Categories Management
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: categories.php
 * LOCATION: /public_html/admin/shop_system/
 * PURPOSE: Standalone shop categories management page
 * 
 * FEATURES:
 * - Full authentication integration
 * - Admin template integration
 * - Category hierarchy management
 * - Product count tracking
 * 
 * CREATED: 2025-08-18
 * VERSION: 2.0 (Standalone)
 */

// Initialize session and security
include '../assets/includes/main.php';
include '../../shop_system/functions.php';

// Icons for the table headers (matching admin pattern)
$table_icons = [
    'asc' => '<i class="bi bi-caret-up-fill"></i>',
    'desc' => '<i class="bi bi-caret-down-fill"></i>'
];
// SQL query to get all categories from the "categories" table
$stmt = $pdo->prepare('SELECT c.*, COUNT(pc.id) AS num_products FROM shop_product_categories c LEFT JOIN shop_product_category pc ON pc.category_id = c.id GROUP BY c.id, c.title, c.parent_id ORDER BY c.title ASC');
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Delete category
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE c, pc FROM shop_product_categories c LEFT JOIN shop_product_category pc ON pc.category_id = c.id WHERE c.id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    header('Location: categories.php?success_msg=3');
    exit;
}
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Category created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Category updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Category deleted successfully!';
    }
}
// Populate categories function
function admin_populate_categories($categories, $parent_id = 0, $n = 0) {
    $html = '';
    foreach ($categories as $category) {
        if ($parent_id == $category['parent_id']) {
            $html .= '
            <tr>
                <td><span style="padding-right:8px;color:#bbbec0;border-left:1px solid #bbbec0;padding-bottom:2px;">-' . str_repeat('--', $n) . '</span>' . $category['title'] . '</td>
                <td><span class="grey small">' . $category['num_products'] . '</span></td>
                <td class="actions">
                    <div class="table-dropdown">
                        <i class="bi bi-three-dots-vertical" aria-hidden="true"></i>
                        <div class="table-dropdown-items">
                            <a href="category.php?id=' . $category['id'] . '">
                                <span class="icon">
                                    <i class="bi bi-pencil"></i>
                                </span>
                                Edit
                            </a>
                            <a href="category.php?parent_id=' . $category['id'] . '">
                                <span class="icon">
                                    <i class="bi bi-plus"></i>
                                </span>
                                Add Child
                            </a>
                            <a href="products.php?category=' . $category['id'] . '">
                                <span class="icon">
                                    <i class="bi bi-eye"></i>
                                </span>
                                View Products
                            </a>
                            <a class="red" href="categories.php?delete=' . $category['id'] . '" onclick="return confirm(\'Are you sure you want to delete this category?\')">
                                <span class="icon">
                                    <i class="bi bi-trash"></i>
                                </span>    
                                Delete
                            </a>
                        </div>
                    </div>
                </td>
            </tr>           
            ';
            $html .= admin_populate_categories($categories, $category['id'], $n+1);
        }
    }
    return $html;
}
?>
<?=template_admin_header('Categories', 'shop', 'categories')?>

<div class="content-title" id="main-shop-categories" role="banner" aria-label="Shop Categories Management Header">
    <div class="title">
        <div class="icon">
            <i class="bi bi-tags" aria-hidden="true"></i>
        </div>
        <div class="txt">
            <h2>Categories</h2>
            <p>View, create, and edit categories</p>
        </div>
    </div>
</div>

<?php if (isset($success_msg)): ?>
<div class="msg success">
    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
    <p><?=$success_msg?></p>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="content-header responsive-flex-column pad-top-5">
    <a href="category.php" class="btn">
        <i class="bi bi-plus me-1"></i>Create Category
    </a>
</div>

<div class="content-block no-pad">
    <div class="table">
        <table>
            <thead>
                <tr>
                    <td>Title</td>
                    <td>Products</td>
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                <tr>
                    <td colspan="20" class="no-results">There are no categories.</td>
                </tr>
                <?php else: ?>
                <?=admin_populate_categories($categories)?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?=template_admin_footer()?>