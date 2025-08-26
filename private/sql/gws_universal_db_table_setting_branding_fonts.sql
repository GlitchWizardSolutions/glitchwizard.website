
-- --------------------------------------------------------

--
-- Table structure for table `setting_branding_fonts`
--

CREATE TABLE `setting_branding_fonts` (
  `id` int(11) NOT NULL,
  `brand_font_primary` varchar(255) DEFAULT 'Roboto, Poppins, Raleway, Arial, sans-serif',
  `brand_font_headings` varchar(255) DEFAULT 'Poppins, Arial, sans-serif',
  `brand_font_body` varchar(255) DEFAULT 'Roboto, Arial, sans-serif',
  `brand_font_accent` varchar(255) DEFAULT 'Raleway, Arial, sans-serif',
  `brand_font_monospace` varchar(255) DEFAULT 'Consolas, Monaco, "Courier New", monospace',
  `brand_font_display` varchar(255) DEFAULT 'Georgia, "Times New Roman", serif',
  `brand_font_file_1` varchar(255) DEFAULT NULL,
  `brand_font_file_2` varchar(255) DEFAULT NULL,
  `brand_font_file_3` varchar(255) DEFAULT NULL,
  `brand_font_file_4` varchar(255) DEFAULT NULL,
  `brand_font_file_5` varchar(255) DEFAULT NULL,
  `brand_font_file_6` varchar(255) DEFAULT NULL,
  `font_size_base` varchar(10) DEFAULT '16px',
  `font_size_small` varchar(10) DEFAULT '14px',
  `font_size_large` varchar(10) DEFAULT '18px',
  `font_weight_normal` varchar(10) DEFAULT '400',
  `font_weight_bold` varchar(10) DEFAULT '700',
  `line_height_base` varchar(10) DEFAULT '1.5',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
