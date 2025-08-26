
-- --------------------------------------------------------

--
-- Table structure for table `blog_newsletter`
--

CREATE TABLE `blog_newsletter` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ip` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
