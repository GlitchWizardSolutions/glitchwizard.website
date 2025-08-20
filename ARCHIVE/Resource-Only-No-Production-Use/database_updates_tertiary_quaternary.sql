-- Database Updates for Tertiary and Quaternary Brand Colors
-- Run this SQL to add the new columns to your existing table

USE gws_universal_db;

-- Add dedicated columns for Tertiary and Quaternary colors
ALTER TABLE `setting_branding_colors` 
ADD COLUMN `brand_tertiary_color` varchar(7) DEFAULT '#8B4513' AFTER `brand_secondary_color`,
ADD COLUMN `brand_quaternary_color` varchar(7) DEFAULT '#2E8B57' AFTER `brand_tertiary_color`;

-- Update the existing record to include default values
UPDATE `setting_branding_colors` 
SET 
    `brand_tertiary_color` = '#8B4513',
    `brand_quaternary_color` = '#2E8B57'
WHERE `id` = 1;

-- Verify the update
SELECT 
    brand_primary_color,
    brand_secondary_color, 
    brand_tertiary_color,
    brand_quaternary_color,
    brand_accent_color
FROM setting_branding_colors 
WHERE id = 1;
