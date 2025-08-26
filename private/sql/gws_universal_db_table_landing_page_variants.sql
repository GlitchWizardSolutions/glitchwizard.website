
-- --------------------------------------------------------

--
-- Table structure for table `landing_page_variants`
--

CREATE TABLE `landing_page_variants` (
  `id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `variant_name` varchar(255) NOT NULL,
  `variant_content` text NOT NULL,
  `traffic_percentage` decimal(5,2) DEFAULT 50.00,
  `is_active` tinyint(1) DEFAULT 1,
  `views` int(11) DEFAULT 0,
  `conversions` int(11) DEFAULT 0,
  `conversion_rate` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
