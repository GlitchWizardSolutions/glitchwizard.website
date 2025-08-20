-- ===============================================================================
-- PHASE 1: DATA MIGRATION FROM EXISTING CONFIGURATION FILES
-- ===============================================================================
-- 
-- This SQL file migrates existing configuration data from file-based systems
-- to the new unified database configuration table.
-- 
-- Created: August 17, 2025
-- Purpose: Migrate blog_system and shop_system configurations to database
-- ===============================================================================

-- Disable foreign key checks for migration
SET FOREIGN_KEY_CHECKS = 0;

-- ===============================================================================
-- BLOG SYSTEM DATA MIGRATION
-- ===============================================================================
-- Based on: /public_html/blog_system/config_settings.php

-- Update blog identity settings with actual values from config_settings.php
UPDATE `setting_app_configurations` SET 
    `config_value` = 'https://glitchwizarddigitalsolutions.com/blog',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'blog_system' AND `section` = 'identity' AND `config_key` = 'blog_site_url';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'GlitchWizard Solutions Blog',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'blog_system' AND `section` = 'identity' AND `config_key` = 'blog_title';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'Information for the Members of GlitchWizard Solutions LLC',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'blog_system' AND `section` = 'identity' AND `config_key` = 'blog_description';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'barbara@glitchwizarddigitalsolutions.com',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'blog_system' AND `section` = 'identity' AND `config_key` = 'email';

-- Update blog display settings
UPDATE `setting_app_configurations` SET 
    `config_value` = 'F j, Y',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'blog_system' AND `section` = 'display' AND `config_key` = 'date_format';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'Wide',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'blog_system' AND `section` = 'display' AND `config_key` = 'layout';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'Enabled',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'blog_system' AND `section` = 'display' AND `config_key` = 'latestposts_bar';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'Right',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'blog_system' AND `section` = 'display' AND `config_key` = 'sidebar_position';

UPDATE `setting_app_configurations` SET 
    `config_value` = '2',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'blog_system' AND `section` = 'display' AND `config_key` = 'posts_per_row';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'Pulse',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'blog_system' AND `section` = 'display' AND `config_key` = 'theme';

UPDATE `setting_app_configurations` SET 
    `config_value` = '',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'blog_system' AND `section` = 'display' AND `config_key` = 'background_image';

-- Update blog functionality settings
UPDATE `setting_app_configurations` SET 
    `config_value` = 'guests',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'blog_system' AND `section` = 'functionality' AND `config_key` = 'comments';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'No',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'blog_system' AND `section` = 'functionality' AND `config_key` = 'rtl';

UPDATE `setting_app_configurations` SET 
    `config_value` = '',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'blog_system' AND `section` = 'functionality' AND `config_key` = 'head_customcode';

-- Update blog social media settings
UPDATE `setting_app_configurations` SET 
    `config_value` = 'https://www.facebook.com/GlitchWizardSolutions/',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'blog_system' AND `section` = 'social' AND `config_key` = 'facebook';

UPDATE `setting_app_configurations` SET 
    `config_value` = '',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'blog_system' AND `section` = 'social' AND `config_key` = 'instagram';

UPDATE `setting_app_configurations` SET 
    `config_value` = '',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'blog_system' AND `section` = 'social' AND `config_key` = 'twitter';

UPDATE `setting_app_configurations` SET 
    `config_value` = '',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'blog_system' AND `section` = 'social' AND `config_key` = 'youtube';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'https://www.linkedin.com/in/glitchwizard/',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'blog_system' AND `section` = 'social' AND `config_key` = 'linkedin';

-- Update blog security settings (sensitive data)
UPDATE `setting_app_configurations` SET 
    `config_value` = '6LdmAmgrAAAAAIdsJeCLDjkPhYeVZIH6wSGqkxIH',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'blog_system' AND `section` = 'security' AND `config_key` = 'gcaptcha_sitekey';

UPDATE `setting_app_configurations` SET 
    `config_value` = '6LdmAmgrAAAAAKXJibD69CmlnsUP5sQFIQImwODW',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'blog_system' AND `section` = 'security' AND `config_key` = 'gcaptcha_secretkey';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'glitchwizardsolu-1696110549072',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'blog_system' AND `section` = 'security' AND `config_key` = 'gcaptcha_projectid';

-- ===============================================================================
-- SHOP SYSTEM DATA MIGRATION  
-- ===============================================================================
-- Based on: /public_html/shop_system/config.php

