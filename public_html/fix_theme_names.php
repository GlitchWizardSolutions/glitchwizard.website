<?php
include_once 'admin/assets/includes/main.php';

echo "<h1>Fix Theme Names</h1>";

try {
    // First check what's currently in the database
    echo "<h2>Current Database State:</h2>";
    $stmt = $pdo->query("SELECT id, template_key, area, is_active FROM setting_branding_templates ORDER BY area, template_key");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<p>ID {$row['id']}: {$row['area']} -> {$row['template_key']} (active: {$row['is_active']})</p>";
    }
    
    // Update the database to use hyphens instead of underscores
    echo "<h2>Updating Theme Names:</h2>";
    
    $stmt = $pdo->prepare("UPDATE setting_branding_templates SET template_key = 'high-contrast' WHERE template_key = 'high_contrast'");
    $result = $stmt->execute();
    $affected = $stmt->rowCount();
    
    echo "<p>Updated {$affected} rows from high_contrast to high-contrast</p>";
    
    // Also make sure high-contrast is active for admin
    $stmt = $pdo->prepare("UPDATE setting_branding_templates SET is_active = 0 WHERE area = 'admin'");
    $stmt->execute();
    
    $stmt = $pdo->prepare("UPDATE setting_branding_templates SET is_active = 1 WHERE template_key = 'high-contrast' AND area = 'admin'");
    $result = $stmt->execute();
    
    echo "<p>Set high-contrast as active for admin area</p>";
    
    // Show updated active themes
    echo "<h2>Updated Active Themes:</h2>";
    $stmt = $pdo->query("SELECT template_key, area, is_active FROM setting_branding_templates WHERE is_active = 1");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<p>✓ Active: {$row['area']} -> {$row['template_key']}</p>";
    }
    
    echo "<p><a href='simple_css_test.php'>Test CSS Loading Again</a></p>";
    echo "<p><a href='debug_css_output.php'>Full Debug Output</a></p>";
    
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
