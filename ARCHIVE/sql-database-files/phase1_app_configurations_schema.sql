-- ===============================================================================
-- PHASE 1: APPLICATION CONFIGURATIONS DATABASE SCHEMA
-- ===============================================================================
-- 
-- This SQL file creates the unified configuration table for all applications
-- following the design outlined in the todo.php implementation plan.
-- 
-- Created: August 17, 2025
-- Purpose: Unified configuration management for GWS Universal Hybrid App
-- ===============================================================================

-- Create the main application configurations table
CREATE TABLE IF NOT EXISTS `setting_app_configurations` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `app_name` varchar(50) NOT NULL COMMENT 'Application identifier (blog_system, shop_system, etc.)',
    `section` varchar(100) NOT NULL COMMENT 'Configuration section (identity, display, security, etc.)',
    `config_key` varchar(100) NOT NULL COMMENT 'Configuration key name',
    `config_value` text DEFAULT NULL COMMENT 'Configuration value (JSON for complex data)',
    `data_type` enum('string','integer','boolean','json','array','float') NOT NULL DEFAULT 'string' COMMENT 'Data type for proper casting',
    `is_sensitive` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Whether this is sensitive data (passwords, API keys)',
    `description` text DEFAULT NULL COMMENT 'Human-readable description of the setting',
    `default_value` text DEFAULT NULL COMMENT 'Default value for the setting',
    `validation_rules` text DEFAULT NULL COMMENT 'JSON validation rules',
    `display_group` varchar(50) DEFAULT NULL COMMENT 'Admin UI grouping',
    `display_order` int(11) DEFAULT 0 COMMENT 'Order in admin interface',
    `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Whether setting is active',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `updated_by` varchar(100) DEFAULT NULL COMMENT 'User who last updated this setting',
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_app_config` (`app_name`, `section`, `config_key`),
    KEY `idx_app_section` (`app_name`, `section`),
    KEY `idx_display_group` (`display_group`),
    KEY `idx_sensitive` (`is_sensitive`),
    KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Unified application configuration storage';

-- Create audit trail table for configuration changes
CREATE TABLE IF NOT EXISTS `setting_app_configurations_audit` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `config_id` int(11) NOT NULL,
    `app_name` varchar(50) NOT NULL,
    `section` varchar(100) NOT NULL,
    `config_key` varchar(100) NOT NULL,
    `old_value` text DEFAULT NULL,
    `new_value` text DEFAULT NULL,
    `change_type` enum('CREATE','UPDATE','DELETE') NOT NULL,
    `changed_by` varchar(100) DEFAULT NULL,
    `change_reason` text DEFAULT NULL,
    `ip_address` varchar(45) DEFAULT NULL,
    `user_agent` text DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `idx_config_id` (`config_id`),
    KEY `idx_app_name` (`app_name`),
    KEY `idx_change_type` (`change_type`),
    KEY `idx_changed_by` (`changed_by`),
    KEY `idx_created_at` (`created_at`),
    CONSTRAINT `fk_config_audit` FOREIGN KEY (`config_id`) REFERENCES `setting_app_configurations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Audit trail for configuration changes';

-- Create configuration cache table for performance
CREATE TABLE IF NOT EXISTS `setting_app_configurations_cache` (
    `cache_key` varchar(255) NOT NULL,
    `app_name` varchar(50) NOT NULL,
    `cached_data` longtext NOT NULL COMMENT 'JSON cached configuration data',
    `expires_at` timestamp NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`cache_key`),
    KEY `idx_app_name` (`app_name`),
    KEY `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configuration cache for performance optimization';

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS `idx_config_lookup` ON `setting_app_configurations` (`app_name`, `section`, `config_key`, `is_active`);
CREATE INDEX IF NOT EXISTS `idx_admin_display` ON `setting_app_configurations` (`app_name`, `display_group`, `display_order`);

-- Create views for easier access
CREATE OR REPLACE VIEW `v_app_configurations` AS
SELECT 
    ac.id,
    ac.app_name,
    ac.section,
    ac.config_key,
    CASE 
        WHEN ac.is_sensitive = 1 THEN '***SENSITIVE***'
        ELSE ac.config_value 
    END AS display_value,
    ac.config_value,
    ac.data_type,
    ac.is_sensitive,
    ac.description,
    ac.default_value,
    ac.display_group,
    ac.display_order,
    ac.is_active,
    ac.updated_at,
    ac.updated_by
FROM setting_app_configurations ac
WHERE ac.is_active = 1
ORDER BY ac.app_name, ac.display_group, ac.display_order, ac.section, ac.config_key;

