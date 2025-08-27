<?php
/**
 * Quick Brand System Test
 * 
 * This page tests if the brand system is working correctly
 */

echo "<h1>Brand System Test</h1>";

// Test 1: Database connection
echo "<h2>1. Testing Database Connection</h2>";
try {
    require_once '../private/gws-universal-config.php';
    echo "✅ Database connection successful<br>";
    echo "Database Host: " . (defined('DB_HOST') ? DB_HOST : 'Not defined') . "<br>";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    exit;
}

// Test 2: Brand loader
echo "<h2>2. Testing Brand Loader</h2>";
try {
    require_once 'assets/includes/brand_loader.php';
    echo "✅ Brand loader included successfully<br>";
    echo "Primary Color: " . $brand_primary_color . "<br>";
    echo "Secondary Color: " . $brand_secondary_color . "<br>";
} catch (Exception $e) {
    echo "❌ Brand loader failed: " . $e->getMessage() . "<br>";
}

// Test 3: Branding functions
echo "<h2>3. Testing Branding Functions</h2>";
try {
    require_once 'assets/includes/branding-functions.php';
    echo "✅ Branding functions included successfully<br>";
    
    $active_template = getActiveBrandingTemplate();
    if ($active_template) {
        echo "Active Template: " . $active_template['template_name'] . "<br>";
        echo "Template Description: " . $active_template['template_description'] . "<br>";
    } else {
        echo "⚠️ No active template found - this is normal if database tables aren't initialized yet<br>";
    }
    
    $css_file = getActiveBrandingCSSFile();
    echo "CSS File: " . $css_file . "<br>";
    
} catch (Exception $e) {
    echo "❌ Branding functions failed: " . $e->getMessage() . "<br>";
}

// Test 4: CSS Variable Output
echo "<h2>4. Testing CSS Variable Output</h2>";
try {
    echo "✅ CSS Variables generated successfully:<br>";
    echo "<div style='background: #f8f9fa; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
    outputBrandCSS();
    echo "</div>";
} catch (Exception $e) {
    echo "❌ CSS variable output failed: " . $e->getMessage() . "<br>";
}

// Test 5: File existence check
echo "<h2>5. Checking Theme CSS Files</h2>";
$theme_files = [
    'assets/css/public-branding.css',
    'assets/css/public-branding-subtle.css',
    'assets/css/public-branding-bold.css',
    'assets/css/public-branding-casual.css',
    'assets/css/public-branding-high-contrast.css',
    'admin/assets/css/admin-branding.css',
    'client_portal/assets/css/client-branding.css'
];

foreach ($theme_files as $file) {
    if (file_exists($file)) {
        $size = filesize($file);
        echo "✅ {$file} - {$size} bytes<br>";
    } else {
        echo "❌ {$file} - Missing<br>";
    }
}

echo "<h2>6. Summary</h2>";
echo "<p>If all tests above show ✅, your brand system is ready to use!</p>";
echo "<p><a href='admin/theme-selection.php'>→ Go to Theme Selection</a></p>";
echo "<p><a href='brand-theme-test.php'>→ Go to Theme Testing</a></p>";

?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    line-height: 1.6;
}
h1, h2 {
    color: #333;
}
h1 {
    border-bottom: 2px solid #007bff;
    padding-bottom: 10px;
}
h2 {
    color: #007bff;
    margin-top: 30px;
}
</style>
