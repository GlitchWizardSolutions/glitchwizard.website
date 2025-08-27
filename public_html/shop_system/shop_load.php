<?php
/*
 * SHOP SYSTEM INTEGRATION LOADER
 * LOCATION: public_html/shop_system/shop_load.php
 * PURPOSE: Load main site config, database, and authentication for shop system integration
 * PATTERN: Follow same integration pattern as blog_load.php
 */

// Load main site config/session

// Robustly locate and include main.php
$main_found = false;
$main_paths = [
    $_SERVER['DOCUMENT_ROOT'] . '/accounts_system/main.php',
    __DIR__ . '/../accounts_system/main.php',
    __DIR__ . '/../../accounts_system/main.php',
    __DIR__ . '/accounts_system/main.php',
    __DIR__ . '/main.php',
];
foreach ($main_paths as $main_path) {
    if (file_exists($main_path)) {
        include_once $main_path;
        $main_found = true;
        break;
    }
}
if (!$main_found) {
    die('Critical error: Could not locate main.php');
}

// Set timezone to EST for consistent time display
date_default_timezone_set('America/New_York');

// Load shop settings (create if doesn't exist)
$shop_settings_found = false;
$shop_settings_paths = [
    __DIR__ . '/shop_settings.php',
    __DIR__ . '/../settings/shop_settings.php',
    $_SERVER['DOCUMENT_ROOT'] . '/settings/shop_settings.php',
];
foreach ($shop_settings_paths as $shop_settings_path) {
    if (file_exists($shop_settings_path)) {
        include_once $shop_settings_path;
        $shop_settings_found = true;
        break;
    }
}

// Create default shop settings if not found
if (!$shop_settings_found) {
    // Define default shop settings
    $shop_name = 'Online Shop';
    $currency_code = '&dollar;';
    $featured_image = 'uploads/featured-image.jpg';
    $default_payment_status = 'Completed';
    $account_required = false;
    $weight_unit = 'lbs';
    $rewrite_url = false;
    $template_editor = 'tinymce';
    $secret_key = 'YOUR_SECRET_KEY';
    
    // Mail settings
    $mail_enabled = false;
    $mail_from = 'noreply@example.com';
    $mail_name = 'Your Website/Business Name';
    $notifications_enabled = true;
    $notification_email = 'notifications@example.com';
    $smtp = false;
    $smtp_secure = 'ssl';
    $smtp_host = 'smtp.example.com';
    $smtp_port = 465;
    $smtp_user = 'user@example.com';
    $smtp_pass = 'secret';
    
    // Payment settings
    $pay_on_delivery_enabled = true;
    $paypal_enabled = true;
    $paypal_business = 'business@example.com';
    $paypal_sandbox = true;
    $paypal_auto_return = true;
    $stripe_enabled = false;
    $stripe_publishable_key = '';
    $stripe_secret_key = '';
    $coinbase_enabled = false;
    $coinbase_api_key = '';
    $coinbase_webhook_secret = '';
}

// Define shop constants using loaded settings or defaults
if (!defined('site_name')) define('site_name', $shop_name ?? 'Online Shop');
if (!defined('currency_code')) define('currency_code', $currency_code ?? '&dollar;');
if (!defined('featured_image')) define('featured_image', $featured_image ?? 'uploads/featured-image.jpg');
if (!defined('default_payment_status')) define('default_payment_status', $default_payment_status ?? 'Completed');
if (!defined('account_required')) define('account_required', $account_required ?? false);
if (!defined('weight_unit')) define('weight_unit', $weight_unit ?? 'lbs');
if (!defined('rewrite_url')) define('rewrite_url', $rewrite_url ?? false);
if (!defined('template_editor')) define('template_editor', $template_editor ?? 'tinymce');
if (!defined('secret_key')) define('secret_key', $secret_key ?? 'YOUR_SECRET_KEY');

