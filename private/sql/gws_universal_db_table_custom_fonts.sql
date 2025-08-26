
-- --------------------------------------------------------

--
-- Table structure for table `custom_fonts`
--

CREATE TABLE `custom_fonts` (
  `id` int(11) NOT NULL,
  `font_name` varchar(255) NOT NULL,
  `font_family` varchar(255) NOT NULL,
  `font_file_path` varchar(500) NOT NULL,
  `font_format` enum('woff2','woff','ttf','otf') NOT NULL DEFAULT 'woff2',
  `file_size` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `uploaded_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
