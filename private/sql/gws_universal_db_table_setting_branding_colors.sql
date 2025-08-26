
-- --------------------------------------------------------

--
-- Table structure for table `setting_branding_colors`
--

CREATE TABLE `setting_branding_colors` (
  `id` int(11) NOT NULL,
  `brand_primary_color` varchar(7) NOT NULL DEFAULT '#6c2eb6',
  `brand_secondary_color` varchar(7) NOT NULL DEFAULT '#bf5512',
  `brand_tertiary_color` varchar(7) DEFAULT '#8B4513' COMMENT 'Third brand color',
  `brand_quaternary_color` varchar(7) DEFAULT '#2E8B57' COMMENT 'Fourth brand color',
  `brand_accent_color` varchar(7) DEFAULT '#28a745',
  `brand_warning_color` varchar(7) DEFAULT '#ffc107',
  `brand_danger_color` varchar(7) DEFAULT '#dc3545',
  `brand_info_color` varchar(7) DEFAULT '#17a2b8',
  `brand_background_color` varchar(7) DEFAULT '#ffffff',
  `brand_text_color` varchar(7) DEFAULT '#333333',
  `brand_text_light` varchar(7) DEFAULT '#666666',
  `brand_text_muted` varchar(7) DEFAULT '#999999',
  `brand_font_primary` varchar(255) DEFAULT 'Inter, system-ui, sans-serif',
  `brand_font_secondary` varchar(255) DEFAULT 'Roboto, Arial, sans-serif',
  `brand_font_heading` varchar(255) DEFAULT 'Inter, system-ui, sans-serif',
  `brand_font_body` varchar(255) DEFAULT 'Roboto, Arial, sans-serif',
  `brand_font_monospace` varchar(255) DEFAULT 'SF Mono, Monaco, Consolas, monospace',
  `brand_success_color` varchar(7) DEFAULT '#28a745',
  `brand_error_color` varchar(7) DEFAULT '#dc3545',
  `custom_color_1` varchar(7) DEFAULT NULL,
  `custom_color_2` varchar(7) DEFAULT NULL,
  `custom_color_3` varchar(7) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `brand_spinner_style` varchar(50) DEFAULT 'rainbow_ring'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
