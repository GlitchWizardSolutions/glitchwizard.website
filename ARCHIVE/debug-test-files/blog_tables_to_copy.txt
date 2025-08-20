-- ===============================================================================
-- PHASE 2: BLOG SYSTEM DATABASE TABLES CREATION
-- ===============================================================================
-- 
-- This SQL file creates all specialized blog system tables for complete
-- blog configuration management through the admin dashboard system.
-- 
-- Created: August 17, 2025
-- Purpose: Blog system database integration for unified admin management
-- ===============================================================================

-- Disable foreign key checks for table creation
SET FOREIGN_KEY_CHECKS = 0;

-- ===============================================================================
-- BLOG IDENTITY TABLE: Basic Blog Information
-- ===============================================================================

CREATE TABLE IF NOT EXISTS `setting_blog_identity` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `blog_title` varchar(255) NOT NULL DEFAULT 'My Blog',
    `blog_description` text DEFAULT 'Welcome to my blog',
    `blog_tagline` varchar(255) DEFAULT 'Sharing thoughts and ideas',
    `author_name` varchar(255) DEFAULT 'Blog Author',
    `author_bio` text DEFAULT 'About the author',
    `default_author_id` int(11) DEFAULT 1,
    `meta_description` text DEFAULT 'Blog meta description',
    `meta_keywords` text DEFAULT 'blog, content, articles',
    `blog_email` varchar(255) DEFAULT '',
    `blog_url` varchar(255) DEFAULT '',
    `copyright_text` varchar(255) DEFAULT '',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default blog identity settings
INSERT IGNORE INTO `setting_blog_identity` (
    `blog_title`, `blog_description`, `blog_tagline`, `author_name`, `author_bio`,
    `default_author_id`, `meta_description`, `meta_keywords`, `blog_email`,
    `blog_url`, `copyright_text`
) VALUES (
    'My Blog',
    'Welcome to my blog where I share thoughts and ideas',
    'Sharing thoughts and ideas',
    'Blog Author',
    'About the author - passionate about sharing knowledge and experiences',
    1,
    'A blog featuring articles, insights, and thoughts on various topics',
    'blog, articles, insights, thoughts, content',
    'blog@example.com',
    'https://example.com/blog',
    '© 2025 My Blog. All rights reserved.'
);

-- ===============================================================================
-- BLOG DISPLAY TABLE: Layout and Appearance Settings
-- ===============================================================================

CREATE TABLE IF NOT EXISTS `setting_blog_display` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `posts_per_page` int(11) DEFAULT 10,
    `excerpt_length` int(11) DEFAULT 250,
    `date_format` varchar(50) DEFAULT 'F j, Y',
    `layout` enum('Wide','Boxed','Sidebar') DEFAULT 'Wide',
    `sidebar_position` enum('Left','Right','None') DEFAULT 'Right',
    `posts_per_row` int(11) DEFAULT 2,
    `theme` varchar(100) DEFAULT 'Default',
    `enable_featured_image` tinyint(1) DEFAULT 1,
    `thumbnail_width` int(11) DEFAULT 300,
    `thumbnail_height` int(11) DEFAULT 200,
    `background_image` text DEFAULT '',
    `custom_css` text DEFAULT '',
    `show_author` tinyint(1) DEFAULT 1,
    `show_date` tinyint(1) DEFAULT 1,
    `show_categories` tinyint(1) DEFAULT 1,
    `show_tags` tinyint(1) DEFAULT 1,
    `show_excerpt` tinyint(1) DEFAULT 1,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default blog display settings
INSERT IGNORE INTO `setting_blog_display` (
    `posts_per_page`, `excerpt_length`, `date_format`, `layout`, `sidebar_position`,
    `posts_per_row`, `theme`, `enable_featured_image`, `thumbnail_width`, `thumbnail_height`,
    `background_image`, `custom_css`, `show_author`, `show_date`, `show_categories`,
    `show_tags`, `show_excerpt`
) VALUES (
    10, 250, 'F j, Y', 'Wide', 'Right',
    2, 'Default', 1, 300, 200,
    '', '', 1, 1, 1,
    1, 1
);

-- ===============================================================================
-- BLOG FEATURES TABLE: Functionality Toggles
-- ===============================================================================

CREATE TABLE IF NOT EXISTS `setting_blog_features` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `enable_posts` tinyint(1) DEFAULT 1,
    `enable_pages` tinyint(1) DEFAULT 1,
    `enable_categories` tinyint(1) DEFAULT 1,
    `enable_tags` tinyint(1) DEFAULT 1,
    `enable_comments` tinyint(1) DEFAULT 1,
    `enable_author_bio` tinyint(1) DEFAULT 1,
    `enable_social_sharing` tinyint(1) DEFAULT 1,
    `enable_related_posts` tinyint(1) DEFAULT 1,
    `enable_search` tinyint(1) DEFAULT 1,
    `enable_archives` tinyint(1) DEFAULT 1,
    `enable_rss` tinyint(1) DEFAULT 1,
    `enable_sitemap` tinyint(1) DEFAULT 1,
    `enable_breadcrumbs` tinyint(1) DEFAULT 1,
    `enable_post_navigation` tinyint(1) DEFAULT 1,
    `enable_reading_time` tinyint(1) DEFAULT 1,
    `enable_post_views` tinyint(1) DEFAULT 1,
    `enable_newsletter_signup` tinyint(1) DEFAULT 0,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default blog features settings
