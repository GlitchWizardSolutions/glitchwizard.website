
-- --------------------------------------------------------

--
-- Table structure for table `comment_filters`
--

CREATE TABLE `comment_filters` (
  `id` int(11) NOT NULL,
  `word` varchar(255) NOT NULL,
  `replacement` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
