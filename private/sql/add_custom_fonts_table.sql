-- =====================================================
-- Custom Fonts Table Creation
-- =====================================================
-- This table stores uploaded custom fonts for the branding system
-- Run this SQL to add custom font support to your database

CREATE TABLE IF NOT EXISTS `custom_fonts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `font_name` varchar(255) NOT NULL,
    `font_family` varchar(255) NOT NULL,
    `font_file_path` varchar(500) NOT NULL,
    `font_format` enum('woff2','woff','ttf','otf') NOT NULL DEFAULT 'woff2',
    `file_size` int(11) DEFAULT NULL,
    `is_active` tinyint(1) DEFAULT 1,
    `uploaded_date` timestamp DEFAULT CURRENT_TIMESTAMP,
    `created_by` int(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_font_family` (`font_family`),
    KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Add font columns to branding colors table
-- =====================================================
-- Add font family columns to the existing branding table

ALTER TABLE `setting_branding_colors` 
ADD COLUMN IF NOT EXISTS `brand_font_primary` varchar(255) DEFAULT 'Inter, system-ui, sans-serif' AFTER `brand_text_muted`,
ADD COLUMN IF NOT EXISTS `brand_font_secondary` varchar(255) DEFAULT 'Roboto, Arial, sans-serif' AFTER `brand_font_primary`,
ADD COLUMN IF NOT EXISTS `brand_font_heading` varchar(255) DEFAULT 'Inter, system-ui, sans-serif' AFTER `brand_font_secondary`,
ADD COLUMN IF NOT EXISTS `brand_font_body` varchar(255) DEFAULT 'Roboto, Arial, sans-serif' AFTER `brand_font_heading`,
ADD COLUMN IF NOT EXISTS `brand_font_monospace` varchar(255) DEFAULT 'SF Mono, Monaco, Consolas, monospace' AFTER `brand_font_body`;

-- =====================================================
-- Sample Custom Font Insert (Optional)
-- =====================================================
-- This is an example of how custom fonts would be stored
-- Remove or modify as needed

INSERT IGNORE INTO `custom_fonts` 
(`font_name`, `font_family`, `font_file_path`, `font_format`, `file_size`, `is_active`) 
VALUES 
('Inter Regular', 'Inter', '/admin/assets/fonts/custom/inter-regular.woff2', 'woff2', 125000, 1),
('Inter Bold', 'Inter', '/admin/assets/fonts/custom/inter-bold.woff2', 'woff2', 130000, 1),
('Roboto Regular', 'Roboto', '/admin/assets/fonts/custom/roboto-regular.woff2', 'woff2', 120000, 1);

-- =====================================================
-- Create uploads directory structure
-- =====================================================
-- Note: You will need to create these directories manually:
-- 
-- /public_html/admin/assets/fonts/
-- /public_html/admin/assets/fonts/custom/
-- 
-- Make sure the directory has proper write permissions (755 or 644)
-- and is accessible by your web server.
