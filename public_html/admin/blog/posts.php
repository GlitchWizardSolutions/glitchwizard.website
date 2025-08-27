<?php
/* 
 * Blog Posts Management
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: posts.php
 * LOCATION: /public_html/admin/blog/
 * PURPOSE: Manage blog posts and content
 * DETAILED DESCRIPTION:
 * This file provides a comprehensive interface for managing blog posts,
 * including creation, editing, deletion, and organization of blog content.
 * It supports rich text editing, media embedding, post scheduling, and
 * various post-specific settings and metadata management.
 * REQUIRED FILES: 
 * - /public_html/assets/includes/main.php
 * - /public_html/assets/includes/settings/blog_config.php
 * - /public_html/assets/includes/settings/editor_config.php
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: [IN DEVELOPMENT]
 * 
 * FEATURES:
 * - Post creation and editing
 * - Rich text editor
 * - Media management
 * - Post scheduling
 * - Category assignment
 */
include_once "header.php";
function show_alert($message, $type = "danger")
{
    echo '<div class="alert alert-' . htmlspecialchars($type) . '" role="alert" aria-live="assertive">' . htmlspecialchars($message) . '</div>';
}

// --- Pagination and Search Setup ---

$per_page = 10;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$offset = ($page - 1) * $per_page;

// Filters
$post_id = isset($_GET['post_id']) ? trim($_GET['post_id']) : '';
$username = isset($_GET['username']) ? trim($_GET['username']) : '';
$approved = isset($_GET['approved']) ? trim($_GET['approved']) : '';
$category_filter = isset($_GET['category_filter']) ? trim($_GET['category_filter']) : '';

// Build WHERE clause for search and filters
$where = [];
$params = [];
if ($search !== '')
{
    $where[] = "title LIKE ?";
    $params[] = "%$search%";
}
if ($post_id !== '')
{
    $where[] = "id = ?";
    $params[] = $post_id;
}
if ($username !== '')
{
    $where[] = "author_id = ?";
    $params[] = $username;
}
if ($approved !== '')
{
    $where[] = "active = ?";
    $params[] = $approved;
}
if ($category_filter !== '')
{
    $where[] = "category_id = ?";
    $params[] = $category_filter;
}
$where_sql = '';
if (count($where) > 0)
{
    $where_sql = 'WHERE ' . implode(' AND ', $where);
}

// Get total count for pagination
$stmt = $pdo->prepare("SELECT COUNT(*) FROM blog_posts $where_sql");
$stmt->execute($params);
$total_posts = $stmt->fetchColumn();
$total_pages = ceil($total_posts / $per_page);


// --- Sorting Setup (like blog_dash.php) ---
$table_icons = [
    'asc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9650;</span>',
    'desc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9660;</span>'
];
$order = isset($_GET['order']) && strtoupper($_GET['order']) === 'DESC' ? 'DESC' : 'ASC';
$order_by_whitelist = [
    'title' => 'title',
    'author' => 'author_id',
    'date' => 'date',
    'status' => 'active',
    'category' => 'category_id'
];
$order_by = isset($_GET['order_by']) && isset($order_by_whitelist[$_GET['order_by']]) ? $_GET['order_by'] : 'date';
$order_by_sql = $order_by_whitelist[$order_by];

// Get posts for current page with sorting
$sql = "SELECT * FROM blog_posts $where_sql ORDER BY $order_by_sql $order, id DESC LIMIT $per_page OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle delete
if (isset($_GET['delete']))
{
    $id = (int) $_GET["delete"];
    // Get image filename before deleting post
    $stmtImg = $pdo->prepare("SELECT image FROM blog_posts WHERE id = ?");
    $stmtImg->execute([$id]);
    $imgRow = $stmtImg->fetch(PDO::FETCH_ASSOC);
    if ($imgRow && !empty($imgRow['image'])) {
        $imagePath = __DIR__ . '/' . $imgRow['image'];
        if (file_exists($imagePath) && is_file($imagePath)) {
            @unlink($imagePath);
        }
    }
    // Delete post and comments
    $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
    $stmt->execute([$id]);
    $stmt2 = $pdo->prepare("DELETE FROM blog_comments WHERE post_id = ?");
    $stmt2->execute([$id]);
    header("Location: posts.php");
    exit;
}
?>
<?= template_admin_header('Blog Posts', 'blog', 'posts') ?>

