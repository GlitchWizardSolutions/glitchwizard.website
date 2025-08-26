
-- --------------------------------------------------------

--
-- Table structure for table `setting_chat_config`
--

CREATE TABLE `setting_chat_config` (
  `id` int(11) NOT NULL,
  `chat_enabled` tinyint(1) DEFAULT 0,
  `chat_widget_position` varchar(20) DEFAULT 'bottom-right',
  `chat_widget_color` varchar(7) DEFAULT '#3498db',
  `chat_welcome_message` text DEFAULT 'Hello! How can we help you today?',
  `chat_offline_message` text DEFAULT 'We are currently offline. Please leave a message and we will get back to you.',
  `chat_auto_assign` tinyint(1) DEFAULT 1,
  `chat_session_timeout` int(11) DEFAULT 30,
  `chat_require_email` tinyint(1) DEFAULT 0,
  `chat_require_name` tinyint(1) DEFAULT 1,
  `chat_enable_file_upload` tinyint(1) DEFAULT 1,
  `chat_max_file_size` int(11) DEFAULT 5,
  `chat_enable_sound_notifications` tinyint(1) DEFAULT 1,
  `chat_enable_email_notifications` tinyint(1) DEFAULT 1,
  `chat_notification_email` varchar(255) DEFAULT '',
  `chat_business_hours_enabled` tinyint(1) DEFAULT 0,
  `chat_business_hours_start` time DEFAULT '09:00:00',
  `chat_business_hours_end` time DEFAULT '17:00:00',
  `chat_business_days` varchar(20) DEFAULT '1,2,3,4,5',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
