<?php
try {
    include_once 'assets/includes/main.php';
    echo "<h1>Database Debug</h1>";
    
    // Check if table exists
    $tables = $pdo->query("SHOW TABLES LIKE 'setting_branding_templates'")->fetchAll();
    echo "<p>Table exists: " . (count($tables) > 0 ? "YES" : "NO") . "</p>";
    
    if (count($tables) > 0) {
        // Check table structure
        $columns = $pdo->query("DESCRIBE setting_branding_templates")->fetchAll();
        echo "<h2>Table Structure:</h2><ul>";
        foreach ($columns as $col) {
            echo "<li>{$col['Field']} - {$col['Type']}</li>";
        }
        echo "</ul>";
        
        // Check data
        $count = $pdo->query("SELECT COUNT(*) as cnt FROM setting_branding_templates")->fetch()['cnt'];
        echo "<p>Record count: {$count}</p>";
        
        if ($count > 0) {
            $rows = $pdo->query("SELECT * FROM setting_branding_templates")->fetchAll();
            echo "<h2>All Records:</h2><table border='1'>";
            echo "<tr><th>ID</th><th>Template Key</th><th>Area</th><th>Is Active</th></tr>";
            foreach ($rows as $row) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>{$row['template_key']}</td>";
                echo "<td>{$row['area']}</td>";
                echo "<td>{$row['is_active']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No records found. Let me create the theme records...</p>";
            
            // Insert the theme records
            $themes = ['default', 'subtle', 'bold', 'casual', 'high_contrast'];
            $areas = ['admin', 'public', 'client_portal'];
            
            foreach ($areas as $area) {
                foreach ($themes as $theme) {
                    $is_active = ($theme === 'high_contrast' && $area === 'admin') ? 1 : 0;
                    $stmt = $pdo->prepare("INSERT INTO setting_branding_templates (template_key, area, is_active) VALUES (?, ?, ?)");
                    $stmt->execute([$theme, $area, $is_active]);
                }
            }
            
            echo "<p>Theme records created! High contrast theme set as active for admin.</p>";
            
            // Show the new records
            $rows = $pdo->query("SELECT * FROM setting_branding_templates WHERE area = 'admin'")->fetchAll();
            echo "<h2>Admin Theme Records:</h2><table border='1'>";
            echo "<tr><th>ID</th><th>Template Key</th><th>Area</th><th>Is Active</th></tr>";
            foreach ($rows as $row) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>{$row['template_key']}</td>";
                echo "<td>{$row['area']}</td>";
                echo "<td>{$row['is_active']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p>Table doesn't exist!</p>";
    }
    
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
