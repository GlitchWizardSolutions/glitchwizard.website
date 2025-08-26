
-- --------------------------------------------------------

--
-- Table structure for table `form_integrations`
--

CREATE TABLE `form_integrations` (
  `id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `integration_type` enum('mailchimp','zapier','webhook','slack','google_sheets','custom') NOT NULL,
  `integration_name` varchar(255) NOT NULL,
  `configuration` text NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_sync` timestamp NULL DEFAULT NULL,
  `sync_status` enum('success','failed','pending') DEFAULT 'pending',
  `error_log` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
