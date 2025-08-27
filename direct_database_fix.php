<?php
include_once 'admin/assets/includes/main.php';

echo "<h1>Direct Database Fix</h1>";

try {
    // Show all records first
    echo "<h2>All Database Records:</h2>";
    $stmt = $pdo->query("SELECT * FROM setting_branding_templates ORDER BY id");
    $all_records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Template Key</th><th>Area</th><th>Active</th></tr>";
    foreach ($all_records as $row) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['template_key']}</td>";
        echo "<td>{$row['area']}</td>";
        echo "<td>{$row['is_active']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Clear all active themes first
    echo "<h2>Clearing all active themes...</h2>";
    $stmt = $pdo->prepare("UPDATE setting_branding_templates SET is_active = 0");
    $stmt->execute();
    echo "<p>All themes deactivated</p>";
    
    // Check if high-contrast record exists
    $stmt = $pdo->prepare("SELECT * FROM setting_branding_templates WHERE template_key = 'high-contrast' AND area = 'admin'");
    $stmt->execute();
    $exists = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$exists) {
        echo "<p>Creating high-contrast record for admin...</p>";
        $stmt = $pdo->prepare("INSERT INTO setting_branding_templates (template_key, area, is_active) VALUES ('high-contrast', 'admin', 1)");
        $stmt->execute();
        echo "<p>✓ Created high-contrast record</p>";
    } else {
        echo "<p>Activating existing high-contrast record...</p>";
        $stmt = $pdo->prepare("UPDATE setting_branding_templates SET is_active = 1 WHERE template_key = 'high-contrast' AND area = 'admin'");
        $stmt->execute();
        echo "<p>✓ Activated high-contrast theme</p>";
    }
    
    // Verify the change
    echo "<h2>Active Themes After Fix:</h2>";
    $stmt = $pdo->query("SELECT * FROM setting_branding_templates WHERE is_active = 1");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<p>✓ {$row['area']}: {$row['template_key']}</p>";
    }
    
    echo "<p><a href='simple_css_test.php'>Test CSS Loading Now</a></p>";
    
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
