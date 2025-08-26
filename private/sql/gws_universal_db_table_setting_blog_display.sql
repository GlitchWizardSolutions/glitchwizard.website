
-- --------------------------------------------------------

--
-- Table structure for table `setting_blog_display`
--

CREATE TABLE `setting_blog_display` (
  `id` int(11) NOT NULL,
  `posts_per_page` int(11) DEFAULT 10,
  `excerpt_length` int(11) DEFAULT 250,
  `date_format` varchar(50) DEFAULT 'F j, Y',
  `layout` enum('Wide','Boxed','Sidebar') DEFAULT 'Wide',
  `sidebar_position` enum('Left','Right','None') DEFAULT 'Right',
  `posts_per_row` int(11) DEFAULT 2,
  `theme` varchar(100) DEFAULT 'Default',
  `enable_featured_image` tinyint(1) DEFAULT 1,
  `thumbnail_width` int(11) DEFAULT 300,
  `thumbnail_height` int(11) DEFAULT 200,
  `background_image` text DEFAULT '',
  `custom_css` text DEFAULT '',
  `show_author` tinyint(1) DEFAULT 1,
  `show_date` tinyint(1) DEFAULT 1,
  `show_categories` tinyint(1) DEFAULT 1,
  `show_tags` tinyint(1) DEFAULT 1,
  `show_excerpt` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
