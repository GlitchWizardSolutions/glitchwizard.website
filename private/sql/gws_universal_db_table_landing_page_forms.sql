
-- --------------------------------------------------------

--
-- Table structure for table `landing_page_forms`
--

CREATE TABLE `landing_page_forms` (
  `id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `form_id` int(11) DEFAULT NULL,
  `form_type` enum('contact','newsletter','lead_capture','survey','custom') NOT NULL,
  `form_settings` text DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `submission_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
