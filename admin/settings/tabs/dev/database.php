<?php
// Load current settings
$settings_file = __DIR__ . '/../data/database_settings.php';
$default_settings = [
    'DB_HOST' => 'localhost',
    'DB_PORT' => '3306',
    'DB_NAME' => '',
    'DB_USER' => '',
    'DB_PASS' => '',
    'DB_CHARSET' => 'utf8mb4',
    'DB_COLLATION' => 'utf8mb4_unicode_ci',
    'DB_PREFIX' => 'gws_',
    'DB_BACKUP_PATH' => PROJECT_ROOT . '/private/backups/database'
];

if (file_exists($settings_file)) {
    $settings = include($settings_file);
    $settings = array_merge($default_settings, $settings);
} else {
    $settings = $default_settings;
}

foreach ($settings as $key => $value): ?>
    <div class="col-md-6">
        <div class="form-group">
            <label for="<?= $key ?>" class="form-label"><?= str_replace('_', ' ', $key) ?></label>
            <input type="<?= $key === 'DB_PASS' ? 'password' : 'text' ?>" 
                   class="form-control" 
                   id="<?= $key ?>" 
                   name="settings[<?= $key ?>]" 
                   value="<?= htmlspecialchars($value) ?>">
        </div>
    </div>
<?php endforeach; ?>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="DB_HOST" class="form-label">Database Host</label>
                    <input type="text" class="form-control" id="DB_HOST" name="settings[DB_HOST]" 
                           value="<?= htmlspecialchars($settings['DB_HOST']) ?>" required>
                    <div class="form-text">Database server hostname</div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="DB_PORT" class="form-label">Database Port</label>
                    <input type="text" class="form-control" id="DB_PORT" name="settings[DB_PORT]" 
                           value="<?= htmlspecialchars($settings['DB_PORT']) ?>" required>
                    <div class="form-text">Database server port</div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="DB_NAME" class="form-label">Database Name</label>
                    <input type="text" class="form-control" id="DB_NAME" name="settings[DB_NAME]" 
                           value="<?= htmlspecialchars($settings['DB_NAME']) ?>" required>
                    <div class="form-text">Name of the database</div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="DB_USER" class="form-label">Database User</label>
                    <input type="text" class="form-control" id="DB_USER" name="settings[DB_USER]" 
                           value="<?= htmlspecialchars($settings['DB_USER']) ?>" required>
                    <div class="form-text">Database username</div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="DB_PASS" class="form-label">Database Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="DB_PASS" name="settings[DB_PASS]" 
                               value="<?= htmlspecialchars($settings['DB_PASS']) ?>" required>
                        <button type="button" class="btn btn-primary" onclick="togglePassword('DB_PASS')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="form-text">Database password</div>
                </div>
            </div>
        </div>
    </div>

    <div class="professional-form-section mb-4">
        <h6 class="border-bottom pb-2">Database Configuration</h6>
        
        <div class="row g-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="DB_CHARSET" class="form-label">Character Set</label>
                    <input type="text" class="form-control" id="DB_CHARSET" name="settings[DB_CHARSET]" 
                           value="<?= htmlspecialchars($settings['DB_CHARSET']) ?>" required>
                    <div class="form-text">Database character set</div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="DB_COLLATION" class="form-label">Collation</label>
                    <input type="text" class="form-control" id="DB_COLLATION" name="settings[DB_COLLATION]" 
                           value="<?= htmlspecialchars($settings['DB_COLLATION']) ?>" required>
                    <div class="form-text">Database collation</div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="DB_PREFIX" class="form-label">Table Prefix</label>
                    <input type="text" class="form-control" id="DB_PREFIX" name="settings[DB_PREFIX]" 
                           value="<?= htmlspecialchars($settings['DB_PREFIX']) ?>" required>
                    <div class="form-text">Prefix for database tables</div>
                </div>
            </div>
            
            <div class="col-12">
                <div class="form-group">
                    <label for="DB_BACKUP_PATH" class="form-label">Backup Directory</label>
                    <input type="text" class="form-control" id="DB_BACKUP_PATH" name="settings[DB_BACKUP_PATH]" 
                           value="<?= htmlspecialchars($settings['DB_BACKUP_PATH']) ?>" required>
                    <div class="form-text">Path for database backups</div>
                </div>
            </div>
        </div>
    </div>
</form>
