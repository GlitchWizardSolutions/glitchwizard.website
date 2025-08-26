
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
