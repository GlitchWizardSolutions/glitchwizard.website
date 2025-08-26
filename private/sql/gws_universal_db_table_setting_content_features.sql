
-- --------------------------------------------------------

--
-- Table structure for table `setting_content_features`
--

CREATE TABLE `setting_content_features` (
  `id` int(11) NOT NULL,
  `feature_key` varchar(100) NOT NULL,
  `feature_title` varchar(255) NOT NULL,
  `feature_description` text DEFAULT NULL,
  `feature_icon` varchar(255) DEFAULT NULL,
  `feature_image` varchar(255) DEFAULT NULL,
  `feature_category` varchar(100) DEFAULT NULL,
  `feature_order` int(11) DEFAULT 0,
  `is_highlighted` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