<?php
// Handle edit
if (isset($_GET['edit']))
{
    $id = (int) $_GET["edit"];
    $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (empty($id) || !$row)
    {
        header("Location: posts.php");
        exit;
    }

    if (isset($_POST['submit']))
    {
        $title = $_POST['title'];
        $slug = generateSeoURL($title);
        $image = $row['image'];
        $active = $_POST['active'];
        $featured = $_POST['featured'];
        $category_id = $_POST['category_id'];
        $content = htmlspecialchars($_POST['content']);
        $date = date('n/j/Y');
        $time = date('h:i A');

        $error = '';
        if (@$_FILES['image']['name'] != '')
        {
            $baseName = pathinfo($_FILES["image"]["name"], PATHINFO_FILENAME);
            $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            $date_str = date('Y-m-d');
            // Correct upload directory for blog_post_images (relative to this file)
            $uploadDir = __DIR__ . "/blog_post_images/";
            if (!is_dir($uploadDir))
            {
                mkdir($uploadDir, 0777, true);
            }
            $newFileName = $baseName . '-' . $date_str . '.' . $imageFileType;
            $location = $uploadDir . $newFileName;
            $counter = 1;
            while (file_exists($location))
            {
                $newFileName = $baseName . '-' . $date_str . "($counter)." . $imageFileType;
                $location = $uploadDir . $newFileName;
                $counter++;
            }
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if ($check === false)
            {
                $error = 'The file is not an image.';
            } elseif ($_FILES["image"]["size"] > 10000000)
            {
                $error = 'Sorry, your file is too large.';
            } else
            {
                move_uploaded_file($_FILES["image"]["tmp_name"], $location);
                $image = "blog_post_images/" . $newFileName;
            }
        }

        if (!empty($error))
        {
            show_alert($error, "danger");
        } else
        {
            $stmtEdit = $pdo->prepare("UPDATE blog_posts SET title=?, slug=?, image=?, active=?, featured=?, date=?, time=?, category_id=?, content=? WHERE id=?");
            $stmtEdit->execute([$title, $slug, $image, $active, $featured, $date, $time, $category_id, $content, $id]);
            header("Location: posts.php");
            exit;
        }
    }
    ?>

    <div class="card mb-3">
        <h6 class="card-header"><i class="fas fa-edit me-2"></i>Edit Post</h6>
        <div class="card-body">
            <form name="post_form" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) . '?edit=' . $id ?>" method="post"
                enctype="multipart/form-data" aria-label="Edit Blog Post">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Post Title</label>
                            <input class="form-control" name="title" id="title" type="text"
                                value="<?= htmlspecialchars($row['title']) ?>" oninput="countText()" required
                                aria-required="true">
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>For best SEO keep title under 50 characters.
                                <span id="characters-label">Characters: </span>
                                <span id="characters" class="fw-bold"><?= strlen($row['title']) ?></span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="summernote" class="form-label">Post Content</label>
                            <textarea name="content" id="summernote" rows="8" required
                                aria-required="true"><?= html_entity_decode($row['content']) ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="image" class="form-label">Featured Image</label>
                            <?php if ($row['image'] != ''): ?>
                                <div class="mb-2">
                                    <img src="blog_post_images/<?= htmlspecialchars(basename($row['image'])) ?>"
                                        class="img-thumbnail" style="max-width: 200px;"
                                        alt="<?= htmlspecialchars($row['title']) ?>">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="image" id="image" class="form-control" aria-label="Upload post image">
                            <div class="form-text"><i class="fas fa-image me-1"></i>Recommended size: 800x600px or larger
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category</label>
                            <select name="category_id" id="category_id" class="form-select" required aria-required="true">
                                <?php
                                $stmtCat = $pdo->query("SELECT * FROM blog_categories ORDER BY category ASC");
                                $categories = $stmtCat->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($categories as $rw)
                                {
                                    $selected = ($row['category_id'] == $rw['id']) ? "selected" : "";
                                    echo '<option value="' . $rw['id'] . '" ' . $selected . '>' . htmlspecialchars($rw['category']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="active" class="form-label">Status</label>
                                    <select name="active" id="active" class="form-select" required aria-required="true">
                                        <option value="Yes" <?= $row['active'] == "Yes" ? 'selected' : '' ?>>Published</option>
                                        <option value="No" <?= $row['active'] == "No" ? 'selected' : '' ?>>Draft</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="featured" class="form-label">Featured</label>
                                    <select name="featured" id="featured" class="form-select" required aria-required="true">
                                        <option value="Yes" <?= $row['featured'] == "Yes" ? 'selected' : '' ?>>Yes</option>
                                        <option value="No" <?= $row['featured'] == "No" ? 'selected' : '' ?>>No</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 pt-3 border-top">
                    <a href="posts.php" class="btn btn-outline-secondary" aria-label="Cancel and return to posts list"><i
                            class="fa fa-arrow-left" aria-hidden="true"></i> Cancel</a>
                    <button type="submit" class="btn btn-success" name="submit" aria-label="Save Post"><i
                            class="fas fa-save me-1"></i>Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $('#summernote').summernote({ height: 350 });
            var noteBar = $('.note-toolbar');
            noteBar.find('[data-toggle]').each(function () {
                $(this).attr('data-bs-toggle', $(this).attr('data-toggle')).removeAttr('data-toggle');
            });
        });
    </script>
    <?= template_admin_footer(); ?>
    <?php exit;
}
?>



