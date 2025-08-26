
-- --------------------------------------------------------

--
-- Table structure for table `section_templates`
--

CREATE TABLE `section_templates` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `section_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `preview_image` varchar(500) DEFAULT NULL,
  `template_html` text NOT NULL,
  `template_css` text DEFAULT NULL,
  `template_js` text DEFAULT NULL,
  `default_content` text DEFAULT NULL,
  `settings_schema` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `is_premium` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `usage_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
