
-- --------------------------------------------------------

--
-- Table structure for table `shop_products`
--

CREATE TABLE `shop_products` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` mediumtext NOT NULL,
  `sku` varchar(255) NOT NULL DEFAULT '',
  `price` decimal(7,2) NOT NULL,
  `rrp` decimal(7,2) NOT NULL DEFAULT 0.00,
  `quantity` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `weight` decimal(7,2) NOT NULL DEFAULT 0.00,
  `url_slug` varchar(255) NOT NULL DEFAULT '',
  `product_status` tinyint(1) NOT NULL DEFAULT 1,
  `subscription` tinyint(1) NOT NULL DEFAULT 0,
  `subscription_period` int(11) NOT NULL DEFAULT 0,
  `subscription_period_type` varchar(50) NOT NULL DEFAULT 'day'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
