
-- --------------------------------------------------------

--
-- Table structure for table `setting_blog_config`
--

CREATE TABLE `setting_blog_config` (
  `id` int(11) NOT NULL,
  `blog_site_url` varchar(255) DEFAULT NULL,
  `sitename` varchar(255) DEFAULT 'GWS Blog',
  `blog_description` text DEFAULT 'Latest news and insights',
  `blog_email` varchar(255) DEFAULT NULL,
  `posts_per_page` int(11) DEFAULT 10,
  `comments_enabled` varchar(20) DEFAULT 'guests',
  `date_format` varchar(50) DEFAULT 'F j, Y',
  `layout` varchar(50) DEFAULT 'Wide',
  `sidebar_position` varchar(20) DEFAULT 'Right',
  `posts_per_row` int(11) DEFAULT 2,
  `theme` varchar(100) DEFAULT 'Pulse',
  `background_image` varchar(255) DEFAULT NULL,
  `featured_posts_count` int(11) DEFAULT 5,
  `excerpt_length` int(11) DEFAULT 150,
  `read_more_text` varchar(100) DEFAULT 'Read More',
  `author_display` tinyint(1) DEFAULT 1,
  `category_display` tinyint(1) DEFAULT 1,
  `tag_display` tinyint(1) DEFAULT 1,
  `related_posts_count` int(11) DEFAULT 3,
  `rss_enabled` tinyint(1) DEFAULT 1,
  `search_enabled` tinyint(1) DEFAULT 1,
  `archive_enabled` tinyint(1) DEFAULT 1,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
