<?php
// Universal functions for use across admin, client portal, and public-facing apps

/**
 * UI TEMPLATE FUNCTIONS
 * Core functions for consistent UI rendering across all applications
 */

/**
 * Render a standard page header with title
 * @param string $title Page title
 * @param string $subtitle Optional subtitle
 * @param array $breadcrumbs Optional breadcrumb array [label => url]
 */
if (!function_exists('renderPageHeader')) {
function renderPageHeader($title, $subtitle = '', array $breadcrumbs = []) {
    echo '<div class="container mt-3">';
    if (!empty($breadcrumbs)) {
        echo '<nav aria-label="breadcrumb">';
        echo '<ol class="breadcrumb">';
        foreach ($breadcrumbs as $label => $url) {
            if ($url) {
                echo '<li class="breadcrumb-item"><a href="' . htmlspecialchars($url) . '">' . htmlspecialchars($label) . '</a></li>';
            } else {
                echo '<li class="breadcrumb-item active" aria-current="page">' . htmlspecialchars($label) . '</li>';
            }
        }
        echo '</ol>';
        echo '</nav>';
    }
    echo '<h1 class="mb-3">' . htmlspecialchars($title) . '</h1>';
    if ($subtitle) {
        echo '<p class="lead">' . htmlspecialchars($subtitle) . '</p>';
    }
    echo '</div>';
}
}

/**
 * Render a card container with header
 * @param string $title Card title
 * @param string $icon Optional FontAwesome icon class
 * @param array $headerButtons Optional array of header buttons [label => [url, class]]
 */
if (!function_exists('renderCard')) {
function renderCard($title, $icon = '', array $headerButtons = []) {
    echo '<div class="card">';
    echo '<div class="card-header d-flex justify-content-between align-items-center">';
    echo '<div>';
    if ($icon) {
        echo '<i class="' . htmlspecialchars($icon) . '"></i> ';
    }
    echo htmlspecialchars($title);
    echo '</div>';
    if (!empty($headerButtons)) {
        echo '<div>';
        foreach ($headerButtons as $label => $button) {
            echo '<a href="' . htmlspecialchars($button['url']) . '" class="btn ' . htmlspecialchars($button['class']) . '">' . htmlspecialchars($label) . '</a> ';
        }
        echo '</div>';
    }
    echo '</div>';
    echo '<div class="card-body">';
}
}

/**
 * Close a card container
 */
if (!function_exists('closeCard')) {
function closeCard() {
    echo '</div></div>';
}
}

/**
 * Render a standard page footer
 * @param array $scripts Optional array of additional script URLs to include
 */
if (!function_exists('renderPageFooter')) {
function renderPageFooter(array $scripts = []) {
    foreach ($scripts as $script) {
        echo '<script src="' . htmlspecialchars($script) . '"></script>';
    }
}
}

/**
 * SETTINGS MANAGEMENT FUNCTIONS
 * Helper functions for working with the new settings format
 */

/**
 * Load settings for a specific app
 * @param string $appName The name of the app (e.g., 'accounts', 'blog', etc.)
 * @return array The settings array with values
 */
function loadAppSettings($appName) {
    // Define the settings file paths
    $mainConfigFile = PROJECT_ROOT . "/public_html/{$appName}_system/settings/{$appName}-config.php";
    $adminConfigFile = PROJECT_ROOT . "/public_html/admin/settings/{$appName}_settings.php";
    
    // Try to load settings from main config file first
    if (file_exists($mainConfigFile)) {
        $settings = include($mainConfigFile);
    }
    // Fallback to admin settings
    elseif (file_exists($adminConfigFile)) {
        $settings = include($adminConfigFile);
    }
    // No settings found
    else {
        return [];
    }
    
    // Extract just the values for easier use
    $values = [];
    foreach ($settings as $key => $setting) {
        $values[$key] = $setting['value'];
    }
    
    return $values;
}

