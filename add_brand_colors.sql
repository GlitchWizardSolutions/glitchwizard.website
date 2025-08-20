-- Add Tertiary and Quaternary Brand Colors to Database
-- This script adds the missing columns for the enhanced branding system

-- Add brand_tertiary_color column if it doesn't exist
ALTER TABLE setting_branding_colors 
ADD COLUMN IF NOT EXISTS brand_tertiary_color VARCHAR(7) DEFAULT '#8B4513' 
COMMENT 'Third brand color - Tertiary';

-- Add brand_quaternary_color column if it doesn't exist  
ALTER TABLE setting_branding_colors 
ADD COLUMN IF NOT EXISTS brand_quaternary_color VARCHAR(7) DEFAULT '#2E8B57' 
COMMENT 'Fourth brand color - Quaternary';

-- Update existing record with default values if null
UPDATE setting_branding_colors 
SET 
    brand_tertiary_color = COALESCE(brand_tertiary_color, '#8B4513'),
    brand_quaternary_color = COALESCE(brand_quaternary_color, '#2E8B57')
WHERE id = 1;

-- Show the updated table structure
SELECT 'Table structure updated:' as message;
DESCRIBE setting_branding_colors;

-- Show current color values
SELECT 'Current brand colors:' as message;
SELECT 
    brand_primary_color,
    brand_secondary_color,
    brand_tertiary_color,
    brand_quaternary_color,
    brand_accent_color,
    brand_warning_color,
    brand_danger_color,
    brand_info_color
FROM setting_branding_colors 
WHERE id = 1;
