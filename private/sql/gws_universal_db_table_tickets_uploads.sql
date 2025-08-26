
-- --------------------------------------------------------

--
-- Table structure for table `tickets_uploads`
--

CREATE TABLE `tickets_uploads` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `filepath` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
