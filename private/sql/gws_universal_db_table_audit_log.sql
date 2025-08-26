
-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(11) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  `document_title` varchar(255) DEFAULT NULL,
  `file_type` enum('pdf','docx','csv','xlsx') DEFAULT NULL,
  `file_path` text DEFAULT NULL,
  `output_path` text DEFAULT NULL,
  `version_number` int(11) DEFAULT 1,
  `version_notes` text DEFAULT NULL,
  `version_tags` varchar(255) DEFAULT NULL,
  `signed` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payment_status` enum('pending','paid') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
