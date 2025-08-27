<?php
/* 
 * Blog Widgets Management
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: widgets.php
 * LOCATION: /public_html/admin/blog/
 * PURPOSE: Blog widget management and configuration interface
 * DETAILED DESCRIPTION:
 * This file provides an interface for managing blog widgets and content areas.
 * It allows administrators to create, edit, and organize widgets for sidebars,
 * headers, footers, and other widget-enabled areas of the blog system. The
 * interface supports drag-and-drop organization and real-time preview.
 * REQUIRED FILES: 
 * - /public_html/assets/includes/main.php
 * - /public_html/assets/includes/settings/widget_settings.php
 * - /public_html/assets/includes/settings/blog_config.php
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: [IN DEVELOPMENT]
 * 
 * FEATURES:
 * - Widget creation and editing
 * - Drag-and-drop organization
 * - Widget area management
 * - Real-time preview
 * - Widget configuration
 */
 
include_once "header.php";

if (isset($_GET['delete']))
{
    $id = (int) $_GET["delete"];
    $stmt = $pdo->prepare("DELETE FROM `blog_widgets` WHERE id = ?");
    $stmt->execute([$id]);
}
?>
<?= template_admin_header('Blog Widgets', 'blog', 'widgets') ?>

<div class="content-title mb-4" aria-label="Widgets Management">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" aria-hidden="true"
                focusable="false">
                <path
                    d="M0 96C0 60.7 28.7 32 64 32H448c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96zM64 160c-17.7 0-32 14.3-32 32v64c0 17.7 14.3 32 32 32H144c17.7 0 32-14.3 32-32V192c0-17.7-14.3-32-32-32H64zM208 160c-17.7 0-32 14.3-32 32v64c0 17.7 14.3 32 32 32H288c17.7 0 32-14.3 32-32V192c0-17.7-14.3-32-32-32H208zM352 160c-17.7 0-32 14.3-32 32v64c0 17.7 14.3 32 32 32H432c17.7 0 32-14.3 32-32V192c0-17.7-14.3-32-32-32H352zM64 304c-17.7 0-32 14.3-32 32v64c0 17.7 14.3 32 32 32H144c17.7 0 32-14.3 32-32V336c0-17.7-14.3-32-32-32H64zM208 304c-17.7 0-32 14.3-32 32v64c0 17.7 14.3 32 32 32H288c17.7 0 32-14.3 32-32V336c0-17.7-14.3-32-32-32H208zM352 304c-17.7 0-32 14.3-32 32v64c0 17.7 14.3 32 32 32H432c17.7 0 32-14.3 32-32V336c0-17.7-14.3-32-32-32H352z" />
            </svg>
        </div>
        <div class="txt">
            <h2>Widgets Management</h2>
            <p>Create and manage custom widgets for your blog's sidebar, header, and footer areas.</p>
        </div>
    </div>
</div>
 
<div class="mb-3">
    <a href="add_widget.php" class="btn btn-outline-secondary">
        <i class="fas fa-plus me-1"></i>Add Widget
    </a>
