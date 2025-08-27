<?php
/* 
 * Settings Dashboard - Business Configuration Center
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: settings_dash.php
 * LOCATION: /public_html/admin/settings/
 * PURPOSE: Intuitive business setup and configuration management dashboard
 * 
 * FEATURES:
 * - Business setup wizard
 * - Configuration completion tracking
 * - System metrics and health monitoring
 * - Organized settings categories with tabs
 * - New installation guidance
 * - Professional, minimal interface
 * 
 * VERSION: 3.0 - Complete redesign for business setup focus
 * UPDATED: 2025-08-18
 */

// Include admin main file which handles authentication
include_once '../assets/includes/main.php';

// Initialize admin template parameters
$selected = 'settings';
$selected_child = 'dashboard';
$title = 'Settings Dashboard';

// Database connection
try {
    $stmt = $pdo->prepare('SELECT * FROM accounts WHERE id = ?');
    $stmt->execute([$_SESSION['id']]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$account) {
        header('Location: ../login.php');
        exit;
    }
} catch(PDOException $e) {
    error_log("Settings Dashboard Error: " . $e->getMessage());
    $error_message = "Database connection error.";
}

// Business configuration tracking
$business_setup_items = [
    'business_info' => [
        'title' => 'Business Information',
        'description' => 'Company name, logo, contact details',
        'file' => 'branding_settings.php',
        'icon' => 'building',
        'priority' => 1,
        'category' => 'essential'
    ],
    'site_identity' => [
        'title' => 'Website Identity',
        'description' => 'Site title, description, SEO basics',
        'file' => 'seo_settings.php',
        'icon' => 'globe',
        'priority' => 2,
        'category' => 'essential'
    ],
    'content_setup' => [
        'title' => 'Content Configuration',
        'description' => 'Homepage content, about page, basic pages',
        'file' => 'content_settings.php',
        'icon' => 'file-text',
        'priority' => 3,
        'category' => 'essential'
    ],
    'contact_info' => [
        'title' => 'Contact Settings',
        'description' => 'Contact forms, business address, phone',
        'file' => 'public_settings.php',
        'icon' => 'phone',
        'priority' => 4,
        'category' => 'essential'
    ],
    'user_accounts' => [
        'title' => 'User Management',
        'description' => 'Account settings, registration, permissions',
        'file' => 'account_settings.php',
        'icon' => 'users',
        'priority' => 5,
        'category' => 'important'
    ],
    'ecommerce_setup' => [
        'title' => 'E-commerce Settings',
        'description' => 'Shop configuration, payment, shipping',
        'file' => 'shop_settings.php',
        'icon' => 'shopping-cart',
        'priority' => 6,
        'category' => 'optional'
    ],
    'blog_config' => [
        'title' => 'Blog Configuration',
        'description' => 'Blog settings, categories, features',
        'file' => 'blog_settings.php',
        'icon' => 'edit',
        'priority' => 7,
        'category' => 'optional'
    ],
    'system_config' => [
        'title' => 'System Settings',
        'description' => 'Advanced system configuration',
        'file' => 'system_settings.php',
        'icon' => 'cogs',
        'priority' => 8,
        'category' => 'advanced'
    ]
];

// Check completion status for each item
function checkSettingCompletion($setting_file) {
    // Simple check - if file exists and has some configuration
    if (file_exists($setting_file)) {
        $content = file_get_contents($setting_file);
        // Check if it has meaningful content (not just default/empty)
        return strlen(trim($content)) > 500; // Basic heuristic
    }
    return false;
}

// Calculate completion metrics
$essential_completed = 0;
$important_completed = 0;
$optional_completed = 0;
$total_essential = 0;
$total_important = 0;
$total_optional = 0;

foreach ($business_setup_items as $key => $item) {
    $is_complete = checkSettingCompletion($item['file']);
    
    switch ($item['category']) {
        case 'essential':
            $total_essential++;
            if ($is_complete) $essential_completed++;
            break;
        case 'important':
            $total_important++;
            if ($is_complete) $important_completed++;
            break;
        case 'optional':
            $total_optional++;
            if ($is_complete) $optional_completed++;
            break;
    }
    
    $business_setup_items[$key]['completed'] = $is_complete;
}

// Create settings arrays for tabs
$essential_settings = [];
$important_settings = [];
$optional_settings = [];

foreach ($business_setup_items as $key => $item) {
    $setting = [
        'title' => $item['title'],
        'description' => $item['description'],
        'link' => $item['file'],
        'status' => $item['completed'] ? 'complete' : 'incomplete',
        'icon' => $item['icon']
    ];
    
    switch ($item['category']) {
        case 'essential':
            $essential_settings[] = $setting;
            break;
        case 'important':
            $important_settings[] = $setting;
            break;
        case 'optional':
            $optional_settings[] = $setting;
            break;
    }
}

// Overall completion percentage
$total_items = count($business_setup_items);
$completed_items = array_sum(array_column($business_setup_items, 'completed'));
$completion_percentage = $total_items > 0 ? round(($completed_items / $total_items) * 100) : 0;

// System health checks
$system_health = [
    'database' => [
        'status' => 'good',
        'message' => 'Database connection active',
        'check' => function() {
            global $pdo;
            try {
                $pdo->query('SELECT 1');
                return ['status' => 'good', 'message' => 'Database connection active'];
            } catch (Exception $e) {
                return ['status' => 'error', 'message' => 'Database connection failed'];
            }
        }
    ],
    'file_permissions' => [
        'status' => 'good',
        'message' => 'File permissions correct',
        'check' => function() {
            $upload_dir = '../../uploads/';
            if (is_writable($upload_dir)) {
                return ['status' => 'good', 'message' => 'File permissions correct'];
            } else {
                return ['status' => 'warning', 'message' => 'Upload directory not writable'];
            }
        }
    ],
    'ssl_status' => [
        'status' => isset($_SERVER['HTTPS']) ? 'good' : 'warning',
        'message' => isset($_SERVER['HTTPS']) ? 'SSL enabled' : 'SSL not detected',
        'check' => function() {
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                return ['status' => 'good', 'message' => 'SSL enabled'];
            } else {
                return ['status' => 'warning', 'message' => 'SSL not detected'];
            }
        }
    ]
];

// Run health checks
foreach ($system_health as $key => $check) {
    if (isset($check['check']) && is_callable($check['check'])) {
        $result = $check['check']();
        $system_health[$key] = array_merge($system_health[$key], $result);
    }
}


foreach ($settings_files as $name => $info) {
    $file_path = $info['file'];
    $required_role = $info['role'] ?? 'Admin';
    
    // Check if user has access to this file based on role
    if ($_SESSION['admin_role'] === 'Developer' || 
        ($required_role === 'Admin' && in_array($_SESSION['admin_role'], ['Admin', 'Editor']))) {
        $accessible_files++;
        
        // Check if file actually exists
        if (file_exists($file_path)) {
            $existing_files++;
            $configured_settings++;
        }
    }
}

require_once 'page_detection.php';

// Get detected pages
$detected_pages = detectExistingPages();

// Get page completion statistics
$stmt = $pdo->query('SELECT COUNT(*) as total_pages, SUM(is_complete) as completed_pages FROM page_completion_status');
$page_stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Initialize page completion status if not exists
foreach ($public_pages as $path => $name) {
    $stmt = $pdo->prepare('INSERT IGNORE INTO page_completion_status (page_path, page_name, is_complete) VALUES (?, ?, 0)');
    $stmt->execute([$path, $name]);
}

// Get updated page stats
$stmt = $pdo->query('SELECT COUNT(*) as total_pages, SUM(is_complete) as completed_pages FROM page_completion_status');
$page_stats = $stmt->fetch(PDO::FETCH_ASSOC);
$total_pages = $page_stats['total_pages'] ?: count($public_pages);
$completed_pages = $page_stats['completed_pages'] ?: 0;

// Get action items
$action_items = [];

// Check for settings that need attention
$incomplete_settings = [];
foreach ($settings_files as $name => $info) {
    if (!file_exists($info['file'])) {
        $incomplete_settings[] = $name . ' (File Missing)';
    }
}

