<?php
/**
 * Enhanced Blog Page with Configuration Integration
 * 
 * SYSTEM: GWS Universal Hybrid App - Blog Display
 * FILE: blog_enhanced_example.php
 * LOCATION: /public_html/
 * PURPOSE: Demonstration of blog configuration integration
 * 
 * This file demonstrates how the new blog configuration system integrates
 * with existing blog display pages. It shows how settings from the admin
 * configuration forms control the display and behavior of blog content.
 * 
 * INTEGRATION FEATURES:
 * - Dynamic posts per page from display settings
 * - Layout options (Wide, Boxed, Sidebar)
 * - Show/hide elements based on feature settings
 * - SEO meta tags generation
 * - Social sharing buttons
 * - Responsive design based on configuration
 * 
 * CREATED: 2025-08-17
 * VERSION: 1.0 - Integration Example
 */

// Include necessary files
include_once "assets/includes/doctype.php";

// Load blog configuration integration
include_once "assets/includes/blog_config_reader.php";

// Get blog configuration settings
$blogConfig = getBlogConfig();
$display = $blogConfig['display'];
$features = $blogConfig['features'];
$identity = $blogConfig['identity'];
$seo = $blogConfig['seo'];

// Set dynamic variables based on configuration
$postsPerPage = $display['posts_per_page'] ?? 10;
$layout = $display['layout'] ?? 'Wide';
$showSidebar = $display['sidebar_position'] !== 'None';
$sidebarPosition = $display['sidebar_position'] ?? 'Right';

include_once "assets/includes/header.php";
?>

<!-- Dynamic SEO Meta Tags from Configuration -->
<?php if (isBlogFeatureEnabled('sitemap') && $seo['enable_meta_tags']): ?>
<?= generateBlogMetaTags() ?>
<?php endif; ?>

