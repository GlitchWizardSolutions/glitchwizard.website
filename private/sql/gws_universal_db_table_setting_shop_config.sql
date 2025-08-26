
-- --------------------------------------------------------

--
-- Table structure for table `setting_shop_config`
--

CREATE TABLE `setting_shop_config` (
  `id` int(11) NOT NULL,
  `site_name` varchar(255) DEFAULT 'Shopping Cart',
  `currency_code` varchar(10) DEFAULT '$',
  `currency_symbol` varchar(5) DEFAULT '$',
  `featured_image` varchar(255) DEFAULT 'uploads/featured-image.jpg',
  `default_payment_status` varchar(50) DEFAULT 'Completed',
  `account_required` tinyint(1) DEFAULT 0,
  `weight_unit` varchar(10) DEFAULT 'lbs',
  `rewrite_url` tinyint(1) DEFAULT 0,
  `template_editor` varchar(50) DEFAULT 'tinymce',
  `products_per_page` int(11) DEFAULT 12,
  `low_stock_threshold` int(11) DEFAULT 5,
  `out_of_stock_action` varchar(50) DEFAULT 'hide',
  `tax_enabled` tinyint(1) DEFAULT 0,
  `tax_rate` decimal(5,4) DEFAULT 0.0000,
  `shipping_enabled` tinyint(1) DEFAULT 1,
  `free_shipping_threshold` decimal(10,2) DEFAULT 0.00,
  `inventory_tracking` tinyint(1) DEFAULT 1,
  `reviews_enabled` tinyint(1) DEFAULT 1,
  `wishlist_enabled` tinyint(1) DEFAULT 1,
  `coupon_system_enabled` tinyint(1) DEFAULT 1,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `currency` varchar(8) NOT NULL DEFAULT 'USD'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