// Get pages needing attention
$stmt = $pdo->query('SELECT page_name FROM page_completion_status WHERE is_complete = 0 ORDER BY page_name');
$incomplete_pages = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Get recent activity
$stmt = $pdo->query('SELECT page_name, last_checked FROM page_completion_status WHERE last_checked > DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY last_checked DESC LIMIT 5');
$recent_activity = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Content scanning function
function scanPageContent($page_path) {
    $full_path = "../../../public_html/" . $page_path;
    if (!file_exists($full_path)) {
        return ['error' => 'Page not found'];
    }
    
    $content = file_get_contents($full_path);
    $issues = [];
    
    // Remove PHP code blocks to avoid false positives
    $content_no_php = preg_replace('/<\?php.*?\?>/s', '', $content);
    $content_no_php = preg_replace('/<\?.*?\?>/s', '', $content_no_php);
    
    // Look for actual hardcoded HTML content in common tags
    $html_tags = ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'span', 'div', 'a', 'li', 'td', 'th', 'label', 'button'];
    
    foreach ($html_tags as $tag) {
        // Match content between HTML tags that's not empty and substantial
        if (preg_match_all("/<{$tag}[^>]*>([^<>{]+)<\/{$tag}>/i", $content_no_php, $matches)) {
            foreach ($matches[1] as $text) {
                $text = trim($text);
                // Only flag substantial text content (not single words, numbers, or common UI elements)
                if (strlen($text) > 15 && 
                    !preg_match('/^[\d\s\-\.,:;!?()]+$/', $text) && // Not just numbers/punctuation
                    !in_array(strtolower($text), ['home', 'about', 'contact', 'login', 'logout', 'submit', 'cancel', 'save', 'edit', 'delete', 'back', 'next', 'previous', 'search']) && // Not common UI text
                    !preg_match('/^\$[a-zA-Z_]/', $text) && // Not variables
                    substr_count($text, ' ') >= 2) { // At least 3 words
                    
                    $issues[] = "Hardcoded content in <{$tag}>: " . substr($text, 0, 60) . (strlen($text) > 60 ? "..." : "");
                }
            }
        }
    }
    
    // Check for hardcoded text in value attributes (form inputs, etc.)
    if (preg_match_all('/value=["\']([^"\']{15,})["\']/', $content_no_php, $matches)) {
        foreach ($matches[1] as $text) {
            if (!preg_match('/^[\d\s\-\.@]+$/', $text) && // Not just numbers/emails
                !preg_match('/^\$[a-zA-Z_]/', $text)) { // Not variables
                $issues[] = "Hardcoded value attribute: " . substr($text, 0, 50) . (strlen($text) > 50 ? "..." : "");
            }
        }
    }
    
    // Check for hardcoded text in alt attributes
    if (preg_match_all('/alt=["\']([^"\']{10,})["\']/', $content_no_php, $matches)) {
        foreach ($matches[1] as $text) {
            if (!preg_match('/^\$[a-zA-Z_]/', $text)) { // Not variables
                $issues[] = "Hardcoded alt text: " . substr($text, 0, 50) . (strlen($text) > 50 ? "..." : "");
            }
        }
    }
    
    // Check for TODO comments
    if (preg_match_all('/TODO:?\s*(.+)/', $content, $matches)) {
        foreach ($matches[1] as $todo) {
            $issues[] = "TODO: " . trim($todo);
        }
    }
    
    return [
        'issues' => array_slice(array_unique($issues), 0, 15), // Remove duplicates and limit to 15 issues
        'total_issues' => count(array_unique($issues))
    ];
}

