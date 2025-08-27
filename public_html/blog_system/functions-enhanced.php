<?php
/**
 * Blog System Style Fixes - Replace Inline Styles with CSS Classes
 * 
 * PURPOSE: Replace hardcoded inline styles with CSS classes that use brand variables
 * INTEGRATES: With the new CSS framework and brand color system
 * FIXES: Accessibility issues and hardcoded colors
 * 
 * VERSION: 1.0
 * CREATED: 2025-08-17
 */

/**
 * Enhanced blog functions with proper CSS classes instead of inline styles
 */

// Replace the problematic navigation function
if (!function_exists('blog_navigation_enhanced')) {
    function blog_navigation_enhanced() {
        global $pdo, $logged, $settings;
        
        // Get navigation items from database
        $nav_items = [];
        $stmt = $pdo->query("SELECT * FROM blog_pages WHERE nav_show = 'Yes' ORDER BY nav_order ASC");
        $nav_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // User information for logged-in users
        $avatar_src = 'accounts_system/assets/uploads/avatars/default-guest.svg';
        $avatar_alt = 'User Avatar';
        $username = 'Guest';
        
        if ($logged === 'Yes' && isset($_SESSION['id'])) {
            $stmt = $pdo->prepare('SELECT username, avatar FROM accounts WHERE id = ? LIMIT 1');
            $stmt->execute([$_SESSION['id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $username = htmlspecialchars($user['username']);
                if (!empty($user['avatar'])) {
                    $avatar_src = 'accounts_system/assets/uploads/avatars/' . $user['avatar'];
                }
                $avatar_alt = $username . ' Avatar';
            }
        }
        
        ?>
        <!-- Enhanced Blog Navigation with Accessibility Fixes -->
        <nav class="navbar navbar-expand-lg blog-navbar mb-4">
            <div class="container-fluid">
                <!-- Brand/Logo -->
                <a class="navbar-brand navbar-brand-styled" href="index.php">
                    <?= htmlspecialchars($settings['blog_title'] ?? 'Blog') ?>
                </a>
                
                <!-- Mobile toggle -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#blogNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <!-- Navigation links -->
                <div class="collapse navbar-collapse" id="blogNavbar">
                    <ul class="navbar-nav me-auto">
                        <!-- Blog link -->
                        <li class="nav-item">
                            <a href="blog.php" class="nav-link">
                                <i class="fas fa-home me-1"></i> Blog
                            </a>
                        </li>
                        
                        <!-- Gallery link -->
                        <li class="nav-item">
                            <a href="gallery.php" class="nav-link">
                                <i class="fas fa-images me-1"></i> Gallery
                            </a>
                        </li>
                        
                        <!-- Dynamic pages -->
                        <?php foreach ($nav_items as $item): ?>
                        <li class="nav-item">
                            <a href="<?= htmlspecialchars($item['path']) ?>.php" class="nav-link">
                                <?= htmlspecialchars($item['title']) ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <!-- Right side navigation -->
                    <ul class="navbar-nav">
                        <?php if ($logged === 'Yes'): ?>
                        <!-- Logged in user -->
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" 
                               data-bs-toggle="dropdown">
                                <img src="<?= htmlspecialchars($avatar_src) ?>" 
                                     alt="<?= htmlspecialchars($avatar_alt) ?>" 
                                     class="blog-author-avatar rounded-circle me-2" 
                                     width="28" height="28">
                                <?= htmlspecialchars($username) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="profile.php">
                                    <i class="fas fa-user me-2"></i> Profile
                                </a></li>
                                <li><a class="dropdown-item" href="my-comments.php">
                                    <i class="fas fa-comments me-2"></i> My Comments
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a></li>
                            </ul>
                        </li>
                        <?php else: ?>
                        <!-- Not logged in -->
                        <li class="nav-item">
                            <a class="nav-link" href="auth.php">
                                <i class="fas fa-sign-in-alt me-1"></i> Login
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <!-- Search form -->
                        <li class="nav-item">
                            <form class="d-flex blog-search-form ms-2" action="search.php" method="GET">
                                <input class="form-control form-control-sm" type="search" 
                                       name="q" placeholder="Search..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                                <button class="btn btn-sm" type="submit" title="Search">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <?php
    }
}

// Enhanced sidebar with proper CSS classes
if (!function_exists('blog_sidebar_enhanced')) {
    function blog_sidebar_enhanced() {
        global $pdo, $settings;
        
        if (!isset($pdo) || !$pdo) {
            echo '<div class="alert alert-danger">Database connection not available.</div>';
            return;
        }
        ?>
        <div class="blog-sidebar">
            <!-- Categories Card -->
            <div class="card card-branded">
                <div class="card-header card-header-branded">
                    <i class="fas fa-list me-2"></i> Categories
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <?php
                        $stmt = $pdo->query("SELECT * FROM blog_categories ORDER BY category ASC");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $category_id = $row['id'];
                            $postc_stmt = $pdo->prepare("SELECT COUNT(id) FROM blog_posts WHERE category_id = ? AND active = 'Yes'");
                            $postc_stmt->execute([$category_id]);
                            $posts_count = $postc_stmt->fetchColumn();
                            ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="category.php?name=<?= urlencode($row['slug']) ?>" 
                                   class="text-decoration-none text-brand-primary">
                                    <?= htmlspecialchars($row['category']) ?>
                                </a>
                                <span class="badge blog-category-badge rounded-pill"><?= $posts_count ?></span>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            </div>

            <!-- Recent Content Card -->
            <div class="card card-branded mt-3">
                <div class="card-header card-header-branded">
                    <ul class="nav nav-tabs card-header-tabs nav-justified">
                        <li class="nav-item">
                            <a class="nav-link active text-white" href="#recentComments" data-bs-toggle="tab">
                                Recent Comments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#latestPosts" data-bs-toggle="tab">
                                Latest Posts
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Recent Comments -->
                        <div id="recentComments" class="tab-pane fade show active">
                            <?php blog_render_recent_comments(); ?>
                        </div>
                        
                        <!-- Latest Posts -->
                        <div id="latestPosts" class="tab-pane fade">
                            <?php blog_render_latest_posts(); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tags Card -->
            <div class="card card-branded mt-3">
                <div class="card-header card-header-branded">
                    <i class="fas fa-tags me-2"></i> Tags
                </div>
                <div class="card-body">
                    <?php
                    $stmt = $pdo->query("SELECT * FROM blog_tags ORDER BY tag ASC");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                        <a href="search.php?q=<?= urlencode($row['tag']) ?>" 
                           class="badge blog-tag-badge text-decoration-none me-1 mb-1">
                            <?= htmlspecialchars($row['tag']) ?>
                        </a>
                        <?php
                    }
                    ?>
                </div>
            </div>

            <!-- Newsletter Subscription -->
            <div class="card card-branded mt-3">
                <div class="card-header card-header-branded">
                    <i class="fas fa-envelope-open-text me-2"></i> Subscribe
                </div>
                <div class="card-body blog-newsletter-box">
                    <h6 class="mb-3">Get the latest news and exclusive offers</h6>
                    <form action="" method="POST">
                        <div class="input-group">
                            <input type="email" class="form-control form-control-brand" 
                                   placeholder="E-Mail Address" name="email" required>
                            <button class="btn blog-subscribe-btn" type="submit" name="subscribe">
                                Subscribe
                            </button>
                        </div>
                    </form>
                    <?php blog_handle_newsletter_subscription(); ?>
                </div>
            </div>
        </div>
        <?php
    }
}

// Helper function for recent comments
if (!function_exists('blog_render_recent_comments')) {
    function blog_render_recent_comments() {
        global $pdo, $settings;
        
        $stmt = $pdo->query("SELECT * FROM blog_comments WHERE approved='Yes' ORDER BY date DESC, id DESC LIMIT 4");
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!$comments) {
            echo '<p class="text-muted">No comments yet</p>';
            return;
        }
        
        foreach ($comments as $comment) {
            // Get user info
            $author = htmlspecialchars($comment['username']);
            $avatar = 'accounts_system/assets/uploads/avatars/default-guest.svg';
            $badge = '';
            
            if ($comment['guest'] !== 'Yes' && !empty($comment['account_id'])) {
                $stmt2 = $pdo->prepare('SELECT username, avatar FROM accounts WHERE id = ? LIMIT 1');
                $stmt2->execute([$comment['account_id']]);
                $user = $stmt2->fetch(PDO::FETCH_ASSOC);
                if ($user) {
                    $author = htmlspecialchars($user['username']);
                    if (!empty($user['avatar'])) {
                        $avatar = 'accounts_system/assets/uploads/avatars/' . $user['avatar'];
                    }
                }
            } else {
                $badge = '<span class="badge bg-secondary ms-1">Guest</span>';
            }
            
            // Get post info
            $stmt3 = $pdo->prepare('SELECT slug, title FROM blog_posts WHERE id = ? AND active = "Yes" LIMIT 1');
            $stmt3->execute([$comment['post_id']]);
            $post = $stmt3->fetch(PDO::FETCH_ASSOC);
            
            if ($post) {
                ?>
                <div class="blog-comment-item">
                    <div class="d-flex align-items-start">
                        <img src="<?= htmlspecialchars($avatar) ?>" 
                             alt="<?= htmlspecialchars($author) ?>" 
                             class="blog-author-avatar rounded-circle me-2" 
                             width="40" height="40">
                        <div>
                            <h6 class="blog-comment-author mb-1">
                                <a href="post.php?name=<?= urlencode($post['slug']) ?>#comments" 
                                   class="text-decoration-none">
                                    <?= $author ?><?= $badge ?>
                                </a>
                            </h6>
                            <p class="blog-comment-date mb-1">
                                on <a href="post.php?name=<?= urlencode($post['slug']) ?>#comments" 
                                      class="text-decoration-none">
                                    <?= htmlspecialchars($post['title']) ?>
                                </a>
                            </p>
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                <?= date($settings['date_format'] . ' g:i a', strtotime($comment['date'])) ?>
                            </small>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
    }
}

// Helper function for latest posts
if (!function_exists('blog_render_latest_posts')) {
    function blog_render_latest_posts() {
        global $pdo, $settings;
        
        $stmt = $pdo->query("SELECT * FROM blog_posts WHERE active='Yes' ORDER BY id DESC LIMIT 4");
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!$posts) {
            echo '<div class="alert alert-info">No published posts</div>';
            return;
        }
        
        foreach ($posts as $post) {
            // Handle image
            $image_html = '';
            if (!empty($post['image'])) {
                $img_path = $post['image'];
                if (!preg_match('/^(https?:\/\/|\/)/', $img_path)) {
                    $img_path = 'admin/blog/blog_post_images/' . ltrim($img_path, '/');
                }
                $image_html = '<img class="rounded me-2" src="' . htmlspecialchars($img_path) . '" 
                                   alt="' . htmlspecialchars($post['title']) . '" width="60" height="60" 
                                   style="object-fit: cover;">';
            } else {
                $image_html = '<div class="bg-secondary rounded me-2 d-flex align-items-center justify-content-center" 
                                   style="width: 60px; height: 60px; color: white;">
                                   <i class="fas fa-image"></i>
                               </div>';
            }
            
            // Get author info
            $author = 'Unknown';
            if (!empty($post['author_id'])) {
                $stmt_author = $pdo->prepare('SELECT username FROM accounts WHERE id = ? LIMIT 1');
                $stmt_author->execute([$post['author_id']]);
                $author_row = $stmt_author->fetch(PDO::FETCH_ASSOC);
                if ($author_row) {
                    $author = htmlspecialchars($author_row['username']);
                }
            }
            
            ?>
            <div class="blog-comment-item">
                <div class="d-flex align-items-start">
                    <?= $image_html ?>
                    <div>
                        <h6 class="mb-1">
                            <a href="post.php?name=<?= urlencode($post['slug']) ?>" 
                               class="text-decoration-none text-brand-primary">
                                <?= htmlspecialchars($post['title']) ?>
                            </a>
                        </h6>
                        <div class="blog-post-meta">
                            <small>
                                <i class="fas fa-calendar me-1"></i>
                                <?= date($settings['date_format'], strtotime($post['date'])) ?>
                            </small><br>
                            <small>
                                <i class="fas fa-user me-1"></i>
                                <?= $author ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    }
}

// Newsletter subscription handler
if (!function_exists('blog_handle_newsletter_subscription')) {
    function blog_handle_newsletter_subscription() {
        global $pdo;
        
        if (isset($_POST['subscribe'])) {
            $email = $_POST['email'] ?? '';
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo '<div class="alert alert-danger mt-2">Invalid email address</div>';
                return;
            }
            
            try {
                $stmt = $pdo->prepare("SELECT COUNT(id) FROM blog_newsletter WHERE email = ?");
                $stmt->execute([$email]);
                
                if ($stmt->fetchColumn() > 0) {
                    echo '<div class="alert alert-warning mt-2">Email already subscribed</div>';
                } else {
                    $stmt = $pdo->prepare("INSERT INTO blog_newsletter (email) VALUES (?)");
                    $stmt->execute([$email]);
                    echo '<div class="alert alert-success mt-2">Successfully subscribed!</div>';
                }
            } catch (PDOException $e) {
                echo '<div class="alert alert-danger mt-2">Subscription failed</div>';
            }
        }
    }
}

// Enhanced filter sidebar for gallery
if (!function_exists('gallery_sidebar_enhanced')) {
    function gallery_sidebar_enhanced() {
        global $pdo;
        
        ?>
        <div class="card card-branded">
            <div class="card-header card-header-branded">
                <i class="fas fa-filter me-2"></i> Filters
            </div>
            <div class="card-body">
                <form method="GET" action="gallery.php" class="blog-filter-form">
                    <div class="row">
                        <!-- Categories -->
                        <div class="col-6">
                            <h6 class="mb-2"><i class="fas fa-images me-1"></i> Categories</h6>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM blog_gallery_categories ORDER BY name ASC");
                            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            foreach ($categories as $cat) {
                                $checked = isset($_GET['categories']) && in_array($cat['slug'], (array)$_GET['categories']) ? 'checked' : '';
                                ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input blog-filter-checkbox" type="checkbox" 
                                           name="categories[]" value="<?= htmlspecialchars($cat['slug']) ?>" 
                                           id="cat_<?= $cat['id'] ?>" <?= $checked ?>>
                                    <label class="form-check-label" for="cat_<?= $cat['id'] ?>">
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </label>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        
                        <!-- Tags -->
                        <div class="col-6">
                            <h6 class="mb-2"><i class="fas fa-tags me-1"></i> Tags</h6>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM blog_gallery_tags ORDER BY name ASC");
                            $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            foreach ($tags as $tag) {
                                $checked = isset($_GET['tags']) && in_array($tag['slug'], (array)$_GET['tags']) ? 'checked' : '';
                                ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input blog-filter-checkbox" type="checkbox" 
                                           name="tags[]" value="<?= htmlspecialchars($tag['slug']) ?>" 
                                           id="tag_<?= $tag['id'] ?>" <?= $checked ?>>
                                    <label class="form-check-label" for="tag_<?= $tag['id'] ?>">
                                        <?= htmlspecialchars($tag['name']) ?>
                                    </label>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    
                    <div class="d-grid mt-3">
                        <button type="submit" class="btn blog-filter-apply-btn">
                            <i class="fa fa-filter me-1"></i> Apply Filters
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php
    }
}
?>
