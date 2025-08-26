
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