<!-- Dynamic Blog Layout Based on Configuration -->
<div class="container<?= $layout === 'Wide' ? '-fluid' : '' ?>">
    <div class="row">
        
        <!-- Sidebar Left (if enabled) -->
        <?php if ($showSidebar && $sidebarPosition === 'Left'): ?>
        <div class="col-md-3 mb-3">
            <?php include 'assets/includes/blog_sidebar.php'; ?>
        </div>
        <?php endif; ?>
        
        <!-- Main Content Area -->
        <div class="col-md-<?= $showSidebar ? '9' : '12' ?> mb-3">
            
            <!-- Blog Header with Dynamic Identity -->
            <?php if (isBlogFeatureEnabled('posts')): ?>
            <div class="card <?= $layout === 'Boxed' ? 'blog-boxed' : '' ?>">
                <div class="card-header accent-background">
                    <i class="far fa-file-alt"></i> <?= htmlspecialchars($identity['blog_title']) ?>
                    <?php if (!empty($identity['blog_tagline'])): ?>
                    <small class="text-muted"> - <?= htmlspecialchars($identity['blog_tagline']) ?></small>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    
                    <!-- Breadcrumbs (if enabled) -->
                    <?php if (isBlogFeatureEnabled('breadcrumbs')): ?>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Blog</li>
                        </ol>
                    </nav>
                    <?php endif; ?>
                    
                    <?php
                    // Dynamic pagination based on configuration
                    $pageNum = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
                    $rows = ($pageNum - 1) * $postsPerPage;
                    
                    // Include blog loading functionality
                    include_once "assets/includes/blog_load.php";
                    
                    $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE active='Yes' ORDER BY id DESC LIMIT ?, ?");
                    $stmt->bindValue(1, $rows, PDO::PARAM_INT);
                    $stmt->bindValue(2, $postsPerPage, PDO::PARAM_INT);
                    $stmt->execute();
                    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (!$posts) {
                        echo '<div class="alert alert-info">There are no published posts</div>';
                    } else {
                        // Dynamic posts layout based on configuration
                        $postsPerRow = $display['posts_per_row'] ?? 2;
                        $colClass = $postsPerRow == 1 ? 'col-12' : ($postsPerRow == 2 ? 'col-md-6' : 'col-md-4');
                        
                        echo '<div class="row">';
                        
                        foreach ($posts as $row) {
                            echo '<div class="' . $colClass . ' mb-4">';
                            echo '<div class="card h-100">';
                            
                            // Featured Image (if enabled in display settings)
                            if ($display['enable_featured_image'] && !empty($row['image'])) {
                                $img_path = $row['image'];
                                if (!preg_match('/^(https?:\/\/)/', $img_path)) {
                                    $img_path = 'admin/blog/blog_post_images/' . ltrim($img_path, '/');
                                }
                                echo '<img src="' . htmlspecialchars($img_path) . '" class="card-img-top" alt="' . htmlspecialchars($row['title']) . '" style="height: ' . $display['thumbnail_height'] . 'px; object-fit: cover;">';
                            }
                            
                            echo '<div class="card-body d-flex flex-column">';
                            echo '<h5 class="card-title"><a href="post.php?id=' . $row['id'] . '">' . htmlspecialchars($row['title']) . '</a></h5>';
                            
                            // Post meta information based on display settings
                            if ($display['show_date'] || $display['show_author'] || $display['show_categories']) {
                                echo '<div class="post-meta text-muted small mb-2">';
                                
                                if ($display['show_date']) {
                                    $dateFormat = $display['date_format'] ?? 'F j, Y';
                                    echo '<span><i class="far fa-calendar"></i> ' . date($dateFormat, strtotime($row['date'])) . '</span>';
                                }
                                
                                if ($display['show_author'] && isBlogFeatureEnabled('author_bio')) {
                                    echo ' <span><i class="far fa-user"></i> ' . htmlspecialchars($row['author'] ?? $identity['author_name']) . '</span>';
                                }
                                
                                if ($display['show_categories'] && isBlogFeatureEnabled('categories')) {
                                    // Get post categories
                                    $catStmt = $pdo->prepare("SELECT c.name FROM blog_categories c JOIN blog_post_categories pc ON c.id = pc.category_id WHERE pc.post_id = ?");
                                    $catStmt->execute([$row['id']]);
                                    $categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);
                                    if ($categories) {
                                        echo ' <span><i class="far fa-folder"></i> ' . implode(', ', $categories) . '</span>';
                                    }
                                }
                                
                                echo '</div>';
                            }
                            
                            // Post excerpt (if enabled)
                            if ($display['show_excerpt']) {
                                $excerptLength = $display['excerpt_length'] ?? 250;
                                $excerpt = substr(strip_tags($row['content']), 0, $excerptLength);
                                if (strlen($excerpt) >= $excerptLength) {
                                    $excerpt .= '...';
                                }
                                echo '<p class="card-text">' . htmlspecialchars($excerpt) . '</p>';
                            }
                            
                            // Reading time (if enabled)
                            if (isBlogFeatureEnabled('reading_time')) {
                                $wordCount = str_word_count(strip_tags($row['content']));
                                $readingTime = ceil($wordCount / 200); // Average reading speed
                                echo '<div class="text-muted small mb-2"><i class="far fa-clock"></i> ' . $readingTime . ' min read</div>';
                            }
                            
                            // Tags (if enabled)
                            if ($display['show_tags'] && isBlogFeatureEnabled('tags')) {
                                $tagStmt = $pdo->prepare("SELECT t.tag FROM blog_tags t JOIN blog_post_tags pt ON t.id = pt.tag_id WHERE pt.post_id = ?");
                                $tagStmt->execute([$row['id']]);
                                $tags = $tagStmt->fetchAll(PDO::FETCH_COLUMN);
                                if ($tags) {
                                    echo '<div class="mb-2">';
                                    foreach ($tags as $tag) {
                                        echo '<span class="badge badge-secondary me-1">' . htmlspecialchars($tag) . '</span>';
                                    }
                                    echo '</div>';
                                }
                            }
                            
                            echo '<div class="mt-auto">';
                            echo '<a href="post.php?id=' . $row['id'] . '" class="btn btn-primary btn-sm">Read More</a>';
                            
                            // Social sharing buttons (if enabled)
                            if (isBlogFeatureEnabled('social_sharing')) {
                                $postUrl = 'post.php?id=' . $row['id'];
                                echo '<div class="mt-2">' . generateBlogSharingButtons($row, $postUrl) . '</div>';
                            }
                            
                            echo '</div>'; // mt-auto
                            echo '</div>'; // card-body
                            echo '</div>'; // card
                            echo '</div>'; // col
                        }
                        
                        echo '</div>'; // row
                        
                        // Pagination
                        $totalPostsStmt = $pdo->query("SELECT COUNT(*) FROM blog_posts WHERE active='Yes'");
                        $totalPosts = $totalPostsStmt->fetchColumn();
                        $totalPages = ceil($totalPosts / $postsPerPage);
                        
                        if ($totalPages > 1) {
                            echo '<nav aria-label="Blog pagination">';
                            echo '<ul class="pagination justify-content-center">';
                            
                            for ($i = 1; $i <= $totalPages; $i++) {
                                $activeClass = $i == $pageNum ? 'active' : '';
                                echo '<li class="page-item ' . $activeClass . '">';
                                echo '<a class="page-link" href="?page=' . $i . '">' . $i . '</a>';
                                echo '</li>';
                            }
                            
                            echo '</ul>';
                            echo '</nav>';
                        }
                    }
                    ?>
                    
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Newsletter Signup (if enabled) -->
            <?php if (isBlogFeatureEnabled('newsletter_signup')): ?>
            <div class="card mt-4">
                <div class="card-body text-center">
                    <h5>Subscribe to Our Newsletter</h5>
                    <p class="text-muted">Stay updated with our latest posts and news.</p>
                    <form class="d-flex justify-content-center">
                        <input type="email" class="form-control me-2" placeholder="Enter your email" style="max-width: 300px;">
                        <button type="submit" class="btn btn-primary">Subscribe</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
            
        </div>
        
        <!-- Sidebar Right (if enabled) -->
        <?php if ($showSidebar && $sidebarPosition === 'Right'): ?>
        <div class="col-md-3 mb-3">
            <?php include 'assets/includes/blog_sidebar.php'; ?>
        </div>
        <?php endif; ?>
        
    </div>
