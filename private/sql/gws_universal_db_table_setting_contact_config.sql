
-- --------------------------------------------------------

--
-- Table structure for table `setting_contact_config`
--

CREATE TABLE `setting_contact_config` (
  `id` int(11) NOT NULL,
  `receiving_email` varchar(255) NOT NULL,
  `email_subject_prefix` varchar(100) DEFAULT '[Contact Form]',
  `email_from_name` varchar(255) DEFAULT 'Contact Form',
  `auto_reply_enabled` tinyint(1) DEFAULT 1,
  `auto_reply_subject` varchar(255) DEFAULT 'Thank you for contacting us',
  `auto_reply_message` text DEFAULT 'We have received your message and will respond as soon as possible.',
  `rate_limit_enabled` tinyint(1) DEFAULT 1,
  `rate_limit_max` int(11) DEFAULT 3,
  `rate_limit_window` int(11) DEFAULT 3600,
  `min_submit_interval` int(11) DEFAULT 10,
  `spam_protection_enabled` tinyint(1) DEFAULT 1,
  `blocked_words` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`blocked_words`)),
  `max_links_allowed` int(11) DEFAULT 2,
  `captcha_enabled` tinyint(1) DEFAULT 1,
  `captcha_type` varchar(50) DEFAULT 'recaptcha',
  `form_fields_required` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`form_fields_required`)),
  `custom_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`custom_fields`)),
  `enable_logging` tinyint(1) DEFAULT 1,
  `redirect_after_submit` varchar(255) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
