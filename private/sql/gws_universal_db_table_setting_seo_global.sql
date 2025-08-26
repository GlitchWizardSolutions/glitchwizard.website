
-- --------------------------------------------------------

--
-- Table structure for table `setting_seo_global`
--

CREATE TABLE `setting_seo_global` (
  `id` int(11) NOT NULL,
  `default_title_suffix` varchar(255) DEFAULT ' | GWS Universal',
  `default_meta_description` text DEFAULT 'Professional business solutions and services',
  `default_meta_keywords` varchar(500) DEFAULT NULL,
  `canonical_domain` varchar(255) DEFAULT NULL,
  `google_analytics_id` varchar(50) DEFAULT NULL,
  `google_tag_manager_id` varchar(50) DEFAULT NULL,
  `facebook_pixel_id` varchar(50) DEFAULT NULL,
  `google_site_verification` varchar(255) DEFAULT NULL,
  `bing_site_verification` varchar(255) DEFAULT NULL,
  `yandex_site_verification` varchar(255) DEFAULT NULL,
  `robots_txt_content` text DEFAULT NULL,
  `sitemap_enabled` tinyint(1) DEFAULT 1,
  `sitemap_priority` decimal(2,1) DEFAULT 0.8,
  `sitemap_changefreq` varchar(20) DEFAULT 'weekly',
  `open_graph_type` varchar(50) DEFAULT 'website',
  `twitter_card_type` varchar(50) DEFAULT 'summary_large_image',
  `schema_org_type` varchar(100) DEFAULT 'Organization',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