?>
<?php echo template_admin_header('Settings Dashboard', 'settings', 'dashboard'); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                        <path d="M495.9 166.6c3.2 8.7 .5 18.4-6.4 24.6l-43.3 39.4c1.1 8.3 1.7 16.8 1.7 25.4s-.6 17.1-1.7 25.4l43.3 39.4c6.9 6.2 9.6 15.9 6.4 24.6c-4.4 11.9-9.7 23.3-15.8 34.3l-4.7 8.1c-6.6 11-14 21.4-22.1 31.2c-5.9 7.2-15.7 9.6-24.5 6.8l-55.7-17.7c-13.4 10.3-28.2 18.9-44 25.4l-12.5 57.1c-2 9.1-9 16.3-18.2 17.8c-13.8 2.3-28 3.5-42.5 3.5s-28.7-1.2-42.5-3.5c-9.2-1.5-16.2-8.7-18.2-17.8l-12.5-57.1c-15.8-6.5-30.6-15.1-44-25.4L83.1 425.9c-8.8 2.8-18.6 .3-24.5-6.8c-8.1-9.8-15.5-20.2-22.1-31.2l-4.7-8.1c-6.1-11-11.4-22.4-15.8-34.3c-3.2-8.7-.5-18.4 6.4-24.6l43.3-39.4C64.6 273.1 64 264.6 64 256s.6-17.1 1.7-25.4L22.4 191.2c-6.9-6.2-9.6-15.9-6.4-24.6c4.4-11.9 9.7-23.3 15.8-34.3l4.7-8.1c6.6-11 14-21.4 22.1-31.2c5.9-7.2 15.7-9.6 24.5-6.8l55.7 17.7c13.4-10.3 28.2-18.9 44-25.4l12.5-57.1c2-9.1 9-16.3 18.2-17.8C227.3 1.2 241.5 0 256 0s28.7 1.2 42.5 3.5c9.2 1.5 16.2 8.7 18.2 17.8l12.5 57.1c15.8 6.5 30.6 15.1 44 25.4l55.7-17.7c8.8-2.8 18.6-.3 24.5 6.8c8.1 9.8 15.5 20.2 22.1 31.2l4.7 8.1c6.1 11 11.4 22.4 15.8 34.3zM256 336a80 80 0 1 0 0-160 80 80 0 1 0 0 160z"/>
                    </svg>
                    &nbsp;&nbsp;Business Configuration Center
                </h1>
                <div class="d-flex gap-2">
                    <a href="new_installation.php" class="btn btn-primary btn-sm">
                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                            <path d="M156.6 384.9L125.7 354c-8.5-8.5-11.5-20.8-7.7-32.2c3-8.9 7-20.5 11.8-33.8L24 288c-8.6 0-16.6-4.6-20.9-12.1s-4.2-16.7 .2-24.1l52.5-87.5c4.3-7.4 12.2-11.9 20.7-11.9s16.4 4.6 20.7 11.9L149.6 256c2.5-2.3 5-4.5 7.4-6.8c2.8-2.6 5.7-5.1 8.6-7.5c-4.9-24.2-1.9-49.7 9.4-71.5L122.6 117c-7.4-4.3-11.9-12.2-11.9-20.7s4.6-16.4 11.9-20.7L210.1 23.1c7.4-4.3 16.7-4.2 24.1 .2S246.3 35.6 246.3 44.2L246.3 136c15.1-8.9 32.2-13.4 49.6-13.4c5.4 0 10.7 .4 15.9 1.2L362.6 42.1c4.6-8.6 13.6-13.9 23.7-13.9c10.1 0 19.1 5.3 23.7 13.9l52.5 87.5c4.3 7.4 4.6 16.7 .2 24.1S450.4 167.9 441.8 167.9L349.1 167.9c9.5 20.9 13.3 45.3 8.5 69.8c24.2 4.9 49.7 1.9 71.5-9.4L482.3 281c4.3 7.4 5.3 16.7 .2 24.1S470.4 318.2 461.8 318.2L369.1 318.2c-8.9 15.1-13.4 32.2-13.4 49.6c0 5.4 .4 10.7 1.2 15.9L469.9 434.4c8.6 4.6 13.9 13.6 13.9 23.7c0 10.1-5.3 19.1-13.9 23.7l-87.5 52.5c-7.4 4.3-16.7 4.6-24.1 .2S344.1 522.2 344.1 513.6L344.1 420.9c-20.9-9.5-45.3-13.3-69.8-8.5c-4.9 24.2-1.9 49.7 9.4 71.5L336.4 537.1c7.4 4.3 11.9 12.2 11.9 20.7s-4.6 16.4-11.9 20.7L248.9 630.9c-7.4 4.3-16.7 4.2-24.1-.2S212.7 618.4 212.7 609.8L212.7 517.1c-15.1 8.9-32.2 13.4-49.6 13.4c-5.4 0-10.7-.4-15.9-1.2L95.4 611C90.8 619.6 81.8 624.9 71.7 624.9c-10.1 0-19.1-5.3-23.7-13.9L-.5 523.5c-4.3-7.4-4.6-16.7-.2-24.1S11.6 486.1 20.2 486.1L112.9 486.1c-9.5-20.9-13.3-45.3-8.5-69.8c-24.2-4.9-49.7-1.9-71.5 9.4L-20.3 372.4c-4.3-7.4-5.3-16.7-.2-24.1S-8.4 335.2 .2 335.2L92.9 335.2c8.9-15.1 13.4-32.2 13.4-49.6c0-5.4-.4-10.7-1.2-15.9L-7.9 217c-8.6-4.6-13.9-13.6-13.9-23.7c0-10.1 5.3-19.1 13.9-23.7l87.5-52.5c7.4-4.3 16.7-4.6 24.1-.2S116.9 129.2 116.9 137.8L116.9 230.5c20.9 9.5 45.3 13.3 69.8 8.5c4.9-24.2 1.9-49.7-9.4-71.5L230.6 114.3c-7.4-4.3-11.9-12.2-11.9-20.7s4.6-16.4 11.9-20.7L318.1 20.5c7.4-4.3 16.7-4.2 24.1 .2S354.3 33 354.3 41.6L354.3 134.3c15.1-8.9 32.2-13.4 49.6-13.4c5.4 0 10.7 .4 15.9 1.2L505.1 204.8c8.6 4.6 13.9 13.6 13.9 23.7c0 10.1-5.3 19.1-13.9 23.7l-87.5 52.5c-7.4 4.3-16.7 4.6-24.1 .2S380.1 292.6 380.1 284L380.1 191.3c-20.9-9.5-45.3-13.3-69.8-8.5z"/>
                        </svg>
                        &nbsp;&nbsp;New Installation Wizard
                    </a>
                    <a href="settings_help.php" class="btn btn-secondary btn-sm">
                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                            <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM216 336h24V272H216c-13.3 0-24-10.7-24-24s10.7-24 24-24h48c13.3 0 24 10.7 24 24v88h8c13.3 0 24 10.7 24 24s-10.7 24-24 24H216c-13.3 0-24-10.7-24-24s10.7-24 24-24zm40-208a32 32 0 1 1 0 64 32 32 0 1 1 0-64z"/>
                        </svg>
                        &nbsp;&nbsp;Help Guide
                    </a>
                </div>
            </div>

            <!-- Top Row: Quick Actions, Action Items, and Statistics -->
            <div class="row mb-4">
                <!-- Quick Actions -->
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="app-card" role="region" aria-labelledby="quick-actions-title">
                        <div class="app-header" role="banner" aria-labelledby="quick-actions-title">
                            <h3 id="quick-actions-title">Quick Actions</h3>
                            <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="header-icon">
                                <path d="M349.4 44.6c5.9-13.7 1.5-29.7-10.6-38.5s-28.6-8-39.9 1.8l-256 224c-10 8.8-13.6 22.9-8.9 35.3S50.7 288 64 288H175.5L98.6 467.4c-5.9 13.7-1.5 29.7 10.6 38.5s28.6 8 39.9-1.8l256-224c10-8.8 13.6-22.9 8.9-35.3s-16.6-20.7-30-20.7H272.5L349.4 44.6z"/>
                            </svg>
                        </div>
                        <div class="app-body">
                            <div class="quick-actions-grid">
                                <a href="new_installation.php" class="quick-action primary">
                                    <div class="action-icon">
                                        <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                            <path d="M256 0c4.6 0 9.2 1 13.4 2.9L457.7 82.8c22 9.3 38.4 31 38.3 57.2c-.5 99.2-41.3 280.7-213.6 363.2c-16.7 8-36.1 8-52.8 0C57.3 420.7 16.5 239.2 16 140c-.1-26.2 16.3-47.9 38.3-57.2L242.7 2.9C246.8 1 251.4 0 256 0zm0 66.8V444.8C394 378 431.1 230.1 432 141.4L256 66.8l0 0z"/>
                                        </svg>
                                    </div>
                                    <div class="action-details">
                                        <h4>Setup Wizard</h4>
                                        <small class="text-muted">New installation guide</small>
                                    </div>
                                </a>
                                <a href="settings_help.php" class="quick-action info">
                                    <div class="action-icon">
                                        <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                            <path d="M464 256A208 208 0 1 0 48 256a208 208 0 1 0 416 0zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zM169.8 165.3c7.9-22.3 29.1-37.3 52.8-37.3h58.3c34.9 0 63.1 28.3 63.1 63.1c0 22.6-12.1 43.5-31.7 54.8L280 264.4c-.2 13-10.9 23.6-24 23.6s-24-10.7-24-24V250.5c0-8.6 4.6-16.5 12.1-20.8l44.3-25.4c4.7-2.7 7.6-7.7 7.6-13.1c0-8.4-6.8-15.1-15.1-15.1H222.6c-3.4 0-6.4 2.1-7.5 5.3l-.4 1.2c-4.4 12.5-18.2 19-30.6 14.6s-19-18.2-14.6-30.6l.4-1.2zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"/>
                                        </svg>
                                    </div>
                                    <div class="action-details">
                                        <h4>Help Guide</h4>
                                        <small class="text-muted">Documentation & support</small>
                                    </div>
                                </a>
                                <a href="../../index.php" class="quick-action secondary">
                                    <div class="action-icon">
                                        <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                            <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-92.7-69.4z"/>
                                        </svg>
                                    </div>
                                    <div class="action-details">
                                        <h4>Preview Website</h4>
                                        <small class="text-muted">View public site</small>
                                    </div>
                                </a>
                                <a href="../shop_system/shop_dash.php" class="quick-action success">
                                    <div class="action-icon">
                                        <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                            <path d="M0 24C0 10.7 10.7 0 24 0H69.5c22 0 41.5 12.8 50.6 32h411c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3H170.7l5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5H488c13.3 0 24 10.7 24 24s-10.7 24-24 24H199.7c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5H24C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z"/>
                                        </svg>
                                    </div>
                                    <div class="action-details">
                                        <h4>Shop Dashboard</h4>
                                        <small class="text-muted">Manage store</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Items -->
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="app-card" role="region" aria-labelledby="action-items-title">
                        <div class="app-header" role="banner" aria-labelledby="action-items-title">
                            <h3 id="action-items-title">Action Items</h3>
                            <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="header-icon">
                                <path d="M256 32c14.2 0 27.3 7.5 34.5 19.8l216 368c7.3 12.4 7.3 27.7 .2 40.1S486.3 480 472 480H40c-14.3 0-27.6-7.7-34.7-20.1s-7-27.8 .2-40.1l216-368C228.7 39.5 241.8 32 256 32zm0 128c-13.3 0-24 10.7-24 24V296c0 13.3 10.7 24 24 24s24-10.7 24-24V184c0-13.3-10.7-24-24-24zm32 224a32 32 0 1 0 -64 0 32 32 0 1 0 64 0z"/>
                            </svg>
                            <?php if ($total_essential - $essential_completed > 0): ?>
                                <span class="badge badge-urgent"><?= $total_essential - $essential_completed ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="app-body">
                            <div class="action-items-list">
                                <?php if ($total_essential - $essential_completed > 0): ?>
                                    <div class="action-item urgent">
                                        <div class="item-icon">
                                            <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                <path d="M256 32c14.2 0 27.3 7.5 34.5 19.8l216 368c7.3 12.4 7.3 27.7 .2 40.1S486.3 480 472 480H40c-14.3 0-27.6-7.7-34.7-20.1s-7-27.8 .2-40.1l216-368C228.7 39.5 241.8 32 256 32zm0 128c-13.3 0-24 10.7-24 24V296c0 13.3 10.7 24 24 24s24-10.7 24-24V184c0-13.3-10.7-24-24-24zm32 224a32 32 0 1 0 -64 0 32 32 0 1 0 64 0z"/>
                                            </svg>
                                        </div>
                                        <div class="item-details">
                                            <h5>Essential Setup Required</h5>
                                            <p><?= $total_essential - $essential_completed ?> critical settings need configuration</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($total_important - $important_completed > 0): ?>
                                    <div class="action-item warning">
                                        <div class="item-icon">
                                            <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                                                <path d="M48 0C21.5 0 0 21.5 0 48V464c0 26.5 21.5 48 48 48h96V432c0-26.5 21.5-48 48-48s48 21.5 48 48v80h96c26.5 0 48-21.5 48-48V48c0-26.5-21.5-48-48-48H48zM64 240c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V240zm112-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H176c-8.8 0-16-7.2-16-16V240c0-8.8 7.2-16 16-16zm80 16c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H272c-8.8 0-16-7.2-16-16V240zM80 96h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16zm80 16c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H176c-8.8 0-16-7.2-16-16V112zm112-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H272c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16z"/>
                                            </svg>
                                        </div>
                                        <div class="item-details">
                                            <h5>Business Settings Pending</h5>
                                            <p><?= $total_important - $important_completed ?> business settings need attention</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($total_essential + $total_important == $essential_completed + $important_completed): ?>
                                    <div class="action-item success">
                                        <div class="item-icon">
                                            <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                <path d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z"/>
                                            </svg>
                                        </div>
                                        <div class="item-details">
                                            <h5>All Set!</h5>
                                            <p>Essential and business settings are configured</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="col-lg-4 col-md-12 mb-3">
                    <div class="app-card" role="region" aria-labelledby="stats-title">
                        <div class="app-header" role="banner" aria-labelledby="stats-title">
                            <h3 id="stats-title">Statistics</h3>
                            <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="header-icon">
                                <path d="M160 80c0-26.5 21.5-48 48-48h32c26.5 0 48 21.5 48 48V432c0 26.5-21.5 48-48 48H208c-26.5 0-48-21.5-48-48V80zM0 272c0-26.5 21.5-48 48-48H80c26.5 0 48 21.5 48 48V432c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V272zM368 96h32c26.5 0 48 21.5 48 48V432c0 26.5-21.5 48-48 48H368c-26.5 0-48-21.5-48-48V144c0-26.5 21.5-48 48-48z"/>
                            </svg>
                        </div>
                        <div class="app-body">
                            <div class="stats-grid">
                                <div class="stat-item">
                                    <div class="stat-value"><?= $essential_completed ?>/<?= $total_essential ?></div>
                                    <div class="stat-label">Essential Settings</div>
                                    <div class="stat-progress">
                                        <div class="progress-bar" style="width: <?= $total_essential > 0 ? round(($essential_completed / $total_essential) * 100) : 0 ?>%"></div>
                                    </div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value"><?= $important_completed ?>/<?= $total_important ?></div>
                                    <div class="stat-label">Business Settings</div>
                                    <div class="stat-progress">
                                        <div class="progress-bar" style="width: <?= $total_important > 0 ? round(($important_completed / $total_important) * 100) : 0 ?>%"></div>
                                    </div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value"><?= $optional_completed ?>/<?= $total_optional ?></div>
                                    <div class="stat-label">Technical Features</div>
                                    <div class="stat-progress">
                                        <div class="progress-bar" style="width: <?= $total_optional > 0 ? round(($optional_completed / $total_optional) * 100) : 0 ?>%"></div>
                                    </div>
                                </div>
                                
                                <!-- System Health Compact View -->
                                <div class="system-health-compact mt-3">
                                    <h6 class="text-muted mb-2">System Health</h6>
                                    <div class="health-indicators">
                                        <?php foreach ($system_health as $key => $health): ?>
                                            <span class="health-badge <?= $health['status'] ?>" title="<?= ucwords(str_replace('_', ' ', $key)) ?>: <?= $health['status'] ?>">
                                                <?= ucwords(str_replace('_', ' ', $key)) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Setup Progress -->
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="app-card" role="region" aria-labelledby="setup-progress-title">
                        <div class="app-header" role="banner" aria-labelledby="setup-progress-title">
                            <h3 id="setup-progress-title">Setup Progress</h3>
                            <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="header-icon">
                                <path d="M160 80c0-26.5 21.5-48 48-48h32c26.5 0 48 21.5 48 48V432c0 26.5-21.5 48-48 48H208c-26.5 0-48-21.5-48-48V80zM0 272c0-26.5 21.5-48 48-48H80c26.5 0 48 21.5 48 48V432c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V272zM368 96h32c26.5 0 48 21.5 48 48V432c0 26.5-21.5 48-48 48H368c-26.5 0-48-21.5-48-48V144c0-26.5 21.5-48 48-48z"/>
                            </svg>
                            <span class="badge" aria-label="<?= $completion_percentage ?>% complete"><?= $completion_percentage ?>%</span>
                        </div>
                        <div class="app-body">
                            <div class="stats-grid">
                                <div class="stat-item">
                                    <div class="stat-value"><?= $completion_percentage ?>%</div>
                                    <div class="stat-label">Overall Complete</div>
                                    <div class="stat-progress">
                                        <div class="progress-bar" style="width: <?= $completion_percentage ?>%"></div>
                                    </div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value"><?= $essential_completed ?>/<?= $total_essential ?></div>
                                    <div class="stat-label">Essential Setup</div>
                                    <div class="stat-progress">
                                        <div class="progress-bar <?= $essential_completed == $total_essential ? 'complete' : 'warning' ?>" 
                                             style="width: <?= $total_essential > 0 ? round(($essential_completed / $total_essential) * 100) : 0 ?>%"></div>
                                    </div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value"><?= $important_completed ?>/<?= $total_important ?></div>
                                    <div class="stat-label">Business Features</div>
                                    <div class="stat-progress">
                                        <div class="progress-bar info" 
                                             style="width: <?= $total_important > 0 ? round(($important_completed / $total_important) * 100) : 0 ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Health -->
                <div class="col-lg-4 col-md-12 mb-3">
                    <div class="app-card" role="region" aria-labelledby="system-health-title">
                        <div class="app-header" role="banner" aria-labelledby="system-health-title">
                            <h3 id="system-health-title">System Health</h3>
                            <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="header-icon">
                                <path d="M142.4 21.9c5.6 16.8-3.5 34.9-20.2 40.5L96 71.1V192c0 53 43 96 96 96s96-43 96-96V71.1l-26.1-8.7c-16.8-5.6-25.8-23.7-20.2-40.5s23.7-25.8 40.5-20.2l26.1 8.7C334.4 19.1 352 43.5 352 71.1V192c0 77.2-54.6 141.6-127.3 156.7C231 404.6 278.4 448 336 448c61.9 0 112-50.1 112-112V265.3c-28.3-12.3-48-40.5-48-73.3c0-44.2 35.8-80 80-80s80 35.8 80 80c0 32.8-19.7 61-48 73.3V336c0 97.2-78.8 176-176 176c-92.9 0-168.9-71.9-175.5-163.1C87.2 334.2 32 269.6 32 192V71.1c0-27.5 17.6-52 43.8-60.7l26.1-8.7c16.8-5.6 34.9 3.5 40.5 20.2z"/>
                            </svg>
                        </div>
                        <div class="app-body">
                            <div class="health-status">
                                <?php foreach ($system_health as $key => $health): ?>
                                    <div class="health-item">
                                        <span class="health-indicator <?= $health['status'] ?>"></span>
                                        <span class="health-label"><?= ucwords(str_replace('_', ' ', $key)) ?></span>
                                        <span class="health-status-text"><?= $health['message'] ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Second Row: Business Configuration (Full Width) -->
            <div class="row">
                <div class="col-12">
                    <!-- Business Configuration Tabs -->
                    <div class="app-card" role="region" aria-labelledby="config-tabs-title">
                        <div class="app-header" role="banner" aria-labelledby="config-tabs-title">
                            <h3 id="config-tabs-title">Business Configuration</h3>
                            <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="header-icon">
                                <path d="M495.9 166.6c3.2 8.7 .5 18.4-6.4 24.6l-43.3 39.4c1.1 8.3 1.7 16.8 1.7 25.4s-.6 17.1-1.7 25.4l43.3 39.4c6.9 6.2 9.6 15.9 6.4 24.6c-4.4 11.9-9.7 23.3-15.8 34.3l-4.7 8.1c-6.6 11-14 21.4-22.1 31.2c-5.9 7.2-15.7 9.6-24.5 6.8l-55.7-17.7c-13.4 10.3-28.2 18.9-44 25.4l-12.5 57.1c-2 9.1-9 16.3-18.2 17.8c-13.8 2.3-28 3.5-42.5 3.5s-28.7-1.2-42.5-3.5c-9.2-1.5-16.2-8.7-18.2-17.8l-12.5-57.1c-15.8-6.5-30.6-15.1-44-25.4L83.1 425.9c-8.8 2.8-18.6 .3-24.5-6.8c-8.1-9.8-15.5-20.2-22.1-31.2l-4.7-8.1c-6.1-11-11.4-22.4-15.8-34.3c-3.2-8.7-.5-18.4 6.4-24.6l43.3-39.4C64.6 273.1 64 264.6 64 256s.6-17.1 1.7-25.4L22.4 191.2c-6.9-6.2-9.6-15.9-6.4-24.6c4.4-11.9 9.7-23.3 15.8-34.3l4.7-8.1c6.6-11 14-21.4 22.1-31.2c5.9-7.2 15.7-9.6 24.5-6.8l55.7 17.7c13.4-10.3 28.2-18.9 44-25.4l12.5-57.1c2-9.1 9-16.3 18.2-17.8C227.3 1.2 241.5 0 256 0s28.7 1.2 42.5 3.5c9.2 1.5 16.2 8.7 18.2 17.8l12.5 57.1c15.8 6.5 30.6 15.1 44 25.4l55.7-17.7c8.8-2.8 18.6-.3 24.5 6.8c8.1 9.8 15.5 20.2 22.1 31.2l4.7 8.1c6.1 11 11.4 22.4 15.8 34.3zM256 336a80 80 0 1 0 0-160 80 80 0 1 0 0 160z"/>
                            </svg>
                        </div>
                        
                        <!-- Tab Navigation -->
                        <div class="tab-nav" role="tablist" aria-label="Business configuration options">
                            <button class="tab-btn active" 
                                role="tab"
                                aria-selected="true"
                                aria-controls="essential-tab"
                                id="essential-tab-btn"
                                onclick="openTab(event, 'essential-tab')">
                                <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                    <path d="M256 32c14.2 0 27.3 7.5 34.5 19.8l216 368c7.3 12.4 7.3 27.7 .2 40.1S486.3 480 472 480H40c-14.3 0-27.6-7.7-34.7-20.1s-7-27.8 .2-40.1l216-368C228.7 39.5 241.8 32 256 32zm0 128c-13.3 0-24 10.7-24 24V296c0 13.3 10.7 24 24 24s24-10.7 24-24V184c0-13.3-10.7-24-24-24zm32 224a32 32 0 1 0 -64 0 32 32 0 1 0 64 0z"/>
                                </svg>
                                Essential Setup
                                <?php if ($total_essential - $essential_completed > 0): ?>
                                    <span class="badge-urgent"><?= $total_essential - $essential_completed ?></span>
                                <?php endif; ?>
                            </button>
                            <button class="tab-btn" 
                                role="tab"
                                aria-selected="false"
                                aria-controls="business-tab"
                                id="business-tab-btn"
                                onclick="openTab(event, 'business-tab')">
                                <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                                    <path d="M48 0C21.5 0 0 21.5 0 48V464c0 26.5 21.5 48 48 48h96V432c0-26.5 21.5-48 48-48s48 21.5 48 48v80h96c26.5 0 48-21.5 48-48V48c0-26.5-21.5-48-48-48H48zM64 240c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V240zm112-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H176c-8.8 0-16-7.2-16-16V240c0-8.8 7.2-16 16-16zm80 16c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H272c-8.8 0-16-7.2-16-16V240zM80 96h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16zm80 16c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H176c-8.8 0-16-7.2-16-16V112zm112-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H272c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16z"/>
                                </svg>
                                Business Settings
                                <?php if ($total_important - $important_completed > 0): ?>
                                    <span class="badge-warning"><?= $total_important - $important_completed ?></span>
                                <?php endif; ?>
                            </button>
                            <button class="tab-btn" 
                                role="tab"
                                aria-selected="false"
                                aria-controls="technical-tab"
                                id="technical-tab-btn"
                                onclick="openTab(event, 'technical-tab')">
                                <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                    <path d="M78.6 5C69.1-2.4 55.6-1.5 47 7L7 47c-8.5 8.5-9.4 22-2.1 31.6l80 104c4.5 5.9 11.6 9.4 19 9.4h54.1l109 109c-14.7 29-10 65.4 14.3 89.6l112 112c12.5 12.5 32.8 12.5 45.3 0l64-64c12.5-12.5 12.5-32.8 0-45.3L400 288c-24.2-24.2-60.6-29-89.6-14.3l-109-109V110.1c0-7.5-3.5-14.5-9.4-19L78.6 5zM19.9 396.1C7.2 408.8 7.2 429.2 19.9 441.9l50.7 50.7c12.7 12.7 33.1 12.7 45.8 0l67.9-67.9c37.1-37.1 37.1-97.3 0-134.4c-37.1-37.1-97.3-37.1-134.4 0L19.9 396.1z"/>
                                </svg>
                                Technical Settings
                                <?php if ($total_optional - $optional_completed > 0): ?>
                                    <span class="badge-info"><?= $total_optional - $optional_completed ?></span>
                                <?php endif; ?>
                            </button>
                        </div>
                        
                        <!-- Tab Content -->
                        <div class="tab-content">
                            <!-- Essential Setup Tab -->
                            <div id="essential-tab" class="tab-panel active" role="tabpanel" aria-labelledby="essential-tab-btn">
                                <div class="quick-actions-grid">
                                    <?php foreach ($essential_settings as $setting): ?>
                                        <a href="<?= $setting['link'] ?>" class="quick-action <?= $setting['status'] == 'complete' ? 'success' : 'primary' ?>">
                                            <div class="action-icon">
                                                <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                    <?php if ($setting['status'] == 'complete'): ?>
                                                        <!-- Checkmark for completed -->
                                                        <path d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z"/>
                                                    <?php else: ?>
                                                        <!-- Gear for incomplete -->
                                                        <path d="M495.9 166.6c3.2 8.7 .5 18.4-6.4 24.6l-43.3 39.4c1.1 8.3 1.7 16.8 1.7 25.4s-.6 17.1-1.7 25.4l43.3 39.4c6.9 6.2 9.6 15.9 6.4 24.6c-4.4 11.9-9.7 23.3-15.8 34.3l-4.7 8.1c-6.6 11-14 21.4-22.1 31.2c-5.9 7.2-15.7 9.6-24.5 6.8l-55.7-17.7c-13.4 10.3-28.2 18.9-44 25.4l-12.5 57.1c-2 9.1-9 16.3-18.2 17.8c-13.8 2.3-28 3.5-42.5 3.5s-28.7-1.2-42.5-3.5c-9.2-1.5-16.2-8.7-18.2-17.8l-12.5-57.1c-15.8-6.5-30.6-15.1-44-25.4L83.1 425.9c-8.8 2.8-18.6 .3-24.5-6.8c-8.1-9.8-15.5-20.2-22.1-31.2l-4.7-8.1c-6.1-11-11.4-22.4-15.8-34.3c-3.2-8.7-.5-18.4 6.4-24.6l43.3-39.4C64.6 273.1 64 264.6 64 256s.6-17.1 1.7-25.4L22.4 191.2c-6.9-6.2-9.6-15.9-6.4-24.6c4.4-11.9 9.7-23.3 15.8-34.3l4.7-8.1c6.6-11 14-21.4 22.1-31.2c5.9-7.2 15.7-9.6 24.5-6.8l55.7 17.7c13.4-10.3 28.2-18.9 44-25.4l12.5-57.1c2-9.1 9-16.3 18.2-17.8C227.3 1.2 241.5 0 256 0s28.7 1.2 42.5 3.5c9.2 1.5 16.2 8.7 18.2 17.8l12.5 57.1c15.8 6.5 30.6 15.1 44 25.4l55.7-17.7c8.8-2.8 18.6-.3 24.5 6.8c8.1 9.8 15.5 20.2 22.1 31.2l4.7 8.1c6.1 11 11.4 22.4 15.8 34.3zM256 336a80 80 0 1 0 0-160 80 80 0 1 0 0 160z"/>
                                                    <?php endif; ?>
                                                </svg>
                                            </div>
                                            <div class="action-details">
                                                <h4><?= $setting['title'] ?></h4>
                                                <small class="text-muted"><?= $setting['description'] ?></small>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <!-- Business Settings Tab -->
                            <div id="business-tab" class="tab-panel" role="tabpanel" aria-labelledby="business-tab-btn">
                                <div class="quick-actions-grid">
                                    <?php foreach ($important_settings as $setting): ?>
                                        <a href="<?= $setting['link'] ?>" class="quick-action <?= $setting['status'] == 'complete' ? 'success' : 'info' ?>">
                                            <div class="action-icon">
                                                <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                    <?php if ($setting['status'] == 'complete'): ?>
                                                        <!-- Checkmark for completed -->
                                                        <path d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z"/>
                                                    <?php else: ?>
                                                        <!-- Building icon for business settings -->
                                                        <path d="M48 0C21.5 0 0 21.5 0 48V464c0 26.5 21.5 48 48 48h96V432c0-26.5 21.5-48 48-48s48 21.5 48 48v80h96c26.5 0 48-21.5 48-48V48c0-26.5-21.5-48-48-48H48zM64 240c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V240zm112-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H176c-8.8 0-16-7.2-16-16V240c0-8.8 7.2-16 16-16zm80 16c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H272c-8.8 0-16-7.2-16-16V240zM80 96h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16zm80 16c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H176c-8.8 0-16-7.2-16-16V112zm112-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H272c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16z"/>
                                                    <?php endif; ?>
                                                </svg>
                                            </div>
                                            <div class="action-details">
                                                <h4><?= $setting['title'] ?></h4>
                                                <small class="text-muted"><?= $setting['description'] ?></small>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <!-- Technical Settings Tab -->
                            <div id="technical-tab" class="tab-panel" role="tabpanel" aria-labelledby="technical-tab-btn">
                                <div class="quick-actions-grid">
                                    <?php foreach ($optional_settings as $setting): ?>
                                        <a href="<?= $setting['link'] ?>" class="quick-action <?= $setting['status'] == 'complete' ? 'success' : 'secondary' ?>">
                                            <div class="action-icon">
                                                <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                    <?php if ($setting['status'] == 'complete'): ?>
                                                        <!-- Checkmark for completed -->
                                                        <path d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z"/>
                                                    <?php else: ?>
                                                        <!-- Cog/tools icon for technical settings -->
                                                        <path d="M78.6 5C69.1-2.4 55.6-1.5 47 7L7 47c-8.5 8.5-9.4 22-2.1 31.6l80 104c4.5 5.9 11.6 9.4 19 9.4h54.1l109 109c-14.7 29-10 65.4 14.3 89.6l112 112c12.5 12.5 32.8 12.5 45.3 0l64-64c12.5-12.5 12.5-32.8 0-45.3L400 288c-24.2-24.2-60.6-29-89.6-14.3l-109-109V110.1c0-7.5-3.5-14.5-9.4-19L78.6 5zM19.9 396.1C7.2 408.8 7.2 429.2 19.9 441.9l50.7 50.7c12.7 12.7 33.1 12.7 45.8 0l67.9-67.9c37.1-37.1 37.1-97.3 0-134.4c-37.1-37.1-97.3-37.1-134.4 0L19.9 396.1z"/>
                                                    <?php endif; ?>
                                                </svg>
                                            </div>
                                            <div class="action-details">
                                                <h4><?= $setting['title'] ?></h4>
                                                <small class="text-muted"><?= $setting['description'] ?></small>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <!-- Business Settings Tab -->
                            <div id="business-tab" class="tab-panel" role="tabpanel" aria-labelledby="business-tab-btn">
                                <div class="quick-actions-grid">
                                    <?php foreach ($important_settings as $setting): ?>
                                        <a href="<?= $setting['link'] ?>" class="quick-action <?= $setting['status'] == 'complete' ? 'success' : 'info' ?>">
                                            <div class="action-icon">
                                                <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                    <?php if ($setting['status'] == 'complete'): ?>
                                                        <!-- Checkmark for completed -->
                                                        <path d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z"/>
                                                    <?php else: ?>
                                                        <!-- Building icon for business settings -->
                                                        <path d="M48 0C21.5 0 0 21.5 0 48V464c0 26.5 21.5 48 48 48h96V432c0-26.5 21.5-48 48-48s48 21.5 48 48v80h96c26.5 0 48-21.5 48-48V48c0-26.5-21.5-48-48-48H48zM64 240c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V240zm112-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H176c-8.8 0-16-7.2-16-16V240c0-8.8 7.2-16 16-16zm80 16c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H272c-8.8 0-16-7.2-16-16V240zM80 96h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16zm80 16c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H176c-8.8 0-16-7.2-16-16V112zm112-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H272c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16z"/>
                                                    <?php endif; ?>
                                                </svg>
                                            </div>
                                            <div class="action-details">
                                                <h4><?= $setting['title'] ?></h4>
                                                <small class="text-muted"><?= $setting['description'] ?></small>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <!-- Technical Settings Tab -->
                            <div id="technical-tab" class="tab-panel" role="tabpanel" aria-labelledby="technical-tab-btn">
                                <div class="quick-actions-grid">
                                    <?php foreach ($optional_settings as $setting): ?>
                                        <a href="<?= $setting['link'] ?>" class="quick-action <?= $setting['status'] == 'complete' ? 'success' : 'secondary' ?>">
                                            <div class="action-icon">
                                                <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                    <?php if ($setting['status'] == 'complete'): ?>
                                                        <!-- Checkmark for completed -->
                                                        <path d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z"/>
                                                    <?php else: ?>
                                                        <!-- Cog/tools icon for technical settings -->
                                                        <path d="M78.6 5C69.1-2.4 55.6-1.5 47 7L7 47c-8.5 8.5-9.4 22-2.1 31.6l80 104c4.5 5.9 11.6 9.4 19 9.4h54.1l109 109c-14.7 29-10 65.4 14.3 89.6l112 112c12.5 12.5 32.8 12.5 45.3 0l64-64c12.5-12.5 12.5-32.8 0-45.3L400 288c-24.2-24.2-60.6-29-89.6-14.3l-109-109V110.1c0-7.5-3.5-14.5-9.4-19L78.6 5zM19.9 396.1C7.2 408.8 7.2 429.2 19.9 441.9l50.7 50.7c12.7 12.7 33.1 12.7 45.8 0l67.9-67.9c37.1-37.1 37.1-97.3 0-134.4c-37.1-37.1-97.3-37.1-134.4 0L19.9 396.1z"/>
                                                    <?php endif; ?>
                                                </svg>
                                            </div>
                                            <div class="action-details">
                                                <h4><?= $setting['title'] ?></h4>
                                                <small class="text-muted"><?= $setting['description'] ?></small>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script>
