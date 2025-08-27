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
    'contact_info' => [
        'title' => 'Contact Settings',
        'description' => 'Contact forms, business address, phone',
        'file' => 'forms/business_contact_form.php',
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
        'description' => 'Database-driven blog settings management',
        'file' => 'forms/blog_identity_form.php',
        'icon' => 'edit',
        'priority' => 7,
        'category' => 'optional'
    ],
    'blog_display' => [
        'title' => 'Blog Display',
        'description' => 'Layout, pagination & visual presentation',
        'file' => 'forms/blog_display_form.php',
        'icon' => 'layout-text-sidebar',
        'priority' => 7.1,
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

// Developer-only settings (shown separately)
$developer_settings = [];
if ($_SESSION['admin_role'] === 'Developer') {
    $developer_settings = [
        'dev_config' => [
            'title' => 'Developer Configuration',
            'description' => 'Core system files, database config, environment settings',
            'file' => 'dev_settings.php',
            'icon' => 'wrench',
            'priority' => 9,
            'category' => 'developer'
        ]
    ];
}

// Include shared matrix + helpers
require_once PROJECT_ROOT . '/private/settings_completion_matrix.php';

// Calculate completion metrics
$essential_completed = 0;
$important_completed = 0;
$optional_completed = 0;
$total_essential = 0;
$total_important = 0;
$total_optional = 0;

// Compute completion using matrix; feature-flagged disabled items excluded from totals
$flags_local = $FEATURE_FLAGS ?? [];
foreach ($business_setup_items as $key => $item) {
    $def = $SETTINGS_COMPLETION_MATRIX[$key] ?? null;
    $is_flag_disabled = $def && isset($def['flag']) && function_exists('featureEnabled') && !featureEnabled($def['flag'], $flags_local);
    $is_complete = false;
    if ($def && !$is_flag_disabled) {
        $is_complete = setting_is_complete($pdo, $key, $def, $flags_local);
    }
    // Categorize only if not disabled by flag
    if (!$is_flag_disabled) {
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
    }
    $business_setup_items[$key]['completed'] = $is_complete;
    $business_setup_items[$key]['excluded'] = $is_flag_disabled;
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
// Totals exclude feature-flag disabled modules
$total_items = count(array_filter($business_setup_items, function($i){ return empty($i['excluded']); }));
$completed_items = 0;
foreach ($business_setup_items as $i) { if (empty($i['excluded']) && !empty($i['completed'])) $completed_items++; }
$completion_percentage = $total_items > 0 ? round(($completed_items / $total_items) * 100) : 0;

// Content settings quick links (not part of completion matrix; convenience only)
$content_settings_actions = [
    [
        'title' => 'Home Content',
        'description' => 'Hero, about + homepage messaging',
        'link' => 'content_settings.php#home',
        'icon' => 'house',
        'status' => 'info'
    ],
    [
        'title' => 'Sections Content',
        'description' => 'Services & features blocks',
        'link' => 'content_settings.php#sections',
        'icon' => 'grid',
        'status' => 'secondary'
    ],
    [
        'title' => 'Media Assets',
        'description' => 'Images & videos management',
        'link' => 'content_settings.php#media',
        'icon' => 'images',
        'status' => 'secondary'
    ],
    [
        'title' => 'Pages Content',
        'description' => 'Static page copy & meta',
        'link' => 'content_settings.php#pages',
        'icon' => 'file-earmark-text',
        'status' => 'primary'
    ]
];

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

<div class="content-title" id="main-dashboard" role="banner" aria-label="Settings Dashboard Header">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                <path d="M495.9 166.6c3.2 8.7 .5 18.4-6.4 24.6l-43.3 39.4c1.1 8.3 1.7 16.8 1.7 25.4s-.6 17.1-1.7 25.4l43.3 39.4c6.9 6.2 9.6 15.9 6.4 24.6c-4.4 11.9-9.7 23.3-15.8 34.3l-4.7 8.1c-6.6 11-14 21.4-22.1 31.2c-5.9 7.2-15.7 9.6-24.5 6.8l-55.7-17.7c-13.4 10.3-28.2 18.9-44 25.4l-12.5 57.1c-2 9.1-9 16.3-18.2 17.8c-13.8 2.3-28 3.5-42.5 3.5s-28.7-1.2-42.5-3.5c-9.2-1.5-16.2-8.7-18.2-17.8l-12.5-57.1c-15.8-6.5-30.6-15.1-44-25.4L83.1 425.9c-8.8 2.8-18.6 .3-24.5-6.8c-8.1-9.8-15.5-20.2-22.1-31.2l-4.7-8.1c-6.1-11-11.4-22.4-15.8-34.3c-3.2-8.7-.5-18.4 6.4-24.6l43.3-39.4C64.6 273.1 64 264.6 64 256s.6-17.1 1.7-25.4L22.4 191.2c-6.9-6.2-9.6-15.9-6.4-24.6c4.4-11.9 9.7-23.3 15.8-34.3l4.7-8.1c6.6-11 14-21.4 22.1-31.2c5.9-7.2 15.7-9.6 24.5-6.8l55.7 17.7c13.4-10.3 28.2-18.9 44-25.4l12.5-57.1c2-9.1 9-16.3 18.2-17.8C227.3 1.2 241.5 0 256 0s28.7 1.2 42.5 3.5c9.2 1.5 16.2 8.7 18.2 17.8l12.5 57.1c15.8 6.5 30.6 15.1 44 25.4l55.7-17.7c8.8-2.8 18.6-.3 24.5 6.8c8.1 9.8 15.5 20.2 22.1 31.2l4.7 8.1c6.1 11 11.4 22.4 15.8 34.3zM256 336a80 80 0 1 0 0-160 80 80 0 1 0 0 160z"/>
            </svg>
        </div>
        <div class="txt">
            <h2>Business Configuration Center</h2>
            <p>Intuitive business setup and configuration management dashboard.</p>
        </div>
    </div>
</div>

<!-- Top Row: Quick Actions, Action Items, and Statistics -->
<div class="dashboard-apps">
    <!-- Quick Actions -->
    <div class="app-card" role="region" aria-labelledby="quick-actions-title">
        <div class="app-header events-header" role="banner" aria-labelledby="quick-actions-title">
            <h3 id="quick-actions-title">Quick Actions</h3>
            <i class="bi bi-lightning-charge header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="Business configuration actions">Setup</span>
        </div>
        <div class="app-body">
            <div class="quick-actions">
                <a href="new_installation.php" class="quick-action primary">
                    <div class="action-icon">
                        <i class="bi bi-shield-check" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Setup Wizard</h4>
                        <small class="text-muted">New installation guide</small>
                    </div>
                </a>
                <a href="settings_help.php" class="quick-action info">
                    <div class="action-icon">
                        <i class="bi bi-question-circle" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Help Guide</h4>
                        <small class="text-muted">Documentation & support</small>
                    </div>
                </a>
                <a href="../../index.php" class="quick-action secondary">
                    <div class="action-icon">
                        <i class="bi bi-eye" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Preview Website</h4>
                        <small class="text-muted">View public site</small>
                    </div>
                </a>
                <a href="../shop_system/shop_dash.php" class="quick-action success">
                    <div class="action-icon">
                        <i class="bi bi-cart" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Shop Dashboard</h4>
                        <small class="text-muted">Manage store</small>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Action Items -->
    <div class="app-card" role="region" aria-labelledby="action-items-title">
        <div class="app-header blog-header" role="banner" aria-labelledby="action-items-title">
            <h3 id="action-items-title">Action Items</h3>
            <i class="bi bi-exclamation-triangle header-icon" aria-hidden="true"></i>
            <?php if ($total_essential - $essential_completed > 0): ?>
                <span class="badge" aria-label="<?= $total_essential - $essential_completed ?> critical items"><?= $total_essential - $essential_completed ?> urgent</span>
            <?php else: ?>
                <span class="badge" aria-label="All essential items complete">All set</span>
            <?php endif; ?>
        </div>
        <div class="app-body">
            <?php if ($total_essential - $essential_completed > 0 || $total_important - $important_completed > 0): ?>
                <div class="action-items">
                    <?php if ($total_essential - $essential_completed > 0): ?>
                        <a href="#essential-tab" class="action-item danger" onclick="document.getElementById('essential-tab-btn').click(); return false;">
                            <div class="action-icon">
                                <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Essential Setup Required</h4>
                                <small class="text-muted">Critical settings need configuration</small>
                            </div>
                            <div class="action-count"><?= $total_essential - $essential_completed ?></div>
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($total_important - $important_completed > 0): ?>
                        <a href="#business-tab" class="action-item warning" onclick="document.getElementById('business-tab-btn').click(); return false;">
                            <div class="action-icon">
                                <i class="bi bi-building" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Business Settings Pending</h4>
                                <small class="text-muted">Business settings need attention</small>
                            </div>
                            <div class="action-count"><?= $total_important - $important_completed ?></div>
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="no-action-items">
                    <i class="bi bi-check-circle" aria-hidden="true"></i>
                    <p>All essential and business settings configured!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Statistics -->
    <div class="app-card" role="region" aria-labelledby="stats-title">
        <div class="app-header accounts-header" role="banner" aria-labelledby="stats-title">
            <h3 id="stats-title">Setup Progress</h3>
            <i class="bi bi-bar-chart header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="<?= $completion_percentage ?>% complete"><?= $completion_percentage ?>%</span>
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
                <div class="stat-item">
                    <div class="stat-value"><?= $completion_percentage ?>%</div>
                    <div class="stat-label">Overall Complete</div>
                    <div class="stat-progress">
                        <div class="progress-bar" style="width: <?= $completion_percentage ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Business Configuration Section - Full Width -->
<div class="business-config-section">
    <div class="app-card full-width-card" role="region" aria-labelledby="config-tabs-title">
        <div class="app-header" role="banner" aria-labelledby="config-tabs-title">
            <h3 id="config-tabs-title">Business Configuration</h3>
            <i class="bi bi-gear-wide-connected header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="Business configuration tabs">Configure</span>
        </div>
        
        <!-- Tab Navigation -->
        <div class="tab-nav" role="tablist" aria-label="Business configuration options">
            <button class="tab-btn active" 
                role="tab"
                aria-selected="true"
                aria-controls="essential-tab"
                id="essential-tab-btn"
                onclick="openTab(event, 'essential-tab')">
                <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
                Essential Setup
                <?php if ($total_essential - $essential_completed > 0): ?>
                    <span class="tab-badge urgent"><?= $total_essential - $essential_completed ?></span>
                <?php endif; ?>
            </button>
            <button class="tab-btn" 
                role="tab"
                aria-selected="false"
                aria-controls="business-tab"
                id="business-tab-btn"
                onclick="openTab(event, 'business-tab')">
                <i class="bi bi-building" aria-hidden="true"></i>
                Business Settings
                <?php if ($total_important - $important_completed > 0): ?>
                    <span class="tab-badge warning"><?= $total_important - $important_completed ?></span>
                <?php endif; ?>
            </button>
            <button class="tab-btn" 
                role="tab"
                aria-selected="false"
                aria-controls="technical-tab"
                id="technical-tab-btn"
                onclick="openTab(event, 'technical-tab')">
                <i class="bi bi-tools" aria-hidden="true"></i>
                Technical Settings
                <?php if ($total_optional - $optional_completed > 0): ?>
                    <span class="tab-badge info"><?= $total_optional - $optional_completed ?></span>
                <?php endif; ?>
            </button>
            <button class="tab-btn" 
                role="tab"
                aria-selected="false"
                aria-controls="content-tab"
                id="content-tab-btn"
                onclick="openTab(event, 'content-tab')">
                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                Content Settings
            </button>
        </div>

        <!-- Tab Content -->
        <!-- Essential Setup Tab -->
        <div id="essential-tab" class="tab-content active" role="tabpanel" aria-labelledby="essential-tab-btn">
                <div class="quick-actions">
                    <?php foreach ($essential_settings as $setting): ?>
                        <a href="<?= $setting['link'] ?>" class="quick-action <?= $setting['status'] == 'complete' ? 'success' : 'primary' ?>">
                            <div class="action-icon">
                                <?php if ($setting['status'] == 'complete'): ?>
                                    <i class="bi bi-check-circle" aria-hidden="true"></i>
                                <?php else: ?>
                                    <i class="bi bi-gear" aria-hidden="true"></i>
                                <?php endif; ?>
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
            <div id="business-tab" class="tab-content" role="tabpanel" aria-labelledby="business-tab-btn">
                <div class="quick-actions">
                    <?php foreach ($important_settings as $setting): ?>
                        <a href="<?= $setting['link'] ?>" class="quick-action <?= $setting['status'] == 'complete' ? 'success' : 'info' ?>">
                            <div class="action-icon">
                                <?php if ($setting['status'] == 'complete'): ?>
                                    <i class="bi bi-check-circle" aria-hidden="true"></i>
                                <?php else: ?>
                                    <i class="bi bi-building" aria-hidden="true"></i>
                                <?php endif; ?>
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
            <div id="technical-tab" class="tab-content" role="tabpanel" aria-labelledby="technical-tab-btn">
                <div class="quick-actions">
                    <?php foreach ($optional_settings as $setting): ?>
                        <a href="<?= $setting['link'] ?>" class="quick-action <?= $setting['status'] == 'complete' ? 'success' : 'secondary' ?>">
                            <div class="action-icon">
                                <?php if ($setting['status'] == 'complete'): ?>
                                    <i class="bi bi-check-circle" aria-hidden="true"></i>
                                <?php else: ?>
                                    <i class="bi bi-tools" aria-hidden="true"></i>
                                <?php endif; ?>
                            </div>
                            <div class="action-details">
                                <h4><?= $setting['title'] ?></h4>
                                <small class="text-muted"><?= $setting['description'] ?></small>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Content Settings Tab -->
            <div id="content-tab" class="tab-content" role="tabpanel" aria-labelledby="content-tab-btn">
                <div class="quick-actions">
                    <?php foreach ($content_settings_actions as $c): ?>
                        <a href="<?= $c['link'] ?>" class="quick-action <?= $c['status'] ?>">
                            <div class="action-icon">
                                <i class="bi bi-<?= $c['icon'] ?>" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4><?= htmlspecialchars($c['title']) ?></h4>
                                <small class="text-muted"><?= htmlspecialchars($c['description']) ?></small>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
    </div>
</div>
<script>
// Tab functionality for Business Configuration tabs
function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    
    // Hide all tab content
    tabcontent = document.getElementsByClassName("tab-content");
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

<?php echo template_admin_footer(); ?>
                                      