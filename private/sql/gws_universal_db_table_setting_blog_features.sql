
-- --------------------------------------------------------

--
-- Table structure for table `setting_blog_features`
--

CREATE TABLE `setting_blog_features` (
  `id` int(11) NOT NULL,
  `enable_posts` tinyint(1) DEFAULT 1,
  `enable_pages` tinyint(1) DEFAULT 1,
  `enable_categories` tinyint(1) DEFAULT 1,
  `enable_tags` tinyint(1) DEFAULT 1,
  `enable_comments` tinyint(1) DEFAULT 1,
  `enable_author_bio` tinyint(1) DEFAULT 1,
  `enable_social_sharing` tinyint(1) DEFAULT 1,
  `enable_related_posts` tinyint(1) DEFAULT 1,
  `enable_search` tinyint(1) DEFAULT 1,
  `enable_archives` tinyint(1) DEFAULT 1,
  `enable_rss` tinyint(1) DEFAULT 1,
  `enable_sitemap` tinyint(1) DEFAULT 1,
  `enable_breadcrumbs` tinyint(1) DEFAULT 1,
  `enable_post_navigation` tinyint(1) DEFAULT 1,
  `enable_reading_time` tinyint(1) DEFAULT 1,
  `enable_post_views` tinyint(1) DEFAULT 1,
  `enable_newsletter_signup` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
