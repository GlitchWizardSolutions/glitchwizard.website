
-- --------------------------------------------------------

--
-- Table structure for table `draft_locks`
--

CREATE TABLE `draft_locks` (
  `id` int(11) NOT NULL,
  `document_title` varchar(255) NOT NULL,
  `client_id` int(11) NOT NULL,
  `locked_until` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
