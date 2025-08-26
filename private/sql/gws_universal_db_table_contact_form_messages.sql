
-- --------------------------------------------------------

--
-- Table structure for table `contact_form_messages`
--

CREATE TABLE `contact_form_messages` (
  `id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `subject` varchar(50) NOT NULL,
  `msg` text NOT NULL,
  `extra` text NOT NULL,
  `submit_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('Unread','Read','Replied') NOT NULL DEFAULT 'Unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
