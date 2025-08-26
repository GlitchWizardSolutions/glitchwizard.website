
-- --------------------------------------------------------

--
-- Table structure for table `campaign_unsubscribes`
--

CREATE TABLE `campaign_unsubscribes` (
  `id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `submit_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
