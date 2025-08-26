
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