-- Create view for configuration summary
CREATE OR REPLACE VIEW `v_app_config_summary` AS
SELECT 
    app_name,
    COUNT(*) as total_settings,
    COUNT(CASE WHEN is_sensitive = 1 THEN 1 END) as sensitive_settings,
    COUNT(CASE WHEN config_value IS NULL OR config_value = '' THEN 1 END) as empty_settings,
    COUNT(DISTINCT section) as sections_count,
    COUNT(DISTINCT display_group) as groups_count,
    MAX(updated_at) as last_updated
FROM setting_app_configurations
WHERE is_active = 1
GROUP BY app_name
ORDER BY app_name;

-- ===============================================================================
-- INITIAL CONFIGURATION TEMPLATES
-- ===============================================================================

-- Insert default configuration structure for blog system
INSERT INTO `setting_app_configurations` 
(`app_name`, `section`, `config_key`, `config_value`, `data_type`, `description`, `default_value`, `display_group`, `display_order`, `is_sensitive`) 
VALUES
-- Blog Identity Settings
('blog_system', 'identity', 'blog_title', NULL, 'string', 'Main title of the blog', 'My Blog', 'Basic Info', 10, 0),
('blog_system', 'identity', 'blog_description', NULL, 'string', 'Blog description/tagline', 'Welcome to my blog', 'Basic Info', 20, 0),
('blog_system', 'identity', 'blog_site_url', NULL, 'string', 'Full URL to the blog', 'https://example.com/blog', 'Basic Info', 30, 0),
('blog_system', 'identity', 'author_name', NULL, 'string', 'Default author name', 'Blog Author', 'Basic Info', 40, 0),
('blog_system', 'identity', 'author_bio', NULL, 'string', 'Default author biography', '', 'Basic Info', 50, 0),
('blog_system', 'identity', 'email', NULL, 'string', 'Contact email for the blog', 'blog@example.com', 'Basic Info', 60, 0),

-- Blog Display Settings  
('blog_system', 'display', 'layout', NULL, 'string', 'Blog layout style', 'Wide', 'Appearance', 10, 0),
('blog_system', 'display', 'theme', NULL, 'string', 'Blog theme name', 'Default', 'Appearance', 20, 0),
('blog_system', 'display', 'sidebar_position', NULL, 'string', 'Position of sidebar', 'Right', 'Appearance', 30, 0),
('blog_system', 'display', 'posts_per_row', NULL, 'integer', 'Number of posts per row', '2', 'Appearance', 40, 0),
('blog_system', 'display', 'date_format', NULL, 'string', 'Date display format', 'F j, Y', 'Appearance', 50, 0),
('blog_system', 'display', 'latestposts_bar', NULL, 'string', 'Show latest posts bar', 'Enabled', 'Appearance', 60, 0),
('blog_system', 'display', 'background_image', NULL, 'string', 'Background image URL', '', 'Appearance', 70, 0),

-- Blog Functionality Settings
('blog_system', 'functionality', 'comments', NULL, 'string', 'Comment system settings', 'guests', 'Features', 10, 0),
('blog_system', 'functionality', 'rtl', NULL, 'string', 'Right-to-left text support', 'No', 'Features', 20, 0),
('blog_system', 'functionality', 'head_customcode', NULL, 'string', 'Custom HTML head code', '', 'Features', 30, 0),

-- Blog Social Media Settings
('blog_system', 'social', 'facebook', NULL, 'string', 'Facebook page URL', '', 'Social Media', 10, 0),
('blog_system', 'social', 'instagram', NULL, 'string', 'Instagram profile URL', '', 'Social Media', 20, 0),
('blog_system', 'social', 'twitter', NULL, 'string', 'Twitter profile URL', '', 'Social Media', 30, 0),
('blog_system', 'social', 'youtube', NULL, 'string', 'YouTube channel URL', '', 'Social Media', 40, 0),
('blog_system', 'social', 'linkedin', NULL, 'string', 'LinkedIn profile URL', '', 'Social Media', 50, 0),

-- Blog Security Settings
('blog_system', 'security', 'gcaptcha_sitekey', NULL, 'string', 'Google reCAPTCHA site key', '', 'Security', 10, 1),
('blog_system', 'security', 'gcaptcha_secretkey', NULL, 'string', 'Google reCAPTCHA secret key', '', 'Security', 20, 1),
('blog_system', 'security', 'gcaptcha_projectid', NULL, 'string', 'Google reCAPTCHA project ID', '', 'Security', 30, 1),

