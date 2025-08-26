
-- --------------------------------------------------------

--
-- Table structure for table `setting_content_services`
--

CREATE TABLE `setting_content_services` (
  `id` int(11) NOT NULL,
  `service_title` varchar(255) NOT NULL,
  `service_description` text DEFAULT NULL,
  `service_icon` varchar(255) DEFAULT NULL,
  `service_link` varchar(255) DEFAULT NULL,
  `service_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `service_category` varchar(100) DEFAULT 'foreclosure_help',
  `service_summary` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` varchar(100) DEFAULT 'system'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
