<?php
/*
 * SYSTEM: GWS Universal Hybrid Application
 * LOCATION: public_html/accounts_system/account-settings.php
 * LOG: Account settings management page for users
 * PRODUCTION: [To be updated on deployment]
 */

include 'main.php';
require_once '../../private/gws-universal-functions.php';

// Check if user is logged in with remember-me support
check_loggedin_full($pdo, '../auth.php?tab=login');

// Only allow admin access to this page
if (!has_role('admin')) {
    header('Location: ../auth.php?tab=login&error=' . urlencode('You do not have permission to access this page.'));
    exit;
}

// Get the current settings
$settingsPath = __DIR__ . '/accounts-system-config.php';
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

        // Update notifications settings
        $newSettings['notifications']['new_registration'] = isset($_POST['notify_new_registration']);
        $newSettings['notifications']['admin_email'] = filter_var($_POST['admin_email'], FILTER_VALIDATE_EMAIL);
        $newSettings['notifications']['notify_password_reset'] = isset($_POST['notify_password_reset']);

        // Generate settings file content
        $content = "<?php\n\n";
        $content .= "// Accounts System Configuration File\n";
        $content .= "// Centralized settings for the accounts system functionality\n\n";
        $content .= "return " . var_export($newSettings, true) . ";\n";
        
        // Save the settings
        if (file_put_contents($settingsPath, $content)) {
            header('Location: account-settings.php?success=1');
            exit;
        } else {
            throw new Exception('Failed to save settings');
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<?= template_header('Account Settings') ?>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Account System Settings</h5>
                    
                    <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        Settings updated successfully!
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

                        <!-- Notification Settings -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-3">Notification Settings</h6>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="notify_new_registration" name="notify_new_registration" <?= $settings['notifications']['new_registration'] ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="notify_new_registration">Notify on New Registration</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-floating">
                                            <input type="email" class="form-control" id="admin_email" name="admin_email" value="<?= $settings['notifications']['admin_email'] ?>" required>
                                            <label for="admin_email">Admin Email</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="notify_password_reset" name="notify_password_reset" <?= $settings['notifications']['notify_password_reset'] ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="notify_password_reset">Notify on Password Reset</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?= template_footer() ?>
