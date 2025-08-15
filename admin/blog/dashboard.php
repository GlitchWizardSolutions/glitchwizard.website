<?php
/**
 * Blog Dashboard - Admin Control Center
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: dashboard.php
 * LOCATION: /public_html/admin/blog/
 * PURPOSE: Central hub for blog management with statistics, shortcuts, and recent activities
 * 
 * CREATED: 2025-07-04
 * UPDATED: 2025-07-04
 * VERSION: 2.0
 * PRODUCTION: [READY FOR PRODUCTION]
 * 
 * CHANGE LOG:
 * 2025-07-04 - Modernized with professional header, styling, and structure
 * 2025-07-04 - Added content title block and consistent button formatting
 * 2025-07-04 - Enhanced statistics display and navigation
 * 
 * FEATURES:
 * - Quick action shortcuts for common blog tasks
 * - Real-time statistics for all blog content types
 * - Recent activity overview
 * - Professional admin interface
 * 
 * DEPENDENCIES:
 * - header.php (blog includes)
 * - Bootstrap 5 for styling
 * - Font Awesome icons
 * 
 * SECURITY NOTES:
 * - Admin authentication required
 * - PDO prepared statements for database access
 * - XSS protection on output
 */
include "header.php";
?>
<?= template_admin_header('Blog Dashboard', 'blog') ?>

<div class="content-title mb-4">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                <path
                    d="M192 32c0 17.7 14.3 32 32 32c123.7 0 224 100.3 224 224c0 17.7 14.3 32 32 32s32-14.3 32-32C512 128.9 383.1 0 224 0c-17.7 0-32 14.3-32 32zm0 96c0 17.7 14.3 32 32 32c70.7 0 128 57.3 128 128c0 17.7 14.3 32 32 32s32-14.3 32-32c0-106-86-192-192-192c-17.7 0-32 14.3-32 32zM96 144c0-26.5-21.5-48-48-48S0 117.5 0 144V368c0 79.5 64.5 144 144 144s144-64.5 144-144s-64.5-144-144-144H96z" />
            </svg>
        </div>
        <div class="txt">
            <h2>Blog Dashboard</h2>
            <p>Manage your blog content, view statistics, and access quick action tools.</p>
        </div>
    </div>
</div>

<div style="height: 20px;"></div>

<!-- Quick Actions -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <a href="add_post.php" class="btn btn-outline-primary d-flex flex-column align-items-center p-3 h-100">
                    <i class="fas fa-edit fa-2x mb-2"></i>
                    <span>Write Post</span>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="blog_settings.php"
                    class="btn btn-outline-secondary d-flex flex-column align-items-center p-3 h-100">
                    <i class="fas fa-cogs fa-2x mb-2"></i>
                    <span>Settings</span>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="messages.php" class="btn btn-outline-info d-flex flex-column align-items-center p-3 h-100">
                    <i class="fas fa-envelope fa-2x mb-2"></i>
                    <span>Messages</span>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="menu_editor.php" class="btn btn-outline-dark d-flex flex-column align-items-center p-3 h-100">
                    <i class="fas fa-bars fa-2x mb-2"></i>
                    <span>Menu Editor</span>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="add_page.php" class="btn btn-outline-success d-flex flex-column align-items-center p-3 h-100">
                    <i class="fas fa-file-alt fa-2x mb-2"></i>
                    <span>Add Page</span>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="add_image.php" class="btn btn-outline-warning d-flex flex-column align-items-center p-3 h-100">
                    <i class="fas fa-camera-retro fa-2x mb-2"></i>
                    <span>Add Image</span>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="widgets.php" class="btn btn-outline-purple d-flex flex-column align-items-center p-3 h-100">
                    <i class="fas fa-archive fa-2x mb-2"></i>
                    <span>Widgets</span>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="upload_file.php"
                    class="btn btn-outline-danger d-flex flex-column align-items-center p-3 h-100">
                    <i class="fas fa-upload fa-2x mb-2"></i>
                    <span>Upload File</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Statistics and Activity -->
