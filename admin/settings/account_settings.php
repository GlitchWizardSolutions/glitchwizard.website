<?php
/**
 * Account Settings Management Interface
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: account_settings.php
 * LOCATION: /public_html/admin/settings/
 * PURPOSE: Main administrative interface for managing all account-related settings
 * 
 * FILE RELATIONSHIP:
 * This file (account_settings.php) serves as the main interface controller that presents
 * a unified tabbed settings panel. It loads and manages settings stored in several
 * configuration files:
 * - accounts_settings.php: Stores general account settings (SMTP, notifications)
 * - account_settings.php: Stores account-specific configuration
 * - account_feature_settings.php: Stores feature-related settings
 * - accounts_system_settings.php: Stores system-level account settings
 * 
 * HOW IT WORKS:
 * 1. This interface file loads settings from all config files
 * 2. Presents them in an organized tabbed interface
 * 3. When settings are saved, they are written back to their respective config files
 * 4. Each config file maintains its own specific scope of settings
 * 
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: [IN DEVELOPMENT]
 * 
 * FEATURES:
 * - Unified tabbed interface for all account settings
 * - Features tab (from account_feature_settings.php)
 * - System tab (from accounts_system_settings.php)
 * - Configuration tab (account specific settings)
 * - General tab (global account settings)
 */

include_once '../assets/includes/main.php';

// Load persistent account settings from config files
$settings_files = [
    'features' => PROJECT_ROOT . '/public_html/assets/includes/settings/account_feature_settings.php',
    'system' => PROJECT_ROOT . '/public_html/assets/includes/settings/accounts_system_settings.php',
    'config' => PROJECT_ROOT . '/public_html/assets/includes/settings/account_settings.php',
    'general' => PROJECT_ROOT . '/public_html/assets/includes/settings/accounts_settings.php'
];

// Create settings directory if it doesn't exist
$settings_dir = PROJECT_ROOT . '/public_html/assets/includes/settings';
if (!file_exists($settings_dir)) {
    mkdir($settings_dir, 0755, true);
}

// Initialize default settings if files don't exist
foreach ($settings_files as $type => $file) {
    if (!file_exists($file)) {
        $default_settings = [];
        $php_code = "<?php\n// Account Settings - {$type}\n// Last updated: " . date('Y-m-d H:i:s') . "\n\n";
        $php_code .= "\$account_settings = " . var_export($default_settings, true) . ";\n";
        file_put_contents($file, $php_code);
    }
}

$settings = [];
foreach ($settings_files as $type => $file) {
    if (file_exists($file)) {
        include $file;
        // Each file should populate its own settings array which we merge here
        $settings[$type] = isset($account_settings) ? $account_settings : [];
        unset($account_settings); // Clear for next iteration
    } else {
        $settings[$type] = [];
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $messages = [];
    $success = true;

    // Process each settings category
    foreach (['features', 'system', 'config', 'general'] as $category) {
        if (isset($_POST[$category])) {
            // Get the file path for this category
            $file_path = $settings_files[$category];
            
            // Create settings array
            $settings_array = [];
            foreach ($_POST[$category] as $key => $value) {
                // Handle boolean values from checkboxes
                if (in_array($key, [
                    'registration_enabled', 'email_verification_required', 
                    'password_complexity_required', 'two_factor_auth_enabled',
                    'social_login_enabled', 'avatar_upload_enabled',
                    'gdpr_compliance_enabled', 'audit_log_enabled',
                    'api_access_enabled', 'notification_email_welcome',
                    'notification_email_password_reset', 'notification_email_login_alerts'
                ])) {
                    $settings_array[$key] = ($value === '1');
                }
                // Handle numeric values
                else if (in_array($key, [
                    'min_password_length', 'max_login_attempts',
                    'login_lockout_duration', 'session_timeout',
                    'rememberme_duration', 'avatar_max_size',
                    'password_reset_token_expiry', 'username_min_length',
                    'username_max_length', 'auto_cleanup_inactive_days',
                    'smtp_port'
                ])) {
                    $settings_array[$key] = intval($value);
                }
                // Everything else as string
                else {
                    $settings_array[$key] = trim($value);
                }
            }

            // Generate PHP code
            $php_code = "<?php\n// Account Settings - {$category}\n// Last updated: " . date('Y-m-d H:i:s') . "\n\n";
            $php_code .= "\$account_settings = " . var_export($settings_array, true) . ";\n";
            
            // Save to file
            if (file_put_contents($file_path, $php_code)) {
                $messages[] = ucfirst($category) . " settings saved successfully.";
            } else {
                $success = false;
                $messages[] = "Error saving " . $category . " settings.";
            }

            // Update the settings array for the current request
            $settings[$category] = $settings_array;
        }
    }

    // Add alert message
    if ($success) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>Settings saved successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    } else {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>Error saving some settings. Please check the messages below.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    }
}