/**
 * Save settings for a specific app
 * @param string $appName The name of the app (e.g., 'accounts', 'blog', etc.)
 * @param array $newValues The new values to save
 * @return bool True if successful, false otherwise
 */
function saveAppSettings($appName, $newValues) {
    // Define the settings file paths
    $mainConfigFile = PROJECT_ROOT . "/public_html/{$appName}_system/settings/{$appName}-config.php";
    $adminConfigFile = PROJECT_ROOT . "/public_html/admin/settings/{$appName}_settings.php";
    
    // Try to load existing settings to preserve descriptions
    if (file_exists($mainConfigFile)) {
        $settings = include($mainConfigFile);
    } elseif (file_exists($adminConfigFile)) {
        $settings = include($adminConfigFile);
    } else {
        return false;
    }
    
    // Update the values while preserving descriptions
    foreach ($settings as $key => &$setting) {
        if (isset($newValues[$key])) {
            // Handle checkbox values that come as 'true' or 'false' strings
            if ($newValues[$key] === 'true') {
                $setting['value'] = true;
            } elseif ($newValues[$key] === 'false') {
                $setting['value'] = false;
            } else {
                $setting['value'] = $newValues[$key];
            }
        }
    }
    unset($setting); // Break the reference
    
    // Generate settings file content
    $content = "<?php\n\n";
    $content .= "// Prevent direct access\n";
    $content .= "if (!defined('PROJECT_ROOT')) {\n";
    $content .= "    die('Direct access to this file is not allowed');\n";
    $content .= "}\n\n";
    $content .= "// {$appName} system settings\n";
    $content .= "return " . var_export($settings, true) . ";\n";
    
    // Save to both locations
    $success = true;
    if (file_exists($mainConfigFile)) {
        $success = $success && file_put_contents($mainConfigFile, $content);
    }
    if (file_exists($adminConfigFile)) {
        $success = $success && file_put_contents($adminConfigFile, $content);
    }
    
    return $success;
}

/**
 * Get a specific setting value for an app
 * @param string $appName The name of the app (e.g., 'accounts', 'blog', etc.)
 * @param string $key The setting key
 * @param mixed $default The default value if setting not found
 * @return mixed The setting value
 */
function getAppSetting($appName, $key, $default = null) {
    static $settings = [];
    
    // Load settings if not already loaded
    if (!isset($settings[$appName])) {
        $settings[$appName] = loadAppSettings($appName);
    }
    
    return isset($settings[$appName][$key]) ? $settings[$appName][$key] : $default;
}

/**
 * Set a specific setting value for an app
 * @param string $appName The name of the app (e.g., 'accounts', 'blog', etc.)
 * @param string $key The setting key
 * @param mixed $value The new value
 * @return bool True if successful, false otherwise
 */
function setAppSetting($appName, $key, $value) {
    // Load current settings
    $settings = loadAppSettings($appName);
    
    // Update the value
    $settings[$key] = $value;
    
    // Save all settings
    return saveAppSettings($appName, $settings);
}

/**
 * Check if a feature is enabled for an app
 * @param string $appName The name of the app (e.g., 'accounts', 'blog', etc.)
 * @param string $feature The feature name (should end with _enabled)
 * @return bool True if enabled, false otherwise
 */
function isFeatureEnabled($appName, $feature) {
    return (bool) getAppSetting($appName, $feature . '_enabled', false);
}

/**
 * AUTHENTICATION AND USER FUNCTIONS
 * Core functions for user authentication and session management
 */

/**
 * Check if the request is an AJAX request
 * @return bool True if request is AJAX
 */
function is_ajax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * BRAND SPINNER HELPERS
 * Centralized rendering for the globally selected brand spinner style.
 * Styles are defined in gws-universal-branding.css (appended spinner section).
 */
