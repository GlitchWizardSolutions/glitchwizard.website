<?php
/*
 * SYSTEM: Core Configuration
 * LOCATION: private/
 * LOG:
 * 2025-07-04 - Original Development
 * PRODUCTION:
 * File: gws-universal-config.php
 * Description: Central configuration file for paths, database connection, and global function loading.
 * Functions:
 *   - Defines constants for DB credentials and path setup
 *   - Creates PDO connection for application-wide use
 * Expected Outputs:
 *   - Initialized constants and $pdo object
 * Related Files:
 *   - All files requiring database access.
 */

// Session Security Configuration
$session_config = [
    'cookie_secure' => true,           // Cookies only sent over HTTPS
    'cookie_httponly' => true,         // Prevent JavaScript access to session cookie
    'use_only_cookies' => true,        // Forces sessions to only use cookies
    'cookie_samesite' => 'Lax',        // Cookie sent for same-site requests and top-level navigation from outside
    'gc_maxlifetime' => 3600,          // Session timeout in seconds (1 hour)
    'cookie_lifetime' => 0,            // Session cookie expires when browser closes
    'gc_probability' => 1,
    'gc_divisor' => 100,              // 1% chance of garbage collection
];

// Apply session configuration only if session is not already active
if (session_status() === PHP_SESSION_NONE) {
    foreach ($session_config as $key => $value) {
        ini_set('session.' . $key, $value);
    }
}

// Start session with security settings
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set session absolute timeout
if (isset($_SESSION['CREATED'])) {
    if (time() - $_SESSION['CREATED'] > 3600) {
        // Session is older than 1 hour, regenerate and reset
        session_regenerate_id(true);
        $_SESSION = array();
        $_SESSION['CREATED'] = time();
    }
} else {
    $_SESSION['CREATED'] = time();
}
// ========================================
// TIMEZONE CONFIGURATION
// ========================================
// This ensures consistent time display across admin, client portal, and public areas
date_default_timezone_set('America/New_York');

/**
 * PROJECT_ROOT is the absolute path to the project root (gws-universal-hybrid-app).
 * Use PROJECT_ROOT for all includes and path resolutions in this project.
 */

if (!defined('PROJECT_ROOT')) define('PROJECT_ROOT', dirname(__DIR__));

// Environment Configuration
// Change this to 'prod' when deploying to production
if (!defined('ENVIRONMENT')) define('ENVIRONMENT', 'dev');

// Database Configuration based on environment
if (ENVIRONMENT === 'dev')
{
    // Development (Local XAMPP) Settings
    if (!defined('db_host'))
        define('db_host', 'localhost');
    if (!defined('db_user'))
        define('db_user', 'root');
    if (!defined('db_pass'))
        define('db_pass', '');
    if (!defined('db_name'))
        define('db_name', 'gws_universal_db');
    if (!defined('db_charset'))
        define('db_charset', 'utf8');
    if (!defined('secret_key'))
        define('secret_key', ' ');

    // Dynamically determine the web root URL for assets (dev or prod)
    if (!defined('WEB_ROOT_URL'))
    {
        $script_name = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
        $script_filename = isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '';
        $doc_root = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));
        $project_root = str_replace('\\', '/', realpath(PROJECT_ROOT . '/public_html'));
        $web_root_url = '';

        if ($doc_root && $project_root && strpos($project_root, $doc_root) === 0)
        {
            $web_root_url = substr($project_root, strlen($doc_root));
            if ($web_root_url === false)
                $web_root_url = '';
            if ($web_root_url === '')
                $web_root_url = '/';
        }
        define('WEB_ROOT_URL', '/public_html');
    }
} else
{
    // Production (cPanel) Settings
    if (!defined('db_host'))
        define('db_host', 'localhost');
    if (!defined('db_user'))
        define('db_user', 'root'); // Change this
    if (!defined('db_pass'))
        define('db_pass', ''); // Change this
    if (!defined('db_name'))
        define('db_name', ''); // Change this
    if (!defined('db_charset'))
        define('db_charset', 'utf8');
    if (!defined('secret_key'))
        define('secret_key', 'dev_secret_key'); // Change this

    // Dynamically determine the web root URL for assets (dev or prod)
    if (!defined('WEB_ROOT_URL'))
    {
        $script_name = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
        $script_filename = isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '';
        $doc_root = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));
        $project_root = str_replace('\\', '/', realpath(PROJECT_ROOT . '/public_html'));
        $web_root_url = '';

        if ($doc_root && $project_root && strpos($project_root, $doc_root) === 0)
        {
            $web_root_url = substr($project_root, strlen($doc_root));
            if ($web_root_url === false)
                $web_root_url = '';
            if ($web_root_url === '')
                $web_root_url = '/';
        }
        define('WEB_ROOT_URL', '/public_html');
    }
}

