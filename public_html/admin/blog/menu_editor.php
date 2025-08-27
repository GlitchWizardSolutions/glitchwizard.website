<?php
/*
 * SYSTEM: GWS Universal Hybrid Application
 * LOG: Menu editor for managing navigation menu items with reordering functionality
 * PRODUCTION: [To be updated on deployment]
 */

include "header.php";

if (isset($_GET['up']))
{
    $current_id = (int) $_GET["up"];

    // Get previous menu item ID
    $previous_menu_stmt = $pdo->prepare("SELECT id FROM `blog_menu` WHERE id < ? ORDER BY id DESC LIMIT 1");
    $previous_menu_stmt->execute([$current_id]);
    $previous_id = $previous_menu_stmt->fetchColumn();

    if ($previous_id)
    {
        // Swap menu item positions using temporary ID
        $update_temp_stmt = $pdo->prepare("UPDATE blog_menu SET id = '9999999' WHERE id = ?");
        $update_temp_stmt->execute([$previous_id]);

        $update_current_stmt = $pdo->prepare("UPDATE blog_menu SET id = ? WHERE id = ?");
        $update_current_stmt->execute([$previous_id, $current_id]);

        $update_previous_stmt = $pdo->prepare("UPDATE blog_menu SET id = ? WHERE id = '9999999'");
        $update_previous_stmt->execute([$current_id]);
    }
}

if (isset($_GET['down']))
{
    $current_id = (int) $_GET["down"];

    // Get next menu item ID
    $next_menu_stmt = $pdo->prepare("SELECT id FROM `blog_menu` WHERE id > ? ORDER BY id ASC LIMIT 1");
    $next_menu_stmt->execute([$current_id]);
    $next_id = $next_menu_stmt->fetchColumn();

    if ($next_id)
    {
        // Swap menu item positions using temporary ID
        $update_temp_stmt = $pdo->prepare("UPDATE blog_menu SET id = '9999998' WHERE id = ?");
        $update_temp_stmt->execute([$next_id]);

        $update_current_stmt = $pdo->prepare("UPDATE blog_menu SET id = ? WHERE id = ?");
        $update_current_stmt->execute([$next_id, $current_id]);

        $update_next_stmt = $pdo->prepare("UPDATE blog_menu SET id = ? WHERE id = '9999998'");
        $update_next_stmt->execute([$current_id]);
    }
}

if (isset($_GET['delete']))
{
    $delete_id = (int) $_GET["delete"];
    $delete_stmt = $pdo->prepare("DELETE FROM `blog_menu` WHERE id = ?");
    $delete_stmt->execute([$delete_id]);
}

// Get total menu items for is_first/last logic
$count_stmt = $pdo->prepare("SELECT MAX(id) as max_id FROM `blog_menu`");
$count_stmt->execute();
$last_id = $count_stmt->fetchColumn();
?>

<?= template_admin_header('Menu Editor', 'blog', 'menu') ?>

<div class="content-title mb-4">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" aria-hidden="true" focusable="false">
                <path d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z"/>
            </svg>
        </div>
        <div class="txt">
            <h2>Menu Editor</h2>
            <p>Manage blog navigation menu items with reordering functionality.</p>
        </div>
    </div>
</div>

<div style="height: 20px;"></div>

<div class="mb-3">
    <a href="add_menu.php" class="btn btn-success" aria-label="Add new menu item">
        <i class="fas fa-plus me-1"></i>Add Menu Item
    </a>
</div>

<?php
if (isset($_GET['edit']))
{
    $edit_id = (int) $_GET["edit"];
    $edit_stmt = $pdo->prepare("SELECT * FROM `blog_menu` WHERE id = ?");
    $edit_stmt->execute([$edit_id]);
    $edit_row = $edit_stmt->fetch(PDO::FETCH_ASSOC);

    if (empty($edit_id))
    {
        echo '<meta http-equiv="refresh" content="0; url=menu_editor.php">';
        exit;
    }
    if (!$edit_row)
    {
        echo '<meta http-equiv="refresh" content="0; url=menu_editor.php">';
        exit;
    }

    if (isset($_POST['submit']))
    {
        $page = $_POST['page'];
        $path = $_POST['path'];
        $fa_icon = $_POST['fa_icon'];

        $update_menu_stmt = $pdo->prepare("UPDATE blog_menu SET page = ?, path = ?, fa_icon = ? WHERE id = ?");
        $update_menu_stmt->execute([$page, $path, $fa_icon, $edit_id]);
        echo '<meta http-equiv="refresh" content="0;url=menu_editor.php">';
    }
}
?>

