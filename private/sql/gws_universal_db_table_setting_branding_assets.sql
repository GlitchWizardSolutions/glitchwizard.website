
-- --------------------------------------------------------

--
-- Table structure for table `setting_branding_assets`
--

CREATE TABLE `setting_branding_assets` (
  `id` int(11) NOT NULL,
  `business_logo_main` varchar(255) DEFAULT 'assets/img/logo.png',
  `business_logo_horizontal` varchar(255) DEFAULT 'assets/branding/logo_horizontal.png',
  `business_logo_vertical` varchar(255) DEFAULT 'assets/branding/logo_vertical.png',
  `business_logo_square` varchar(255) DEFAULT 'assets/branding/logo_square.png',
  `business_logo_white` varchar(255) DEFAULT 'assets/branding/logo_white.png',
  `business_logo_small` varchar(255) DEFAULT 'assets/branding/logo_small.png',
  `favicon_main` varchar(255) DEFAULT 'assets/img/favicon.png',
  `favicon_blog` varchar(255) DEFAULT 'assets/branding/favicon_blog.ico',
  `favicon_portal` varchar(255) DEFAULT 'assets/branding/favicon_portal.ico',
  `apple_touch_icon` varchar(255) DEFAULT 'assets/img/apple-touch-icon.png',
  `social_share_default` varchar(255) DEFAULT 'assets/branding/social_default.jpg',
  `social_share_facebook` varchar(255) DEFAULT 'assets/branding/social_facebook.jpg',
  `social_share_twitter` varchar(255) DEFAULT 'assets/branding/social_twitter.jpg',
  `social_share_linkedin` varchar(255) DEFAULT 'assets/branding/social_linkedin.jpg',
  `social_share_instagram` varchar(255) DEFAULT 'assets/branding/social_instagram.jpg',
  `social_share_blog` varchar(255) DEFAULT 'assets/branding/social_blog.jpg',
  `watermark_image` varchar(255) DEFAULT NULL,
  `loading_animation` varchar(255) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