-- Shop System Configuration Template
('shop_system', 'basic', 'site_name', NULL, 'string', 'Shop website title', 'Shopping Cart', 'Basic Info', 10, 0),
('shop_system', 'basic', 'currency_code', NULL, 'string', 'Currency symbol/code', '&dollar;', 'Basic Info', 20, 0),
('shop_system', 'basic', 'weight_unit', NULL, 'string', 'Weight measurement unit', 'lbs', 'Basic Info', 30, 0),
('shop_system', 'basic', 'featured_image', NULL, 'string', 'Default featured image path', 'uploads/featured-image.jpg', 'Basic Info', 40, 0),
('shop_system', 'basic', 'base_url', NULL, 'string', 'Base URL of the shop', '', 'Basic Info', 50, 0),

-- Shop Functionality Settings
('shop_system', 'functionality', 'account_required', NULL, 'boolean', 'Require account for checkout', 'false', 'Features', 10, 0),
('shop_system', 'functionality', 'rewrite_url', NULL, 'boolean', 'Enable URL rewriting', 'false', 'Features', 20, 0),
('shop_system', 'functionality', 'template_editor', NULL, 'string', 'Template editor to use', 'tinymce', 'Features', 30, 0),
('shop_system', 'functionality', 'default_payment_status', NULL, 'string', 'Default payment status for new orders', 'Completed', 'Features', 40, 0),

-- Shop Mail Settings
('shop_system', 'mail', 'mail_enabled', NULL, 'boolean', 'Enable email notifications', 'false', 'Email', 10, 0),
('shop_system', 'mail', 'mail_from', NULL, 'string', 'From email address', 'noreply@example.com', 'Email', 20, 0),
('shop_system', 'mail', 'mail_name', NULL, 'string', 'From name for emails', 'Your Website/Business Name', 'Email', 30, 0),
('shop_system', 'mail', 'notifications_enabled', NULL, 'boolean', 'Enable admin notifications', 'true', 'Email', 40, 0),
('shop_system', 'mail', 'notification_email', NULL, 'string', 'Admin notification email', 'notifications@example.com', 'Email', 50, 0),

-- Shop SMTP Settings  
('shop_system', 'smtp', 'smtp_enabled', NULL, 'boolean', 'Use SMTP server', 'false', 'SMTP', 10, 0),
('shop_system', 'smtp', 'smtp_secure', NULL, 'string', 'SMTP security type', 'ssl', 'SMTP', 20, 0),
('shop_system', 'smtp', 'smtp_host', NULL, 'string', 'SMTP hostname', 'smtp.example.com', 'SMTP', 30, 1),
('shop_system', 'smtp', 'smtp_port', NULL, 'integer', 'SMTP port number', '465', 'SMTP', 40, 0),
('shop_system', 'smtp', 'smtp_username', NULL, 'string', 'SMTP username', '', 'SMTP', 50, 1),
('shop_system', 'smtp', 'smtp_password', NULL, 'string', 'SMTP password', '', 'SMTP', 60, 1),

-- Shop Security Settings
('shop_system', 'security', 'secret_key', NULL, 'string', 'Secret key for password resets', 'YOUR_SECRET_KEY', 'Security', 10, 1),

-- Accounts System Template (for future use)
('accounts_system', 'registration', 'enabled', NULL, 'boolean', 'Allow new user registration', 'true', 'Registration', 10, 0),
('accounts_system', 'registration', 'email_verification', NULL, 'boolean', 'Require email verification', 'true', 'Registration', 20, 0),
('accounts_system', 'registration', 'admin_approval', NULL, 'boolean', 'Require admin approval', 'false', 'Registration', 30, 0),
('accounts_system', 'registration', 'default_role', NULL, 'string', 'Default role for new users', 'user', 'Registration', 40, 0),

('accounts_system', 'security', 'password_min_length', NULL, 'integer', 'Minimum password length', '8', 'Security', 10, 0),
('accounts_system', 'security', 'session_timeout', NULL, 'integer', 'Session timeout in minutes', '1440', 'Security', 20, 0),
('accounts_system', 'security', 'max_login_attempts', NULL, 'integer', 'Maximum login attempts before lockout', '5', 'Security', 30, 0),
('accounts_system', 'security', 'lockout_duration', NULL, 'integer', 'Account lockout duration in minutes', '30', 'Security', 40, 0);

-- ===============================================================================
-- INITIAL DATA VERIFICATION
-- ===============================================================================

-- Show configuration summary
SELECT 'Configuration Summary:' as status;
SELECT 
    app_name,
    COUNT(*) as total_settings,
    COUNT(DISTINCT section) as sections,
    COUNT(DISTINCT display_group) as groups
FROM setting_app_configurations 
GROUP BY app_name 
ORDER BY app_name;

-- Show table structure verification
SELECT 'Schema Created Successfully' as status;
SHOW TABLES LIKE 'setting_app_configurations%';