<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Content Statistics</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php
                    // --- PDO version of statistics ---
                    $stats = [
                        ['blog_posts', 'fas fa-edit', 'Posts', 'posts.php', 'primary'],
                        ['blog_categories', 'fas fa-list-ul', 'Categories', 'categories.php', 'secondary'],
                        ['blog_comments', 'fas fa-comments', 'Comments', 'comments.php', 'info'],
                        ['blog_gallery', 'fas fa-images', 'Images', 'gallery.php', 'warning'],
                        ['blog_albums', 'fas fa-photo-video', 'Albums', 'albums.php', 'success'],
                        ['blog_pages', 'fas fa-file-alt', 'Pages', 'pages.php', 'dark'],
                        ['blog_widgets', 'fas fa-archive', 'Widgets', 'widgets.php', 'purple'],
                        ['blog_files', 'fas fa-folder-open', 'Files', 'files.php', 'danger'],
                    ];

                    // Normal tables
                    foreach ($stats as $stat)
                    {
                        [$table, $icon, $label, $link, $color] = $stat;
                        $stmt = $pdo->query("SELECT COUNT(*) FROM `$table`");
                        $total = $stmt->fetchColumn();
                        ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="<?= $icon ?> text-<?= $color ?> me-2"></i>
                                <a href="<?= $link ?>" class="text-decoration-none fw-medium"><?= $label ?></a>
                            </div>
                            <span class="badge bg-<?= $color ?> rounded-pill"><?= number_format($total) ?></span>
                        </div>
                        <?php
                    }
                    // Unread messages
                    $stmt = $pdo->query("SELECT COUNT(*) FROM blog_messages WHERE viewed = 'No'");
                    $total = $stmt->fetchColumn();
                    ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-envelope text-warning me-2"></i>
                            <a href="messages.php" class="text-decoration-none fw-medium">Unread Messages</a>
                        </div>
                        <span class="badge bg-warning rounded-pill"><?= number_format($total) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-comments me-2"></i>Recent Comments</h6>
            </div>
            <div class="card-body">
                <?php
                // --- PDO version of recent comments ---
                $stmt = $pdo->query("SELECT * FROM `blog_comments` ORDER BY `id` DESC LIMIT 4");
                $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($comments) == 0)
                {
                    echo '<div class="alert alert-info mb-0"><i class="fas fa-info-circle me-2"></i>No comments posted yet.</div>';
                } else
                {
                    foreach ($comments as $row)
                    {
                        $stmt2 = $pdo->prepare("SELECT * FROM `blog_posts` WHERE id = ?");
                        $stmt2->execute([$row['post_id']]);
                        $posts = $stmt2->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($posts as $row2)
                        {
                            $author = $row['user_id'];
                            if ($row['guest'] == 'Yes')
                            {
                                $avatar = '../assets/img/avatar.png';
                            } else
                            {
                                $stmtch = $pdo->prepare("SELECT * FROM `accounts` WHERE id = ? LIMIT 1");
                                $stmtch->execute([$author]);
                                if ($rowch = $stmtch->fetch(PDO::FETCH_ASSOC))
                                {
                                    $avatar = '../' . $rowch['avatar'];
                                    $author = $rowch['username'];
                                }
                            }
                            ?>
                            <div class="d-flex mb-3 pb-3 border-bottom">
                                <img src="<?= htmlspecialchars($avatar) ?>" class="rounded-circle me-3" width="50" height="50"
                                    alt="Avatar">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-0">
                                            <a href="comments.php?edit-id=<?= $row['id'] ?>" class="text-decoration-none">
                                                <?= htmlspecialchars($author) ?>
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            <?= date('M d, Y', strtotime($row['date'])) ?>
                                        </small>
                                    </div>
                                    <div class="mb-2">
                                        <?php
                                        if ($row['approved'] == "Yes")
                                        {
                                            echo '<span class="badge bg-success me-2"><i class="fas fa-check"></i> Approved</span>';
                                        } else
                                        {
                                            echo '<span class="badge bg-warning me-2"><i class="fas fa-clock"></i> Pending</span>';
                                        }
                                        if ($row['guest'] == "Yes")
                                        {
                                            echo '<span class="badge bg-info"><i class="fas fa-user"></i> Guest</span>';
                                        }
                                        ?>
                                    </div>
                                    <p class="mb-0 text-muted small"><?= htmlspecialchars(short_text($row['comment'], 100)) ?></p>
                                </div>
                            </div>
                            <?php
                        }
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?= template_admin_footer() ?>