
-- --------------------------------------------------------

--
-- Table structure for table `setting_content_pricing`
--

CREATE TABLE `setting_content_pricing` (
  `id` int(11) NOT NULL,
  `plan_key` varchar(100) NOT NULL,
  `plan_name` varchar(255) NOT NULL,
  `plan_description` text DEFAULT NULL,
  `plan_short_desc` varchar(500) DEFAULT NULL,
  `plan_price` varchar(100) NOT NULL,
  `plan_price_numeric` decimal(10,2) DEFAULT NULL,
  `plan_billing_period` varchar(50) DEFAULT 'monthly',
  `plan_currency` varchar(10) DEFAULT 'USD',
  `plan_features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`plan_features`)),
  `plan_benefits` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`plan_benefits`)),
  `plan_limitations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`plan_limitations`)),
  `plan_button_text` varchar(100) DEFAULT 'Get Started',
  `plan_button_link` varchar(255) DEFAULT '#',
  `plan_icon` varchar(255) DEFAULT NULL,
  `plan_badge` varchar(100) DEFAULT NULL,
  `plan_color_scheme` varchar(50) DEFAULT 'primary',
  `plan_category` varchar(100) DEFAULT 'standard',
  `plan_order` int(11) DEFAULT 0,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_popular` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
