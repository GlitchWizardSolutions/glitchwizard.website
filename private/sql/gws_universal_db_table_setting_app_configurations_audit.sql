
-- --------------------------------------------------------

--
-- Table structure for table `setting_app_configurations_audit`
--

CREATE TABLE `setting_app_configurations_audit` (
  `id` int(11) NOT NULL,
  `config_id` int(11) NOT NULL,
  `app_name` varchar(50) NOT NULL,
  `section` varchar(100) NOT NULL,
  `config_key` varchar(100) NOT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `change_type` enum('CREATE','UPDATE','DELETE') NOT NULL,
  `changed_by` varchar(100) DEFAULT NULL,
  `change_reason` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Audit trail for configuration changes';
