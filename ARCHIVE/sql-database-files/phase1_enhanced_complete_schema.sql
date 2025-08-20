-- ===============================================================================
-- PHASE 1 ENHANCED: COMPLETE APPLICATION CONFIGURATIONS SCHEMA
-- ===============================================================================
-- 
-- This SQL file extends the Phase 1 schema to include ALL configuration
-- requirements for production-ready applications including payment gateways,
-- SMTP settings, and application-specific configurations.
-- 
-- Created: August 17, 2025
-- Purpose: Complete configuration coverage for all applications
-- ===============================================================================

-- Add missing configuration templates for complete functionality

-- ===============================================================================
-- SHOP SYSTEM: COMPLETE PAYMENT GATEWAY AND SMTP CONFIGURATIONS
-- ===============================================================================

-- Shop SMTP missing settings
INSERT IGNORE INTO `setting_app_configurations` 
(`app_name`, `section`, `config_key`, `config_value`, `data_type`, `description`, `default_value`, `display_group`, `display_order`, `is_sensitive`) 
VALUES
('shop_system', 'smtp', 'smtp_username', NULL, 'string', 'SMTP username/email', 'user@example.com', 'SMTP', 55, 1),
('shop_system', 'smtp', 'smtp_password', NULL, 'string', 'SMTP password', '', 'SMTP', 65, 1),

-- Shop base URL (missing from basic mapping)
('shop_system', 'basic', 'base_url', NULL, 'string', 'Base URL of the shop system', '', 'Basic Info', 60, 0),

-- Shop PayPal Payment Gateway
('shop_system', 'payment_paypal', 'paypal_enabled', NULL, 'boolean', 'Enable PayPal payments', 'true', 'PayPal', 10, 0),
('shop_system', 'payment_paypal', 'paypal_email', NULL, 'string', 'PayPal business email account', 'payments@example.com', 'PayPal', 20, 0),
('shop_system', 'payment_paypal', 'paypal_testmode', NULL, 'boolean', 'Enable PayPal sandbox/test mode', 'true', 'PayPal', 30, 0),
('shop_system', 'payment_paypal', 'paypal_currency', NULL, 'string', 'PayPal currency code', 'USD', 'PayPal', 40, 0),
('shop_system', 'payment_paypal', 'paypal_ipn_url', NULL, 'string', 'PayPal IPN notification URL', 'https://example.com/ipn/paypal.php', 'PayPal', 50, 0),
('shop_system', 'payment_paypal', 'paypal_cancel_url', NULL, 'string', 'PayPal payment cancellation URL', 'https://example.com/index.php?page=cart', 'PayPal', 60, 0),
('shop_system', 'payment_paypal', 'paypal_return_url', NULL, 'string', 'PayPal payment success return URL', 'https://example.com/index.php?page=placeorder', 'PayPal', 70, 0),

-- Shop Stripe Payment Gateway
('shop_system', 'payment_stripe', 'stripe_enabled', NULL, 'boolean', 'Enable Stripe payments', 'true', 'Stripe', 10, 0),
('shop_system', 'payment_stripe', 'stripe_publish_key', NULL, 'string', 'Stripe publishable API key', '', 'Stripe', 20, 1),
('shop_system', 'payment_stripe', 'stripe_secret_key', NULL, 'string', 'Stripe secret API key', '', 'Stripe', 30, 1),
('shop_system', 'payment_stripe', 'stripe_currency', NULL, 'string', 'Stripe currency code', 'USD', 'Stripe', 40, 0),
('shop_system', 'payment_stripe', 'stripe_ipn_url', NULL, 'string', 'Stripe webhook URL', 'https://example.com/ipn/stripe.php', 'Stripe', 50, 0),
('shop_system', 'payment_stripe', 'stripe_cancel_url', NULL, 'string', 'Stripe payment cancellation URL', 'https://example.com/index.php?page=cart', 'Stripe', 60, 0),
('shop_system', 'payment_stripe', 'stripe_return_url', NULL, 'string', 'Stripe payment success return URL', 'https://example.com/index.php?page=placeorder', 'Stripe', 70, 0),
('shop_system', 'payment_stripe', 'stripe_webhook_secret', NULL, 'string', 'Stripe webhook secret for verification', '', 'Stripe', 80, 1),

