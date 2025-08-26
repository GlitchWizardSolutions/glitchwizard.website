
-- --------------------------------------------------------

--
-- Table structure for table `group_subscribers`
--

CREATE TABLE `group_subscribers` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
