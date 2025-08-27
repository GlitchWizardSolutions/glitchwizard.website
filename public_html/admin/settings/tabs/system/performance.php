<?php
// Process form submission if POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: Process form submission
    // Will implement form processing logic here
}

// Get current settings
$default_settings = [
    'enable_caching' => true,
    'cache_lifetime' => 3600,
    'minify_html' => true,
    'minify_css' => true,
    'minify_js' => true,
    'gzip_compression' => true,
    'database_optimization' => true,
    'query_cache_size' => 64,
    'max_execution_time' => 30,
    'memory_limit' => 256,
    'enable_cdn' => false,
    'cdn_url' => '',
    'lazy_loading' => true
];

// Load saved settings if they exist
$settings_file = dirname(dirname(__DIR__)) . '/tabs/system/data/performance_settings.php';
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
    <input type="hidden" name="settings_tab" value="performance">
    <div class="professional-form-section">
        <h3>Caching Settings</h3>
        
        <div class="form-group">
            <label for="enable_caching">
                Enable Caching
                <div class="form-text">Enable system-wide caching for better performance.</div>
            </label>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="enable_caching" name="settings[enable_caching]" 
                       <?= $settings['enable_caching'] ? 'checked' : '' ?>>
            </div>
        </div>

        <div class="form-group">
            <label for="cache_lifetime">Cache Lifetime (seconds)</label>
            <input type="number" class="form-control" id="cache_lifetime" name="settings[cache_lifetime]" 
                   value="<?= htmlspecialchars($settings['cache_lifetime']) ?>" min="300" max="86400">
            <div class="form-text">How long to keep cached items before refreshing.</div>
        </div>
    </div>

    <div class="professional-form-section">
        <h3>Resource Optimization</h3>
        
        <div class="form-group">
            <label for="minify_html">
                Minify HTML
                <div class="form-text">Remove unnecessary whitespace from HTML output.</div>
            </label>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="minify_html" name="settings[minify_html]" 
                       <?= $settings['minify_html'] ? 'checked' : '' ?>>
            </div>
        </div>

        <div class="form-group">
            <label for="minify_css">
                Minify CSS
                <div class="form-text">Compress CSS files for faster loading.</div>
            </label>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="minify_css" name="settings[minify_css]" 
                       <?= $settings['minify_css'] ? 'checked' : '' ?>>
            </div>
        </div>

        <div class="form-group">
            <label for="minify_js">
                Minify JavaScript
                <div class="form-text">Compress JavaScript files for faster loading.</div>
            </label>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="minify_js" name="settings[minify_js]" 
                       <?= $settings['minify_js'] ? 'checked' : '' ?>>
            </div>
        </div>

        <div class="form-group">
            <label for="gzip_compression">
                GZIP Compression
                <div class="form-text">Enable GZIP compression for faster page loading.</div>
            </label>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="gzip_compression" name="settings[gzip_compression]" 
                       <?= $settings['gzip_compression'] ? 'checked' : '' ?>>
            </div>
        </div>
    </div>

    <div class="professional-form-section">
        <h3>Database Performance</h3>
        
        <div class="form-group">
            <label for="database_optimization">
                Database Optimization
                <div class="form-text">Automatically optimize database tables periodically.</div>
            </label>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="database_optimization" 
                       name="settings[database_optimization]" <?= $settings['database_optimization'] ? 'checked' : '' ?>>
            </div>
        </div>

        <div class="form-group">
            <label for="query_cache_size">Query Cache Size (MB)</label>
            <input type="number" class="form-control" id="query_cache_size" name="settings[query_cache_size]" 
                   value="<?= htmlspecialchars($settings['query_cache_size']) ?>" min="16" max="256">
            <div class="form-text">Size of the MySQL query cache in megabytes.</div>
        </div>
    </div>

    <div class="professional-form-section">
        <h3>PHP Settings</h3>
        
        <div class="form-group">
            <label for="max_execution_time">Maximum Execution Time (seconds)</label>
            <input type="number" class="form-control" id="max_execution_time" name="settings[max_execution_time]" 
                   value="<?= htmlspecialchars($settings['max_execution_time']) ?>" min="10" max="300">
            <div class="form-text">Maximum time a script can run before timing out.</div>
        </div>

        <div class="form-group">
            <label for="memory_limit">Memory Limit (MB)</label>
            <input type="number" class="form-control" id="memory_limit" name="settings[memory_limit]" 
                   value="<?= htmlspecialchars($settings['memory_limit']) ?>" min="64" max="512">
            <div class="form-text">Maximum memory a script can consume.</div>
        </div>
    </div>

    <div class="professional-form-section">
        <h3>Content Delivery</h3>
        
        <div class="form-group">
            <label for="enable_cdn">
                Enable CDN
                <div class="form-text">Use Content Delivery Network for static assets.</div>
            </label>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="enable_cdn" name="settings[enable_cdn]" 
                       <?= $settings['enable_cdn'] ? 'checked' : '' ?>>
            </div>
        </div>

        <div class="form-group">
            <label for="cdn_url">CDN URL</label>
            <input type="url" class="form-control" id="cdn_url" name="settings[cdn_url]" 
                   value="<?= htmlspecialchars($settings['cdn_url']) ?>" 
                   placeholder="https://cdn.example.com">
            <div class="form-text">Base URL for your CDN (if enabled).</div>
        </div>

        <div class="form-group">
            <label for="lazy_loading">
                Image Lazy Loading
                <div class="form-text">Load images only when they come into view.</div>
            </label>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="lazy_loading" name="settings[lazy_loading]" 
                       <?= $settings['lazy_loading'] ? 'checked' : '' ?>>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save Performance Settings</button>
        <button type="reset" class="btn btn-secondary">Reset</button>
    </div>
</form>
