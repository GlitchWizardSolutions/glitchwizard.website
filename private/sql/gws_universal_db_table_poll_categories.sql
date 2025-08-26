
-- --------------------------------------------------------

--
-- Table structure for table `poll_categories`
--

CREATE TABLE `poll_categories` (
  `id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
