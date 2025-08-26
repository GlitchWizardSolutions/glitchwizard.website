
-- --------------------------------------------------------

--
-- Table structure for table `landing_page_media`
--

CREATE TABLE `landing_page_media` (
  `id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `media_type` enum('image','video','audio','document') NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `stored_filename` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `caption` text DEFAULT NULL,
  `usage_context` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
