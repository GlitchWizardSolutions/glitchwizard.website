
-- --------------------------------------------------------

--
-- Table structure for table `form_files`
--

CREATE TABLE `form_files` (
  `id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `field_name` varchar(100) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `stored_filename` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
