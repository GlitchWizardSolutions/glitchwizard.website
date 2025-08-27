<?php
// Image Helper Functions
// Provides access to centralized image settings including alt text

function get_image_config() {
    static $image_config = null;
    
    if ($image_config === null) {
        // Load the image configuration
        $config_file = $_SERVER['DOCUMENT_ROOT'] . '/admin/settings/public_image_settings_config.php';
        if (file_exists($config_file)) {
            include $config_file;
            $image_config = $images ?? [];
        } else {
            $image_config = [];
        }
    }
    
    return $image_config;
}

function get_image_alt($image_key, $default = '') {
    $config = get_image_config();
    return $config[$image_key]['alt'] ?? $default;
}

function get_image_path($image_key, $default = '') {
    $config = get_image_config();
    return $config[$image_key]['path'] ?? $default;
}

function get_image_data($image_key) {
    $config = get_image_config();
    return $config[$image_key] ?? null;
}
?>
