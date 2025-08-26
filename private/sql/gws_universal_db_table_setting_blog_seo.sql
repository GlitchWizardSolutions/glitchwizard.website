
-- --------------------------------------------------------

--
-- Table structure for table `setting_blog_seo`
--

CREATE TABLE `setting_blog_seo` (
  `id` int(11) NOT NULL,
  `enable_seo_urls` tinyint(1) DEFAULT 1,
  `post_url_structure` varchar(255) DEFAULT '{year}/{month}/{slug}',
  `enable_meta_tags` tinyint(1) DEFAULT 1,
  `enable_open_graph` tinyint(1) DEFAULT 1,
  `enable_twitter_cards` tinyint(1) DEFAULT 1,
  `default_post_image` text DEFAULT '',
  `robots_txt_additions` text DEFAULT '',
  `sitemap_frequency` varchar(20) DEFAULT 'weekly',
  `sitemap_priority` decimal(2,1) DEFAULT 0.8,
  `enable_canonical_urls` tinyint(1) DEFAULT 1,
  `enable_schema_markup` tinyint(1) DEFAULT 1,
  `google_analytics_id` varchar(255) DEFAULT '',
  `google_site_verification` varchar(255) DEFAULT '',
  `bing_site_verification` varchar(255) DEFAULT '',
  `enable_breadcrumb_schema` tinyint(1) DEFAULT 1,
  `enable_article_schema` tinyint(1) DEFAULT 1,
  `default_meta_description` text DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
