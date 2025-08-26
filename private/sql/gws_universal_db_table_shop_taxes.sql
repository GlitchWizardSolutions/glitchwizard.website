
-- --------------------------------------------------------

--
-- Table structure for table `shop_taxes`
--

CREATE TABLE `shop_taxes` (
  `id` int(11) NOT NULL,
  `country` varchar(255) NOT NULL,
  `rate` decimal(5,2) NOT NULL,
  `rate_type` varchar(50) NOT NULL DEFAULT 'percentage',
  `rules` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
