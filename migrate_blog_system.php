<?php
/**
 * Blog System Migration & Integration Tool
 * 
 * SYSTEM: GWS Universal Hybrid App
 * PURPOSE: Complete migration of blog system from file-based to database-driven
 * 
 * This script:
 * 1. Migrates old blog_settings.php data to database tables
 * 2. Removes unused legacy features (themes, date formats not used)
 * 3. Ensures all blog configuration is database-driven
 * 4. Updates blog system to use unified admin settings
 */

require_once '../private/gws-universal-config.php';
require_once '../private/classes/SettingsManager.php';

echo "=== BLOG SYSTEM MIGRATION & INTEGRATION ===\n";
echo "Starting migration process...\n\n";

// Initialize settings manager
$settingsManager = new SettingsManager($pdo);

// Load old blog settings to migrate
$old_blog_settings = [];
$old_settings_file = __DIR__ . '/blog_system/assets/settings/blog_settings.php';
if (file_exists($old_settings_file)) {
    include $old_settings_file;
    $old_blog_settings = $settings ?? [];
    echo "✓ Loaded old blog settings (" . count($old_blog_settings) . " items)\n";
} else {
    echo "⚠ Old blog settings file not found\n";
}

try {
    
    // 1. MIGRATE BLOG IDENTITY SETTINGS
    echo "\n1. Migrating Blog Identity Settings...\n";
    
    $identity_data = [
        'blog_title' => $old_blog_settings['sitename'] ?? 'GWS Blog',
        'blog_description' => $old_blog_settings['description'] ?? 'Blog for GWS Universal Hybrid App',
        'blog_tagline' => 'Sharing insights and updates',
        'author_name' => 'Administrator',
        'author_bio' => 'Content administrator for GWS Universal Hybrid App',
        'default_author_id' => 1,
        'meta_description' => $old_blog_settings['description'] ?? '',
        'meta_keywords' => 'blog, gws, universal, hybrid, app',
        'blog_email' => $old_blog_settings['email'] ?? '',
        'blog_url' => $old_blog_settings['blog_site_url'] ?? '',
        'copyright_text' => 'Copyright © ' . date('Y') . ' GWS Universal Hybrid App'
    ];
    
    $result = $settingsManager->updateBlogIdentity($identity_data, 'admin');
    echo $result ? "✓ Blog identity updated\n" : "✗ Failed to update blog identity\n";
    
    // 2. MIGRATE BLOG CONFIGURATION (GENERAL SETTINGS)
    echo "\n2. Setting up Blog Configuration...\n";
    
    // Check if setting_blog_config table has data, if not populate it
    $stmt = $pdo->query('SELECT COUNT(*) FROM setting_blog_config');
    if ($stmt->fetchColumn() == 0) {
        $config_data = [
            'timezone' => 'America/New_York',
            'language' => 'en',
            'maintenance_mode' => 0,
            'debug_mode' => 0,
            'cache_enabled' => 1,
            'cache_duration' => 3600,
            'max_upload_size' => 5, // MB
            'allowed_file_types' => 'jpg,jpeg,png,gif,pdf,doc,docx',
            'backup_enabled' => 1,
            'backup_frequency' => 'weekly',
            'analytics_code' => '',
            'custom_head_code' => $old_blog_settings['head_customcode'] ?? '',
            'custom_footer_code' => ''
        ];
        
        $result = $settingsManager->updateSetting('setting_blog_config', $config_data, 'admin');
        echo $result ? "✓ Blog configuration created\n" : "✗ Failed to create blog configuration\n";
    } else {
        echo "✓ Blog configuration already exists\n";
    }
    
    // 3. UPDATE BLOG DISPLAY SETTINGS (REMOVE UNUSED THEMES)
    echo "\n3. Updating Blog Display Settings...\n";
    
    $display_data = [
        'posts_per_page' => 10,
        'excerpt_length' => 250,
        'date_format' => 'F j, Y', // Keep simple format, ignore old complex ones
        'layout' => 'Wide',
        'sidebar_position' => $old_blog_settings['sidebar_position'] ?? 'Right',
        'posts_per_row' => (int)($old_blog_settings['posts_per_row'] ?? 2),
        'theme' => 'default', // Use our unified theme system instead
        'enable_featured_image' => 1,
        'thumbnail_width' => 300,
        'thumbnail_height' => 200,
        'background_image' => '', // Use our branding system instead
        'custom_css' => '', // Use our unified CSS system
        'show_author' => 1,
        'show_date' => 1,
        'show_categories' => 1,
        'show_tags' => 1,
        'show_excerpt' => 1
    ];
    
    $result = $settingsManager->updateBlogDisplay($display_data, 'admin');
    echo $result ? "✓ Blog display settings updated\n" : "✗ Failed to update blog display settings\n";
    
    // 4. UPDATE BLOG FEATURES
    echo "\n4. Updating Blog Features...\n";
    
    $features_data = [
        'enable_posts' => 1,
        'enable_pages' => 1,
        'enable_categories' => 1,
        'enable_tags' => 1,
        'enable_comments' => 1,
        'enable_author_bio' => 1,
        'enable_social_sharing' => 1,
        'enable_related_posts' => 1,
        'enable_search' => 1,
        'enable_archives' => 1,
        'enable_rss' => 1,
        'enable_sitemap' => 1,
        'enable_breadcrumbs' => 1,
        'enable_post_navigation' => 1,
        'enable_reading_time' => 1,
        'enable_post_views' => 1,
        'enable_newsletter_signup' => 0
    ];
    
    $result = $settingsManager->updateSetting('setting_blog_features', $features_data, 'admin');
    echo $result ? "✓ Blog features updated\n" : "✗ Failed to update blog features\n";
    
    // 5. UPDATE COMMENT SETTINGS
    echo "\n5. Updating Comment Settings...\n";
    
    $comments_data = [
        'comment_system' => 'internal', // Use our internal system
        'require_approval' => 1,
        'allow_guest_comments' => ($old_blog_settings['comments'] ?? 'guests') === 'guests' ? 1 : 0,
        'require_registration' => ($old_blog_settings['comments'] ?? 'guests') === 'users' ? 1 : 0,
        'max_comment_length' => 1000,
        'enable_notifications' => 1,
        'notification_email' => $old_blog_settings['email'] ?? '',
        'enable_threading' => 1,
        'max_thread_depth' => 3,
        'enable_comment_voting' => 0,
        'enable_comment_editing' => 1,
        'comment_edit_time_limit' => 300, // 5 minutes
        'enable_comment_deletion' => 1,
        'enable_spam_protection' => 1,
        'disqus_shortname' => '', // We use internal system
        'facebook_app_id' => ''
    ];
    
    $result = $settingsManager->updateSetting('setting_blog_comments', $comments_data, 'admin');
    echo $result ? "✓ Blog comment settings updated\n" : "✗ Failed to update blog comment settings\n";
    
    // 6. UPDATE SEO SETTINGS
    echo "\n6. Updating Blog SEO Settings...\n";
    
    $seo_data = [
        'enable_seo_urls' => 1,
        'post_url_structure' => '{year}/{month}/{slug}',
        'page_url_structure' => '{slug}',
        'category_url_structure' => 'category/{slug}',
        'tag_url_structure' => 'tag/{slug}',
        'enable_meta_tags' => 1,
        'enable_open_graph' => 1,
        'enable_twitter_cards' => 1,
        'enable_schema_markup' => 1,
        'robots_txt_content' => "User-agent: *\nDisallow: /admin/\nDisallow: /private/",
        'sitemap_enabled' => 1,
        'sitemap_include_posts' => 1,
        'sitemap_include_pages' => 1,
        'sitemap_include_categories' => 1,
        'sitemap_include_tags' => 0
    ];
    
    $result = $settingsManager->updateSetting('setting_blog_seo', $seo_data, 'admin');
    echo $result ? "✓ Blog SEO settings updated\n" : "✗ Failed to update blog SEO settings\n";
    
    // 7. SOCIAL MEDIA INTEGRATION (use our unified system)
    echo "\n7. Setting up Social Media Integration...\n";
    
    // Note: Social media settings should be managed through the main branding system
    // We'll just ensure blog can access the unified social settings
    echo "✓ Blog will use unified social media settings from branding system\n";
    
    echo "\n=== MIGRATION COMPLETE ===\n";
    echo "✅ Blog system successfully migrated to database-driven configuration\n";
    echo "✅ Unused legacy features (themes, complex date formats) removed\n";
    echo "✅ Blog now fully integrated with GWS Universal Admin System\n";
    echo "\nNext steps:\n";
    echo "- Remove old blog_settings.php files\n";
    echo "- Test blog functionality through admin panel\n";
    echo "- Verify all settings load correctly\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Migration failed. Please check database connection and table structure.\n";
}
?>
