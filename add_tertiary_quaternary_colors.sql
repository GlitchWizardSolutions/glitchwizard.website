-- SQL Script to Add Tertiary and Quaternary Brand Colors
-- Run this in your MySQL database to add the new color columns

-- Add Tertiary and Quaternary color columns to setting_branding_colors table
ALTER TABLE `setting_branding_colors` 
ADD COLUMN `brand_tertiary_color` varchar(7) DEFAULT '#8B4513' COMMENT 'Third brand color' AFTER `brand_secondary_color`,
ADD COLUMN `brand_quaternary_color` varchar(7) DEFAULT '#2E8B57' COMMENT 'Fourth brand color' AFTER `brand_tertiary_color`;

-- Update the existing record to set default values for the new columns
UPDATE `setting_branding_colors` 
SET 
    `brand_tertiary_color` = '#8B4513',
    `brand_quaternary_color` = '#2E8B57'
WHERE `id` = 1;

-- Verify the changes
SELECT 
    id,
    brand_primary_color,
    brand_secondary_color,
    brand_tertiary_color,
    brand_quaternary_color,
    brand_accent_color,
    last_updated
FROM setting_branding_colors;

-- Alternative: If you prefer to use the existing custom_color columns instead:
-- UPDATE `setting_branding_colors` 
-- SET 
--     `custom_color_1` = '#8B4513',  -- Use as Tertiary
--     `custom_color_2` = '#2E8B57',  -- Use as Quaternary
--     `custom_color_3` = NULL        -- Keep for future use
-- WHERE `id` = 1;

-- INTEGRATION: Update the admin form to also save to database
-- The PHP admin form should use both systems:
-- 1. Save to database (setting_branding_colors table)
-- 2. Save to PHP file (for backward compatibility)
