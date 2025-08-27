<?php
include_once 'admin/assets/includes/main.php';

echo "<h1>Manual Database Fix for Default Theme</h1>";

try {
    // Direct SQL to add missing default theme records
    echo "<h2>Adding Default Theme Records...</h2>";
    
    $areas = ['admin', 'public', 'client_portal'];
    
    foreach ($areas as $area) {
        // Check if record already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM setting_branding_templates WHERE template_key = 'default' AND area = ?");
        $stmt->execute([$area]);
        $exists = $stmt->fetch()['count'] > 0;
        
        if (!$exists) {
            // Insert the record
            $stmt = $pdo->prepare("INSERT INTO setting_branding_templates (template_key, area, is_active) VALUES ('default', ?, 0)");
            $result = $stmt->execute([$area]);
            
            if ($result) {
                echo "<p>✓ Added default theme for {$area}</p>";
            } else {
                echo "<p>✗ Failed to add default theme for {$area}</p>";
            }
        } else {
            echo "<p>- Default theme for {$area} already exists</p>";
        }
    }
    
    // Show all themes now
    echo "<h2>All Theme Records:</h2>";
    $stmt = $pdo->query("SELECT id, template_key, area, is_active FROM setting_branding_templates ORDER BY template_key, area");
    $all = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Theme</th><th>Area</th><th>Active</th></tr>";
    foreach ($all as $row) {
        $bg = $row['is_active'] ? "background-color: lightgreen;" : "";
        echo "<tr style='{$bg}'>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['template_key']}</td>";
        echo "<td>{$row['area']}</td>";
        echo "<td>{$row['is_active']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test the theme selection
    echo "<h2>Test Theme Selection:</h2>";
    include_once 'assets/includes/theme-loader.php';
    
    $active_theme = getActiveTheme('admin');
    echo "<p>Currently active admin theme: <strong>{$active_theme}</strong></p>";
    
    // Count themes by template_key
    echo "<h2>Theme Count by Type:</h2>";
    $stmt = $pdo->query("SELECT template_key, COUNT(*) as count FROM setting_branding_templates GROUP BY template_key ORDER BY template_key");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<p>{$row['template_key']}: {$row['count']} records</p>";
    }
    
    echo "<p><a href='admin/settings/branding_settings_tabbed.php?tab=visual-themes'>Test Visual Themes Now</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace: " . $e->getTraceAsString() . "</p>";
}
?>