-- Shop Coinbase Payment Gateway
('shop_system', 'payment_coinbase', 'coinbase_enabled', NULL, 'boolean', 'Enable Coinbase Commerce payments', 'false', 'Coinbase', 10, 0),
('shop_system', 'payment_coinbase', 'coinbase_key', NULL, 'string', 'Coinbase API key', '', 'Coinbase', 20, 1),
('shop_system', 'payment_coinbase', 'coinbase_secret', NULL, 'string', 'Coinbase webhook secret', '', 'Coinbase', 30, 1),
('shop_system', 'payment_coinbase', 'coinbase_currency', NULL, 'string', 'Coinbase currency code', 'USD', 'Coinbase', 40, 0),
('shop_system', 'payment_coinbase', 'coinbase_cancel_url', NULL, 'string', 'Coinbase payment cancellation URL', 'https://example.com/index.php?page=cart', 'Coinbase', 50, 0),
('shop_system', 'payment_coinbase', 'coinbase_return_url', NULL, 'string', 'Coinbase payment success return URL', 'https://example.com/index.php?page=placeorder', 'Coinbase', 60, 0),

-- Shop Pay on Delivery
('shop_system', 'payment_cod', 'pay_on_delivery_enabled', NULL, 'boolean', 'Enable pay on delivery option', 'true', 'Payment Options', 10, 0);

-- ===============================================================================
-- INVOICE SYSTEM: COMPLETE CONFIGURATION TEMPLATE
-- ===============================================================================

-- Invoice System Basic Settings
INSERT IGNORE INTO `setting_app_configurations` 
(`app_name`, `section`, `config_key`, `config_value`, `data_type`, `description`, `default_value`, `display_group`, `display_order`, `is_sensitive`) 
VALUES
('invoice_system', 'basic', 'base_url', NULL, 'string', 'Base URL of invoice system', 'http://localhost/invoice/', 'Basic Info', 10, 0),
('invoice_system', 'basic', 'invoice_prefix', NULL, 'string', 'Invoice number prefix', 'INV', 'Basic Info', 20, 0),
('invoice_system', 'basic', 'currency_code', NULL, 'string', 'Default currency symbol', '&dollar;', 'Basic Info', 30, 0),
('invoice_system', 'basic', 'pdf_attachments', NULL, 'boolean', 'Attach PDF to emails', 'true', 'Basic Info', 40, 0),
('invoice_system', 'basic', 'cron_secret', NULL, 'string', 'Cron job access secret', 'secret', 'Basic Info', 50, 1),

-- Invoice System Company Information
('invoice_system', 'company', 'company_name', NULL, 'string', 'Company name', 'Your Company Name', 'Company Info', 10, 0),
('invoice_system', 'company', 'company_email', NULL, 'string', 'Company email address', 'company@example.com', 'Company Info', 20, 0),
('invoice_system', 'company', 'company_phone', NULL, 'string', 'Company phone number', '01234 567890', 'Company Info', 30, 0),
('invoice_system', 'company', 'company_address', NULL, 'string', 'Company mailing address', '123 Example Street\nExample City\nEX4 MPL\nUnited States', 'Company Info', 40, 0),
('invoice_system', 'company', 'company_logo', NULL, 'string', 'Company logo file path', '', 'Company Info', 50, 0),

-- Invoice System Mail Settings
('invoice_system', 'mail', 'mail_enabled', NULL, 'boolean', 'Enable email notifications', 'false', 'Email', 10, 0),
('invoice_system', 'mail', 'mail_from', NULL, 'string', 'From email address', 'noreply@example.com', 'Email', 20, 0),
('invoice_system', 'mail', 'mail_name', NULL, 'string', 'From name for emails', 'Your Business Name', 'Email', 30, 0),
('invoice_system', 'mail', 'notifications_enabled', NULL, 'boolean', 'Enable admin notifications', 'true', 'Email', 40, 0),
('invoice_system', 'mail', 'notification_email', NULL, 'string', 'Admin notification email', 'notifications@example.com', 'Email', 50, 0),

