<?php
include_once 'admin/assets/includes/main.php';

echo "<h1>Add Missing Default Theme</h1>";

try {
    // Check what themes currently exist in database
    echo "<h2>Current Database Themes:</h2>";
    $stmt = $pdo->query("SELECT template_key, area, is_active FROM setting_branding_templates ORDER BY area, template_key");
    $existing = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1'>";
    echo "<tr><th>Theme</th><th>Area</th><th>Active</th></tr>";
    foreach ($existing as $row) {
        echo "<tr><td>{$row['template_key']}</td><td>{$row['area']}</td><td>{$row['is_active']}</td></tr>";
    }
    echo "</table>";
    
    // Check if default theme exists
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM setting_branding_templates WHERE template_key = 'default'");
    $default_count = $stmt->fetch()['count'];
    
    echo "<h2>Default Theme Status:</h2>";
    echo "<p>Default theme records found: {$default_count}</p>";
    
    if ($default_count == 0) {
        echo "<p>Creating missing default theme records...</p>";
        
        $areas = ['admin', 'public', 'client_portal'];
        foreach ($areas as $area) {
            $stmt = $pdo->prepare("INSERT INTO setting_branding_templates (template_key, area, is_active) VALUES ('default', ?, 0)");
            $stmt->execute([$area]);
            echo "<p>✓ Created default theme for {$area} area</p>";
        }
        
        echo "<p>✅ Default theme records created successfully!</p>";
    } else {
        echo "<p>Default theme already exists</p>";
    }
    
    // Also check if the CSS file exists
    $css_file = "D:\\XAMPP\\htdocs\\gws-universal-hybrid-app\\public_html\\assets\\css\\themes\\admin-branding-default.css";
    echo "<h2>CSS File Check:</h2>";
    echo "<p>Default CSS file exists: " . (file_exists($css_file) ? "✓ YES" : "✗ NO") . "</p>";
    echo "<p>File path: {$css_file}</p>";
    
    if (!file_exists($css_file)) {
        echo "<p>⚠️ Need to create default CSS file</p>";
    }
    
    // Show final database state
    echo "<h2>Updated Database State:</h2>";
    $stmt = $pdo->query("SELECT template_key, area, is_active FROM setting_branding_templates ORDER BY template_key, area");
    $all_themes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1'>";
    echo "<tr><th>Theme</th><th>Area</th><th>Active</th></tr>";
    foreach ($all_themes as $row) {
        $highlight = $row['is_active'] ? "style='background-color: lightgreen'" : "";
        echo "<tr {$highlight}><td>{$row['template_key']}</td><td>{$row['area']}</td><td>{$row['is_active']}</td></tr>";
    }
    echo "</table>";
    
    echo "<p><a href='admin/settings/branding_settings_tabbed.php?tab=visual-themes'>Test Visual Themes</a></p>";
    
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
