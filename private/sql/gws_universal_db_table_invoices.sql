
-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT 0,
  `invoice_number` varchar(255) NOT NULL,
  `payment_amount` decimal(7,2) NOT NULL,
  `payment_status` varchar(50) NOT NULL,
  `payment_methods` varchar(255) NOT NULL DEFAULT 'Cash, PayPal',
  `due_date` datetime NOT NULL,
  `created` datetime NOT NULL,
  `notes` text NOT NULL,
  `viewed` tinyint(1) NOT NULL DEFAULT 0,
  `tax` varchar(50) NOT NULL DEFAULT 'fixed',
  `tax_total` decimal(7,2) NOT NULL DEFAULT 0.00,
  `invoice_template` varchar(255) NOT NULL DEFAULT 'default',
  `payment_ref` varchar(255) NOT NULL DEFAULT '',
  `paid_with` varchar(50) NOT NULL DEFAULT '',
  `paid_total` decimal(7,2) NOT NULL DEFAULT 0.00,
  `recurrence` tinyint(1) NOT NULL DEFAULT 0,
  `recurrence_period` int(11) NOT NULL DEFAULT 0,
  `recurrence_period_type` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
