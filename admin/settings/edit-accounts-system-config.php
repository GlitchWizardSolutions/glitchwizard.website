<?php
/*
 * SYSTEM: GWS Universal Hybrid Application
 * LOCATION: public_html/admin/settings/edit-accounts-system-config.php
 * LOG: Edit interface for the new accounts system configuration
 * PRODUCTION: [To be updated on deployment]
 */

include_once '../assets/includes/main.php';
// Dynamically locate and include private/gws-universal-config.php
$config_found = false;
$max_levels = 5; // Maximum directory levels to traverse up
$config_path = 'private/gws-universal-config.php';
$dir = __DIR__;
for ($i = 0; $i <= $max_levels; $i++)
{
    $try_path = $dir . str_repeat('/..', $i) . '/' . $config_path;
    if (file_exists($try_path))
    {
        require_once $try_path;
        $config_found = true;
        break;
    }
}
require_once '../../../private/gws-universal-functions.php';
require_once '../../accounts_system/main.php';

// Check if user is logged in with remember-me support
check_loggedin_full($pdo, '../../auth.php?tab=login');

// Only allow admin access to this page
if (!has_role('admin')) {
    header('Location: ../../auth.php?tab=login&error=' . urlencode('You do not have permission to access this page.'));
    exit;
}

// Get the current settings
$settingsPath = PROJECT_ROOT . '/public_html/accounts_system/accounts-system-config.php';
$settings = include($settingsPath);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate and update settings
        $newSettings = $settings; // Start with current settings
        
        // Update registration settings
        $newSettings['registration']['enabled'] = isset($_POST['registration_enabled']);
        $newSettings['registration']['require_email'] = isset($_POST['require_email']);
        $newSettings['registration']['min_password'] = filter_var($_POST['min_password'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 6]]);
        $newSettings['registration']['special_chars'] = isset($_POST['special_chars']);
        
        // Update session settings
        $newSettings['session']['lifetime'] = filter_var($_POST['session_lifetime'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 300]]);
        $newSettings['session']['remember_me'] = isset($_POST['remember_me']);
        $newSettings['session']['remember_duration'] = filter_var($_POST['remember_duration'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 86400]]);
        
        // Update profile settings
        $newSettings['profile']['allow_avatar'] = isset($_POST['allow_avatar']);
        $newSettings['profile']['max_size'] = $_POST['max_size'];
        $newSettings['profile']['allowed_types'] = array_filter(explode(',', $_POST['allowed_types']));
        
        // Update security settings
        $newSettings['security']['max_attempts'] = filter_var($_POST['max_attempts'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
        $newSettings['security']['lockout_time'] = filter_var($_POST['lockout_time'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 60]]);
        $newSettings['security']['enable_2fa'] = isset($_POST['enable_2fa']);

        // Update email templates
        $newSettings['email_templates']['welcome'] = $_POST['welcome_template'];
        $newSettings['email_templates']['reset'] = $_POST['reset_template'];
        $newSettings['email_templates']['verify'] = $_POST['verify_template'];

        // Update features
        $newSettings['features']['social_login'] = isset($_POST['social_login']);
        $newSettings['features']['api_access'] = isset($_POST['api_access']);
        $newSettings['features']['delete_account'] = isset($_POST['delete_account']);

        // Generate settings file content
        $content = "<?php\n\n";
        $content .= "// Accounts System Configuration File\n";
        $content .= "// Centralized settings for the accounts system functionality\n\n";
        $content .= "return " . var_export($newSettings, true) . ";\n";
        
        // Create backup of current settings
        $backupDir = PROJECT_ROOT . '/private/backups/settings_' . date('Y-m-d_His');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        copy($settingsPath, $backupDir . '/accounts-system-config.php');
        
        // Save the new settings
        if (file_put_contents($settingsPath, $content)) {
            header('Location: edit-accounts-system-config.php?success=1');
            exit;
        } else {
            throw new Exception('Failed to save settings');
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<?= template_admin_header('Edit Accounts System Configuration', 'settings', 'accounts') ?>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Edit Accounts System Configuration</h5>
                    
                    <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        Settings updated successfully! A backup was created in the private/backups directory.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <form method="post" class="needs-validation" novalidate>
                        <!-- Registration Settings -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-3">Registration Settings</h6>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="registration_enabled" name="registration_enabled" <?= $settings['registration']['enabled'] ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="registration_enabled">Enable Registration</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="require_email" name="require_email" <?= $settings['registration']['require_email'] ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="require_email">Require Email</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" id="min_password" name="min_password" value="<?= $settings['registration']['min_password'] ?>" min="6" required>
                                            <label for="min_password">Min Password Length</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="special_chars" name="special_chars" <?= $settings['registration']['special_chars'] ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="special_chars">Require Special Chars</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Session Settings -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-3">Session Settings</h6>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" id="session_lifetime" name="session_lifetime" value="<?= $settings['session']['lifetime'] ?>" min="300" required>
                                            <label for="session_lifetime">Session Lifetime (seconds)</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" id="remember_me" name="remember_me" <?= $settings['session']['remember_me'] ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="remember_me">Enable Remember Me</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" id="remember_duration" name="remember_duration" value="<?= $settings['session']['remember_duration'] ?>" min="86400" required>
                                            <label for="remember_duration">Remember Duration (seconds)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Settings -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-3">Profile Settings</h6>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="allow_avatar" name="allow_avatar" <?= $settings['profile']['allow_avatar'] ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="allow_avatar">Allow Avatars</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="max_size" name="max_size" value="<?= $settings['profile']['max_size'] ?>" required>
                                            <label for="max_size">Max Avatar Size</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="allowed_types" name="allowed_types" value="<?= implode(',', $settings['profile']['allowed_types']) ?>" required>
                                            <label for="allowed_types">Allowed File Types (comma-separated)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Security Settings -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-3">Security Settings</h6>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" id="max_attempts" name="max_attempts" value="<?= $settings['security']['max_attempts'] ?>" min="1" required>
                                            <label for="max_attempts">Max Login Attempts</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" id="lockout_time" name="lockout_time" value="<?= $settings['security']['lockout_time'] ?>" min="60" required>
                                            <label for="lockout_time">Lockout Time (seconds)</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" id="enable_2fa" name="enable_2fa" <?= $settings['security']['enable_2fa'] ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="enable_2fa">Enable 2FA</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Email Templates -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-3">Email Templates</h6>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="welcome_template" name="welcome_template" value="<?= $settings['email_templates']['welcome'] ?>" required>
                                            <label for="welcome_template">Welcome Email Template</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="reset_template" name="reset_template" value="<?= $settings['email_templates']['reset'] ?>" required>
                                            <label for="reset_template">Password Reset Template</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="verify_template" name="verify_template" value="<?= $settings['email_templates']['verify'] ?>" required>
                                            <label for="verify_template">Email Verification Template</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Features -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-3">Feature Settings</h6>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="social_login" name="social_login" <?= $settings['features']['social_login'] ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="social_login">Enable Social Login</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="api_access" name="api_access" <?= $settings['features']['api_access'] ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="api_access">Enable API Access</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="delete_account" name="delete_account" <?= $settings['features']['delete_account'] ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="delete_account">Allow Account Deletion</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <div class="content-header form-actions-header">
                                <div class="form-actions">
                                    <a href="settings.php" class="btn btn-outline-secondary" aria-label="Cancel and return to settings">
                                        <i class="bi bi-arrow-left" aria-hidden="true"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-success" aria-label="Save settings">
                                        <i class="bi bi-save me-2" aria-hidden="true"></i>Save Settings
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?= template_admin_footer() ?>
