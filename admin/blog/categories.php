<?php
/* 
 * Blog Categories Management
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: categories.php
 * LOCATION: /public_html/admin/blog/
 * PURPOSE: Manage blog content categories and organization
 * DETAILED DESCRIPTION:
 * This file provides an interface for managing blog categories, including
 * creation, editing, deletion, and organization of content categories.
 * It supports hierarchical category structures, bulk operations, and
 * category relationship management for the blog system.
 * REQUIRED FILES: 
 * - /public_html/assets/includes/main.php
 * - /public_html/assets/includes/settings/blog_config.php
 * - /public_html/assets/includes/settings/categories_config.php
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: [IN DEVELOPMENT]
 * 
 * FEATURES:
 * - Category creation and editing
 * - Hierarchical organization
 * - Bulk category operations
 * - Category relationships
 * - SEO optimization tools
 */
include_once "header.php";

// Sorting setup following Table.php standard
$table_icons = [
    'asc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9650;</span>',
    'desc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9660;</span>'
];
$order = isset($_GET['order']) && strtoupper($_GET['order']) === 'DESC' ? 'DESC' : 'ASC';
$order_by_whitelist = [
    'category' => 'category',
    'post_count' => 'category'  // Note: post_count sorting handled separately since it's computed
];
$order_by = isset($_GET['order_by']) && isset($order_by_whitelist[$_GET['order_by']]) ? $_GET['order_by'] : 'category';
$order_by_sql = $order_by_whitelist[$order_by];

// DELETE
if (isset($_GET['delete']))
{
    $id = (int) $_GET["delete"];

    // Get category name for success message
    $nameStmt = $pdo->prepare("SELECT category FROM blog_categories WHERE id = ?");
    $nameStmt->execute([$id]);
    $categoryName = $nameStmt->fetchColumn();

    if ($categoryName)
    {
        $stmt = $pdo->prepare("DELETE FROM blog_categories WHERE id = ?");
        $stmt->execute([$id]);
        $stmt2 = $pdo->prepare("DELETE FROM blog_posts WHERE category_id = ?");
        $stmt2->execute([$id]);

        header("Location: categories.php?success=deleted&name=" . urlencode($categoryName));
        exit;
    }
}

?>

<?= template_admin_header('Blog Categories', 'blog', 'categories') ?>

<div class="content-title mb-4" id="main-blog-categories" role="banner" aria-label="Blog Categories Management Header">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                <path d="M96 96c0-35.3 28.7-64 64-64H448c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H80c-44.2 0-80-35.8-80-80V128c0-17.7 14.3-32 32-32s32 14.3 32 32V400c0 8.8 7.2 16 16 16s16-7.2 16-16V96zm64 24v80c0 13.3 10.7 24 24 24H296c13.3 0 24-10.7 24-24V120c0-13.3-10.7-24-24-24H184c-13.3 0-24 10.7-24 24zm208-8c0 8.8 7.2 16 16 16h48c8.8 0 16-7.2 16-16s-7.2-16-16-16H384c-8.8 0-16 7.2-16 16zm0 96c0 8.8 7.2 16 16 16h48c8.8 0 16-7.2 16-16s-7.2-16-16-16H384c-8.8 0-16 7.2-16 16zM160 304c0 8.8 7.2 16 16 16H432c8.8 0 16-7.2 16-16s-7.2-16-16-16H176c-8.8 0-16 7.2-16 16zm0 96c0 8.8 7.2 16 16 16H432c8.8 0 16-7.2 16-16s-7.2-16-16-16H176c-8.8 0-16 7.2-16 16z"/>
            </svg>
        </div>
        <div class="txt">
            <h2>Blog Categories</h2>
            <p>Create, edit, and manage blog categories to organize your content effectively.</p>
        </div>
    </div>
</div>

 

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <strong>Success!</strong>
        <?php if ($_GET['success'] === 'deleted' && isset($_GET['name'])): ?>
            Category "<?= htmlspecialchars(urldecode($_GET['name'])) ?>" and all associated posts have been deleted.
        <?php elseif ($_GET['success'] === 'updated'): ?>
            Category updated successfully.
        <?php elseif ($_GET['success'] === 'created'): ?>
            Category created successfully.
        <?php endif; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php
