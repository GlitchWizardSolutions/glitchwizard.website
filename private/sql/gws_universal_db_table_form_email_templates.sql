
-- --------------------------------------------------------

--
-- Table structure for table `form_email_templates`
--

CREATE TABLE `form_email_templates` (
  `id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `template_type` enum('admin_notification','user_confirmation','autoresponder') NOT NULL,
  `template_name` varchar(255) NOT NULL,
  `subject_line` varchar(255) NOT NULL,
  `email_body` text NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `send_to` text DEFAULT NULL,
  `send_from` varchar(255) DEFAULT NULL,
  `send_from_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
