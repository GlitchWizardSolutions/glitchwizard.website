<?php
require_once 'private/gws-universal-config.php';

try {
    // Define the expected themes
    $themes_to_add = [
        [
            'template_key' => 'default',
            'template_name' => 'Classic Professional',
            'template_description' => 'Clean, traditional layout perfect for professional businesses',
            'css_class' => 'theme-classic',
            'template_config' => json_encode(['css_file' => 'public-branding.css']),
            'is_active' => 0
        ],
        [
            'template_key' => 'subtle',
            'template_name' => 'Subtle Elegance',
            'template_description' => 'Minimal, understated design for sophisticated brands',
            'css_class' => 'theme-subtle',
            'template_config' => json_encode(['css_file' => 'public-branding-subtle.css']),
            'is_active' => 0
        ],
        [
            'template_key' => 'bold',
            'template_name' => 'Bold Impact',
            'template_description' => 'Strong, vibrant design for maximum visual impact',
            'css_class' => 'theme-bold',
            'template_config' => json_encode(['css_file' => 'public-branding-bold.css']),
            'is_active' => 0
        ],
        [
            'template_key' => 'casual',
            'template_name' => 'Friendly Casual',
            'template_description' => 'Approachable, relaxed design for friendly businesses',
            'css_class' => 'theme-casual',
            'template_config' => json_encode(['css_file' => 'public-branding-casual.css']),
            'is_active' => 0
        ],
        [
            'template_key' => 'high_contrast',
            'template_name' => 'High Contrast',
            'template_description' => 'Accessible design with strong contrast ratios',
            'css_class' => 'theme-high-contrast',
            'template_config' => json_encode(['css_file' => 'public-branding-high-contrast.css']),
            'is_active' => 0
        ]
    ];

    echo "Adding missing theme templates to database...\n";

    foreach ($themes_to_add as $theme) {
        // Check if theme already exists
        $stmt = $pdo->prepare('SELECT template_key FROM setting_branding_templates WHERE template_key = ?');
        $stmt->execute([$theme['template_key']]);
        
        if ($stmt->fetch()) {
            echo "- Theme '{$theme['template_key']}' already exists, skipping\n";
            continue;
        }

        // Insert new theme
        $stmt = $pdo->prepare('
            INSERT INTO setting_branding_templates 
            (template_key, template_name, template_description, css_class, template_config, is_active, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ');
        
        $result = $stmt->execute([
            $theme['template_key'],
            $theme['template_name'],
            $theme['template_description'],
            $theme['css_class'],
            $theme['template_config'],
            $theme['is_active']
        ]);

        if ($result) {
            echo "✅ Added theme: {$theme['template_key']} - {$theme['template_name']}\n";
        } else {
            echo "❌ Failed to add theme: {$theme['template_key']}\n";
        }
    }

    // Set 'default' as active if no theme is currently active
    $stmt = $pdo->query('SELECT COUNT(*) FROM setting_branding_templates WHERE is_active = 1');
    $active_count = $stmt->fetchColumn();
    
    if ($active_count == 0) {
        $stmt = $pdo->prepare('UPDATE setting_branding_templates SET is_active = 1 WHERE template_key = ?');
        $stmt->execute(['default']);
        echo "✅ Set 'default' theme as active\n";
    }

    echo "\nFinal theme list:\n";
    $stmt = $pdo->query('SELECT template_key, template_name, is_active FROM setting_branding_templates ORDER BY template_key');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $active_indicator = $row['is_active'] ? ' (ACTIVE)' : '';
        echo "- {$row['template_key']}: {$row['template_name']}{$active_indicator}\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
