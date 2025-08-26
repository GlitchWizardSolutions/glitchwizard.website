
-- --------------------------------------------------------

--
-- Table structure for table `chat_operator_departments`
--

CREATE TABLE `chat_operator_departments` (
  `id` int(11) NOT NULL,
  `operator_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
