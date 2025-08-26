
-- --------------------------------------------------------

--
-- Table structure for table `setting_blog_comments`
--

CREATE TABLE `setting_blog_comments` (
  `id` int(11) NOT NULL,
  `comment_system` enum('internal','disqus','facebook','disabled') DEFAULT 'internal',
  `require_approval` tinyint(1) DEFAULT 1,
  `allow_guest_comments` tinyint(1) DEFAULT 1,
  `require_registration` tinyint(1) DEFAULT 0,
  `max_comment_length` int(11) DEFAULT 1000,
  `enable_notifications` tinyint(1) DEFAULT 1,
  `notification_email` varchar(255) DEFAULT 'admin@example.com',
  `enable_threading` tinyint(1) DEFAULT 1,
  `max_thread_depth` int(11) DEFAULT 3,
  `enable_comment_voting` tinyint(1) DEFAULT 0,
  `enable_comment_editing` tinyint(1) DEFAULT 1,
  `comment_edit_time_limit` int(11) DEFAULT 300,
  `enable_comment_deletion` tinyint(1) DEFAULT 1,
  `enable_spam_protection` tinyint(1) DEFAULT 1,
  `disqus_shortname` varchar(255) DEFAULT '',
  `facebook_app_id` varchar(255) DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