// Working tab functionality from polls system
function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    
    // Hide all tab content
    tabcontent = document.getElementsByClassName("tab-panel");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].classList.remove("active");
    }
    
    // Remove active class from all tab buttons
    tablinks = document.getElementsByClassName("tab-btn");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].classList.remove("active");
        tablinks[i].setAttribute("aria-selected", "false");
    }
    
    // Show the selected tab content and mark button as active
    document.getElementById(tabName).classList.add("active");
    evt.currentTarget.classList.add("active");
    evt.currentTarget.setAttribute("aria-selected", "true");
}
</script>

<style>
/* Core dashboard styles */
.container-fluid {
    max-width: 1200px;
    margin: 0 auto;
}

/* Card components */
.app-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
    border: 1px solid #e3e6f0;
    width: 100%;
    overflow: hidden;
}

.app-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e3e6f0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: #f8f9fc;
}

.app-header h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #5a5c69;
    flex: 1;
}

.app-header .header-icon {
    color: #858796;
    width: 16px;
    height: 16px;
}

.app-header .badge {
    background: #5a5c69;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.app-body {
    padding: 1.5rem;
}

/* Quick Actions Grid */
.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.25rem;
    padding: 0.5rem 0;
}

.tab-panel .quick-actions-grid {
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 1.5rem;
}

