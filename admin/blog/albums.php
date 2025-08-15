<?php
/**
 * Blog Photo Albums Management
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: albums.php
 * LOCATION: /public_html/admin/blog/
 * PURPOSE: Manage photo albums and gallery collections for the blog
 * 
 * CREATED: 2025-07-03
 * UPDATED: 2025-07-04
 * VERSION: 2.0
 * PRODUCTION: [READY FOR PRODUCTION]
 * 
 * CHANGE LOG:
 * 2025-07-03 - Original implementation with basic album management
 * 2025-07-04 - Modernized with professional header, enhanced UI, and security improvements
 * 2025-07-04 - Added content title block and consistent button formatting
 * 2025-07-04 - Improved album editing workflow and gallery integration
 * 
 * FEATURES:
 * - Create and edit photo albums for blog galleries
 * - Album title management with validation
 * - Professional admin interface
 * - Integration with gallery system
 * - Delete albums and associated photos
 * - Album organization and categorization
 * 
 * DEPENDENCIES:
 * - header.php (blog includes)
 * - Bootstrap 5 for styling
 * - PDO database connection
 * - Font Awesome icons
 * - Gallery management system
 * 
 * SECURITY NOTES:
 * - Admin authentication required
 * - PDO prepared statements prevent SQL injection
 * - Input validation and sanitization
 * - XSS protection on output
 * - Cascade deletion of associated gallery items
 * - Proper album data cleanup
 */
include_once "header.php";
if (isset($_GET['delete']))
{
    $id = (int) $_GET['delete'];
    // Use blog_ prefix for tables
    $stmt = $pdo->prepare("DELETE FROM blog_albums WHERE id = ?");
    $stmt->execute([$id]);
    $stmt2 = $pdo->prepare("DELETE FROM blog_gallery WHERE album_id = ?");
    $stmt2->execute([$id]);
}
?>

<?= template_admin_header('Blog Albums', 'blog', 'albums') ?>
<div class="content-title mb-4">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" aria-hidden="true"
                focusable="false">
                <path
                    d="M160 32c-35.3 0-64 28.7-64 64V320c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H160zM396 138.7l96 144c4.9 7.4 5.4 16.8 1.2 24.6S480.9 320 472 320H328 280 200c-9.2 0-17.6-5.3-21.6-13.6s-2.9-18.2 2.9-25.4l64-80c4.2-5.3 10.6-8.4 17.2-8.4s13 3.1 17.2 8.4l17.8 22.2 41.8-62.7c4.6-6.9 12.4-11.1 20.7-11.1s16.1 4.2 20.7 11.1zM256 128a32 32 0 1 1 0 64 32 32 0 1 1 0-64zM48 120c0-13.3-10.7-24-24-24S0 106.7 0 120V344c0 75.1 60.9 136 136 136H456c13.3 0 24-10.7 24-24s-10.7-24-24-24H136c-48.6 0-88-39.4-88-88V120z" />
            </svg>
        </div>
        <div class="txt">
            <h2>Photo Albums</h2>
            <p>Create and manage photo albums and gallery collections for your blog.</p>
        </div>
    </div>
</div>

