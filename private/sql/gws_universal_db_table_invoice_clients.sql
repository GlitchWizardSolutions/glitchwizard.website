
-- --------------------------------------------------------

--
-- Table structure for table `invoice_clients`
--

CREATE TABLE `invoice_clients` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT 0,
  `business_name` varchar(200) NOT NULL,
  `description` varchar(1500) NOT NULL,
  `facebook` varchar(150) NOT NULL DEFAULT 'https://facebook.com/#',
  `instagram` varchar(150) NOT NULL DEFAULT 'https://instagram.com/#',
  `bluesky` varchar(150) NOT NULL DEFAULT 'https://bluesky.com/#',
  `x` varchar(150) NOT NULL DEFAULT 'https://twitter.com/#',
  `linkedin` varchar(150) NOT NULL DEFAULT 'https://linkedin.com/#"',
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address_street` varchar(255) NOT NULL,
  `address_city` varchar(255) NOT NULL,
  `address_state` varchar(255) NOT NULL,
  `address_zip` varchar(255) NOT NULL,
  `address_country` varchar(255) NOT NULL DEFAULT 'USA',
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `total_invoices` int(11) NOT NULL DEFAULT 0,
  `issue` varchar(4) NOT NULL DEFAULT 'No',
  `incomplete` varchar(4) NOT NULL DEFAULT 'Yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
