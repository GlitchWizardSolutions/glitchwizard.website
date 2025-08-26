
-- --------------------------------------------------------

--
-- Table structure for table `gallery_media_collections`
--

CREATE TABLE `gallery_media_collections` (
  `id` int(11) NOT NULL,
  `collection_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
