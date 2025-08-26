
-- --------------------------------------------------------

--
-- Table structure for table `landing_page_analytics`
--

CREATE TABLE `landing_page_analytics` (
  `id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `date_tracked` date NOT NULL,
  `page_views` int(11) DEFAULT 0,
  `unique_visitors` int(11) DEFAULT 0,
  `bounce_rate` decimal(5,2) DEFAULT 0.00,
  `avg_time_on_page` int(11) DEFAULT 0,
  `conversions` int(11) DEFAULT 0,
  `conversion_rate` decimal(5,2) DEFAULT 0.00,
  `traffic_sources` text DEFAULT NULL,
  `device_breakdown` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
