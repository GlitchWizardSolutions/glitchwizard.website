<?php
// Load current settings
$settings_file = __DIR__ . '/../data/security_settings.php';
$default_settings = [
    'ENCRYPTION_KEY' => '',
    'SESSION_SECURE' => true,
    'SESSION_HTTPONLY' => true,
    'CSRF_PROTECTION' => true,
    'XSS_PROTECTION' => true,
    'SECURE_HEADERS' => true,
    'ALLOWED_HOSTS' => '',
    'API_RATE_LIMIT' => 60,
    'MAX_LOGIN_ATTEMPTS' => 5,
    'LOCKOUT_TIME' => 900,
    'PASSWORD_MIN_LENGTH' => 12,
    'PASSWORD_REQUIRES_SPECIAL' => true,
    'PASSWORD_REQUIRES_NUMBER' => true,
    'PASSWORD_REQUIRES_MIXED' => true
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
        echo '<input type="' . ($key === 'ENCRYPTION_KEY' ? 'password' : 'text') . '" 
                     class="form-control" id="' . $key . '" 
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
                <input type="<?= $key === 'ENCRYPTION_KEY' ? 'password' : 'text' ?>" 
                       class="form-control" id="<?= $key ?>" 
                       name="settings[<?= $key ?>]" value="<?= htmlspecialchars($value) ?>">
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>
if (file_exists($settings_file)) {
    $settings = include($settings_file);
    // Merge with defaults for any missing settings
    $settings = array_merge($default_settings, $settings);
} else {
    $settings = $default_settings;
}
?>

<form method="post" action="" class="settings-form">
    <input type="hidden" name="settings_tab" value="security">
    <div class="professional-form-section mb-4">
        <h6 class="border-bottom pb-2">Core Security</h6>
        
        <div class="row g-3">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="ENCRYPTION_KEY" class="form-label">Encryption Key</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="ENCRYPTION_KEY" name="settings[ENCRYPTION_KEY]" 
                               value="<?= htmlspecialchars($settings['ENCRYPTION_KEY']) ?>" required>
                        <button type="button" class="btn btn-primary" onclick="togglePassword('ENCRYPTION_KEY')">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="generateEncryptionKey()">
                            <i class="fas fa-key"></i> Generate
                        </button>
                    </div>
                    <div class="form-text">Master encryption key for sensitive data</div>
                </div>
            </div>
        </div>
    </div>

    <div class="professional-form-section mb-4">
        <h6 class="border-bottom pb-2">Session Security</h6>
        
        <div class="row g-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Session Cookie Settings</label>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="SESSION_SECURE" name="settings[SESSION_SECURE]" 
                               <?= $settings['SESSION_SECURE'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="SESSION_SECURE">Secure Sessions (HTTPS only)</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="SESSION_HTTPONLY" name="settings[SESSION_HTTPONLY]" 
                               <?= $settings['SESSION_HTTPONLY'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="SESSION_HTTPONLY">HttpOnly Cookie Flag</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="professional-form-section mb-4">
        <h6 class="border-bottom pb-2">Protection Mechanisms</h6>
        
        <div class="row g-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Security Features</label>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="CSRF_PROTECTION" name="settings[CSRF_PROTECTION]" 
                               <?= $settings['CSRF_PROTECTION'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="CSRF_PROTECTION">CSRF Protection</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="XSS_PROTECTION" name="settings[XSS_PROTECTION]" 
                               <?= $settings['XSS_PROTECTION'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="XSS_PROTECTION">XSS Protection</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="SECURE_HEADERS" name="settings[SECURE_HEADERS]" 
                               <?= $settings['SECURE_HEADERS'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="SECURE_HEADERS">Security Headers</label>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="ALLOWED_HOSTS" class="form-label">Allowed Hosts</label>
                    <textarea class="form-control" id="ALLOWED_HOSTS" name="settings[ALLOWED_HOSTS]" 
                              rows="3"><?= htmlspecialchars($settings['ALLOWED_HOSTS']) ?></textarea>
                    <div class="form-text">Comma-separated list of allowed hostnames</div>
                </div>
            </div>
        </div>
    </div>

    <div class="professional-form-section mb-4">
        <h6 class="border-bottom pb-2">Authentication Settings</h6>
        
        <div class="row g-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="MAX_LOGIN_ATTEMPTS" class="form-label">Max Login Attempts</label>
                    <input type="number" class="form-control" id="MAX_LOGIN_ATTEMPTS" name="settings[MAX_LOGIN_ATTEMPTS]" 
                           value="<?= htmlspecialchars($settings['MAX_LOGIN_ATTEMPTS']) ?>" min="1" max="10" required>
                    <div class="form-text">Maximum failed login attempts before lockout</div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="LOCKOUT_TIME" class="form-label">Lockout Time (seconds)</label>
                    <input type="number" class="form-control" id="LOCKOUT_TIME" name="settings[LOCKOUT_TIME]" 
                           value="<?= htmlspecialchars($settings['LOCKOUT_TIME']) ?>" min="300" required>
                    <div class="form-text">Duration of account lockout after max attempts</div>
                </div>
            </div>
        </div>
    </div>

    <div class="professional-form-section mb-4">
        <h6 class="border-bottom pb-2">Password Policy</h6>
        
        <div class="row g-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="PASSWORD_MIN_LENGTH" class="form-label">Minimum Password Length</label>
                    <input type="number" class="form-control" id="PASSWORD_MIN_LENGTH" name="settings[PASSWORD_MIN_LENGTH]" 
                           value="<?= htmlspecialchars($settings['PASSWORD_MIN_LENGTH']) ?>" min="8" required>
                    <div class="form-text">Minimum required password length</div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Password Requirements</label>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="PASSWORD_REQUIRES_SPECIAL" 
                               name="settings[PASSWORD_REQUIRES_SPECIAL]" <?= $settings['PASSWORD_REQUIRES_SPECIAL'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="PASSWORD_REQUIRES_SPECIAL">Require Special Characters</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="PASSWORD_REQUIRES_NUMBER" 
                               name="settings[PASSWORD_REQUIRES_NUMBER]" <?= $settings['PASSWORD_REQUIRES_NUMBER'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="PASSWORD_REQUIRES_NUMBER">Require Numbers</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="PASSWORD_REQUIRES_MIXED" 
                               name="settings[PASSWORD_REQUIRES_MIXED]" <?= $settings['PASSWORD_REQUIRES_MIXED'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="PASSWORD_REQUIRES_MIXED">Require Mixed Case</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function generateEncryptionKey() {
    // Generate a random 32-byte key and convert to hex
    const array = new Uint8Array(32);
    window.crypto.getRandomValues(array);
    const key = Array.from(array, b => b.toString(16).padStart(2, '0')).join('');
    
    document.getElementById('ENCRYPTION_KEY').value = key;
}
</script>
