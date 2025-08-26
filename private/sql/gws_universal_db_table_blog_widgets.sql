
-- --------------------------------------------------------

--
-- Table structure for table `blog_widgets`
--

CREATE TABLE `blog_widgets` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` mediumtext NOT NULL,
  `position` varchar(10) NOT NULL DEFAULT 'Sidebar'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