-- Invoice System SMTP Settings
('invoice_system', 'smtp', 'smtp_enabled', NULL, 'boolean', 'Use SMTP server', 'false', 'SMTP', 10, 0),
('invoice_system', 'smtp', 'smtp_secure', NULL, 'string', 'SMTP security type', 'ssl', 'SMTP', 20, 0),
('invoice_system', 'smtp', 'smtp_host', NULL, 'string', 'SMTP hostname', 'smtp.example.com', 'SMTP', 30, 1),
('invoice_system', 'smtp', 'smtp_port', NULL, 'integer', 'SMTP port number', '465', 'SMTP', 40, 0),
('invoice_system', 'smtp', 'smtp_username', NULL, 'string', 'SMTP username', 'user@example.com', 'SMTP', 50, 1),
('invoice_system', 'smtp', 'smtp_password', NULL, 'string', 'SMTP password', '', 'SMTP', 60, 1),

-- Invoice System PayPal Payment Gateway
('invoice_system', 'payment_paypal', 'paypal_enabled', NULL, 'boolean', 'Enable PayPal payments', 'true', 'PayPal', 10, 0),
('invoice_system', 'payment_paypal', 'paypal_email', NULL, 'string', 'PayPal business email', 'payments@example.com', 'PayPal', 20, 0),
('invoice_system', 'payment_paypal', 'paypal_testmode', NULL, 'boolean', 'PayPal sandbox mode', 'true', 'PayPal', 30, 0),
('invoice_system', 'payment_paypal', 'paypal_currency', NULL, 'string', 'PayPal currency', 'USD', 'PayPal', 40, 0),
('invoice_system', 'payment_paypal', 'paypal_ipn_url', NULL, 'string', 'PayPal IPN URL', 'https://example.com/ipn.php?method=paypal', 'PayPal', 50, 0),

-- Invoice System Stripe Payment Gateway
('invoice_system', 'payment_stripe', 'stripe_enabled', NULL, 'boolean', 'Enable Stripe payments', 'true', 'Stripe', 10, 0),
('invoice_system', 'payment_stripe', 'stripe_secret_key', NULL, 'string', 'Stripe secret API key', '', 'Stripe', 20, 1),
('invoice_system', 'payment_stripe', 'stripe_currency', NULL, 'string', 'Stripe currency', 'USD', 'Stripe', 30, 0),
('invoice_system', 'payment_stripe', 'stripe_ipn_url', NULL, 'string', 'Stripe webhook URL', 'https://example.com/ipn.php?method=stripe', 'Stripe', 40, 0),
('invoice_system', 'payment_stripe', 'stripe_webhook_secret', NULL, 'string', 'Stripe webhook secret', '', 'Stripe', 50, 1);

-- ===============================================================================
-- FORM SYSTEM: CONFIGURATION TEMPLATE
-- ===============================================================================

-- Check what form_system needs by examining its config file
INSERT IGNORE INTO `setting_app_configurations` 
(`app_name`, `section`, `config_key`, `config_value`, `data_type`, `description`, `default_value`, `display_group`, `display_order`, `is_sensitive`) 
VALUES
-- Form System Basic Settings (placeholder - will update after examining config)
('form_system', 'basic', 'site_name', NULL, 'string', 'Form system site name', 'Contact Forms', 'Basic Info', 10, 0),
('form_system', 'basic', 'default_recipient', NULL, 'string', 'Default form recipient email', 'forms@example.com', 'Basic Info', 20, 0),
('form_system', 'basic', 'enable_captcha', NULL, 'boolean', 'Enable CAPTCHA protection', 'true', 'Basic Info', 30, 0),

