
-- --------------------------------------------------------

--
-- Table structure for table `shop_product_media_map`
--

CREATE TABLE `shop_product_media_map` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `position` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