<?php if (isset($_GET['edit'])): ?>
    <div class="card mb-3">
        <h6 class="card-header">Edit Menu Item</h6>
        <div class="card-body">
            <form action="" method="post">
                <div class="mb-3">
                    <label for="page" class="form-label">Page Name</label>
                    <input name="page" id="page" class="form-control" type="text" value="<?= htmlspecialchars($edit_row['page']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="path" class="form-label">Path (Link)</label>
                    <input name="path" id="path" class="form-control" type="text" value="<?= htmlspecialchars($edit_row['path']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="fa_icon" class="form-label">Font Awesome 5 Icon</label>
                    <input name="fa_icon" id="fa_icon" class="form-control" type="text" value="<?= htmlspecialchars($edit_row['fa_icon']) ?>" placeholder="fa-home">
                </div>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="menu_editor.php" class="btn btn-outline-secondary me-md-2" aria-label="Cancel and return to menu editor">
                        <i class="fa fa-arrow-left" aria-hidden="true"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-success" name="submit" aria-label="Save menu changes">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php
// --- Sorting Setup ---
$table_icons = [
    'asc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9650;</span>',
    'desc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9660;</span>'
];
$order = isset($_GET['order']) && strtoupper($_GET['order']) === 'DESC' ? 'DESC' : 'ASC';
$order_by_whitelist = [
    'id' => 'id',
    'page' => 'page',
    'path' => 'path'
];
$order_by = isset($_GET['order_by']) && isset($order_by_whitelist[$_GET['order_by']]) ? $_GET['order_by'] : 'id';
$order_by_sql = $order_by_whitelist[$order_by];
?>

<div class="card">
    <h6 class="card-header">Menu Items</h6>
    <div class="card-body p-0">
        <div class="table" role="table" aria-label="Blog Menu Items">
            <table role="grid">
                <thead role="rowgroup">
                    <tr role="row">
                        <th class="text-left" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'id'; $q['order'] = ($order_by == 'id' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="sort-header">Order<?= $order_by == 'id' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th class="text-left" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'page'; $q['order'] = ($order_by == 'page' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="sort-header">Page<?= $order_by == 'page' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th class="text-left" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'path'; $q['order'] = ($order_by == 'path' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="sort-header">Path<?= $order_by == 'path' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th role="columnheader" scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody role="rowgroup">
            <?php
            $menu_query = $pdo->prepare("SELECT * FROM blog_menu ORDER BY $order_by_sql $order");
            $menu_query->execute();
            $menu_items = $menu_query->fetchAll(PDO::FETCH_ASSOC);

            $is_first = true;
            foreach ($menu_items as $menu_row):
            ?>
                <tr role="row">
                    <td role="gridcell"><?= htmlspecialchars($menu_row['id']) ?></td>
                    <td class="text-left" role="gridcell">
                        <i class="fa <?= htmlspecialchars($menu_row['fa_icon']) ?>"></i> 
                        <?= htmlspecialchars($menu_row['page']) ?>
                    </td>
                    <td class="text-left" role="gridcell"><?= htmlspecialchars($menu_row['path']) ?></td>
                    <td class="actions text-center" role="gridcell">
                        <div class="table-dropdown">
                            <button class="actions-btn" aria-haspopup="true" aria-expanded="false" aria-label="Actions for menu item <?= htmlspecialchars($menu_row['page']) ?>">
                                <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">
                                    <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/>
                                </svg>
                            </button>
                            <div class="table-dropdown-items" role="menu" aria-label="Menu Item Actions">
                                <?php if (!$is_first): ?>
                                <div role="menuitem">
                                    <a href="?up=<?= $menu_row['id'] ?>" class="green" tabindex="-1" aria-label="Move <?= htmlspecialchars($menu_row['page']) ?> up">
                                        <i class="fas fa-arrow-up" aria-hidden="true"></i>
                                        <span>&nbsp;Move Up</span>
                                    </a>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($menu_row['id'] != $last_id): ?>
                                <div role="menuitem">
                                    <a href="?down=<?= $menu_row['id'] ?>" class="green" tabindex="-1" aria-label="Move <?= htmlspecialchars($menu_row['page']) ?> down">
                                        <i class="fas fa-arrow-down" aria-hidden="true"></i>
                                        <span>&nbsp;Move Down</span>
                                    </a>
                                </div>
                                <?php endif; ?>
                                
                                <div role="menuitem">
                                    <a href="?edit=<?= $menu_row['id'] ?>" class="green" tabindex="-1" aria-label="Edit menu item <?= htmlspecialchars($menu_row['page']) ?>">
                                        <i class="fas fa-edit" aria-hidden="true"></i>
                                        <span>&nbsp;Edit</span>
                                    </a>
                                </div>
                                <div role="menuitem">
                                    <a href="?delete=<?= $menu_row['id'] ?>" class="red" onclick="return confirm('Are you sure you want to delete this menu item?')" tabindex="-1" aria-label="Delete menu item <?= htmlspecialchars($menu_row['page']) ?>">
                                        <i class="fas fa-trash" aria-hidden="true"></i>
                                        <span>&nbsp;Delete</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php 
                $is_first = false;
            endforeach; 
            ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
<?= template_admin_footer(); ?>