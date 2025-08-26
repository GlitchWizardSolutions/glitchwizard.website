
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
