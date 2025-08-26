
-- --------------------------------------------------------

--
-- Table structure for table `review_filters`
--

CREATE TABLE `review_filters` (
  `id` int(11) NOT NULL,
  `word` varchar(255) NOT NULL,
  `replacement` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
