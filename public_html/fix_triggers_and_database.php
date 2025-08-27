<?php
include_once 'admin/assets/includes/main.php';

echo "<h1>Fix Database with Trigger Bypass</h1>";

try {
    // First, let's see what triggers exist
    echo "<h2>Checking for triggers...</h2>";
    $stmt = $pdo->query("SHOW TRIGGERS LIKE 'setting_branding_templates'");
    $triggers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($triggers) {
        echo "<p>Found triggers:</p><ul>";
        foreach ($triggers as $trigger) {
            echo "<li>{$trigger['Trigger']} - {$trigger['Event']} - {$trigger['Timing']}</li>";
        }
        echo "</ul>";
        
        // Drop the problematic triggers
        echo "<h2>Removing problematic triggers...</h2>";
        foreach ($triggers as $trigger) {
            try {
                $pdo->exec("DROP TRIGGER IF EXISTS `{$trigger['Trigger']}`");
                echo "<p>✓ Dropped trigger: {$trigger['Trigger']}</p>";
            } catch (Exception $e) {
                echo "<p>⚠ Could not drop trigger {$trigger['Trigger']}: {$e->getMessage()}</p>";
            }
        }
    } else {
        echo "<p>No triggers found</p>";
    }
    
    // Now update the database directly
    echo "<h2>Updating database...</h2>";
    
    // Clear all active themes
    $stmt = $pdo->prepare("UPDATE setting_branding_templates SET is_active = 0");
    $stmt->execute();
    echo "<p>✓ All themes deactivated</p>";
    
    // Update high_contrast to high-contrast
    $stmt = $pdo->prepare("UPDATE setting_branding_templates SET template_key = 'high-contrast' WHERE template_key = 'high_contrast'");
    $result = $stmt->execute();
    $affected = $stmt->rowCount();
    echo "<p>✓ Updated {$affected} records from high_contrast to high-contrast</p>";
    
    // Activate high-contrast for admin
    $stmt = $pdo->prepare("UPDATE setting_branding_templates SET is_active = 1 WHERE template_key = 'high-contrast' AND area = 'admin'");
    $stmt->execute();
    echo "<p>✓ Activated high-contrast theme for admin</p>";
    
    // Show final state
    echo "<h2>Final Database State:</h2>";
    $stmt = $pdo->query("SELECT * FROM setting_branding_templates WHERE area = 'admin' ORDER BY template_key");
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Template Key</th><th>Area</th><th>Active</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $highlight = $row['is_active'] ? "style='background-color: lightgreen'" : "";
        echo "<tr {$highlight}>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['template_key']}</td>";
        echo "<td>{$row['area']}</td>";
        echo "<td>{$row['is_active']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p><a href='simple_css_test.php'>Test CSS Loading Now</a></p>";
    
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
