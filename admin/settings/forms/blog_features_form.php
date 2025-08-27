<?php
/**
 * Blog Features Settings Form
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel  
 * FILE: blog_features_form.php
 * LOCATION: /public_html/admin/settings/
 * PURPOSE: Blog functionality toggles and feature management
 * 
 * Manages all blog feature toggles including posts, pages, comments,
 * search, RSS, and other functionality through the unified database system.
 * 
 * FEATURES:
 * - Enable/disable blog components
 * - Search and archive configuration
 * - RSS and sitemap toggles
 * - Social sharing controls
 * - Newsletter signup options
 * 
 * CREATED: 2025-08-17
 * VERSION: 1.0
 */

// Initialize session and security
session_start();
require_once __DIR__ . '/../../../private/gws-universal-config.php';
require_once __DIR__ . '/../../../private/classes/SettingsManager.php';
include_once '../assets/includes/main.php';

// Security check for admin access
if (!isset($_SESSION['admin_role']) || !in_array($_SESSION['admin_role'], ['Admin', 'Editor', 'Developer'])) {
    header('Location: ../index.php');
    exit();
}

// Initialize settings manager
$settingsManager = new SettingsManager($pdo);

// Handle form submissions
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated_by = $account_loggedin['username'] ?? 'admin';
    
    try {
        // Get the features from the form (all checkboxes)
        $features = [
            'enable_posts', 'enable_pages', 'enable_categories', 'enable_tags',
            'enable_comments', 'enable_author_bio', 'enable_social_sharing',
            'enable_related_posts', 'enable_search', 'enable_archives',
            'enable_rss', 'enable_sitemap', 'enable_breadcrumbs',
            'enable_post_navigation', 'enable_reading_time', 'enable_post_views',
            'enable_newsletter_signup'
        ];
        
        $data = [];
        foreach ($features as $feature) {
            $data[$feature] = isset($_POST[$feature]) ? 1 : 0;
        }
        
        // Check if record exists
        $stmt = $pdo->prepare("SELECT id FROM setting_blog_features LIMIT 1");
        $stmt->execute();
        $exists = $stmt->fetch();
        
        if ($exists) {
            // Update existing record
            $sql = "UPDATE setting_blog_features SET 
                    enable_posts = ?, enable_pages = ?, enable_categories = ?, enable_tags = ?,
                    enable_comments = ?, enable_author_bio = ?, enable_social_sharing = ?,
                    enable_related_posts = ?, enable_search = ?, enable_archives = ?,
                    enable_rss = ?, enable_sitemap = ?, enable_breadcrumbs = ?,
                    enable_post_navigation = ?, enable_reading_time = ?, enable_post_views = ?,
                    enable_newsletter_signup = ?, updated_at = CURRENT_TIMESTAMP 
                    WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                $data['enable_posts'], $data['enable_pages'], $data['enable_categories'], $data['enable_tags'],
                $data['enable_comments'], $data['enable_author_bio'], $data['enable_social_sharing'],
                $data['enable_related_posts'], $data['enable_search'], $data['enable_archives'],
                $data['enable_rss'], $data['enable_sitemap'], $data['enable_breadcrumbs'],
                $data['enable_post_navigation'], $data['enable_reading_time'], $data['enable_post_views'],
                $data['enable_newsletter_signup'], $exists['id']
            ]);
        } else {
            // Insert new record
            $sql = "INSERT INTO setting_blog_features 
                    (enable_posts, enable_pages, enable_categories, enable_tags, enable_comments,
                     enable_author_bio, enable_social_sharing, enable_related_posts, enable_search,
                     enable_archives, enable_rss, enable_sitemap, enable_breadcrumbs,
                     enable_post_navigation, enable_reading_time, enable_post_views, enable_newsletter_signup) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                $data['enable_posts'], $data['enable_pages'], $data['enable_categories'], $data['enable_tags'],
                $data['enable_comments'], $data['enable_author_bio'], $data['enable_social_sharing'],
                $data['enable_related_posts'], $data['enable_search'], $data['enable_archives'],
                $data['enable_rss'], $data['enable_sitemap'], $data['enable_breadcrumbs'],
                $data['enable_post_navigation'], $data['enable_reading_time'], $data['enable_post_views'],
                $data['enable_newsletter_signup']
            ]);
        }
        
        if ($result) {
            $message = 'Blog features settings updated successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to update blog features settings.';
            $message_type = 'error';
        }
        
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Load current settings
$current_settings = $settingsManager->getBlogFeatures();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Features Settings - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .settings-form {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .feature-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }
        
        .feature-section h3 {
            margin: 0 0 15px 0;
            color: #495057;
            font-size: 16px;
            border-bottom: 2px solid #007cba;
            padding-bottom: 5px;
        }
        
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            padding: 8px;
            background: white;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }
        
        .checkbox-item input[type="checkbox"] {
            width: auto;
            margin: 0;
        }
        
        .checkbox-item label {
            margin: 0;
            font-weight: normal;
            cursor: pointer;
            flex: 1;
        }
        
        .checkbox-item small {
            color: #6c757d;
            font-size: 11px;
            display: block;
            margin-top: 2px;
        }
        
        .submit-btn {
            background: #007cba;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 20px;
        }
        
        .submit-btn:hover {
            background: #005a87;
        }
        
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .nav-links {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        
        .nav-links a {
            margin-right: 15px;
            color: #007cba;
            text-decoration: none;
        }
        
        .nav-links a:hover {
            text-decoration: underline;
        }
        
        .toggle-all {
            margin-bottom: 20px;
            padding: 15px;
            background: #e3f2fd;
            border-radius: 4px;
            text-align: center;
        }
        
        .toggle-all button {
            margin: 0 5px;
            padding: 8px 16px;
            background: #007cba;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .toggle-all button:hover {
            background: #005a87;
        }
    </style>
</head>
<body>
    <div class="settings-form">
        <div class="nav-links">
            <a href="blog_display_form.php">← Blog Display</a>
            <a href="settings_dash.php">Settings Dashboard</a>
            <a href="blog_comments_form.php">Blog Comments →</a>
        </div>
        
        <h1>Blog Features Settings</h1>
        <p>Enable or disable specific blog functionality and features. These settings control what components are available on your blog.</p>
        
        <?php if ($message): ?>
            <div class="message <?= $message_type ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <div class="toggle-all">
            <button type="button" onclick="toggleAll(true)">Enable All</button>
            <button type="button" onclick="toggleAll(false)">Disable All</button>
        </div>
        
        <form method="POST" action="">
            <div class="features-grid">
                <div class="feature-section">
                    <h3>Core Content</h3>
                    
                    <div class="checkbox-item">
                        <input type="checkbox" id="enable_posts" name="enable_posts" 
                               <?= ($current_settings['enable_posts'] ?? 1) ? 'checked' : '' ?>>
                        <label for="enable_posts">
                            Enable Blog Posts
                            <small>Allow creation and display of blog posts</small>
                        </label>
                    </div>
                    
                    <div class="checkbox-item">
                        <input type="checkbox" id="enable_pages" name="enable_pages" 
                               <?= ($current_settings['enable_pages'] ?? 1) ? 'checked' : '' ?>>
                        <label for="enable_pages">
                            Enable Static Pages
                            <small>Allow creation and display of static pages</small>
                        </label>
                    </div>
                    
                    <div class="checkbox-item">
                        <input type="checkbox" id="enable_categories" name="enable_categories" 
                               <?= ($current_settings['enable_categories'] ?? 1) ? 'checked' : '' ?>>
                        <label for="enable_categories">
                            Enable Categories
                            <small>Allow post categorization and category pages</small>
                        </label>
                    </div>
                    
                    <div class="checkbox-item">
                        <input type="checkbox" id="enable_tags" name="enable_tags" 
                               <?= ($current_settings['enable_tags'] ?? 1) ? 'checked' : '' ?>>
                        <label for="enable_tags">
                            Enable Tags
                            <small>Allow post tagging and tag clouds</small>
                        </label>
                    </div>
                </div>
                
                <div class="feature-section">
                    <h3>Interaction Features</h3>
                    
                    <div class="checkbox-item">
                        <input type="checkbox" id="enable_comments" name="enable_comments" 
                               <?= ($current_settings['enable_comments'] ?? 1) ? 'checked' : '' ?>>
                        <label for="enable_comments">
                            Enable Comments
                            <small>Allow visitors to comment on posts</small>
                        </label>
                    </div>
                    
                    <div class="checkbox-item">
                        <input type="checkbox" id="enable_social_sharing" name="enable_social_sharing" 
                               <?= ($current_settings['enable_social_sharing'] ?? 1) ? 'checked' : '' ?>>
                        <label for="enable_social_sharing">
                            Enable Social Sharing
                            <small>Show social media sharing buttons</small>
                        </label>
                    </div>
                    
                    <div class="checkbox-item">
                        <input type="checkbox" id="enable_newsletter_signup" name="enable_newsletter_signup" 
                               <?= ($current_settings['enable_newsletter_signup'] ?? 0) ? 'checked' : '' ?>>
                        <label for="enable_newsletter_signup">
                            Enable Newsletter Signup
                            <small>Show newsletter subscription forms</small>
                        </label>
                    </div>
                </div>
                
                <div class="feature-section">
                    <h3>Navigation & Discovery</h3>
                    
                    <div class="checkbox-item">
                        <input type="checkbox" id="enable_search" name="enable_search" 
                               <?= ($current_settings['enable_search'] ?? 1) ? 'checked' : '' ?>>
                        <label for="enable_search">
                            Enable Search
                            <small>Allow visitors to search blog content</small>
                        </label>
                    </div>
                    
                    <div class="checkbox-item">
                        <input type="checkbox" id="enable_archives" name="enable_archives" 
                               <?= ($current_settings['enable_archives'] ?? 1) ? 'checked' : '' ?>>
                        <label for="enable_archives">
                            Enable Archives
                            <small>Show date-based archive pages</small>
                        </label>
                    </div>
                    
                    <div class="checkbox-item">
                        <input type="checkbox" id="enable_related_posts" name="enable_related_posts" 
                               <?= ($current_settings['enable_related_posts'] ?? 1) ? 'checked' : '' ?>>
                        <label for="enable_related_posts">
                            Enable Related Posts
                            <small>Show related posts on post pages</small>
                        </label>
                    </div>
                    
                    <div class="checkbox-item">
                        <input type="checkbox" id="enable_breadcrumbs" name="enable_breadcrumbs" 
                               <?= ($current_settings['enable_breadcrumbs'] ?? 1) ? 'checked' : '' ?>>
                        <label for="enable_breadcrumbs">
                            Enable Breadcrumbs
                            <small>Show navigation breadcrumb trails</small>
                        </label>
                    </div>
                    
                    <div class="checkbox-item">
                        <input type="checkbox" id="enable_post_navigation" name="enable_post_navigation" 
                               <?= ($current_settings['enable_post_navigation'] ?? 1) ? 'checked' : '' ?>>
                        <label for="enable_post_navigation">
                            Enable Post Navigation
                            <small>Show previous/next post links</small>
                        </label>
                    </div>
                </div>
                
                <div class="feature-section">
                    <h3>Technical Features</h3>
                    
                    <div class="checkbox-item">
                        <input type="checkbox" id="enable_rss" name="enable_rss" 
                               <?= ($current_settings['enable_rss'] ?? 1) ? 'checked' : '' ?>>
                        <label for="enable_rss">
                            Enable RSS Feeds
                            <small>Generate RSS feeds for blog content</small>
                        </label>
                    </div>
                    
                    <div class="checkbox-item">
                        <input type="checkbox" id="enable_sitemap" name="enable_sitemap" 
                               <?= ($current_settings['enable_sitemap'] ?? 1) ? 'checked' : '' ?>>
                        <label for="enable_sitemap">
                            Enable XML Sitemap
                            <small>Generate XML sitemaps for SEO</small>
                        </label>
                    </div>
                    
                    <div class="checkbox-item">
                        <input type="checkbox" id="enable_author_bio" name="enable_author_bio" 
                               <?= ($current_settings['enable_author_bio'] ?? 1) ? 'checked' : '' ?>>
                        <label for="enable_author_bio">
                            Enable Author Bio
                            <small>Show author information on posts</small>
                        </label>
                    </div>
                    
                    <div class="checkbox-item">
                        <input type="checkbox" id="enable_reading_time" name="enable_reading_time" 
                               <?= ($current_settings['enable_reading_time'] ?? 1) ? 'checked' : '' ?>>
                        <label for="enable_reading_time">
                            Enable Reading Time
                            <small>Show estimated reading time for posts</small>
                        </label>
                    </div>
                    
                    <div class="checkbox-item">
                        <input type="checkbox" id="enable_post_views" name="enable_post_views" 
                               <?= ($current_settings['enable_post_views'] ?? 1) ? 'checked' : '' ?>>
                        <label for="enable_post_views">
                            Enable Post Views Counter
                            <small>Track and display post view counts</small>
                        </label>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="submit-btn">Update Blog Features Settings</button>
        </form>
    </div>
    
    <script>
        function toggleAll(enable) {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = enable;
            });
        }
    </script>
</body>
</html>
