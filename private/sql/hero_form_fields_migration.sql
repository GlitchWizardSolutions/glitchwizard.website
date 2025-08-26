-- Migration: Add Hero Form Text Fields to setting_content_homepage table
-- Date: 2025-08-22
-- Purpose: Add database fields for managing hero form text content

-- Add new columns for hero form text management
ALTER TABLE `setting_content_homepage` 
ADD COLUMN `hero_form_top_text` TEXT DEFAULT NULL COMMENT 'Text that appears above the hero form' AFTER `hero_button_link`,
ADD COLUMN `hero_form_side_text` VARCHAR(100) DEFAULT NULL COMMENT 'Text that appears on the left side of the hero form (e.g., GET STARTED!)' AFTER `hero_form_top_text`,
ADD COLUMN `hero_form_button_text` VARCHAR(100) DEFAULT NULL COMMENT 'Text for the hero form submit button (e.g., Get My Offer)' AFTER `hero_form_side_text`;

-- Set default values for existing records
UPDATE `setting_content_homepage` SET 
    `hero_form_top_text` = 'No obligation. No spam. Just a real offer from your local neighbor.',
    `hero_form_side_text` = 'GET STARTED!',
    `hero_form_button_text` = 'Get My Offer'
WHERE `id` = 1;

-- Create the initial record if it doesn't exist
INSERT IGNORE INTO `setting_content_homepage` (
    `id`, 
    `hero_headline`, 
    `hero_subheadline`, 
    `hero_form_top_text`, 
    `hero_form_side_text`, 
    `hero_form_button_text`,
    `hero_button_text`,
    `created_at`,
    `updated_at`,
    `updated_by`
) VALUES (
    1, 
    'Welcome to Our Website', 
    'Your trusted partner for professional services',
    'No obligation. No spam. Just a real offer from your local neighbor.',
    'GET STARTED!',
    'Get My Offer',
    'Learn More',
    NOW(),
    NOW(),
    'system'
);