if (isset($_GET['edit']))
{
    $id = (int) $_GET["edit"];
    $stmt = $pdo->prepare("SELECT * FROM blog_categories WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (empty($id) || !$row)
    {
        header("Location: categories.php");
        exit;
    }

    if (isset($_POST['submit']))
    {
        $category = $_POST['category'];
        $slug = generateSeoURL($category, 0);

        $stmtValid = $pdo->prepare("SELECT * FROM blog_categories WHERE category = ? AND id != ? LIMIT 1");
        $stmtValid->execute([$category, $id]);
        $validator = $stmtValid->rowCount();
        if ($validator > 0)
        {
            echo '<div class="alert alert-warning"><i class="fas fa-info-circle"></i> Category with this name has already been added.</div>';
        } else
        {
            $stmtEdit = $pdo->prepare("UPDATE blog_categories SET category = ?, slug = ? WHERE id = ?");
            $stmtEdit->execute([$category, $slug, $id]);
            header("Location: categories.php?success=updated");
            exit;
        }
    }
    ?>
    <div class="mb-4">
        <a href="categories.php" class="btn btn-outline-secondary" aria-label="Cancel and return to categories list">
            <i class="fa fa-arrow-left" aria-hidden="true"></i> Cancel
        </a>
    </div>
    <div class="card mb-4 shadow-sm">
        <div class="card-header">
            <h6 class="mb-0">Edit Category</h6>
        </div>
        <div class="card-body">
            <form action="" method="post">
                <div class="mb-3">
                    <label for="category" class="form-label fw-bold">Category Name</label>
                    <input class="form-control" name="category" id="category" type="text"
                        value="<?= htmlspecialchars($row['category']) ?>" placeholder="Enter category name" required>
                    <div class="form-text">Category names should be descriptive and unique.</div>
                </div>
                <div class="d-flex justify-content-start">
                    <button type="submit" name="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php
    // Stop further output (hide add button and table)
    return;
}
?>

<!-- Add Category button above the card, left-aligned -->
<div class="mb-3">
    <a href="add_category.php" class="btn btn-outline-secondary">
        <i class="fas fa-plus me-1"></i>Add Category
    </a>
</div>
<div class="card shadow-sm">
    <div class="card-header bg-light d-flex align-items-center">
        <h6 class="mb-0">Listing of all categories</h6>
    </div>
    <div class="card-body p-0">
        <div class="table" role="table" aria-label="Blog Categories">
            <table role="grid">
                <thead role="rowgroup">
                    <tr role="row">
                        <th class="text-left" style="text-align: left;" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'category'; $q['order'] = ($order_by == 'category' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="sort-header">Category Name<?= $order_by == 'category' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th style="text-align: center;" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'post_count'; $q['order'] = ($order_by == 'post_count' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="sort-header">Post Count<?= $order_by == 'post_count' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th style="text-align: center;" role="columnheader" scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody role="rowgroup">
                    <?php
                    // Use proper sorting from our setup
                    $stmt = $pdo->query("SELECT * FROM blog_categories ORDER BY $order_by_sql $order");
                    if ($stmt->rowCount() === 0)
                    {
                        echo '<tr role="row"><td colspan="3" class="text-center text-muted py-4" role="gridcell">
                        <i class="fas fa-folder-open fa-2x mb-2"></i><br>
                        No categories found. <a href="add_category.php">Create your first category</a>.
                      </td></tr>';
                    } else
                    {
                        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        // If sorting by post count, we need to sort the array after fetching
                        if ($order_by == 'post_count') {
                            // Get post counts for all categories
                            foreach ($categories as &$category) {
                                $postCountStmt = $pdo->prepare("SELECT COUNT(*) FROM blog_posts WHERE category_id = ?");
                                $postCountStmt->execute([$category['id']]);
                                $category['post_count'] = $postCountStmt->fetchColumn();
                            }
                            // Sort by post count
                            usort($categories, function($a, $b) use ($order) {
                                return $order == 'ASC' ? $a['post_count'] - $b['post_count'] : $b['post_count'] - $a['post_count'];
                            });
                        }
                        
                        foreach ($categories as $row)
                        {
                            // Get post count if not already calculated
                            if (!isset($row['post_count'])) {
                                $postCountStmt = $pdo->prepare("SELECT COUNT(*) FROM blog_posts WHERE category_id = ?");
                                $postCountStmt->execute([$row['id']]);
                                $postCount = $postCountStmt->fetchColumn();
                            } else {
                                $postCount = $row['post_count'];
                            }

                            echo '<tr role="row">
                        <td class="text-left py-3" role="gridcell">
                            <span class="fw-medium">' . htmlspecialchars($row['category']) . '</span>
                        </td>
                        <td class="py-3" style="text-align: center;" role="gridcell">
                            <span class="badge bg-info">' . number_format($postCount) . ' posts</span>
                        </td>
                        <td class="actions" style="text-align: center;" role="gridcell">
                            <div class="table-dropdown">
                                <button class="actions-btn" aria-haspopup="true" aria-expanded="false" aria-label="Actions for category ' . htmlspecialchars($row['category']) . '">
                                    <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">
                                        <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/>
                                    </svg>
                                </button>
                                <div class="table-dropdown-items" role="menu" aria-label="Category Actions">
                                    <div role="menuitem">
                                        <a href="?edit=' . $row['id'] . '" class="green" tabindex="-1" aria-label="Edit category ' . htmlspecialchars($row['category']) . '">
                                            <i class="fas fa-edit" aria-hidden="true"></i>
                                            <span>&nbsp;Edit</span>
                                        </a>
                                    </div>
                                    <div role="menuitem">
                                        <a class="red" href="?delete=' . $row['id'] . '" onclick="return confirm(\'Are you sure you want to delete this category and all its posts?\')" tabindex="-1" aria-label="Delete category ' . htmlspecialchars($row['category']) . '">
                                            <i class="fas fa-trash" aria-hidden="true"></i>
                                            <span>&nbsp;Delete</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
            <div class="card-footer bg-light">
        <div class="small">
            <span>Showing <?= count($categories) ?> categor<?= count($categories) != 1 ? 'ies' : 'y' ?></span>
        </div>
    </div>
    </div>

</div>
<?= template_admin_footer(); ?>