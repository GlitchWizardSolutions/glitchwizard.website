
-- --------------------------------------------------------

--
-- Table structure for table `gallery_media_likes`
--

CREATE TABLE `gallery_media_likes` (
  `id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
