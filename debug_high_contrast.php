<?php
require_once '../../private/gws-universal-config.php';
require_once '../assets/includes/branding-functions.php';

echo "Testing high_contrast theme activation...\n";

// Check if high_contrast template exists
$stmt = $pdo->prepare('SELECT * FROM setting_branding_templates WHERE template_key = ?');
$stmt->execute(['high_contrast']);
$high_contrast_template = $stmt->fetch(PDO::FETCH_ASSOC);

if ($high_contrast_template) {
    echo "✅ high_contrast template found in database\n";
    echo "Template details:\n";
    print_r($high_contrast_template);
    
    // Try to activate it
    echo "\nAttempting activation...\n";
    $result = setActiveBrandingTemplate('high_contrast');
    
    if ($result) {
        echo "✅ Successfully activated high_contrast theme!\n";
    } else {
        echo "❌ Failed to activate high_contrast theme\n";
        
        // Debug: Check what went wrong
        echo "\nDebugging...\n";
        
        // Check if any templates are active
        $stmt = $pdo->query('SELECT template_key, is_active FROM setting_branding_templates WHERE is_active = 1');
        $active_templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Currently active templates: " . count($active_templates) . "\n";
        foreach ($active_templates as $template) {
            echo "- {$template['template_key']}\n";
        }
    }
} else {
    echo "❌ high_contrast template NOT found in database\n";
    
    // Show what templates do exist
    echo "\nAvailable templates:\n";
    $stmt = $pdo->query('SELECT template_key, template_name FROM setting_branding_templates ORDER BY template_key');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['template_key']}: {$row['template_name']}\n";
    }
}
?>
