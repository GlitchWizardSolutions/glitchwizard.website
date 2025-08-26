
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
