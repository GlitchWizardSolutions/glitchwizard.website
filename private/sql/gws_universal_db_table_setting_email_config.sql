
-- --------------------------------------------------------

--
-- Table structure for table `setting_email_config`
--

CREATE TABLE `setting_email_config` (
  `id` int(11) NOT NULL,
  `mail_enabled` tinyint(1) DEFAULT 1,
  `mail_from` varchar(255) DEFAULT 'noreply@example.com',
  `mail_name` varchar(255) DEFAULT 'GWS Universal',
  `reply_to` varchar(255) DEFAULT NULL,
  `smtp_enabled` tinyint(1) DEFAULT 0,
  `smtp_host` varchar(255) DEFAULT NULL,
  `smtp_port` int(11) DEFAULT 587,
  `smtp_username` varchar(255) DEFAULT NULL,
  `smtp_password` varchar(255) DEFAULT NULL,
  `smtp_encryption` varchar(10) DEFAULT 'tls',
  `smtp_auth` tinyint(1) DEFAULT 1,
  `notifications_enabled` tinyint(1) DEFAULT 1,
  `notification_email` varchar(255) DEFAULT NULL,
  `auto_reply_enabled` tinyint(1) DEFAULT 1,
  `email_templates_path` varchar(255) DEFAULT 'assets/email_templates',
  `email_signature` text DEFAULT NULL,
  `bounce_handling` tinyint(1) DEFAULT 0,
  `email_tracking` tinyint(1) DEFAULT 0,
  `unsubscribe_enabled` tinyint(1) DEFAULT 1,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
