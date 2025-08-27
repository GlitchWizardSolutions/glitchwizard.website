 <?php
/* 
 * Developer Settings Management
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: dev_settings.php
 * LOCATION: /public_html/admin/settings/
 * PURPOSE: Developer-level system configuration interface with advanced settings
 * DETAILED DESCRIPTION:
 * This file provides a comprehensive interface for managing developer-level settings 
 * and system configurations. It includes advanced configuration options, system paths,
 * debug settings, and other technical configurations that should only be accessible
 * to users with developer privileges.
 * REQUIRED FILES: 
 * - /public_html/assets/includes/main.php
 * - /private/gws-universal-config.php
 * - /private/role-definitions.php
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: [IN DEVELOPMENT]
 * 
 * FEATURES:
 * - Secure developer-only access
 * - System configuration management
 * - Debug and logging settings
 * - Path and environment configurations
 * - Advanced system settings
 */

// Include required files
include_once '../assets/includes/main.php';

// Security check for developer access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Developer') {
    header('Location: ../auth.php?error=' . urlencode('Access restricted to Developer role only'));
    exit();
}

// Load configuration variables
$config_file = PROJECT_ROOT . '/private/gws-universal-config.php';
require_once $config_file;

// Define settings sections
$settings_files = [
    'system' => [
        'path' => $config_file,
        'title' => 'System Configuration',
        'description' => 'Core system configuration settings',
    'icon' => 'bi bi-gear',
        'variables' => ['ENVIRONMENT', 'PROJECT_ROOT', 'WEB_ROOT_URL']
    ],
    'database' => [
        'path' => $config_file,
        'title' => 'Database Settings',
        'description' => 'Database connection settings',
    'icon' => 'bi bi-database',
        'variables' => ['db_host', 'db_name', 'db_user', 'db_pass', 'db_charset']
    ],
    'security' => [
        'path' => $config_file,
        'title' => 'Security Settings',
        'description' => 'Security configuration',
    'icon' => 'bi bi-shield-check',
        'variables' => ['secret_key']
    ],
    'urls' => [
        'path' => $config_file,
        'title' => 'URL & Path Settings',
        'description' => 'URL and path configuration',
    'icon' => 'bi bi-link-45deg',
        'variables' => [
            'BLOG_ASSETS_URL', 'PUBLIC_ASSETS_URL', 'BACKGROUND_IMAGES_URL',
            'ACCOUNTS_AVATARS_URL', 'BLOG_AVATARS_URL', 'BLOG_POST_IMAGES_URL',
            'BLOG_GALLERY_URL', 'private_path', 'public_path', 'admin_path',
            'documents_system_path', 'vendor_path', 'public_assets_path',
            'blog_path', 'client_portal_path', 'accounts_system_path'
        ]
    ]
];