if (!function_exists('getBrandSpinnerStyle')) {
function getBrandSpinnerStyle(PDO $pdo = null) {
    // Allow dependency injection or fallback to global
    if ($pdo === null && isset($GLOBALS['pdo'])) { $pdo = $GLOBALS['pdo']; }
    $style = 'rainbow_ring';
    if ($pdo) {
        try {
            $row = $pdo->query("SELECT brand_spinner_style FROM setting_branding_colors LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            if (!empty($row['brand_spinner_style'])) {
                $style = $row['brand_spinner_style'];
            }
        } catch (Throwable $e) {
            error_log('Spinner style lookup failed: ' . $e->getMessage());
        }
    }
    return $style;
}
}

if (!function_exists('getBrandSpinnerHTML')) {
/**
 * Return spinner HTML for a given style or the globally configured style.
 * @param string|null $style One of rainbow_ring|border|gradient|logo_ring|pulse_orb|dots (null = use global)
 * @param array $opts Supported keys: 'label' => ARIA label (default 'Loading'), 'class' => extra classes, 'logo' => path override for logo_ring, 'size' => sm|md|lg
 * @return string HTML markup
 */
function getBrandSpinnerHTML($style = null, array $opts = []) {
    $pdo = $GLOBALS['pdo'] ?? null;
    if ($style === null) { $style = getBrandSpinnerStyle($pdo); }
    $label = $opts['label'] ?? 'Loading';
    $extra = trim($opts['class'] ?? '');
    $logo  = $opts['logo'] ?? '/assets/branding/logo.svg';
    $size  = $opts['size'] ?? 'md'; // sm|md|lg

    // Size class map (handled via CSS utility we append)
    $sizeClass = 'brand-spinner-size-md';
    if ($size === 'sm') $sizeClass = 'brand-spinner-size-sm';
    elseif ($size === 'lg') $sizeClass = 'brand-spinner-size-lg';

    $common = 'role="status" aria-label="' . htmlspecialchars($label, ENT_QUOTES) . '"';

    switch ($style) {
        case 'border':
            $html = '<div class="brand-spinner ' . $sizeClass . ' ' . $extra . '" ' . $common . '></div>'; break;
        case 'gradient':
            $html = '<div class="brand-spinner-gradient ' . $sizeClass . ' ' . $extra . '" ' . $common . '></div>'; break;
        case 'logo_ring':
            $html = '<div class="brand-spinner-logo ' . $sizeClass . ' ' . $extra . '" ' . $common . '><img src="' . htmlspecialchars($logo, ENT_QUOTES) . '" alt="" aria-hidden="true"></div>'; break;
        case 'pulse_orb':
            $html = '<div class="brand-spinner-pulse ' . $sizeClass . ' ' . $extra . '" ' . $common . '></div>'; break;
        case 'dots':
            $html = '<div class="brand-spinner-dots ' . $sizeClass . ' ' . $extra . '" ' . $common . '><span></span><span></span><span></span></div>'; break;
        case 'rainbow_ring':
        default:
            $html = '<div class="brand-spinner-rainbow ' . $sizeClass . ' ' . $extra . '" ' . $common . '></div>'; break;
    }
    return $html;
}
}

if (!function_exists('echoBrandSpinner')) {
/** Echo spinner directly */
function echoBrandSpinner($style = null, array $opts = []) { echo getBrandSpinnerHTML($style, $opts); }
}

if (!function_exists('getBrandSpinnerOverlayHTML')) {
/**
 * Return a full-screen overlay containing the spinner (hidden by default) for AJAX usage.
 * @param array $opts Additional opts passed to getBrandSpinnerHTML plus 'id' for container id.
 */
function getBrandSpinnerOverlayHTML(array $opts = []) {
    $id = $opts['id'] ?? 'brand-spinner-overlay';
    $spinner = getBrandSpinnerHTML(null, $opts);
    return '<div id="' . htmlspecialchars($id, ENT_QUOTES) . '" class="brand-spinner-overlay d-none" aria-hidden="true">' . $spinner . '</div>';
}
}

