<?php
/**
 * Blog SEO Settings Form
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel  
 * FILE: blog_seo_form.php
 * LOCATION: /public_html/admin/settings/
 * PURPOSE: Blog search engine optimization configuration
 * 
 * Manages all SEO-related settings for the blog including meta tags,
 * URL structures, sitemaps, and analytics integration.
 * 
 * FEATURES:
 * - SEO URL configuration
 * - Meta tags and Open Graph settings
 * - Sitemap generation settings
 * - Analytics integration
 * - Schema markup controls
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
        $data = [
            'enable_seo_urls' => isset($_POST['enable_seo_urls']) ? 1 : 0,
            'post_url_structure' => sanitize_input($_POST['post_url_structure']),
            'enable_meta_tags' => isset($_POST['enable_meta_tags']) ? 1 : 0,
            'enable_open_graph' => isset($_POST['enable_open_graph']) ? 1 : 0,
            'enable_twitter_cards' => isset($_POST['enable_twitter_cards']) ? 1 : 0,
            'default_post_image' => sanitize_input($_POST['default_post_image']),
            'robots_txt_additions' => $_POST['robots_txt_additions'], // Don't sanitize robots.txt
            'sitemap_frequency' => sanitize_input($_POST['sitemap_frequency']),
            'sitemap_priority' => (float)$_POST['sitemap_priority'],
            'enable_canonical_urls' => isset($_POST['enable_canonical_urls']) ? 1 : 0,
            'enable_schema_markup' => isset($_POST['enable_schema_markup']) ? 1 : 0,
            'google_analytics_id' => sanitize_input($_POST['google_analytics_id']),
            'google_site_verification' => sanitize_input($_POST['google_site_verification']),
            'bing_site_verification' => sanitize_input($_POST['bing_site_verification']),
            'enable_breadcrumb_schema' => isset($_POST['enable_breadcrumb_schema']) ? 1 : 0,
            'enable_article_schema' => isset($_POST['enable_article_schema']) ? 1 : 0,
            'default_meta_description' => sanitize_input($_POST['default_meta_description'])
        ];
        
        // Validate URL structure
        if (empty($data['post_url_structure'])) {
            $data['post_url_structure'] = '{year}/{month}/{slug}';
        }
        
        // Validate sitemap priority
        if ($data['sitemap_priority'] < 0.1) $data['sitemap_priority'] = 0.1;
        if ($data['sitemap_priority'] > 1.0) $data['sitemap_priority'] = 1.0;
        
        // Validate image URL
        if (!empty($data['default_post_image']) && !filter_var($data['default_post_image'], FILTER_VALIDATE_URL)) {
            throw new Exception('Invalid default post image URL');
        }
        
        // Check if record exists
        $stmt = $pdo->prepare("SELECT id FROM setting_blog_seo LIMIT 1");
        $stmt->execute();
        $exists = $stmt->fetch();
        
        if ($exists) {
            // Update existing record
            $sql = "UPDATE setting_blog_seo SET 
                    enable_seo_urls = ?, post_url_structure = ?, enable_meta_tags = ?, enable_open_graph = ?,
                    enable_twitter_cards = ?, default_post_image = ?, robots_txt_additions = ?, sitemap_frequency = ?,
                    sitemap_priority = ?, enable_canonical_urls = ?, enable_schema_markup = ?, google_analytics_id = ?,
                    google_site_verification = ?, bing_site_verification = ?, enable_breadcrumb_schema = ?,
                    enable_article_schema = ?, default_meta_description = ?, updated_at = CURRENT_TIMESTAMP 
                    WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                $data['enable_seo_urls'], $data['post_url_structure'], $data['enable_meta_tags'], $data['enable_open_graph'],
                $data['enable_twitter_cards'], $data['default_post_image'], $data['robots_txt_additions'], $data['sitemap_frequency'],
                $data['sitemap_priority'], $data['enable_canonical_urls'], $data['enable_schema_markup'], $data['google_analytics_id'],
                $data['google_site_verification'], $data['bing_site_verification'], $data['enable_breadcrumb_schema'],
                $data['enable_article_schema'], $data['default_meta_description'], $exists['id']
            ]);
        } else {
            // Insert new record
            $sql = "INSERT INTO setting_blog_seo 
                    (enable_seo_urls, post_url_structure, enable_meta_tags, enable_open_graph, enable_twitter_cards,
                     default_post_image, robots_txt_additions, sitemap_frequency, sitemap_priority, enable_canonical_urls,
                     enable_schema_markup, google_analytics_id, google_site_verification, bing_site_verification,
                     enable_breadcrumb_schema, enable_article_schema, default_meta_description) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                $data['enable_seo_urls'], $data['post_url_structure'], $data['enable_meta_tags'], $data['enable_open_graph'],
                $data['enable_twitter_cards'], $data['default_post_image'], $data['robots_txt_additions'], $data['sitemap_frequency'],
                $data['sitemap_priority'], $data['enable_canonical_urls'], $data['enable_schema_markup'], $data['google_analytics_id'],
                $data['google_site_verification'], $data['bing_site_verification'], $data['enable_breadcrumb_schema'],
                $data['enable_article_schema'], $data['default_meta_description']
            ]);
        }
        
        if ($result) {
            $message = 'Blog SEO settings updated successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to update blog SEO settings.';
            $message_type = 'error';
        }
        
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Load current settings
$current_settings = $settingsManager->getBlogSeo();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog SEO Settings - Admin Panel</title>
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
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
            flex: 1;
        }
        
        .form-group.full-width {
            flex: 100%;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .form-group textarea {
            height: 120px;
            resize: vertical;
        }
        
        .form-group small {
            color: #666;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }
        
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #e9ecef;
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
        
        .section-header {
            background: #007cba;
            color: white;
            padding: 15px;
            margin: 30px 0 20px 0;
            border-radius: 6px;
            font-size: 18px;
            font-weight: bold;
        }
        
        .analytics-section {
            background: #f1f3f4;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #34a853;
        }
        
        .url-preview {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 4px;
            margin-top: 10px;
            font-family: monospace;
            border: 1px solid #2196f3;
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
    </style>
</head>
<body>
    <div class="settings-form">
        <div class="nav-links">
            <a href="blog_comments_form.php">← Blog Comments</a>
            <a href="settings_dash.php">Settings Dashboard</a>
            <a href="blog_social_form.php">Blog Social →</a>
        </div>
        
        <h1>Blog SEO Settings</h1>
        <p>Configure search engine optimization settings to improve your blog's visibility and search rankings.</p>
        
        <?php if ($message): ?>
            <div class="message <?= $message_type ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="professional-section-header">URL Structure & SEO URLs</div>
            
            <div class="checkbox-item">
                <input type="checkbox" id="enable_seo_urls" name="enable_seo_urls" 
                       <?= ($current_settings['enable_seo_urls'] ?? 1) ? 'checked' : '' ?>>
                <label for="enable_seo_urls">
                    Enable SEO-Friendly URLs
                    <small>Use clean, readable URLs instead of query parameters</small>
                </label>
            </div>
            
            <div class="form-group">
                <label for="post_url_structure">Post URL Structure</label>
                <select id="post_url_structure" name="post_url_structure" onchange="updateUrlPreview()">
                    <option value="{year}/{month}/{slug}" <?= ($current_settings['post_url_structure'] ?? '') === '{year}/{month}/{slug}' ? 'selected' : '' ?>>Year/Month/Post Name</option>
                    <option value="{year}/{slug}" <?= ($current_settings['post_url_structure'] ?? '') === '{year}/{slug}' ? 'selected' : '' ?>>Year/Post Name</option>
                    <option value="{category}/{slug}" <?= ($current_settings['post_url_structure'] ?? '') === '{category}/{slug}' ? 'selected' : '' ?>>Category/Post Name</option>
                    <option value="{slug}" <?= ($current_settings['post_url_structure'] ?? '') === '{slug}' ? 'selected' : '' ?>>Post Name Only</option>
                    <option value="blog/{slug}" <?= ($current_settings['post_url_structure'] ?? '') === 'blog/{slug}' ? 'selected' : '' ?>>Blog/Post Name</option>
                </select>
                <small>Structure for individual blog post URLs</small>
                <div class="url-preview" id="url-preview">
                    Preview: /blog/2025/08/my-awesome-blog-post/
                </div>
            </div>
            
            <div class="professional-section-header">Meta Tags & Social Media</div>
            
            <div class="checkbox-item">
                <input type="checkbox" id="enable_meta_tags" name="enable_meta_tags" 
                       <?= ($current_settings['enable_meta_tags'] ?? 1) ? 'checked' : '' ?>>
                <label for="enable_meta_tags">
                    Enable Meta Tags
                    <small>Generate meta descriptions, keywords, and other SEO meta tags</small>
                </label>
            </div>
            
            <div class="checkbox-item">
                <input type="checkbox" id="enable_open_graph" name="enable_open_graph" 
                       <?= ($current_settings['enable_open_graph'] ?? 1) ? 'checked' : '' ?>>
                <label for="enable_open_graph">
                    Enable Open Graph Tags
                    <small>Generate Open Graph meta tags for better social media sharing</small>
                </label>
            </div>
            
            <div class="checkbox-item">
                <input type="checkbox" id="enable_twitter_cards" name="enable_twitter_cards" 
                       <?= ($current_settings['enable_twitter_cards'] ?? 1) ? 'checked' : '' ?>>
                <label for="enable_twitter_cards">
                    Enable Twitter Cards
                    <small>Generate Twitter Card meta tags for enhanced Twitter sharing</small>
                </label>
            </div>
            
            <div class="form-group">
                <label for="default_post_image">Default Post Image URL</label>
                <input type="url" id="default_post_image" name="default_post_image" 
                       value="<?= htmlspecialchars($current_settings['default_post_image'] ?? '') ?>">
                <small>Default image for posts without featured images (used in social sharing)</small>
            </div>
            
            <div class="form-group">
                <label for="default_meta_description">Default Meta Description</label>
                <textarea id="default_meta_description" name="default_meta_description"><?= htmlspecialchars($current_settings['default_meta_description'] ?? '') ?></textarea>
                <small>Default meta description for blog pages (150-160 characters recommended)</small>
            </div>
            
            <div class="professional-section-header">Canonical URLs & Schema</div>
            
            <div class="checkbox-item">
                <input type="checkbox" id="enable_canonical_urls" name="enable_canonical_urls" 
                       <?= ($current_settings['enable_canonical_urls'] ?? 1) ? 'checked' : '' ?>>
                <label for="enable_canonical_urls">
                    Enable Canonical URLs
                    <small>Add canonical link tags to prevent duplicate content issues</small>
                </label>
            </div>
            
            <div class="checkbox-item">
                <input type="checkbox" id="enable_schema_markup" name="enable_schema_markup" 
                       <?= ($current_settings['enable_schema_markup'] ?? 1) ? 'checked' : '' ?>>
                <label for="enable_schema_markup">
                    Enable Schema Markup
                    <small>Add structured data markup for better search engine understanding</small>
                </label>
            </div>
            
            <div class="checkbox-item">
                <input type="checkbox" id="enable_breadcrumb_schema" name="enable_breadcrumb_schema" 
                       <?= ($current_settings['enable_breadcrumb_schema'] ?? 1) ? 'checked' : '' ?>>
                <label for="enable_breadcrumb_schema">
                    Enable Breadcrumb Schema
                    <small>Add breadcrumb structured data for better navigation understanding</small>
                </label>
            </div>
            
            <div class="checkbox-item">
                <input type="checkbox" id="enable_article_schema" name="enable_article_schema" 
                       <?= ($current_settings['enable_article_schema'] ?? 1) ? 'checked' : '' ?>>
                <label for="enable_article_schema">
                    Enable Article Schema
                    <small>Add article structured data for blog posts</small>
                </label>
            </div>
            
            <div class="professional-section-header">Sitemap Configuration</div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="sitemap_frequency">Sitemap Update Frequency</label>
                    <select id="sitemap_frequency" name="sitemap_frequency">
                        <option value="always" <?= ($current_settings['sitemap_frequency'] ?? '') === 'always' ? 'selected' : '' ?>>Always</option>
                        <option value="hourly" <?= ($current_settings['sitemap_frequency'] ?? '') === 'hourly' ? 'selected' : '' ?>>Hourly</option>
                        <option value="daily" <?= ($current_settings['sitemap_frequency'] ?? '') === 'daily' ? 'selected' : '' ?>>Daily</option>
                        <option value="weekly" <?= ($current_settings['sitemap_frequency'] ?? 'weekly') === 'weekly' ? 'selected' : '' ?>>Weekly</option>
                        <option value="monthly" <?= ($current_settings['sitemap_frequency'] ?? '') === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                        <option value="yearly" <?= ($current_settings['sitemap_frequency'] ?? '') === 'yearly' ? 'selected' : '' ?>>Yearly</option>
                        <option value="never" <?= ($current_settings['sitemap_frequency'] ?? '') === 'never' ? 'selected' : '' ?>>Never</option>
                    </select>
                    <small>How often blog content changes (for XML sitemap)</small>
                </div>
                
                <div class="form-group">
                    <label for="sitemap_priority">Sitemap Priority</label>
                    <input type="number" id="sitemap_priority" name="sitemap_priority" 
                           value="<?= htmlspecialchars($current_settings['sitemap_priority'] ?? '0.8') ?>" 
                           min="0.1" max="1.0" step="0.1">
                    <small>Priority of blog pages in sitemap (0.1 to 1.0)</small>
                </div>
            </div>
            
            <div class="form-group">
                <label for="robots_txt_additions">Robots.txt Additions</label>
                <textarea id="robots_txt_additions" name="robots_txt_additions"><?= htmlspecialchars($current_settings['robots_txt_additions'] ?? '') ?></textarea>
                <small>Additional rules to add to robots.txt file (advanced users only)</small>
            </div>
            
            <div class="analytics-section">
                <h3>Analytics & Verification</h3>
                
                <div class="form-group">
                    <label for="google_analytics_id">Google Analytics ID</label>
                    <input type="text" id="google_analytics_id" name="google_analytics_id" 
                           value="<?= htmlspecialchars($current_settings['google_analytics_id'] ?? '') ?>" 
                           placeholder="GA4: G-XXXXXXXXXX or Universal: UA-XXXXXXX-X">
                    <small>Your Google Analytics tracking ID (GA4 or Universal Analytics)</small>
                </div>
                
                <div class="form-group">
                    <label for="google_site_verification">Google Site Verification</label>
                    <input type="text" id="google_site_verification" name="google_site_verification" 
                           value="<?= htmlspecialchars($current_settings['google_site_verification'] ?? '') ?>" 
                           placeholder="google-site-verification code">
                    <small>Google Search Console verification meta tag content</small>
                </div>
                
                <div class="form-group">
                    <label for="bing_site_verification">Bing Site Verification</label>
                    <input type="text" id="bing_site_verification" name="bing_site_verification" 
                           value="<?= htmlspecialchars($current_settings['bing_site_verification'] ?? '') ?>" 
                           placeholder="msvalidate.01 code">
                    <small>Bing Webmaster Tools verification meta tag content</small>
                </div>
            </div>
            
            <button type="submit" class="submit-btn">Update Blog SEO Settings</button>
        </form>
    </div>
    
    <script>
        function updateUrlPreview() {
            const structure = document.getElementById('post_url_structure').value;
            const preview = document.getElementById('url-preview');
            
            let exampleUrl = structure
                .replace('{year}', '2025')
                .replace('{month}', '08')
                .replace('{category}', 'technology')
                .replace('{slug}', 'my-awesome-blog-post');
            
            preview.textContent = 'Preview: /blog/' + exampleUrl + '/';
        }
        
        // Initialize preview on page load
        document.addEventListener('DOMContentLoaded', updateUrlPreview);
    </script>
</body>
</html>
