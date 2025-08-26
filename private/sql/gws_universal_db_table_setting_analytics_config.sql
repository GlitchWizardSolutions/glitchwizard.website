
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
