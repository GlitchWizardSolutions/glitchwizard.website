
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
