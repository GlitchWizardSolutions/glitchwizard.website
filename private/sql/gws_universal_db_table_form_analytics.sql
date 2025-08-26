
-- --------------------------------------------------------

--
-- Table structure for table `form_analytics`
--

CREATE TABLE `form_analytics` (
  `id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `date_tracked` date NOT NULL,
  `views` int(11) DEFAULT 0,
  `submissions` int(11) DEFAULT 0,
  `conversion_rate` decimal(5,2) DEFAULT 0.00,
  `bounce_rate` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
