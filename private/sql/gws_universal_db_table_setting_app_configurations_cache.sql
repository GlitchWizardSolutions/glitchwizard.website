
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
