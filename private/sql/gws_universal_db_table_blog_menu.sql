
-- --------------------------------------------------------

--
-- Table structure for table `blog_menu`
--

CREATE TABLE `blog_menu` (
  `id` int(11) NOT NULL,
  `page` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `fa_icon` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
