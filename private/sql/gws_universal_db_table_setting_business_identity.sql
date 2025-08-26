
-- --------------------------------------------------------

--
-- Table structure for table `setting_business_identity`
--

CREATE TABLE `setting_business_identity` (
  `id` int(11) NOT NULL,
  `business_name_short` varchar(50) NOT NULL DEFAULT 'GWS',
  `business_name_medium` varchar(100) NOT NULL DEFAULT 'GWS Universal',
  `business_name_long` varchar(200) NOT NULL DEFAULT 'GWS Universal Hybrid Application',
  `business_tagline_short` varchar(100) DEFAULT 'Innovation Simplified',
  `business_tagline_medium` varchar(200) DEFAULT 'Your complete business solution platform',
  `business_tagline_long` text DEFAULT 'Comprehensive hybrid application platform designed to streamline your business operations',
  `legal_business_name` varchar(200) DEFAULT NULL,
  `business_type` varchar(100) DEFAULT NULL,
  `tax_id` varchar(50) DEFAULT NULL,
  `registration_number` varchar(100) DEFAULT NULL,
  `established_date` date DEFAULT NULL,
  `about_business` text DEFAULT NULL,
  `mission_statement` text DEFAULT NULL,
  `vision_statement` text DEFAULT NULL,
  `core_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`core_values`)),
  `author` varchar(100) DEFAULT 'GWS',
  `footer_business_name_type` varchar(20) DEFAULT 'medium',
  `footer_logo_enabled` tinyint(1) DEFAULT 1,
  `footer_logo_position` varchar(20) DEFAULT 'left',
  `footer_logo_file` varchar(50) DEFAULT 'business_logo',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