// Web URL paths for assets and images (environment-aware)
if (!defined('BLOG_ASSETS_URL')) define('BLOG_ASSETS_URL', '/public_html/blog_system/assets');
if (!defined('PUBLIC_ASSETS_URL')) define('PUBLIC_ASSETS_URL', '/public_html/assets');
if (!defined('BACKGROUND_IMAGES_URL')) define('BACKGROUND_IMAGES_URL', '/public_html/blog_system/assets/settings/background_image');
if (!defined('ACCOUNTS_AVATARS_URL')) define('ACCOUNTS_AVATARS_URL', '/public_html/accounts_system/assets/uploads/avatars');
if (!defined('BLOG_AVATARS_URL')) define('BLOG_AVATARS_URL', '/public_html/blog_system/assets/uploads/img/avatars');
if (!defined('BLOG_POST_IMAGES_URL')) define('BLOG_POST_IMAGES_URL', '/public_html/blog_system/assets/uploads/img/blog_post_images');
if (!defined('BLOG_GALLERY_URL')) define('BLOG_GALLERY_URL', '/public_html/blog_system/assets/uploads/img/gallery');

// Define file system paths first (needed for branding paths)
if (!defined('private_path')) define('private_path', 'C:\xampp\htdocs\gws-universal-hybrid-app\private');
if (!defined('public_path')) define('public_path', 'C:\xampp\htdocs\gws-universal-hybrid-app\public_html');
if (!defined('admin_path')) define('admin_path', 'C:\xampp\htdocs\gws-universal-hybrid-app\public_html\admin');
if (!defined('documents_system_path')) define('documents_system_path', 'C:\xampp\htdocs\gws-universal-hybrid-app\public_html\documents_system');
if (!defined('vendor_path')) define('vendor_path', 'C:\xampp\htdocs\gws-universal-hybrid-app\public_html\documents_system\vendor');
if (!defined('public_assets_path')) define('public_assets_path', 'C:\xampp\htdocs\gws-universal-hybrid-app\public_html\assets');
if (!defined('blog_path')) define('blog_path', 'C:\xampp\htdocs\gws-universal-hybrid-app\public_html\blog');
if (!defined('client_portal_path')) define('client_portal_path', 'C:\xampp\htdocs\gws-universal-hybrid-app\public_html\client_portal');
if (!defined('accounts_system_path')) define('accounts_system_path', 'C:\xampp\htdocs\gws-universal-hybrid-app\public_html\accounts_system');

// Branding Configuration - Business-specific customization
if (!defined('BRANDING_ASSETS_URL')) define('BRANDING_ASSETS_URL', WEB_ROOT_URL . '/assets/branding');
if (!defined('BRANDING_ASSETS_PATH')) define('BRANDING_ASSETS_PATH', rtrim(public_path, '/\\') . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'branding');

// Business Branding Settings - Update these for each business installation
if (!defined('BUSINESS_NAME')) define('BUSINESS_NAME', 'Your Business Name'); // Replace with actual business name
if (!defined('BUSINESS_SHORT_NAME')) define('BUSINESS_SHORT_NAME', 'YBN'); // Short version/acronym
if (!defined('ADMIN_LOGO')) define('ADMIN_LOGO', 'admin_logo.svg'); // Logo for admin interface (recommended: 40x40px)
if (!defined('MAIN_LOGO')) define('MAIN_LOGO', 'main_logo.png'); // Main logo for public pages (flexible size)
if (!defined('FAVICON')) define('FAVICON', 'favicon.ico'); // Website favicon (16x16px)
if (!defined('SECONDARY_LOGO')) define('SECONDARY_LOGO', 'secondary_logo.png'); // Smaller/alternate logo

