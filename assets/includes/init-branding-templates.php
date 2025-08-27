<?php
/**
 * Initialize Branding Templates in Database
 * 
 * This script populates the setting_branding_templates table with
 * the available CSS variations for public pages.
 */

// Include database configuration
require_once __DIR__ . '/private/gws-master-config.php';

try {
    $settingsManager = new SettingsManager($pdo);
    
    // Define branding templates
    $templates = [
        [
            'template_key' => 'default',
            'template_name' => 'Default',
            'template_description' => 'Standard GWS Universal Public branding with clean, professional styling',
            'css_class' => 'branding-default',
            'is_active' => true,
            'template_config' => json_encode([
                'css_file' => 'public-branding.css',
                'style' => 'professional',
                'contrast' => 'medium',
                'intensity' => 'moderate'
            ]),
            'preview_image' => 'assets/branding/previews/default.jpg'
        ],
        [
            'template_key' => 'high_contrast',
            'template_name' => 'High Contrast',
            'template_description' => 'Accessibility-focused design with high contrast and bold elements for improved readability',
            'css_class' => 'branding-high-contrast',
            'is_active' => false,
            'template_config' => json_encode([
                'css_file' => 'public-branding-high-contrast.css',
                'style' => 'accessibility',
                'contrast' => 'high',
                'intensity' => 'strong',
                'accessibility_features' => [
                    'high_contrast_text',
                    'bold_borders',
                    'enhanced_focus_states',
                    'clear_visual_hierarchy'
                ]
            ]),
            'preview_image' => 'assets/branding/previews/high-contrast.jpg'
        ],
        [
            'template_key' => 'subtle',
            'template_name' => 'Subtle',
            'template_description' => 'Minimal, understated branding for elegant and refined appearance',
            'css_class' => 'branding-subtle',
            'is_active' => false,
            'template_config' => json_encode([
                'css_file' => 'public-branding-subtle.css',
                'style' => 'minimal',
                'contrast' => 'low',
                'intensity' => 'light',
                'features' => [
                    'soft_shadows',
                    'minimal_borders',
                    'subtle_hover_effects',
                    'clean_typography'
                ]
            ]),
            'preview_image' => 'assets/branding/previews/subtle.jpg'
        ],
        [
            'template_key' => 'bold',
            'template_name' => 'Bold',
            'template_description' => 'Strong, vibrant design with gradient backgrounds and dramatic effects for maximum impact',
            'css_class' => 'branding-bold',
            'is_active' => false,
            'template_config' => json_encode([
                'css_file' => 'public-branding-bold.css',
                'style' => 'dramatic',
                'contrast' => 'high',
                'intensity' => 'maximum',
                'features' => [
                    'gradient_backgrounds',
                    'bold_typography',
                    'animated_effects',
                    'strong_shadows',
                    'vibrant_colors'
                ]
            ]),
            'preview_image' => 'assets/branding/previews/bold.jpg'
        ],
        [
            'template_key' => 'casual',
            'template_name' => 'Casual',
            'template_description' => 'Friendly, approachable design with rounded corners and playful elements',
            'css_class' => 'branding-casual',
            'is_active' => false,
            'template_config' => json_encode([
                'css_file' => 'public-branding-casual.css',
                'style' => 'friendly',
                'contrast' => 'medium',
                'intensity' => 'playful',
                'features' => [
                    'rounded_corners',
                    'playful_animations',
                    'friendly_typography',
                    'emoji_integration',
                    'soft_hover_effects'
                ]
            ]),
            'preview_image' => 'assets/branding/previews/casual.jpg'
        ]
    ];
    
    echo "<h2>Initializing Branding Templates...</h2>\n";
    
    // Check if table exists
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'setting_branding_templates'");
    if ($tableCheck->rowCount() == 0) {
        echo "<p style='color: red;'>❌ Error: setting_branding_templates table does not exist!</p>\n";
        echo "<p>Please run the database schema first: database_settings_schema.sql</p>\n";
        exit;
    }
    
    // Clear existing templates
    $pdo->exec("DELETE FROM setting_branding_templates");
    echo "<p>🗑️ Cleared existing templates</p>\n";
    
    // Insert new templates
    $stmt = $pdo->prepare("
        INSERT INTO setting_branding_templates 
        (template_key, template_name, template_description, css_class, is_active, template_config, preview_image)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($templates as $template) {
        $stmt->execute([
            $template['template_key'],
            $template['template_name'],
            $template['template_description'],
            $template['css_class'],
            $template['is_active'],
            $template['template_config'],
            $template['preview_image']
        ]);
        
        $status = $template['is_active'] ? '✅ Active' : '⚪ Available';
        echo "<p>$status Added: <strong>{$template['template_name']}</strong> - {$template['template_description']}</p>\n";
    }
    
    echo "<br><h3>✅ Branding Templates Initialized Successfully!</h3>\n";
    echo "<p><strong>Next Steps:</strong></p>\n";
    echo "<ul>\n";
    echo "<li>Visit the admin settings to select your preferred branding template</li>\n";
    echo "<li>Test each template on your public pages</li>\n";
    echo "<li>Customize template configurations as needed</li>\n";
    echo "</ul>\n";
    
    // Show current active template
    $activeTemplate = $pdo->query("SELECT template_name FROM setting_branding_templates WHERE is_active = 1")->fetchColumn();
    echo "<p><strong>Current Active Template:</strong> $activeTemplate</p>\n";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>\n";
    echo "<p>Please check your database connection and ensure the settings tables exist.</p>\n";
}
?>
