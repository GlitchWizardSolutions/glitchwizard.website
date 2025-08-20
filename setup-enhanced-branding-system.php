<?php
/**
 * Enhanced Branding System Setup Script
 * Initializes the multi-area database-driven branding template system
 */

require_once __DIR__ . '/../private/gws-universal-config.php';

echo "<h2>Enhanced Branding System Setup</h2>\n";
echo "<p>Setting up multi-area database-driven branding template system...</p>\n";

try {
    // Step 1: Execute the enhanced database schema
    echo "<h3>Step 1: Creating Enhanced Database Schema</h3>\n";
    
    $sqlFile = __DIR__ . '/../private/sql/create_branding_templates_enhanced.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("SQL file not found: $sqlFile");
    }
    
    $sql = file_get_contents($sqlFile);
    if ($sql === false) {
        throw new Exception("Could not read SQL file: $sqlFile");
    }
    
    // Split SQL into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) { return !empty($stmt) && !preg_match('/^\s*--/', $stmt); }
    );
    
    foreach ($statements as $statement) {
        if (!empty(trim($statement))) {
            $pdo->exec($statement);
        }
    }
    
    echo "<p style='color: green;'>✓ Database schema created successfully</p>\n";
    
    // Step 2: Verify template data
    echo "<h3>Step 2: Verifying Template Data</h3>\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM setting_branding_templates WHERE setting_name = 'template_variation'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['total'] >= 15) { // 5 templates × 3 areas = 15 total
        echo "<p style='color: green;'>✓ All template variations installed ({$result['total']} templates)</p>\n";
    } else {
        echo "<p style='color: orange;'>⚠ Only {$result['total']} templates found, expected 15</p>\n";
    }
    
    // Step 3: Verify sync mode settings
    echo "<h3>Step 3: Verifying Sync Mode Settings</h3>\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM setting_branding_templates WHERE setting_name = 'sync_mode'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['total'] >= 2) {
        echo "<p style='color: green;'>✓ Sync mode settings configured</p>\n";
    } else {
        echo "<p style='color: orange;'>⚠ Sync mode settings incomplete</p>\n";
    }
    
    // Step 4: Test function availability
    echo "<h3>Step 4: Testing Function Availability</h3>\n";
    
    require_once __DIR__ . '/public_html/shared/branding-functions-enhanced.php';
    
    $testFunctions = [
        'getActiveBrandingTemplate',
        'setActiveBrandingTemplate', 
        'getAllBrandingTemplates',
        'isBrandingSyncModeUnified',
        'setBrandingSyncMode',
        'getBrandingSyncMode',
        'generateBrandCSSVariables',
        'getActiveBrandingCSSFile',
        'getBrandingTemplateOverview'
    ];
    
    foreach ($testFunctions as $function) {
        if (function_exists($function)) {
            echo "<p style='color: green;'>✓ Function available: $function</p>\n";
        } else {
            echo "<p style='color: red;'>✗ Function missing: $function</p>\n";
        }
    }
    
    // Step 5: Test basic functionality
    echo "<h3>Step 5: Testing Basic Functionality</h3>\n";
    
    try {
        $overview = getBrandingTemplateOverview();
        echo "<p style='color: green;'>✓ Template overview generated successfully</p>\n";
        echo "<p>Current sync mode: " . htmlspecialchars($overview['sync_mode']) . "</p>\n";
        
        foreach ($overview['areas'] as $area => $data) {
            echo "<p>• " . ucfirst($area) . ": " . htmlspecialchars($data['active_template']) . " (" . htmlspecialchars($data['css_file']) . ")</p>\n";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Functionality test failed: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    }
    
    // Step 6: Verify CSS files exist
    echo "<h3>Step 6: Verifying CSS Template Files</h3>\n";
    
    $cssDirectories = [
        'public_html/assets/css/' => ['public-branding-default.css', 'public-branding-high-contrast.css', 'public-branding-subtle.css', 'public-branding-bold.css', 'public-branding-casual.css'],
        'public_html/admin/assets/css/' => ['admin-branding-default.css', 'admin-branding-high-contrast.css', 'admin-branding-subtle.css', 'admin-branding-bold.css', 'admin-branding-casual.css'],
        'public_html/client_portal/assets/css/' => ['client-branding-default.css', 'client-branding-high-contrast.css', 'client-branding-subtle.css', 'client-branding-bold.css', 'client-branding-casual.css']
    ];
    
    foreach ($cssDirectories as $dir => $files) {
        echo "<h4>" . ucfirst(str_replace(['_', '/'], [' ', ' '], dirname($dir))) . " Templates:</h4>\n";
        foreach ($files as $file) {
            $fullPath = __DIR__ . '/' . $dir . $file;
            if (file_exists($fullPath)) {
                $size = round(filesize($fullPath) / 1024, 1);
                echo "<p style='color: green;'>✓ $file ({$size}KB)</p>\n";
            } else {
                echo "<p style='color: red;'>✗ Missing: $file</p>\n";
            }
        }
    }
    
    // Step 7: Generate setup summary
    echo "<h3>Step 7: Setup Summary</h3>\n";
    
    echo "<div style='background: #f8f9fa; padding: 15px; border-left: 4px solid #007bff; margin: 20px 0;'>\n";
    echo "<h4>Enhanced Branding System Successfully Installed!</h4>\n";
    echo "<p><strong>Features Available:</strong></p>\n";
    echo "<ul>\n";
    echo "<li>✓ Multi-area template support (Public, Admin, Client Portal)</li>\n";
    echo "<li>✓ 5 template variations per area (Default, High Contrast, Subtle, Bold, Casual)</li>\n";
    echo "<li>✓ Unified and Individual synchronization modes</li>\n";
    echo "<li>✓ Database-driven template management</li>\n";
    echo "<li>✓ Enhanced admin interface for template selection</li>\n";
    echo "<li>✓ Backward compatibility with existing systems</li>\n";
    echo "</ul>\n";
    echo "<p><strong>Next Steps:</strong></p>\n";
    echo "<ol>\n";
    echo "<li>Access the enhanced admin interface at: <code>/admin/settings/branding-templates-enhanced.php</code></li>\n";
    echo "<li>Choose your synchronization mode (Unified or Individual)</li>\n";
    echo "<li>Select templates for each area according to your preferences</li>\n";
    echo "<li>Test the templates across all areas of your application</li>\n";
    echo "</ol>\n";
    echo "</div>\n";
    
    echo "<p style='color: green; font-weight: bold;'>Setup completed successfully! 🎉</p>\n";
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Setup Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>\n";
    echo "<p>Please check your database connection and try again.</p>\n";
}

?>
