
-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `username` varchar(200) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL DEFAULT '''''',
  `phone` varchar(255) NOT NULL DEFAULT '''''',
  `address_street` varchar(255) NOT NULL DEFAULT '''''',
  `address_city` varchar(255) NOT NULL DEFAULT '''''',
  `address_state` varchar(255) NOT NULL DEFAULT '''''',
  `address_zip` varchar(255) NOT NULL DEFAULT '''''',
  `address_country` varchar(255) NOT NULL DEFAULT 'USA',
  `registered` datetime NOT NULL DEFAULT current_timestamp(),
  `role` varchar(50) NOT NULL DEFAULT 'Customer' COMMENT '''Admin'', ''Member'', ''Developer'', ''Guest'', ''Subscriber'', ''Editor'', ''Blog_User'', ''Customer''',
  `access_level` tinyint(3) NOT NULL DEFAULT 50,
  `document_path` varchar(200) NOT NULL DEFAULT 'Welcome/',
  `full_name` varchar(200) NOT NULL DEFAULT 'please update',
  `rememberme` varchar(255) NOT NULL DEFAULT '''''',
  `activation_code` varchar(255) NOT NULL DEFAULT 'activated',
  `last_seen` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `method` varchar(50) NOT NULL DEFAULT 'password',
  `social_email` varchar(200) NOT NULL DEFAULT 'please update',
  `reset_code` varchar(255) NOT NULL DEFAULT '''''',
  `password` varchar(255) NOT NULL,
  `tfa_code` varchar(255) NOT NULL DEFAULT '''''',
  `ip` varchar(255) NOT NULL DEFAULT '''''',
  `approved` varchar(50) NOT NULL DEFAULT 'approved',
  `blog_user` int(11) NOT NULL DEFAULT 1,
  `avatar` varchar(255) NOT NULL DEFAULT 'default.svg',
  `banned` tinyint(1) NOT NULL DEFAULT 0,
  `website_url` varchar(255) DEFAULT NULL,
  `chat_operator` tinyint(1) NOT NULL DEFAULT 0,
  `chat_status` enum('offline','available','busy','away') NOT NULL DEFAULT 'offline',
  `chat_auto_accept` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `username`, `first_name`, `last_name`, `email`, `phone`, `address_street`, `address_city`, `address_state`, `address_zip`, `address_country`, `registered`, `role`, `access_level`, `document_path`, `full_name`, `rememberme`, `activation_code`, `last_seen`, `method`, `social_email`, `reset_code`, `password`, `tfa_code`, `ip`, `approved`, `blog_user`, `avatar`, `banned`, `website_url`, `chat_operator`, `chat_status`, `chat_auto_accept`) VALUES
(1, 'Dio', 'please', 'update', 'sidewaysy.tasks@gmail.com', '\'\'', '127 Northwood Road', 'Crawfordville', 'FL', '32327', 'USA', '2025-07-02 23:38:00', 'Blog_User', 0, 'Welcome/', 'please update', '$2y$10$esagPd1Lo4sKApSjRvoLVOwX3gaFaRlfVx6QgVKPD21jkPjGobA36', 'activated', '2025-08-13 10:51:02', 'password', 'please update', 'bfee4b3c9490b4ad70e051280a452269570f94f3e403ca66d83f658a61246d19', '$2y$10$Zy64yPZ7YQRI11cEpnFc7u7E5/j/Hcp6151knKsnrZQkQiRhwHM6C', '\'\'', '\'\'', '1', 1, 'default-developer.svg', 0, NULL, 0, 'offline', 1),
(3, 'GlitchWizard', 'Glitch', 'Wizard', 'webdev@glitchwizardsolutions.com', '(850) 294-4226', '127 Northwood Rd', 'Crawfordville', 'FL', '32327', 'United States', '2024-09-10 10:32:00', 'Developer', 0, 'Barbara_Moore', 'Glitch Wizard', '$2y$10$OPJqs7NOIGg/Pwag7is2C.RJUqSM4VZ4Sbfxld.Z3p4sUSoT/YzGC', 'activated', '2025-08-20 21:57:20', 'password', 'sidewaysy@gmail.com', '\'\'', '$2y$10$Qr0AlGEglzRepKFncvVrKuCzeDWORE4UsQ4ZzmucEnH/l1/ein7a2', 'DC1955', '75.229.47.137', '1', 1, 'default-developer.svg', 0, NULL, 0, 'offline', 1),
(48, 'Joseph', 'Joseph', 'Gross', 'cherokeejoey@gmail.com', '(850) 491-9028', '18627 CR 23', 'Bristol', 'Indiana', '46507', 'USA', '2025-08-01 01:34:35', 'Member', 0, '', 'Joseph Gross', '\'\'', 'activated', '2025-08-19 14:06:41', 'password', 'please update', '272f5ad258a5d3f8ad4e59848a7d31eacdeecea761b8346a184a54aec75d98c1', '$2y$10$F5g51ASqGAl0KceMVGxyRO0bXOy5sA0X1UVjWZaw55mrdv7nPKqKK', '\'\'', '\'\'', '1', 1, 'default.svg', 0, NULL, 0, 'offline', 1);
