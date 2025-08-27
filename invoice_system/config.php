<?php
/*
PAGE NAME  : config.php
LOCATION   : public_html/invoice_system/config.php
DESCRIPTION: Invoice system configuration integrated with main system database.
FUNCTION   : Configure invoice system settings using main system's database configuration.
CHANGE LOG : 2025-08-17 - Integrated with main system using PUBLIC_WEBSITE_INTEGRATION_CHECKLIST.php
*/

// Include main system configuration to get database settings
// Only include if constants are not already defined (prevents double inclusion issues)
if (!defined('db_host')) {
    require_once dirname(__DIR__, 2) . '/private/gws-universal-config.php';
}

define("base_path", __DIR__ . '/');
// Use main system database configuration
// Note: These constants may already be defined in gws-universal-config.php
if (!defined('invoice_db_host')) {
    define('invoice_db_host', db_host);
}
if (!defined('invoice_db_user')) {
    define('invoice_db_user', db_user);
}
if (!defined('invoice_db_pass')) {
    define('invoice_db_pass', db_pass);
}
if (!defined('invoice_db_name')) {
    define('invoice_db_name', db_name); // Use main database: gws_universal_db
}
if (!defined('invoice_db_charset')) {
    define('invoice_db_charset', db_charset);
}
// The invoice prefix is used in the invoice number. Set to an empty string to disable it.
define('invoice_prefix','INV');
// The default currency code.
define('currency_code','&dollar;');
// If set to true, the system will attach a PDF copy of the invoice to the email.
define('pdf_attachments',true);
// The cron secret is used to prevent unauthorized access to the cron.php file. It should be a random string.
define('cron_secret','secret');
// The base URL of the PHP invoice system (e.g. https://example.com/phpinvoice/). Must include a trailing slash.
define('base_url','http://localhost/projects/phpinvoice/advanced/');
/* Company */
// Your company name.
define('company_name','Your Company Name');
// Your company email address.
define('company_email','company@example.com');
// Your company phone number.
define('company_phone','01234 567890');
// Your company address.
define('company_address','123 Example Street\nExample City\nEX4 MPL\nUnited States');
// Your company logo.
define('company_logo','');
/* Mail */
// If enabled, the website will send an email to the client when a new invoice is created.
define('mail_enabled',false);
// Send mail from which address?
define('mail_from','noreply@example.com');
// The name of your website/business.
define('mail_name','Your Website/Business Name');
// If enabled, you will receive email notifications when a new payment is received.
define('notifications_enabled',true);
// The email address to send notification emails to.
define('notification_email','notifications@example.com');
// Is SMTP server?
define('SMTP',false);
// The SMTP Secure connection type (ssl, tls).
define('smtp_secure','ssl');
// This can be found in your email provider's settings.
define('smtp_host','smtp.example.com');
// This can be found in your email provider's settings.
define('smtp_port',465);
// This can be found in your email provider's settings.
define('smtp_user','user@example.com');
// This can be found in your email provider's settings.
define('smtp_pass','secret');
/* PayPal */
// Accept payments with PayPal?
define('paypal_enabled',true);
// Your business email account, which is where you'll receive the payments.
define('paypal_email','payments@example.com');
// If the test mode is set to true it will use the PayPal sandbox website, which is used for testing purposes.
// Read more about PayPal sandbox here: https://developer.paypal.com/developer/accounts/
// Set this to false when you're ready to start accepting payments on your website.
define('paypal_testmode',true);
// Currency to use with PayPal (default is USD).
define('paypal_currency','USD');
// This should point to the IPN file located in the "ipn" directory.
define('paypal_ipn_url','https://example.com/ipn.php?method=paypal');
/* Stripe */
// Accept payments with Stripe?
define('stripe_enabled',true);
// Stripe Secret API Key
define('stripe_secret_key','');
// Stripe currency
define('stripe_currency','USD');
// This should point to the IPN file located in the "ipn" directory.
define('stripe_ipn_url','https://example.com/ipn.php?method=stripe');
// This is used to verify the webhook request. You can find this in the webhook settings in your stripe dashboard.
define('stripe_webhook_secret','');
/* Coinbase */
// Create a new webhook endpoint in the coinbase commerce dashboard and add the full url to the IPN file along with the key parameter
// Webhook endpoint URL example: https://example.com/ipn.php?method=coinbase&key=SAME_AS_COINBASE_SECRET
// Accept payments with coinbase?
define('coinbase_enabled',false);
// Coinbase API Key
define('coinbase_key','');
// Coinbase Secret
define('coinbase_secret','');
// Coinbase currency
define('coinbase_currency','USD');

// Uncomment the below to output all errors
// ini_set('log_errors', true);
// ini_set('error_log', 'error.log');
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Uncomment the below to set the default timezone
// date_default_timezone_set('Europe/London');
?>