-- ===============================================================================
-- PHASE 1 ENHANCED: COMPLETE DATA MIGRATION FOR ALL APPLICATIONS
-- ===============================================================================
-- 
-- This SQL file migrates ALL configuration data from existing files including
-- payment gateways, SMTP settings, and application-specific configurations.
-- 
-- Created: August 17, 2025
-- Purpose: Complete production-ready configuration migration
-- ===============================================================================

-- Disable foreign key checks for migration
SET FOREIGN_KEY_CHECKS = 0;

-- ===============================================================================
-- SHOP SYSTEM: COMPLETE CONFIGURATION MIGRATION
-- ===============================================================================
-- Based on: /public_html/shop_system/config.php

-- Update missing SMTP settings for shop system
UPDATE `setting_app_configurations` SET 
    `config_value` = 'user@example.com',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'smtp' AND `config_key` = 'smtp_username';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'secret',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'smtp' AND `config_key` = 'smtp_password';

-- Update base URL
UPDATE `setting_app_configurations` SET 
    `config_value` = 'http://yourdomain.com/shoppingcart/',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'basic' AND `config_key` = 'base_url';

-- PayPal Payment Gateway Settings
UPDATE `setting_app_configurations` SET 
    `config_value` = 'true',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'payment_paypal' AND `config_key` = 'paypal_enabled';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'payments@example.com',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'payment_paypal' AND `config_key` = 'paypal_email';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'true',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'payment_paypal' AND `config_key` = 'paypal_testmode';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'USD',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'payment_paypal' AND `config_key` = 'paypal_currency';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'https://example.com/ipn/paypal.php',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'payment_paypal' AND `config_key` = 'paypal_ipn_url';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'https://example.com/index.php?page=cart',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'payment_paypal' AND `config_key` = 'paypal_cancel_url';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'https://example.com/index.php?page=placeorder',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'payment_paypal' AND `config_key` = 'paypal_return_url';

-- Stripe Payment Gateway Settings
UPDATE `setting_app_configurations` SET 
    `config_value` = 'true',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'payment_stripe' AND `config_key` = 'stripe_enabled';

UPDATE `setting_app_configurations` SET 
    `config_value` = '',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'payment_stripe' AND `config_key` = 'stripe_publish_key';

UPDATE `setting_app_configurations` SET 
    `config_value` = '',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'payment_stripe' AND `config_key` = 'stripe_secret_key';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'USD',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'payment_stripe' AND `config_key` = 'stripe_currency';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'https://example.com/ipn/stripe.php',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'payment_stripe' AND `config_key` = 'stripe_ipn_url';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'https://example.com/index.php?page=cart',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'payment_stripe' AND `config_key` = 'stripe_cancel_url';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'https://example.com/index.php?page=placeorder',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'payment_stripe' AND `config_key` = 'stripe_return_url';

UPDATE `setting_app_configurations` SET 
    `config_value` = '',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'payment_stripe' AND `config_key` = 'stripe_webhook_secret';

-- Coinbase Payment Gateway Settings
UPDATE `setting_app_configurations` SET 
    `config_value` = 'false',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'payment_coinbase' AND `config_key` = 'coinbase_enabled';

UPDATE `setting_app_configurations` SET 
    `config_value` = '',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'payment_coinbase' AND `config_key` = 'coinbase_key';

UPDATE `setting_app_configurations` SET 
    `config_value` = '',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'payment_coinbase' AND `config_key` = 'coinbase_secret';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'USD',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'payment_coinbase' AND `config_key` = 'coinbase_currency';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'https://example.com/index.php?page=cart',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'payment_coinbase' AND `config_key` = 'coinbase_cancel_url';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'https://example.com/index.php?page=placeorder',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'payment_coinbase' AND `config_key` = 'coinbase_return_url';

-- Pay on Delivery
UPDATE `setting_app_configurations` SET 
    `config_value` = 'true',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'shop_system' AND `section` = 'payment_cod' AND `config_key` = 'pay_on_delivery_enabled';

-- ===============================================================================
-- INVOICE SYSTEM: COMPLETE CONFIGURATION MIGRATION
-- ===============================================================================
-- Based on: /public_html/invoice_system/config.php

-- Invoice Basic Settings
UPDATE `setting_app_configurations` SET 
    `config_value` = 'http://localhost/projects/phpinvoice/advanced/',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'basic' AND `config_key` = 'base_url';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'INV',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'basic' AND `config_key` = 'invoice_prefix';

UPDATE `setting_app_configurations` SET 
    `config_value` = '&dollar;',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'basic' AND `config_key` = 'currency_code';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'true',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'basic' AND `config_key` = 'pdf_attachments';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'secret',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'basic' AND `config_key` = 'cron_secret';

-- Invoice Company Settings
UPDATE `setting_app_configurations` SET 
    `config_value` = 'Your Company Name',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'company' AND `config_key` = 'company_name';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'company@example.com',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'company' AND `config_key` = 'company_email';

UPDATE `setting_app_configurations` SET 
    `config_value` = '01234 567890',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'company' AND `config_key` = 'company_phone';

