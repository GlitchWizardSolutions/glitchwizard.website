
-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `page_id` varchar(255) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT -1,
  `display_name` varchar(255) NOT NULL,
  `content` mediumtext NOT NULL,
  `submit_date` datetime NOT NULL DEFAULT current_timestamp(),
  `edited_date` datetime NOT NULL DEFAULT current_timestamp(),
  `votes` int(11) NOT NULL DEFAULT 0,
  `approved` tinyint(1) NOT NULL DEFAULT 0,
  `account_id` int(11) NOT NULL DEFAULT -1,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `top_parent_id` int(11) NOT NULL DEFAULT 0,
  `reply` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