INSERT IGNORE INTO `setting_blog_features` (
    `enable_posts`, `enable_pages`, `enable_categories`, `enable_tags`, `enable_comments`,
    `enable_author_bio`, `enable_social_sharing`, `enable_related_posts`, `enable_search`,
    `enable_archives`, `enable_rss`, `enable_sitemap`, `enable_breadcrumbs`,
    `enable_post_navigation`, `enable_reading_time`, `enable_post_views`, `enable_newsletter_signup`
) VALUES (
    1, 1, 1, 1, 1,
    1, 1, 1, 1,
    1, 1, 1, 1,
    1, 1, 1, 0
);

-- ===============================================================================
-- BLOG COMMENTS TABLE: Comment System Configuration
-- ===============================================================================

CREATE TABLE IF NOT EXISTS `setting_blog_comments` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `comment_system` enum('internal','disqus','facebook','disabled') DEFAULT 'internal',
    `require_approval` tinyint(1) DEFAULT 1,
    `allow_guest_comments` tinyint(1) DEFAULT 1,
    `require_registration` tinyint(1) DEFAULT 0,
    `max_comment_length` int(11) DEFAULT 1000,
    `enable_notifications` tinyint(1) DEFAULT 1,
    `notification_email` varchar(255) DEFAULT 'admin@example.com',
    `enable_threading` tinyint(1) DEFAULT 1,
    `max_thread_depth` int(11) DEFAULT 3,
    `enable_comment_voting` tinyint(1) DEFAULT 0,
    `enable_comment_editing` tinyint(1) DEFAULT 1,
    `comment_edit_time_limit` int(11) DEFAULT 300,
    `enable_comment_deletion` tinyint(1) DEFAULT 1,
    `enable_spam_protection` tinyint(1) DEFAULT 1,
    `disqus_shortname` varchar(255) DEFAULT '',
    `facebook_app_id` varchar(255) DEFAULT '',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default blog comments settings
INSERT IGNORE INTO `setting_blog_comments` (
    `comment_system`, `require_approval`, `allow_guest_comments`, `require_registration`,
    `max_comment_length`, `enable_notifications`, `notification_email`, `enable_threading`,
    `max_thread_depth`, `enable_comment_voting`, `enable_comment_editing`, `comment_edit_time_limit`,
    `enable_comment_deletion`, `enable_spam_protection`, `disqus_shortname`, `facebook_app_id`
) VALUES (
    'internal', 1, 1, 0,
    1000, 1, 'admin@example.com', 1,
    3, 0, 1, 300,
    1, 1, '', ''
);

-- ===============================================================================
-- BLOG SEO TABLE: Search Engine Optimization Settings
-- ===============================================================================

CREATE TABLE IF NOT EXISTS `setting_blog_seo` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `enable_seo_urls` tinyint(1) DEFAULT 1,
    `post_url_structure` varchar(255) DEFAULT '{year}/{month}/{slug}',
    `enable_meta_tags` tinyint(1) DEFAULT 1,
    `enable_open_graph` tinyint(1) DEFAULT 1,
    `enable_twitter_cards` tinyint(1) DEFAULT 1,
    `default_post_image` text DEFAULT '',
    `robots_txt_additions` text DEFAULT '',
    `sitemap_frequency` varchar(20) DEFAULT 'weekly',
    `sitemap_priority` decimal(2,1) DEFAULT 0.8,
    `enable_canonical_urls` tinyint(1) DEFAULT 1,
    `enable_schema_markup` tinyint(1) DEFAULT 1,
    `google_analytics_id` varchar(255) DEFAULT '',
    `google_site_verification` varchar(255) DEFAULT '',
    `bing_site_verification` varchar(255) DEFAULT '',
    `enable_breadcrumb_schema` tinyint(1) DEFAULT 1,
    `enable_article_schema` tinyint(1) DEFAULT 1,
    `default_meta_description` text DEFAULT '',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default blog SEO settings
INSERT IGNORE INTO `setting_blog_seo` (
    `enable_seo_urls`, `post_url_structure`, `enable_meta_tags`, `enable_open_graph`,
    `enable_twitter_cards`, `default_post_image`, `robots_txt_additions`, `sitemap_frequency`,
    `sitemap_priority`, `enable_canonical_urls`, `enable_schema_markup`, `google_analytics_id`,
    `google_site_verification`, `bing_site_verification`, `enable_breadcrumb_schema`,
    `enable_article_schema`, `default_meta_description`
) VALUES (
    1, '{year}/{month}/{slug}', 1, 1,
    1, '', '', 'weekly',
    0.8, 1, 1, '',
    '', '', 1,
    1, 'Discover engaging content and insights on our blog covering various topics and interests.'
);

