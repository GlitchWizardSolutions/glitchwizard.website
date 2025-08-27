<?php
// PHP Environment Diagnostic Script
// This will help determine your current PHP setup and what you need

echo "<!DOCTYPE html>\n";
echo "<html><head><title>PHP Environment Check</title>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .pass { color: green; font-weight: bold; }
    .fail { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style></head><body>";

echo "<h1>🔍 PHP Environment Diagnostic</h1>";

// Basic PHP Info
echo "<div class='section'>";
echo "<h2>📋 Basic PHP Information</h2>";
echo "<table>";
echo "<tr><th>Setting</th><th>Value</th><th>Status</th></tr>";

// PHP Version
$php_version = phpversion();
$version_ok = version_compare($php_version, '7.4', '>=');
echo "<tr><td>PHP Version</td><td>{$php_version}</td><td class='" . ($version_ok ? 'pass' : 'fail') . "'>";
echo $version_ok ? "✅ Good (7.4+)" : "❌ Too Old (Need 7.4+)";
echo "</td></tr>";

// Server Software
echo "<tr><td>Server Software</td><td>" . $_SERVER['SERVER_SOFTWARE'] . "</td><td class='pass'>ℹ️ Info</td></tr>";

// Document Root
echo "<tr><td>Document Root</td><td>" . $_SERVER['DOCUMENT_ROOT'] . "</td><td class='pass'>ℹ️ Info</td></tr>";

echo "</table>";
echo "</div>";

// Required Extensions Check
echo "<div class='section'>";
echo "<h2>🔧 Required PHP Extensions</h2>";
echo "<table>";
echo "<tr><th>Extension</th><th>Status</th><th>Notes</th></tr>";

$required_extensions = [
    'pdo' => 'Required for database connections',
    'pdo_mysql' => 'Required for MySQL database',
    'mysqli' => 'Alternative database option',
    'curl' => 'Required for external API calls',
    'json' => 'Required for JSON handling',
    'mbstring' => 'Required for string handling',
    'openssl' => 'Required for secure connections',
    'fileinfo' => 'Required for file uploads',
    'gd' => 'Required for image processing',
    'zip' => 'Useful for file compression',
    'session' => 'Required for user sessions'
];

foreach ($required_extensions as $ext => $note) {
    $loaded = extension_loaded($ext);
    echo "<tr><td>{$ext}</td><td class='" . ($loaded ? 'pass' : 'fail') . "'>";
    echo $loaded ? "✅ Loaded" : "❌ Missing";
    echo "</td><td>{$note}</td></tr>";
}

echo "</table>";
echo "</div>";

// PHP Configuration
echo "<div class='section'>";
echo "<h2>⚙️ PHP Configuration</h2>";
echo "<table>";
echo "<tr><th>Setting</th><th>Current Value</th><th>Recommended</th><th>Status</th></tr>";

$config_checks = [
    'memory_limit' => ['current' => ini_get('memory_limit'), 'recommended' => '256M', 'min' => '128M'],
    'max_execution_time' => ['current' => ini_get('max_execution_time'), 'recommended' => '60', 'min' => '30'],
    'post_max_size' => ['current' => ini_get('post_max_size'), 'recommended' => '64M', 'min' => '32M'],
    'upload_max_filesize' => ['current' => ini_get('upload_max_filesize'), 'recommended' => '64M', 'min' => '16M'],
    'max_input_vars' => ['current' => ini_get('max_input_vars'), 'recommended' => '5000', 'min' => '1000']
];

foreach ($config_checks as $setting => $info) {
    $current = $info['current'];
    $recommended = $info['recommended'];
    
    // Simple comparison (this is basic, real comparison would be more complex)
    $status = $current >= $info['min'] ? 'pass' : 'warning';
    
    echo "<tr><td>{$setting}</td><td>{$current}</td><td>{$recommended}</td>";
    echo "<td class='{$status}'>" . ($status == 'pass' ? '✅ OK' : '⚠️ Low') . "</td></tr>";
}

echo "</table>";
echo "</div>";

// Database Connection Test
echo "<div class='section'>";
echo "<h2>🗄️ Database Connection Test</h2>";

if (extension_loaded('pdo') && extension_loaded('pdo_mysql')) {
    echo "<p class='pass'>✅ PDO and PDO_MySQL extensions are available!</p>";
    echo "<p>Your application should work correctly with these extensions.</p>";
} else {
    echo "<p class='fail'>❌ PDO extensions are missing!</p>";
    echo "<p><strong>Action Required:</strong> Enable PDO and PDO_MySQL extensions in cPanel.</p>";
    
    if (extension_loaded('mysqli')) {
        echo "<p class='warning'>⚠️ MySQLi is available as a fallback option.</p>";
    }
}

echo "</div>";

// Recommendations
echo "<div class='section'>";
echo "<h2>💡 Recommendations</h2>";

if (!$version_ok) {
    echo "<p class='fail'><strong>🚨 Critical:</strong> Update PHP to version 7.4 or higher (8.1+ recommended)</p>";
}

if (!extension_loaded('pdo')) {
    echo "<p class='fail'><strong>🚨 Critical:</strong> Enable PDO extension in cPanel → PHP Extensions</p>";
}

if (!extension_loaded('pdo_mysql')) {
    echo "<p class='fail'><strong>🚨 Critical:</strong> Enable PDO_MySQL extension in cPanel → PHP Extensions</p>";
}

$memory_limit = ini_get('memory_limit');
if (preg_match('/(\d+)/', $memory_limit, $matches) && $matches[1] < 128) {
    echo "<p class='warning'><strong>⚠️ Recommended:</strong> Increase memory_limit to 256M in cPanel → PHP Configuration</p>";
}

echo "</div>";

// Next Steps
echo "<div class='section'>";
echo "<h2>🎯 Next Steps for cPanel</h2>";
echo "<ol>";
echo "<li><strong>Go to cPanel → Software → Select PHP Version</strong></li>";
echo "<li><strong>Select PHP 8.1 or 8.2</strong> (recommended for best performance)</li>";
echo "<li><strong>Go to Extensions tab</strong></li>";
echo "<li><strong>Enable these extensions:</strong>";
echo "<ul>";
echo "<li>✅ pdo</li>";
echo "<li>✅ pdo_mysql</li>";
echo "<li>✅ curl</li>";
echo "<li>✅ mbstring</li>";
echo "<li>✅ gd</li>";
echo "<li>✅ fileinfo</li>";
echo "</ul></li>";
echo "<li><strong>Click 'Save'</strong></li>";
echo "<li><strong>Test your website again</strong></li>";
echo "</ol>";
echo "</div>";

// System Info
echo "<div class='section'>";
echo "<h2>🖥️ System Information</h2>";
echo "<p><strong>Generated:</strong> " . date('Y-m-d H:i:s T') . "</p>";
echo "<p><strong>User Agent:</strong> " . $_SERVER['HTTP_USER_AGENT'] . "</p>";
echo "</div>";

echo "</body></html>";
?>
