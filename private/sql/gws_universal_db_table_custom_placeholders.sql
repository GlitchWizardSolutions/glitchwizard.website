
-- --------------------------------------------------------

--
-- Table structure for table `custom_placeholders`
--

CREATE TABLE `custom_placeholders` (
  `id` int(11) NOT NULL,
  `placeholder_text` varchar(255) NOT NULL,
  `placeholder_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