.quick-action {
    display: flex;
    align-items: center;
    padding: 1rem;
    border: 1px solid #e3e6f0;
    border-radius: 6px;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease;
    background: white;
}

.quick-action:hover {
    text-decoration: none;
    color: inherit;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.quick-action.primary {
    border-color: #4e73df;
}

.quick-action.primary:hover {
    background: #f8f9fc;
    border-color: #3c63d8;
}

.quick-action.info {
    border-color: #36b9cc;
}

.quick-action.info:hover {
    background: #f8fdff;
    border-color: #2ba8bb;
}

.quick-action.secondary {
    border-color: #858796;
}

.quick-action.secondary:hover {
    background: #f8f9fa;
    border-color: #6e707e;
}

.quick-action.success {
    border-color: #1cc88a;
}

.quick-action.success:hover {
    background: #f3fcf8;
    border-color: #17a673;
}

.action-icon {
    margin-right: 1rem;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fc;
    border-radius: 50%;
    flex-shrink: 0;
}

.action-icon svg {
    width: 20px;
    height: 20px;
    fill: #858796;
}

.quick-action.primary .action-icon {
    background: #e7edff;
}

.quick-action.primary .action-icon svg {
    fill: #4e73df;
}

.quick-action.info .action-icon {
    background: #e7f9fc;
}

.quick-action.info .action-icon svg {
    fill: #36b9cc;
}

.quick-action.secondary .action-icon {
    background: #f1f1f2;
}

.quick-action.secondary .action-icon svg {
    fill: #858796;
}

.quick-action.success .action-icon {
    background: #e8f8f2;
}

.quick-action.success .action-icon svg {
    fill: #1cc88a;
}

.action-details h4 {
    margin: 0 0 0.25rem 0;
    font-size: 1rem;
    font-weight: 600;
    color: #5a5c69;
}

.action-details small {
    color: #858796;
    font-size: 0.875rem;
}

/* Tab functionality styles */
.tab-nav {
    display: flex;
    gap: 0.5rem;
    padding: 1rem 1.5rem 0;
    border-bottom: 1px solid #e3e6f0;
    background: #f8f9fc;
}

.tab-btn {
    background: transparent;
    border: 1px solid #e3e6f0;
    color: #858796;
    border-radius: 6px 6px 0 0;
    padding: 0.75rem 1rem;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
}

.tab-btn:hover {
    background: #f8f9fc;
    border-color: #d1d3e2;
    color: #5a5c69;
}

.tab-btn.active {
    background: white;
    border-color: #e3e6f0;
    border-bottom-color: white;
    color: #5a5c69;
    position: relative;
    z-index: 1;
}

.tab-btn svg {
    width: 14px;
    height: 14px;
    fill: currentColor;
}

.tab-content {
    padding: 1.5rem;
}

.tab-panel {
    display: none;
}

.tab-panel.active {
    display: block;
}

/* Badge styles */
.badge-urgent {
    background: #e74a3b;
    color: white;
    border-radius: 10px;
    padding: 0.2rem 0.4rem;
    font-size: 0.7rem;
    font-weight: 600;
}

.badge-warning {
    background: #f6c23e;
    color: #5a5c69;
    border-radius: 10px;
    padding: 0.2rem 0.4rem;
    font-size: 0.7rem;
    font-weight: 600;
}

.badge-info {
    background: #36b9cc;
    color: white;
    border-radius: 10px;
    padding: 0.2rem 0.4rem;
    font-size: 0.7rem;
    font-weight: 600;
}

/* Responsive design */
@media (max-width: 768px) {
    .quick-actions-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .tab-panel .quick-actions-grid {
        grid-template-columns: 1fr;
    }
    
    .tab-nav {
        flex-wrap: wrap;
    }
}
</style>

<?php echo template_admin_footer(); ?>
                                                    </span>
                                                    <?php if ($item['completed']): ?>
                                                        <span class="badge badge-success">Complete</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger">Required</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text"><?= $item['description'] ?></p>
                                                    <a href="<?= $item['file'] ?>" class="btn btn-primary btn-block">
                                                        <?= $item['completed'] ? 'Edit Settings' : 'Configure Now' ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Business Settings Tab -->
                        <div class="tab-pane fade" id="business" role="tabpanel">
                            <div class="tab-header mb-4">
                                <h5 class="text-warning">
                                    <i class="fas fa-building"></i> Business Configuration
                                </h5>
                                <p class="text-muted">Configure your business operations and user management</p>
                            </div>
                            
                            <div class="row">
                                <?php foreach ($business_setup_items as $key => $item): ?>
                                    <?php if ($item['category'] === 'important'): ?>
                                        <div class="col-md-6 col-lg-4 mb-4">
                                            <div class="card h-100 <?= $item['completed'] ? 'border-success' : 'border-warning' ?>">
                                                <div class="professional-card-header">
                                                    <span class="h6 mb-0">
                                                        <i class="fas fa-<?= $item['icon'] ?>"></i> <?= $item['title'] ?>
                                                    </span>
                                                    <?php if ($item['completed']): ?>
                                                        <span class="badge badge-success">Complete</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-warning">Recommended</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text"><?= $item['description'] ?></p>
                                                    <a href="<?= $item['file'] ?>" class="btn btn-warning btn-block">
                                                        <?= $item['completed'] ? 'Edit Settings' : 'Configure' ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Features & Apps Tab -->
                        <div class="tab-pane fade" id="features" role="tabpanel">
                            <div class="tab-header mb-4">
                                <h5 class="text-success">
                                    <i class="fas fa-star"></i> Features & Applications
                                </h5>
                                <p class="text-muted">Enable and configure optional features for your business</p>
                            </div>
                            
                            <div class="row">
                                <?php foreach ($business_setup_items as $key => $item): ?>
                                    <?php if ($item['category'] === 'optional'): ?>
                                        <div class="col-md-6 col-lg-4 mb-4">
                                            <div class="card h-100 <?= $item['completed'] ? 'border-success' : 'border-light' ?>">
                                                <div class="professional-card-header">
                                                    <span class="h6 mb-0">
                                                        <i class="fas fa-<?= $item['icon'] ?>"></i> <?= $item['title'] ?>
                                                    </span>
                                                    <?php if ($item['completed']): ?>
                                                        <span class="badge badge-success">Enabled</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-light">Optional</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text"><?= $item['description'] ?></p>
                                                    <a href="<?= $item['file'] ?>" class="btn btn-primary btn-block">
                                                        <?= $item['completed'] ? 'Configure' : 'Enable Feature' ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Advanced Tab -->
                        <div class="tab-pane fade" id="advanced" role="tabpanel">
                            <div class="tab-header mb-4">
                                <h5 class="text-secondary">
                                    <i class="fas fa-cogs"></i> Advanced Configuration
                                </h5>
                                <p class="text-muted">System-level settings for developers and advanced users</p>
                            </div>
                            
                            <div class="row">
                                <?php foreach ($business_setup_items as $key => $item): ?>
                                    <?php if ($item['category'] === 'advanced'): ?>
                                        <div class="col-md-6 col-lg-4 mb-4">
                                            <div class="card h-100 border-secondary">
                                                <div class="professional-card-header">
                                                    <span class="h6 mb-0">
                                                        <i class="fas fa-<?= $item['icon'] ?>"></i> <?= $item['title'] ?>
                                                    </span>
                                                    <span class="badge badge-secondary">Advanced</span>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text"><?= $item['description'] ?></p>
                                                    <a href="<?= $item['file'] ?>" class="btn btn-secondary btn-block">
                                                        Configure
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                
                                <!-- Additional Advanced Settings -->
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 border-secondary">
                                        <div class="professional-card-header">
                                            <span class="h6 mb-0">
                                                <i class="fas fa-database"></i> Database Settings
                                            </span>
                                            <span class="badge badge-secondary">Advanced</span>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text">Database configuration and migration tools</p>
                                            <a href="database_settings.php" class="btn btn-secondary btn-block">Configure</a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 border-secondary">
                                        <div class="professional-card-header">
                                            <span class="h6 mb-0">
                                                <i class="fas fa-code"></i> Developer Tools
                                            </span>
                                            <span class="badge badge-secondary">Advanced</span>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text">Development tools and debugging options</p>
                                            <a href="dev_settings.php" class="btn btn-secondary btn-block">Configure</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
// Tab switching functionality
$(document).ready(function() {
    $('.tab-btn').click(function() {
        var target = $(this).data('tab');
        
        // Update tab buttons
        $('.tab-btn').removeClass('active');
        $(this).addClass('active');
        
        // Update tab content
        $('.tab-content').removeClass('active');
        $('#' + target).addClass('active');
    });
});

// Progress circle animation
function animateProgressCircle() {
    const progressElement = document.querySelector('[data-progress]');
    if (progressElement) {
        const progress = progressElement.dataset.progress;
        // Add CSS animation for progress circle here
    }
}

// Call animation on page load
$(document).ready(function() {
    animateProgressCircle();
});
</script>

<style>
/* Custom styles for the business dashboard */
.progress-circle-container {
    position: relative;
    width: 150px;
    height: 150px;
    margin: 0 auto;
}

.progress-circle-large {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background: conic-gradient(#28a745 calc(var(--progress, 0) * 1%), #e9ecef 0);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.progress-circle-large::before {
    content: '';
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: white;
    position: absolute;
}

.progress-text {
    position: relative;
    z-index: 2;
    text-align: center;
}

.health-indicator {
    font-size: 0.8em;
}

.setting-card {
    transition: transform 0.2s;
}

.setting-card:hover {
    transform: translateY(-2px);
}

.setting-card.completed {
    border-color: #28a745 !important;
}

.setting-card.pending {
    border-color: #ffc107 !important;
}

.status-badge.complete {
    background-color: #28a745;
    color: white;
}

.status-badge.pending {
    background-color: #ffc107;
    color: #212529;
}

/* New component styles following polls dashboard pattern */
.app-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
    border: 1px solid #e3e6f0;
    width: 100%;
    overflow: hidden;
}

.app-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e3e6f0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: #f8f9fc;
}

