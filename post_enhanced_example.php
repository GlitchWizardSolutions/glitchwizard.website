<?php
/**
 * Enhanced Blog Post Display with Phase 2 Integration
 * 
 * SYSTEM: GWS Universal Hybrid App - Enhanced Post Display
 * FILE: post_enhanced_example.php
 * LOCATION: /public_html/
 * PURPOSE: Demonstration of Phase 2 SEO and social integration
 * 
 * This file demonstrates the complete Phase 2 integration including:
 * - Advanced SEO automation with meta tags and schema markup
 * - Enhanced social sharing with analytics
 * - Auto-posting capabilities
 * - Breadcrumb navigation with schema
 * - Analytics integration
 * 
 * CREATED: 2025-08-17
 * VERSION: 1.0 - Phase 2 Example
 */

// Include necessary files
include_once "assets/includes/doctype.php";

// Load blog configuration and Phase 2 systems
include_once "assets/includes/blog_config_reader.php";
include_once "assets/includes/blog_seo_automation.php";
include_once "assets/includes/blog_social_integration.php";

// Get post ID
$postId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$postId) {
    header('Location: blog.php');
    exit;
}

// Include blog loading functionality
include_once "assets/includes/blog_load.php";

// Get post data
$stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ? AND active = 'Yes'");
$stmt->execute([$postId]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    header('Location: blog.php');
    exit;
}

