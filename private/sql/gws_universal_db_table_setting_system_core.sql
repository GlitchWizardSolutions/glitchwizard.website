
-- --------------------------------------------------------

--
-- Table structure for table `setting_system_core`
--

CREATE TABLE `setting_system_core` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `maintenance_mode` tinyint(1) NOT NULL DEFAULT 0,
  `default_timezone` varchar(64) DEFAULT 'UTC',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