.app-header h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #5a5c69;
    flex: 1;
}

.app-header .header-icon {
    color: #858796;
    width: 16px;
    height: 16px;
}

.app-header .badge {
    background: #5a5c69;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.app-body {
    padding: 1.5rem;
}

/* Ensure proper row/column behavior */
.row {
    margin-left: -15px;
    margin-right: -15px;
}

.row > [class*="col-"] {
    padding-left: 15px;
    padding-right: 15px;
}

/* Fix any potential width issues */
.container-fluid {
    width: 100%;
    padding-right: 15px;
    padding-left: 15px;
    margin-right: auto;
    margin-left: auto;
}
    height: 16px;
}

.app-header .badge {
    background: #5a5c69;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.app-body {
    padding: 1.5rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.stat-item {
    text-align: center;
    padding: 1rem;
    border: 1px solid #e3e6f0;
    border-radius: 6px;
    background: #f8f9fc;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #5a5c69;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.85rem;
    color: #858796;
    margin-bottom: 0.5rem;
}

.stat-progress {
    width: 100%;
    height: 4px;
    background: #e3e6f0;
    border-radius: 2px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: #4e73df;
    transition: width 0.3s ease;
}

.progress-bar.complete {
    background: #1cc88a;
}

.progress-bar.warning {
    background: #f6c23e;
}

.system-health-compact {
    margin-top: 1rem;
}

.health-indicators {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.health-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 16px;
    font-size: 0.75rem;
    font-weight: 500;
    border: 1px solid;
}

.health-badge.good {
    background: #d4edda;
    color: #155724;
    border-color: #c3e6cb;
}

.health-badge.warning {
    background: #fff3cd;
    color: #856404;
    border-color: #ffeaa7;
}

.health-badge.error {
    background: #f8d7da;
    color: #721c24;
    border-color: #f5c6cb;
}

.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.25rem;
    padding: 0.5rem 0;
}

