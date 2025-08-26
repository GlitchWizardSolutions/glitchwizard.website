
-- --------------------------------------------------------

--
-- Table structure for table `setting_content_homepage`
--

CREATE TABLE `setting_content_homepage` (
  `id` int(11) NOT NULL,
  `hero_headline` varchar(255) DEFAULT NULL,
  `hero_subheadline` text DEFAULT NULL,
  `hero_background_image` varchar(255) DEFAULT NULL,
  `hero_button_text` varchar(100) DEFAULT NULL,
  `hero_button_link` varchar(255) DEFAULT NULL,
  `services_section_title` varchar(255) DEFAULT NULL,
  `services_section_description` text DEFAULT NULL,
  `about_section_title` varchar(255) DEFAULT NULL,
  `about_section_subtitle` text DEFAULT NULL,
  `about_section_description` text DEFAULT NULL,
  `about_section_list` text DEFAULT NULL,
  `testimonials_section_title` varchar(255) DEFAULT NULL,
  `testimonials_section_description` text DEFAULT NULL,
  `team_section_title` varchar(255) DEFAULT NULL,
  `team_section_description` text DEFAULT NULL,
  `contact_section_title` varchar(255) DEFAULT NULL,
  `contact_section_description` text DEFAULT NULL,
  `cta_section_title` varchar(255) DEFAULT NULL,
  `cta_section_description` text DEFAULT NULL,
  `cta_button_text` varchar(100) DEFAULT NULL,
  `cta_button_link` varchar(255) DEFAULT NULL,
  `process_section_title` varchar(255) DEFAULT NULL,
  `process_section_description` text DEFAULT NULL,
  `mission_statement` text DEFAULT NULL,
  `value_proposition` text DEFAULT NULL,
  `service_area` varchar(255) DEFAULT NULL,
  `key_differentiator` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` varchar(100) DEFAULT 'system',
  `portfolio_section_title` varchar(255) DEFAULT 'Portfolio',
  `portfolio_section_description` text DEFAULT 'Explore some of our recent projects and creative work. Each item showcases our commitment to quality and innovation.',
  `pricing_section_title` varchar(255) DEFAULT 'Pricing',
  `pricing_section_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
