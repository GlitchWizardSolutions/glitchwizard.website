
-- --------------------------------------------------------

--
-- Table structure for table `setting_accounts_config`
--

CREATE TABLE `setting_accounts_config` (
  `id` int(11) NOT NULL,
  `registration_enabled` tinyint(1) DEFAULT 1,
  `email_verification_required` tinyint(1) DEFAULT 1,
  `admin_approval_required` tinyint(1) DEFAULT 0,
  `username_min_length` int(11) DEFAULT 4,
  `username_max_length` int(11) DEFAULT 50,
  `password_min_length` int(11) DEFAULT 8,
  `password_require_special` tinyint(1) DEFAULT 1,
  `password_require_uppercase` tinyint(1) DEFAULT 1,
  `password_require_lowercase` tinyint(1) DEFAULT 1,
  `password_require_numbers` tinyint(1) DEFAULT 1,
  `max_login_attempts` int(11) DEFAULT 5,
  `lockout_duration` int(11) DEFAULT 900,
  `session_lifetime` int(11) DEFAULT 3600,
  `remember_me_enabled` tinyint(1) DEFAULT 1,
  `remember_duration` int(11) DEFAULT 2592000,
  `two_factor_enabled` tinyint(1) DEFAULT 0,
  `profile_pictures_enabled` tinyint(1) DEFAULT 1,
  `profile_picture_max_size` int(11) DEFAULT 2097152,
  `allowed_image_types` varchar(255) DEFAULT 'jpg,jpeg,png,gif',
  `default_role` varchar(50) DEFAULT 'Member',
  `welcome_email_enabled` tinyint(1) DEFAULT 1,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
