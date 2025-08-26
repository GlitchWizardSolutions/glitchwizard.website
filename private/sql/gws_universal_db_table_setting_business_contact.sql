
-- --------------------------------------------------------

--
-- Table structure for table `setting_business_contact`
--

CREATE TABLE `setting_business_contact` (
  `id` int(11) NOT NULL,
  `business_identity_id` int(11) NOT NULL DEFAULT 1,
  `primary_email` varchar(255) DEFAULT NULL,
  `primary_phone` varchar(50) DEFAULT NULL,
  `primary_address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `zipcode` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'United States',
  `website_url` varchar(255) DEFAULT NULL,
  `business_hours` text DEFAULT NULL,
  `secondary_phone` varchar(50) DEFAULT NULL,
  `fax_number` varchar(50) DEFAULT NULL,
  `mailing_address` varchar(255) DEFAULT NULL,
  `mailing_city` varchar(100) DEFAULT NULL,
  `mailing_state` varchar(50) DEFAULT NULL,
  `mailing_zipcode` varchar(20) DEFAULT NULL,
  `social_facebook` varchar(255) DEFAULT NULL,
  `social_instagram` varchar(255) DEFAULT NULL,
  `social_twitter` varchar(255) DEFAULT NULL,
  `social_linkedin` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