?>
<?= template_admin_header('Account Settings', 'settings', 'accounts') ?>
<!-- Include tab fixes CSS -->
<link rel="stylesheet" href="../assets/css/tab-fixes.css">

<div class="content-title">
    <div class="title">
        <div class="icon">
            <i class="fas fa-user-cog"></i>
        </div>
        <div class="txt">
            <h2>Account Settings</h2>
            <p>Manage all account-related settings and configurations</p>
        </div>
    </div>
</div>

    <form action="" method="post" id="accountSettingsForm" class="needs-validation" novalidate>
        <div class="card shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <ul class="nav nav-tabs card-header-tabs" id="settingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="features-tab" data-bs-toggle="tab" href="#features" role="tab" aria-controls="features" aria-selected="true">
                            <i class="fas fa-puzzle-piece"></i>
                            Features
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="system-tab" data-bs-toggle="tab" href="#system" role="tab" aria-controls="system" aria-selected="false">
                            <i class="fas fa-cogs"></i>
                            System
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="config-tab" data-bs-toggle="tab" href="#config" role="tab" aria-controls="config" aria-selected="false">
                            <i class="fas fa-sliders-h"></i>
                            Configuration
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="general-tab" data-bs-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="false">
                            <i class="fas fa-wrench"></i>
                            General
                        </a>
                    </li>
                </ul>
                <button type="submit" class="btn btn-success px-4">
                    <i class="fas fa-save me-2"></i>Save Settings
                </button>
            </div>
            
            <div class="card-body p-4">
                <div class="tab-content">
                    <!-- Features Tab -->
                    <div class="tab-pane fade show active" id="features" role="tabpanel" aria-labelledby="features-tab">
                        <div class="row">
                            <?php
                            $features_meta = [
                                'registration_enabled' => ['Enable User Registration', 'boolean', 'Allow new users to register accounts'],
                                'email_verification_required' => ['Require Email Verification', 'boolean', 'Users must verify their email address before accessing their account'],
                                'min_password_length' => ['Minimum Password Length', 'number', 'Minimum number of characters required for passwords'],
                                'password_complexity_required' => ['Require Complex Passwords', 'boolean', 'Passwords must contain uppercase, lowercase, numbers, and special characters'],
                                'two_factor_auth_enabled' => ['Enable Two-Factor Authentication', 'boolean', 'Require two-factor authentication for login'],
                                'social_login_enabled' => ['Enable Social Login', 'boolean', 'Allow users to login with social accounts'],
                                'avatar_upload_enabled' => ['Enable Avatar Uploads', 'boolean', 'Allow users to upload profile pictures'],
                                'avatar_max_size' => ['Maximum Avatar Size (bytes)', 'number', 'Maximum file size for avatar uploads'],
                                'profile_fields_required' => ['Required Profile Fields', 'text', 'Comma-separated list of required profile fields']
                            ];
                            foreach ($features_meta as $key => $meta): 
                                $value = $settings['features'][$key] ?? '';
                            ?>
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label for="features_<?= $key ?>" class="form-label fw-bold">
                                            <?= htmlspecialchars($meta[0]) ?>
                                        </label>
                                        <?php if (!empty($meta[2])): ?>
                                            <div class="form-text text-muted mb-2">
                                                <?= htmlspecialchars($meta[2]) ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($meta[1] === 'boolean'): ?>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" 
                                                       name="features[<?= $key ?>]" 
                                                       id="features_<?= $key ?>" 
                                                       class="form-check-input" 
                                                       value="1" 
                                                       <?= $value ? ' checked' : '' ?>>
                                                <label class="form-check-label" for="features_<?= $key ?>">
                                                    Enable <?= htmlspecialchars($meta[0]) ?>
                                                </label>
                                            </div>
                                        <?php elseif ($meta[1] === 'number'): ?>
                                            <input type="number" 
                                                   name="features[<?= $key ?>]" 
                                                   id="features_<?= $key ?>" 
                                                   class="form-control" 
                                                   value="<?= htmlspecialchars($value) ?>">
                                        <?php else: ?>
                                            <input type="text" 
                                                   name="features[<?= $key ?>]" 
                                                   id="features_<?= $key ?>" 
                                                   class="form-control" 
                                                   value="<?= htmlspecialchars($value) ?>">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- System Tab -->
                    <div class="tab-pane fade" id="system" role="tabpanel" aria-labelledby="system-tab">
                        <div class="row">
                            <?php
                            $system_meta = [
                                'max_login_attempts' => ['Maximum Login Attempts', 'number', 'Number of failed login attempts before account lockout'],
                                'login_lockout_duration' => ['Lockout Duration (seconds)', 'number', 'How long to lock accounts after failed login attempts'],
                                'session_timeout' => ['Session Timeout (seconds)', 'number', 'How long user sessions remain active without activity'],
                                'rememberme_duration' => ['Remember Me Duration (seconds)', 'number', 'How long "Remember Me" sessions last'],
                                'password_reset_token_expiry' => ['Password Reset Token Expiry (seconds)', 'number', 'How long password reset tokens are valid'],
                                'auto_cleanup_inactive_days' => ['Auto Cleanup Inactive Days', 'number', 'Days before inactive accounts are cleaned up'],
                                'gdpr_compliance_enabled' => ['Enable GDPR Compliance', 'boolean', 'Enable GDPR compliance features'],
                                'audit_log_enabled' => ['Enable Audit Log', 'boolean', 'Log account system actions for auditing'],
                                'api_access_enabled' => ['Enable API Access', 'boolean', 'Allow API access for accounts']
                            ];
                            foreach ($system_meta as $key => $meta): 
                                $value = $settings['system'][$key] ?? '';
                            ?>
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label for="system_<?= $key ?>" class="form-label fw-bold">
                                            <?= htmlspecialchars($meta[0]) ?>
                                        </label>
                                        <?php if (!empty($meta[2])): ?>
                                            <div class="form-text text-muted mb-2">
                                                <?= htmlspecialchars($meta[2]) ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($meta[1] === 'boolean'): ?>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" 
                                                       name="system[<?= $key ?>]" 
                                                       id="system_<?= $key ?>" 
                                                       class="form-check-input" 
                                                       value="1" 
                                                       <?= $value ? ' checked' : '' ?>>
                                                <label class="form-check-label" for="system_<?= $key ?>">
                                                    Enable <?= htmlspecialchars($meta[0]) ?>
                                                </label>
                                            </div>
                                        <?php elseif ($meta[1] === 'number'): ?>
                                            <input type="number" 
                                                   name="system[<?= $key ?>]" 
                                                   id="system_<?= $key ?>" 
                                                   class="form-control" 
                                                   value="<?= htmlspecialchars($value) ?>">
                                        <?php else: ?>
                                            <input type="text" 
                                                   name="system[<?= $key ?>]" 
                                                   id="system_<?= $key ?>" 
                                                   class="form-control" 
                                                   value="<?= htmlspecialchars($value) ?>">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Configuration Tab -->
                    <div class="tab-pane fade" id="config" role="tabpanel" aria-labelledby="config-tab">
                        <div class="row">
                            <?php
                            $config_meta = [
                                'username_min_length' => ['Minimum Username Length', 'number', 'Minimum number of characters for usernames'],
                                'username_max_length' => ['Maximum Username Length', 'number', 'Maximum number of characters for usernames'],
                                'profile_picture_types' => ['Allowed Profile Picture Types', 'text', 'Comma-separated list of allowed image types'],
                                'default_role' => ['Default User Role', 'select', 'Default role assigned to new user accounts', ['Member', 'Admin', 'Guest', 'Subscriber']]
                            ];
                            foreach ($config_meta as $key => $meta): 
                                $value = $settings['config'][$key] ?? '';
                            ?>
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label for="config_<?= $key ?>" class="form-label fw-bold">
                                            <?= htmlspecialchars($meta[0]) ?>
                                        </label>
                                        <?php if (!empty($meta[2])): ?>
                                            <div class="form-text text-muted mb-2">
                                                <?= htmlspecialchars($meta[2]) ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($meta[1] === 'select'): ?>
                                            <select name="config[<?= $key ?>]" 
                                                    id="config_<?= $key ?>" 
                                                    class="form-select">
                                                <?php foreach ($meta[3] as $option): ?>
                                                    <option value="<?= htmlspecialchars($option) ?>" 
                                                            <?= $value === $option ? ' selected' : '' ?>>
                                                        <?= htmlspecialchars($option) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php elseif ($meta[1] === 'number'): ?>
                                            <input type="number" 
                                                   name="config[<?= $key ?>]" 
                                                   id="config_<?= $key ?>" 
                                                   class="form-control" 
                                                   value="<?= htmlspecialchars($value) ?>">
                                        <?php else: ?>
                                            <input type="text" 
                                                   name="config[<?= $key ?>]" 
                                                   id="config_<?= $key ?>" 
                                                   class="form-control" 
                                                   value="<?= htmlspecialchars($value) ?>">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- General Tab -->
                    <div class="tab-pane fade" id="general" role="tabpanel" aria-labelledby="general-tab">
                        <div class="row">
                            <?php
                            $general_meta = [
                                'notification_email_welcome' => ['Send Welcome Emails', 'boolean', 'Send welcome email to new users after registration'],
                                'notification_email_password_reset' => ['Send Password Reset Emails', 'boolean', 'Send email notifications for password reset requests'],
                                'notification_email_login_alerts' => ['Send Login Alert Emails', 'boolean', 'Send email alerts for new login sessions'],
                                'smtp_host' => ['SMTP Host', 'text', 'SMTP server hostname'],
                                'smtp_port' => ['SMTP Port', 'number', 'SMTP server port'],
                                'smtp_user' => ['SMTP Username', 'text', 'SMTP authentication username'],
                                'smtp_pass' => ['SMTP Password', 'password', 'SMTP authentication password'],
                                'smtp_secure' => ['SMTP Security', 'select', 'SMTP connection security', ['none', 'ssl', 'tls']],
                                'mail_from_name' => ['From Name', 'text', 'Name to show in the From field of emails'],
                                'mail_from_email' => ['From Email', 'text', 'Email address to send from']
                            ];
                            foreach ($general_meta as $key => $meta): 
                                $value = $settings['general'][$key] ?? '';
                            ?>
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label for="general_<?= $key ?>" class="form-label fw-bold">
                                            <?= htmlspecialchars($meta[0]) ?>
                                        </label>
                                        <?php if (!empty($meta[2])): ?>
                                            <div class="form-text text-muted mb-2">
                                                <?= htmlspecialchars($meta[2]) ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($meta[1] === 'boolean'): ?>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" 
                                                       name="general[<?= $key ?>]" 
                                                       id="general_<?= $key ?>" 
                                                       class="form-check-input" 
                                                       value="1" 
                                                       <?= $value ? ' checked' : '' ?>>
                                                <label class="form-check-label" for="general_<?= $key ?>">
                                                    Enable <?= htmlspecialchars($meta[0]) ?>
                                                </label>
                                            </div>
                                        <?php elseif ($meta[1] === 'select'): ?>
                                            <select name="general[<?= $key ?>]" 
                                                    id="general_<?= $key ?>" 
                                                    class="form-select">
                                                <?php foreach ($meta[3] as $option): ?>
                                                    <option value="<?= htmlspecialchars($option) ?>" 
                                                            <?= $value === $option ? ' selected' : '' ?>>
                                                        <?= htmlspecialchars($option) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php elseif ($meta[1] === 'password'): ?>
                                            <input type="password" 
                                                   name="general[<?= $key ?>]" 
                                                   id="general_<?= $key ?>" 
                                                   class="form-control" 
                                                   value="<?= htmlspecialchars($value) ?>"
                                                   autocomplete="new-password">
                                        <?php else: ?>
                                            <input type="text" 
                                                   name="general[<?= $key ?>]" 
                                                   id="general_<?= $key ?>" 
                                                   class="form-control" 
                                                   value="<?= htmlspecialchars($value) ?>">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form><script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    });

    // Get the active tab from URL hash or default to features
    var activeTabId = window.location.hash ? window.location.hash.substring(1) : 'features';
    
    // Activate the appropriate tab
    var activeTab = document.querySelector('[data-bs-target="#' + activeTabId + '"]');
    if (activeTab) {
        var tab = new bootstrap.Tab(activeTab);
        tab.show();
    }

    // Update URL hash when tab changes and ensure proper display
    var tabs = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabs.forEach(function(tab) {
        tab.addEventListener('shown.bs.tab', function (event) {
            var targetId = event.target.getAttribute('data-bs-target');
            history.replaceState(null, null, targetId);
            
            // Ensure proper display of tab content
            document.querySelector(targetId).classList.add('show', 'active');
        });
    });
});
</script>

<?= template_admin_footer() ?>