</div>

<!-- Custom CSS from Display Settings -->
<?php if (!empty($display['custom_css'])): ?>
<style>
<?= $display['custom_css'] ?>
</style>
<?php endif; ?>

<!-- Configuration Debug Panel (for development) -->
<?php if (isset($_GET['debug']) && $_SESSION['admin_role'] === 'Developer'): ?>
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-warning">
            <h5>Configuration Debug Panel</h5>
        </div>
        <div class="card-body">
            <h6>Active Configuration:</h6>
            <pre><?= htmlspecialchars(json_encode($blogConfig, JSON_PRETTY_PRINT)) ?></pre>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include_once "assets/includes/footer.php"; ?>

<style>
/* Dynamic theme styles based on configuration */
.blog-boxed {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

.social-sharing-buttons {
    margin-top: 10px;
}

.social-sharing-buttons a {
    display: inline-block;
    margin-right: 5px;
    padding: 5px 10px;
    background: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    font-size: 12px;
}

.social-sharing-buttons a:hover {
    background: #0056b3;
    color: white;
}

.social-sharing-buttons .facebook { background: #3b5998; }
.social-sharing-buttons .twitter { background: #1da1f2; }
.social-sharing-buttons .linkedin { background: #0077b5; }
.social-sharing-buttons .pinterest { background: #bd081c; }
.social-sharing-buttons .whatsapp { background: #25d366; }

.post-meta span {
    margin-right: 15px;
}

.badge-secondary {
    background-color: #6c757d;
}
</style>
