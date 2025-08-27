<?php
include_once 'admin/assets/includes/main.php';
include_once 'assets/includes/theme-loader.php';

echo "<h1>Theme CSS Debug - HTML Source Check</h1>";

echo "<h2>Theme Loader Output:</h2>";
echo "<pre>";
ob_start();
loadActiveThemeCSS('admin');
$css_output = ob_get_clean();
echo htmlspecialchars($css_output);
echo "</pre>";

echo "<h2>Active Theme:</h2>";
echo "<p>" . getActiveTheme('admin') . "</p>";

echo "<h2>Direct CSS Link Test:</h2>";
$active_theme = getActiveTheme('admin');
$css_url = "http://localhost/gws-universal-hybrid-app/public_html/assets/css/themes/admin-branding-{$active_theme}.css";
echo "<p><a href='{$css_url}' target='_blank'>Click to test CSS file directly: {$css_url}</a></p>";

echo "<h2>File System Check:</h2>";
$css_file = "D:\\XAMPP\\htdocs\\gws-universal-hybrid-app\\public_html\\assets\\css\\themes\\admin-branding-{$active_theme}.css";
echo "<p>File exists: " . (file_exists($css_file) ? "YES" : "NO") . "</p>";
echo "<p>File path: {$css_file}</p>";

if (file_exists($css_file)) {
    echo "<h2>CSS File Content (first 300 chars):</h2>";
    echo "<pre>" . htmlspecialchars(substr(file_get_contents($css_file), 0, 300)) . "...</pre>";
}
?>
