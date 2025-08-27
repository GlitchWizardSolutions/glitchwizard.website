<?php
/*
FILE NAME  : shop_settings.php
LOCATION   : assets/includes/settings/shop_settings.php
DESCRIPTION: Shop settings reader and configuration provider
FUNCTION   : Provides shop configuration values throughout the application
CREATED    : August 2025 - Shop Integration Step 3
*/

// Shop Settings Reader - Load all shop configuration values
if (!function_exists('getShopConfig')) {
    function getShopConfig($key = null, $default = null) {
        global $pdo;
        static $shop_config = null;
        
        // Load all settings once and cache them
        if ($shop_config === null) {
            $shop_config = [];
            
            try {
                $stmt = $pdo->query('SELECT setting_key, setting_value FROM shop_settings');
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $shop_config[$row['setting_key']] = $row['setting_value'];
                }
            } catch (Exception $e) {
                // If table doesn't exist, use defaults
                $shop_config = getShopDefaults();
            }
            
            // Merge with defaults for any missing values
            $shop_config = array_merge(getShopDefaults(), $shop_config);
        }
        
        // Return specific key or all config
        if ($key === null) {
            return $shop_config;
        }
        
        return isset($shop_config[$key]) ? $shop_config[$key] : $default;
    }
}

if (!function_exists('getShopDefaults')) {
    function getShopDefaults() {
        return [
            // General Settings
            'shop_site_name' => 'Shopping Cart',
            'shop_currency_code' => '&dollar;',
            'shop_weight_unit' => 'lbs',
            'shop_account_required' => '0',
            'shop_rewrite_url' => '0',
            'shop_featured_image' => 'uploads/featured-image.jpg',
            'shop_template_editor' => 'tinymce',
            'shop_default_payment_status' => 'Completed',
            'shop_secret_key' => 'YOUR_SECRET_KEY',
            
            // PayPal Settings
            'paypal_enabled' => '1',
            'paypal_email' => 'payments@example.com',
            'paypal_testmode' => '1',
            'paypal_currency' => 'USD',
            'paypal_ipn_url' => '',
            'paypal_cancel_url' => '',
            'paypal_return_url' => '',
            
            // Stripe Settings
            'stripe_enabled' => '1',
            'stripe_publish_key' => '',
            'stripe_secret_key' => '',
            'stripe_webhook_secret' => '',
            
            // Coinbase Settings
            'coinbase_enabled' => '0',
            'coinbase_api_key' => '',
            'coinbase_currency' => 'USD',
            'coinbase_return_url' => '',
            'coinbase_cancel_url' => ''
        ];
    }
}

// Define shop constants based on configuration
$shop_settings = getShopConfig();

// General shop constants
define('site_name', $shop_settings['shop_site_name']);
define('currency_code', $shop_settings['shop_currency_code']);
define('weight_unit', $shop_settings['shop_weight_unit']);
define('account_required', $shop_settings['shop_account_required'] === '1');
define('rewrite_url', $shop_settings['shop_rewrite_url'] === '1');
define('featured_image', $shop_settings['shop_featured_image']);
define('template_editor', $shop_settings['shop_template_editor']);
define('default_payment_status', $shop_settings['shop_default_payment_status']);
if (!defined('secret_key')) define('secret_key', $shop_settings['shop_secret_key']);

// PayPal constants
define('paypal_enabled', $shop_settings['paypal_enabled'] === '1');
define('paypal_email', $shop_settings['paypal_email']);
define('paypal_testmode', $shop_settings['paypal_testmode'] === '1');
define('paypal_currency', $shop_settings['paypal_currency']);
define('paypal_ipn_url', $shop_settings['paypal_ipn_url']);
define('paypal_cancel_url', $shop_settings['paypal_cancel_url']);
define('paypal_return_url', $shop_settings['paypal_return_url']);

// Stripe constants
define('stripe_enabled', $shop_settings['stripe_enabled'] === '1');
define('stripe_publish_key', $shop_settings['stripe_publish_key']);
define('stripe_secret_key', $shop_settings['stripe_secret_key']);
define('stripe_webhook_secret', $shop_settings['stripe_webhook_secret']);

// Coinbase constants
define('coinbase_enabled', $shop_settings['coinbase_enabled'] === '1');
define('coinbase_key', $shop_settings['coinbase_api_key']);
define('coinbase_currency', $shop_settings['coinbase_currency']);
define('coinbase_return_url', $shop_settings['coinbase_return_url']);
define('coinbase_cancel_url', $shop_settings['coinbase_cancel_url']);

// Determine the base URL
if (!defined('base_url')) {
    $base_url = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $base_url .= $_SERVER['HTTP_HOST'];
    $base_url .= dirname($_SERVER['REQUEST_URI'] !== '/' ? $_SERVER['REQUEST_URI'] : '');
    $base_url = rtrim($base_url, '/') . '/';
    define('base_url', $base_url);
}

// Helper functions for shop operations
if (!function_exists('url')) {
    function url($path = '') {
        return base_url . ltrim($path, '/');
    }
}

if (!function_exists('num_format')) {
    function num_format($number, $decimals = 2) {
        return number_format((float)$number, $decimals);
    }
}

if (!function_exists('shop_setting')) {
    function shop_setting($key, $default = null) {
        return getShopConfig($key, $default);
    }
}

// Shop configuration status check
if (!function_exists('isShopConfigured')) {
    function isShopConfigured() {
        $required_settings = [
            'shop_site_name',
            'paypal_email'
        ];
        
        foreach ($required_settings as $setting) {
            $value = getShopConfig($setting);
            if (empty($value) || $value === 'payments@example.com') {
                return false;
            }
        }
        
        return true;
    }
}

// Get shop configuration status for dashboard
if (!function_exists('getShopConfigStatus')) {
    function getShopConfigStatus() {
        $status = [
            'general' => false,
            'paypal' => false,
            'stripe' => false,
            'coinbase' => false,
            'overall_percent' => 0
        ];
        
        // Check general settings
        $site_name = getShopConfig('shop_site_name');
        if (!empty($site_name) && $site_name !== 'Shopping Cart') {
            $status['general'] = true;
        }
        
        // Check PayPal settings
        $paypal_email = getShopConfig('paypal_email');
        if (!empty($paypal_email) && $paypal_email !== 'payments@example.com') {
            $status['paypal'] = true;
        }
        
        // Check Stripe settings
        $stripe_key = getShopConfig('stripe_publish_key');
        if (!empty($stripe_key)) {
            $status['stripe'] = true;
        }
        
        // Check Coinbase settings
        $coinbase_key = getShopConfig('coinbase_api_key');
        if (!empty($coinbase_key)) {
            $status['coinbase'] = true;
        }
        
        // Calculate overall percentage
        $configured_count = 0;
        if ($status['general']) $configured_count++;
        if ($status['paypal']) $configured_count++;
        if ($status['stripe']) $configured_count++;
        if ($status['coinbase']) $configured_count++;
        
        $status['overall_percent'] = round(($configured_count / 4) * 100);
        
        return $status;
    }
}

?>
