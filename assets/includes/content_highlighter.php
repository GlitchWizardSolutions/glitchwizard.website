<?php
/**
 * CONTENT HIGHLIGHTER SYSTEM
 * 
 * Development tool to identify non-updatable content
 * Shows red highlighting around hardcoded content when ENVIRONMENT is set to 'dev'
 * 
 * Usage: Set ENVIRONMENT to 'dev' in private/gws-universal-config.php
 * Will automatically highlight content on all pages
 */

// Check if development mode is enabled via environment setting
$dev_mode = (defined('ENVIRONMENT') && ENVIRONMENT === 'dev');

/**
 * Mark hardcoded content with red highlighting
 * Usage: echo highlight_content("Hardcoded text", "optional-label");
 */
function highlight_content($content, $label = '') {
    global $dev_mode;
    
    if ($dev_mode) {
        $label_text = $label ? " data-label=\"{$label}\"" : "";
        return "<span class=\"dev-highlight\"{$label_text}>{$content}</span>";
    }
    
    return $content;
}

/**
 * Mark content that should be settings-integrated
 * Usage: echo settings_todo("Current hardcoded text", "settings_variable_name");
 */
function settings_todo($content, $settings_var = '') {
    global $dev_mode;
    
    if ($dev_mode) {
        $var_text = $settings_var ? " data-settings=\"\${$settings_var}\"" : "";
        return "<span class=\"dev-settings-todo\"{$var_text} title=\"TODO: Move to settings\">{$content}</span>";
    }
    
    return $content;
}

/**
 * Check if content is properly integrated with settings
 * Usage: echo check_integration($settings_var, "Fallback hardcoded text");
 */
function check_integration($settings_content, $fallback_content) {
    global $dev_mode;
    
    if (empty($settings_content) || $settings_content === $fallback_content) {
        // Content is not properly integrated
        if ($dev_mode) {
            return "<span class=\"dev-not-integrated\" title=\"Settings integration needed\">{$fallback_content}</span>";
        }
        return $fallback_content;
    }
    
    // Content is properly integrated
    if ($dev_mode) {
        return "<span class=\"dev-integrated\" title=\"✓ Settings integrated\">{$settings_content}</span>";
    }
    
    return $settings_content;
}

// Function to highlight hardcoded content in output
function highlight_hardcoded_content($content) {
    global $dev_mode;
    
    if (!$dev_mode) {
        return $content;
    }
    
    // Check for common hardcoded content patterns
    $hardcoded_patterns = [
        'RSS Feed', 'XML Sitemap', 'Accessibility Policy', 'Terms of Service', 'Privacy Policy',
        'All Rights Reserved', 'Useful Links', 'Our Services', 'Contact Us', 'About Us',
        'Welcome', 'Learn More', 'Click Here', 'Read More', 'Get Started', 'View More',
        'Copyright', '© Copyright', 'Design by', 'Powered by'
    ];
    
    foreach ($hardcoded_patterns as $pattern) {
        // Look for the pattern in text content (not in attributes or URLs)
        $content = preg_replace(
            '/(\>)([^<]*' . preg_quote($pattern, '/') . '[^<]*)(\<)/i',
            '$1<span class="dev-highlight" title="Hardcoded text: ' . $pattern . ' - should use settings variable">$2</span>$3',
            $content
        );
    }
    
    return $content;
}

// Function to check if page content should be highlighted
function should_highlight_page() {
    global $dev_mode;
    return $dev_mode;
}

// Start output buffering to capture and modify content
if ($dev_mode) {
    ob_start('highlight_hardcoded_content');
}

// Inject CSS for development mode highlighting
if ($dev_mode) {
    echo '<style>
        .dev-highlight {
            background-color: #ff4444 !important;
            color: white !important;
            padding: 2px 4px !important;
            border-radius: 3px !important;
            position: relative !important;
            font-weight: bold !important;
        }
        
        .dev-settings-todo {
            background-color: #ff6b35 !important;
            color: white !important;
            padding: 2px 4px !important;
            border-radius: 3px !important;
            border: 2px dashed #ff0000 !important;
            position: relative !important;
            cursor: help !important;
        }
        
        .dev-not-integrated {
            background-color: #dc3545 !important;
            color: white !important;
            padding: 2px 4px !important;
            border-radius: 3px !important;
            border: 2px solid #ff0000 !important;
            position: relative !important;
            cursor: help !important;
            animation: pulse 2s infinite !important;
        }
        
        .dev-integrated {
            background-color: #28a745 !important;
            color: white !important;
            padding: 1px 3px !important;
            border-radius: 2px !important;
            border: 1px solid #155724 !important;
            font-size: 0.9em !important;
            cursor: help !important;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }
        
        /* Development mode indicator */
        body::before {
            content: "🔧 DEVELOPMENT MODE - Content Integration Checker Active";
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #ff4444;
            color: white;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            z-index: 9999;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }
        
        body {
            padding-top: 50px !important;
        }
        
        /* Legend */
        .dev-legend {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(0,0,0,0.9);
            color: white;
            padding: 15px;
            border-radius: 8px;
            font-size: 12px;
            z-index: 9998;
            max-width: 300px;
        }
        
        .dev-legend h6 {
            margin: 0 0 10px 0;
            color: #ffc107;
        }
        
        .dev-legend div {
            margin: 5px 0;
        }
    </style>';
    
    // Add legend
    echo '<div class="dev-legend">
        <h6>🔍 Content Integration Legend</h6>
        <div><span class="dev-highlight">Red</span> = Hardcoded content</div>
        <div><span class="dev-settings-todo">Orange</span> = Needs settings integration</div>
        <div><span class="dev-not-integrated">Pulsing Red</span> = Not integrated properly</div>
        <div><span class="dev-integrated">Green</span> = Properly integrated</div>
    </div>';
}

// Development mode console log
if ($dev_mode) {
    echo '<script>
        console.log("🔧 Development Mode Active - Content Integration Checker");
        console.log("📋 Legend:");
        console.log("🔴 Red highlight = Hardcoded content that should be moved to settings");
        console.log("🟠 Orange dashed = Content marked for settings integration");
        console.log("🔴 Pulsing red = Settings not properly integrated");
        console.log("🟢 Green = Content properly integrated with settings");
        console.log("💡 Set ENVIRONMENT to \'prod\' in gws-universal-config.php to disable this mode");
    </script>';
}
?>
