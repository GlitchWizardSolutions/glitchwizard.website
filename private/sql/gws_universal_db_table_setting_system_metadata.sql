
-- --------------------------------------------------------

--
-- Table structure for table `setting_system_metadata`
--

CREATE TABLE `setting_system_metadata` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(255) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `subcategory` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `data_type` enum('string','text','integer','boolean','json','array','file_path','url','email','color','font') NOT NULL,
  `is_required` tinyint(1) DEFAULT 0,
  `default_value` text DEFAULT NULL,
  `validation_rules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`validation_rules`)),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
