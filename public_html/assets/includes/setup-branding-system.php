<?php
/**
 * Branding Template System Setup
 * 
 * This script sets up the complete branding template system for public pages
 */

// Include database configuration
require_once dirname(__DIR__, 4) . '/private/gws-universal-config.php';

echo "<h1>🎨 GWS Universal Public Branding Template System Setup</h1>\n";
echo "<hr>\n";

// Step 1: Check if database tables exist
echo "<h2>Step 1: Database Schema Check</h2>\n";

try {
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'setting_branding_templates'");
    if ($tableCheck->rowCount() == 0) {
        echo "<p style='color: red;'>❌ Required database tables not found!</p>\n";
        echo "<p><strong>Action Required:</strong> Please run the database schema file first:</p>\n";
        echo "<pre>mysql -u [username] -p [database_name] < database_settings_schema.sql</pre>\n";
        echo "<p>The schema file should be in your project root directory.</p>\n";
        exit;
    } else {
        echo "<p style='color: green;'>✅ Database tables found</p>\n";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection error: " . $e->getMessage() . "</p>\n";
    exit;
}

// Step 2: Initialize branding templates
echo "<h2>Step 2: Initialize Branding Templates</h2>\n";

try {
    require_once dirname(__DIR__, 4) . '/private/classes/SettingsManager.php';
    
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
            'template_description' => 'Accessibility-focused design with high contrast and bold elements',
            'css_class' => 'branding-high-contrast',
            'is_active' => false,
            'template_config' => json_encode([
                'css_file' => 'public-branding-high-contrast.css',
                'style' => 'accessibility',
                'contrast' => 'high',
                'intensity' => 'strong',
                'features' => ['high_contrast_text', 'bold_borders', 'enhanced_focus_states']
            ]),
            'preview_image' => 'assets/branding/previews/high-contrast.jpg'
        ],
        [
            'template_key' => 'subtle',
            'template_name' => 'Subtle',
            'template_description' => 'Minimal, understated branding for elegant appearance',
            'css_class' => 'branding-subtle',
            'is_active' => false,
            'template_config' => json_encode([
                'css_file' => 'public-branding-subtle.css',
                'style' => 'minimal',
                'contrast' => 'low',
                'intensity' => 'light',
                'features' => ['soft_shadows', 'minimal_borders', 'subtle_hover_effects']
            ]),
            'preview_image' => 'assets/branding/previews/subtle.jpg'
        ],
        [
            'template_key' => 'bold',
            'template_name' => 'Bold',
            'template_description' => 'Strong, vibrant design with dramatic effects for maximum impact',
            'css_class' => 'branding-bold',
            'is_active' => false,
            'template_config' => json_encode([
                'css_file' => 'public-branding-bold.css',
                'style' => 'dramatic',
                'contrast' => 'high',
                'intensity' => 'maximum',
                'features' => ['gradient_backgrounds', 'bold_typography', 'animated_effects']
            ]),
            'preview_image' => 'assets/branding/previews/bold.jpg'
        ],
        [
            'template_key' => 'casual',
            'template_name' => 'Casual',
            'template_description' => 'Friendly, approachable design with playful elements',
            'css_class' => 'branding-casual',
            'is_active' => false,
            'template_config' => json_encode([
                'css_file' => 'public-branding-casual.css',
                'style' => 'friendly',
                'contrast' => 'medium',
                'intensity' => 'playful',
                'features' => ['rounded_corners', 'playful_animations', 'emoji_integration']
            ]),
            'preview_image' => 'assets/branding/previews/casual.jpg'
        ]
    ];
    
    // Clear existing templates
    $pdo->exec("DELETE FROM setting_branding_templates");
    
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
        echo "<p>$status {$template['template_name']} template initialized</p>\n";
    }
    
    echo "<p style='color: green;'>✅ All branding templates initialized successfully!</p>\n";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error initializing templates: " . $e->getMessage() . "</p>\n";
}

// Step 3: Initialize basic business identity if not exists
echo "<h2>Step 3: Basic Business Identity Setup</h2>\n";

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM setting_business_identity");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        $pdo->exec("
            INSERT INTO setting_business_identity 
            (business_name_short, business_name_medium, business_name_long, business_tagline_short, business_tagline_medium)
            VALUES 
            ('GWS', 'GWS Universal', 'GWS Universal Public', 'Professional Solutions', 'Professional business solutions and services')
        ");
        echo "<p style='color: green;'>✅ Basic business identity created</p>\n";
    } else {
        echo "<p style='color: blue;'>ℹ️ Business identity already exists</p>\n";
    }
} catch (Exception $e) {
    echo "<p style='color: orange;'>⚠️ Business identity setup skipped: " . $e->getMessage() . "</p>\n";
}

