
-- --------------------------------------------------------

--
-- Table structure for table `page_completion_status`
--

CREATE TABLE `page_completion_status` (
  `id` int(11) NOT NULL,
  `page_path` varchar(255) NOT NULL,
  `page_name` varchar(100) NOT NULL,
  `is_complete` tinyint(1) DEFAULT 0,
  `completion_notes` text DEFAULT NULL,
  `last_checked` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