if (!function_exists('echoBrandSpinnerOverlay')) {
function echoBrandSpinnerOverlay(array $opts = []) { echo getBrandSpinnerOverlayHTML($opts); }
}

/**
 * BRAND ICON INLINE SVG HELPERS
 * Provide inline SVG for brand / social logos so we can remove external icon font dependencies.
 */
if (!function_exists('getBrandIconSVG')) {
function getBrandIconSVG($name, array $attrs = []) {
    $safe = preg_replace('/[^a-z0-9_-]+/i','', strtolower($name));
    if (!$safe) return '';
    $baseDir = PROJECT_ROOT . '/public_html/assets/icons';
    $file = $baseDir . '/' . $safe . '.svg';
    if (!is_file($file)) {
        // Fallback attempt for variants (e.g., facebook-square -> facebook)
        $alt = preg_replace('/-square$/','', $safe);
        $altFile = $baseDir . '/' . $alt . '.svg';
        if (is_file($altFile)) $file = $altFile; else return '';
    }
    $svg = @file_get_contents($file);
    if ($svg === false) return '';
    // Inject extra attributes if provided (e.g., class, aria-hidden)
    if ($attrs) {
        // Simple injection: add attributes to first <svg ...>
        if (preg_match('/<svg\s+[^>]*>/i', $svg, $m)) {
            $tag = $m[0];
            $extra = '';
            foreach ($attrs as $k=>$v) {
                $extra .= ' ' . htmlspecialchars($k, ENT_QUOTES) . '="' . htmlspecialchars($v, ENT_QUOTES) . '"';
            }
            $newTag = rtrim(substr($tag,0,-1)) . $extra . '>';
            $svg = str_replace($tag, $newTag, $svg);
        }
    }
    return $svg;
}
}

if (!function_exists('echoBrandIconSVG')) {
function echoBrandIconSVG($name, array $attrs = []) { echo getBrandIconSVG($name, $attrs); }
}

/**
 * Comprehensive login check with remember-me functionality
 * @param PDO $pdo Database connection
 * @param string $redirect_file Where to redirect if not logged in
 */
function check_loggedin_full($pdo, $redirect_file = 'auth.php?tab=login') {
    // Security headers for all responses
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('X-Content-Type-Options: nosniff');
    
    // First check for normal session
    if (isset($_SESSION['loggedin'])) {
        if (!isset($_SESSION['CREATED'])) {
            $_SESSION['CREATED'] = time();
        } else if (time() - $_SESSION['CREATED'] > 3600) {
            // Session older than 1 hour, regenerate ID
            session_regenerate_id(true);
            $_SESSION['CREATED'] = time();
        }
        
        // Update last seen
        $date = date('Y-m-d\TH:i:s');
        $stmt = $pdo->prepare('UPDATE accounts SET last_seen = ? WHERE id = ?');
        $stmt->execute([$date, $_SESSION['id']]);
        return;
    }

    // Check remember-me cookie if session not active
    if (isset($_COOKIE['rememberme'])) {
        require_once PROJECT_ROOT . '/private/remember-me-functions.php';
        
        $account = verifyRememberMeToken($pdo, $_COOKIE['rememberme']);
        if ($account) {
            // Clear any existing session data
            session_unset();
            session_destroy();
            
            // Start new session
            session_start();
            session_regenerate_id(true);
            
            // Set session variables
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['name'] = $account['username'];
            $_SESSION['id'] = $account['id'];
            $_SESSION['role'] = $account['role'];
            $_SESSION['CREATED'] = time();
            
            // Update last seen
            $date = date('Y-m-d\TH:i:s');
            $stmt = $pdo->prepare('UPDATE accounts SET last_seen = ? WHERE id = ?');
            $stmt->execute([$date, $account['id']]);
            
            // Create new remember-me token (rotation for security)
            if ($token = createRememberMeToken($pdo, $account['id'])) {
                setRememberMeCookie($token);
            }
            
            return;
        } else {
            // Invalid remember-me token, clear it
            setcookie('rememberme', '', time() - 3600, '/', '', true, true);
        }
    }

    // No valid session or remember-me token
    if (!isset($_SESSION['loggedin'])) {
        header('Location: ' . $redirect_file);
        exit;
    }
}

