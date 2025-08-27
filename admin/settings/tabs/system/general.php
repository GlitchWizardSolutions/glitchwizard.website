<?php
// Process form submission if POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: Process form submission
    // Will implement form processing logic here
}

// Get current settings
$default_settings = [
    'maintenance_mode' => false,
    'debug_mode' => false,
    'session_lifetime' => 24,
    'max_upload_size' => '10',
    'default_timezone' => 'UTC',
    'system_email' => '',
    'error_reporting_level' => 'production'
];

// Load saved settings if they exist
$settings_file = dirname(dirname(__DIR__)) . '/tabs/system/data/general_settings.php';
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
    <input type="hidden" name="settings_tab" value="general">
    <div class="professional-form-section">
        <h3>System Configuration</h3>
        
        <div class="form-group">
            <label for="maintenance_mode">
                Maintenance Mode
                <div class="form-text">When enabled, the site will display a maintenance message to non-admin users.</div>
            </label>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="maintenance_mode" name="settings[maintenance_mode]" 
                       <?= $settings['maintenance_mode'] ? 'checked' : '' ?>>
            </div>
        </div>

        <div class="form-group">
            <label for="debug_mode">
                Debug Mode
                <div class="form-text">Enable detailed error messages and logging. Should be disabled in production.</div>
            </label>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="debug_mode" name="settings[debug_mode]" 
                       <?= $settings['debug_mode'] ? 'checked' : '' ?>>
            </div>
        </div>
    </div>

    <div class="professional-form-section">
        <h3>Performance Settings</h3>
        
        <div class="form-group">
            <label for="session_lifetime">Session Lifetime (hours)</label>
            <input type="number" class="form-control" id="session_lifetime" name="settings[session_lifetime]" 
                   value="<?= htmlspecialchars($settings['session_lifetime']) ?>" min="1" max="168">
            <div class="form-text">How long user sessions remain active before requiring re-login.</div>
        </div>

        <div class="form-group">
            <label for="max_upload_size">Maximum Upload Size (MB)</label>
            <input type="number" class="form-control" id="max_upload_size" name="settings[max_upload_size]" 
                   value="<?= htmlspecialchars($settings['max_upload_size']) ?>" min="1" max="100">
            <div class="form-text">Maximum allowed file upload size in megabytes.</div>
        </div>
    </div>

    <div class="professional-form-section">
        <h3>Regional Settings</h3>
        
        <div class="form-group">
            <label for="default_timezone">Default Timezone</label>
            <select class="form-control" id="default_timezone" name="settings[default_timezone]">
                <?php
                $timezones = DateTimeZone::listIdentifiers();
                foreach ($timezones as $timezone) {
                    $selected = $timezone === $settings['default_timezone'] ? 'selected' : '';
                    echo "<option value=\"" . htmlspecialchars($timezone) . "\" {$selected}>" . 
                         htmlspecialchars($timezone) . "</option>";
                }
                ?>
            </select>
            <div class="form-text">System default timezone for dates and times.</div>
        </div>
    </div>

    <div class="professional-form-section">
        <h3>Contact Information</h3>
        
        <div class="form-group">
            <label for="system_email">System Email Address</label>
            <input type="email" class="form-control" id="system_email" name="settings[system_email]" 
                   value="<?= htmlspecialchars($settings['system_email']) ?>">
            <div class="form-text">Primary email address for system notifications and alerts.</div>
        </div>
    </div>

    <div class="professional-form-section">
        <h3>Error Handling</h3>
        
        <div class="form-group">
            <label for="error_reporting_level">Error Reporting Level</label>
            <select class="form-control" id="error_reporting_level" name="settings[error_reporting_level]">
                <option value="development" <?= $settings['error_reporting_level'] === 'development' ? 'selected' : '' ?>>
                    Development (Show all errors)
                </option>
                <option value="production" <?= $settings['error_reporting_level'] === 'production' ? 'selected' : '' ?>>
                    Production (Hide errors)
                </option>
                <option value="custom" <?= $settings['error_reporting_level'] === 'custom' ? 'selected' : '' ?>>
                    Custom
                </option>
            </select>
            <div class="form-text">Determine how errors are handled and displayed.</div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <button type="reset" class="btn btn-secondary">Reset</button>
    </div>
</form>
