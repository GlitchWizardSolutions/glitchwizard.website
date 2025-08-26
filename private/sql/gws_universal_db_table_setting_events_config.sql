
-- --------------------------------------------------------

--
-- Table structure for table `setting_events_config`
--

CREATE TABLE `setting_events_config` (
  `id` int(11) NOT NULL,
  `events_enabled` tinyint(1) DEFAULT 1,
  `public_events_enabled` tinyint(1) DEFAULT 1,
  `events_per_page` int(11) DEFAULT 12,
  `allow_public_registration` tinyint(1) DEFAULT 1,
  `require_approval` tinyint(1) DEFAULT 0,
  `send_confirmation_emails` tinyint(1) DEFAULT 1,
  `send_reminder_emails` tinyint(1) DEFAULT 1,
  `reminder_days_before` int(11) DEFAULT 1,
  `max_events_per_user` int(11) DEFAULT 0,
  `event_image_max_size` int(11) DEFAULT 5242880,
  `allowed_file_types` varchar(255) DEFAULT 'jpg,jpeg,png,gif,pdf,doc,docx',
  `default_event_duration` int(11) DEFAULT 60,
  `timezone` varchar(50) DEFAULT 'America/New_York',
  `date_format` varchar(20) DEFAULT 'Y-m-d',
  `time_format` varchar(10) DEFAULT 'H:i',
  `calendar_view_default` varchar(20) DEFAULT 'month',
  `enable_recurring_events` tinyint(1) DEFAULT 0,
  `enable_event_categories` tinyint(1) DEFAULT 1,
  `enable_event_ratings` tinyint(1) DEFAULT 1,
  `enable_event_comments` tinyint(1) DEFAULT 1,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
