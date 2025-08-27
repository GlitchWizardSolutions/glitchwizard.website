<?php
// Ensure we're in the right context
if (!defined('PROJECT_ROOT')) {
    exit('Direct access denied');
}

// Process form submission if POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: Process form submission
    // Will implement form processing logic here
}

// Get current settings
$default_settings = [
    'auto_backup' => true,
    'backup_frequency' => 'daily',
    'backup_retention' => 30,
    'backup_include_files' => true,
    'backup_include_database' => true,
    'backup_compression' => true,
    'log_retention' => 90,
    'error_log_enabled' => true,
    'access_log_enabled' => true,
    'system_log_enabled' => true,
    'cleanup_temp_files' => true,
    'temp_files_age' => 24,
    'auto_update_check' => true,
    'update_notification_email' => ''
];

// Load saved settings if they exist
$settings_file = dirname(dirname(__DIR__)) . '/tabs/system/data/maintenance_settings.php';
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
error_log("Final merged settings: " . print_r($settings, true));

// Get list of available backup files
$backup_files = [
    ['name' => 'backup_2025-08-06_000000.zip', 'size' => '125MB', 'date' => '2025-08-06 00:00:00'],
    ['name' => 'backup_2025-08-05_000000.zip', 'size' => '124MB', 'date' => '2025-08-05 00:00:00'],
    ['name' => 'backup_2025-08-04_000000.zip', 'size' => '123MB', 'date' => '2025-08-04 00:00:00']
];
?>

<form method="post" action="" class="settings-form">
    <input type="hidden" name="settings_tab" value="maintenance">
    <div class="professional-form-section">
        <h3>Backup Settings</h3>
        
        <div class="form-group">
            <label for="auto_backup">
                Automatic Backups
                <div class="form-text">Enable automated system backups.</div>
            </label>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="auto_backup" name="settings[auto_backup]" 
                       <?= $settings['auto_backup'] ? 'checked' : '' ?>>
            </div>
        </div>

        <div class="form-group">
            <label for="backup_frequency">Backup Frequency</label>
            <select class="form-control" id="backup_frequency" name="settings[backup_frequency]">
                <option value="hourly" <?= $settings['backup_frequency'] === 'hourly' ? 'selected' : '' ?>>Hourly</option>
                <option value="daily" <?= $settings['backup_frequency'] === 'daily' ? 'selected' : '' ?>>Daily</option>
                <option value="weekly" <?= $settings['backup_frequency'] === 'weekly' ? 'selected' : '' ?>>Weekly</option>
                <option value="monthly" <?= $settings['backup_frequency'] === 'monthly' ? 'selected' : '' ?>>Monthly</option>
            </select>
        </div>

        <div class="form-group">
            <label for="backup_retention">Backup Retention (days)</label>
            <input type="number" class="form-control" id="backup_retention" name="settings[backup_retention]" 
                   value="<?= htmlspecialchars($settings['backup_retention']) ?>" min="1" max="365">
            <div class="form-text">How long to keep backup files.</div>
        </div>

        <div class="form-group">
            <label for="backup_include_files">
                Include Files in Backup
                <div class="form-text">Backup system files along with the database.</div>
            </label>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="backup_include_files" 
                       name="settings[backup_include_files]" <?= $settings['backup_include_files'] ? 'checked' : '' ?>>
            </div>
        </div>

        <div class="form-group">
            <label for="backup_compression">
                Compress Backups
                <div class="form-text">Use ZIP compression for backup files.</div>
            </label>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="backup_compression" 
                       name="settings[backup_compression]" <?= $settings['backup_compression'] ? 'checked' : '' ?>>
            </div>
        </div>
    </div>

    <div class="professional-form-section">
        <h3>Recent Backups</h3>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Backup File</th>
                        <th>Size</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($backup_files as $backup): ?>
                    <tr>
                        <td><?= htmlspecialchars($backup['name']) ?></td>
                        <td><?= htmlspecialchars($backup['size']) ?></td>
                        <td><?= htmlspecialchars($backup['date']) ?></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary">Download</button>
                            <button type="button" class="btn btn-sm btn-danger">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="professional-form-section">
        <h3>Log Management</h3>
        
        <div class="form-group">
            <label for="log_retention">Log Retention (days)</label>
            <input type="number" class="form-control" id="log_retention" name="settings[log_retention]" 
                   value="<?= htmlspecialchars($settings['log_retention']) ?>" min="1" max="365">
            <div class="form-text">How long to keep system logs.</div>
        </div>

        <div class="form-group">
            <label for="error_log_enabled">
                Error Logging
                <div class="form-text">Enable error log file.</div>
            </label>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="error_log_enabled" 
                       name="settings[error_log_enabled]" <?= $settings['error_log_enabled'] ? 'checked' : '' ?>>
            </div>
        </div>

        <div class="form-group">
            <label for="system_log_enabled">
                System Logging
                <div class="form-text">Enable system events log file.</div>
            </label>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="system_log_enabled" 
                       name="settings[system_log_enabled]" <?= $settings['system_log_enabled'] ? 'checked' : '' ?>>
            </div>
        </div>
    </div>

    <div class="professional-form-section">
        <h3>Cleanup Settings</h3>
        
        <div class="form-group">
            <label for="cleanup_temp_files">
                Automatic Temp File Cleanup
                <div class="form-text">Automatically remove old temporary files.</div>
            </label>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="cleanup_temp_files" 
                       name="settings[cleanup_temp_files]" <?= $settings['cleanup_temp_files'] ? 'checked' : '' ?>>
            </div>
        </div>

        <div class="form-group">
            <label for="temp_files_age">Remove Temp Files Older Than (hours)</label>
            <input type="number" class="form-control" id="temp_files_age" name="settings[temp_files_age]" 
                   value="<?= htmlspecialchars($settings['temp_files_age']) ?>" min="1" max="168">
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save Maintenance Settings</button>
        <button type="reset" class="btn btn-secondary">Reset</button>
    </div>
</form>
