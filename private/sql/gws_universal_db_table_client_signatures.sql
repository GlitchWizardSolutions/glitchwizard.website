
-- --------------------------------------------------------

--
-- Table structure for table `client_signatures`
--

CREATE TABLE `client_signatures` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `signature_path` text DEFAULT NULL,
  `initials_path` text DEFAULT NULL,
  `thumbnail_path` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
