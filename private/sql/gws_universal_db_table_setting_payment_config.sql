
-- --------------------------------------------------------

--
-- Table structure for table `setting_payment_config`
--

CREATE TABLE `setting_payment_config` (
  `id` int(11) NOT NULL,
  `pay_on_delivery_enabled` tinyint(1) DEFAULT 1,
  `paypal_enabled` tinyint(1) DEFAULT 1,
  `paypal_email` varchar(255) DEFAULT NULL,
  `paypal_testmode` tinyint(1) DEFAULT 1,
  `paypal_currency` varchar(10) DEFAULT 'USD',
  `paypal_ipn_url` varchar(255) DEFAULT NULL,
  `paypal_cancel_url` varchar(255) DEFAULT NULL,
  `paypal_return_url` varchar(255) DEFAULT NULL,
  `stripe_enabled` tinyint(1) DEFAULT 1,
  `stripe_publish_key` varchar(255) DEFAULT NULL,
  `stripe_secret_key` varchar(255) DEFAULT NULL,
  `stripe_currency` varchar(10) DEFAULT 'USD',
  `stripe_webhook_secret` varchar(255) DEFAULT NULL,
  `coinbase_enabled` tinyint(1) DEFAULT 0,
  `coinbase_api_key` varchar(255) DEFAULT NULL,
  `coinbase_secret` varchar(255) DEFAULT NULL,
  `default_currency` varchar(10) DEFAULT 'USD',
  `accepted_currencies` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`accepted_currencies`)),
  `payment_timeout` int(11) DEFAULT 1800,
  `payment_confirmation_page` varchar(255) DEFAULT NULL,
  `failed_payment_redirect` varchar(255) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
