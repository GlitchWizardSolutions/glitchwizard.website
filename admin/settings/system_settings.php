<?php
/* 
 * System Settings Management
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: system_settings.php
 * LOCATION: /public_html/admin/settings/
 * PURPOSE: Core system settings management interface
 * DETAILED DESCRIPTION:
 * This file provides a centralized interface for managing core system settings
 * and configurations. It allows administrators to configure essential system
 * parameters, performance settings, security options, and other fundamental
 * aspects of the application.
 * REQUIRED FILES: 
 * - /public_html/assets/includes/main.php
 * - /public_html/assets/includes/settings/system_settings_config.php
 * - /private/gws-universal-config.php
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: [IN DEVELOPMENT]
 * 
 * FEATURES:
 * - Core system configuration
 * - Performance optimization settings
 * - Security configuration
 * - System maintenance options
 * - Error logging and reporting
 */

// Include the configuration file and functions
require_once __DIR__ . '/../assets/includes/main.php';

// Page title
$page_title = 'System Settings';
?>

<?= template_admin_header($page_title, 'settings', 'system') ?>

<div class="content-title">
    <div class="title">
        <div class="icon">
          <i class="bi bi-gear"></i>
        </div>
        <div class="txt">
            <h2>System Settings</h2>
            <p>Manage system settings and configurations.</p>
        </div>
    </div>
</div>
<br>

    <?php

// Process form submissions
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted_tab = $_POST['settings_tab'] ?? 'general';
    $settings_file = __DIR__ . '/tabs/system/data/' . $submitted_tab . '_settings.php';
    
    // Create data directory if it doesn't exist
    $data_dir = __DIR__ . '/tabs/system/data';
    if (!file_exists($data_dir)) {
        if (!mkdir($data_dir, 0755, true)) {
            $message = 'Error creating data directory.';
            $message_type = 'danger';
            error_log("Failed to create directory: $data_dir");
        }
    }
    
    // Save the settings
    $settings_data = $_POST['settings'] ?? [];
    
    // Get the list of expected checkbox fields based on the tab
    $checkbox_fields = [];
    switch ($submitted_tab) {
        case 'maintenance':
            $checkbox_fields = [
                'auto_backup',
                'backup_include_files',
                'backup_include_database',
                'backup_compression',
                'error_log_enabled',
                'access_log_enabled',
                'system_log_enabled',
                'cleanup_temp_files',
                'auto_update_check'
            ];
            break;
        case 'general':
            $checkbox_fields = [
                'maintenance_mode',
                'debug_mode'
            ];
            break;
        case 'security':
            $checkbox_fields = [
                'force_ssl',
                'password_requires_special',
                'password_requires_number',
                'password_requires_uppercase',
                'two_factor_auth',
                'csrf_protection',
                'xss_protection',
                'secure_headers'
            ];
            break;
        case 'performance':
            $checkbox_fields = [
                'enable_caching',
                'minify_html',
                'minify_css',
                'minify_js',
                'gzip_compression',
                'database_optimization',
                'enable_cdn',
                'lazy_loading'
            ];
            break;
    }
    
    // Set unchecked checkboxes to false
    foreach ($checkbox_fields as $field) {
        if (!isset($settings_data[$field])) {
            $settings_data[$field] = false;
        }
    }
    
    // Normalize "on" values to true for checkboxes
    foreach ($settings_data as $key => $value) {
        if ($value === 'on') {
            $settings_data[$key] = true;
        }
    }
    
    if (!empty($settings_data)) {
        $settings_content = "<?php\nreturn " . var_export($settings_data, true) . ";\n";
        error_log("Attempting to save settings to: $settings_file");
        error_log("Settings data: " . print_r($settings_data, true));
        
        if (file_put_contents($settings_file, $settings_content)) {
            $message = 'Settings saved successfully.';
            $message_type = 'success';
            error_log("Settings saved successfully to: $settings_file");
        } else {
            $message = 'Error saving settings. Check file permissions.';
            $message_type = 'danger';
            error_log("Failed to save settings to: $settings_file");
        }
    } else {
        $message = 'No settings data received.';
        $message_type = 'warning';
        error_log("No settings data in POST request");
    }
    
    // Set active tab to the one that was submitted
    $active_tab = $submitted_tab;
} else {
    // Get the current active tab, default to 'general' if none specified
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
}

// Define the tabs
$tabs = [
    'general' => ['title' => 'General', 'icon' => 'bi bi-gear'],
    'security' => ['title' => 'Security', 'icon' => 'bi bi-shield-check'],
    'performance' => ['title' => 'Performance', 'icon' => 'bi bi-speedometer2'],
    'maintenance' => ['title' => 'Maintenance', 'icon' => 'bi bi-tools']
];

// Ensure the active tab exists in our tabs array
if (!array_key_exists($active_tab, $tabs)) {
    $active_tab = 'general';
}

