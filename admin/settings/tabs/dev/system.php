<?php
// Load current settings
$settings_file = __DIR__ . '/../data/system_settings.php';
$default_settings = [
    'ENVIRONMENT' => 'dev',
    'DEBUG_MODE' => false,
    'ERROR_REPORTING' => E_ALL,
    'DISPLAY_ERRORS' => true,
    'LOG_ERRORS' => true,
    'ERROR_LOG_PATH' => PROJECT_ROOT . '/logs/error.log',
    'TIMEZONE' => 'UTC',
    'CHARSET' => 'UTF-8'
];

if (file_exists($settings_file)) {
    $settings = include($settings_file);
    $settings = array_merge($default_settings, $settings);
} else {
    $settings = $default_settings;
}

foreach ($settings as $key => $value) {
    echo '<div class="col-md-6">';
    echo '<div class="form-group">';
    echo '<label for="' . $key . '" class="form-label">' . str_replace('_', ' ', $key) . '</label>';
    if (is_bool($value)) {
        echo '<div class="form-check form-switch">';
        echo '<input type="checkbox" class="form-check-input" id="' . $key . '" 
                     name="settings[' . $key . ']"' . ($value ? ' checked' : '') . '>';
        echo '</div>';
    } else {
        echo '<input type="text" class="form-control" id="' . $key . '" 
                     name="settings[' . $key . ']" value="' . htmlspecialchars($value) . '">';
    }
    echo '</div>';
    echo '</div>';
}

// Output form fields
foreach ($settings as $key => $value): ?>
    <div class="col-md-6">
        <div class="form-group">
            <label for="<?= $key ?>" class="form-label"><?= str_replace('_', ' ', $key) ?></label>
            <?php if (is_bool($value)): ?>
                <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="<?= $key ?>" 
                           name="settings[<?= $key ?>]" <?= $value ? 'checked' : '' ?>>
                </div>
            <?php else: ?>
                <input type="text" class="form-control" id="<?= $key ?>" 
                       name="settings[<?= $key ?>]" value="<?= htmlspecialchars($value) ?>">
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>
}
?>

<form method="post" action="" class="settings-form">
    <input type="hidden" name="settings_tab" value="system">
    <div class="professional-form-section mb-4">
        <h6 class="border-bottom pb-2">Environment Settings</h6>
        
        <div class="row g-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="ENVIRONMENT" class="form-label">Environment</label>
                    <select class="form-select" id="ENVIRONMENT" name="settings[ENVIRONMENT]" required>
                        <option value="dev" <?= $settings['ENVIRONMENT'] === 'dev' ? 'selected' : '' ?>>Development</option>
                        <option value="staging" <?= $settings['ENVIRONMENT'] === 'staging' ? 'selected' : '' ?>>Staging</option>
                        <option value="prod" <?= $settings['ENVIRONMENT'] === 'prod' ? 'selected' : '' ?>>Production</option>
                    </select>
                    <div class="form-text">Current application environment</div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="DEBUG_MODE" class="form-label">Debug Mode</label>
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="DEBUG_MODE" name="settings[DEBUG_MODE]" 
                               <?= $settings['DEBUG_MODE'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="DEBUG_MODE">Enable debug mode</label>
                    </div>
                    <div class="form-text">Shows detailed error messages and debug information</div>
                </div>
            </div>
        </div>
    </div>

    <div class="professional-form-section mb-4">
        <h6 class="border-bottom pb-2">Error Handling</h6>
        
        <div class="row g-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="ERROR_REPORTING" class="form-label">Error Reporting Level</label>
                    <select class="form-select" id="ERROR_REPORTING" name="settings[ERROR_REPORTING]">
                        <option value="<?= E_ALL ?>" <?= $settings['ERROR_REPORTING'] === E_ALL ? 'selected' : '' ?>>All Errors</option>
                        <option value="<?= E_ALL & ~E_NOTICE ?>" <?= $settings['ERROR_REPORTING'] === (E_ALL & ~E_NOTICE) ? 'selected' : '' ?>>All Errors (except notices)</option>
                        <option value="<?= E_ERROR | E_PARSE ?>" <?= $settings['ERROR_REPORTING'] === (E_ERROR | E_PARSE) ? 'selected' : '' ?>>Fatal Errors Only</option>
                    </select>
                    <div class="form-text">Level of error reporting for PHP</div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Error Display Settings</label>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="DISPLAY_ERRORS" name="settings[DISPLAY_ERRORS]" 
                               <?= $settings['DISPLAY_ERRORS'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="DISPLAY_ERRORS">Display Errors</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="LOG_ERRORS" name="settings[LOG_ERRORS]" 
                               <?= $settings['LOG_ERRORS'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="LOG_ERRORS">Log Errors</label>
                    </div>
                </div>
            </div>
            
            <div class="col-12">
                <div class="form-group">
                    <label for="ERROR_LOG_PATH" class="form-label">Error Log Path</label>
                    <input type="text" class="form-control" id="ERROR_LOG_PATH" name="settings[ERROR_LOG_PATH]" 
                           value="<?= htmlspecialchars($settings['ERROR_LOG_PATH']) ?>" required>
                    <div class="form-text">Absolute path to the error log file</div>
                </div>
            </div>
        </div>
    </div>

    <div class="professional-form-section mb-4">
        <h6 class="border-bottom pb-2">Localization</h6>
        
        <div class="row g-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="TIMEZONE" class="form-label">Default Timezone</label>
                    <select class="form-select" id="TIMEZONE" name="settings[TIMEZONE]" required>
                        <?php
                        $timezones = DateTimeZone::listIdentifiers();
                        foreach ($timezones as $timezone) {
                            $selected = $settings['TIMEZONE'] === $timezone ? 'selected' : '';
                            echo "<option value=\"$timezone\" $selected>$timezone</option>";
                        }
                        ?>
                    </select>
                    <div class="form-text">System default timezone</div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="CHARSET" class="form-label">Character Set</label>
                    <input type="text" class="form-control" id="CHARSET" name="settings[CHARSET]" 
                           value="<?= htmlspecialchars($settings['CHARSET']) ?>" required>
                    <div class="form-text">Default character encoding</div>
                </div>
            </div>
        </div>
    </div>
</form>
