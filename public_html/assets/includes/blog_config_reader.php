<?php
/**
 * Blog Configuration Integration Helper
 * 
 * SYSTEM: GWS Universal Hybrid App - Blog Integration
 * FILE: blog_config_reader.php
 * LOCATION: /public_html/assets/includes/
 * PURPOSE: Bridge between existing blog system and new configuration system
 * 
 * This helper file provides seamless integration between the existing blog
 * management system (/admin/blog/) and the new configuration system 
 * (/admin/settings/blog_*_form.php). It reads settings from the new
 * configuration tables and makes them available to existing blog pages.
 * 
 * FEATURES:
 * - Cached settings reading for performance
 * - Fallback to defaults if tables don't exist
 * - Backward compatibility with existing blog_settings.php
 * - Zero breaking changes to existing functionality
 * 
 * CREATED: 2025-08-17
 * VERSION: 1.0
 */

// Prevent direct access
if (!defined('BLOG_CONFIG_INTEGRATION')) {
    define('BLOG_CONFIG_INTEGRATION', true);
}

class BlogConfigReader {
    private $pdo;
    private $settingsManager;
    private $cache = [];
    private $cacheTimeout = 300; // 5 minutes
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        
        // Try to load SettingsManager if available
        $settingsManagerPath = __DIR__ . '/../../private/classes/SettingsManager.php';
        if (file_exists($settingsManagerPath)) {
            require_once $settingsManagerPath;
            $this->settingsManager = new SettingsManager($pdo);
        }
    }
    
    /**
     * Get all blog configuration settings with caching
     */
    public function getAllBlogConfig() {
        $cacheKey = 'all_blog_config';
        
        // Check cache first
        if (isset($this->cache[$cacheKey])) {
            if (time() - $this->cache[$cacheKey]['timestamp'] < $this->cacheTimeout) {
                return $this->cache[$cacheKey]['data'];
            }
        }
        
        $config = [
            'identity' => $this->getBlogIdentity(),
            'display' => $this->getBlogDisplay(), 
            'features' => $this->getBlogFeatures(),
            'comments' => $this->getBlogComments(),
            'seo' => $this->getBlogSeo(),
            'social' => $this->getBlogSocial()
        ];
        
        // Cache the result
        $this->cache[$cacheKey] = [
            'data' => $config,
            'timestamp' => time()
        ];
        
        return $config;
    }
    
    /**
     * Get blog identity settings
     */
    public function getBlogIdentity() {
        if ($this->settingsManager && method_exists($this->settingsManager, 'getBlogIdentity')) {
            try {
                return $this->settingsManager->getBlogIdentity();
            } catch (Exception $e) {
                // Fall back to defaults if table doesn't exist
            }
        }
        
        // Default values if new system not available
        return [
            'blog_title' => 'My Blog',
            'blog_description' => 'Welcome to my blog',
            'blog_tagline' => 'Sharing thoughts and ideas',
            'author_name' => 'Blog Author',
            'author_bio' => 'About the author',
            'meta_description' => 'Blog meta description',
            'meta_keywords' => 'blog, content, articles',
            'blog_email' => '',
            'blog_url' => '',
            'copyright_text' => '© 2025 My Blog. All rights reserved.'
        ];
    }
    
    /**
     * Get blog display settings
     */
    public function getBlogDisplay() {
        if ($this->settingsManager && method_exists($this->settingsManager, 'getBlogDisplay')) {
            try {
                return $this->settingsManager->getBlogDisplay();
            } catch (Exception $e) {
                // Fall back to defaults
            }
        }
        
        return [
            'posts_per_page' => 10,
            'excerpt_length' => 250,
            'date_format' => 'F j, Y',
            'layout' => 'Wide',
            'sidebar_position' => 'Right',
            'posts_per_row' => 2,
            'theme' => 'Default',
            'enable_featured_image' => 1,
            'thumbnail_width' => 300,
            'thumbnail_height' => 200,
            'show_author' => 1,
            'show_date' => 1,
            'show_categories' => 1,
            'show_tags' => 1,
            'show_excerpt' => 1
        ];
    }
    
    /**
     * Get blog features settings
     */
    public function getBlogFeatures() {
        if ($this->settingsManager && method_exists($this->settingsManager, 'getBlogFeatures')) {
            try {
                return $this->settingsManager->getBlogFeatures();
            } catch (Exception $e) {
                // Fall back to defaults
            }
        }
        
        return [
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
    }
    
    /**
     * Get blog comments settings
     */
    public function getBlogComments() {
        if ($this->settingsManager && method_exists($this->settingsManager, 'getBlogComments')) {
            try {
                return $this->settingsManager->getBlogComments();
            } catch (Exception $e) {
                // Fall back to defaults
            }
        }
        
        return [
            'comment_system' => 'internal',
            'require_approval' => 1,
            'allow_guest_comments' => 1,
            'require_registration' => 0,
            'max_comment_length' => 1000,
            'enable_notifications' => 1,
            'notification_email' => 'admin@example.com',
            'enable_threading' => 1,
            'max_thread_depth' => 3,
            'enable_comment_voting' => 0,
            'enable_comment_editing' => 1,
            'comment_edit_time_limit' => 300,
            'enable_comment_deletion' => 1,
            'enable_spam_protection' => 1
        ];
    }
    
    /**
     * Get blog SEO settings
     */
    public function getBlogSeo() {
        if ($this->settingsManager && method_exists($this->settingsManager, 'getBlogSeo')) {
            try {
                return $this->settingsManager->getBlogSeo();
            } catch (Exception $e) {
                // Fall back to defaults
            }
        }
        
        return [
            'enable_seo_urls' => 1,
            'post_url_structure' => '{year}/{month}/{slug}',
            'enable_meta_tags' => 1,
            'enable_open_graph' => 1,
            'enable_twitter_cards' => 1,
            'default_post_image' => '',
            'sitemap_frequency' => 'weekly',
            'sitemap_priority' => 0.8,
            'enable_canonical_urls' => 1,
            'enable_schema_markup' => 1,
            'google_analytics_id' => '',
            'google_site_verification' => '',
            'bing_site_verification' => '',
            'enable_breadcrumb_schema' => 1,
            'enable_article_schema' => 1,
            'default_meta_description' => ''
        ];
    }
    
    /**
     * Get blog social settings
     */
    public function getBlogSocial() {
        if ($this->settingsManager && method_exists($this->settingsManager, 'getBlogSocial')) {
            try {
                return $this->settingsManager->getBlogSocial();
            } catch (Exception $e) {
                // Fall back to defaults
            }
        }
        
        return [
            'enable_sharing_buttons' => 1,
            'sharing_platforms' => 'facebook,twitter,linkedin',
            'sharing_button_style' => 'buttons',
            'sharing_button_position' => 'bottom',
            'enable_social_meta' => 1,
            'twitter_username' => '',
            'facebook_page_url' => '',
            'instagram_username' => '',
            'linkedin_company_id' => '',
            'pinterest_username' => '',
            'enable_follow_buttons' => 0,
            'follow_button_style' => 'icons'
        ];
    }
    
    /**
     * Get specific setting value with fallback
     */
    public function getSetting($section, $key, $default = null) {
        $method = 'getBlog' . ucfirst($section);
        if (method_exists($this, $method)) {
            $settings = $this->$method();
            return isset($settings[$key]) ? $settings[$key] : $default;
        }
        return $default;
    }
    
    /**
     * Check if a feature is enabled
     */
    public function isFeatureEnabled($feature) {
        $features = $this->getBlogFeatures();
        $key = 'enable_' . $feature;
        return isset($features[$key]) ? (bool)$features[$key] : false;
    }
    
    /**
     * Generate meta tags based on SEO settings
     */
    public function generateMetaTags($post = null) {
        $seo = $this->getBlogSeo();
        $identity = $this->getBlogIdentity();
        $social = $this->getBlogSocial();
        
        $metaTags = [];
        
        if ($seo['enable_meta_tags']) {
            // Basic meta tags
            if ($post) {
                $metaTags[] = '<meta name="description" content="' . htmlspecialchars($post['meta_description'] ?? $post['excerpt'] ?? $identity['meta_description']) . '">';
                $metaTags[] = '<meta name="keywords" content="' . htmlspecialchars($post['meta_keywords'] ?? $identity['meta_keywords']) . '">';
            } else {
                $metaTags[] = '<meta name="description" content="' . htmlspecialchars($identity['meta_description']) . '">';
                $metaTags[] = '<meta name="keywords" content="' . htmlspecialchars($identity['meta_keywords']) . '">';
            }
        }
        
        if ($seo['enable_open_graph'] && $social['enable_social_meta']) {
            // Open Graph tags
            $metaTags[] = '<meta property="og:site_name" content="' . htmlspecialchars($identity['blog_title']) . '">';
            if ($post) {
                $metaTags[] = '<meta property="og:title" content="' . htmlspecialchars($post['title']) . '">';
                $metaTags[] = '<meta property="og:description" content="' . htmlspecialchars($post['excerpt'] ?? $identity['meta_description']) . '">';
                $metaTags[] = '<meta property="og:type" content="article">';
                if (!empty($post['featured_image'])) {
                    $metaTags[] = '<meta property="og:image" content="' . htmlspecialchars($post['featured_image']) . '">';
                } elseif (!empty($seo['default_post_image'])) {
                    $metaTags[] = '<meta property="og:image" content="' . htmlspecialchars($seo['default_post_image']) . '">';
                }
            } else {
                $metaTags[] = '<meta property="og:title" content="' . htmlspecialchars($identity['blog_title']) . '">';
                $metaTags[] = '<meta property="og:description" content="' . htmlspecialchars($identity['blog_description']) . '">';
                $metaTags[] = '<meta property="og:type" content="website">';
            }
        }
        
        if ($seo['enable_twitter_cards']) {
            // Twitter Card tags
            $metaTags[] = '<meta name="twitter:card" content="summary_large_image">';
            if (!empty($social['twitter_username'])) {
                $metaTags[] = '<meta name="twitter:site" content="@' . htmlspecialchars($social['twitter_username']) . '">';
            }
        }
        
        return implode("\n", $metaTags);
    }
    
    /**
     * Generate social sharing buttons
     */
    public function generateSharingButtons($post = null, $url = null) {
        $social = $this->getBlogSocial();
        
        if (!$social['enable_sharing_buttons']) {
            return '';
        }
        
        $platforms = explode(',', $social['sharing_platforms']);
        $style = $social['sharing_button_style'];
        $url = $url ?: (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
        $title = $post ? $post['title'] : $this->getBlogIdentity()['blog_title'];
        
        $buttons = [];
        $buttons[] = '<div class="social-sharing-buttons sharing-style-' . $style . '">';
        
        foreach ($platforms as $platform) {
            $platform = trim($platform);
            switch ($platform) {
                case 'facebook':
                    $buttons[] = '<a href="https://www.facebook.com/sharer/sharer.php?u=' . urlencode($url) . '" target="_blank" class="share-btn facebook">Facebook</a>';
                    break;
                case 'twitter':
                    $buttons[] = '<a href="https://twitter.com/intent/tweet?url=' . urlencode($url) . '&text=' . urlencode($title) . '" target="_blank" class="share-btn twitter">Twitter</a>';
                    break;
                case 'linkedin':
                    $buttons[] = '<a href="https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($url) . '" target="_blank" class="share-btn linkedin">LinkedIn</a>';
                    break;
                case 'pinterest':
                    $image = $post['featured_image'] ?? '';
                    $buttons[] = '<a href="https://pinterest.com/pin/create/button/?url=' . urlencode($url) . '&media=' . urlencode($image) . '&description=' . urlencode($title) . '" target="_blank" class="share-btn pinterest">Pinterest</a>';
                    break;
                case 'whatsapp':
                    $buttons[] = '<a href="https://api.whatsapp.com/send?text=' . urlencode($title . ' ' . $url) . '" target="_blank" class="share-btn whatsapp">WhatsApp</a>';
                    break;
            }
        }
        
        $buttons[] = '</div>';
        return implode("\n", $buttons);
    }
    
    /**
     * Clear cache
     */
    public function clearCache() {
        $this->cache = [];
    }
}

// Global blog config instance
global $blogConfig;
if (!isset($blogConfig) && isset($pdo)) {
    $blogConfig = new BlogConfigReader($pdo);
}

/**
 * Helper functions for easy access
 */
function getBlogConfig($section = null) {
    global $blogConfig;
    if (!$blogConfig) return [];
    
    if ($section) {
        $method = 'getBlog' . ucfirst($section);
        return method_exists($blogConfig, $method) ? $blogConfig->$method() : [];
    }
    
    return $blogConfig->getAllBlogConfig();
}

function getBlogSetting($section, $key, $default = null) {
    global $blogConfig;
    return $blogConfig ? $blogConfig->getSetting($section, $key, $default) : $default;
}

function isBlogFeatureEnabled($feature) {
    global $blogConfig;
    return $blogConfig ? $blogConfig->isFeatureEnabled($feature) : false;
}

function generateBlogMetaTags($post = null) {
    global $blogConfig;
    return $blogConfig ? $blogConfig->generateMetaTags($post) : '';
}

function generateBlogSharingButtons($post = null, $url = null) {
    global $blogConfig;
    return $blogConfig ? $blogConfig->generateSharingButtons($post, $url) : '';
}
?>
