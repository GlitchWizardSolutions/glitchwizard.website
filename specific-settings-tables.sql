-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 19, 2025 at 01:09 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gws_universal_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `settings_status`
--

CREATE TABLE `settings_status` (
  `id` int(11) NOT NULL,
  `settings_file` varchar(100) NOT NULL,
  `section_name` varchar(100) DEFAULT NULL,
  `setting_key` varchar(100) DEFAULT NULL,
  `is_configured` tinyint(1) DEFAULT 0,
  `is_complete` tinyint(1) DEFAULT 0,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_accounts_config`
--

CREATE TABLE `setting_accounts_config` (
  `id` int(11) NOT NULL,
  `registration_enabled` tinyint(1) DEFAULT 1,
  `email_verification_required` tinyint(1) DEFAULT 1,
  `admin_approval_required` tinyint(1) DEFAULT 0,
  `username_min_length` int(11) DEFAULT 4,
  `username_max_length` int(11) DEFAULT 50,
  `password_min_length` int(11) DEFAULT 8,
  `password_require_special` tinyint(1) DEFAULT 1,
  `password_require_uppercase` tinyint(1) DEFAULT 1,
  `password_require_lowercase` tinyint(1) DEFAULT 1,
  `password_require_numbers` tinyint(1) DEFAULT 1,
  `max_login_attempts` int(11) DEFAULT 5,
  `lockout_duration` int(11) DEFAULT 900,
  `session_lifetime` int(11) DEFAULT 3600,
  `remember_me_enabled` tinyint(1) DEFAULT 1,
  `remember_duration` int(11) DEFAULT 2592000,
  `two_factor_enabled` tinyint(1) DEFAULT 0,
  `profile_pictures_enabled` tinyint(1) DEFAULT 1,
  `profile_picture_max_size` int(11) DEFAULT 2097152,
  `allowed_image_types` varchar(255) DEFAULT 'jpg,jpeg,png,gif',
  `default_role` varchar(50) DEFAULT 'Member',
  `welcome_email_enabled` tinyint(1) DEFAULT 1,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_analytics_config`
--

CREATE TABLE `setting_analytics_config` (
  `id` int(11) NOT NULL,
  `google_analytics_enabled` tinyint(1) DEFAULT 0,
  `google_analytics_id` varchar(50) DEFAULT NULL,
  `google_tag_manager_enabled` tinyint(1) DEFAULT 0,
  `google_tag_manager_id` varchar(50) DEFAULT NULL,
  `facebook_pixel_enabled` tinyint(1) DEFAULT 0,
  `facebook_pixel_id` varchar(50) DEFAULT NULL,
  `hotjar_enabled` tinyint(1) DEFAULT 0,
  `hotjar_id` varchar(50) DEFAULT NULL,
  `custom_analytics_code` text DEFAULT NULL,
  `internal_analytics_enabled` tinyint(1) DEFAULT 1,
  `page_view_tracking` tinyint(1) DEFAULT 1,
  `event_tracking` tinyint(1) DEFAULT 1,
  `user_behavior_tracking` tinyint(1) DEFAULT 1,
  `conversion_tracking` tinyint(1) DEFAULT 1,
  `bounce_rate_tracking` tinyint(1) DEFAULT 1,
  `session_recording` tinyint(1) DEFAULT 0,
  `heatmap_tracking` tinyint(1) DEFAULT 0,
  `a_b_testing_enabled` tinyint(1) DEFAULT 0,
  `data_retention_days` int(11) DEFAULT 365,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_app_configurations`
--

CREATE TABLE `setting_app_configurations` (
  `id` int(11) NOT NULL,
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
  `updated_by` varchar(100) DEFAULT NULL COMMENT 'User who last updated this setting'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Unified application configuration storage';

--
-- Dumping data for table `setting_app_configurations`
--

INSERT INTO `setting_app_configurations` (`id`, `app_name`, `section`, `config_key`, `config_value`, `data_type`, `is_sensitive`, `description`, `default_value`, `validation_rules`, `display_group`, `display_order`, `is_active`, `created_at`, `updated_at`, `updated_by`) VALUES
(1, 'blog_system', 'identity', 'blog_title', 'GlitchWizard Solutions Blog', 'string', 0, 'Main title of the blog', 'My Blog', NULL, 'Basic Info', 10, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(2, 'blog_system', 'identity', 'blog_description', 'Information for the Members of GlitchWizard Solutions LLC', 'string', 0, 'Blog description/tagline', 'Welcome to my blog', NULL, 'Basic Info', 20, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(3, 'blog_system', 'identity', 'blog_site_url', 'https://glitchwizarddigitalsolutions.com/blog', 'string', 0, 'Full URL to the blog', 'https://example.com/blog', NULL, 'Basic Info', 30, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(4, 'blog_system', 'identity', 'author_name', NULL, 'string', 0, 'Default author name', 'Blog Author', NULL, 'Basic Info', 40, 1, '2025-08-17 17:27:27', '2025-08-17 17:27:27', NULL),
(5, 'blog_system', 'identity', 'author_bio', NULL, 'string', 0, 'Default author biography', '', NULL, 'Basic Info', 50, 1, '2025-08-17 17:27:27', '2025-08-17 17:27:27', NULL),
(6, 'blog_system', 'identity', 'email', 'barbara@glitchwizarddigitalsolutions.com', 'string', 0, 'Contact email for the blog', 'blog@example.com', NULL, 'Basic Info', 60, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(7, 'blog_system', 'display', 'layout', 'Wide', 'string', 0, 'Blog layout style', 'Wide', NULL, 'Appearance', 10, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(8, 'blog_system', 'display', 'theme', 'Pulse', 'string', 0, 'Blog theme name', 'Default', NULL, 'Appearance', 20, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(9, 'blog_system', 'display', 'sidebar_position', 'Right', 'string', 0, 'Position of sidebar', 'Right', NULL, 'Appearance', 30, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(10, 'blog_system', 'display', 'posts_per_row', '2', 'integer', 0, 'Number of posts per row', '2', NULL, 'Appearance', 40, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(11, 'blog_system', 'display', 'date_format', 'F j, Y', 'string', 0, 'Date display format', 'F j, Y', NULL, 'Appearance', 50, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(12, 'blog_system', 'display', 'latestposts_bar', 'Enabled', 'string', 0, 'Show latest posts bar', 'Enabled', NULL, 'Appearance', 60, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(13, 'blog_system', 'display', 'background_image', '', 'string', 0, 'Background image URL', '', NULL, 'Appearance', 70, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(14, 'blog_system', 'functionality', 'comments', 'guests', 'string', 0, 'Comment system settings', 'guests', NULL, 'Features', 10, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(15, 'blog_system', 'functionality', 'rtl', 'No', 'string', 0, 'Right-to-left text support', 'No', NULL, 'Features', 20, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(16, 'blog_system', 'functionality', 'head_customcode', '', 'string', 0, 'Custom HTML head code', '', NULL, 'Features', 30, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(17, 'blog_system', 'social', 'facebook', 'https://www.facebook.com/GlitchWizardSolutions/', 'string', 0, 'Facebook page URL', '', NULL, 'Social Media', 10, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(18, 'blog_system', 'social', 'instagram', '', 'string', 0, 'Instagram profile URL', '', NULL, 'Social Media', 20, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(19, 'blog_system', 'social', 'twitter', '', 'string', 0, 'Twitter profile URL', '', NULL, 'Social Media', 30, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(20, 'blog_system', 'social', 'youtube', '', 'string', 0, 'YouTube channel URL', '', NULL, 'Social Media', 40, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(21, 'blog_system', 'social', 'linkedin', 'https://www.linkedin.com/in/glitchwizard/', 'string', 0, 'LinkedIn profile URL', '', NULL, 'Social Media', 50, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(22, 'blog_system', 'security', 'gcaptcha_sitekey', '6LdmAmgrAAAAAIdsJeCLDjkPhYeVZIH6wSGqkxIH', 'string', 1, 'Google reCAPTCHA site key', '', NULL, 'Security', 10, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(23, 'blog_system', 'security', 'gcaptcha_secretkey', '6LdmAmgrAAAAAKXJibD69CmlnsUP5sQFIQImwODW', 'string', 1, 'Google reCAPTCHA secret key', '', NULL, 'Security', 20, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(24, 'blog_system', 'security', 'gcaptcha_projectid', 'glitchwizardsolu-1696110549072', 'string', 1, 'Google reCAPTCHA project ID', '', NULL, 'Security', 30, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(25, 'shop_system', 'basic', 'site_name', 'Shopping Cart', 'string', 0, 'Shop website title', 'Shopping Cart', NULL, 'Basic Info', 10, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(26, 'shop_system', 'basic', 'currency_code', '&dollar;', 'string', 0, 'Currency symbol/code', '&dollar;', NULL, 'Basic Info', 20, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(27, 'shop_system', 'basic', 'weight_unit', 'lbs', 'string', 0, 'Weight measurement unit', 'lbs', NULL, 'Basic Info', 30, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(28, 'shop_system', 'basic', 'featured_image', 'uploads/featured-image.jpg', 'string', 0, 'Default featured image path', 'uploads/featured-image.jpg', NULL, 'Basic Info', 40, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(29, 'shop_system', 'basic', 'base_url', 'http://yourdomain.com/shoppingcart/', 'string', 0, 'Base URL of the shop', '', NULL, 'Basic Info', 50, 1, '2025-08-17 17:27:27', '2025-08-17 19:16:31', 'enhanced_migration'),
(30, 'shop_system', 'functionality', 'account_required', 'false', 'boolean', 0, 'Require account for checkout', 'false', NULL, 'Features', 10, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(31, 'shop_system', 'functionality', 'rewrite_url', 'false', 'boolean', 0, 'Enable URL rewriting', 'false', NULL, 'Features', 20, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(32, 'shop_system', 'functionality', 'template_editor', 'tinymce', 'string', 0, 'Template editor to use', 'tinymce', NULL, 'Features', 30, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(33, 'shop_system', 'functionality', 'default_payment_status', 'Completed', 'string', 0, 'Default payment status for new orders', 'Completed', NULL, 'Features', 40, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(34, 'shop_system', 'mail', 'mail_enabled', 'false', 'boolean', 0, 'Enable email notifications', 'false', NULL, 'Email', 10, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(35, 'shop_system', 'mail', 'mail_from', 'noreply@example.com', 'string', 0, 'From email address', 'noreply@example.com', NULL, 'Email', 20, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(36, 'shop_system', 'mail', 'mail_name', 'Your Website/Business Name', 'string', 0, 'From name for emails', 'Your Website/Business Name', NULL, 'Email', 30, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(37, 'shop_system', 'mail', 'notifications_enabled', 'true', 'boolean', 0, 'Enable admin notifications', 'true', NULL, 'Email', 40, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(38, 'shop_system', 'mail', 'notification_email', 'notifications@example.com', 'string', 0, 'Admin notification email', 'notifications@example.com', NULL, 'Email', 50, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(39, 'shop_system', 'smtp', 'smtp_enabled', 'false', 'boolean', 0, 'Use SMTP server', 'false', NULL, 'SMTP', 10, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(40, 'shop_system', 'smtp', 'smtp_secure', 'ssl', 'string', 0, 'SMTP security type', 'ssl', NULL, 'SMTP', 20, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(41, 'shop_system', 'smtp', 'smtp_host', 'smtp.example.com', 'string', 1, 'SMTP hostname', 'smtp.example.com', NULL, 'SMTP', 30, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(42, 'shop_system', 'smtp', 'smtp_port', '465', 'integer', 0, 'SMTP port number', '465', NULL, 'SMTP', 40, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(43, 'shop_system', 'smtp', 'smtp_username', 'user@example.com', 'string', 1, 'SMTP username', '', NULL, 'SMTP', 50, 1, '2025-08-17 17:27:27', '2025-08-17 19:16:31', 'enhanced_migration'),
(44, 'shop_system', 'smtp', 'smtp_password', 'secret', 'string', 1, 'SMTP password', '', NULL, 'SMTP', 60, 1, '2025-08-17 17:27:27', '2025-08-17 19:16:31', 'enhanced_migration'),
(45, 'shop_system', 'security', 'secret_key', 'YOUR_SECRET_KEY', 'string', 1, 'Secret key for password resets', 'YOUR_SECRET_KEY', NULL, 'Security', 10, 1, '2025-08-17 17:27:27', '2025-08-17 17:37:14', 'migration_script'),
(46, 'accounts_system', 'registration', 'enabled', NULL, 'boolean', 0, 'Allow new user registration', 'true', NULL, 'Registration', 10, 1, '2025-08-17 17:27:27', '2025-08-17 17:27:27', NULL),
(47, 'accounts_system', 'registration', 'email_verification', NULL, 'boolean', 0, 'Require email verification', 'true', NULL, 'Registration', 20, 1, '2025-08-17 17:27:27', '2025-08-17 17:27:27', NULL),
(48, 'accounts_system', 'registration', 'admin_approval', NULL, 'boolean', 0, 'Require admin approval', 'false', NULL, 'Registration', 30, 1, '2025-08-17 17:27:27', '2025-08-17 17:27:27', NULL),
(49, 'accounts_system', 'registration', 'default_role', NULL, 'string', 0, 'Default role for new users', 'user', NULL, 'Registration', 40, 1, '2025-08-17 17:27:27', '2025-08-17 17:27:27', NULL),
(50, 'accounts_system', 'security', 'password_min_length', NULL, 'integer', 0, 'Minimum password length', '8', NULL, 'Security', 10, 1, '2025-08-17 17:27:27', '2025-08-17 17:27:27', NULL),
(51, 'accounts_system', 'security', 'session_timeout', NULL, 'integer', 0, 'Session timeout in minutes', '1440', NULL, 'Security', 20, 1, '2025-08-17 17:27:27', '2025-08-17 17:27:27', NULL),
(52, 'accounts_system', 'security', 'max_login_attempts', NULL, 'integer', 0, 'Maximum login attempts before lockout', '5', NULL, 'Security', 30, 1, '2025-08-17 17:27:27', '2025-08-17 17:27:27', NULL),
(53, 'accounts_system', 'security', 'lockout_duration', NULL, 'integer', 0, 'Account lockout duration in minutes', '30', NULL, 'Security', 40, 1, '2025-08-17 17:27:27', '2025-08-17 17:27:27', NULL),
(79, 'shop_system', 'payment_paypal', 'paypal_enabled', 'true', 'boolean', 0, 'Enable PayPal payments', 'true', NULL, 'PayPal', 10, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(80, 'shop_system', 'payment_paypal', 'paypal_email', 'payments@example.com', 'string', 0, 'PayPal business email account', 'payments@example.com', NULL, 'PayPal', 20, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(81, 'shop_system', 'payment_paypal', 'paypal_testmode', 'true', 'boolean', 0, 'Enable PayPal sandbox/test mode', 'true', NULL, 'PayPal', 30, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(82, 'shop_system', 'payment_paypal', 'paypal_currency', 'USD', 'string', 0, 'PayPal currency code', 'USD', NULL, 'PayPal', 40, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(83, 'shop_system', 'payment_paypal', 'paypal_ipn_url', 'https://example.com/ipn/paypal.php', 'string', 0, 'PayPal IPN notification URL', 'https://example.com/ipn/paypal.php', NULL, 'PayPal', 50, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(84, 'shop_system', 'payment_paypal', 'paypal_cancel_url', 'https://example.com/index.php?page=cart', 'string', 0, 'PayPal payment cancellation URL', 'https://example.com/index.php?page=cart', NULL, 'PayPal', 60, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(85, 'shop_system', 'payment_paypal', 'paypal_return_url', 'https://example.com/index.php?page=placeorder', 'string', 0, 'PayPal payment success return URL', 'https://example.com/index.php?page=placeorder', NULL, 'PayPal', 70, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(86, 'shop_system', 'payment_stripe', 'stripe_enabled', 'true', 'boolean', 0, 'Enable Stripe payments', 'true', NULL, 'Stripe', 10, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(87, 'shop_system', 'payment_stripe', 'stripe_publish_key', '', 'string', 1, 'Stripe publishable API key', '', NULL, 'Stripe', 20, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(88, 'shop_system', 'payment_stripe', 'stripe_secret_key', '', 'string', 1, 'Stripe secret API key', '', NULL, 'Stripe', 30, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(89, 'shop_system', 'payment_stripe', 'stripe_currency', 'USD', 'string', 0, 'Stripe currency code', 'USD', NULL, 'Stripe', 40, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(90, 'shop_system', 'payment_stripe', 'stripe_ipn_url', 'https://example.com/ipn/stripe.php', 'string', 0, 'Stripe webhook URL', 'https://example.com/ipn/stripe.php', NULL, 'Stripe', 50, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(91, 'shop_system', 'payment_stripe', 'stripe_cancel_url', 'https://example.com/index.php?page=cart', 'string', 0, 'Stripe payment cancellation URL', 'https://example.com/index.php?page=cart', NULL, 'Stripe', 60, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(92, 'shop_system', 'payment_stripe', 'stripe_return_url', 'https://example.com/index.php?page=placeorder', 'string', 0, 'Stripe payment success return URL', 'https://example.com/index.php?page=placeorder', NULL, 'Stripe', 70, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(93, 'shop_system', 'payment_stripe', 'stripe_webhook_secret', '', 'string', 1, 'Stripe webhook secret for verification', '', NULL, 'Stripe', 80, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(94, 'shop_system', 'payment_coinbase', 'coinbase_enabled', 'false', 'boolean', 0, 'Enable Coinbase Commerce payments', 'false', NULL, 'Coinbase', 10, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(95, 'shop_system', 'payment_coinbase', 'coinbase_key', '', 'string', 1, 'Coinbase API key', '', NULL, 'Coinbase', 20, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(96, 'shop_system', 'payment_coinbase', 'coinbase_secret', '', 'string', 1, 'Coinbase webhook secret', '', NULL, 'Coinbase', 30, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(97, 'shop_system', 'payment_coinbase', 'coinbase_currency', 'USD', 'string', 0, 'Coinbase currency code', 'USD', NULL, 'Coinbase', 40, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(98, 'shop_system', 'payment_coinbase', 'coinbase_cancel_url', 'https://example.com/index.php?page=cart', 'string', 0, 'Coinbase payment cancellation URL', 'https://example.com/index.php?page=cart', NULL, 'Coinbase', 50, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(99, 'shop_system', 'payment_coinbase', 'coinbase_return_url', 'https://example.com/index.php?page=placeorder', 'string', 0, 'Coinbase payment success return URL', 'https://example.com/index.php?page=placeorder', NULL, 'Coinbase', 60, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(100, 'shop_system', 'payment_cod', 'pay_on_delivery_enabled', 'true', 'boolean', 0, 'Enable pay on delivery option', 'true', NULL, 'Payment Options', 10, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(104, 'invoice_system', 'basic', 'base_url', 'http://localhost/projects/phpinvoice/advanced/', 'string', 0, 'Base URL of invoice system', 'http://localhost/invoice/', NULL, 'Basic Info', 10, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(105, 'invoice_system', 'basic', 'invoice_prefix', 'INV', 'string', 0, 'Invoice number prefix', 'INV', NULL, 'Basic Info', 20, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(106, 'invoice_system', 'basic', 'currency_code', '&dollar;', 'string', 0, 'Default currency symbol', '&dollar;', NULL, 'Basic Info', 30, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(107, 'invoice_system', 'basic', 'pdf_attachments', 'true', 'boolean', 0, 'Attach PDF to emails', 'true', NULL, 'Basic Info', 40, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(108, 'invoice_system', 'basic', 'cron_secret', 'secret', 'string', 1, 'Cron job access secret', 'secret', NULL, 'Basic Info', 50, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(109, 'invoice_system', 'company', 'company_name', 'Your Company Name', 'string', 0, 'Company name', 'Your Company Name', NULL, 'Company Info', 10, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(110, 'invoice_system', 'company', 'company_email', 'company@example.com', 'string', 0, 'Company email address', 'company@example.com', NULL, 'Company Info', 20, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(111, 'invoice_system', 'company', 'company_phone', '01234 567890', 'string', 0, 'Company phone number', '01234 567890', NULL, 'Company Info', 30, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(112, 'invoice_system', 'company', 'company_address', '123 Example Street\nExample City\nEX4 MPL\nUnited States', 'string', 0, 'Company mailing address', '123 Example Street\nExample City\nEX4 MPL\nUnited States', NULL, 'Company Info', 40, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(113, 'invoice_system', 'company', 'company_logo', '', 'string', 0, 'Company logo file path', '', NULL, 'Company Info', 50, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(114, 'invoice_system', 'mail', 'mail_enabled', 'false', 'boolean', 0, 'Enable email notifications', 'false', NULL, 'Email', 10, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(115, 'invoice_system', 'mail', 'mail_from', 'noreply@example.com', 'string', 0, 'From email address', 'noreply@example.com', NULL, 'Email', 20, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(116, 'invoice_system', 'mail', 'mail_name', 'Your Website/Business Name', 'string', 0, 'From name for emails', 'Your Business Name', NULL, 'Email', 30, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(117, 'invoice_system', 'mail', 'notifications_enabled', 'true', 'boolean', 0, 'Enable admin notifications', 'true', NULL, 'Email', 40, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(118, 'invoice_system', 'mail', 'notification_email', 'notifications@example.com', 'string', 0, 'Admin notification email', 'notifications@example.com', NULL, 'Email', 50, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(119, 'invoice_system', 'smtp', 'smtp_enabled', 'false', 'boolean', 0, 'Use SMTP server', 'false', NULL, 'SMTP', 10, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(120, 'invoice_system', 'smtp', 'smtp_secure', 'ssl', 'string', 0, 'SMTP security type', 'ssl', NULL, 'SMTP', 20, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(121, 'invoice_system', 'smtp', 'smtp_host', 'smtp.example.com', 'string', 1, 'SMTP hostname', 'smtp.example.com', NULL, 'SMTP', 30, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(122, 'invoice_system', 'smtp', 'smtp_port', '465', 'integer', 0, 'SMTP port number', '465', NULL, 'SMTP', 40, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(123, 'invoice_system', 'smtp', 'smtp_username', 'user@example.com', 'string', 1, 'SMTP username', 'user@example.com', NULL, 'SMTP', 50, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(124, 'invoice_system', 'smtp', 'smtp_password', 'secret', 'string', 1, 'SMTP password', '', NULL, 'SMTP', 60, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(125, 'invoice_system', 'payment_paypal', 'paypal_enabled', 'true', 'boolean', 0, 'Enable PayPal payments', 'true', NULL, 'PayPal', 10, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(126, 'invoice_system', 'payment_paypal', 'paypal_email', 'payments@example.com', 'string', 0, 'PayPal business email', 'payments@example.com', NULL, 'PayPal', 20, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(127, 'invoice_system', 'payment_paypal', 'paypal_testmode', 'true', 'boolean', 0, 'PayPal sandbox mode', 'true', NULL, 'PayPal', 30, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(128, 'invoice_system', 'payment_paypal', 'paypal_currency', 'USD', 'string', 0, 'PayPal currency', 'USD', NULL, 'PayPal', 40, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(129, 'invoice_system', 'payment_paypal', 'paypal_ipn_url', 'https://example.com/ipn.php?method=paypal', 'string', 0, 'PayPal IPN URL', 'https://example.com/ipn.php?method=paypal', NULL, 'PayPal', 50, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(130, 'invoice_system', 'payment_stripe', 'stripe_enabled', 'true', 'boolean', 0, 'Enable Stripe payments', 'true', NULL, 'Stripe', 10, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(131, 'invoice_system', 'payment_stripe', 'stripe_secret_key', '', 'string', 1, 'Stripe secret API key', '', NULL, 'Stripe', 20, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(132, 'invoice_system', 'payment_stripe', 'stripe_currency', 'USD', 'string', 0, 'Stripe currency', 'USD', NULL, 'Stripe', 30, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(133, 'invoice_system', 'payment_stripe', 'stripe_ipn_url', 'https://example.com/ipn.php?method=stripe', 'string', 0, 'Stripe webhook URL', 'https://example.com/ipn.php?method=stripe', NULL, 'Stripe', 40, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(134, 'invoice_system', 'payment_stripe', 'stripe_webhook_secret', '', 'string', 1, 'Stripe webhook secret', '', NULL, 'Stripe', 50, 1, '2025-08-17 17:57:03', '2025-08-17 19:16:31', 'enhanced_migration'),
(135, 'form_system', 'basic', 'site_name', NULL, 'string', 0, 'Form system site name', 'Contact Forms', NULL, 'Basic Info', 10, 1, '2025-08-17 17:57:03', '2025-08-17 17:57:03', NULL),
(136, 'form_system', 'basic', 'default_recipient', NULL, 'string', 0, 'Default form recipient email', 'forms@example.com', NULL, 'Basic Info', 20, 1, '2025-08-17 17:57:03', '2025-08-17 17:57:03', NULL),
(137, 'form_system', 'basic', 'enable_captcha', NULL, 'boolean', 0, 'Enable CAPTCHA protection', 'true', NULL, 'Basic Info', 30, 1, '2025-08-17 17:57:03', '2025-08-17 17:57:03', NULL),
(138, 'form_system', 'mail', 'mail_enabled', NULL, 'boolean', 0, 'Enable email notifications', 'true', NULL, 'Email', 10, 1, '2025-08-17 17:57:03', '2025-08-17 17:57:03', NULL),
(139, 'form_system', 'mail', 'mail_from', NULL, 'string', 0, 'From email address', 'noreply@example.com', NULL, 'Email', 20, 1, '2025-08-17 17:57:03', '2025-08-17 17:57:03', NULL),
(140, 'form_system', 'mail', 'mail_name', NULL, 'string', 0, 'From name for emails', 'Contact Form System', NULL, 'Email', 30, 1, '2025-08-17 17:57:03', '2025-08-17 17:57:03', NULL),
(141, 'form_system', 'smtp', 'smtp_enabled', NULL, 'boolean', 0, 'Use SMTP server', 'false', NULL, 'SMTP', 10, 1, '2025-08-17 17:57:03', '2025-08-17 17:57:03', NULL),
(142, 'form_system', 'smtp', 'smtp_secure', NULL, 'string', 0, 'SMTP security type', 'ssl', NULL, 'SMTP', 20, 1, '2025-08-17 17:57:03', '2025-08-17 17:57:03', NULL),
(143, 'form_system', 'smtp', 'smtp_host', NULL, 'string', 1, 'SMTP hostname', 'smtp.example.com', NULL, 'SMTP', 30, 1, '2025-08-17 17:57:03', '2025-08-17 17:57:03', NULL),
(144, 'form_system', 'smtp', 'smtp_port', NULL, 'integer', 0, 'SMTP port number', '465', NULL, 'SMTP', 40, 1, '2025-08-17 17:57:03', '2025-08-17 17:57:03', NULL),
(145, 'form_system', 'smtp', 'smtp_username', NULL, 'string', 1, 'SMTP username', 'user@example.com', NULL, 'SMTP', 50, 1, '2025-08-17 17:57:03', '2025-08-17 17:57:03', NULL),
(146, 'form_system', 'smtp', 'smtp_password', NULL, 'string', 1, 'SMTP password', '', NULL, 'SMTP', 60, 1, '2025-08-17 17:57:03', '2025-08-17 17:57:03', NULL),
(147, 'chat_system', 'basic', 'site_name', NULL, 'string', 0, 'Chat system name', 'Live Chat', NULL, 'Basic Info', 10, 1, '2025-08-17 17:57:03', '2025-08-17 17:57:03', NULL),
(148, 'chat_system', 'basic', 'enable_guest_chat', NULL, 'boolean', 0, 'Allow guest users to chat', 'true', NULL, 'Basic Info', 20, 1, '2025-08-17 17:57:03', '2025-08-17 17:57:03', NULL),
(149, 'chat_system', 'basic', 'max_message_length', NULL, 'integer', 0, 'Maximum message length', '500', NULL, 'Basic Info', 30, 1, '2025-08-17 17:57:03', '2025-08-17 17:57:03', NULL),
(150, 'chat_system', 'basic', 'chat_history_days', NULL, 'integer', 0, 'Days to keep chat history', '30', NULL, 'Basic Info', 40, 1, '2025-08-17 17:57:03', '2025-08-17 17:57:03', NULL),
(151, 'review_system', 'basic', 'site_name', NULL, 'string', 0, 'Review system name', 'Customer Reviews', NULL, 'Basic Info', 10, 1, '2025-08-17 17:57:03', '2025-08-17 17:57:03', NULL),
(152, 'review_system', 'basic', 'enable_guest_reviews', NULL, 'boolean', 0, 'Allow guest reviews', 'false', NULL, 'Basic Info', 20, 1, '2025-08-17 17:57:03', '2025-08-17 17:57:03', NULL),
(153, 'review_system', 'basic', 'require_approval', NULL, 'boolean', 0, 'Require admin approval', 'true', NULL, 'Basic Info', 30, 1, '2025-08-17 17:57:03', '2025-08-17 17:57:03', NULL),
(154, 'review_system', 'basic', 'max_rating', NULL, 'integer', 0, 'Maximum rating scale', '5', NULL, 'Basic Info', 40, 1, '2025-08-17 17:57:03', '2025-08-17 17:57:03', NULL),
(155, 'review_system', 'mail', 'mail_enabled', NULL, 'boolean', 0, 'Enable review notifications', 'true', NULL, 'Email', 10, 1, '2025-08-17 17:57:03', '2025-08-17 17:57:03', NULL),
(156, 'review_system', 'mail', 'notification_email', NULL, 'string', 0, 'Review notification email', 'reviews@example.com', NULL, 'Email', 20, 1, '2025-08-17 17:57:03', '2025-08-17 17:57:03', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `setting_app_configurations_audit`
--

CREATE TABLE `setting_app_configurations_audit` (
  `id` int(11) NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Audit trail for configuration changes';

--
-- Dumping data for table `setting_app_configurations_audit`
--

INSERT INTO `setting_app_configurations_audit` (`id`, `config_id`, `app_name`, `section`, `config_key`, `old_value`, `new_value`, `change_type`, `changed_by`, `change_reason`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'blog_system', 'identity', 'blog_title', 'My Blog', 'GlitchWizard Solutions Blog', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(2, 2, 'blog_system', 'identity', 'blog_description', 'Welcome to my blog', 'Information for the Members of GlitchWizard Solutions LLC', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(3, 3, 'blog_system', 'identity', 'blog_site_url', 'https://example.com/blog', 'https://glitchwizarddigitalsolutions.com/blog', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(4, 6, 'blog_system', 'identity', 'email', 'blog@example.com', 'barbara@glitchwizarddigitalsolutions.com', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(5, 7, 'blog_system', 'display', 'layout', 'Wide', 'Wide', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(6, 8, 'blog_system', 'display', 'theme', 'Default', 'Pulse', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(7, 9, 'blog_system', 'display', 'sidebar_position', 'Right', 'Right', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(8, 10, 'blog_system', 'display', 'posts_per_row', '2', '2', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(9, 11, 'blog_system', 'display', 'date_format', 'F j, Y', 'F j, Y', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(10, 12, 'blog_system', 'display', 'latestposts_bar', 'Enabled', 'Enabled', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(11, 14, 'blog_system', 'functionality', 'comments', 'guests', 'guests', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(12, 15, 'blog_system', 'functionality', 'rtl', 'No', 'No', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(13, 17, 'blog_system', 'social', 'facebook', '', 'https://www.facebook.com/GlitchWizardSolutions/', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(14, 21, 'blog_system', 'social', 'linkedin', '', 'https://www.linkedin.com/in/glitchwizard/', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(15, 22, 'blog_system', 'security', 'gcaptcha_sitekey', '', '6LdmAmgrAAAAAIdsJeCLDjkPhYeVZIH6wSGqkxIH', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(16, 23, 'blog_system', 'security', 'gcaptcha_secretkey', '', '6LdmAmgrAAAAAKXJibD69CmlnsUP5sQFIQImwODW', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(17, 24, 'blog_system', 'security', 'gcaptcha_projectid', '', 'glitchwizardsolu-1696110549072', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(18, 25, 'shop_system', 'basic', 'site_name', 'Shopping Cart', 'Shopping Cart', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(19, 26, 'shop_system', 'basic', 'currency_code', '&dollar;', '&dollar;', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(20, 27, 'shop_system', 'basic', 'weight_unit', 'lbs', 'lbs', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(21, 28, 'shop_system', 'basic', 'featured_image', 'uploads/featured-image.jpg', 'uploads/featured-image.jpg', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(22, 30, 'shop_system', 'functionality', 'account_required', 'false', 'false', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(23, 31, 'shop_system', 'functionality', 'rewrite_url', 'false', 'false', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(24, 32, 'shop_system', 'functionality', 'template_editor', 'tinymce', 'tinymce', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(25, 33, 'shop_system', 'functionality', 'default_payment_status', 'Completed', 'Completed', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(26, 34, 'shop_system', 'mail', 'mail_enabled', 'false', 'false', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(27, 35, 'shop_system', 'mail', 'mail_from', 'noreply@example.com', 'noreply@example.com', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(28, 36, 'shop_system', 'mail', 'mail_name', 'Your Website/Business Name', 'Your Website/Business Name', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(29, 37, 'shop_system', 'mail', 'notifications_enabled', 'true', 'true', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(30, 38, 'shop_system', 'mail', 'notification_email', 'notifications@example.com', 'notifications@example.com', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(31, 39, 'shop_system', 'smtp', 'smtp_enabled', 'false', 'false', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(32, 40, 'shop_system', 'smtp', 'smtp_secure', 'ssl', 'ssl', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(33, 41, 'shop_system', 'smtp', 'smtp_host', 'smtp.example.com', 'smtp.example.com', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(34, 42, 'shop_system', 'smtp', 'smtp_port', '465', '465', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24'),
(35, 45, 'shop_system', 'security', 'secret_key', 'YOUR_SECRET_KEY', 'YOUR_SECRET_KEY', 'UPDATE', 'migration_script', 'Phase 1 migration from file-based configuration to database', '127.0.0.1', NULL, '2025-08-17 17:28:24');

-- --------------------------------------------------------

--
-- Table structure for table `setting_app_configurations_cache`
--

CREATE TABLE `setting_app_configurations_cache` (
  `cache_key` varchar(255) NOT NULL,
  `app_name` varchar(50) NOT NULL,
  `cached_data` longtext NOT NULL COMMENT 'JSON cached configuration data',
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configuration cache for performance optimization';

-- --------------------------------------------------------

--
-- Table structure for table `setting_blog_comments`
--

CREATE TABLE `setting_blog_comments` (
  `id` int(11) NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `setting_blog_comments`
--

INSERT INTO `setting_blog_comments` (`id`, `comment_system`, `require_approval`, `allow_guest_comments`, `require_registration`, `max_comment_length`, `enable_notifications`, `notification_email`, `enable_threading`, `max_thread_depth`, `enable_comment_voting`, `enable_comment_editing`, `comment_edit_time_limit`, `enable_comment_deletion`, `enable_spam_protection`, `disqus_shortname`, `facebook_app_id`, `created_at`, `updated_at`) VALUES
(1, 'internal', 1, 1, 0, 1000, 1, 'admin@example.com', 1, 3, 0, 1, 300, 1, 1, '', '', '2025-08-17 18:04:54', '2025-08-17 18:04:54'),
(2, 'internal', 1, 1, 0, 1000, 1, 'admin@example.com', 1, 3, 0, 1, 300, 1, 1, '', '', '2025-08-17 18:10:26', '2025-08-17 18:10:26'),
(3, 'internal', 1, 1, 0, 1000, 1, 'admin@example.com', 1, 3, 0, 1, 300, 1, 1, '', '', '2025-08-17 19:17:00', '2025-08-17 19:17:00');

-- --------------------------------------------------------

--
-- Table structure for table `setting_blog_config`
--

CREATE TABLE `setting_blog_config` (
  `id` int(11) NOT NULL,
  `blog_site_url` varchar(255) DEFAULT NULL,
  `sitename` varchar(255) DEFAULT 'GWS Blog',
  `blog_description` text DEFAULT 'Latest news and insights',
  `blog_email` varchar(255) DEFAULT NULL,
  `posts_per_page` int(11) DEFAULT 10,
  `comments_enabled` varchar(20) DEFAULT 'guests',
  `date_format` varchar(50) DEFAULT 'F j, Y',
  `layout` varchar(50) DEFAULT 'Wide',
  `sidebar_position` varchar(20) DEFAULT 'Right',
  `posts_per_row` int(11) DEFAULT 2,
  `theme` varchar(100) DEFAULT 'Pulse',
  `background_image` varchar(255) DEFAULT NULL,
  `featured_posts_count` int(11) DEFAULT 5,
  `excerpt_length` int(11) DEFAULT 150,
  `read_more_text` varchar(100) DEFAULT 'Read More',
  `author_display` tinyint(1) DEFAULT 1,
  `category_display` tinyint(1) DEFAULT 1,
  `tag_display` tinyint(1) DEFAULT 1,
  `related_posts_count` int(11) DEFAULT 3,
  `rss_enabled` tinyint(1) DEFAULT 1,
  `search_enabled` tinyint(1) DEFAULT 1,
  `archive_enabled` tinyint(1) DEFAULT 1,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_blog_display`
--

CREATE TABLE `setting_blog_display` (
  `id` int(11) NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `setting_blog_display`
--

INSERT INTO `setting_blog_display` (`id`, `posts_per_page`, `excerpt_length`, `date_format`, `layout`, `sidebar_position`, `posts_per_row`, `theme`, `enable_featured_image`, `thumbnail_width`, `thumbnail_height`, `background_image`, `custom_css`, `show_author`, `show_date`, `show_categories`, `show_tags`, `show_excerpt`, `created_at`, `updated_at`) VALUES
(1, 10, 250, 'F j, Y', 'Wide', 'Right', 2, 'Default', 1, 300, 200, '', '', 1, 1, 1, 1, 1, '2025-08-17 18:04:54', '2025-08-17 18:04:54'),
(2, 10, 250, 'F j, Y', 'Wide', 'Right', 2, 'Default', 1, 300, 200, '', '', 1, 1, 1, 1, 1, '2025-08-17 18:10:26', '2025-08-17 18:10:26'),
(3, 10, 250, 'F j, Y', 'Wide', 'Right', 2, 'Default', 1, 300, 200, '', '', 1, 1, 1, 1, 1, '2025-08-17 19:16:59', '2025-08-17 19:16:59');

-- --------------------------------------------------------

--
-- Table structure for table `setting_blog_features`
--

CREATE TABLE `setting_blog_features` (
  `id` int(11) NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `setting_blog_features`
--

INSERT INTO `setting_blog_features` (`id`, `enable_posts`, `enable_pages`, `enable_categories`, `enable_tags`, `enable_comments`, `enable_author_bio`, `enable_social_sharing`, `enable_related_posts`, `enable_search`, `enable_archives`, `enable_rss`, `enable_sitemap`, `enable_breadcrumbs`, `enable_post_navigation`, `enable_reading_time`, `enable_post_views`, `enable_newsletter_signup`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '2025-08-17 18:04:54', '2025-08-17 18:04:54'),
(2, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '2025-08-17 18:10:26', '2025-08-17 18:10:26'),
(3, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '2025-08-17 19:16:59', '2025-08-17 19:16:59');

-- --------------------------------------------------------

--
-- Table structure for table `setting_blog_identity`
--

CREATE TABLE `setting_blog_identity` (
  `id` int(11) NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `setting_blog_identity`
--

INSERT INTO `setting_blog_identity` (`id`, `blog_title`, `blog_description`, `blog_tagline`, `author_name`, `author_bio`, `default_author_id`, `meta_description`, `meta_keywords`, `blog_email`, `blog_url`, `copyright_text`, `created_at`, `updated_at`) VALUES
(1, 'My Blog', 'Welcome to my blog where I share thoughts and ideas', 'Sharing thoughts and ideas', 'Blog Author', 'About the author - passionate about sharing knowledge and experiences', 1, 'A blog featuring articles, insights, and thoughts on various topics', 'blog, articles, insights, thoughts, content', 'blog@example.com', 'https://example.com/blog', '© 2025 My Blog. All rights reserved.', '2025-08-17 18:04:54', '2025-08-17 18:04:54'),
(3, 'My Blog', 'Welcome to my blog where I share thoughts and ideas', 'Sharing thoughts and ideas', 'Blog Author', 'About the author - passionate about sharing knowledge and experiences', 1, 'A blog featuring articles, insights, and thoughts on various topics', 'blog, articles, insights, thoughts, content', 'blog@example.com', 'https://example.com/blog', '© 2025 My Blog. All rights reserved.', '2025-08-17 19:16:59', '2025-08-17 19:16:59');

-- --------------------------------------------------------

--
-- Table structure for table `setting_blog_seo`
--

CREATE TABLE `setting_blog_seo` (
  `id` int(11) NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `setting_blog_seo`
--

INSERT INTO `setting_blog_seo` (`id`, `enable_seo_urls`, `post_url_structure`, `enable_meta_tags`, `enable_open_graph`, `enable_twitter_cards`, `default_post_image`, `robots_txt_additions`, `sitemap_frequency`, `sitemap_priority`, `enable_canonical_urls`, `enable_schema_markup`, `google_analytics_id`, `google_site_verification`, `bing_site_verification`, `enable_breadcrumb_schema`, `enable_article_schema`, `default_meta_description`, `created_at`, `updated_at`) VALUES
(1, 1, '{year}/{month}/{slug}', 1, 1, 1, '', '', 'weekly', 0.8, 1, 1, '', '', '', 1, 1, 'Discover engaging content and insights on our blog covering various topics and interests.', '2025-08-17 18:04:54', '2025-08-17 18:04:54'),
(2, 1, '{year}/{month}/{slug}', 1, 1, 1, '', '', 'weekly', 0.8, 1, 1, '', '', '', 1, 1, 'Discover engaging content and insights on our blog covering various topics and interests.', '2025-08-17 18:10:26', '2025-08-17 18:10:26'),
(3, 1, '{year}/{month}/{slug}', 1, 1, 1, '', '', 'weekly', 0.8, 1, 1, '', '', '', 1, 1, 'Discover engaging content and insights on our blog covering various topics and interests.', '2025-08-17 19:17:00', '2025-08-17 19:17:00');

-- --------------------------------------------------------

--
-- Table structure for table `setting_blog_social`
--

CREATE TABLE `setting_blog_social` (
  `id` int(11) NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `setting_blog_social`
--

INSERT INTO `setting_blog_social` (`id`, `facebook_url`, `twitter_url`, `instagram_url`, `linkedin_url`, `youtube_url`, `pinterest_url`, `github_url`, `enable_facebook_sharing`, `enable_twitter_sharing`, `enable_linkedin_sharing`, `enable_pinterest_sharing`, `enable_email_sharing`, `enable_whatsapp_sharing`, `enable_reddit_sharing`, `twitter_username`, `facebook_app_id`, `social_sharing_position`, `enable_social_login`, `facebook_login_enabled`, `google_login_enabled`, `twitter_login_enabled`, `created_at`, `updated_at`) VALUES
(1, '', '', '', '', '', '', '', 1, 1, 1, 0, 1, 1, 0, '', '', 'bottom', 0, 0, 0, 0, '2025-08-17 18:04:54', '2025-08-17 18:04:54'),
(2, '', '', '', '', '', '', '', 1, 1, 1, 0, 1, 1, 0, '', '', 'bottom', 0, 0, 0, 0, '2025-08-17 18:10:26', '2025-08-17 18:10:26'),
(3, '', '', '', '', '', '', '', 1, 1, 1, 0, 1, 1, 0, '', '', 'bottom', 0, 0, 0, 0, '2025-08-17 19:17:00', '2025-08-17 19:17:00');

-- --------------------------------------------------------

--
-- Table structure for table `setting_branding_assets`
--

CREATE TABLE `setting_branding_assets` (
  `id` int(11) NOT NULL,
  `business_logo_main` varchar(255) DEFAULT 'assets/img/logo.png',
  `business_logo_horizontal` varchar(255) DEFAULT 'assets/branding/logo_horizontal.png',
  `business_logo_vertical` varchar(255) DEFAULT 'assets/branding/logo_vertical.png',
  `business_logo_square` varchar(255) DEFAULT 'assets/branding/logo_square.png',
  `business_logo_white` varchar(255) DEFAULT 'assets/branding/logo_white.png',
  `business_logo_small` varchar(255) DEFAULT 'assets/branding/logo_small.png',
  `favicon_main` varchar(255) DEFAULT 'assets/img/favicon.png',
  `favicon_blog` varchar(255) DEFAULT 'assets/branding/favicon_blog.ico',
  `favicon_portal` varchar(255) DEFAULT 'assets/branding/favicon_portal.ico',
  `apple_touch_icon` varchar(255) DEFAULT 'assets/img/apple-touch-icon.png',
  `social_share_default` varchar(255) DEFAULT 'assets/branding/social_default.jpg',
  `social_share_facebook` varchar(255) DEFAULT 'assets/branding/social_facebook.jpg',
  `social_share_twitter` varchar(255) DEFAULT 'assets/branding/social_twitter.jpg',
  `social_share_linkedin` varchar(255) DEFAULT 'assets/branding/social_linkedin.jpg',
  `social_share_instagram` varchar(255) DEFAULT 'assets/branding/social_instagram.jpg',
  `social_share_blog` varchar(255) DEFAULT 'assets/branding/social_blog.jpg',
  `hero_background_image` varchar(255) DEFAULT NULL,
  `watermark_image` varchar(255) DEFAULT NULL,
  `loading_animation` varchar(255) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_branding_assets`
--

INSERT INTO `setting_branding_assets` (`id`, `business_logo_main`, `business_logo_horizontal`, `business_logo_vertical`, `business_logo_square`, `business_logo_white`, `business_logo_small`, `favicon_main`, `favicon_blog`, `favicon_portal`, `apple_touch_icon`, `social_share_default`, `social_share_facebook`, `social_share_twitter`, `social_share_linkedin`, `social_share_instagram`, `social_share_blog`, `hero_background_image`, `watermark_image`, `loading_animation`, `last_updated`) VALUES
(1, 'assets/img/logo.jpg', 'assets/branding/logo_horizontal.png', 'assets/branding/logo_vertical.png', 'assets/branding/logo_square.png', 'assets/branding/logo_white.png', 'assets/branding/logo_small.png', 'assets/img/favicon.png', 'assets/branding/favicon_blog.ico', 'assets/branding/favicon_portal.ico', 'assets/img/apple-touch-icon.png', 'assets/branding/social_default.jpg', 'assets/branding/social_facebook.jpg', 'assets/branding/social_twitter.jpg', 'assets/branding/social_linkedin.jpg', 'assets/branding/social_instagram.jpg', 'assets/branding/social_blog.jpg', 'assets/img/hero-uploads/hero-bg.png', NULL, NULL, '2025-08-17 03:29:28');

-- --------------------------------------------------------

--
-- Table structure for table `setting_branding_colors`
--

CREATE TABLE `setting_branding_colors` (
  `id` int(11) NOT NULL,
  `brand_primary_color` varchar(7) NOT NULL DEFAULT '#6c2eb6',
  `brand_secondary_color` varchar(7) NOT NULL DEFAULT '#bf5512',
  `brand_tertiary_color` varchar(7) DEFAULT '#8B4513' COMMENT 'Third brand color',
  `brand_quaternary_color` varchar(7) DEFAULT '#2E8B57' COMMENT 'Fourth brand color',
  `brand_accent_color` varchar(7) DEFAULT '#28a745',
  `brand_warning_color` varchar(7) DEFAULT '#ffc107',
  `brand_danger_color` varchar(7) DEFAULT '#dc3545',
  `brand_info_color` varchar(7) DEFAULT '#17a2b8',
  `brand_background_color` varchar(7) DEFAULT '#ffffff',
  `brand_text_color` varchar(7) DEFAULT '#333333',
  `brand_text_light` varchar(7) DEFAULT '#666666',
  `brand_text_muted` varchar(7) DEFAULT '#999999',
  `brand_font_primary` varchar(255) DEFAULT 'Inter, system-ui, sans-serif',
  `brand_font_secondary` varchar(255) DEFAULT 'Roboto, Arial, sans-serif',
  `brand_font_heading` varchar(255) DEFAULT 'Inter, system-ui, sans-serif',
  `brand_font_body` varchar(255) DEFAULT 'Roboto, Arial, sans-serif',
  `brand_font_monospace` varchar(255) DEFAULT 'SF Mono, Monaco, Consolas, monospace',
  `brand_success_color` varchar(7) DEFAULT '#28a745',
  `brand_error_color` varchar(7) DEFAULT '#dc3545',
  `custom_color_1` varchar(7) DEFAULT NULL,
  `custom_color_2` varchar(7) DEFAULT NULL,
  `custom_color_3` varchar(7) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_branding_colors`
--

INSERT INTO `setting_branding_colors` (`id`, `brand_primary_color`, `brand_secondary_color`, `brand_tertiary_color`, `brand_quaternary_color`, `brand_accent_color`, `brand_warning_color`, `brand_danger_color`, `brand_info_color`, `brand_background_color`, `brand_text_color`, `brand_text_light`, `brand_text_muted`, `brand_font_primary`, `brand_font_secondary`, `brand_font_heading`, `brand_font_body`, `brand_font_monospace`, `brand_success_color`, `brand_error_color`, `custom_color_1`, `custom_color_2`, `custom_color_3`, `last_updated`) VALUES
(1, '#669999', '#b3ced1', '#e7b09e', '#2e8b57', '#ddaa50', '#ffc107', '#dc3545', '#17a2b8', '#ffffff', '#333333', '#666666', '#999999', 'Inter, system-ui, sans-serif', 'Roboto, Arial, sans-serif', 'Inter, system-ui, sans-serif', 'Roboto, Arial, sans-serif', 'SF Mono, Monaco, Consolas, monospace', '#28a745', '#dc3545', NULL, NULL, NULL, '2025-08-17 23:56:26');

-- --------------------------------------------------------

--
-- Table structure for table `setting_branding_fonts`
--

CREATE TABLE `setting_branding_fonts` (
  `id` int(11) NOT NULL,
  `brand_font_primary` varchar(255) DEFAULT 'Roboto, Poppins, Raleway, Arial, sans-serif',
  `brand_font_headings` varchar(255) DEFAULT 'Poppins, Arial, sans-serif',
  `brand_font_body` varchar(255) DEFAULT 'Roboto, Arial, sans-serif',
  `brand_font_accent` varchar(255) DEFAULT 'Raleway, Arial, sans-serif',
  `brand_font_monospace` varchar(255) DEFAULT 'Consolas, Monaco, "Courier New", monospace',
  `brand_font_display` varchar(255) DEFAULT 'Georgia, "Times New Roman", serif',
  `brand_font_file_1` varchar(255) DEFAULT NULL,
  `brand_font_file_2` varchar(255) DEFAULT NULL,
  `brand_font_file_3` varchar(255) DEFAULT NULL,
  `brand_font_file_4` varchar(255) DEFAULT NULL,
  `brand_font_file_5` varchar(255) DEFAULT NULL,
  `brand_font_file_6` varchar(255) DEFAULT NULL,
  `font_size_base` varchar(10) DEFAULT '16px',
  `font_size_small` varchar(10) DEFAULT '14px',
  `font_size_large` varchar(10) DEFAULT '18px',
  `font_weight_normal` varchar(10) DEFAULT '400',
  `font_weight_bold` varchar(10) DEFAULT '700',
  `line_height_base` varchar(10) DEFAULT '1.5',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_branding_fonts`
--

INSERT INTO `setting_branding_fonts` (`id`, `brand_font_primary`, `brand_font_headings`, `brand_font_body`, `brand_font_accent`, `brand_font_monospace`, `brand_font_display`, `brand_font_file_1`, `brand_font_file_2`, `brand_font_file_3`, `brand_font_file_4`, `brand_font_file_5`, `brand_font_file_6`, `font_size_base`, `font_size_small`, `font_size_large`, `font_weight_normal`, `font_weight_bold`, `line_height_base`, `last_updated`) VALUES
(1, 'Roboto, Poppins, Raleway, Arial, sans-serif', 'Poppins, Arial, sans-serif', 'Roboto, Arial, sans-serif', 'Raleway, Arial, sans-serif', 'Consolas, Monaco, &amp;amp;amp;quot;Courier New&amp;amp;amp;quot;, monospace', 'Georgia, &amp;amp;amp;quot;Times New Roman&amp;amp;amp;quot;, serif', NULL, NULL, NULL, NULL, NULL, NULL, '16px', '14px', '18px', '400', '700', '1.5', '2025-08-16 18:50:22');

-- --------------------------------------------------------

--
-- Table structure for table `setting_branding_templates`
--

CREATE TABLE `setting_branding_templates` (
  `id` int(11) NOT NULL,
  `template_key` varchar(50) NOT NULL,
  `template_name` varchar(100) NOT NULL,
  `template_description` text DEFAULT NULL,
  `css_class` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `template_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`template_config`)),
  `preview_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_branding_templates`
--

INSERT INTO `setting_branding_templates` (`id`, `template_key`, `template_name`, `template_description`, `css_class`, `is_active`, `template_config`, `preview_image`, `created_at`, `last_updated`) VALUES
(1, 'template_1', 'Classic', 'Traditional layout with primary color for headers', 'brand-template-classic', 1, NULL, NULL, '2025-08-15 21:00:34', '2025-08-15 21:00:34'),
(2, 'template_2', 'Modern', 'Contemporary layout with secondary color emphasis', 'brand-template-modern', 0, NULL, NULL, '2025-08-15 21:00:34', '2025-08-15 21:00:34'),
(3, 'template_3', 'Bold', 'High contrast layout with accent colors', 'brand-template-bold', 0, NULL, NULL, '2025-08-15 21:00:34', '2025-08-15 21:00:34');

--
-- Triggers `setting_branding_templates`
--
DELIMITER $$
CREATE TRIGGER `template_activation_control` BEFORE UPDATE ON `setting_branding_templates` FOR EACH ROW BEGIN
    -- If setting a template to active, deactivate all others
    IF NEW.is_active = TRUE AND OLD.is_active = FALSE THEN
        UPDATE setting_branding_templates 
        SET is_active = FALSE 
        WHERE template_key != NEW.template_key AND is_active = TRUE;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `template_activation_control_insert` BEFORE INSERT ON `setting_branding_templates` FOR EACH ROW BEGIN
    -- If inserting an active template, deactivate all others
    IF NEW.is_active = TRUE THEN
        UPDATE setting_branding_templates 
        SET is_active = FALSE 
        WHERE is_active = TRUE;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `setting_business_contact`
--

CREATE TABLE `setting_business_contact` (
  `id` int(11) NOT NULL,
  `business_identity_id` int(11) NOT NULL DEFAULT 1,
  `primary_email` varchar(255) DEFAULT NULL,
  `primary_phone` varchar(50) DEFAULT NULL,
  `primary_address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `zipcode` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'United States',
  `website_url` varchar(255) DEFAULT NULL,
  `business_hours` text DEFAULT NULL,
  `secondary_phone` varchar(50) DEFAULT NULL,
  `fax_number` varchar(50) DEFAULT NULL,
  `mailing_address` varchar(255) DEFAULT NULL,
  `mailing_city` varchar(100) DEFAULT NULL,
  `mailing_state` varchar(50) DEFAULT NULL,
  `mailing_zipcode` varchar(20) DEFAULT NULL,
  `social_facebook` varchar(255) DEFAULT NULL,
  `social_instagram` varchar(255) DEFAULT NULL,
  `social_twitter` varchar(255) DEFAULT NULL,
  `social_linkedin` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_business_contact`
--

INSERT INTO `setting_business_contact` (`id`, `business_identity_id`, `primary_email`, `primary_phone`, `primary_address`, `city`, `state`, `zipcode`, `country`, `website_url`, `business_hours`, `secondary_phone`, `fax_number`, `mailing_address`, `mailing_city`, `mailing_state`, `mailing_zipcode`, `social_facebook`, `social_instagram`, `social_twitter`, `social_linkedin`, `created_at`, `last_updated`) VALUES
(1, 1, 'help@burden-to-blessings.com', '(574) 633-1736', '5776 Grape Rd. STE 51, PMB 141', 'Mishawaka', 'IN', '46545', 'United States', 'https://burden-to-blessings.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'https://www.facebook.com/share/1AXCLnLRmr/', 'https://www.instagram.com/burden_to_blessings?igsh=cm0zenZoYTV6NjRm', NULL, NULL, '2025-08-17 08:23:59', '2025-08-17 08:23:59');

-- --------------------------------------------------------

--
-- Table structure for table `setting_business_identity`
--

CREATE TABLE `setting_business_identity` (
  `id` int(11) NOT NULL,
  `business_name_short` varchar(50) NOT NULL DEFAULT 'GWS',
  `business_name_medium` varchar(100) NOT NULL DEFAULT 'GWS Universal',
  `business_name_long` varchar(200) NOT NULL DEFAULT 'GWS Universal Hybrid Application',
  `business_tagline_short` varchar(100) DEFAULT 'Innovation Simplified',
  `business_tagline_medium` varchar(200) DEFAULT 'Your complete business solution platform',
  `business_tagline_long` text DEFAULT 'Comprehensive hybrid application platform designed to streamline your business operations',
  `legal_business_name` varchar(200) DEFAULT NULL,
  `business_type` varchar(100) DEFAULT NULL,
  `tax_id` varchar(50) DEFAULT NULL,
  `registration_number` varchar(100) DEFAULT NULL,
  `established_date` date DEFAULT NULL,
  `about_business` text DEFAULT NULL,
  `mission_statement` text DEFAULT NULL,
  `vision_statement` text DEFAULT NULL,
  `core_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`core_values`)),
  `author` varchar(100) DEFAULT 'GWS',
  `footer_business_name_type` varchar(20) DEFAULT 'medium',
  `footer_logo_enabled` tinyint(1) DEFAULT 1,
  `footer_logo_position` varchar(20) DEFAULT 'left',
  `footer_logo_file` varchar(50) DEFAULT 'business_logo',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_business_identity`
--

INSERT INTO `setting_business_identity` (`id`, `business_name_short`, `business_name_medium`, `business_name_long`, `business_tagline_short`, `business_tagline_medium`, `business_tagline_long`, `legal_business_name`, `business_type`, `tax_id`, `registration_number`, `established_date`, `about_business`, `mission_statement`, `vision_statement`, `core_values`, `author`, `footer_business_name_type`, `footer_logo_enabled`, `footer_logo_position`, `footer_logo_file`, `last_updated`) VALUES
(1, 'B2B', 'Burden to Blessings', 'Burden to Blessings Home Solutions LLC', 'Burdens Into Blessings', 'Transforming foreclosure challenges into family solutions', 'We handle the complexities of the foreclosure process, so you can focus on what&#039;s next.', 'Burden to Blessings Home Solutions LLC', 'Limited Liability Company', NULL, NULL, '2024-01-01', 'Burden to Blessings specializes in helping Indiana families navigate foreclosure challenges with compassion and expertise. We provide comprehensive foreclosure assistance, creative home solutions, and family-focused financial planning to transform burdens into blessings.', 'Our mission is to transform Indiana from a state burdened by high foreclosure rates to a model of financial resilience and community well-being.', 'We envision vibrant neighborhoods enriched by community support, where every family has the opportunity to thrive despite facing foreclosure challenges.', '[\"People First, House Second\", \"We Listen & Help\", \"Doing What\'s Right\", \"Family-Focused Solutions\", \"Local Community Impact\"]', 'GlitchWizard Solutions LLC', 'medium', 1, 'left', 'favicon', '2025-08-17 17:31:20');

-- --------------------------------------------------------

--
-- Table structure for table `setting_chat_config`
--

CREATE TABLE `setting_chat_config` (
  `id` int(11) NOT NULL,
  `chat_enabled` tinyint(1) DEFAULT 0,
  `chat_widget_position` varchar(20) DEFAULT 'bottom-right',
  `chat_widget_color` varchar(7) DEFAULT '#3498db',
  `chat_welcome_message` text DEFAULT 'Hello! How can we help you today?',
  `chat_offline_message` text DEFAULT 'We are currently offline. Please leave a message and we will get back to you.',
  `chat_auto_assign` tinyint(1) DEFAULT 1,
  `chat_session_timeout` int(11) DEFAULT 30,
  `chat_require_email` tinyint(1) DEFAULT 0,
  `chat_require_name` tinyint(1) DEFAULT 1,
  `chat_enable_file_upload` tinyint(1) DEFAULT 1,
  `chat_max_file_size` int(11) DEFAULT 5,
  `chat_enable_sound_notifications` tinyint(1) DEFAULT 1,
  `chat_enable_email_notifications` tinyint(1) DEFAULT 1,
  `chat_notification_email` varchar(255) DEFAULT '',
  `chat_business_hours_enabled` tinyint(1) DEFAULT 0,
  `chat_business_hours_start` time DEFAULT '09:00:00',
  `chat_business_hours_end` time DEFAULT '17:00:00',
  `chat_business_days` varchar(20) DEFAULT '1,2,3,4,5',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `setting_chat_config`
--

INSERT INTO `setting_chat_config` (`id`, `chat_enabled`, `chat_widget_position`, `chat_widget_color`, `chat_welcome_message`, `chat_offline_message`, `chat_auto_assign`, `chat_session_timeout`, `chat_require_email`, `chat_require_name`, `chat_enable_file_upload`, `chat_max_file_size`, `chat_enable_sound_notifications`, `chat_enable_email_notifications`, `chat_notification_email`, `chat_business_hours_enabled`, `chat_business_hours_start`, `chat_business_hours_end`, `chat_business_days`, `last_updated`) VALUES
(1, 0, 'bottom-right', '#3498db', 'Hello! How can we help you today?', 'We are currently offline. Please leave a message and we will get back to you.', 1, 30, 0, 1, 1, 5, 1, 1, '', 0, '09:00:00', '17:00:00', '1,2,3,4,5', '2025-08-18 03:48:32');

-- --------------------------------------------------------

--
-- Table structure for table `setting_contact_config`
--

CREATE TABLE `setting_contact_config` (
  `id` int(11) NOT NULL,
  `receiving_email` varchar(255) NOT NULL,
  `email_subject_prefix` varchar(100) DEFAULT '[Contact Form]',
  `email_from_name` varchar(255) DEFAULT 'Contact Form',
  `auto_reply_enabled` tinyint(1) DEFAULT 1,
  `auto_reply_subject` varchar(255) DEFAULT 'Thank you for contacting us',
  `auto_reply_message` text DEFAULT 'We have received your message and will respond as soon as possible.',
  `rate_limit_enabled` tinyint(1) DEFAULT 1,
  `rate_limit_max` int(11) DEFAULT 3,
  `rate_limit_window` int(11) DEFAULT 3600,
  `min_submit_interval` int(11) DEFAULT 10,
  `spam_protection_enabled` tinyint(1) DEFAULT 1,
  `blocked_words` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`blocked_words`)),
  `max_links_allowed` int(11) DEFAULT 2,
  `captcha_enabled` tinyint(1) DEFAULT 1,
  `captcha_type` varchar(50) DEFAULT 'recaptcha',
  `form_fields_required` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`form_fields_required`)),
  `custom_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`custom_fields`)),
  `enable_logging` tinyint(1) DEFAULT 1,
  `redirect_after_submit` varchar(255) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_contact_info`
--

CREATE TABLE `setting_contact_info` (
  `id` int(11) NOT NULL,
  `contact_email` varchar(255) NOT NULL,
  `contact_phone` varchar(50) DEFAULT NULL,
  `contact_address` varchar(255) DEFAULT NULL,
  `contact_city` varchar(100) DEFAULT NULL,
  `contact_state` varchar(100) DEFAULT NULL,
  `contact_zipcode` varchar(20) DEFAULT NULL,
  `contact_country` varchar(100) DEFAULT 'United States',
  `business_hours` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`business_hours`)),
  `time_zone` varchar(50) DEFAULT 'America/New_York',
  `contact_form_email` varchar(255) DEFAULT NULL,
  `support_email` varchar(255) DEFAULT NULL,
  `sales_email` varchar(255) DEFAULT NULL,
  `billing_email` varchar(255) DEFAULT NULL,
  `emergency_contact` varchar(255) DEFAULT NULL,
  `mailing_address` text DEFAULT NULL,
  `physical_address` text DEFAULT NULL,
  `gps_coordinates` varchar(100) DEFAULT NULL,
  `office_locations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`office_locations`)),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_contact_info`
--

INSERT INTO `setting_contact_info` (`id`, `contact_email`, `contact_phone`, `contact_address`, `contact_city`, `contact_state`, `contact_zipcode`, `contact_country`, `business_hours`, `time_zone`, `contact_form_email`, `support_email`, `sales_email`, `billing_email`, `emergency_contact`, `mailing_address`, `physical_address`, `gps_coordinates`, `office_locations`, `last_updated`) VALUES
(1, 'help@burdentoblessings.com', '(574) 555-0123', '123 Main Street', 'Elkhart', 'Indiana', '32327', 'United States', NULL, 'America/New_York', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-17 00:05:35');

-- --------------------------------------------------------

--
-- Table structure for table `setting_content_clients`
--

CREATE TABLE `setting_content_clients` (
  `id` int(11) NOT NULL,
  `client_name` varchar(100) NOT NULL,
  `client_logo` varchar(255) NOT NULL,
  `client_website` varchar(255) DEFAULT NULL,
  `client_order` int(11) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_content_clients`
--

INSERT INTO `setting_content_clients` (`id`, `client_name`, `client_logo`, `client_website`, `client_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'First National Bank', 'assets/img/clients/fnb-logo.svg', 'https://www.firstnational.com', 1, 1, '2025-08-17 06:02:41', '2025-08-17 06:17:56'),
(2, 'Home Inspectors Inc', 'assets/img/clients/inspector-logo.svg', 'https://www.homeinspectors.com', 2, 1, '2025-08-17 06:02:41', '2025-08-17 06:17:57'),
(3, 'Title Company', 'assets/img/clients/title-logo.svg', 'https://www.titlecompany.com', 3, 1, '2025-08-17 06:02:41', '2025-08-17 06:17:57'),
(4, 'Insurance Agency', 'assets/img/clients/insurance-logo.svg', 'https://www.insurance.com', 4, 1, '2025-08-17 06:02:41', '2025-08-17 06:17:57'),
(5, 'Mortgage Solutions', 'assets/img/clients/mortgage-logo.svg', 'https://www.mortgagesolutions.com', 5, 1, '2025-08-17 06:02:41', '2025-08-17 06:17:57'),
(6, 'Property Management', 'assets/img/clients/property-logo.svg', 'https://www.propertymanagement.com', 6, 1, '2025-08-17 06:02:41', '2025-08-17 06:17:57');

-- --------------------------------------------------------

--
-- Table structure for table `setting_content_features`
--

CREATE TABLE `setting_content_features` (
  `id` int(11) NOT NULL,
  `feature_key` varchar(100) NOT NULL,
  `feature_title` varchar(255) NOT NULL,
  `feature_description` text DEFAULT NULL,
  `feature_icon` varchar(255) DEFAULT NULL,
  `feature_image` varchar(255) DEFAULT NULL,
  `feature_category` varchar(100) DEFAULT NULL,
  `feature_order` int(11) DEFAULT 0,
  `is_highlighted` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_content_homepage`
--

CREATE TABLE `setting_content_homepage` (
  `id` int(11) NOT NULL,
  `hero_headline` varchar(255) DEFAULT NULL,
  `hero_subheadline` text DEFAULT NULL,
  `hero_button_text` varchar(100) DEFAULT NULL,
  `hero_button_link` varchar(255) DEFAULT NULL,
  `services_section_title` varchar(255) DEFAULT NULL,
  `services_section_description` text DEFAULT NULL,
  `about_section_title` varchar(255) DEFAULT NULL,
  `about_section_subtitle` text DEFAULT NULL,
  `about_section_description` text DEFAULT NULL,
  `about_section_list` text DEFAULT NULL,
  `testimonials_section_title` varchar(255) DEFAULT NULL,
  `testimonials_section_description` text DEFAULT NULL,
  `team_section_title` varchar(255) DEFAULT NULL,
  `team_section_description` text DEFAULT NULL,
  `contact_section_title` varchar(255) DEFAULT NULL,
  `contact_section_description` text DEFAULT NULL,
  `cta_section_title` varchar(255) DEFAULT NULL,
  `cta_section_description` text DEFAULT NULL,
  `cta_button_text` varchar(100) DEFAULT NULL,
  `cta_button_link` varchar(255) DEFAULT NULL,
  `process_section_title` varchar(255) DEFAULT NULL,
  `process_section_description` text DEFAULT NULL,
  `mission_statement` text DEFAULT NULL,
  `value_proposition` text DEFAULT NULL,
  `service_area` varchar(255) DEFAULT NULL,
  `key_differentiator` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` varchar(100) DEFAULT 'system',
  `portfolio_section_title` varchar(255) DEFAULT 'Portfolio',
  `portfolio_section_description` text DEFAULT 'Explore some of our recent projects and creative work. Each item showcases our commitment to quality and innovation.',
  `pricing_section_title` varchar(255) DEFAULT 'Pricing',
  `pricing_section_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_content_homepage`
--

INSERT INTO `setting_content_homepage` (`id`, `hero_headline`, `hero_subheadline`, `hero_button_text`, `hero_button_link`, `services_section_title`, `services_section_description`, `about_section_title`, `about_section_subtitle`, `about_section_description`, `about_section_list`, `testimonials_section_title`, `testimonials_section_description`, `team_section_title`, `team_section_description`, `contact_section_title`, `contact_section_description`, `cta_section_title`, `cta_section_description`, `cta_button_text`, `cta_button_link`, `process_section_title`, `process_section_description`, `mission_statement`, `value_proposition`, `service_area`, `key_differentiator`, `created_at`, `updated_at`, `updated_by`, `portfolio_section_title`, `portfolio_section_description`, `pricing_section_title`, `pricing_section_description`) VALUES
(1, 'Indiana Foreclosure Help', 'Providing Hoosier families with free customized foreclosure prevention plans. No one should have to face these burdens alone.', 'Get My Free Plan', '#contact', 'How We Help', 'We do this for FREE for Indiana families, because we are better together. We are with you every single step along the way!', 'About Burden to Blessings', 'We treat families the way we wish we had been treated during those hard days.', 'We handle the complexities of the foreclosure process, so you can focus on what\'s next. It just goes to show, that with a little faith, even the biggest burdens can become blessings.', 'Free customized foreclosure prevention plans for Indiana families|Personal guidance through every step of the process|Help with bank communication and negotiations|Assistance finding local resources for bills and utilities|Support with moving and finding safe housing|Food assistance and community resources', 'Success Stories', 'See how we\'ve helped Indiana families transform their burdens into blessings through our free foreclosure prevention services.', 'Meet the Team', 'We have simplified the process we went through, so OUR family is ready to help YOUR family!', 'Contact Us', 'Ready to get the help you need? Contact us today for your free customized foreclosure prevention plan. We\'re here to listen and help.', 'Ready to Transform Your Burden into a Blessing?', 'Don\'t face foreclosure alone. Contact us today for your free, personalized plan to help keep your home or find the best path forward for your family.', 'Get My Free Plan Today', '#contact', 'What to Expect from Our Process', 'We are with you every single step along the way! Our proven process helps Indiana families navigate foreclosure with confidence.', 'Providing Hoosier families with free customized foreclosure prevention plans.', 'We are better together - helping families transform their biggest burdens into blessings.', 'Proudly serving Indiana families statewide', 'Unlike the bank, we are on YOUR side. We know how the banks work and help you ask the right questions.', '2025-08-17 01:13:44', '2025-08-17 08:08:23', 'sql_direct_update', 'Portfolio', 'Explore some of our recent projects and creative work. Each item showcases our commitment to quality and innovation.', 'Our Services', 'We offer comprehensive foreclosure assistance and real estate solutions tailored to your family\'s needs.');

-- --------------------------------------------------------

--
-- Table structure for table `setting_content_portfolio`
--

CREATE TABLE `setting_content_portfolio` (
  `id` int(11) NOT NULL,
  `project_title` varchar(100) NOT NULL,
  `project_description` text NOT NULL,
  `project_category` varchar(50) NOT NULL DEFAULT 'all',
  `project_image` varchar(255) NOT NULL,
  `project_large_image` varchar(255) DEFAULT NULL,
  `project_url` varchar(255) DEFAULT NULL,
  `portfolio_order` int(11) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_content_portfolio`
--

INSERT INTO `setting_content_portfolio` (`id`, `project_title`, `project_description`, `project_category`, `project_image`, `project_large_image`, `project_url`, `portfolio_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Mobile App Design', 'Modern, user-friendly mobile application for business productivity.', 'app', 'assets/img/masonry-portfolio/masonry-portfolio-1.jpg', 'assets/img/masonry-portfolio/masonry-portfolio-1.jpg', 'portfolio-details.php?id=1', 1, 1, '2025-08-17 06:35:00', '2025-08-17 06:35:00'),
(2, 'Product Launch Campaign', 'Comprehensive marketing campaign for a new product release.', 'product', 'assets/img/masonry-portfolio/masonry-portfolio-2.jpg', 'assets/img/masonry-portfolio/masonry-portfolio-2.jpg', 'portfolio-details.php?id=2', 2, 1, '2025-08-17 06:35:00', '2025-08-17 06:35:00'),
(3, 'Brand Identity', 'Complete branding package for a growing business.', 'branding', 'assets/img/masonry-portfolio/masonry-portfolio-3.jpg', 'assets/img/masonry-portfolio/masonry-portfolio-3.jpg', 'portfolio-details.php?id=3', 3, 1, '2025-08-17 06:35:00', '2025-08-17 06:35:00'),
(4, 'Task Management App', 'Efficient task tracking and collaboration tool for teams.', 'app', 'assets/img/masonry-portfolio/masonry-portfolio-4.jpg', 'assets/img/masonry-portfolio/masonry-portfolio-4.jpg', 'portfolio-details.php?id=4', 4, 1, '2025-08-17 06:35:00', '2025-08-17 06:35:00'),
(5, 'Product Packaging Design', 'Creative packaging for a retail product line.', 'product', 'assets/img/masonry-portfolio/masonry-portfolio-5.jpg', 'assets/img/masonry-portfolio/masonry-portfolio-5.jpg', 'portfolio-details.php?id=5', 5, 1, '2025-08-17 06:35:00', '2025-08-17 06:35:00'),
(6, 'Website Redesign', 'Modern responsive website for a local business.', 'branding', 'assets/img/masonry-portfolio/masonry-portfolio-6.jpg', 'assets/img/masonry-portfolio/masonry-portfolio-6.jpg', 'portfolio-details.php?id=6', 6, 1, '2025-08-17 06:35:00', '2025-08-17 06:35:00'),
(7, 'Event App', 'Mobile app for event scheduling and attendee engagement.', 'app', 'assets/img/masonry-portfolio/masonry-portfolio-7.jpg', 'assets/img/masonry-portfolio/masonry-portfolio-7.jpg', 'portfolio-details.php?id=7', 7, 1, '2025-08-17 06:35:00', '2025-08-17 06:35:00'),
(8, 'Product Demo Video', 'Engaging video content to showcase product features.', 'product', 'assets/img/masonry-portfolio/masonry-portfolio-8.jpg', 'assets/img/masonry-portfolio/masonry-portfolio-8.jpg', 'portfolio-details.php?id=8', 8, 1, '2025-08-17 06:35:00', '2025-08-17 06:35:00'),
(9, 'Logo & Branding', 'Distinctive logo and branding for a startup company.', 'branding', 'assets/img/masonry-portfolio/masonry-portfolio-9.jpg', 'assets/img/masonry-portfolio/masonry-portfolio-9.jpg', 'portfolio-details.php?id=9', 9, 1, '2025-08-17 06:35:00', '2025-08-17 06:35:00');

-- --------------------------------------------------------

--
-- Table structure for table `setting_content_pricing`
--

CREATE TABLE `setting_content_pricing` (
  `id` int(11) NOT NULL,
  `plan_key` varchar(100) NOT NULL,
  `plan_name` varchar(255) NOT NULL,
  `plan_description` text DEFAULT NULL,
  `plan_short_desc` varchar(500) DEFAULT NULL,
  `plan_price` varchar(100) NOT NULL,
  `plan_price_numeric` decimal(10,2) DEFAULT NULL,
  `plan_billing_period` varchar(50) DEFAULT 'monthly',
  `plan_currency` varchar(10) DEFAULT 'USD',
  `plan_features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`plan_features`)),
  `plan_benefits` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`plan_benefits`)),
  `plan_limitations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`plan_limitations`)),
  `plan_button_text` varchar(100) DEFAULT 'Get Started',
  `plan_button_link` varchar(255) DEFAULT '#',
  `plan_icon` varchar(255) DEFAULT NULL,
  `plan_badge` varchar(100) DEFAULT NULL,
  `plan_color_scheme` varchar(50) DEFAULT 'primary',
  `plan_category` varchar(100) DEFAULT 'standard',
  `plan_order` int(11) DEFAULT 0,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_popular` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_content_pricing`
--

INSERT INTO `setting_content_pricing` (`id`, `plan_key`, `plan_name`, `plan_description`, `plan_short_desc`, `plan_price`, `plan_price_numeric`, `plan_billing_period`, `plan_currency`, `plan_features`, `plan_benefits`, `plan_limitations`, `plan_button_text`, `plan_button_link`, `plan_icon`, `plan_badge`, `plan_color_scheme`, `plan_category`, `plan_order`, `is_featured`, `is_popular`, `is_active`, `created_at`, `last_updated`) VALUES
(7, 'consultation', 'Free Consultation', 'Get personalized help understanding your options during foreclosure', 'No-cost assessment of your situation', 'Free', 0.00, 'one-time', 'USD', '[\"Family situation assessment\", \"Foreclosure timeline review\", \"Available options explanation\", \"Resource connections\", \"Follow-up support\"]', '[\"No financial commitment\", \"Expert guidance\", \"Personalized approach\", \"Local connections\"]', '[\"Consultation only\", \"Implementation requires additional services\"]', 'Get Help Now', '/contact', 'fas fa-handshake', 'No Cost', 'success', 'consultation', 1, 0, 1, 1, '2025-08-17 08:08:23', '2025-08-17 08:08:23'),
(8, 'foreclosure_assistance', 'Foreclosure Assistance', 'Comprehensive support to help you navigate the foreclosure process', 'Complete foreclosure navigation support', 'Contact Us', NULL, 'custom', 'USD', '[\"Foreclosure process guidance\", \"Documentation assistance\", \"Lender negotiations\", \"Timeline management\", \"Legal resource connections\", \"Alternative solution research\"]', '[\"Expert navigation\", \"Reduced stress\", \"Better outcomes\", \"Professional representation\"]', '[\"Pricing varies by complexity\", \"Timeline depends on situation\"]', 'Learn More', '/contact', 'fas fa-shield-alt', 'Most Popular', 'primary', 'assistance', 2, 1, 1, 1, '2025-08-17 08:08:23', '2025-08-17 08:08:23'),
(9, 'home_solutions', 'Creative Home Solutions', 'Explore alternatives to foreclosure that work for your family', 'Alternative solutions to foreclosure', 'Custom Pricing', NULL, 'custom', 'USD', '[\"Loan modification assistance\", \"Short sale coordination\", \"Deed in lieu options\", \"Refinancing exploration\", \"Rental conversion options\", \"Family financial planning\"]', '[\"Avoid foreclosure\", \"Preserve credit rating\", \"Multiple options\", \"Family-focused approach\"]', '[\"Solutions depend on individual circumstances\", \"Not all options available for every situation\"]', 'Explore Options', '/contact', 'fas fa-home', 'Recommended', 'info', 'solutions', 3, 0, 0, 1, '2025-08-17 08:08:23', '2025-08-17 08:08:23');

-- --------------------------------------------------------

--
-- Table structure for table `setting_content_services`
--

CREATE TABLE `setting_content_services` (
  `id` int(11) NOT NULL,
  `service_title` varchar(255) NOT NULL,
  `service_description` text DEFAULT NULL,
  `service_icon` varchar(255) DEFAULT NULL,
  `service_link` varchar(255) DEFAULT NULL,
  `service_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `service_category` varchar(100) DEFAULT 'foreclosure_help',
  `service_summary` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` varchar(100) DEFAULT 'system'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_content_services`
--

INSERT INTO `setting_content_services` (`id`, `service_title`, `service_description`, `service_icon`, `service_link`, `service_order`, `is_active`, `service_category`, `service_summary`, `created_at`, `updated_at`, `updated_by`) VALUES
(1, 'Free Foreclosure Prevention Plans', 'We create customized foreclosure prevention plans specifically for your situation. Every family\'s circumstances are unique, and we take the time to understand your specific needs and develop a plan that works for you. Our plans include step-by-step guidance, timeline expectations, and resources tailored to your location in Indiana.', 'fas fa-shield-alt', '#contact', 1, 1, 'foreclosure_help', 'Customized foreclosure prevention plans for Indiana families at no cost', '2025-08-17 01:19:38', '2025-08-17 01:19:38', 'sql_direct_update'),
(2, 'Bank Communication & Negotiation', 'We help you communicate effectively with your lender and navigate the complex world of bank negotiations. Unlike the bank, we are on YOUR side. We know how the banks work and help you ask the right questions. We can guide you through loan modification requests, payment plans, and other options your lender may offer.', 'fas fa-handshake', '#contact', 2, 1, 'foreclosure_help', 'Expert guidance for communicating and negotiating with your lender', '2025-08-17 01:19:38', '2025-08-17 01:19:38', 'sql_direct_update'),
(3, 'Local Resource Assistance', 'We connect you with local Indiana resources to help with bills, utilities, food assistance, and other essential needs during this difficult time. Our team maintains an extensive network of community resources and can help you access programs you may not have known existed.', 'fas fa-map-marker-alt', '#contact', 3, 1, 'foreclosure_help', 'Connections to local Indiana resources for bills, utilities, and essential needs', '2025-08-17 01:19:38', '2025-08-17 01:19:38', 'sql_direct_update'),
(4, 'Moving & Housing Support', 'If staying in your current home isn\'t possible, we help you find safe, affordable housing options and provide support during the moving process. We understand this can be one of the most stressful parts of the foreclosure process, and we\'re here to help make it as smooth as possible.', 'fas fa-home', '#contact', 4, 1, 'foreclosure_help', 'Assistance finding safe housing and support during relocation', '2025-08-17 01:19:38', '2025-08-17 01:19:38', 'sql_direct_update'),
(5, 'Step-by-Step Guidance', 'We are with you every single step along the way! From the moment you contact us until your situation is resolved, we provide personal guidance and support. You\'ll never have to wonder what comes next or feel like you\'re facing this alone. We treat families the way we wish we had been treated during those hard days.', 'fas fa-route', '#contact', 5, 1, 'foreclosure_help', 'Personal guidance through every step of the foreclosure process', '2025-08-17 01:19:38', '2025-08-17 01:19:38', 'sql_direct_update'),
(6, 'Emotional Support & Understanding', 'We understand what you\'re going through because we\'ve been there too. Our team provides not just practical help, but emotional support during this challenging time. We believe that with a little faith, even the biggest burdens can become blessings, and we\'re here to help you see the light at the end of the tunnel.', 'fas fa-heart', '#contact', 6, 1, 'foreclosure_help', 'Compassionate support from people who understand your situation', '2025-08-17 01:19:38', '2025-08-17 01:19:38', 'sql_direct_update');

-- --------------------------------------------------------

--
-- Table structure for table `setting_content_stats`
--

CREATE TABLE `setting_content_stats` (
  `id` int(11) NOT NULL,
  `stat_value` varchar(20) NOT NULL,
  `stat_label` varchar(100) NOT NULL,
  `stat_order` int(11) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_content_stats`
--

INSERT INTO `setting_content_stats` (`id`, `stat_value`, `stat_label`, `stat_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, '250', 'Homes Sold', 1, 1, '2025-08-17 05:52:15', '2025-08-17 05:52:15'),
(2, '98', 'Client Satisfaction %', 2, 1, '2025-08-17 05:52:15', '2025-08-17 05:52:15'),
(3, '15', 'Years in Business', 3, 1, '2025-08-17 05:52:15', '2025-08-17 05:52:15'),
(4, '50', 'Million in Sales', 4, 1, '2025-08-17 05:52:15', '2025-08-17 05:52:15');

-- --------------------------------------------------------

--
-- Table structure for table `setting_content_team`
--

CREATE TABLE `setting_content_team` (
  `id` int(11) NOT NULL,
  `member_name` varchar(100) NOT NULL,
  `member_role` varchar(100) NOT NULL,
  `member_bio` text DEFAULT NULL,
  `member_image` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_content_team`
--

INSERT INTO `setting_content_team` (`id`, `member_name`, `member_role`, `member_bio`, `member_image`, `display_order`, `is_active`, `created_at`, `last_updated`) VALUES
(1, 'Elizabeth Riggs', 'CEO', 'I\'m the person whose voice you hear on the phone, the hand you shake and who personally oversees your experience with us.', 'assets/img/team/elizabeth-riggs.jpg', 1, 1, '2025-08-17 07:45:33', '2025-08-17 07:45:33'),
(2, 'Jon-David Riggs', 'Logistics Manager', 'I handle all of the paper-work and behind the scenes stuff. Some may say it\'s boring, but I know without the boring technical stuff, we can\'t help families the way they really deserve.', 'assets/img/team/jon-david-riggs.jpg', 2, 1, '2025-08-17 07:45:33', '2025-08-17 07:45:33'),
(3, 'Our Family', 'Purpose Coordinators', 'Appreciation of the blessings we have with our family, is the drive behind our purpose to positively impact other Indiana families, like ours.', 'assets/img/team/riggs-family.jpg', 3, 1, '2025-08-17 07:45:33', '2025-08-17 07:45:33');

-- --------------------------------------------------------

--
-- Table structure for table `setting_content_testimonials`
--

CREATE TABLE `setting_content_testimonials` (
  `id` int(11) NOT NULL,
  `client_name` varchar(100) NOT NULL,
  `client_role` varchar(100) NOT NULL,
  `testimonial_text` text NOT NULL,
  `client_image` varchar(255) DEFAULT NULL,
  `testimonial_order` int(11) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_content_testimonials`
--

INSERT INTO `setting_content_testimonials` (`id`, `client_name`, `client_role`, `testimonial_text`, `client_image`, `testimonial_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Sarah Johnson', 'First-Time Homebuyer', 'The team made my first home purchase incredibly smooth. They explained every step and found me the perfect mortgage rate. I couldn\'t be happier with my new home!', 'assets/img/testimonials/client-1.jpg', 1, 1, '2025-08-17 06:23:02', '2025-08-17 06:23:02'),
(2, 'Michael Chen', 'Real Estate Investor', 'I\'ve worked with many real estate professionals, but this team stands out. Their market knowledge and negotiation skills saved me thousands on my investment property.', 'assets/img/testimonials/client-2.jpg', 2, 1, '2025-08-17 06:23:02', '2025-08-17 06:23:02'),
(3, 'Lisa Rodriguez', 'Home Seller', 'Selling our family home was emotional, but they handled everything with such care and professionalism. We got above asking price and closed in just 3 weeks!', 'assets/img/testimonials/client-3.jpg', 3, 1, '2025-08-17 06:23:02', '2025-08-17 06:23:02'),
(4, 'David Thompson', 'Mortgage Refinance Client', 'The refinancing process was seamless. They secured me a rate 2% lower than my previous mortgage, saving me over $500 per month. Highly recommended!', 'assets/img/testimonials/client-4.jpg', 4, 1, '2025-08-17 06:23:02', '2025-08-17 06:23:02'),
(5, 'Jennifer Wilson', 'Luxury Home Buyer', 'Finding our dream home seemed impossible until we worked with this team. Their attention to detail and market expertise helped us find the perfect luxury property.', 'assets/img/testimonials/client-5.jpg', 5, 1, '2025-08-17 06:23:02', '2025-08-17 06:23:02'),
(6, 'Robert Martinez', 'Commercial Property Owner', 'Their commercial real estate division helped me acquire three properties for my business expansion. Professional, knowledgeable, and results-driven.', 'assets/img/testimonials/client-6.jpg', 6, 1, '2025-08-17 06:23:02', '2025-08-17 06:23:02');

-- --------------------------------------------------------

--
-- Table structure for table `setting_email_config`
--

CREATE TABLE `setting_email_config` (
  `id` int(11) NOT NULL,
  `mail_enabled` tinyint(1) DEFAULT 1,
  `mail_from` varchar(255) DEFAULT 'noreply@example.com',
  `mail_name` varchar(255) DEFAULT 'GWS Universal',
  `reply_to` varchar(255) DEFAULT NULL,
  `smtp_enabled` tinyint(1) DEFAULT 0,
  `smtp_host` varchar(255) DEFAULT NULL,
  `smtp_port` int(11) DEFAULT 587,
  `smtp_username` varchar(255) DEFAULT NULL,
  `smtp_password` varchar(255) DEFAULT NULL,
  `smtp_encryption` varchar(10) DEFAULT 'tls',
  `smtp_auth` tinyint(1) DEFAULT 1,
  `notifications_enabled` tinyint(1) DEFAULT 1,
  `notification_email` varchar(255) DEFAULT NULL,
  `auto_reply_enabled` tinyint(1) DEFAULT 1,
  `email_templates_path` varchar(255) DEFAULT 'assets/email_templates',
  `email_signature` text DEFAULT NULL,
  `bounce_handling` tinyint(1) DEFAULT 0,
  `email_tracking` tinyint(1) DEFAULT 0,
  `unsubscribe_enabled` tinyint(1) DEFAULT 1,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_email_config`
--

INSERT INTO `setting_email_config` (`id`, `mail_enabled`, `mail_from`, `mail_name`, `reply_to`, `smtp_enabled`, `smtp_host`, `smtp_port`, `smtp_username`, `smtp_password`, `smtp_encryption`, `smtp_auth`, `notifications_enabled`, `notification_email`, `auto_reply_enabled`, `email_templates_path`, `email_signature`, `bounce_handling`, `email_tracking`, `unsubscribe_enabled`, `last_updated`) VALUES
(1, 1, 'noreply@glitchwizardsolutions.com', 'GWS Universal', NULL, 0, NULL, 587, NULL, NULL, 'tls', 1, 1, NULL, 1, 'assets/email_templates', NULL, 0, 0, 1, '2025-08-15 21:00:35');

-- --------------------------------------------------------

--
-- Table structure for table `setting_events_config`
--

CREATE TABLE `setting_events_config` (
  `id` int(11) NOT NULL,
  `events_enabled` tinyint(1) DEFAULT 1,
  `public_events_enabled` tinyint(1) DEFAULT 1,
  `events_per_page` int(11) DEFAULT 12,
  `allow_public_registration` tinyint(1) DEFAULT 1,
  `require_approval` tinyint(1) DEFAULT 0,
  `send_confirmation_emails` tinyint(1) DEFAULT 1,
  `send_reminder_emails` tinyint(1) DEFAULT 1,
  `reminder_days_before` int(11) DEFAULT 1,
  `max_events_per_user` int(11) DEFAULT 0,
  `event_image_max_size` int(11) DEFAULT 5242880,
  `allowed_file_types` varchar(255) DEFAULT 'jpg,jpeg,png,gif,pdf,doc,docx',
  `default_event_duration` int(11) DEFAULT 60,
  `timezone` varchar(50) DEFAULT 'America/New_York',
  `date_format` varchar(20) DEFAULT 'Y-m-d',
  `time_format` varchar(10) DEFAULT 'H:i',
  `calendar_view_default` varchar(20) DEFAULT 'month',
  `enable_recurring_events` tinyint(1) DEFAULT 0,
  `enable_event_categories` tinyint(1) DEFAULT 1,
  `enable_event_ratings` tinyint(1) DEFAULT 1,
  `enable_event_comments` tinyint(1) DEFAULT 1,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `setting_events_config`
--

INSERT INTO `setting_events_config` (`id`, `events_enabled`, `public_events_enabled`, `events_per_page`, `allow_public_registration`, `require_approval`, `send_confirmation_emails`, `send_reminder_emails`, `reminder_days_before`, `max_events_per_user`, `event_image_max_size`, `allowed_file_types`, `default_event_duration`, `timezone`, `date_format`, `time_format`, `calendar_view_default`, `enable_recurring_events`, `enable_event_categories`, `enable_event_ratings`, `enable_event_comments`, `last_updated`) VALUES
(1, 1, 1, 12, 1, 0, 1, 1, 1, 0, 5242880, 'jpg,jpeg,png,gif,pdf,doc,docx', 60, 'America/New_York', 'Y-m-d', 'H:i', 'month', 0, 1, 1, 1, '2025-08-18 04:05:59');

-- --------------------------------------------------------

--
-- Table structure for table `setting_footer_special_links`
--

CREATE TABLE `setting_footer_special_links` (
  `id` int(11) NOT NULL,
  `link_key` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_footer_special_links`
--

INSERT INTO `setting_footer_special_links` (`id`, `link_key`, `title`, `url`, `icon`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'rss', 'RSS Feed', 'rss.php', 'fas fa-rss-square', 1, 1, '2025-08-17 08:52:26', '2025-08-17 09:09:50'),
(2, 'sitemap', 'XML Sitemap', 'sitemap.php', 'fas fa-sitemap', 2, 1, '2025-08-17 08:52:26', '2025-08-17 09:09:50'),
(3, 'accessibility_policy', 'Accessibility Policy', 'policy-accessibility.php', 'fas fa-universal-access', 3, 1, '2025-08-17 08:52:26', '2025-08-17 09:09:50'),
(4, 'terms_of_service', 'Terms of Service', 'policy-terms.php', 'fas fa-file-contract', 4, 1, '2025-08-17 08:52:26', '2025-08-17 09:09:51'),
(5, 'privacy_policy', 'Privacy Policy', 'policy-privacy.php', 'fas fa-user-shield', 5, 1, '2025-08-17 08:52:26', '2025-08-17 09:09:51');

-- --------------------------------------------------------

--
-- Table structure for table `setting_footer_useful_links`
--

CREATE TABLE `setting_footer_useful_links` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `icon` varchar(50) DEFAULT 'bi-link-45deg',
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_footer_useful_links`
--

INSERT INTO `setting_footer_useful_links` (`id`, `title`, `url`, `icon`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'About Us', 'about.php', 'bi-info-circle', 1, 1, '2025-08-17 08:52:26', '2025-08-17 09:09:50'),
(2, 'Reviews', 'index.php#testimonials', 'bi-star', 2, 1, '2025-08-17 08:52:26', '2025-08-17 09:09:50'),
(3, 'FAQs', 'faq.php', 'bi-question-circle', 3, 1, '2025-08-17 08:52:26', '2025-08-17 09:22:50'),
(4, 'Blog', 'blog.php', 'bi-folder', 4, 1, '2025-08-17 08:52:26', '2025-08-17 09:09:50'),
(5, 'Contact', 'index.php#contact', 'bi-envelope', 5, 1, '2025-08-17 08:52:26', '2025-08-17 09:09:50'),
(6, 'About Us', 'about.php', 'bi-info-circle', 1, 1, '2025-08-17 08:54:40', '2025-08-17 09:09:50'),
(7, 'Reviews', 'index.php#testimonials', 'bi-star', 2, 1, '2025-08-17 08:54:40', '2025-08-17 09:09:50'),
(8, 'FAQs', 'faq.php', 'bi-question-circle', 3, 1, '2025-08-17 08:54:40', '2025-08-17 09:22:50'),
(9, 'Blog', 'blog.php', 'bi-folder', 4, 1, '2025-08-17 08:54:40', '2025-08-17 09:09:50'),
(10, 'Contact', 'index.php#contact', 'bi-envelope', 5, 1, '2025-08-17 08:54:40', '2025-08-17 09:09:50');

-- --------------------------------------------------------

--
-- Table structure for table `setting_forms_config`
--

CREATE TABLE `setting_forms_config` (
  `setting_name` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('string','number','boolean','json','array') DEFAULT 'string',
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT 'general',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_forms_config`
--

INSERT INTO `setting_forms_config` (`setting_name`, `setting_value`, `setting_type`, `description`, `category`, `created_at`, `updated_at`) VALUES
('admin_notification_email', '', 'string', 'Default admin email for form notifications', 'notifications', '2025-08-18 14:58:22', '2025-08-18 14:58:22'),
('allowed_file_types', '[\"jpg\", \"jpeg\", \"png\", \"gif\", \"pdf\", \"doc\", \"docx\", \"txt\"]', 'json', 'Allowed file extensions for uploads', 'uploads', '2025-08-18 14:58:22', '2025-08-18 14:58:22'),
('auto_delete_submissions', '0', 'number', 'Auto delete submissions after X days (0 = never)', 'cleanup', '2025-08-18 14:58:22', '2025-08-18 14:58:22'),
('default_error_message', 'There was an error processing your submission. Please try again.', 'string', 'Default error message for forms', 'messages', '2025-08-18 14:58:22', '2025-08-18 14:58:22'),
('default_success_message', 'Thank you for your submission!', 'string', 'Default success message for forms', 'messages', '2025-08-18 14:58:22', '2025-08-18 14:58:22'),
('email_from_address', '', 'string', 'Default from address for email notifications', 'notifications', '2025-08-18 14:58:22', '2025-08-18 14:58:22'),
('email_from_name', 'Form System', 'string', 'Default from name for email notifications', 'notifications', '2025-08-18 14:58:22', '2025-08-18 14:58:22'),
('enable_form_analytics', 'true', 'boolean', 'Enable form analytics tracking', 'analytics', '2025-08-18 14:58:22', '2025-08-18 14:58:22'),
('forms_enabled', 'true', 'boolean', 'Enable or disable the forms system', 'general', '2025-08-18 14:58:22', '2025-08-18 14:58:22'),
('form_css_framework', 'bootstrap', 'string', 'CSS framework for form rendering', 'appearance', '2025-08-18 14:58:22', '2025-08-18 14:58:22'),
('honeypot_enabled', 'true', 'boolean', 'Enable honeypot spam protection', 'security', '2025-08-18 14:58:22', '2025-08-18 14:58:22'),
('max_file_size', '10485760', 'number', 'Maximum file upload size in bytes (10MB)', 'uploads', '2025-08-18 14:58:22', '2025-08-18 14:58:22'),
('rate_limiting', '5', 'number', 'Maximum submissions per IP per hour', 'security', '2025-08-18 14:58:22', '2025-08-18 14:58:22'),
('spam_protection', 'true', 'boolean', 'Enable spam protection features', 'security', '2025-08-18 14:58:22', '2025-08-18 14:58:22'),
('upload_directory', 'uploads/forms/', 'string', 'Directory for form file uploads', 'uploads', '2025-08-18 14:58:22', '2025-08-18 14:58:22');

-- --------------------------------------------------------

--
-- Table structure for table `setting_landing_pages_config`
--

CREATE TABLE `setting_landing_pages_config` (
  `setting_name` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('string','number','boolean','json','array') DEFAULT 'string',
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT 'general',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_landing_pages_config`
--

INSERT INTO `setting_landing_pages_config` (`setting_name`, `setting_value`, `setting_type`, `description`, `category`, `created_at`, `updated_at`) VALUES
('allowed_media_types', '[\"jpg\", \"jpeg\", \"png\", \"gif\", \"svg\", \"mp4\", \"webm\", \"pdf\"]', 'json', 'Allowed media file types', 'media', '2025-08-18 14:59:01', '2025-08-18 14:59:01'),
('auto_save_interval', '30', 'number', 'Auto-save interval in seconds', 'editor', '2025-08-18 14:59:01', '2025-08-18 14:59:01'),
('cache_duration', '3600', 'number', 'Cache duration in seconds', 'performance', '2025-08-18 14:59:01', '2025-08-18 14:59:01'),
('cache_enabled', 'true', 'boolean', 'Enable page caching for better performance', 'performance', '2025-08-18 14:59:01', '2025-08-18 14:59:01'),
('default_seo_description', 'Discover amazing products and services on our website.', 'string', 'Default SEO description', 'seo', '2025-08-18 14:59:01', '2025-08-18 14:59:01'),
('default_seo_title', 'Welcome to Our Website', 'string', 'Default SEO title for new pages', 'seo', '2025-08-18 14:59:01', '2025-08-18 14:59:01'),
('default_template', '1', 'number', 'Default template ID for new pages', 'general', '2025-08-18 14:59:01', '2025-08-18 14:59:01'),
('enable_ab_testing', 'true', 'boolean', 'Enable A/B testing features', 'testing', '2025-08-18 14:59:01', '2025-08-18 14:59:01'),
('enable_analytics', 'true', 'boolean', 'Enable built-in analytics tracking', 'analytics', '2025-08-18 14:59:01', '2025-08-18 14:59:01'),
('enable_custom_css', 'true', 'boolean', 'Allow custom CSS in pages', 'customization', '2025-08-18 14:59:01', '2025-08-18 14:59:01'),
('enable_custom_js', 'true', 'boolean', 'Allow custom JavaScript in pages', 'customization', '2025-08-18 14:59:01', '2025-08-18 14:59:01'),
('enable_revision_history', 'true', 'boolean', 'Enable page revision history', 'editor', '2025-08-18 14:59:01', '2025-08-18 14:59:01'),
('enable_schema_markup', 'true', 'boolean', 'Enable automatic schema markup', 'seo', '2025-08-18 14:59:01', '2025-08-18 14:59:01'),
('facebook_pixel_id', '', 'string', 'Facebook Pixel ID', 'analytics', '2025-08-18 14:59:01', '2025-08-18 14:59:01'),
('google_analytics_id', '', 'string', 'Google Analytics tracking ID', 'analytics', '2025-08-18 14:59:01', '2025-08-18 14:59:01'),
('landing_pages_enabled', 'true', 'boolean', 'Enable or disable the landing page system', 'general', '2025-08-18 14:59:01', '2025-08-18 14:59:01'),
('max_pages_per_user', '50', 'number', 'Maximum pages per user (0 = unlimited)', 'limits', '2025-08-18 14:59:01', '2025-08-18 14:59:01'),
('max_revisions', '10', 'number', 'Maximum revisions to keep per page', 'editor', '2025-08-18 14:59:01', '2025-08-18 14:59:01'),
('media_directory', 'uploads/landing-pages/', 'string', 'Directory for landing page media', 'media', '2025-08-18 14:59:01', '2025-08-18 14:59:01'),
('media_upload_max_size', '10485760', 'number', 'Maximum media upload size in bytes', 'media', '2025-08-18 14:59:01', '2025-08-18 14:59:01'),
('minify_html', 'true', 'boolean', 'Minify HTML output', 'performance', '2025-08-18 14:59:01', '2025-08-18 14:59:01');

-- --------------------------------------------------------

--
-- Table structure for table `setting_payment_config`
--

CREATE TABLE `setting_payment_config` (
  `id` int(11) NOT NULL,
  `pay_on_delivery_enabled` tinyint(1) DEFAULT 1,
  `paypal_enabled` tinyint(1) DEFAULT 1,
  `paypal_email` varchar(255) DEFAULT NULL,
  `paypal_testmode` tinyint(1) DEFAULT 1,
  `paypal_currency` varchar(10) DEFAULT 'USD',
  `paypal_ipn_url` varchar(255) DEFAULT NULL,
  `paypal_cancel_url` varchar(255) DEFAULT NULL,
  `paypal_return_url` varchar(255) DEFAULT NULL,
  `stripe_enabled` tinyint(1) DEFAULT 1,
  `stripe_publish_key` varchar(255) DEFAULT NULL,
  `stripe_secret_key` varchar(255) DEFAULT NULL,
  `stripe_currency` varchar(10) DEFAULT 'USD',
  `stripe_webhook_secret` varchar(255) DEFAULT NULL,
  `coinbase_enabled` tinyint(1) DEFAULT 0,
  `coinbase_api_key` varchar(255) DEFAULT NULL,
  `coinbase_secret` varchar(255) DEFAULT NULL,
  `default_currency` varchar(10) DEFAULT 'USD',
  `accepted_currencies` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`accepted_currencies`)),
  `payment_timeout` int(11) DEFAULT 1800,
  `payment_confirmation_page` varchar(255) DEFAULT NULL,
  `failed_payment_redirect` varchar(255) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_performance_config`
--

CREATE TABLE `setting_performance_config` (
  `id` int(11) NOT NULL,
  `performance_monitoring` tinyint(1) DEFAULT 1,
  `slow_query_threshold` decimal(5,3) DEFAULT 1.000,
  `memory_limit_mb` int(11) DEFAULT 256,
  `execution_time_limit` int(11) DEFAULT 30,
  `compression_enabled` tinyint(1) DEFAULT 1,
  `minification_enabled` tinyint(1) DEFAULT 1,
  `css_minification` tinyint(1) DEFAULT 1,
  `js_minification` tinyint(1) DEFAULT 1,
  `image_optimization` tinyint(1) DEFAULT 1,
  `lazy_loading_enabled` tinyint(1) DEFAULT 1,
  `cdn_enabled` tinyint(1) DEFAULT 0,
  `cdn_url` varchar(255) DEFAULT NULL,
  `browser_caching_enabled` tinyint(1) DEFAULT 1,
  `cache_control_headers` tinyint(1) DEFAULT 1,
  `gzip_compression` tinyint(1) DEFAULT 1,
  `resource_bundling` tinyint(1) DEFAULT 1,
  `performance_budget_kb` int(11) DEFAULT 2048,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_portal_config`
--

CREATE TABLE `setting_portal_config` (
  `id` int(11) NOT NULL,
  `site_name` varchar(255) DEFAULT 'Client Portal',
  `company_name` varchar(255) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT 'assets/img/logo.png',
  `favicon_path` varchar(255) DEFAULT 'assets/img/favicon.png',
  `tagline` varchar(255) DEFAULT NULL,
  `theme_color` varchar(7) DEFAULT '#4154f1',
  `default_language` varchar(10) DEFAULT 'en',
  `timezone` varchar(50) DEFAULT 'America/New_York',
  `date_format` varchar(50) DEFAULT 'Y-m-d',
  `currency` varchar(10) DEFAULT 'USD',
  `enable_blog` tinyint(1) DEFAULT 1,
  `enable_chat` tinyint(1) DEFAULT 0,
  `enable_events` tinyint(4) DEFAULT 0,
  `maintenance_mode` tinyint(1) DEFAULT 0,
  `upload_dir` varchar(255) DEFAULT '/uploads/',
  `max_upload_size` int(11) DEFAULT 10485760,
  `session_timeout` int(11) DEFAULT 7200,
  `dashboard_widgets` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dashboard_widgets`)),
  `menu_structure` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`menu_structure`)),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_security_config`
--

CREATE TABLE `setting_security_config` (
  `id` int(11) NOT NULL,
  `csrf_protection` tinyint(1) DEFAULT 1,
  `sql_injection_protection` tinyint(1) DEFAULT 1,
  `xss_protection` tinyint(1) DEFAULT 1,
  `rate_limiting_enabled` tinyint(1) DEFAULT 1,
  `max_requests_per_minute` int(11) DEFAULT 60,
  `ip_whitelist` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ip_whitelist`)),
  `ip_blacklist` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ip_blacklist`)),
  `password_encryption` varchar(50) DEFAULT 'bcrypt',
  `encryption_key` varchar(255) DEFAULT NULL,
  `api_rate_limit` int(11) DEFAULT 1000,
  `api_rate_window` int(11) DEFAULT 3600,
  `file_upload_scanning` tinyint(1) DEFAULT 1,
  `admin_ip_restriction` tinyint(1) DEFAULT 0,
  `admin_allowed_ips` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`admin_allowed_ips`)),
  `login_attempts_tracking` tinyint(1) DEFAULT 1,
  `suspicious_activity_logging` tinyint(1) DEFAULT 1,
  `two_factor_authentication` tinyint(1) DEFAULT 0,
  `session_security_level` varchar(20) DEFAULT 'high',
  `password_history_length` int(11) DEFAULT 5,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_seo_global`
--

CREATE TABLE `setting_seo_global` (
  `id` int(11) NOT NULL,
  `default_title_suffix` varchar(255) DEFAULT ' | GWS Universal',
  `default_meta_description` text DEFAULT 'Professional business solutions and services',
  `default_meta_keywords` varchar(500) DEFAULT NULL,
  `canonical_domain` varchar(255) DEFAULT NULL,
  `google_analytics_id` varchar(50) DEFAULT NULL,
  `google_tag_manager_id` varchar(50) DEFAULT NULL,
  `facebook_pixel_id` varchar(50) DEFAULT NULL,
  `google_site_verification` varchar(255) DEFAULT NULL,
  `bing_site_verification` varchar(255) DEFAULT NULL,
  `yandex_site_verification` varchar(255) DEFAULT NULL,
  `robots_txt_content` text DEFAULT NULL,
  `sitemap_enabled` tinyint(1) DEFAULT 1,
  `sitemap_priority` decimal(2,1) DEFAULT 0.8,
  `sitemap_changefreq` varchar(20) DEFAULT 'weekly',
  `open_graph_type` varchar(50) DEFAULT 'website',
  `twitter_card_type` varchar(50) DEFAULT 'summary_large_image',
  `schema_org_type` varchar(100) DEFAULT 'Organization',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_seo_pages`
--

CREATE TABLE `setting_seo_pages` (
  `id` int(11) NOT NULL,
  `page_slug` varchar(255) NOT NULL,
  `page_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` varchar(500) DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL,
  `og_title` varchar(255) DEFAULT NULL,
  `og_description` text DEFAULT NULL,
  `og_image` varchar(255) DEFAULT NULL,
  `og_type` varchar(50) DEFAULT 'website',
  `twitter_title` varchar(255) DEFAULT NULL,
  `twitter_description` text DEFAULT NULL,
  `twitter_image` varchar(255) DEFAULT NULL,
  `noindex` tinyint(1) DEFAULT 0,
  `nofollow` tinyint(1) DEFAULT 0,
  `schema_markup` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`schema_markup`)),
  `custom_head_code` text DEFAULT NULL,
  `priority` decimal(2,1) DEFAULT 0.5,
  `changefreq` varchar(20) DEFAULT 'monthly',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_shop_config`
--

CREATE TABLE `setting_shop_config` (
  `id` int(11) NOT NULL,
  `site_name` varchar(255) DEFAULT 'Shopping Cart',
  `currency_code` varchar(10) DEFAULT '$',
  `currency_symbol` varchar(5) DEFAULT '$',
  `featured_image` varchar(255) DEFAULT 'uploads/featured-image.jpg',
  `default_payment_status` varchar(50) DEFAULT 'Completed',
  `account_required` tinyint(1) DEFAULT 0,
  `weight_unit` varchar(10) DEFAULT 'lbs',
  `rewrite_url` tinyint(1) DEFAULT 0,
  `template_editor` varchar(50) DEFAULT 'tinymce',
  `products_per_page` int(11) DEFAULT 12,
  `low_stock_threshold` int(11) DEFAULT 5,
  `out_of_stock_action` varchar(50) DEFAULT 'hide',
  `tax_enabled` tinyint(1) DEFAULT 0,
  `tax_rate` decimal(5,4) DEFAULT 0.0000,
  `shipping_enabled` tinyint(1) DEFAULT 1,
  `free_shipping_threshold` decimal(10,2) DEFAULT 0.00,
  `inventory_tracking` tinyint(1) DEFAULT 1,
  `reviews_enabled` tinyint(1) DEFAULT 1,
  `wishlist_enabled` tinyint(1) DEFAULT 1,
  `coupon_system_enabled` tinyint(1) DEFAULT 1,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_social_media`
--

CREATE TABLE `setting_social_media` (
  `id` int(11) NOT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `twitter_url` varchar(255) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `youtube_url` varchar(255) DEFAULT NULL,
  `tiktok_url` varchar(255) DEFAULT NULL,
  `pinterest_url` varchar(255) DEFAULT NULL,
  `snapchat_url` varchar(255) DEFAULT NULL,
  `discord_url` varchar(255) DEFAULT NULL,
  `github_url` varchar(255) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `blog_url` varchar(255) DEFAULT NULL,
  `shop_url` varchar(255) DEFAULT NULL,
  `booking_url` varchar(255) DEFAULT NULL,
  `calendar_url` varchar(255) DEFAULT NULL,
  `review_platforms` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`review_platforms`)),
  `social_handles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`social_handles`)),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_system_audit`
--

CREATE TABLE `setting_system_audit` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(255) NOT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `changed_by` varchar(100) DEFAULT NULL,
  `change_reason` varchar(255) DEFAULT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_system_audit`
--

INSERT INTO `setting_system_audit` (`id`, `setting_key`, `old_value`, `new_value`, `changed_by`, `change_reason`, `changed_at`) VALUES
(1, 'business_identity.author', 'GWS', 'Elizabeth Riggs', 'GlitchWizard', 'Business identity update', '2025-08-16 01:59:33'),
(2, 'business_identity.business_name_short', 'Burden2Blessings', 'Burden2Blessings Short', 'GlitchWizard', 'Business identity update', '2025-08-16 02:00:09'),
(3, 'business_identity.business_name_medium', 'Burden to Blessings', 'Burden to Blessings Medium', 'GlitchWizard', 'Business identity update', '2025-08-16 02:00:09'),
(4, 'business_identity.business_name_long', 'Burden to Blessings LLC', 'Burden to Blessings LLC Longer Business Name', 'GlitchWizard', 'Business identity update', '2025-08-16 02:00:09'),
(5, 'business_identity.business_tagline_short', 'Short Tagline', 'Short Tagline1', 'GlitchWizard', 'Business identity update', '2025-08-16 02:00:29'),
(6, 'business_identity.business_tagline_medium', 'Medium tagline for hero sections', 'Medium tagline for hero sections2', 'GlitchWizard', 'Business identity update', '2025-08-16 02:00:29'),
(7, 'business_identity.business_tagline_long', 'Longer tagline That spans at least one line and is larger than the medium and the small, of course.', 'Longer tagline That spans at least one line and is larger than the medium and the small, of course.3', 'GlitchWizard', 'Business identity update', '2025-08-16 02:00:29'),
(8, 'business_identity.author', 'Elizabeth Riggs', '4', 'GlitchWizard', 'Business identity update', '2025-08-16 02:00:29'),
(9, 'business_identity.business_name_short', 'Burden2Blessings Short', 'Burden2Blessings Short2', 'GlitchWizard', 'Business identity update', '2025-08-16 02:00:44'),
(10, 'business_identity.business_name_medium', 'Burden to Blessings Medium', 'Burden to Blessings Medium2', 'GlitchWizard', 'Business identity update', '2025-08-16 02:00:44'),
(11, 'business_identity.business_name_long', 'Burden to Blessings LLC Longer Business Name', 'Burden to Blessings LLC Longer Business2 Name', 'GlitchWizard', 'Business identity update', '2025-08-16 02:00:44'),
(12, 'business_identity.author', '4', '', 'GlitchWizard', 'Business identity update', '2025-08-16 02:00:44'),
(13, 'branding_colors.brand_accent_color', '#28a745', '#19f0c5', 'GlitchWizard', 'Brand colors update', '2025-08-16 02:02:11'),
(14, 'branding_colors.brand_danger_color', '#ff0505', '#dc3545', 'GlitchWizard', 'Brand colors update', '2025-08-16 02:02:11'),
(15, 'branding_colors.brand_info_color', '#17a2b8', '#6dcbd9', 'GlitchWizard', 'Brand colors update', '2025-08-16 02:03:21'),
(16, 'branding_colors.brand_info_color', '#6dcbd9', '#17a2b8', 'GlitchWizard', 'Brand colors update', '2025-08-16 02:03:34'),
(17, 'branding_colors.brand_background_color', '#ffffff', '#7d3636', 'GlitchWizard', 'Brand colors update', '2025-08-16 02:03:34'),
(18, 'branding_colors.brand_danger_color', '#dc3545', '#ff0f27', 'GlitchWizard', 'Brand colors update', '2025-08-16 02:04:08'),
(19, 'branding_colors.brand_background_color', '#7d3636', '#ffffff', 'GlitchWizard', 'Brand colors update', '2025-08-16 02:04:08'),
(20, 'branding_colors.brand_primary_color', '#ed6f45', '#ff5b24', 'GlitchWizard', 'Brand colors update', '2025-08-16 02:04:34'),
(21, 'branding_colors.brand_danger_color', '#ff0f27', '#dc3545', 'GlitchWizard', 'Brand colors update', '2025-08-16 02:04:34'),
(22, 'branding_colors.brand_danger_color', '#dc3545', '#ff0f27', 'GlitchWizard', 'Brand colors update', '2025-08-16 02:05:03'),
(23, 'contact_info.contact_phone', '+1 555-123-4567', '+1 850-123-4567', 'GlitchWizard', 'Contact information update', '2025-08-16 02:07:29'),
(24, 'contact_info.contact_address', '123 Main Street', '127 Northwood Road', 'GlitchWizard', 'Contact information update', '2025-08-16 02:07:29'),
(25, 'branding_colors.brand_primary_color', '#ff5b24', '#ed6f45', 'GlitchWizard', 'Brand colors update', '2025-08-16 02:08:42'),
(26, 'branding_colors.brand_danger_color', '#ff0f27', '#dc3545', 'GlitchWizard', 'Brand colors update', '2025-08-16 02:09:18'),
(27, 'branding_colors.brand_text_muted', '#999999', '#b0b0b0', 'GlitchWizard', 'Brand colors update', '2025-08-16 02:09:18'),
(28, 'business_identity.author', '', 'Test Author 22:15:20', 'debug_test', 'Business identity update', '2025-08-16 02:15:20'),
(29, 'business_identity.author', 'GlitchWizard Solutions LLC', 'Test Author 19:59:03', 'debug_test', 'Business identity update', '2025-08-16 23:59:03'),
(30, 'business_identity.business_name_short', 'Burden2Blessings', 'B2B Home Solutions', 'content_migration', 'Business identity update', '2025-08-17 00:05:35'),
(31, 'business_identity.business_name_long', 'Burden to Blessings LLC', 'Burden to Blessings Home Solutions', 'content_migration', 'Business identity update', '2025-08-17 00:05:35'),
(32, 'contact_info.contact_phone', '+1 850-123-4567', '(574) 555-0123', 'content_migration', 'Contact information update', '2025-08-17 00:05:35'),
(33, 'contact_info.contact_email', 'barbara@glitchwizardsolutions.com', 'help@burdentoblessings.com', 'content_migration', 'Contact information update', '2025-08-17 00:05:35'),
(34, 'contact_info.contact_address', '127 Northwood Road', '123 Main Street', 'content_migration', 'Contact information update', '2025-08-17 00:05:35'),
(35, 'contact_info.contact_city', 'Crawfordville', 'Elkhart', 'content_migration', 'Contact information update', '2025-08-17 00:05:35'),
(36, 'contact_info.contact_state', 'FL', 'Indiana', 'content_migration', 'Contact information update', '2025-08-17 00:05:35'),
(37, 'business_identity.business_name_short', 'Burden to Blessings', 'B2B Home Solutions', 'admin', 'Business identity update', '2025-08-17 00:59:45'),
(38, 'business_identity.business_name_long', 'Burden to Blessings - Transforming Lives Through Faith', 'Burden to Blessings Home Solutions', 'admin', 'Business identity update', '2025-08-17 00:59:45'),
(39, 'business_identity.business_tagline_long', 'We handle the complexities of the foreclosure process, so you can focus on what\'s next.', 'We handle the complexities of the foreclosure process, so you can focus on what&#039;s next.', 'GlitchWizard', 'Business identity update', '2025-08-17 17:31:20'),
(40, 'business_identity.author', 'Test Author 19:59:03', 'GlitchWizard Solutions LLC', 'GlitchWizard', 'Business identity update', '2025-08-17 17:31:20');

-- --------------------------------------------------------

--
-- Table structure for table `setting_system_config`
--

CREATE TABLE `setting_system_config` (
  `id` int(11) NOT NULL,
  `environment` varchar(50) DEFAULT 'production',
  `debug_mode` tinyint(1) DEFAULT 0,
  `maintenance_mode` tinyint(1) DEFAULT 0,
  `maintenance_message` text DEFAULT 'Site is currently under maintenance. Please check back later.',
  `timezone` varchar(50) DEFAULT 'America/New_York',
  `default_language` varchar(10) DEFAULT 'en',
  `date_format` varchar(50) DEFAULT 'Y-m-d',
  `time_format` varchar(50) DEFAULT 'H:i:s',
  `pagination_limit` int(11) DEFAULT 25,
  `file_upload_limit` int(11) DEFAULT 10485760,
  `allowed_file_types` varchar(500) DEFAULT 'jpg,jpeg,png,gif,pdf,doc,docx',
  `cache_enabled` tinyint(1) DEFAULT 1,
  `cache_duration` int(11) DEFAULT 3600,
  `logging_enabled` tinyint(1) DEFAULT 1,
  `log_level` varchar(20) DEFAULT 'info',
  `error_reporting_level` int(11) DEFAULT 1,
  `backup_enabled` tinyint(1) DEFAULT 1,
  `backup_frequency` varchar(20) DEFAULT 'daily',
  `backup_retention_days` int(11) DEFAULT 30,
  `auto_updates_enabled` tinyint(1) DEFAULT 0,
  `version` varchar(20) DEFAULT '1.0.0',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_system_config`
--

INSERT INTO `setting_system_config` (`id`, `environment`, `debug_mode`, `maintenance_mode`, `maintenance_message`, `timezone`, `default_language`, `date_format`, `time_format`, `pagination_limit`, `file_upload_limit`, `allowed_file_types`, `cache_enabled`, `cache_duration`, `logging_enabled`, `log_level`, `error_reporting_level`, `backup_enabled`, `backup_frequency`, `backup_retention_days`, `auto_updates_enabled`, `version`, `last_updated`) VALUES
(1, 'production', 0, 0, 'Site is currently under maintenance. Please check back later.', 'America/New_York', 'en', 'Y-m-d', 'H:i:s', 25, 10485760, 'jpg,jpeg,png,gif,pdf,doc,docx', 1, 3600, 1, 'info', 1, 1, 'daily', 30, 0, '1.0.0', '2025-08-15 21:00:35');

-- --------------------------------------------------------

--
-- Table structure for table `setting_system_metadata`
--

CREATE TABLE `setting_system_metadata` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(255) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `subcategory` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `data_type` enum('string','text','integer','boolean','json','array','file_path','url','email','color','font') NOT NULL,
  `is_required` tinyint(1) DEFAULT 0,
  `default_value` text DEFAULT NULL,
  `validation_rules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`validation_rules`)),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `settings_status`
--
ALTER TABLE `settings_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `settings_file` (`settings_file`),
  ADD KEY `is_configured` (`is_configured`),
  ADD KEY `is_complete` (`is_complete`);

--
-- Indexes for table `setting_accounts_config`
--
ALTER TABLE `setting_accounts_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_analytics_config`
--
ALTER TABLE `setting_analytics_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_app_configurations`
--
ALTER TABLE `setting_app_configurations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_app_config` (`app_name`,`section`,`config_key`),
  ADD KEY `idx_app_section` (`app_name`,`section`),
  ADD KEY `idx_display_group` (`display_group`),
  ADD KEY `idx_sensitive` (`is_sensitive`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_config_lookup` (`app_name`,`section`,`config_key`,`is_active`),
  ADD KEY `idx_admin_display` (`app_name`,`display_group`,`display_order`);

--
-- Indexes for table `setting_app_configurations_audit`
--
ALTER TABLE `setting_app_configurations_audit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_config_id` (`config_id`),
  ADD KEY `idx_app_name` (`app_name`),
  ADD KEY `idx_change_type` (`change_type`),
  ADD KEY `idx_changed_by` (`changed_by`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `setting_app_configurations_cache`
--
ALTER TABLE `setting_app_configurations_cache`
  ADD PRIMARY KEY (`cache_key`),
  ADD KEY `idx_app_name` (`app_name`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- Indexes for table `setting_blog_comments`
--
ALTER TABLE `setting_blog_comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_blog_config`
--
ALTER TABLE `setting_blog_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_blog_display`
--
ALTER TABLE `setting_blog_display`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_blog_features`
--
ALTER TABLE `setting_blog_features`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_blog_identity`
--
ALTER TABLE `setting_blog_identity`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_blog_seo`
--
ALTER TABLE `setting_blog_seo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_blog_social`
--
ALTER TABLE `setting_blog_social`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_branding_assets`
--
ALTER TABLE `setting_branding_assets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_branding_colors`
--
ALTER TABLE `setting_branding_colors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_setting_branding_colors_primary` (`brand_primary_color`,`brand_secondary_color`);

--
-- Indexes for table `setting_branding_fonts`
--
ALTER TABLE `setting_branding_fonts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_branding_templates`
--
ALTER TABLE `setting_branding_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `template_key` (`template_key`);

--
-- Indexes for table `setting_business_contact`
--
ALTER TABLE `setting_business_contact`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_identity_id` (`business_identity_id`);

--
-- Indexes for table `setting_business_identity`
--
ALTER TABLE `setting_business_identity`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_setting_business_identity_names` (`business_name_short`,`business_name_medium`);

--
-- Indexes for table `setting_chat_config`
--
ALTER TABLE `setting_chat_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_contact_config`
--
ALTER TABLE `setting_contact_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_contact_info`
--
ALTER TABLE `setting_contact_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_content_clients`
--
ALTER TABLE `setting_content_clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_content_features`
--
ALTER TABLE `setting_content_features`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `feature_key` (`feature_key`),
  ADD KEY `idx_category` (`feature_category`),
  ADD KEY `idx_order` (`feature_order`),
  ADD KEY `idx_setting_content_features_category_order` (`feature_category`,`feature_order`,`is_active`);

--
-- Indexes for table `setting_content_homepage`
--
ALTER TABLE `setting_content_homepage`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_content_portfolio`
--
ALTER TABLE `setting_content_portfolio`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_content_pricing`
--
ALTER TABLE `setting_content_pricing`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `plan_key` (`plan_key`),
  ADD KEY `idx_plan_category` (`plan_category`),
  ADD KEY `idx_plan_order` (`plan_order`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `idx_pricing_featured` (`is_featured`,`plan_order`),
  ADD KEY `idx_pricing_popular` (`is_popular`,`plan_order`),
  ADD KEY `idx_pricing_active_order` (`is_active`,`plan_order`);

--
-- Indexes for table `setting_content_services`
--
ALTER TABLE `setting_content_services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_content_stats`
--
ALTER TABLE `setting_content_stats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_content_team`
--
ALTER TABLE `setting_content_team`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_content_testimonials`
--
ALTER TABLE `setting_content_testimonials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_email_config`
--
ALTER TABLE `setting_email_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_events_config`
--
ALTER TABLE `setting_events_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_footer_special_links`
--
ALTER TABLE `setting_footer_special_links`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `link_key` (`link_key`);

--
-- Indexes for table `setting_footer_useful_links`
--
ALTER TABLE `setting_footer_useful_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_forms_config`
--
ALTER TABLE `setting_forms_config`
  ADD PRIMARY KEY (`setting_name`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `setting_landing_pages_config`
--
ALTER TABLE `setting_landing_pages_config`
  ADD PRIMARY KEY (`setting_name`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `setting_payment_config`
--
ALTER TABLE `setting_payment_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_performance_config`
--
ALTER TABLE `setting_performance_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_portal_config`
--
ALTER TABLE `setting_portal_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_security_config`
--
ALTER TABLE `setting_security_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_seo_global`
--
ALTER TABLE `setting_seo_global`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_seo_pages`
--
ALTER TABLE `setting_seo_pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_slug` (`page_slug`),
  ADD KEY `idx_slug` (`page_slug`),
  ADD KEY `idx_setting_seo_pages_slug` (`page_slug`,`noindex`);

--
-- Indexes for table `setting_shop_config`
--
ALTER TABLE `setting_shop_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_social_media`
--
ALTER TABLE `setting_social_media`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_system_audit`
--
ALTER TABLE `setting_system_audit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_setting` (`setting_key`),
  ADD KEY `idx_date` (`changed_at`);

--
-- Indexes for table `setting_system_config`
--
ALTER TABLE `setting_system_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_system_metadata`
--
ALTER TABLE `setting_system_metadata`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_table` (`table_name`),
  ADD KEY `idx_key` (`setting_key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `settings_status`
--
ALTER TABLE `settings_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_accounts_config`
--
ALTER TABLE `setting_accounts_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_analytics_config`
--
ALTER TABLE `setting_analytics_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_app_configurations`
--
ALTER TABLE `setting_app_configurations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- AUTO_INCREMENT for table `setting_app_configurations_audit`
--
ALTER TABLE `setting_app_configurations_audit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `setting_blog_comments`
--
ALTER TABLE `setting_blog_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `setting_blog_config`
--
ALTER TABLE `setting_blog_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_blog_display`
--
ALTER TABLE `setting_blog_display`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `setting_blog_features`
--
ALTER TABLE `setting_blog_features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `setting_blog_identity`
--
ALTER TABLE `setting_blog_identity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `setting_blog_seo`
--
ALTER TABLE `setting_blog_seo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `setting_blog_social`
--
ALTER TABLE `setting_blog_social`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `setting_branding_assets`
--
ALTER TABLE `setting_branding_assets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `setting_branding_colors`
--
ALTER TABLE `setting_branding_colors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `setting_branding_fonts`
--
ALTER TABLE `setting_branding_fonts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `setting_branding_templates`
--
ALTER TABLE `setting_branding_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `setting_business_contact`
--
ALTER TABLE `setting_business_contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `setting_business_identity`
--
ALTER TABLE `setting_business_identity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `setting_chat_config`
--
ALTER TABLE `setting_chat_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `setting_contact_config`
--
ALTER TABLE `setting_contact_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_contact_info`
--
ALTER TABLE `setting_contact_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `setting_content_clients`
--
ALTER TABLE `setting_content_clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `setting_content_features`
--
ALTER TABLE `setting_content_features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_content_homepage`
--
ALTER TABLE `setting_content_homepage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `setting_content_portfolio`
--
ALTER TABLE `setting_content_portfolio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `setting_content_pricing`
--
ALTER TABLE `setting_content_pricing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `setting_content_services`
--
ALTER TABLE `setting_content_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `setting_content_stats`
--
ALTER TABLE `setting_content_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `setting_content_team`
--
ALTER TABLE `setting_content_team`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `setting_content_testimonials`
--
ALTER TABLE `setting_content_testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `setting_email_config`
--
ALTER TABLE `setting_email_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `setting_events_config`
--
ALTER TABLE `setting_events_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `setting_footer_special_links`
--
ALTER TABLE `setting_footer_special_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `setting_footer_useful_links`
--
ALTER TABLE `setting_footer_useful_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `setting_payment_config`
--
ALTER TABLE `setting_payment_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_performance_config`
--
ALTER TABLE `setting_performance_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_portal_config`
--
ALTER TABLE `setting_portal_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_security_config`
--
ALTER TABLE `setting_security_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_seo_global`
--
ALTER TABLE `setting_seo_global`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_seo_pages`
--
ALTER TABLE `setting_seo_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_shop_config`
--
ALTER TABLE `setting_shop_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_social_media`
--
ALTER TABLE `setting_social_media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_system_audit`
--
ALTER TABLE `setting_system_audit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `setting_system_config`
--
ALTER TABLE `setting_system_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `setting_system_metadata`
--
ALTER TABLE `setting_system_metadata`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `setting_app_configurations_audit`
--
ALTER TABLE `setting_app_configurations_audit`
  ADD CONSTRAINT `fk_config_audit` FOREIGN KEY (`config_id`) REFERENCES `setting_app_configurations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `setting_business_contact`
--
ALTER TABLE `setting_business_contact`
  ADD CONSTRAINT `setting_business_contact_ibfk_1` FOREIGN KEY (`business_identity_id`) REFERENCES `setting_business_identity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