// Determine the base URL for shop system
$protocol = ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] == 1)) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) ? 'https' : 'http';
$host = rtrim($_SERVER['HTTP_HOST'], '/');
$port = (in_array($_SERVER['SERVER_PORT'], [80, 443]) || strpos($_SERVER['HTTP_HOST'], ':') !== false) ? '' : ':' . $_SERVER['SERVER_PORT'];
$path = '/' . ltrim(substr(str_replace('\\', '/', realpath(__DIR__)), strlen(rtrim($_SERVER['DOCUMENT_ROOT'], '/'))), '/');
if (!defined('base_url')) define("base_url", rtrim("$protocol://$host$port$path", '/') . '/');

// Mail settings
if (!defined('mail_enabled')) define('mail_enabled', $mail_enabled ?? false);
if (!defined('mail_from')) define('mail_from', $mail_from ?? 'noreply@example.com');
if (!defined('mail_name')) define('mail_name', $mail_name ?? 'Your Website/Business Name');
if (!defined('notifications_enabled')) define('notifications_enabled', $notifications_enabled ?? true);
if (!defined('notification_email')) define('notification_email', $notification_email ?? 'notifications@example.com');
if (!defined('SMTP')) define('SMTP', $smtp ?? false);
if (!defined('smtp_secure')) define('smtp_secure', $smtp_secure ?? 'ssl');
if (!defined('smtp_host')) define('smtp_host', $smtp_host ?? 'smtp.example.com');
if (!defined('smtp_port')) define('smtp_port', $smtp_port ?? 465);
if (!defined('smtp_user')) define('smtp_user', $smtp_user ?? 'user@example.com');
if (!defined('smtp_pass')) define('smtp_pass', $smtp_pass ?? 'secret');

// Payment settings
if (!defined('pay_on_delivery_enabled')) define('pay_on_delivery_enabled', $pay_on_delivery_enabled ?? true);
if (!defined('paypal_enabled')) define('paypal_enabled', $paypal_enabled ?? true);
if (!defined('paypal_business')) define('paypal_business', $paypal_business ?? 'business@example.com');
if (!defined('paypal_sandbox')) define('paypal_sandbox', $paypal_sandbox ?? true);
if (!defined('paypal_auto_return')) define('paypal_auto_return', $paypal_auto_return ?? true);
if (!defined('stripe_enabled')) define('stripe_enabled', $stripe_enabled ?? false);
if (!defined('stripe_publishable_key')) define('stripe_publishable_key', $stripe_publishable_key ?? '');
if (!defined('stripe_secret_key')) define('stripe_secret_key', $stripe_secret_key ?? '');
if (!defined('coinbase_enabled')) define('coinbase_enabled', $coinbase_enabled ?? false);
if (!defined('coinbase_api_key')) define('coinbase_api_key', $coinbase_api_key ?? '');
if (!defined('coinbase_webhook_secret')) define('coinbase_webhook_secret', $coinbase_webhook_secret ?? '');

// Define $rowusers for role checks in shop functions
$rowusers = null;
if (isset($logged_in) && $logged_in && isset($_SESSION['id'])) {
    $stmt_rowusers = $pdo->prepare('SELECT * FROM accounts WHERE id = ? LIMIT 1');
    $stmt_rowusers->execute([$_SESSION['id']]);
    $rowusers = $stmt_rowusers->fetch(PDO::FETCH_ASSOC);
}

// Shop authentication helpers
function shop_require_login() {
    global $logged_in;
    if (!$logged_in) {
        header('Location: ../auth.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

function shop_require_admin() {
    global $logged_in, $rowusers;
    if (!$logged_in || !$rowusers || $rowusers['role'] !== 'admin') {
        header('Location: ../auth.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

function shop_get_user_role() {
    global $rowusers;
    return $rowusers['role'] ?? 'guest';
}

function shop_is_admin() {
    return shop_get_user_role() === 'admin';
}

function shop_is_logged_in() {
    global $logged_in;
    return $logged_in;
}
?>