-- Update shop basic settings with actual values from config.php
UPDATE `setting_app_configurations` SET 
    `config_value` = 'Shopping Cart',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'basic' AND `config_key` = 'site_name';

UPDATE `setting_app_configurations` SET 
    `config_value` = '&dollar;',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'basic' AND `config_key` = 'currency_code';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'lbs',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'basic' AND `config_key` = 'weight_unit';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'uploads/featured-image.jpg',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'basic' AND `config_key` = 'featured_image';

-- Update shop functionality settings
UPDATE `setting_app_configurations` SET 
    `config_value` = 'false',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'functionality' AND `config_key` = 'account_required';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'false',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'functionality' AND `config_key` = 'rewrite_url';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'tinymce',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'functionality' AND `config_key` = 'template_editor';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'Completed',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'functionality' AND `config_key` = 'default_payment_status';

-- Update shop mail settings
UPDATE `setting_app_configurations` SET 
    `config_value` = 'false',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'mail' AND `config_key` = 'mail_enabled';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'noreply@example.com',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'mail' AND `config_key` = 'mail_from';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'Your Website/Business Name',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'mail' AND `config_key` = 'mail_name';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'true',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'mail' AND `config_key` = 'notifications_enabled';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'notifications@example.com',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'mail' AND `config_key` = 'notification_email';

-- Update shop SMTP settings
UPDATE `setting_app_configurations` SET 
    `config_value` = 'false',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'smtp' AND `config_key` = 'smtp_enabled';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'ssl',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'smtp' AND `config_key` = 'smtp_secure';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'smtp.example.com',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'smtp' AND `config_key` = 'smtp_host';

UPDATE `setting_app_configurations` SET 
    `config_value` = '465',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'smtp' AND `config_key` = 'smtp_port';

-- Update shop security settings
UPDATE `setting_app_configurations` SET 
    `config_value` = 'YOUR_SECRET_KEY',
    `updated_by` = 'migration_script',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'security' AND `config_key` = 'secret_key';

-- ===============================================================================
-- MIGRATION AUDIT LOG
-- ===============================================================================

-- Insert audit records for the migration
INSERT INTO `setting_app_configurations_audit` 
(`config_id`, `app_name`, `section`, `config_key`, `old_value`, `new_value`, `change_type`, `changed_by`, `change_reason`, `ip_address`)
SELECT 
    ac.id,
    ac.app_name,
    ac.section,
    ac.config_key,
    ac.default_value,
    ac.config_value,
    'UPDATE',
    'migration_script',
    'Phase 1 migration from file-based configuration to database',
    '127.0.0.1'
FROM setting_app_configurations ac
WHERE ac.updated_by = 'migration_script'
AND ac.config_value IS NOT NULL
AND ac.config_value != '';

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- ===============================================================================
-- MIGRATION VERIFICATION
-- ===============================================================================

-- Show migration results
SELECT 'Migration Summary:' as status;

SELECT 
    app_name,
    section,
    COUNT(*) as migrated_settings,
    COUNT(CASE WHEN config_value IS NOT NULL AND config_value != '' THEN 1 END) as populated_settings,
    COUNT(CASE WHEN config_value IS NULL OR config_value = '' THEN 1 END) as empty_settings
FROM setting_app_configurations 
WHERE app_name IN ('blog_system', 'shop_system')
GROUP BY app_name, section
ORDER BY app_name, section;

-- Show specific migrated values (non-sensitive only)
SELECT 'Migrated Configuration Values:' as status;

SELECT 
    app_name,
    section,
    config_key,
    CASE 
        WHEN is_sensitive = 1 THEN '***SENSITIVE***'
        ELSE config_value 
    END AS display_value,
    updated_at
FROM setting_app_configurations 
WHERE app_name IN ('blog_system', 'shop_system')
AND updated_by = 'migration_script'
AND is_active = 1
ORDER BY app_name, section, display_order;

-- Show audit trail
SELECT 'Audit Trail Summary:' as status;

SELECT 
    app_name,
    change_type,
    COUNT(*) as change_count,
    MIN(created_at) as first_change,
    MAX(created_at) as last_change
FROM setting_app_configurations_audit
WHERE changed_by = 'migration_script'
GROUP BY app_name, change_type
ORDER BY app_name;

SELECT 'Migration completed successfully!' as status;