/**
 * Get the user's role from session
 * @return string User's role or empty string if not found
 */
function get_user_role() {
    return $_SESSION['role'] ?? '';
}

/**
 * Check if user has a specific role or higher
 * @param string|array $roles Single role or array of roles to check
 * @param bool $exact If true, checks for exact role match instead of minimum role level
 * @return bool True if user has any of the specified roles or higher
 */
function has_role($roles, $exact = false) {
    require_once PROJECT_ROOT . '/private/role-functions.php';
    
    $user_role = get_user_role();
    if (empty($user_role)) return false;
    
    if (is_array($roles)) {
        if ($exact) {
            return in_array($user_role, $roles);
        }
        // Check if user has minimum level of any specified role
        foreach ($roles as $role) {
            if (hasMinimumRole($user_role, $role)) {
                return true;
            }
        }
        return false;
    }
    
    return $exact ? ($user_role === $roles) : hasMinimumRole($user_role, $roles);
}

/**
 * Validate CSRF token
 * @param string $token Token from form
 * @return bool True if token is valid
 */
function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Check if user is logged in, redirect if not
 * @param string $redirect_file Where to redirect if not logged in
 */
function check_loggedin($redirect_file = 'auth.php?tab=login') {
    if (!isset($_SESSION['loggedin']) && !isset($_SESSION['client_id'])) {
        header('Location: ' . $redirect_file);
        exit;
    }
}

/**
 * Get current user ID from any valid session
 * @return int User ID or 0 if not logged in
 */
function get_current_user_id() {
    if (isset($_SESSION['id'])) {
        return $_SESSION['id'];
    } elseif (isset($_SESSION['client_id'])) {
        return $_SESSION['client_id'];
    }
    return 0;
}

/**
 * Get current user name from any valid session
 * @return string User name or empty string if not found
 */
function get_current_user_name() {
    if (isset($_SESSION['name'])) {
        return $_SESSION['name'];
    } elseif (isset($_SESSION['username'])) {
        return $_SESSION['username'];
    }
    return '';
}

// GENERAL UTILITY FUNCTIONS

/**
 * Truncate text to a given length, ending at a word boundary
 */
if (!function_exists('short_text')) {
    function short_text($text, $length) {
        $maxTextLength = $length;
        $aspace = " ";
        if (strlen($text) > $maxTextLength) {
            $text = substr(trim($text), 0, $maxTextLength);
            $text = substr($text, 0, strlen($text) - strpos(strrev($text), $aspace));
            $text = $text . "...";
        }
        return $text;
    }
}

/**
 * Convert emoticon text to emoji
 */
if (!function_exists('emoticons')) {
    function emoticons($text) {
        $icons = array(
            ':)' => '🙂', ':-)' => '🙂', ':}' => '🙂', ':D' => '😀', ':d' => '😁', ':-D ' => '😂',
            ';D' => '😂', ';d' => '😂', ';)' => '😉', ';-)' => '😉', ':P' => '😛', ':-P' => '😛',
            ':-p' => '😛', ':p' => '😛', ':-b' => '😛', ':-Þ' => '😛', ':(' => '🙁', ';(' => '😓',
            ':\'(' => '😓', ':o' => '😮', ':O' => '😮', ':0' => '😮', ':-O' => '😮', ':|' => '😐',
            ':-|' => '😐', ' :/' => ' 😕', ':-/' => '😕', ':X' => '😷', ':x' => '😷', ':-X' => '😷',
            ':-x' => '😷', '8)' => '😎', '8-)' => '😎', 'B-)' => '😎', ':3' => '😊', '^^' => '😊',
            '^_^' => '😊', '<3' => '😍', ':*' => '😘', 'O:)' => '😇', '3:)' => '😈', 'o.O' => '😵',
            'O_o' => '😵', 'O_O' => '😵', 'o_o' => '😵', '0_o' => '😵', 'T_T' => '😵', '-_-' => '😑',
            '>:O' => '😆', '><' => '😆', '>:(' => '😣', ':v' => '🙃', '(y)' => '👍', ':poop:' => '💩', ':|]' => '🤖'
        );
        return strtr($text, $icons);
    }
}

