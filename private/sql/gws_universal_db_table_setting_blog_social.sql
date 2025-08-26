
-- --------------------------------------------------------

--
-- Table structure for table `setting_blog_social`
--

CREATE TABLE `setting_blog_social` (
  `id` int(11) NOT NULL,
  `facebook_url` varchar(255) DEFAULT '',
  `twitter_url` varchar(255) DEFAULT '',
  `instagram_url` varchar(255) DEFAULT '',
  `linkedin_url` varchar(255) DEFAULT '',
  `youtube_url` varchar(255) DEFAULT '',
  `pinterest_url` varchar(255) DEFAULT '',
  `github_url` varchar(255) DEFAULT '',
  `enable_facebook_sharing` tinyint(1) DEFAULT 1,
  `enable_twitter_sharing` tinyint(1) DEFAULT 1,
  `enable_linkedin_sharing` tinyint(1) DEFAULT 1,
  `enable_pinterest_sharing` tinyint(1) DEFAULT 0,
  `enable_email_sharing` tinyint(1) DEFAULT 1,
  `enable_whatsapp_sharing` tinyint(1) DEFAULT 1,
  `enable_reddit_sharing` tinyint(1) DEFAULT 0,
  `twitter_username` varchar(255) DEFAULT '',
  `facebook_app_id` varchar(255) DEFAULT '',
  `social_sharing_position` enum('top','bottom','both','floating') DEFAULT 'bottom',
  `enable_social_login` tinyint(1) DEFAULT 0,
  `facebook_login_enabled` tinyint(1) DEFAULT 0,
  `google_login_enabled` tinyint(1) DEFAULT 0,
  `twitter_login_enabled` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
