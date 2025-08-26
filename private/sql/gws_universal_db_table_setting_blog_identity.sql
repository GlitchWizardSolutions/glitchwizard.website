
-- --------------------------------------------------------

--
-- Table structure for table `setting_blog_identity`
--

CREATE TABLE `setting_blog_identity` (
  `id` int(11) NOT NULL,
  `blog_title` varchar(255) NOT NULL DEFAULT 'My Blog',
  `blog_description` text DEFAULT 'Welcome to my blog',
  `blog_tagline` varchar(255) DEFAULT 'Sharing thoughts and ideas',
  `author_name` varchar(255) DEFAULT 'Blog Author',
  `author_bio` text DEFAULT 'About the author',
  `default_author_id` int(11) DEFAULT 1,
  `blog_email` varchar(255) DEFAULT '',
  `blog_url` varchar(255) DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