UPDATE `setting_app_configurations` SET 
    `config_value` = '123 Example Street\nExample City\nEX4 MPL\nUnited States',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'company' AND `config_key` = 'company_address';

UPDATE `setting_app_configurations` SET 
    `config_value` = '',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'company' AND `config_key` = 'company_logo';

-- Invoice Mail Settings
UPDATE `setting_app_configurations` SET 
    `config_value` = 'false',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'mail' AND `config_key` = 'mail_enabled';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'noreply@example.com',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'mail' AND `config_key` = 'mail_from';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'Your Website/Business Name',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'mail' AND `config_key` = 'mail_name';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'true',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'mail' AND `config_key` = 'notifications_enabled';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'notifications@example.com',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'mail' AND `config_key` = 'notification_email';

-- Invoice SMTP Settings
UPDATE `setting_app_configurations` SET 
    `config_value` = 'false',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'smtp' AND `config_key` = 'smtp_enabled';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'ssl',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'smtp' AND `config_key` = 'smtp_secure';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'smtp.example.com',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'smtp' AND `config_key` = 'smtp_host';

UPDATE `setting_app_configurations` SET 
    `config_value` = '465',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'smtp' AND `config_key` = 'smtp_port';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'user@example.com',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'smtp' AND `config_key` = 'smtp_username';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'secret',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'smtp' AND `config_key` = 'smtp_password';

-- Invoice PayPal Settings
UPDATE `setting_app_configurations` SET 
    `config_value` = 'true',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'payment_paypal' AND `config_key` = 'paypal_enabled';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'payments@example.com',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'payment_paypal' AND `config_key` = 'paypal_email';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'true',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'payment_paypal' AND `config_key` = 'paypal_testmode';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'USD',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'payment_paypal' AND `config_key` = 'paypal_currency';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'https://example.com/ipn.php?method=paypal',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'payment_paypal' AND `config_key` = 'paypal_ipn_url';

-- Invoice Stripe Settings
UPDATE `setting_app_configurations` SET 
    `config_value` = 'true',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'payment_stripe' AND `config_key` = 'stripe_enabled';

UPDATE `setting_app_configurations` SET 
    `config_value` = '',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'payment_stripe' AND `config_key` = 'stripe_secret_key';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'USD',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'payment_stripe' AND `config_key` = 'stripe_currency';

UPDATE `setting_app_configurations` SET 
    `config_value` = 'https://example.com/ipn.php?method=stripe',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'payment_stripe' AND `config_key` = 'stripe_ipn_url';

UPDATE `setting_app_configurations` SET 
    `config_value` = '',
    `updated_by` = 'enhanced_migration',
    `updated_at` = NOW()
WHERE `app_name` = 'invoice_system' AND `section` = 'payment_stripe' AND `config_key` = 'stripe_webhook_secret';

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- ===============================================================================
-- ENHANCED MIGRATION VERIFICATION
-- ===============================================================================

-- Show complete migration results
SELECT 'Enhanced Migration Summary:' as status;

SELECT 
    app_name,
    section,
    COUNT(*) as total_settings,
    COUNT(CASE WHEN config_value IS NOT NULL AND config_value != '' THEN 1 END) as populated_settings,
    COUNT(CASE WHEN config_value IS NULL OR config_value = '' THEN 1 END) as empty_settings,
    ROUND((COUNT(CASE WHEN config_value IS NOT NULL AND config_value != '' THEN 1 END) / COUNT(*)) * 100, 1) as completion_percentage
FROM setting_app_configurations 
WHERE app_name IN ('blog_system', 'shop_system', 'invoice_system', 'form_system', 'accounts_system')
GROUP BY app_name, section
ORDER BY app_name, section;

-- Show payment gateway coverage
SELECT 'Payment Gateway Coverage:' as status;
SELECT 
    app_name,
    COUNT(CASE WHEN section LIKE 'payment_%' THEN 1 END) as payment_settings,
    COUNT(CASE WHEN section LIKE 'payment_%' AND config_value IS NOT NULL AND config_value != '' THEN 1 END) as configured_payment_settings
FROM setting_app_configurations 
GROUP BY app_name
HAVING payment_settings > 0
ORDER BY app_name;

-- Show SMTP coverage
SELECT 'SMTP Configuration Coverage:' as status;
SELECT 
    app_name,
    COUNT(CASE WHEN section = 'smtp' THEN 1 END) as smtp_settings,
    COUNT(CASE WHEN section = 'smtp' AND config_value IS NOT NULL AND config_value != '' THEN 1 END) as configured_smtp_settings
FROM setting_app_configurations 
GROUP BY app_name
HAVING smtp_settings > 0
ORDER BY app_name;

-- Show sensitive data protection
SELECT 'Sensitive Data Protection Summary:' as status;
SELECT 
    app_name,
    COUNT(CASE WHEN is_sensitive = 1 THEN 1 END) as sensitive_settings,
    COUNT(CASE WHEN is_sensitive = 1 AND config_value IS NOT NULL AND config_value != '' THEN 1 END) as populated_sensitive_settings
FROM setting_app_configurations 
GROUP BY app_name
HAVING sensitive_settings > 0
ORDER BY app_name;

SELECT 'Complete production-ready migration finished!' as status;