// Step 4: Initialize basic branding colors if not exists
echo "<h2>Step 4: Basic Branding Colors Setup</h2>\n";

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM setting_branding_colors");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        $pdo->exec("
            INSERT INTO setting_branding_colors 
            (brand_primary_color, brand_secondary_color, brand_accent_color, brand_background_color, brand_text_color)
            VALUES 
            ('#3671c9', '#2fc090', '#c93667', '#ffffff', '#333333')
        ");
        echo "<p style='color: green;'>✅ Basic branding colors created</p>\n";
    } else {
        echo "<p style='color: blue;'>ℹ️ Branding colors already exist</p>\n";
    }
} catch (Exception $e) {
    echo "<p style='color: orange;'>⚠️ Branding colors setup skipped: " . $e->getMessage() . "</p>\n";
}

// Step 5: Verify file structure
echo "<h2>Step 5: File Structure Verification</h2>\n";

$required_files = [
    'public_html/assets/css/public-branding.css',
    'public_html/assets/css/public-branding-high-contrast.css',
    'public_html/assets/css/public-branding-subtle.css',
    'public_html/assets/css/public-branding-bold.css',
    'public_html/assets/css/public-branding-casual.css',
    'public_html/assets/includes/branding-functions.php',
    'public_html/admin/settings/branding-templates.php'
];

$project_root = dirname(__DIR__, 4);
foreach ($required_files as $file) {
    $full_path = $project_root . '/' . $file;
    if (file_exists($full_path)) {
        echo "<p style='color: green;'>✅ {$file}</p>\n";
    } else {
        echo "<p style='color: red;'>❌ Missing: {$file}</p>\n";
    }
}

// Summary
echo "<hr>\n";
echo "<h2>🎉 Setup Complete!</h2>\n";
echo "<div style='background: #e7f3ff; padding: 15px; border-left: 4px solid #0066cc; margin: 20px 0;'>\n";
echo "<h3>✅ What's Been Set Up:</h3>\n";
echo "<ul>\n";
echo "<li><strong>5 Branding Templates:</strong> Default, High Contrast, Subtle, Bold, Casual</li>\n";
echo "<li><strong>Database Integration:</strong> Template settings stored in database</li>\n";
echo "<li><strong>Admin Interface:</strong> Template selection and management</li>\n";
echo "<li><strong>Dynamic CSS Loading:</strong> Templates applied automatically</li>\n";
echo "<li><strong>Legacy Compatibility:</strong> Maintains existing functionality</li>\n";
echo "</ul>\n";
echo "</div>\n";

echo "<div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0;'>\n";
echo "<h3>🚀 Next Steps:</h3>\n";
echo "<ol>\n";
echo "<li><strong>Visit Admin Interface:</strong> <a href='admin/settings/branding-templates.php'>admin/settings/branding-templates.php</a></li>\n";
echo "<li><strong>Select Your Template:</strong> Choose the branding style that fits your needs</li>\n";
echo "<li><strong>Test Public Pages:</strong> View your site to see the template in action</li>\n";
echo "<li><strong>Customize as Needed:</strong> Modify templates or create new ones</li>\n";
echo "</ol>\n";
echo "</div>\n";

echo "<div style='background: #d1ecf1; padding: 15px; border-left: 4px solid #0c5460; margin: 20px 0;'>\n";
echo "<h3>📋 Template Descriptions:</h3>\n";
echo "<ul>\n";
echo "<li><strong>Default:</strong> Professional, clean styling (currently active)</li>\n";
echo "<li><strong>High Contrast:</strong> Accessibility-focused with enhanced visibility</li>\n";
echo "<li><strong>Subtle:</strong> Minimal, understated elegance</li>\n";
echo "<li><strong>Bold:</strong> Vibrant, high-impact design with gradients</li>\n";
echo "<li><strong>Casual:</strong> Friendly, approachable with playful elements</li>\n";
echo "</ul>\n";
echo "</div>\n";

echo "<p style='text-align: center; margin-top: 30px;'>\n";
echo "<strong>The GWS Universal Public branding template system is now ready to use!</strong>\n";
echo "</p>\n";

?>
