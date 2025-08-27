<?php
include "header.php";

// Blog-specific dashboard metrics with enhanced insights
$date = date('Y-m-d H:i:s');

// Action Items - Things that need attention
$unread_messages_count = $pdo->query("SELECT COUNT(*) FROM blog_messages WHERE viewed = 'No'")->fetchColumn();
$pending_comments_count = $pdo->query("SELECT COUNT(*) FROM blog_comments WHERE approved = 'No'")->fetchColumn();
$draft_posts_count = $pdo->query("SELECT COUNT(*) FROM blog_posts WHERE active = 'No'")->fetchColumn();
$unpublished_pages_count = $pdo->query("SELECT COUNT(*) FROM blog_pages WHERE active = 'No'")->fetchColumn();

// Content Overview
$posts_count = $pdo->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn();
$published_posts_count = $pdo->query("SELECT COUNT(*) FROM blog_posts WHERE active = 'Yes'")->fetchColumn();
$pages_count = $pdo->query("SELECT COUNT(*) FROM blog_pages")->fetchColumn();
$categories_count = $pdo->query("SELECT COUNT(*) FROM blog_categories")->fetchColumn();

// Engagement Metrics
$total_comments_count = $pdo->query("SELECT COUNT(*) FROM blog_comments")->fetchColumn();
$approved_comments_count = $pdo->query("SELECT COUNT(*) FROM blog_comments WHERE approved = 'Yes'")->fetchColumn();
$recent_comments_count = $pdo->query("SELECT COUNT(*) FROM blog_comments WHERE date >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();

// Media & Assets
$images_count = $pdo->query("SELECT COUNT(*) FROM blog_gallery")->fetchColumn();
$albums_count = $pdo->query("SELECT COUNT(*) FROM blog_albums")->fetchColumn();
$documents_count = $pdo->query("SELECT COUNT(*) FROM blog_files")->fetchColumn();
$widgets_count = $pdo->query("SELECT COUNT(*) FROM blog_widgets")->fetchColumn();

// Activity Metrics
$recent_posts_count = $pdo->query("SELECT COUNT(*) FROM blog_posts WHERE date >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn();
$most_commented_post = $pdo->query("
    SELECT p.title, COUNT(c.id) as comment_count 
    FROM blog_posts p 
    LEFT JOIN blog_comments c ON p.id = c.post_id 
    WHERE c.approved = 'Yes'
    GROUP BY p.id 
    ORDER BY comment_count DESC 
    LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

// Get most recent comments for activity feed
$recent_comments = $pdo->query("
    SELECT c.*, p.title as post_title 
    FROM blog_comments c 
    LEFT JOIN blog_posts p ON c.post_id = p.id 
    ORDER BY c.date DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Calculate action items total
$total_action_items = $unread_messages_count + $pending_comments_count + $draft_posts_count + $unpublished_pages_count;
?>
<?= template_admin_header('Blog Dashboard', 'blog') ?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-4 px-4 branding-settings-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-white">
                    <span class="header-icon"><i class="bi bi-file-earmark-text" aria-hidden="true"></i></span>
                    Blog Dashboard
                </h6>
                <span class="text-white" style="font-size: 0.875rem;">System Management</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="container-fluid py-3 px-4">

<div style="height: 20px;"></div>
<!--BLOG DASHBOARD OVERVIEW-->
<!-- Blog Dashboard Cards Grid -->
<div class="dashboard-apps">
    <!-- Blog Quick Actions Card -->
    <div class="app-card" role="region" aria-labelledby="blog-quick-actions-title">
        <div class="app-header events-header" role="banner" aria-labelledby="blog-quick-actions-title">
            <h3 id="blog-quick-actions-title">Quick Actions</h3>
            <i class="bi bi-lightning" aria-hidden="true"></i>
            <span class="badge" aria-label="Blog management actions">Manage</span>
        </div>
        <div class="app-body">
            <div class="quick-actions">
                <a href="add_post.php" class="quick-action primary">
                    <div class="action-icon">
                        <i class="bi bi-plus-circle" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Create Post</h4>
                        <small class="text-muted">Write new blog post</small>
                    </div>
                </a>
                <a href="posts.php" class="quick-action info">
                    <div class="action-icon">
                        <i class="bi bi-list-ul" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Manage Posts</h4>
                        <small class="text-muted">Edit existing posts</small>
                    </div>
                </a>
                <a href="upload_file.php" class="quick-action warning">
                    <div class="action-icon">
                        <i class="bi bi-cloud-upload" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Upload Files</h4>
                        <small class="text-muted">Upload media and documents</small>
                    </div>
                </a>
                <a href="menu_editor.php" class="quick-action secondary">
                    <div class="action-icon">
                        <i class="bi bi-list" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Menu Editor</h4>
                        <small class="text-muted">Manage navigation menus</small>
                    </div>
                </a>
                <a href="categories.php" class="quick-action secondary">
                    <div class="action-icon">
                        <i class="bi bi-tags" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Categories</h4>
                        <small class="text-muted">Organize content topics</small>
                    </div>
                </a>
                <a href="comments.php" class="quick-action success">
                    <div class="action-icon">
                        <i class="bi bi-chat-dots" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Moderate Comments</h4>
                        <small class="text-muted">Review user comments</small>
                    </div>
                </a>
                <!-- Blog Configuration Quick Access -->
                <a href="../settings/blog_identity_form.php" class="quick-action purple">
                    <div class="action-icon">
                        <i class="bi bi-gear" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Blog Configuration</h4>
                        <small class="text-muted">Settings & preferences</small>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Action Items Card -->
    <div class="app-card" role="region" aria-labelledby="actions-title">
        <div class="app-header blog-header" role="banner" aria-labelledby="actions-title">
            <h3 id="actions-title">Action Items</h3>
            <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
            <span class="badge" aria-label="<?= $total_action_items ?> items requiring attention"><?= $total_action_items ?> items</span>
        </div>
        <div class="app-body">
            <?php if ($total_action_items > 0): ?>
                <div class="action-items">
                    <?php if ($pending_comments_count > 0): ?>
                        <a href="comments.php?approved=No" class="action-item warning">
                            <div class="action-icon">
                                <i class="bi bi-chat-dots" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Pending Comments</h4>
                                <small class="text-muted">Need moderation</small>
                            </div>
                            <div class="action-count"><?= $pending_comments_count ?></div>
                        </a>
                    <?php endif; ?>

                    <?php if ($draft_posts_count > 0): ?>
                        <a href="posts.php?active=No" class="action-item info">
                            <div class="action-icon">
                                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Draft Posts</h4>
                                <small class="text-muted">Ready to publish</small>
                            </div>
                            <div class="action-count"><?= $draft_posts_count ?></div>
                        </a>
                    <?php endif; ?>

                    <?php if ($unread_messages_count > 0): ?>
                        <a href="messages.php?viewed=No" class="action-item danger">
                            <div class="action-icon">
                                <i class="bi bi-envelope" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Unread Messages</h4>
                                <small class="text-muted">Contact form submissions</small>
                            </div>
                            <div class="action-count"><?= $unread_messages_count ?></div>
                        </a>
                    <?php endif; ?>

                    <?php if ($unpublished_pages_count > 0): ?>
                        <a href="pages.php?active=No" class="action-item warning">
                            <div class="action-icon">
                                <i class="bi bi-file-text" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Draft Pages</h4>
                                <small class="text-muted">Need publishing</small>
                            </div>
                            <div class="action-count"><?= $unpublished_pages_count ?></div>
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="no-action-items">
                    <i class="bi bi-check-circle" aria-hidden="true"></i>
                    <p>All caught up! No pending actions.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Blog Statistics Card -->
    <div class="app-card" role="region" aria-labelledby="content-title">
        <div class="app-header accounts-header" role="banner" aria-labelledby="content-title">
            <h3 id="content-title">Blog Statistics</h3>
            <i class="bi bi-bar-chart" aria-hidden="true"></i>
            <span class="badge" aria-label="<?= $posts_count ?> total posts"><?= $posts_count ?> posts</span>
        </div>
        <div class="app-body">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($published_posts_count) ?></div>
                    <div class="stat-label">Published Posts</div>
                    <div class="stat-progress">
                        <div class="progress-bar" style="width: <?= $posts_count > 0 ? round(($published_posts_count / $posts_count) * 100) : 0 ?>%"></div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($pages_count) ?></div>
                    <div class="stat-label">Pages</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($categories_count) ?></div>
                    <div class="stat-label">Categories</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($recent_posts_count) ?></div>
                    <div class="stat-label">Posts This Month</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($approved_comments_count) ?></div>
                    <div class="stat-label">Approved Comments</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($recent_comments_count) ?></div>
                    <div class="stat-label">Comments This Week</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($images_count) ?></div>
                    <div class="stat-label">Images</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($documents_count) ?></div>
                    <div class="stat-label">Documents</div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mt-3">
    <div class="col-md-12 column">
        <div class="card">
            <h6 class="card-header">3 Most Recent Comments</h6>
            <?php
            // Sorting logic for comments table
            $comment_table_icons = [
                'asc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9650;</span>',
                'desc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9660;</span>'
            ];
            $comment_order = isset($_GET['comment_order']) && $_GET['comment_order'] == 'DESC' ? 'DESC' : 'ASC';
            $comment_order_by_whitelist = ['author', 'date', 'status', 'post'];
            $comment_order_by = isset($_GET['comment_order_by']) && in_array($_GET['comment_order_by'], $comment_order_by_whitelist) ? $_GET['comment_order_by'] : 'date';
            // Map to DB columns
            $comment_order_by_map = [
                'author' => 'username',
                'date' => 'date',
                'status' => 'approved',
                'post' => 'post_id'
            ];
            $comment_order_by_sql = $comment_order_by_map[$comment_order_by];
            // Build base URL for sorting links
            $base_url = strtok($_SERVER['REQUEST_URI'], '?');
            $query_params = $_GET;
            ?>
            <div class="table">
                <table>
                    <thead>
                        <tr>
                            <th style="text-align: center;">Avatar</th>
                            <th class="text-left" style="text-align: left;">
                                <?php $query_params['comment_order_by'] = 'author'; $query_params['comment_order'] = ($comment_order_by == 'author' && $comment_order == 'ASC') ? 'DESC' : 'ASC'; ?>
                                <a href="<?= $base_url . '?' . http_build_query($query_params) ?>" class="sort-header">Author<?= $comment_order_by == 'author' ? $comment_table_icons[strtolower($comment_order)] : '' ?></a>
                            </th>
                            <th style="text-align: center;">
                                <?php $query_params['comment_order_by'] = 'date'; $query_params['comment_order'] = ($comment_order_by == 'date' && $comment_order == 'ASC') ? 'DESC' : 'ASC'; ?>
                                <a href="<?= $base_url . '?' . http_build_query($query_params) ?>" class="sort-header">Date<?= $comment_order_by == 'date' ? $comment_table_icons[strtolower($comment_order)] : '' ?></a>
                            </th>
                            <th style="text-align: center;">
                                <?php $query_params['comment_order_by'] = 'status'; $query_params['comment_order'] = ($comment_order_by == 'status' && $comment_order == 'ASC') ? 'DESC' : 'ASC'; ?>
                                <a href="<?= $base_url . '?' . http_build_query($query_params) ?>" class="sort-header">Status<?= $comment_order_by == 'status' ? $comment_table_icons[strtolower($comment_order)] : '' ?></a>
                            </th>
                            <th style="text-align: center;">
                                <?php $query_params['comment_order_by'] = 'post'; $query_params['comment_order'] = ($comment_order_by == 'post' && $comment_order == 'ASC') ? 'DESC' : 'ASC'; ?>
                                <a href="<?= $base_url . '?' . http_build_query($query_params) ?>" class="sort-header">Post<?= $comment_order_by == 'post' ? $comment_table_icons[strtolower($comment_order)] : '' ?></a>
                            </th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Get the 3 most recent comments, sorted
                        $stmt = $pdo->prepare("SELECT * FROM blog_comments ORDER BY $comment_order_by_sql $comment_order, id DESC LIMIT 3");
                        $stmt->execute();
                        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($comments as $row)
                        {
                            $badge = '';
                            if ($row['guest'] == 'Yes')
                            {
                                $author = $row['username'];
                                $avatar = "avatar.png";
                                $badge = ' <span class="badge bg-info"><i class="bi bi-person"></i> Guest</span>';
                            } else
                            {
                                $author = '';
                                $avatar = "avatar.png";
                                if (!empty($row['account_id']))
                                {
                                    $stmtAcc = $pdo->prepare("SELECT username, avatar FROM accounts WHERE id = ? LIMIT 1");
                                    $stmtAcc->execute([$row['account_id']]);
                                    if ($acc = $stmtAcc->fetch(PDO::FETCH_ASSOC))
                                    {
                                        $author = $acc['username'];
                                        if (!empty($acc['avatar']))
                                            $avatar = $acc['avatar'];
                                    }
                                }
                                if (empty($author) && !empty($row['user_id']))
                                {
                                    $stmtUser = $pdo->prepare("SELECT username, avatar FROM accounts WHERE id = ? LIMIT 1");
                                    $stmtUser->execute([$row['user_id']]);
                                    if ($usr = $stmtUser->fetch(PDO::FETCH_ASSOC))
                                    {
                                        $author = $usr['username'];
                                        if (!empty($usr['avatar']))
                                            $avatar = $usr['avatar'];
                                    }
                                }
                                if (empty($author))
                                {
                                    $author = "Unknown";
                                }
                            }
                            echo '
                                <tr>
                                    <td style="text-align: center;">
                                        <img src="' . getUserAvatar(array('avatar' => $avatar, 'role' => ($row['guest'] == 'Yes' ? 'Guest' : 'Member'))) . '" class="avatar-img" alt="' . htmlspecialchars($author) . ' avatar" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #dee2e6;">
                                    </td>
                                    <td class="text-left">' . htmlspecialchars($author) . $badge . '</td>
                                    <td style="text-align: center;" data-sort="' . strtotime($row['date']) . '">' . date('m-d-Y', strtotime($row['date'])) . ', ' . strtolower(date('h:i a', strtotime($row['time']))) . '</td>
                                    <td style="text-align: center;">';
                            if ($row['approved'] == "Yes")
                            {
                                echo '<span class="green">Approved</span>';
                            } else
                            {
                                echo '<span class="grey">Not Shown</span>';
                            }
                            $post_id = $row['post_id'];
                            $stmt2 = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
                            $stmt2->execute([$post_id]);
                            $sql2 = $stmt2->fetch(PDO::FETCH_ASSOC);
                            echo '<td class="text-center">' . htmlspecialchars($sql2['title']) . '</td>
                                    <td class="actions text-center">
                                        <div class="table-dropdown">
                                            <button class="actions-btn" aria-haspopup="true" aria-expanded="false" aria-label="Actions for ' . htmlspecialchars($author) . '">
                                                <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                    <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/>
                                                </svg>
                                            </button>
                                            <div class="table-dropdown-items" role="menu" aria-label="Comment Actions">
                                                <div role="menuitem">
                                                    <a href="comments.php?edit-id=' . $row['id'] . '" class="green" tabindex="-1" aria-label="Edit comment by ' . htmlspecialchars($author) . '">
                                                        <span class="icon" aria-hidden="true">
                                                            <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                                <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z"/>
                                                            </svg>
                                                        </span>
                                                        <span>Edit</span>
                                                    </a>
                                                </div>';
                                if ($row['approved'] == 'No')
                                {
                                    echo '<div role="menuitem">
                                            <a class="green" 
                                               href="comments.php?approve-id=' . $row['id'] . '" 
                                               onclick="return confirm(\'Are you sure you want to approve this comment?\')"
                                               tabindex="-1"
                                               aria-label="Approve comment by ' . htmlspecialchars($author) . '">
                                                <i class="bi bi-check" aria-hidden="true"></i>
                                                <span>&nbsp;Approve</span>
                                            </a>
                                          </div>';
                                } else
                                {
                                    echo '<div role="menuitem">
                                            <a class="black" 
                                               href="comments.php?reject=' . $row['id'] . '" 
                                               onclick="return confirm(\'Are you sure you want to reject this comment?\')"
                                               tabindex="-1"
                                               aria-label="Reject comment by ' . htmlspecialchars($author) . '">
                                                <i class="bi bi-x" aria-hidden="true"></i>
                                                </span>
                                                <span>&nbsp;Reject</span>
                                            </a>
                                          </div>';
                                }
                                echo '<div role="menuitem">
                                        <a class="red" 
                                           href="comments.php?delete-id=' . $row['id'] . '" 
                                           onclick="return confirm(\'Are you sure you want to delete this comment?\')"
                                           tabindex="-1"
                                           aria-label="Delete comment by ' . htmlspecialchars($author) . '">
                                            <span class="icon" aria-hidden="true">
                                                <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                    <path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/>
                                                </svg>
                                            </span>
                                            <span>Delete</span>
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
                <div class="card-footer bg-light">
                    <!-- Recent comments summary -->
                    <div class="small">
                        <span>Showing 3 recent comments</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
            </div>
        </div>
    </div>
</div>
<?= template_admin_footer() ?>