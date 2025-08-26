
-- --------------------------------------------------------

--
-- Table structure for table `setting_branding_templates`
--

CREATE TABLE `setting_branding_templates` (
  `id` int(11) NOT NULL,
  `template_key` varchar(50) NOT NULL,
  `template_name` varchar(100) NOT NULL,
  `template_description` text DEFAULT NULL,
  `css_class` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `template_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`template_config`)),
  `preview_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `setting_branding_templates`
--
DELIMITER $$
CREATE TRIGGER `template_activation_control` BEFORE UPDATE ON `setting_branding_templates` FOR EACH ROW BEGIN
    -- If setting a template to active, deactivate all others
    IF NEW.is_active = TRUE AND OLD.is_active = FALSE THEN
        UPDATE setting_branding_templates 
        SET is_active = FALSE 
        WHERE template_key != NEW.template_key AND is_active = TRUE;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `template_activation_control_insert` BEFORE INSERT ON `setting_branding_templates` FOR EACH ROW BEGIN
    -- If inserting an active template, deactivate all others
    IF NEW.is_active = TRUE THEN
        UPDATE setting_branding_templates 
        SET is_active = FALSE 
        WHERE is_active = TRUE;
    END IF;
END
$$
DELIMITER ;
