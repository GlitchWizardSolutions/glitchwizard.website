
-- --------------------------------------------------------

--
-- Table structure for table `setting_content_testimonials`
--

CREATE TABLE `setting_content_testimonials` (
  `id` int(11) NOT NULL,
  `client_name` varchar(100) NOT NULL,
  `client_role` varchar(100) NOT NULL,
  `testimonial_text` text NOT NULL,
  `client_image` varchar(255) DEFAULT NULL,
  `testimonial_order` int(11) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