// Initialize settings array
$settings = [];
foreach ($settings_files as $type => $info) {
    $settings[$type] = [];
    foreach ($info['variables'] as $var) {
        if (defined($var)) {
            $settings[$type][$var] = constant($var);
        } elseif (isset($$var)) {
            $settings[$type][$var] = $$var;
        }
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $messages = [];
    $success = true;
    
    // Store the active tab
    $active_tab = isset($_POST['active_tab']) ? $_POST['active_tab'] : 'system';

    // Process each settings category
    foreach (array_keys($settings_files) as $category) {
        if (isset($_POST[$category])) {
            // Create settings array
            $settings_array = [];
            foreach ($_POST[$category] as $var => $value) {
                // Handle different value types appropriately
                if (is_numeric($value)) {
                    $value = strpos($value, '.') !== false ? floatval($value) : intval($value);
                } elseif ($value === 'true' || $value === 'false') {
                    $value = $value === 'true';
                } else {
                    $value = trim($value);
                }
                
                $settings_array[$var] = $value;
            }

            // Update the existing file content
            $file_content = file_get_contents($config_file);
            
            // Update each variable in the file
            foreach ($settings_array as $var => $value) {
                // Try to update defined constant first
                $pattern = "/define\(['\"]" . preg_quote($var) . "['\"]\s*,\s*[^)]+\)/";
                $replacement = "define('$var', " . var_export($value, true) . ")";
                $new_content = preg_replace($pattern, $replacement, $file_content);
                
                // If the content changed, update it
                if ($new_content !== $file_content) {
                    $file_content = $new_content;
                } else {
                    // Try to update variable if constant wasn't found
                    $pattern = "/\\\${$var}\s*=\s*[^;]+;/";
                    $replacement = "\$$var = " . var_export($value, true) . ";";
                    $file_content = preg_replace($pattern, $replacement, $file_content);
                }
            }
            
            // Save to file
            if (file_put_contents($config_file, $file_content)) {
                $messages[] = ucfirst($category) . " settings saved successfully.";
            } else {
                $success = false;
                $messages[] = "Error saving " . $category . " settings.";
            }
        }
    }

    // Display messages
    if ($success) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>Settings saved successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    } else {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-octagon-fill me-2" aria-hidden="true"></i>Error saving some settings. Please check the messages below.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    }
}
?>
<?= template_admin_header('Developer Settings', 'settings', 'developer') ?>

<!-- Developer Warning Banner -->
<div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-3 fs-4" aria-hidden="true"></i>
    <div>
        <h5 class="alert-heading mb-1">Developer Configuration Access</h5>
        <p class="mb-0">
            <strong>Warning:</strong> This page provides direct access to core system configuration.
            Changes made here affect the entire application and all users.
            Always create backups and test changes in a development environment first.
        </p>
    </div>
</div>

<link rel="stylesheet" href="../assets/css/tab-fixes.css">

<form action="" method="post" id="settingsForm" class="needs-validation" novalidate>
    <input type="hidden" name="active_tab" id="active_tab" value="system">
    <div class="form-actions">
        <a href="settings_dash.php" class="btn btn-outline-secondary" aria-label="Cancel">
            <i class="bi bi-arrow-left" aria-hidden="true"></i> Cancel
        </a>
        <button type="submit" class="btn btn-success" aria-label="Save configuration">
            <i class="bi bi-save me-2" aria-hidden="true"></i>Save Configuration 
        </button>
    </div>
    <br>
    
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <ul class="nav nav-tabs card-header-tabs" id="settingsTabs" role="tablist">
                <?php foreach ($settings_files as $section => $info) { ?>
                <li class="nav-item" role="presentation">
                    <a class="nav-link <?= ($section === 'system' ? 'active' : '') ?>" 
                       id="<?= $section ?>-tab" 
                       data-bs-toggle="tab" 
                       href="#<?= $section ?>" 
                       role="tab" 
                       aria-controls="<?= $section ?>" 
                       aria-selected="<?= ($section === 'system' ? 'true' : 'false') ?>">
                        <i class="<?= $info['icon'] ?>"></i>
                        <?= htmlspecialchars($info['title']) ?>
                    </a>
                </li>
                <?php } ?>
            </ul>
        </div>
        
        <div class="card-body p-4">
            <div class="tab-content">
                <?php foreach ($settings_files as $section => $info) { ?>
                <div class="tab-pane fade <?= ($section === 'system' ? 'show active' : '') ?>" 
                     id="<?= $section ?>" 
                     role="tabpanel" 
                     aria-labelledby="<?= $section ?>-tab">
                    
                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle me-2" aria-hidden="true"></i>
                        <?= htmlspecialchars($info['description']) ?>
                    </div>
                    
                    <div class="row">
                        <?php 
                        foreach ($info['variables'] as $var) {
                            $value = $settings[$section][$var] ?? '';
                            $is_const = defined($var);
                        ?>
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label for="<?= $section ?>_<?= $var ?>" class="form-label fw-bold">
                                    <?= htmlspecialchars($var) ?>
                                </label>
                                
                                <input type="text" 
                                       name="<?= $section ?>[<?= $var ?>]" 
                                       id="<?= $section ?>_<?= $var ?>" 
                                       class="form-control" 
                                       value="<?= htmlspecialchars($value) ?>"
                                       <?= $is_const ? ' readonly' : '' ?>>
                                       
                                <?php if ($is_const) { ?>
                                <div class="form-text text-muted mt-1">
                                    <i class="bi bi-info-circle" aria-hidden="true"></i>
                                    This is a defined constant.
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Get the active tab from URL hash or stored value
    var activeTabId = window.location.hash ? window.location.hash.substring(1) : 'system';
    document.getElementById('active_tab').value = activeTabId;
    
    // Activate the appropriate tab
    var tabEl = document.querySelector('a[href="#' + activeTabId + '"]');
    if (tabEl) {
        var tab = new bootstrap.Tab(tabEl);
        tab.show();
    }

    // Update hidden input when tab changes
    var tabs = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabs.forEach(function(tab) {
        tab.addEventListener('shown.bs.tab', function (event) {
            var targetId = event.target.getAttribute('href').substring(1);
            document.getElementById('active_tab').value = targetId;
            history.replaceState(null, null, '#' + targetId);
        });
    });
});
</script>

<?= template_admin_footer() ?>
