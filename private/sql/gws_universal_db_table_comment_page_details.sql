
-- --------------------------------------------------------

--
-- Table structure for table `comment_page_details`
--

CREATE TABLE `comment_page_details` (
  `id` int(11) NOT NULL,
  `page_id` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `page_status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
