
-- --------------------------------------------------------

--
-- Table structure for table `event_unavailable_dates`
--

CREATE TABLE `event_unavailable_dates` (
  `id` int(11) NOT NULL,
  `unavailable_date` date NOT NULL,
  `unavailable_label` varchar(255) NOT NULL,
  `event_uid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