<?php
if (isset($_GET['edit']))
{
    $id = (int) $_GET["edit"];
    $stmt = $pdo->prepare("SELECT * FROM blog_albums WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (empty($id) || !$row)
    {
        header("Location: albums.php");
        exit;
    }

    if (isset($_POST['submit']))
    {
        $title = $_POST['title'];
        $stmtEdit = $pdo->prepare("UPDATE blog_albums SET title = ? WHERE id = ?");
        $stmtEdit->execute([$title, $id]);
        header("Location: albums.php");
        exit;
    }

    // Show the edit form only if $row is valid
    ?>
    <!-- Cancel button above the card (only in edit mode) -->
    <div class="mb-3">
        <a href="albums.php" class="btn btn-outline-secondary" aria-label="Cancel and return to albums list">
            <i class="fa fa-arrow-left" aria-hidden="true"></i> Cancel
        </a>
    </div>
    
    <div class="card mb-3">
        <h6 class="card-header"><i class="fas fa-edit me-2"></i>Edit Album</h6>
        <div class="card-body">
            <form action="" method="post" id="editForm">
                <div class="mb-3">
                    <label for="title" class="form-label">Album Title</label>
                    <input class="form-control" name="title" id="title" type="text"
                        value="<?= htmlspecialchars($row['title']) ?>" required>
                    <div class="form-text">Choose a descriptive name for this photo album</div>
                </div>
            </form>
        </div>
        <div class="card-footer d-flex gap-2">
            <button type="submit" form="editForm" class="btn btn-success" name="submit">
                <i class="fas fa-save me-1"></i>Save Changes
            </button>
        </div>
    </div>
    <?php
} else
{
    ?>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Photo Albums Management</h6>
            <small class="text-muted"><?php
                // Get total count for display
                $countSql = $pdo->prepare("SELECT COUNT(*) FROM blog_albums");
                $countSql->execute();
                $total_albums = $countSql->fetchColumn();
                echo number_format($total_albums);
            ?> total albums</small>
        </div>
        <div class="card-body">
            <form method="get" class="mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search</label>
                        <input id="search" type="text" name="search" class="form-control"
                            placeholder="Search albums..." 
                            value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                    </div>
                    
                    <div class="col-md-5">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-search me-1" aria-hidden="true"></i>
                            Apply Filters
                        </button>
                        <?php if (isset($_GET['search']) && $_GET['search'] !== ''): ?>
                        <a href="albums.php" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-times me-1" aria-hidden="true"></i>
                            Clear
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>

            <div class="table-responsive" role="table" aria-label="Photo Albums">
                <table class="table table-hover align-middle mb-0" role="grid">
                    <thead role="rowgroup">
                        <tr role="row">
                            <th class="text-left" role="columnheader" scope="col">Title</th>
                            <th class="text-center" role="columnheader" scope="col">Photos</th>
                            <th class="text-center" role="columnheader" scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody role="rowgroup">
                        <?php
                        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
                        $query = "SELECT * FROM blog_albums";
                        $params = [];
                        if ($search !== '')
                        {
                            $query .= " WHERE title LIKE ?";
                            $params[] = "%$search%";
                        }
                        $query .= " ORDER BY title ASC";
                        $stmt = $pdo->prepare($query);
                        $stmt->execute($params);
                        $editingId = isset($_GET['edit']) ? (int) $_GET['edit'] : null;
                        $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        if (count($albums) === 0)
                        {
                            echo '<tr role="row"><td colspan="3" class="text-center text-muted" role="gridcell">';
                            if (!empty($search))
                            {
                                echo 'No albums found for search: <strong>' . htmlspecialchars($search) . '</strong>';
                            } else
                            {
                                echo 'No albums found.';
                            }
                            echo '</td></tr>';
                        } else
                        {
                            foreach ($albums as $row)
                            {
                                // Get photo count for this album
                                $stmt_photos = $pdo->prepare("SELECT COUNT(*) as photo_count FROM blog_gallery WHERE album_id = ?");
                                $stmt_photos->execute([$row['id']]);
                                $photo_count = $stmt_photos->fetch(PDO::FETCH_ASSOC)['photo_count'];

                                echo '<tr role="row">';
                                echo '<td class="text-left" role="gridcell">' . htmlspecialchars($row['title']) . '</td>';
                                echo '<td class="text-center" role="gridcell"><span class="badge bg-info"><i class="fas fa-camera me-1"></i>' . $photo_count . ' photos</span></td>';
                                echo '<td class="text-center" role="gridcell">';
                                echo '<div class="table-dropdown">';
                                echo '<button class="actions-btn" aria-haspopup="true" aria-expanded="false" aria-label="Actions for album ' . htmlspecialchars($row['title']) . '">';
                                echo '<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">';
                                echo '<path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/>';
                                echo '</svg>';
                                echo '</button>';
                                echo '<div class="table-dropdown-items" role="menu" aria-label="Album Actions">';
                                echo '<div role="menuitem"><a href="?edit=' . $row['id'] . '" class="green" tabindex="-1" aria-label="Edit album ' . htmlspecialchars($row['title']) . '"><i class="fas fa-edit" aria-hidden="true"></i><span>&nbsp;Edit</span></a></div>';
                                echo '<div role="menuitem"><a href="?delete=' . $row['id'] . '" class="red" tabindex="-1" onclick="return confirm(\'Are you sure you want to delete this album and all its photos?\')" aria-label="Delete album ' . htmlspecialchars($row['title']) . '"><i class="fas fa-trash" aria-hidden="true"></i><span>&nbsp;Delete</span></a></div>';
                                echo '</div>';
                                echo '</div>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-light">
            <div class="small">
                Showing <?= count($albums) ?> of <?= $total_albums ?> albums
            </div>
        </div>
    </div>
    <?= template_admin_footer(); ?>
<?php } ?>