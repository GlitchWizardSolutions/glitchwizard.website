-- SQL Commands for phpMyAdmin: Add Tertiary and Quaternary Brand Colors
-- Copy and paste these commands into phpMyAdmin SQL tab

-- Step 1: Add the new columns
ALTER TABLE `setting_branding_colors` 
ADD COLUMN `brand_tertiary_color` VARCHAR(7) DEFAULT '#8B4513' COMMENT 'Third brand color' AFTER `brand_secondary_color`,
ADD COLUMN `brand_quaternary_color` VARCHAR(7) DEFAULT '#2E8B57' COMMENT 'Fourth brand color' AFTER `brand_tertiary_color`;

-- Step 2: Update existing record with default values
UPDATE `setting_branding_colors` 
SET 
    `brand_tertiary_color` = '#8B4513',
    `brand_quaternary_color` = '#2E8B57'
WHERE `id` = 1;

-- Step 3: If no record exists, create one with your current colors
INSERT IGNORE INTO `setting_branding_colors` (
    `id`,
    `brand_primary_color`, 
    `brand_secondary_color`, 
    `brand_tertiary_color`, 
    `brand_quaternary_color`,
    `brand_accent_color`,
    `brand_warning_color`, 
    `brand_danger_color`, 
    `brand_info_color`,
    `brand_background_color`, 
    `brand_text_color`, 
    `brand_text_light`, 
    `brand_text_muted`,
    `created_at`, 
    `updated_at`
) VALUES (
    1,
    '#669999',  -- Your current teal primary
    '#e7b09e',  -- Your current brown secondary  
    '#8B4513',  -- New saddle brown tertiary
    '#2E8B57',  -- New sea green quaternary
    '#ddaa50',  -- Your current gold accent
    '#ffc107',  -- Warning yellow
    '#dc3545',  -- Danger red
    '#17a2b8',  -- Info blue
    '#ffffff',  -- White background
    '#333333',  -- Dark text
    '#666666',  -- Light text
    '#999999',  -- Muted text
    NOW(),
    NOW()
);

-- Step 4: Verify everything worked
DESCRIBE `setting_branding_colors`;
SELECT * FROM `setting_branding_colors`;
