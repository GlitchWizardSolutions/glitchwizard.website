-- Create and populate business tables for Burden to Blessings
-- Uses proper normalization - business identity separate from contact info

-- First, update the existing business identity table with Burden to Blessings branding
UPDATE setting_business_identity 
SET 
    business_name_short = 'B2B',
    business_name_medium = 'Burden to Blessings',
    business_name_long = 'Burden to Blessings Home Solutions LLC',
    business_tagline_short = 'Burdens Into Blessings',
    business_tagline_medium = 'Transforming foreclosure challenges into family solutions',
    business_tagline_long = 'We handle the complexities of the foreclosure process, so you can focus on what''s next.',
    legal_business_name = 'Burden to Blessings Home Solutions LLC',
    business_type = 'Limited Liability Company',
    established_date = '2024-01-01',
    about_business = 'Burden to Blessings specializes in helping Indiana families navigate foreclosure challenges with compassion and expertise. We provide comprehensive foreclosure assistance, creative home solutions, and family-focused financial planning to transform burdens into blessings.',
    mission_statement = 'Our mission is to transform Indiana from a state burdened by high foreclosure rates to a model of financial resilience and community well-being.',
    vision_statement = 'We envision vibrant neighborhoods enriched by community support, where every family has the opportunity to thrive despite facing foreclosure challenges.',
    core_values = '["People First, House Second", "We Listen & Help", "Doing What''s Right", "Family-Focused Solutions", "Local Community Impact"]',
    footer_business_name_type = 'medium'
WHERE id = 1;

-- If no business identity record exists, create it
INSERT IGNORE INTO setting_business_identity (id, business_name_short, business_name_medium, business_name_long, business_tagline_short, business_tagline_medium, business_tagline_long, legal_business_name, business_type, about_business, mission_statement, vision_statement, core_values, footer_business_name_type)
VALUES (1, 'B2B', 'Burden to Blessings', 'Burden to Blessings Home Solutions LLC', 'Burdens Into Blessings', 'Transforming foreclosure challenges into family solutions', 'We handle the complexities of the foreclosure process, so you can focus on what''s next.', 'Burden to Blessings Home Solutions LLC', 'Limited Liability Company', 'Burden to Blessings specializes in helping Indiana families navigate foreclosure challenges with compassion and expertise.', 'Our mission is to transform Indiana from a state burdened by high foreclosure rates to a model of financial resilience and community well-being.', 'We envision vibrant neighborhoods enriched by community support, where every family has the opportunity to thrive despite foreclosure challenges.', '["People First, House Second", "We Listen & Help", "Doing What''s Right", "Family-Focused Solutions", "Local Community Impact"]', 'medium');

-- Create the business contact info table (no business name - that comes from identity table)
CREATE TABLE IF NOT EXISTS `setting_business_contact` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `business_identity_id` int(11) NOT NULL DEFAULT 1,
    `primary_email` varchar(255) DEFAULT NULL,
    `primary_phone` varchar(50) DEFAULT NULL,
    `primary_address` varchar(255) DEFAULT NULL,
    `city` varchar(100) DEFAULT NULL,
    `state` varchar(50) DEFAULT NULL,
    `zipcode` varchar(20) DEFAULT NULL,
    `country` varchar(100) DEFAULT 'United States',
    `website_url` varchar(255) DEFAULT NULL,
    `business_hours` text DEFAULT NULL,
    `secondary_phone` varchar(50) DEFAULT NULL,
    `fax_number` varchar(50) DEFAULT NULL,
    `mailing_address` varchar(255) DEFAULT NULL,
    `mailing_city` varchar(100) DEFAULT NULL,
    `mailing_state` varchar(50) DEFAULT NULL,
    `mailing_zipcode` varchar(20) DEFAULT NULL,
    `social_facebook` varchar(255) DEFAULT NULL,
    `social_instagram` varchar(255) DEFAULT NULL,
    `social_twitter` varchar(255) DEFAULT NULL,
    `social_linkedin` varchar(255) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    FOREIGN KEY (`business_identity_id`) REFERENCES `setting_business_identity`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert Burden to Blessings contact information
INSERT INTO `setting_business_contact` (
    `id`,
    `business_identity_id`,
    `primary_email`, 
    `primary_phone`, 
    `primary_address`, 
    `city`, 
    `state`, 
    `zipcode`,
    `country`,
    `website_url`,
    `social_facebook`,
    `social_instagram`
) VALUES (
    1,
    1,
    'help@burden-to-blessings.com',
    '(574) 633-1736',
    '5776 Grape Rd. STE 51, PMB 141',
    'Mishawaka',
    'IN',
    '46545',
    'United States',
    'https://burden-to-blessings.com',
    'https://www.facebook.com/share/1AXCLnLRmr/',
    'https://www.instagram.com/burden_to_blessings?igsh=cm0zenZoYTV6NjRm'
) ON DUPLICATE KEY UPDATE
    `primary_email` = VALUES(`primary_email`),
    `primary_phone` = VALUES(`primary_phone`),
    `primary_address` = VALUES(`primary_address`),
    `city` = VALUES(`city`),
    `state` = VALUES(`state`),
    `zipcode` = VALUES(`zipcode`),
    `country` = VALUES(`country`),
    `website_url` = VALUES(`website_url`),
    `social_facebook` = VALUES(`social_facebook`),
    `social_instagram` = VALUES(`social_instagram`);

-- Verify the data was inserted
SELECT 
    bi.business_name_medium,
    bi.business_tagline_medium, 
    bc.primary_email, 
    bc.primary_phone, 
    bc.city, 
    bc.state 
FROM setting_business_identity bi 
JOIN setting_business_contact bc ON bi.id = bc.business_identity_id 
WHERE bi.id = 1;
