<?php
// Load current settings
$settings_file = __DIR__ . '/../data/urls_settings.php';
$default_settings = [
    'BASE_URL' => '',
    'SITE_URL' => '',
    'ADMIN_URL' => '',
    'ASSETS_URL' => '',
    'UPLOADS_URL' => '',
    'API_URL' => '',
    'COOKIE_PATH' => '/',
    'COOKIE_DOMAIN' => '',
    'PUBLIC_PATH' => PROJECT_ROOT . '/public_html',
    'PRIVATE_PATH' => PROJECT_ROOT . '/private',
    'TEMP_PATH' => PROJECT_ROOT . '/private/temp',
    'LOG_PATH' => PROJECT_ROOT . '/private/logs'
];

if (file_exists($settings_file)) {
    $settings = include($settings_file);
    if (!is_array($settings)) $settings = [];
    $settings = array_merge($default_settings, $settings);
} else {
    $settings = $default_settings;
}

?>

<form method="post" action="" class="settings-form">
    <input type="hidden" name="settings_tab" value="urls">
    <div class="professional-form-section mb-4">
        <h6 class="border-bottom pb-2">URL Configuration</h6>
        
        <div class="row g-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="BASE_URL" class="form-label">Base URL</label>
                    <input type="url" class="form-control" id="BASE_URL" name="settings[BASE_URL]" 
                           value="<?= htmlspecialchars($settings['BASE_URL']) ?>" required>
                    <div class="form-text">Root URL of the application (e.g., https://example.com)</div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="SITE_URL" class="form-label">Site URL</label>
                    <input type="url" class="form-control" id="SITE_URL" name="settings[SITE_URL]" 
                           value="<?= htmlspecialchars($settings['SITE_URL']) ?>" required>
                    <div class="form-text">Main website URL</div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="ADMIN_URL" class="form-label">Admin URL</label>
                    <input type="url" class="form-control" id="ADMIN_URL" name="settings[ADMIN_URL]" 
                           value="<?= htmlspecialchars($settings['ADMIN_URL']) ?>" required>
                    <div class="form-text">Admin panel URL</div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="ASSETS_URL" class="form-label">Assets URL</label>
                    <input type="url" class="form-control" id="ASSETS_URL" name="settings[ASSETS_URL]" 
                           value="<?= htmlspecialchars($settings['ASSETS_URL']) ?>" required>
                    <div class="form-text">URL for static assets (CSS, JS, images)</div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="UPLOADS_URL" class="form-label">Uploads URL</label>
                    <input type="url" class="form-control" id="UPLOADS_URL" name="settings[UPLOADS_URL]" 
                           value="<?= htmlspecialchars($settings['UPLOADS_URL']) ?>" required>
                    <div class="form-text">URL for uploaded files</div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="API_URL" class="form-label">API URL</label>
                    <input type="url" class="form-control" id="API_URL" name="settings[API_URL]" 
                           value="<?= htmlspecialchars($settings['API_URL']) ?>" required>
                    <div class="form-text">API endpoint base URL</div>
                </div>
            </div>
        </div>
    </div>

    <div class="professional-form-section mb-4">
        <h6 class="border-bottom pb-2">Cookie Settings</h6>
        
        <div class="row g-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="COOKIE_PATH" class="form-label">Cookie Path</label>
                    <input type="text" class="form-control" id="COOKIE_PATH" name="settings[COOKIE_PATH]" 
                           value="<?= htmlspecialchars($settings['COOKIE_PATH']) ?>" required>
                    <div class="form-text">Path where cookies are available</div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="COOKIE_DOMAIN" class="form-label">Cookie Domain</label>
                    <input type="text" class="form-control" id="COOKIE_DOMAIN" name="settings[COOKIE_DOMAIN]" 
                           value="<?= htmlspecialchars($settings['COOKIE_DOMAIN']) ?>">
                    <div class="form-text">Domain where cookies are available (blank for current domain)</div>
                </div>
            </div>
        </div>
    </div>

    <div class="professional-form-section mb-4">
        <h6 class="border-bottom pb-2">File System Paths</h6>
        
        <div class="row g-3">
            <div class="col-12">
                <div class="form-group">
                    <label for="PUBLIC_PATH" class="form-label">Public Path</label>
                    <input type="text" class="form-control" id="PUBLIC_PATH" name="settings[PUBLIC_PATH]" 
                           value="<?= htmlspecialchars($settings['PUBLIC_PATH']) ?>" required>
                    <div class="form-text">Path to public web files</div>
                </div>
            </div>
            
            <div class="col-12">
                <div class="form-group">
                    <label for="PRIVATE_PATH" class="form-label">Private Path</label>
                    <input type="text" class="form-control" id="PRIVATE_PATH" name="settings[PRIVATE_PATH]" 
                           value="<?= htmlspecialchars($settings['PRIVATE_PATH']) ?>" required>
                    <div class="form-text">Path to private application files</div>
                </div>
            </div>
            
            <div class="col-12">
                <div class="form-group">
                    <label for="TEMP_PATH" class="form-label">Temporary Path</label>
                    <input type="text" class="form-control" id="TEMP_PATH" name="settings[TEMP_PATH]" 
                           value="<?= htmlspecialchars($settings['TEMP_PATH']) ?>" required>
                    <div class="form-text">Path for temporary files</div>
                </div>
            </div>
            
            <div class="col-12">
                <div class="form-group">
                    <label for="LOG_PATH" class="form-label">Log Path</label>
                    <input type="text" class="form-control" id="LOG_PATH" name="settings[LOG_PATH]" 
                           value="<?= htmlspecialchars($settings['LOG_PATH']) ?>" required>
                    <div class="form-text">Path for log files</div>
                </div>
            </div>
        </div>
    </div>
</form>
