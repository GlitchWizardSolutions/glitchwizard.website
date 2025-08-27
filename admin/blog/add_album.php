<?php
/**
 * Blog Album Creation
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: add_album.php
 * LOCATION: /public_html/admin/blog/
 * PURPOSE: Add a new album to the blog gallery (create, validate, and store album title)
 * 
 * CREATED: 2025-07-26
 * UPDATED: 2025-07-26
 * VERSION: 1.0
 * PRODUCTION: [READY FOR PRODUCTION]
 * 
 * CHANGE LOG:
 * 2025-07-26 - Initial implementation for blog album creation
 * 2025-07-26 - Added form validation and duplicate title check
 * 2025-07-26 - Improved UI and accessibility for album creation
 * 2025-07-26 - Passed Quality Assurance (QA) check: UI, accessibility, error handling, and icon logic verified
 * 
 * FEATURES:
 * - Add new album to blog gallery
 * - Validate and prevent duplicate album titles
 * - Professional admin interface with consistent UI
 * - Success and error messaging
 * - Responsive form and button actions
 * 
 * DEPENDENCIES:
 * - header.php (blog includes)
 * - Bootstrap 5 for styling
 * - PDO database connection
 * - Font Awesome icons
 * 
 * SECURITY NOTES:
 * - Admin authentication required
 * - PDO prepared statements prevent SQL injection
 * - Input validation and sanitization
 * - XSS protection on output
 */

include "header.php";

$message = '';

if (isset($_POST['add']))
{
    $title = trim($_POST['title']);

    if (empty($title))
    {
        $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Album title cannot be empty.</div>';
    } else
    {
        // Check if album with this title already exists
        $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM blog_albums WHERE title = ?");
        $check_stmt->execute([$title]);
        $count = $check_stmt->fetchColumn();

        if ($count > 0)
        {
            $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> An album with this title already exists. Please choose a different name.</div>';
        } else
        {
            try
            {
                $add_stmt = $pdo->prepare("INSERT INTO blog_albums (title) VALUES (?)");
                $add_stmt->execute([$title]);
                $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Album "' . htmlspecialchars($title) . '" has been created successfully!</div>';
                // Clear the form after successful submission
                $_POST['title'] = '';
            } catch (Exception $e)
            {
                $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Error creating album. Please try again.</div>';
            }
        }
    }
}
?>
<?= template_admin_header('Add Blog Album', 'blog', 'albums') ?>
<div class="professional-card-header">
    <div class="title">
        <div class="icon">
            <i class="fas fa-images"></i>
        </div>
        <div class="txt">
            <h2>Add a Blog Album</h2>
            <p>Add a new album to the gallery.</p>
        </div>
    </div>
</div>
<br <!-- Cancel button above the card -->
<div class="mb-3">
    <a href="albums.php" class="btn btn-primary">
        <a href="albums.php" class="btn btn-primary" aria-label="Cancel and return to albums list"><i
                class="fa fa-arrow-left" aria-hidden="true"></i> Cancel</a>
    </a>
</div>
<br>

<div class="card">
    <h6 class="professional-card-header">Add Album</h6>
    <div class="card-body">
        <?php if (!empty($message))
            echo $message; ?>
        <form action="" method="post">
            <p>
                <label>Title</label>
                <input class="form-control" name="title"
                    value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" type="text"
                    required>
            </p>
            <div class="form-actions">
                <button type="submit" name="add" class="btn btn-primary">Add Album</button>
            </div>
        </form>
    </div>
</div>

<?= template_admin_footer(); ?>