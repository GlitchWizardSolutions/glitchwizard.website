<?php
/**
 * BLOG SYSTEM CONFIGURATION
 * Template file for blog system settings
 * 
 * This file serves as a template for the blog system configuration.
 * Do not modify this template. Instead, copy it to blog-config.php
 * and make changes there.
 */

// Prevent direct access to this file
if (!defined('PROJECT_ROOT')) {
    die('Direct access to this file is not allowed');
}

// Blog System Settings
$blog_settings = [
    // Basic Settings
    'blog_title' => 'My Blog',
    'posts_per_page' => 10,
    'enable_comments' => true,
    
    // Content Settings
    'excerpt_length' => 250,
    'allowed_tags' => '<p><br><a><strong><em><ul><li>',
    
    // Feature Toggles
    'enable_categories' => true,
    'enable_tags' => true,
    'enable_author_bio' => true,
    
    // File Upload Settings
    'max_upload_size' => '5MB',
    'allowed_file_types' => ['jpg', 'jpeg', 'png', 'gif'],
    
    // SEO Settings
    'default_meta_description' => '',
    'default_meta_keywords' => '',
    
    // Advanced Settings
    'cache_enabled' => false,
    'cache_duration' => 3600, // 1 hour
];

// Blog Paths Configuration
$blog_paths = [
    'uploads' => PROJECT_ROOT . '/public_html/blog_system/assets/uploads',
    'templates' => PROJECT_ROOT . '/public_html/blog_system/templates',
    'cache' => PROJECT_ROOT . '/public_html/blog_system/cache'
];

// Return the configuration
return [
    'settings' => $blog_settings,
    'paths' => $blog_paths
];
