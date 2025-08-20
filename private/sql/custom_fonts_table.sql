-- Custom Fonts Table for Branding System
-- This table stores uploaded custom font files and font selections

CREATE TABLE IF NOT EXISTS `setting_custom_fonts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `font_name` varchar(100) NOT NULL COMMENT 'Display name for the font',
  `font_family` varchar(100) NOT NULL COMMENT 'CSS font-family value',
  `font_file_path` varchar(255) DEFAULT NULL COMMENT 'Path to uploaded font file',
  `font_type` enum('system','google','custom') NOT NULL DEFAULT 'system' COMMENT 'Type of font',
  `font_weight` varchar(20) DEFAULT 'normal' COMMENT 'Font weight (normal, bold, etc)',
  `font_style` varchar(20) DEFAULT 'normal' COMMENT 'Font style (normal, italic, etc)',
  `is_active` boolean DEFAULT TRUE COMMENT 'Whether font is available for use',
  `upload_date` timestamp DEFAULT CURRENT_TIMESTAMP,
  `font_category` enum('serif','sans-serif','monospace','display','handwriting') DEFAULT 'sans-serif' COMMENT 'Font category',
  PRIMARY KEY (`id`),
  UNIQUE KEY `font_name` (`font_name`),
  KEY `font_type` (`font_type`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Custom and system fonts for branding';

-- Insert common system fonts
INSERT INTO `setting_custom_fonts` (`font_name`, `font_family`, `font_type`, `font_category`) VALUES
('Arial', 'Arial, sans-serif', 'system', 'sans-serif'),
('Helvetica', 'Helvetica, Arial, sans-serif', 'system', 'sans-serif'),
('Times New Roman', '"Times New Roman", Times, serif', 'system', 'serif'),
('Georgia', 'Georgia, serif', 'system', 'serif'),
('Courier New', '"Courier New", Courier, monospace', 'system', 'monospace'),
('Verdana', 'Verdana, Geneva, sans-serif', 'system', 'sans-serif'),
('Trebuchet MS', '"Trebuchet MS", Arial, sans-serif', 'system', 'sans-serif'),
('Comic Sans MS', '"Comic Sans MS", cursive', 'system', 'handwriting'),
('Impact', 'Impact, Arial Black, sans-serif', 'system', 'display'),
('Lucida Console', '"Lucida Console", Monaco, monospace', 'system', 'monospace');

-- Insert popular Google Fonts
INSERT INTO `setting_custom_fonts` (`font_name`, `font_family`, `font_type`, `font_category`) VALUES
('Roboto', 'Roboto, Arial, sans-serif', 'google', 'sans-serif'),
('Open Sans', '"Open Sans", Arial, sans-serif', 'google', 'sans-serif'),
('Lato', 'Lato, Arial, sans-serif', 'google', 'sans-serif'),
('Poppins', 'Poppins, Arial, sans-serif', 'google', 'sans-serif'),
('Montserrat', 'Montserrat, Arial, sans-serif', 'google', 'sans-serif'),
('Source Sans Pro', '"Source Sans Pro", Arial, sans-serif', 'google', 'sans-serif'),
('Raleway', 'Raleway, Arial, sans-serif', 'google', 'sans-serif'),
('Ubuntu', 'Ubuntu, Arial, sans-serif', 'google', 'sans-serif'),
('Nunito', 'Nunito, Arial, sans-serif', 'google', 'sans-serif'),
('Work Sans', '"Work Sans", Arial, sans-serif', 'google', 'sans-serif'),
('Playfair Display', '"Playfair Display", Georgia, serif', 'google', 'serif'),
('Merriweather', 'Merriweather, Georgia, serif', 'google', 'serif'),
('Lora', 'Lora, Georgia, serif', 'google', 'serif'),
('Source Code Pro', '"Source Code Pro", "Courier New", monospace', 'google', 'monospace'),
('JetBrains Mono', '"JetBrains Mono", "Courier New", monospace', 'google', 'monospace'),
('Dancing Script', '"Dancing Script", cursive', 'google', 'handwriting'),
('Pacifico', 'Pacifico, cursive', 'google', 'handwriting'),
('Oswald', 'Oswald, Arial, sans-serif', 'google', 'display'),
('Anton', 'Anton, Arial, sans-serif', 'google', 'display'),
('Bebas Neue', '"Bebas Neue", Arial, sans-serif', 'google', 'display');

-- Add custom font selection table to store current font choices
CREATE TABLE IF NOT EXISTS `setting_brand_fonts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `font_role` enum('primary','headings','body','accent','monospace','display') NOT NULL COMMENT 'Role of the font',
  `selected_font_id` int(11) NOT NULL COMMENT 'ID from setting_custom_fonts table',
  `custom_fallback` varchar(255) DEFAULT NULL COMMENT 'Custom fallback fonts',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `font_role` (`font_role`),
  KEY `selected_font_id` (`selected_font_id`),
  FOREIGN KEY (`selected_font_id`) REFERENCES `setting_custom_fonts`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Current font selections for different roles';

-- Insert default font selections
INSERT INTO `setting_brand_fonts` (`font_role`, `selected_font_id`) VALUES
('primary', (SELECT id FROM setting_custom_fonts WHERE font_name = 'Roboto' LIMIT 1)),
('headings', (SELECT id FROM setting_custom_fonts WHERE font_name = 'Poppins' LIMIT 1)),
('body', (SELECT id FROM setting_custom_fonts WHERE font_name = 'Open Sans' LIMIT 1)),
('accent', (SELECT id FROM setting_custom_fonts WHERE font_name = 'Raleway' LIMIT 1)),
('monospace', (SELECT id FROM setting_custom_fonts WHERE font_name = 'Source Code Pro' LIMIT 1)),
('display', (SELECT id FROM setting_custom_fonts WHERE font_name = 'Playfair Display' LIMIT 1));
