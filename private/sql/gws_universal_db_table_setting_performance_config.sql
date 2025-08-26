
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