<div class="container-fluid">
    <!-- Action Buttons Row -->
    <?php if (isset($_GET['edit'])): ?>
        <div class="row mb-3" style="gap: 10px; align-items: center;">
            <div class="col-auto p-0">
                <a href="posts.php" class="btn btn-outline-secondary" aria-label="Cancel and return to posts list"><i
                        class="fa fa-arrow-left" aria-hidden="true"></i> Cancel</a>
            </div>
        </div>
    <?php else: ?>
        <div class="row mb-4" style="gap: 10px; align-items: center;">
            <div class="col-auto p-0">
                <a href="add_post.php" class="btn btn-outline-secondary">
                    <i class="fas fa-plus me-1"></i>New Post
                </a>
            </div>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-4 px-4 branding-settings-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-white">
                    <span class="header-icon"><i class="bi bi-file-earmark-text" aria-hidden="true"></i></span>
                    Blog Post Management
                </h6>
                <span class="text-white" style="font-size: 0.875rem;"><?=number_format($total_posts)?> total posts</span>
            </div>
        </div>
    <div class="card-body p-0">
        <div class="container-fluid py-3 px-4">
            <form action="" method="get" class="mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="search_query" class="form-label">Search</label>
                        <input id="search_query" type="text" name="search_query" class="form-control"
                            placeholder="Search posts..." 
                            value="<?=htmlspecialchars($search, ENT_QUOTES)?>">
                    </div>
                <div class="col-md-2">
                    <label for="approved" class="form-label">Status</label>
                    <select name="approved" id="approved" class="form-select">
                        <option value="" <?= $approved == '' ? 'selected' : '' ?>>All</option>
                        <option value="Yes" <?= $approved == 'Yes' ? 'selected' : '' ?>>Published</option>
                        <option value="No" <?= $approved == 'No' ? 'selected' : '' ?>>Draft</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="username" class="form-label">Author</label>
                    <select name="username" id="username" class="form-select">
                        <option value="">All Authors</option>
                        <?php
                        $stmt = $pdo->query('SELECT id, username FROM accounts ORDER BY username');
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
                        {
                            echo '<option value="' . $row['id'] . '"' . ($username == $row['id'] ? ' selected' : '') . '>' . htmlspecialchars($row['username'], ENT_QUOTES) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="category_filter" class="form-label">Category</label>
                    <select name="category_filter" id="category_filter" class="form-select">
                        <option value="">All Categories</option>
                        <?php
                        $stmt = $pdo->query('SELECT id, category FROM blog_categories ORDER BY category');
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
                        {
                            echo '<option value="' . $row['id'] . '"' . ($category_filter == $row['id'] ? ' selected' : '') . '>' . htmlspecialchars($row['category'], ENT_QUOTES) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-search me-1" aria-hidden="true"></i>
                        Apply Filters
                    </button>
                    <?php if ($search || $approved || $username || $category_filter): ?>
                    <a href="posts.php" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-times me-1" aria-hidden="true"></i>
                        Clear
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0" role="grid" aria-label="Blog Posts">
                <thead class="table-light" role="rowgroup">
                    <tr role="row">
                        <th style="width: 90px; text-align: center;" role="columnheader" scope="col">Image</th>
                        <th class="text-start" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'title'; $q['order'] = ($order_by == 'title' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none">Title<?= $order_by == 'title' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th class="text-start" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'author'; $q['order'] = ($order_by == 'author' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none">Author<?= $order_by == 'author' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th class="text-center" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'date'; $q['order'] = ($order_by == 'date' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none">Date<?= $order_by == 'date' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th class="text-center" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'status'; $q['order'] = ($order_by == 'status' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none">Status<?= $order_by == 'status' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th class="text-center" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'category'; $q['order'] = ($order_by == 'category' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none">Category<?= $order_by == 'category' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th class="text-center" role="columnheader" scope="col">Actions</th>
                    </tr>
                </thead>
            <tbody role="rowgroup">
                <?php
                foreach ($posts as $row)
                {
                    $category_id = isset($row['category_id']) ? $row['category_id'] : null;
                    $stmtCat = $pdo->prepare("SELECT * FROM blog_categories WHERE id = ?");
                    $stmtCat->execute([$category_id]);
                    $cat = $stmtCat->fetch(PDO::FETCH_ASSOC);

                    $featured = "";
                    if (isset($row['featured']) && $row['featured'] == "Yes")
                    {
                        $featured = '<span class="badge bg-primary">Featured</span>';
                    }

                    echo '<tr role="row">';
                    echo '<td style="text-align: center;" role="gridcell">';
                    if ($row['image'] != '')
                    {
                        echo '<div class="text-center"><img src="blog_post_images/' . htmlspecialchars(basename($row['image'])) . '" class="rounded" width="60" height="60" style="object-fit: cover;" alt="' . htmlspecialchars($row['title']) . '" /></div>';
                    } else
                    {
                        echo '<div class="text-center text-muted"><i class="fas fa-image fa-2x"></i></div>';
                    }
                    echo '</td>';
                    echo '<td class="text-left" role="gridcell">' . htmlspecialchars($row['title']) . ' ' . $featured . '</td>';
                    echo '<td class="text-left" role="gridcell">' . htmlspecialchars(post_author($row['author_id'])) . '</td>';
                    echo '<td style="text-align: center;" role="gridcell">' . date('n/j/Y', strtotime($row['date'])) . '</td>';
                    echo '<td style="text-align: center;" role="gridcell">';
                    if ($row['active'] == "Yes")
                    {
                        echo '<span class="green">Published</span>';
                    } else
                    {
                        echo '<span class="grey">Draft</span>';
                    }
                    echo '</td>';
                    echo '<td style="text-align: center;" role="gridcell">' . htmlspecialchars($cat['category'] ?? 'Uncategorized') . '</td>';

                    // Canonical actions column structure from Tables.php
                    echo '<td class="actions" style="text-align: center;" role="gridcell">';
                    echo '<div class="table-dropdown">';
                    echo '<button class="actions-btn" aria-haspopup="true" aria-expanded="false" aria-label="Actions for post ' . htmlspecialchars($row['title']) . '">';
                    echo '<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;"><path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/></svg>';
                    echo '</button>';
                    echo '<div class="table-dropdown-items" role="menu" aria-label="Post Actions">';
                    echo '<div role="menuitem">';
                    echo '<a href="?edit=' . $row['id'] . '" class="green" tabindex="-1" aria-label="Edit post ' . htmlspecialchars($row['title']) . '">';
                    echo '<i class="fas fa-edit" aria-hidden="true"></i>';
                    echo '<span>&nbsp;Edit</span>';
                    echo '</a>';
                    echo '</div>';
                    echo '<div role="menuitem">';
                    echo '<a href="?delete=' . $row['id'] . '" class="red" onclick="return confirm(\'Are you sure you want to delete this post and all its comments?\')" tabindex="-1" aria-label="Delete post ' . htmlspecialchars($row['title']) . '">';
                    echo '<i class="fas fa-trash" aria-hidden="true"></i>';
                    echo '<span>&nbsp;Delete</span>';
                    echo '</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
            </table>
        </div>
    </div>
</div>

<div class="pagination">
    <?php if ($page > 1): ?>
    <a href="?page=<?= $page - 1 ?>&order=<?= $order ?>&order_by=<?= $order_by ?><?= !empty($search) ? '&search_query=' . urlencode($search) : '' ?><?= !empty($approved) ? '&approved=' . urlencode($approved) : '' ?><?= !empty($username) ? '&username=' . urlencode($username) : '' ?>">Prev</a>
    <?php endif; ?>
    <span>Page <?= $page ?> of <?= $total_pages == 0 ? 1 : $total_pages ?></span>
    <?php if ($page * $per_page < $total_posts): ?>
    <a href="?page=<?= $page + 1 ?>&order=<?= $order ?>&order_by=<?= $order_by ?><?= !empty($search) ? '&search_query=' . urlencode($search) : '' ?><?= !empty($approved) ? '&approved=' . urlencode($approved) : '' ?><?= !empty($username) ? '&username=' . urlencode($username) : '' ?>">Next</a>
    <?php endif; ?>
</div>
 
<script>
    $(document).ready(function () {
        $('#summernote').summernote({ height: 350 });
        var noteBar = $('.note-toolbar');
        noteBar.find('[data-toggle]').each(function () {
            $(this).attr('data-bs-toggle', $(this).attr('data-toggle')).removeAttr('data-toggle');
        });
    });
</script>
<?= template_admin_footer(); ?>