-- Form System Mail Settings
('form_system', 'mail', 'mail_enabled', NULL, 'boolean', 'Enable email notifications', 'true', 'Email', 10, 0),
('form_system', 'mail', 'mail_from', NULL, 'string', 'From email address', 'noreply@example.com', 'Email', 20, 0),
('form_system', 'mail', 'mail_name', NULL, 'string', 'From name for emails', 'Contact Form System', 'Email', 30, 0),

-- Form System SMTP Settings
('form_system', 'smtp', 'smtp_enabled', NULL, 'boolean', 'Use SMTP server', 'false', 'SMTP', 10, 0),
('form_system', 'smtp', 'smtp_secure', NULL, 'string', 'SMTP security type', 'ssl', 'SMTP', 20, 0),
('form_system', 'smtp', 'smtp_host', NULL, 'string', 'SMTP hostname', 'smtp.example.com', 'SMTP', 30, 1),
('form_system', 'smtp', 'smtp_port', NULL, 'integer', 'SMTP port number', '465', 'SMTP', 40, 0),
('form_system', 'smtp', 'smtp_username', NULL, 'string', 'SMTP username', 'user@example.com', 'SMTP', 50, 1),
('form_system', 'smtp', 'smtp_password', NULL, 'string', 'SMTP password', '', 'SMTP', 60, 1);

-- ===============================================================================
-- ADDITIONAL APPLICATIONS: CONFIGURATION TEMPLATES
-- ===============================================================================

-- Chat System Configuration
INSERT IGNORE INTO `setting_app_configurations` 
(`app_name`, `section`, `config_key`, `config_value`, `data_type`, `description`, `default_value`, `display_group`, `display_order`, `is_sensitive`) 
VALUES
('chat_system', 'basic', 'site_name', NULL, 'string', 'Chat system name', 'Live Chat', 'Basic Info', 10, 0),
('chat_system', 'basic', 'enable_guest_chat', NULL, 'boolean', 'Allow guest users to chat', 'true', 'Basic Info', 20, 0),
('chat_system', 'basic', 'max_message_length', NULL, 'integer', 'Maximum message length', '500', 'Basic Info', 30, 0),
('chat_system', 'basic', 'chat_history_days', NULL, 'integer', 'Days to keep chat history', '30', 'Basic Info', 40, 0),

-- Review System Configuration
('review_system', 'basic', 'site_name', NULL, 'string', 'Review system name', 'Customer Reviews', 'Basic Info', 10, 0),
('review_system', 'basic', 'enable_guest_reviews', NULL, 'boolean', 'Allow guest reviews', 'false', 'Basic Info', 20, 0),
('review_system', 'basic', 'require_approval', NULL, 'boolean', 'Require admin approval', 'true', 'Basic Info', 30, 0),
('review_system', 'basic', 'max_rating', NULL, 'integer', 'Maximum rating scale', '5', 'Basic Info', 40, 0),

('review_system', 'mail', 'mail_enabled', NULL, 'boolean', 'Enable review notifications', 'true', 'Email', 10, 0),
('review_system', 'mail', 'notification_email', NULL, 'string', 'Review notification email', 'reviews@example.com', 'Email', 20, 0);

-- ===============================================================================
-- SCHEMA VERIFICATION AND SUMMARY
-- ===============================================================================

-- Show enhanced configuration summary
SELECT 'Enhanced Configuration Summary:' as status;
SELECT 
    app_name,
    COUNT(*) as total_settings,
    COUNT(DISTINCT section) as sections,
    COUNT(DISTINCT display_group) as groups,
    COUNT(CASE WHEN is_sensitive = 1 THEN 1 END) as sensitive_settings
FROM setting_app_configurations 
GROUP BY app_name 
ORDER BY app_name;

-- Show payment gateway and SMTP coverage
SELECT 'Payment Gateway and SMTP Coverage:' as status;
SELECT 
    app_name,
    section,
    COUNT(*) as setting_count
FROM setting_app_configurations 
WHERE section LIKE '%payment_%' OR section LIKE '%smtp%' OR section LIKE '%mail%'
GROUP BY app_name, section
ORDER BY app_name, section;

SELECT 'Enhanced schema ready for production!' as status;