/* Special handling for Business Configuration tabs */
.tab-panel .quick-actions-grid {
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 1.5rem;
}

@media (max-width: 768px) {
    .quick-actions-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .tab-panel .quick-actions-grid {
        grid-template-columns: 1fr;
    }
}

.quick-action {
    display: flex;
    align-items: center;
    padding: 1rem;
    border: 1px solid #e3e6f0;
    border-radius: 6px;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease;
    background: white;
}

.quick-action:hover {
    text-decoration: none;
    color: inherit;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.quick-action.primary {
    border-color: #4e73df;
}

.quick-action.primary:hover {
    background: #f8f9fc;
    border-color: #3c63d8;
}

.quick-action.info {
    border-color: #36b9cc;
}

.quick-action.info:hover {
    background: #f8fdff;
    border-color: #2ba8bb;
}

.quick-action.secondary {
    border-color: #858796;
}

.quick-action.secondary:hover {
    background: #f8f9fa;
    border-color: #6e707e;
}

.quick-action.success {
    border-color: #1cc88a;
}

.quick-action.success:hover {
    background: #f3fcf8;
    border-color: #17a673;
}

.action-icon {
    margin-right: 1rem;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fc;
    border-radius: 50%;
    flex-shrink: 0;
}

.action-icon svg {
    width: 20px;
    height: 20px;
    fill: #858796;
}

.quick-action.primary .action-icon {
    background: #e7edff;
}

.quick-action.primary .action-icon svg {
    fill: #4e73df;
}

.quick-action.info .action-icon {
    background: #e7f9fc;
}

.quick-action.info .action-icon svg {
    fill: #36b9cc;
}

.quick-action.secondary .action-icon {
    background: #f1f1f2;
}

.quick-action.success .action-icon {
    background: #e8f5f0;
}

.quick-action.success .action-icon svg {
    fill: #1cc88a;
}

.action-details h4 {
    margin: 0 0 0.25rem 0;
    font-size: 1rem;
    font-weight: 600;
    color: #5a5c69;
}

.action-details small {
    font-size: 0.8rem;
    color: #858796;
}

/* Tab navigation following working polls pattern */
.tab-nav {
    display: flex;
    background: #f8f9fc;
    border-radius: 6px;
    margin-bottom: 1.5rem;
    padding: 0.25rem;
    border: 1px solid #e3e6f0;
}

.tab-btn {
    flex: 1;
    padding: 0.75rem 1rem;
    border: none;
    background: transparent;
    color: #858796;
    font-size: 0.9rem;
    font-weight: 500;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    text-align: center;
}

.tab-btn:hover {
    background: rgba(78, 115, 223, 0.1);
    color: #4e73df;
}

.tab-btn.active {
    background: white;
    color: #5a5c69;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.tab-btn svg {
    width: 14px;
    height: 14px;
    fill: currentColor;
}

.badge-urgent {
    background: #e74a3b;
    color: white;
    border-radius: 10px;
    padding: 0.2rem 0.4rem;
    font-size: 0.7rem;
    font-weight: 600;
}

.badge-warning {
    background: #f6c23e;
    color: #5a5c69;
    border-radius: 10px;
    padding: 0.2rem 0.4rem;
    font-size: 0.7rem;
    font-weight: 600;
}

/* Tab functionality styles */
.tab-nav {
    display: flex;
    gap: 0.5rem;
    padding: 1rem 1.5rem 0;
    border-bottom: 1px solid #e3e6f0;
    background: #f8f9fc;
}

.tab-btn {
    background: transparent;
    border: 1px solid #e3e6f0;
    color: #858796;
    border-radius: 6px 6px 0 0;
    padding: 0.75rem 1rem;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
}

.tab-btn:hover {
    background: #f8f9fc;
    border-color: #d1d3e2;
    color: #5a5c69;
}

.tab-btn.active {
    background: white;
    border-color: #e3e6f0;
    border-bottom-color: white;
    color: #5a5c69;
    position: relative;
    z-index: 1;
}

.tab-btn svg {
    width: 14px;
    height: 14px;
    fill: currentColor;
}

.tab-content {
    padding: 1.5rem;
}

.tab-panel {
    display: none;
}

.tab-panel.active {
    display: block;
}

.badge-info {
    background: #36b9cc;
    color: white;
    border-radius: 10px;
    padding: 0.2rem 0.4rem;
    font-size: 0.7rem;
    font-weight: 600;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}
</style>

<script>
// Working tab functionality from polls system
function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    
    // Hide all tab content
    tabcontent = document.getElementsByClassName("tab-panel");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].classList.remove("active");
    }
    
    // Remove active class from all tab buttons
    tablinks = document.getElementsByClassName("tab-btn");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].classList.remove("active");
        tablinks[i].setAttribute("aria-selected", "false");
    }
    
    // Show the selected tab content and mark button as active
    document.getElementById(tabName).classList.add("active");
    evt.currentTarget.classList.add("active");
    evt.currentTarget.setAttribute("aria-selected", "true");
}
}

// Legacy jQuery tab support (fallback)
$(document).ready(function() {
    background-color: #28a745;
    color: white;
}

.status-badge.pending {
    background-color: #ffc107;
    color: #212529;
}

.status-badge.optional {
    background-color: #6c757d;
    color: white;
}

.status-badge.advanced {
    background-color: #17a2b8;
    color: white;
}

.tab-btn {
    border: 1px solid #dee2e6;
    background: white;
    padding: 0.75rem 1.5rem;
    margin-right: 0.5rem;
    border-radius: 0.375rem 0.375rem 0 0;
    cursor: pointer;
    transition: all 0.2s;
}

.tab-btn.active {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.tab-btn:hover:not(.active) {
    background: #f8f9fa;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.badge {
    font-size: 0.7em;
}
</style>

<?php echo template_admin_footer(); ?>