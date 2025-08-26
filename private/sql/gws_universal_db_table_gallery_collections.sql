
-- --------------------------------------------------------

--
-- Table structure for table `gallery_collections`
--

CREATE TABLE `gallery_collections` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description_text` varchar(255) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
