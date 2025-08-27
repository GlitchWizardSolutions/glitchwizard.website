<?php
/*
 * SYSTEM: Core Configuration Template
 * LOCATION: private/
 * 
 * IMPORTANT: This is a template file!
 * Copy this file to 'gws-universal-config.php' and update the database credentials
 * 
 * File: gws-universal-config-template.php
 * Description: Template configuration file for paths, database connection, and global function loading.
 * 
 * SETUP INSTRUCTIONS:
 * 1. Copy this file to 'gws-universal-config.php'
 * 2. Update the database credentials below
 * 3. Never commit the actual config file to Git
 */

// Session Security Configuration
$session_config = [
    'name' => 'gws_session',
    'lifetime' => 3600,
    'path' => '/',
    'domain' => '',
    'secure' => false, // Set to true in production with HTTPS
    'httponly' => true,
    'samesite' => 'Strict'
];

// Apply session configuration
session_set_cookie_params([
    'lifetime' => $session_config['lifetime'],
    'path' => $session_config['path'],
    'domain' => $session_config['domain'],
    'secure' => $session_config['secure'],
    'httponly' => $session_config['httponly'],
    'samesite' => $session_config['samesite']
]);

// Start session with custom name
session_name($session_config['name']);
session_start();

// Regenerate session ID periodically for security
if (!isset($_SESSION['last_regeneration'])) {
    $_SESSION['last_regeneration'] = time();
} elseif (time() - $_SESSION['last_regeneration'] > 300) { // Every 5 minutes
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

// ENVIRONMENT CONFIGURATION
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'development'); // Change to 'production' for live site
}

if (ENVIRONMENT === 'development') {
    // Development database settings
    if (!defined('db_host'))
        define('db_host', 'localhost');
    if (!defined('db_user'))
        define('db_user', 'your_username_here'); // UPDATE THIS
    if (!defined('db_pass'))
        define('db_pass', 'your_password_here'); // UPDATE THIS
    if (!defined('db_name'))
        define('db_name', 'your_database_here'); // UPDATE THIS
    if (!defined('db_charset'))
        define('db_charset', 'utf8');
        
    // Development error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/errors_and_observations/error.log');
} else {
    // PRODUCTION ENVIRONMENT DATABASE CONFIGURATION
    if (!defined('db_host'))
        define('db_host', 'your_production_host_here'); // UPDATE THIS
    if (!defined('db_user'))
        define('db_user', 'your_production_user_here'); // UPDATE THIS
    if (!defined('db_pass'))
        define('db_pass', 'your_production_pass_here'); // UPDATE THIS
    if (!defined('db_name'))
        define('db_name', 'your_production_db_here'); // UPDATE THIS
    if (!defined('db_charset'))
        define('db_charset', 'utf8');
        
    // Production error handling
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/errors_and_observations/error.log');
}

// Additional configuration constants
define('SITE_URL', 'http://localhost/gws-universal-hybrid-app/public_html'); // UPDATE FOR PRODUCTION
define('ADMIN_EMAIL', 'admin@example.com'); // UPDATE THIS
define('SITE_NAME', 'GWS Universal Hybrid App');

// Database Connection
try {
    $pdo = new PDO(
        "mysql:host=" . db_host . ";dbname=" . db_name . ";charset=" . db_charset,
        db_user,
        db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    if (ENVIRONMENT === 'development') {
        die("Database connection failed: " . $e->getMessage());
    } else {
        die("Database connection failed. Please contact the administrator.");
    }
}

// Load additional functions
require_once __DIR__ . '/gws-universal-functions.php';

// Additional includes would go here...
?>
