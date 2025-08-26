
-- --------------------------------------------------------

--
-- Table structure for table `blog_users`
--

CREATE TABLE `blog_users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `registered` datetime NOT NULL DEFAULT current_timestamp(),
  `username` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'Blog User',
  `access_level` varchar(50) NOT NULL DEFAULT 'Blog Only',
  `document_path` varchar(200) NOT NULL DEFAULT 'Blog/',
  `full_name` varchar(200) NOT NULL DEFAULT 'None Provided',
  `rememberme` varchar(255) NOT NULL DEFAULT '''''',
  `activation_code` varchar(255) NOT NULL DEFAULT 'activated',
  `last_seen` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `method` varchar(50) NOT NULL DEFAULT 'password',
  `social_email` varchar(200) NOT NULL DEFAULT 'None Provided',
  `reset_code` varchar(255) NOT NULL DEFAULT '''''',
  `password` varchar(255) NOT NULL,
  `tfa_code` varchar(255) NOT NULL DEFAULT '''''',
  `ip` varchar(255) NOT NULL DEFAULT '''''',
  `approved` varchar(50) NOT NULL DEFAULT 'approved',
  `avatar` varchar(255) NOT NULL DEFAULT 'assets/img/avatar.png',
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `added` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
