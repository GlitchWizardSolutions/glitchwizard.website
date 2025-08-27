<?php
require_once 'private/gws-universal-config.php';

try {
    $stmt = $pdo->query('SELECT template_key, template_name, is_active FROM setting_branding_templates ORDER BY template_key');
    echo "Available templates in database:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- Key: '{$row['template_key']}', Name: '{$row['template_name']}', Active: " . ($row['is_active'] ? 'Yes' : 'No') . "\n";
    }
    
    echo "\nChecking if 'default' template exists:\n";
    $stmt = $pdo->prepare('SELECT * FROM setting_branding_templates WHERE template_key = ?');
    $stmt->execute(['default']);
    $default_template = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($default_template) {
        echo "✅ Default template found:\n";
        print_r($default_template);
    } else {
        echo "❌ Default template NOT found in database\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