/**
 * Get avatar URL for user with role-based fallback
 * @param array $account User account data with 'avatar' and 'role' fields
 * @return string Complete URL to avatar image
 */
function getUserAvatar($account)
{
    $avatar_dir = public_path . '/accounts_system/assets/uploads/avatars/';
    if (!empty($account['avatar'])) {
        $avatar_file_path = $avatar_dir . $account['avatar'];
        if (file_exists($avatar_file_path)) {
            return ACCOUNTS_AVATARS_URL . '/' . $account['avatar'];
        }
    }
    $default_avatar = '';
    switch (strtolower($account['role'])) {
        case 'developer': $default_avatar = 'default-developer.svg'; break;
        case 'admin': $default_avatar = 'default-admin.svg'; break;
        case 'editor': $default_avatar = 'default-editor.svg'; break;
        case 'blog_user': $default_avatar = 'default-blog.svg'; break;
        case 'member': $default_avatar = 'default-member.svg'; break;
        case 'subscriber': $default_avatar = 'default-user.svg'; break;
        case 'guest': $default_avatar = 'default-guest.svg'; break;
        default: $default_avatar = 'default-user.svg';
    }
    return ACCOUNTS_AVATARS_URL . '/' . $default_avatar;
}

/**
 * Get just the avatar filename for user with role-based fallback
 * @param array $account User account data with 'avatar' and 'role' fields
 * @return string Avatar filename (including extension)
 */
function getUserAvatarFilename($account)
{
    if (!empty($account['avatar'])) {
        $avatar_file_path = public_path . '/accounts_system/assets/uploads/avatars/' . $account['avatar'];
        if (file_exists($avatar_file_path)) {
            return $account['avatar'];
        }
    }
    switch (strtolower($account['role'])) {
        case 'developer': return 'default-developer.svg';
        case 'admin': return 'default-admin.svg';
        case 'editor': return 'default-editor.svg';
        case 'blog_user': return 'default-blog.svg';
        case 'member': return 'default-member.svg';
        case 'subscriber': 
        case 'guest': return 'default-user.svg';
        default: return 'default-user.svg';
    }
}

/**
 * Convert file size from bytes to human readable format
 * @param int $bytes File size in bytes
 * @param int $precision Number of decimal places
 * @return string Formatted file size (e.g., "1.5 MB")
 */
function convert_filesize($bytes, $precision = 2) {
    if ($bytes == 0) return '0 B';
    
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    $base = log($bytes, 1024);
    $index = floor($base);
    
    if ($index >= count($units)) {
        $index = count($units) - 1;
    }
    
    $size = pow(1024, $base - $index);
    return round($size, $precision) . ' ' . $units[$index];
}

/**
 * Calculate total directory size recursively
 * @param string $directory Path to directory
 * @return int Total size in bytes
 */
function dir_size($directory) {
    $size = 0;
    
    if (!is_dir($directory)) {
        return 0;
    }
    
    try {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
    } catch (Exception $e) {
        // Handle permission errors or other issues gracefully
        error_log("dir_size error for $directory: " . $e->getMessage());
        return 0;
    }
    
    return $size;
}?>