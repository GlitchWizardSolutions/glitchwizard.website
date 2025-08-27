<?php
/**
 * Master Configuration Loader
 * Location: public_html/assets/includes/private/gws-master-config.php
 * 
 * This file loads the sensitive config from outside web root
 * and all other application functions from within the web structure
 */

// Determine the path to the private config file (outside web root)
$private_config_paths = [
    // Standard cPanel structure (private folder above public_html)
    dirname(__DIR__, 4) . '/private/gws-universal-config.php',
    // Alternative structures
    dirname(__DIR__, 5) . '/private/gws-universal-config.php',
    dirname(__DIR__, 3) . '/private/gws-universal-config.php',
    // Local development
    dirname(__DIR__, 6) . '/private/gws-universal-config.php'
];

$config_loaded = false;
foreach ($private_config_paths as $config_path) {
    if (file_exists($config_path)) {
        require_once $config_path;
        $config_loaded = true;
        break;
    }
}

if (!$config_loaded) {
    die('Critical Error: Could not locate gws-universal-config.php. Please ensure the private folder is properly configured above the web root.');
}

// Load all application functions from the new public location
$functions_dir = __DIR__ . '/functions/';

// Core functions
require_once $functions_dir . 'gws-universal-functions.php';
require_once $functions_dir . 'access-control.php';
require_once $functions_dir . 'role-definitions.php';
require_once $functions_dir . 'role-functions.php';
require_once $functions_dir . 'feature_flags.php';
require_once $functions_dir . 'remember-me-functions.php';
require_once $functions_dir . 'settings_loader.php';
require_once $functions_dir . 'settings_completion_matrix.php';

// Load classes
if (file_exists(__DIR__ . '/classes/SecurityHelper.php')) {
    require_once __DIR__ . '/classes/SecurityHelper.php';
}
if (file_exists(__DIR__ . '/classes/SettingsManager.php')) {
    require_once __DIR__ . '/classes/SettingsManager.php';
}

// Define new paths for moved files
if (!defined('PRIVATE_ASSETS_PATH')) {
    define('PRIVATE_ASSETS_PATH', __DIR__);
}
if (!defined('PRIVATE_CSS_PATH')) {
    define('PRIVATE_CSS_PATH', __DIR__ . '/css/');
}
if (!defined('PRIVATE_DOCS_PATH')) {
    define('PRIVATE_DOCS_PATH', __DIR__ . '/docs/');
}

?>
