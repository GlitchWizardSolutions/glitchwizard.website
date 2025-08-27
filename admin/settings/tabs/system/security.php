<?php
// Process form submission if POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: Process form submission
    // Will implement form processing logic here
}

// Get current settings
$default_settings = [
    'force_ssl' => true,
    'min_password_length' => 8,
    'password_requires_special' => true,
    'password_requires_number' => true,
    'password_requires_uppercase' => true,
    'max_login_attempts' => 5,
    'lockout_time' => 15,
    'session_timeout' => 30,
    'allowed_ip_ranges' => '',
    'two_factor_auth' => false,
    'csrf_protection' => true,
    'xss_protection' => true,
    'secure_headers' => true
];

// Load saved settings if they exist
$settings_file = dirname(dirname(__DIR__)) . '/tabs/system/data/security_settings.php';
error_log("Looking for settings file: $settings_file");

if (file_exists($settings_file)) {
    error_log("Settings file exists, attempting to load");
    $settings = include($settings_file);
    error_log("Loaded settings: " . print_r($settings, true));
} else {
    error_log("Settings file does not exist, using defaults");
    $settings = $default_settings;
}

// Only use defaults for missing keys
foreach ($default_settings as $key => $value) {
    if (!isset($settings[$key])) {
        $settings[$key] = $value;
    }
}
?>

<form method="post" action="" class="settings-form">
    <input type="hidden" name="settings_tab" value="security">
    <div class="professional-form-section">
        <h3>Connection Security</h3>
        
        <div class="form-group">
            <label for="force_ssl">
                Force SSL/HTTPS
                <div class="form-text">Require secure HTTPS connections for all pages.</div>
            </label>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="force_ssl" name="settings[force_ssl]" 
                       <?= $settings['force_ssl'] ? 'checked' : '' ?>>
            </div>
        </div>

        <div class="form-group">
            <label for="allowed_ip_ranges">Allowed IP Ranges</label>
            <textarea class="form-control" id="allowed_ip_ranges" name="settings[allowed_ip_ranges]" rows="3" 
                      placeholder="Enter IP ranges (one per line)"><?= htmlspecialchars($settings['allowed_ip_ranges']) ?></textarea>
            <div class="form-text">Restrict admin access to specific IP ranges. Leave blank to allow all.</div>
        </div>
    </div>

    <div class="professional-form-section">
        <h3>Password Policy</h3>
        
        <div class="form-group">
            <label for="min_password_length">Minimum Password Length</label>
            <input type="number" class="form-control" id="min_password_length" name="settings[min_password_length]" 
                   value="<?= htmlspecialchars($settings['min_password_length']) ?>" min="8" max="32">
        </div>

        <div class="form-group">
            <label for="password_requires_special">
                Require Special Characters
                <div class="form-text">Require at least one special character in passwords.</div>
            </label>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="password_requires_special" 
                       name="settings[password_requires_special]" <?= $settings['password_requires_special'] ? 'checked' : '' ?>>
            </div>
        </div>

        <div class="form-group">
            <label for="password_requires_number">
                Require Numbers
                <div class="form-text">Require at least one number in passwords.</div>
            </label>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="password_requires_number" 
                       name="settings[password_requires_number]" <?= $settings['password_requires_number'] ? 'checked' : '' ?>>
            </div>
        </div>

        <div class="form-group">
            <label for="password_requires_uppercase">
                Require Uppercase Letters
                <div class="form-text">Require at least one uppercase letter in passwords.</div>
            </label>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="password_requires_uppercase" 
                       name="settings[password_requires_uppercase]" <?= $settings['password_requires_uppercase'] ? 'checked' : '' ?>>
            </div>
        </div>
    </div>

    <div class="professional-form-section">
        <h3>Login Security</h3>
        
        <div class="form-group">
            <label for="max_login_attempts">Maximum Login Attempts</label>
            <input type="number" class="form-control" id="max_login_attempts" name="settings[max_login_attempts]" 
                   value="<?= htmlspecialchars($settings['max_login_attempts']) ?>" min="1" max="10">
            <div class="form-text">Number of failed attempts before account lockout.</div>
        </div>

        <div class="form-group">
            <label for="lockout_time">Account Lockout Time (minutes)</label>
            <input type="number" class="form-control" id="lockout_time" name="settings[lockout_time]" 
                   value="<?= htmlspecialchars($settings['lockout_time']) ?>" min="5" max="60">
        </div>

        <div class="form-group">
            <label for="two_factor_auth">
                Two-Factor Authentication
                <div class="form-text">Require 2FA for admin accounts.</div>
            </label>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="two_factor_auth" name="settings[two_factor_auth]" 
                       <?= $settings['two_factor_auth'] ? 'checked' : '' ?>>
            </div>
        </div>
    </div>

    <div class="professional-form-section">
        <h3>Session Security</h3>
        
        <div class="form-group">
            <label for="session_timeout">Session Timeout (minutes)</label>
            <input type="number" class="form-control" id="session_timeout" name="settings[session_timeout]" 
                   value="<?= htmlspecialchars($settings['session_timeout']) ?>" min="5" max="120">
            <div class="form-text">How long until an inactive session expires.</div>
        </div>
    </div>

    <div class="professional-form-section">
        <h3>Protection Features</h3>
        
        <div class="form-group">
            <label for="csrf_protection">
                CSRF Protection
                <div class="form-text">Enable Cross-Site Request Forgery protection.</div>
            </label>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="csrf_protection" name="settings[csrf_protection]" 
                       <?= $settings['csrf_protection'] ? 'checked' : '' ?>>
            </div>
        </div>

        <div class="form-group">
            <label for="xss_protection">
                XSS Protection
                <div class="form-text">Enable Cross-Site Scripting protection.</div>
            </label>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="xss_protection" name="settings[xss_protection]" 
                       <?= $settings['xss_protection'] ? 'checked' : '' ?>>
            </div>
        </div>

        <div class="form-group">
            <label for="secure_headers">
                Secure Headers
                <div class="form-text">Enable security-related HTTP headers.</div>
            </label>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="secure_headers" name="settings[secure_headers]" 
                       <?= $settings['secure_headers'] ? 'checked' : '' ?>>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save Security Settings</button>
        <button type="reset" class="btn btn-secondary">Reset</button>
    </div>
</form>
