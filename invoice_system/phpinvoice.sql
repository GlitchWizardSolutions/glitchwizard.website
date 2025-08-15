CREATE DATABASE IF NOT EXISTS `phpinvoice` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `phpinvoice`;

CREATE TABLE IF NOT EXISTS `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `display_name` varchar(50) NOT NULL,
  `role` enum('Admin','Member') NOT NULL,
  `ip` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `accounts` (`id`, `email`, `password`, `display_name`, `role`, `ip`) VALUES
(1, 'admin@example.com', '$2y$10$ZU7Jq5yZ1U/ifeJoJzvLbenjRyJVkSzmQKQc.X0KDPkfR3qs/iA7O', 'Admin', 'Admin', '::1');

CREATE TABLE IF NOT EXISTS `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address_street` varchar(255) NOT NULL,
  `address_city` varchar(255) NOT NULL,
  `address_state` varchar(255) NOT NULL,
  `address_zip` varchar(255) NOT NULL,
  `address_country` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `payment_amount` decimal(7,2) NOT NULL,
  `payment_status` varchar(50) NOT NULL,
  `payment_methods` varchar(255) NOT NULL,
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
  `recurrence_period_type` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `invoice_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(255) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_description` varchar(255) NOT NULL,
  `item_price` decimal(7,2) NOT NULL,
  `item_quantity` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;