// Brand Colors - FALLBACK DEFAULTS ONLY
// For comprehensive branding management, use /admin/settings/branding_settings.php
// These constants provide fallback values when branding_settings.php is not available
if (!defined('BRAND_PRIMARY_COLOR')) define('BRAND_PRIMARY_COLOR', '#667eea'); // Main brand color (fallback)
if (!defined('BRAND_SECONDARY_COLOR')) define('BRAND_SECONDARY_COLOR', '#764ba2'); // Secondary brand color (fallback)
if (!defined('BRAND_ACCENT_COLOR')) define('BRAND_ACCENT_COLOR', '#28a745'); // Accent color for highlights (fallback)
if (!defined('BRAND_GRADIENT')) define('BRAND_GRADIENT', 'linear-gradient(135deg, ' . BRAND_PRIMARY_COLOR . ' 0%, ' . BRAND_SECONDARY_COLOR . ' 100%)');

// Brand Typography - FALLBACK DEFAULTS ONLY
// For comprehensive font management, use /admin/settings/branding_settings.php
if (!defined('BRAND_FONT_FAMILY')) define('BRAND_FONT_FAMILY', 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif'); // Primary font stack (fallback)
if (!defined('BRAND_HEADING_FONT')) define('BRAND_HEADING_FONT', BRAND_FONT_FAMILY); // Font for headings (fallback)

// Load Composer autoloader (mPDF and dependencies) - only if documents system exists
// If you standardize vendor at project root, use PROJECT_ROOT . '/vendor/autoload.php'
if (defined('documents_system_path') && file_exists(documents_system_path . '/vendor/autoload.php')) {
    require_once documents_system_path . '/vendor/autoload.php';
}

// Load universal helper functions (always use PROJECT_ROOT for path resolution)
require_once PROJECT_ROOT . '/private/gws-universal-functions.php';

// Branding Helper Functions
/**
 * Get the URL for a branding asset (logo, favicon, etc.)
 * Falls back to default if custom asset doesn't exist
 */
function getBrandingAsset($assetName, $fallbackPath = null)
{
    $customPath = BRANDING_ASSETS_PATH . DIRECTORY_SEPARATOR . $assetName;
    $customUrl = BRANDING_ASSETS_URL . '/' . $assetName;

    if (file_exists($customPath))
    {
        return $customUrl;
    }

    // Check for default version
    $defaultPath = BRANDING_ASSETS_PATH . DIRECTORY_SEPARATOR . 'default_' . pathinfo($assetName, PATHINFO_FILENAME) . '.svg';
    $defaultUrl = BRANDING_ASSETS_URL . '/default_' . pathinfo($assetName, PATHINFO_FILENAME) . '.svg';

    if (file_exists($defaultPath))
    {
        return $defaultUrl;
    }

    // Return fallback or generate data URL
    if ($fallbackPath)
    {
        return $fallbackPath;
    }

    // Generate a simple SVG as fallback
    $letter = strtoupper(substr(BUSINESS_SHORT_NAME, 0, 1));
    return "data:image/svg+xml;base64," . base64_encode(
        '<svg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="bg" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:' . BRAND_PRIMARY_COLOR . ';stop-opacity:1" />
                    <stop offset="100%" style="stop-color:' . BRAND_SECONDARY_COLOR . ';stop-opacity:1" />
                </linearGradient>
            </defs>
            <rect width="40" height="40" rx="8" fill="url(#bg)"/>
            <text x="20" y="26" font-family="system-ui, sans-serif" font-size="18" font-weight="bold" text-anchor="middle" fill="white">' . $letter . '</text>
        </svg>'
    );
}

/**
 * Generate CSS custom properties for brand colors and dashboard styling
 */
function getBrandingCSS()
{
    return '
    :root {
        /* Brand Colors */
        --brand-primary: ' . BRAND_PRIMARY_COLOR . ';
        --brand-secondary: ' . BRAND_SECONDARY_COLOR . ';
        --brand-accent: ' . BRAND_ACCENT_COLOR . ';
        --brand-gradient: ' . BRAND_GRADIENT . ';
        
        /* Brand Typography */
        --brand-font: ' . BRAND_FONT_FAMILY . ';
        --brand-heading-font: ' . BRAND_HEADING_FONT . ';
        
        /* Dashboard Theme Colors */
        --dashboard-card-bg: #ffffff;
        --dashboard-card-border: 1px solid #e9ecef;
        --dashboard-card-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        --dashboard-card-hover-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        --dashboard-border-radius: 12px;
        --dashboard-card-gap: 30px;
        --dashboard-card-min-width: 400px;
        
        /* Dashboard Header Colors */
        --dashboard-accounts-bg: ' . BRAND_GRADIENT . ';
        /* Dashboard Typography */
        --dashboard-heading-size: 18px;
        --dashboard-heading-color: #4a5361;
        --dashboard-heading-weight: 600;
        --dashboard-card-title-size: 18px;
        --dashboard-card-title-weight: 600;
        --dashboard-action-title-size: 16px;
        --dashboard-action-title-weight: 600;
        --dashboard-action-title-color: #2c2c2c;
        --dashboard-action-desc-size: 14px;
        --dashboard-action-desc-color: #495057;
        
        /* Dashboard Action States */
        --dashboard-warning-bg: rgba(255, 193, 7, 0.9);
        --dashboard-warning-color: #000000;
        --dashboard-warning-count-bg: #ffc107;
        --dashboard-warning-count-color: #2c2c2c;
        --dashboard-danger-bg: rgba(220, 53, 69, 0.2);
        --dashboard-danger-color: #a41e22;
        --dashboard-danger-count-bg: #dc3545;
        --dashboard-danger-count-color: #fff;
        --dashboard-info-bg: rgba(23, 162, 184, 0.2);
        --dashboard-info-color: #0f6674;
        --dashboard-info-count-bg: #17a2b8;
        --dashboard-info-count-color: #fff;
        --dashboard-success-color: #28a745;
        
        /* Dashboard Interactive Elements */
        --dashboard-transition: all 0.2s ease;
        --dashboard-card-hover-transform: translateY(-2px);
        --dashboard-action-hover-transform: translateX(3px);
        --dashboard-action-hover-border: #dee2e6;
        --dashboard-action-hover-bg: #f8f9fa;
        --dashboard-focus-outline: 3px solid #005fcc;
        
        /* Dashboard Sizing */
        --dashboard-header-padding: 20px 25px;
        --dashboard-body-padding: 25px;
        --dashboard-body-min-height: 150px;
        --dashboard-action-padding: 15px;
        --dashboard-action-gap: 15px;
        --dashboard-action-radius: 8px;
        --dashboard-icon-size: 40px;
        --dashboard-icon-font-size: 18px;
        --dashboard-footer-padding: 15px 25px;
        --dashboard-footer-bg: #f8f9fa;
        --dashboard-footer-border: 1px solid #e9ecef;
        
        /* Dashboard Badges and Counts */
        --dashboard-badge-bg: rgba(0, 0, 0, 0.15);
        --dashboard-badge-color: rgba(255, 255, 255, 0.95);
        --dashboard-badge-padding: 5px 12px;
        --dashboard-badge-radius: 15px;
        --dashboard-badge-font-size: 16px;
        --dashboard-badge-weight: 600;
        --dashboard-count-bg: ' . BRAND_PRIMARY_COLOR . ';
        --dashboard-count-color: white;
        --dashboard-count-padding: 8px 15px;
        --dashboard-count-radius: 20px;
        --dashboard-count-font-size: 16px;
        --dashboard-count-weight: 600;
        
        /* Dashboard Empty States */
        --dashboard-empty-padding: 40px 20px;
        --dashboard-empty-color: #6c757d;
        --dashboard-empty-icon-size: 48px;
        --dashboard-empty-text-size: 16px;
        --dashboard-empty-text-color: #495057;
        
        /* Dashboard Mobile */
        --dashboard-mobile-gap: 20px;
        --dashboard-mobile-title-size: 18px;
        --dashboard-mobile-desc-size: 16px;
        
        /* Dashboard Buttons */
        --dashboard-button-font-size: 16px;
        --dashboard-footer-btn-size: 14px;
        
        /* Accessible Card Header Colors */
        ' . getAccessibleCardCSS() . '
    }
    ';
}

/**
 * Get the current logged-in user's name for display
 * Returns username or fallback text
 */
function getAdminDisplayName()
{
    if (isset($_SESSION['name']) && !empty($_SESSION['name']))
    {
        return $_SESSION['name'];
    }
    return 'Admin';
}

/**
 * ACCESSIBILITY FUNCTIONS
 * Functions to ensure WCAG 2.1 AA compliance for color contrast
 */

/**
 * Calculate relative luminance of a color
 * Used for WCAG contrast ratio calculations
 * 
 * @param string $color Hex color code (with or without #)
 * @return float Relative luminance value (0-1)
 */
function getRelativeLuminance($color)
{
    // Remove # if present and convert to lowercase
    $color = ltrim($color, '#');

    // Convert to RGB values
    $r = hexdec(substr($color, 0, 2)) / 255;
    $g = hexdec(substr($color, 2, 2)) / 255;
    $b = hexdec(substr($color, 4, 2)) / 255;

    // Apply gamma correction
    $r = $r <= 0.03928 ? $r / 12.92 : pow(($r + 0.055) / 1.055, 2.4);
    $g = $g <= 0.03928 ? $g / 12.92 : pow(($g + 0.055) / 1.055, 2.4);
    $b = $b <= 0.03928 ? $b / 12.92 : pow(($b + 0.055) / 1.055, 2.4);

    // Calculate relative luminance
    return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
}

/**
 * Calculate contrast ratio between two colors
 * 
 * @param string $color1 First color (hex)
 * @param string $color2 Second color (hex)
 * @return float Contrast ratio (1-21)
 */
function getContrastRatio($color1, $color2)
{
    $lum1 = getRelativeLuminance($color1);
    $lum2 = getRelativeLuminance($color2);

    // Ensure lighter color is numerator
    $lighter = max($lum1, $lum2);
    $darker = min($lum1, $lum2);

    return ($lighter + 0.05) / ($darker + 0.05);
}

/**
 * Get accessible text color for a given background color
 * Ensures WCAG 2.1 AA compliance (4.5:1 contrast ratio)
 * 
 * @param string $backgroundColor Background color (hex)
 * @param string $preferredTextColor Optional preferred text color (hex)
 * @param float $minContrastRatio Minimum contrast ratio (default: 4.5 for AA)
 * @return string Accessible text color (hex with #)
 */
function getAccessibleTextColor($backgroundColor, $preferredTextColor = null, $minContrastRatio = 4.5)
{
    $backgroundColor = ltrim($backgroundColor, '#');

    // If preferred color is provided, check if it meets contrast requirements
    if ($preferredTextColor)
    {
        $preferredTextColor = ltrim($preferredTextColor, '#');
        $contrast = getContrastRatio($backgroundColor, $preferredTextColor);

        if ($contrast >= $minContrastRatio)
        {
            return '#' . $preferredTextColor;
        }
    }

    // Check contrast with white and black
    $whiteContrast = getContrastRatio($backgroundColor, 'ffffff');
    $blackContrast = getContrastRatio($backgroundColor, '000000');

    // If both white and black meet requirements, choose the one with higher contrast
    if ($whiteContrast >= $minContrastRatio && $blackContrast >= $minContrastRatio)
    {
        return $whiteContrast > $blackContrast ? '#ffffff' : '#000000';
    }

    // If only one meets requirements, use it
    if ($whiteContrast >= $minContrastRatio)
    {
        return '#ffffff';
    }
    if ($blackContrast >= $minContrastRatio)
    {
        return '#000000';
    }

    // If neither pure white nor black work, generate an accessible color
    return generateAccessibleTextColor($backgroundColor, $minContrastRatio);
}

/**
 * Generate an accessible text color when neither white nor black meets requirements
 * 
 * @param string $backgroundColor Background color (hex, no #)
 * @param float $minContrastRatio Minimum contrast ratio
 * @return string Generated accessible color (hex with #)
 */
function generateAccessibleTextColor($backgroundColor, $minContrastRatio)
{
    $bgLuminance = getRelativeLuminance($backgroundColor);

    // Calculate target luminance for minimum contrast
    $targetLuminanceLight = ($bgLuminance + 0.05) * $minContrastRatio - 0.05;
    $targetLuminanceDark = ($bgLuminance + 0.05) / $minContrastRatio - 0.05;

    // Clamp values between 0 and 1
    $targetLuminanceLight = min(1, max(0, $targetLuminanceLight));
    $targetLuminanceDark = min(1, max(0, $targetLuminanceDark));

    // Choose the target that's achievable (within 0-1 range)
    $targetLuminance = $targetLuminanceLight <= 1 ? $targetLuminanceLight : $targetLuminanceDark;

    // Convert luminance back to RGB (simplified - grayscale)
    $grayValue = $targetLuminance <= 0.03928
        ? $targetLuminance * 12.92
        : 1.055 * pow($targetLuminance, 1 / 2.4) - 0.055;

    $grayValue = max(0, min(1, $grayValue));
    $hexValue = sprintf('%02x', round($grayValue * 255));

    return '#' . $hexValue . $hexValue . $hexValue;
}

/**
 * Get accessible colors for dashboard card headers
 * Returns both text and background colors that meet WCAG guidelines
 * 
 * @param string $cardType Card type (accounts, blog, documents, client)
 * @return array Array with 'background' and 'color' keys
 */
function getAccessibleCardColors($cardType)
{
    // Default card background colors - using defined constants
    $cardBackgrounds = [
        'accounts' => BRAND_PRIMARY_COLOR,
        'blog' => BRAND_ACCENT_COLOR,
        'documents' => BRAND_SECONDARY_COLOR,
        'client' => BRAND_PRIMARY_COLOR
    ];

    $backgroundColor = $cardBackgrounds[$cardType] ?? BRAND_PRIMARY_COLOR;
    $textColor = getAccessibleTextColor($backgroundColor);

    // Calculate accessible badge colors - use dark badges for all cards for consistency
    // Dark badges with white text provide excellent contrast on all header backgrounds
    $badgeBackground = 'rgba(0, 0, 0, 0.8)';
    $badgeColor = '#ffffff';

    return [
        'background' => $backgroundColor,
        'color' => $textColor,
        'badgeBackground' => $badgeBackground,
        'badgeColor' => $badgeColor
    ];
}

/**
 * Output CSS custom properties for accessible card colors
 * Call this in the dashboard CSS output
 * 
 * @return string CSS custom properties
 */
function getAccessibleCardCSS()
{
    $cardTypes = ['accounts', 'blog', 'documents', 'client'];
    $css = "/* Accessible Card Header Colors */\n";

    foreach ($cardTypes as $type)
    {
        $colors = getAccessibleCardColors($type);
        $css .= "--{$type}-header-bg: {$colors['background']};\n";
        $css .= "--{$type}-header-color: {$colors['color']};\n";
        $css .= "--{$type}-badge-bg: {$colors['badgeBackground']};\n";
        $css .= "--{$type}-badge-color: {$colors['badgeColor']};\n";
    }

    return $css;
}

// Database connection with PDO/MySQLi fallback
$pdo = null;
$mysqli = null;

// Try PDO first (preferred)
if (class_exists('PDO')) {
    try {
        $pdo = new PDO(
            "mysql:host=" . db_host . ";dbname=" . db_name . ";charset=" . db_charset,
            db_user,
            db_pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    } catch (PDOException $e) {
        if (ENVIRONMENT === 'prod') {
            error_log('PDO Database connection failed: ' . $e->getMessage());
        } else {
            error_log('PDO Database connection failed: ' . $e->getMessage());
        }
        $pdo = null;
    }
}

// Fallback to MySQLi if PDO failed or isn't available
if (!$pdo && class_exists('mysqli')) {
    try {
        $mysqli = new mysqli(db_host, db_user, db_pass, db_name);
        if ($mysqli->connect_error) {
            throw new Exception('MySQLi connection failed: ' . $mysqli->connect_error);
        }
        $mysqli->set_charset(db_charset);
    } catch (Exception $e) {
        if (ENVIRONMENT === 'prod') {
            error_log('MySQLi Database connection failed: ' . $e->getMessage());
            die('A system error occurred. Please try again later.');
        } else {
            die('MySQLi Database connection failed: ' . $e->getMessage());
        }
    }
}

// If neither worked, show error
if (!$pdo && !$mysqli) {
    if (ENVIRONMENT === 'prod') {
        error_log('No database extensions available (PDO or MySQLi)');
        die('A system error occurred. Please try again later.');
    } else {
        die('Database connection failed: Neither PDO nor MySQLi extensions are available.');
    }
}

// Database query helper function for compatibility
function db_query($sql, $params = []) {
    global $pdo, $mysqli;
    
    if ($pdo) {
        // Use PDO
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } elseif ($mysqli) {
        // Use MySQLi
        if (empty($params)) {
            $result = $mysqli->query($sql);
            if (!$result) {
                throw new Exception('MySQLi query failed: ' . $mysqli->error);
            }
            return $result;
        } else {
            // For prepared statements with MySQLi
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                throw new Exception('MySQLi prepare failed: ' . $mysqli->error);
            }
            
            if (!empty($params)) {
                $types = str_repeat('s', count($params)); // Assume all strings for simplicity
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            return $stmt;
        }
    }
    
    throw new Exception('No database connection available');
}

// Database fetch helper function
function db_fetch_all($result) {
    global $pdo, $mysqli;
    
    if ($pdo && $result instanceof PDOStatement) {
        return $result->fetchAll();
    } elseif ($mysqli && $result instanceof mysqli_result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } elseif ($mysqli && $result instanceof mysqli_stmt) {
        $result_set = $result->get_result();
        return $result_set ? $result_set->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    return [];
}