-- ===============================================================================
-- BLOG SOCIAL TABLE: Social Media Integration Settings
-- ===============================================================================

CREATE TABLE IF NOT EXISTS `setting_blog_social` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `facebook_url` varchar(255) DEFAULT '',
    `twitter_url` varchar(255) DEFAULT '',
    `instagram_url` varchar(255) DEFAULT '',
    `linkedin_url` varchar(255) DEFAULT '',
    `youtube_url` varchar(255) DEFAULT '',
    `pinterest_url` varchar(255) DEFAULT '',
    `github_url` varchar(255) DEFAULT '',
    `enable_facebook_sharing` tinyint(1) DEFAULT 1,
    `enable_twitter_sharing` tinyint(1) DEFAULT 1,
    `enable_linkedin_sharing` tinyint(1) DEFAULT 1,
    `enable_pinterest_sharing` tinyint(1) DEFAULT 0,
    `enable_email_sharing` tinyint(1) DEFAULT 1,
    `enable_whatsapp_sharing` tinyint(1) DEFAULT 1,
    `enable_reddit_sharing` tinyint(1) DEFAULT 0,
    `twitter_username` varchar(255) DEFAULT '',
    `facebook_app_id` varchar(255) DEFAULT '',
    `social_sharing_position` enum('top','bottom','both','floating') DEFAULT 'bottom',
    `enable_social_login` tinyint(1) DEFAULT 0,
    `facebook_login_enabled` tinyint(1) DEFAULT 0,
    `google_login_enabled` tinyint(1) DEFAULT 0,
    `twitter_login_enabled` tinyint(1) DEFAULT 0,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default blog social settings
INSERT IGNORE INTO `setting_blog_social` (
    `facebook_url`, `twitter_url`, `instagram_url`, `linkedin_url`, `youtube_url`,
    `pinterest_url`, `github_url`, `enable_facebook_sharing`, `enable_twitter_sharing`,
    `enable_linkedin_sharing`, `enable_pinterest_sharing`, `enable_email_sharing`,
    `enable_whatsapp_sharing`, `enable_reddit_sharing`, `twitter_username`, `facebook_app_id`,
    `social_sharing_position`, `enable_social_login`, `facebook_login_enabled`,
    `google_login_enabled`, `twitter_login_enabled`
) VALUES (
    '', '', '', '', '',
    '', '', 1, 1,
    1, 0, 1,
    1, 0, '', '',
    'bottom', 0, 0,
    0, 0
);

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- ===============================================================================
-- BLOG TABLES VERIFICATION
-- ===============================================================================

-- Show created tables summary
SELECT 'Blog System Tables Created Successfully:' as status;

-- Verify all blog tables exist using SHOW TABLES
SHOW TABLES LIKE 'setting_blog_%';

-- Check table structures
DESCRIBE setting_blog_identity;
DESCRIBE setting_blog_display;
DESCRIBE setting_blog_features;
DESCRIBE setting_blog_comments;
DESCRIBE setting_blog_seo;
DESCRIBE setting_blog_social;

-- Show blog identity settings
SELECT 'Blog Identity Settings:' as section, blog_title, blog_description, author_name FROM setting_blog_identity LIMIT 1;

-- Show blog display settings  
SELECT 'Blog Display Settings:' as section, posts_per_page, layout, theme FROM setting_blog_display LIMIT 1;

-- Show blog features status
SELECT 'Blog Features Status:' as section, 
    enable_posts, enable_comments, enable_social_sharing, enable_search 
FROM setting_blog_features LIMIT 1;

-- Show blog comments configuration
SELECT 'Blog Comments Config:' as section, 
    comment_system, require_approval, max_comment_length 
FROM setting_blog_comments LIMIT 1;

-- Show blog SEO configuration
SELECT 'Blog SEO Config:' as section, 
    enable_seo_urls, enable_meta_tags, sitemap_frequency 
FROM setting_blog_seo LIMIT 1;

-- Show blog social configuration
SELECT 'Blog Social Config:' as section, 
    enable_facebook_sharing, enable_twitter_sharing, social_sharing_position 
FROM setting_blog_social LIMIT 1;

-- Count records in each table
SELECT 'Record Counts:' as status;
SELECT 'setting_blog_identity' as table_name, COUNT(*) as records FROM setting_blog_identity
UNION ALL
SELECT 'setting_blog_display' as table_name, COUNT(*) as records FROM setting_blog_display
UNION ALL
SELECT 'setting_blog_features' as table_name, COUNT(*) as records FROM setting_blog_features
UNION ALL
SELECT 'setting_blog_comments' as table_name, COUNT(*) as records FROM setting_blog_comments
UNION ALL
SELECT 'setting_blog_seo' as table_name, COUNT(*) as records FROM setting_blog_seo
UNION ALL
SELECT 'setting_blog_social' as table_name, COUNT(*) as records FROM setting_blog_social;

SELECT 'Blog system database tables ready for admin integration!' as status;