// Get post category for breadcrumbs
$catStmt = $pdo->prepare("
    SELECT c.* 
    FROM blog_categories c 
    JOIN blog_post_categories pc ON c.id = pc.category_id 
    WHERE pc.post_id = ? 
    LIMIT 1
");
$catStmt->execute([$postId]);
$category = $catStmt->fetch(PDO::FETCH_ASSOC);

// Get blog configuration
$blogConfig = getBlogConfig();
$features = $blogConfig['features'];
$seo = $blogConfig['seo'];
$social = $blogConfig['social'];

// Update post views if feature is enabled
if (isBlogFeatureEnabled('post_views')) {
    $viewStmt = $pdo->prepare("UPDATE blog_posts SET views = views + 1 WHERE id = ?");
    $viewStmt->execute([$postId]);
    $post['views'] = ($post['views'] ?? 0) + 1;
}

include_once "assets/includes/header.php";
?>

<!-- Advanced SEO Meta Tags -->
<?= generatePostMetaTags($post) ?>

<!-- Schema Markup for Post -->
<?= generatePostSchema($post) ?>

<!-- Breadcrumb Schema -->
<?= generateBreadcrumbSchema($post, $category) ?>

<!-- Analytics Code -->
<?= generateAnalyticsCode() ?>

<!-- Site Verification Tags -->
<?= generateVerificationTags() ?>

<div class="container">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            
            <!-- Breadcrumbs (if enabled) -->
            <?php if (isBlogFeatureEnabled('breadcrumbs')): ?>
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="blog.php">Blog</a></li>
                    <?php if ($category): ?>
                    <li class="breadcrumb-item"><a href="category.php?id=<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></a></li>
                    <?php endif; ?>
                    <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($post['title']) ?></li>
                </ol>
            </nav>
            <?php endif; ?>
            
            <!-- Main Post Content -->
            <article class="blog-post-content">
                <header class="post-header mb-4">
                    <h1 class="post-title"><?= htmlspecialchars($post['title']) ?></h1>
                    
                    <!-- Post Meta Information -->
                    <div class="post-meta text-muted mb-3">
                        <?php if ($blogConfig['display']['show_date']): ?>
                        <span class="post-date">
                            <i class="far fa-calendar"></i> 
                            <?= date($blogConfig['display']['date_format'], strtotime($post['date'])) ?>
                        </span>
                        <?php endif; ?>
                        
                        <?php if ($blogConfig['display']['show_author'] && isBlogFeatureEnabled('author_bio')): ?>
                        <span class="post-author">
                            <i class="far fa-user"></i> 
                            <?= htmlspecialchars($post['author'] ?? $blogConfig['identity']['author_name']) ?>
                        </span>
                        <?php endif; ?>
                        
                        <?php if ($category && $blogConfig['display']['show_categories']): ?>
                        <span class="post-category">
                            <i class="far fa-folder"></i> 
                            <a href="category.php?id=<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></a>
                        </span>
                        <?php endif; ?>
                        
                        <?php if (isBlogFeatureEnabled('reading_time')): ?>
                        <span class="reading-time">
                            <i class="far fa-clock"></i> 
                            <?php
                            $wordCount = str_word_count(strip_tags($post['content']));
                            $readingTime = ceil($wordCount / 200);
                            echo $readingTime . ' min read';
                            ?>
                        </span>
                        <?php endif; ?>
                        
                        <?php if (isBlogFeatureEnabled('post_views')): ?>
                        <span class="post-views">
                            <i class="far fa-eye"></i> 
                            <?= number_format($post['views'] ?? 0) ?> views
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Featured Image -->
                    <?php if (!empty($post['image']) && $blogConfig['display']['enable_featured_image']): ?>
                    <div class="post-featured-image mb-4">
                        <?php
                        $imagePath = $post['image'];
                        if (!preg_match('/^https?:\/\//', $imagePath)) {
                            $imagePath = 'admin/blog/blog_post_images/' . ltrim($imagePath, '/');
                        }
                        ?>
                        <img src="<?= htmlspecialchars($imagePath) ?>" 
                             alt="<?= htmlspecialchars($post['title']) ?>" 
                             class="img-fluid rounded">
                    </div>
                    <?php endif; ?>
                    
                    <!-- Social Sharing Buttons (Top Position) -->
                    <?php if (isBlogFeatureEnabled('social_sharing') && ($social['sharing_button_position'] === 'top' || $social['sharing_button_position'] === 'both')): ?>
                    <div class="social-sharing-top mb-4">
                        <?= generateAdvancedSharingButtons($post) ?>
                    </div>
                    <?php endif; ?>
                </header>
                
                <!-- Post Content -->
                <div class="post-content">
                    <?= $post['content'] ?>
                </div>
                
                <!-- Post Tags -->
                <?php if ($blogConfig['display']['show_tags'] && isBlogFeatureEnabled('tags')): ?>
                <div class="post-tags mt-4">
                    <?php
                    $tagStmt = $pdo->prepare("
                        SELECT t.tag 
                        FROM blog_tags t 
                        JOIN blog_post_tags pt ON t.id = pt.tag_id 
                        WHERE pt.post_id = ?
                    ");
                    $tagStmt->execute([$postId]);
                    $tags = $tagStmt->fetchAll(PDO::FETCH_COLUMN);
                    
                    if ($tags):
                    ?>
                    <div class="tags-container">
                        <strong>Tags:</strong>
                        <?php foreach ($tags as $tag): ?>
                        <span class="badge badge-secondary me-2"><?= htmlspecialchars($tag) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <!-- Social Sharing Buttons (Bottom Position) -->
                <?php if (isBlogFeatureEnabled('social_sharing') && ($social['sharing_button_position'] === 'bottom' || $social['sharing_button_position'] === 'both')): ?>
                <div class="social-sharing-bottom mt-4 pt-4 border-top">
                    <h6>Share this post:</h6>
                    <?= generateAdvancedSharingButtons($post) ?>
                </div>
                <?php endif; ?>
                
                <!-- Author Bio (if enabled) -->
                <?php if (isBlogFeatureEnabled('author_bio')): ?>
                <div class="author-bio mt-5 pt-4 border-top">
                    <div class="d-flex align-items-center">
                        <img src="assets/img/default-avatar.png" 
                             alt="Author" 
                             class="rounded-circle me-3" 
                             style="width: 60px; height: 60px;">
                        <div>
                            <h6 class="mb-1"><?= htmlspecialchars($post['author'] ?? $blogConfig['identity']['author_name']) ?></h6>
                            <p class="text-muted mb-0"><?= htmlspecialchars($blogConfig['identity']['author_bio']) ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Post Navigation (if enabled) -->
                <?php if (isBlogFeatureEnabled('post_navigation')): ?>
                <nav class="post-navigation mt-5 pt-4 border-top">
                    <div class="row">
                        <?php
                        // Get previous post
                        $prevStmt = $pdo->prepare("SELECT id, title FROM blog_posts WHERE id < ? AND active = 'Yes' ORDER BY id DESC LIMIT 1");
                        $prevStmt->execute([$postId]);
                        $prevPost = $prevStmt->fetch(PDO::FETCH_ASSOC);
                        
                        // Get next post
                        $nextStmt = $pdo->prepare("SELECT id, title FROM blog_posts WHERE id > ? AND active = 'Yes' ORDER BY id ASC LIMIT 1");
                        $nextStmt->execute([$postId]);
                        $nextPost = $nextStmt->fetch(PDO::FETCH_ASSOC);
                        ?>
                        
                        <div class="col-6">
                            <?php if ($prevPost): ?>
                            <a href="post.php?id=<?= $prevPost['id'] ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-chevron-left"></i> Previous Post
                                <small class="d-block text-muted"><?= htmlspecialchars($prevPost['title']) ?></small>
                            </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-6 text-end">
                            <?php if ($nextPost): ?>
                            <a href="post.php?id=<?= $nextPost['id'] ?>" class="btn btn-outline-secondary">
                                Next Post <i class="fas fa-chevron-right"></i>
                                <small class="d-block text-muted"><?= htmlspecialchars($nextPost['title']) ?></small>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </nav>
                <?php endif; ?>
                
                <!-- Related Posts (if enabled) -->
                <?php if (isBlogFeatureEnabled('related_posts')): ?>
                <section class="related-posts mt-5 pt-4 border-top">
                    <h5>Related Posts</h5>
                    <?php
                    // Get related posts based on shared categories
                    $relatedStmt = $pdo->prepare("
                        SELECT DISTINCT p.id, p.title, p.image, p.date 
                        FROM blog_posts p 
                        JOIN blog_post_categories pc1 ON p.id = pc1.post_id
                        JOIN blog_post_categories pc2 ON pc1.category_id = pc2.category_id
                        WHERE pc2.post_id = ? AND p.id != ? AND p.active = 'Yes'
                        ORDER BY p.date DESC 
                        LIMIT 3
                    ");
                    $relatedStmt->execute([$postId, $postId]);
                    $relatedPosts = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if ($relatedPosts):
                    ?>
                    <div class="row">
                        <?php foreach ($relatedPosts as $relatedPost): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <?php if (!empty($relatedPost['image'])): ?>
                                <img src="admin/blog/blog_post_images/<?= htmlspecialchars($relatedPost['image']) ?>" 
                                     class="card-img-top" 
                                     alt="<?= htmlspecialchars($relatedPost['title']) ?>"
                                     style="height: 150px; object-fit: cover;">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <a href="post.php?id=<?= $relatedPost['id'] ?>">
                                            <?= htmlspecialchars($relatedPost['title']) ?>
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        <?= date($blogConfig['display']['date_format'], strtotime($relatedPost['date'])) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p class="text-muted">No related posts found.</p>
                    <?php endif; ?>
                </section>
                <?php endif; ?>
                
                <!-- Comments Section (if enabled) -->
                <?php if (isBlogFeatureEnabled('comments')): ?>
                <section class="comments-section mt-5 pt-4 border-top">
                    <h5>Comments</h5>
                    
                    <?php
                    $commentSystem = $blogConfig['comments']['comment_system'] ?? 'internal';
                    
                    switch ($commentSystem):
                        case 'disqus':
                            $disqusShortname = $blogConfig['comments']['disqus_shortname'] ?? '';
                            if ($disqusShortname):
                    ?>
                    <!-- Disqus Comments -->
                    <div id="disqus_thread"></div>
                    <script>
                    var disqus_config = function () {
                        this.page.url = '<?= htmlspecialchars($this->siteUrl . '/post.php?id=' . $postId) ?>';
                        this.page.identifier = 'post-<?= $postId ?>';
                    };
                    (function() {
                        var d = document, s = d.createElement('script');
                        s.src = 'https://<?= htmlspecialchars($disqusShortname) ?>.disqus.com/embed.js';
                        s.setAttribute('data-timestamp', +new Date());
                        (d.head || d.body).appendChild(s);
                    })();
                    </script>
                    <?php
                            endif;
                            break;
                            
                        case 'facebook':
                            $facebookAppId = $blogConfig['comments']['facebook_app_id'] ?? '';
                            if ($facebookAppId):
                    ?>
                    <!-- Facebook Comments -->
                    <div id="fb-root"></div>
                    <script async defer crossorigin="anonymous" 
                            src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v12.0&appId=<?= htmlspecialchars($facebookAppId) ?>">
                    </script>
                    <div class="fb-comments" 
                         data-href="<?= htmlspecialchars($this->siteUrl . '/post.php?id=' . $postId) ?>" 
                         data-width="100%" 
                         data-numposts="5">
                    </div>
                    <?php
                            endif;
                            break;
                            
                        case 'internal':
                        default:
                            // Load your existing internal comment system
                            include_once "assets/includes/blog_comments.php";
                            break;
                    endswitch;
                    ?>
                </section>
                <?php endif; ?>
                
            </article>
        </div>
        
        <!-- Sidebar with Follow Buttons -->
        <div class="col-lg-4">
            <div class="sidebar">
                <!-- Social Follow Buttons -->
                <?= generateFollowButtons() ?>
                
                <!-- Newsletter Signup (if enabled) -->
                <?php if (isBlogFeatureEnabled('newsletter_signup')): ?>
                <div class="newsletter-signup mt-4">
                    <h6>Subscribe to Newsletter</h6>
                    <p class="text-muted small">Get notified about new posts</p>
                    <form>
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Your email">
                            <button class="btn btn-primary" type="submit">Subscribe</button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced CSS for Phase 2 Features -->
<style>
.post-meta span {
    margin-right: 20px;
    font-size: 14px;
}

.post-meta i {
    margin-right: 5px;
}

.advanced-social-sharing {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.sharing-stats {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-right: 15px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 6px;
    min-width: 60px;
}

.share-count {
    font-weight: bold;
    font-size: 18px;
    color: #007bff;
}

.share-label {
    font-size: 12px;
    color: #666;
}

.share-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 8px 12px;
    text-decoration: none;
    color: white;
    border-radius: 4px;
    font-size: 14px;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.share-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    color: white;
    text-decoration: none;
}

.share-btn.facebook { background-color: #3b5998; }
.share-btn.twitter { background-color: #1da1f2; }
.share-btn.linkedin { background-color: #0077b5; }
.share-btn.pinterest { background-color: #bd081c; }
.share-btn.reddit { background-color: #ff4500; }
.share-btn.whatsapp { background-color: #25d366; }
.share-btn.copy-link { background-color: #666666; }

.icons-style .share-btn {
    width: 40px;
    height: 40px;
    padding: 0;
    justify-content: center;
    border-radius: 50%;
}

.floating-position {
    position: fixed;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    flex-direction: column;
    background: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 1000;
}

.social-follow-buttons {
    text-align: center;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
}

.follow-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 8px 15px;
    margin: 5px;
    text-decoration: none;
    color: white;
    border-radius: 4px;
    transition: all 0.3s ease;
}

.follow-btn:hover {
    transform: translateY(-2px);
    color: white;
    text-decoration: none;
}

.author-bio img {
    object-fit: cover;
}

.post-navigation .btn {
    width: 100%;
    text-align: left;
}

.post-navigation .col-6:last-child .btn {
    text-align: right;
}

.related-posts .card {
    transition: transform 0.3s ease;
}

.related-posts .card:hover {
    transform: translateY(-5px);
}

.newsletter-signup {
    padding: 20px;
    background: #e3f2fd;
    border-radius: 8px;
    border: 1px solid #2196f3;
}

@media (max-width: 768px) {
    .floating-position {
        position: relative;
        left: auto;
        top: auto;
        transform: none;
        margin: 20px 0;
    }
    
    .advanced-social-sharing {
        justify-content: center;
    }
}
</style>

<!-- Auto-posting trigger (for development) -->
<?php
// In production, this would be triggered when a post is published
if (isset($_GET['trigger_autopost']) && $_SESSION['admin_role'] === 'Developer') {
    $autoPostResults = autoPostToSocial($post);
    echo '<script>console.log("Auto-post results:", ' . json_encode($autoPostResults) . ');</script>';
}
?>

<?php include_once "assets/includes/footer.php"; ?>
