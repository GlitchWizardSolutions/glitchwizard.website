
-- --------------------------------------------------------

--
-- Table structure for table `landing_page_templates`
--

CREATE TABLE `landing_page_templates` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `preview_image` varchar(500) DEFAULT NULL,
  `template_structure` text NOT NULL,
  `default_content` text DEFAULT NULL,
  `css_framework` varchar(50) DEFAULT 'bootstrap',
  `is_premium` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `usage_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