?>
<div class="settings-container">
    <?php if ($message): ?>
        <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
 
    <div class="settings-tabs">
        <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
            <?php foreach ($tabs as $tab_id => $tab): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link nav-tab <?= $tab_id === $active_tab ? 'active' : '' ?>"
                       id="tab-<?= $tab_id ?>"
                       data-bs-toggle="tab"
                       data-bs-target="#panel-<?= $tab_id ?>"
                       type="button"
                       role="tab"
                       aria-controls="panel-<?= $tab_id ?>"
                       aria-selected="<?= $tab_id === $active_tab ? 'true' : 'false' ?>">
                        <i class="<?= htmlspecialchars($tab['icon']) ?>" aria-hidden="true"></i>
                        <?= htmlspecialchars($tab['title']) ?>
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="tab-content bg-white p-3" id="settingsTabsContent">
            <?php foreach ($tabs as $tab_id => $tab): ?>
                <div class="tab-pane fade <?= $tab_id === $active_tab ? 'show active' : '' ?>" 
                     id="panel-<?= $tab_id ?>" 
                     role="tabpanel" 
                     aria-labelledby="tab-<?= $tab_id ?>"
                     tabindex="0">
                     <div class="settings-tab-content">
            <?php
            // Include the content for this tab
            $tab_file = __DIR__ . '/tabs/system/' . $tab_id . '.php';
            echo "<!-- Looking for tab file: " . htmlspecialchars($tab_file) . " -->\n";
            if (file_exists($tab_file)) {
                echo "<!-- Found tab file, including content for: " . htmlspecialchars($tab_id) . " -->\n";
                // Define context for included files
                if (!defined('PROJECT_ROOT')) {
                    define('PROJECT_ROOT', dirname(dirname(dirname(__DIR__))));
                }
                ob_start();
                include $tab_file;
                $content = ob_get_clean();
                echo "<!-- Content length: " . strlen($content) . " bytes -->\n";
                echo $content;
            } else {
                echo '<div class="alert alert-warning">Tab content file not found: ' . htmlspecialchars($tab_file) . '</div>';
            }
            ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
.settings-container {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.settings-header {
    margin-bottom: 30px;
}

.settings-header h2 {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.settings-header p {
    color: #666;
    font-size: 0.95em;
}

.settings-tabs {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
}

.nav-tabs {
    display: flex;
    border-bottom: 1px solid #dee2e6;
    padding: 0 15px;
    background: #f8f9fa;
    border-radius: 8px 8px 0 0;
}

.nav-tab.nav-link {
    padding: 15px 20px;
    color: #495057;
    text-decoration: none;
    border: none;
    background: none;
    border-bottom: 2px solid transparent;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    cursor: pointer;
}

.nav-tab:hover {
    color: #0d6efd;
    border-bottom-color: #0d6efd;
    background: rgba(13,110,253,0.1);
}

.nav-tab.active {
    color: #0d6efd;
    border-bottom-color: #0d6efd;
    background: #fff;
}

.nav-tab i {
    font-size: 0.9em;
}

.tab-content {
    padding: 20px;
    background: #fff;
    border: 1px solid #dee2e6;
    border-top: none;
    min-height: 200px;
    display: block !important;
}

.settings-tab-content {
    background: #fff;
    padding: 20px;
    border-radius: 0 0 8px 8px;
}

.tab-pane.fade {
    opacity: 0;
    transition: opacity 0.15s linear;
}

.tab-pane.fade.show {
    opacity: 1;
}

/* Form styling */
.settings-form {
    max-width: 800px;
    margin: 0 auto;
}

.form-section {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #dee2e6;
}

.form-section:last-child {
    border-bottom: none;
}

.form-section h3 {
    margin-bottom: 20px;
    color: #212529;
    font-size: 1.25rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-group .form-text {
    font-size: 0.875em;
    color: #6c757d;
}

/* Responsive Design */
@media (max-width: 768px) {
    .nav-tabs {
        flex-direction: column;
        padding: 10px;
    }

    .nav-tab {
        border-bottom: none;
        border-left: 2px solid transparent;
    }

    .nav-tab.active {
        border-bottom: none;
        border-left: 2px solid #0d6efd;
    }
}
</style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all tabs
        const triggerTabList = document.querySelectorAll('#settingsTabs button');
        triggerTabList.forEach(triggerEl => {
            const targetId = triggerEl.getAttribute('data-bs-target');
            const targetPane = document.querySelector(targetId);
            if (targetPane) {
                const tabTrigger = new bootstrap.Tab(triggerEl);
                triggerEl.addEventListener('click', event => {
                    event.preventDefault();
                    tabTrigger.show();
                });
            }
        });
        
        // Show initial tab
        const firstTabEl = document.querySelector('#settingsTabs button.active');
        if (firstTabEl) {
            const firstTab = new bootstrap.Tab(firstTabEl);
            firstTab.show();
        }
    });
  </script>
  <?= template_admin_footer() ?>
```
