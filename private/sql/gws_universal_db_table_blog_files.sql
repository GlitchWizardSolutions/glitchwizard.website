
-- --------------------------------------------------------

--
-- Table structure for table `blog_files`
--

CREATE TABLE `blog_files` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `time` varchar(5) NOT NULL,
  `path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