</div>
<?php if (isset($_GET['edit'])): ?>
    <?php
    $id = (int) $_GET["edit"];
    $stmt = $pdo->prepare("SELECT * FROM `blog_widgets` WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (empty($id) || !$row)
    {
        echo '<meta http-equiv="refresh" content="0; url=widgets.php">';
        exit;
    }

    if (isset($_POST['submit']))
    {
        $title = $_POST['title'];
        $position = $_POST['position'];
        $content = htmlspecialchars($_POST['content']);

        $stmtUpdate = $pdo->prepare("UPDATE blog_widgets SET title = ?, content = ?, position = ? WHERE id = ?");
        $stmtUpdate->execute([$title, $content, $position, $id]);
        header("Location: widgets.php");
        exit;
    }
    ?>
    <div class="card mb-3" aria-labelledby="edit-widget-heading">
        <h6 class="card-header" id="edit-widget-heading"><i class="fas fa-puzzle-piece me-2"></i>Edit Widget</h6>
        <div class="card-body">
            <form action="" method="post" name="post_form" aria-label="Edit Widget Form">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Widget Title</label>
                            <input name="title" id="title" type="text" class="form-control" maxlength="200"
                                value="<?= htmlspecialchars($row['title']) ?>" required>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Characters: <span id="title-characters">0</span> / 200
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="summernote" class="form-label">Widget Content</label>
                            <textarea name="content" id="summernote"
                                required><?= html_entity_decode($row['content']) ?></textarea>
                            <div class="form-text">
                                <i class="fas fa-text-width me-1"></i>
                                Characters: <span id="content-characters">0</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h6><i class="fas fa-cog me-2"></i>Widget Settings</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="position" class="form-label">Display Position</label>
                                    <select class="form-select" name="position" id="position" required
                                        aria-label="Widget Position">
                                        <option value="Sidebar" <?= $row['position'] == "Sidebar" ? 'selected' : '' ?>>
                                            <i class="fas fa-columns"></i> Sidebar
                                        </option>
                                        <option value="Header" <?= $row['position'] == "Header" ? 'selected' : '' ?>>
                                            <i class="fas fa-arrow-up"></i> Header
                                        </option>
                                        <option value="Footer" <?= $row['position'] == "Footer" ? 'selected' : '' ?>>
                                            <i class="fas fa-arrow-down"></i> Footer
                                        </option>
                                    </select>
                                    <div class="form-text">Choose where this widget should appear on the blog</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 pt-3 border-top">
                    <a href="widgets.php" class="btn btn-outline-secondary">
                        <a href="widgets.php" class="btn btn-outline-secondary"
                            aria-label="Cancel and return to widgets list"><i class="fa fa-arrow-left"
                                aria-hidden="true"></i> Cancel</a>
                    </a>
                    <button type="submit" class="btn btn-success" name="submit">
                        <i class="fas fa-save me-1"></i>Save Widget
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php else: ?>
    <div class="card">
        <h6 class="card-header"><i class="fas fa-puzzle-piece me-2"></i>Widgets</h6>
        <div class="card-body p-0">
            <div class="table-responsive" role="table" aria-label="Widgets">
                <table class="table table-hover mb-0" role="grid">
                    <thead role="rowgroup">
                        <tr role="row">
                            <th class="text-left" role="columnheader" scope="col">Title</th>
                            <th class="text-center" role="columnheader" scope="col">Position</th>
                            <th class="text-center" role="columnheader" scope="col">Content Preview</th>
                            <th class="text-center" role="columnheader" scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody role="rowgroup">
                        <?php
                        $stmt = $pdo->query("SELECT * FROM blog_widgets ORDER BY position ASC, title ASC");
                        $widgets = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($widgets as $row)
                        {
                            // Generate position badge
                            $position_badge = '';
                            switch ($row['position'])
                            {
                                case 'Sidebar':
                                    $position_badge = '<span class="badge bg-primary"><i class="fas fa-columns me-1"></i>Sidebar</span>';
                                    break;
                                case 'Header':
                                    $position_badge = '<span class="badge bg-success"><i class="fas fa-arrow-up me-1"></i>Header</span>';
                                    break;
                                case 'Footer':
                                    $position_badge = '<span class="badge bg-secondary"><i class="fas fa-arrow-down me-1"></i>Footer</span>';
                                    break;
                                default:
                                    $position_badge = '<span class="badge bg-warning">Unknown</span>';
                            }

                            // Create content preview (strip HTML and limit length)
                            $content_preview = strip_tags(html_entity_decode($row['content']));
                            $content_preview = strlen($content_preview) > 50 ? substr($content_preview, 0, 50) . '...' : $content_preview;

                            echo '
                            <tr role="row">
                                <td class="text-left" role="gridcell">' . htmlspecialchars($row['title']) . '</td>
                                <td class="text-center" role="gridcell">' . $position_badge . '</td>
                                <td class="text-center" role="gridcell"><small class="text-muted">' . htmlspecialchars($content_preview) . '</small></td>
                                <td class="actions text-center" role="gridcell">
                                    <div class="table-dropdown">
                                        <button class="actions-btn" aria-haspopup="true" aria-expanded="false" aria-label="Actions for widget ' . htmlspecialchars($row['title']) . '">
                                            <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">
                                                <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/>
                                            </svg>
                                        </button>
                                        <div class="table-dropdown-items" role="menu" aria-label="Widget Actions">
                                            <div role="menuitem">
                                                <a href="?edit=' . $row['id'] . '" class="green" tabindex="-1" aria-label="Edit widget ' . htmlspecialchars($row['title']) . '">
                                                    <i class="fas fa-edit" aria-hidden="true"></i>
                                                    <span>&nbsp;Edit</span>
                                                </a>
                                            </div>
                                            <div role="menuitem">
                                                <a href="?delete=' . $row['id'] . '" class="red" tabindex="-1" onclick="return confirm(\'Are you sure you want to delete this widget?\')" aria-label="Delete widget ' . htmlspecialchars($row['title']) . '">
                                                    <i class="fas fa-trash" aria-hidden="true"></i>
                                                    <span>&nbsp;Delete</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-light">
            <div class="small">
                Total widgets: <?= count($widgets) ?>
            </div>
        </div>
    </div>
<?php endif; ?>
</div>
</div>
<script>
    $(document).ready(function () {
        // Summernote init
        $('#summernote').summernote({ height: 350 });

        // Fix for toolbar
        var noteBar = $('.note-toolbar');
        noteBar.find('[data-toggle]').each(function () {
            $(this).attr('data-bs-toggle', $(this).attr('data-toggle')).removeAttr('data-toggle');
        });

        // Title character count and limit
        function updateTitleCharCount() {
            var title = $('#title').val();
            $('#title-characters').text(title.length);
            if (title.length > 200) {
                $('#title').val(title.substring(0, 200));
                $('#title-characters').text(200);
            }
        }
        $('#title').on('input', updateTitleCharCount);
        updateTitleCharCount();

        // Content character count (strip HTML tags)
        function updateContentCharCount() {
            var text = $('#summernote').summernote('code').replace(/<\/?[^>]+(>|$)/g, "");
            $('#content-characters').text(text.length);
        }
        $('#summernote').on('summernote.keyup summernote.change', updateContentCharCount);
        updateContentCharCount();
    });
</script>
<?= template_admin_footer(); ?>