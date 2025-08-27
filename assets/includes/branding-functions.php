<?php
/**
 * GWS Universal Hybrid App - Branding Functions
 * Clean, efficient branding system with database-driven templates
 * Single source of truth for all branding functionality
 * 
 * @package GWS Universal Hybrid App
 * @version 2.0.0
 * @date August 26, 2025
 */

// Prevent direct access
if (!defined('DB_HOST')) {
    require_once __DIR__ . '/private/gws-master-config.php';
}

/**
 * Get the active branding template for a specific area
 * @param string $area The area to get template for (public, admin, client_portal)
 * @return string The active template name
 */
function getActiveBrandingTemplate($area = 'public') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT template_key FROM setting_branding_templates WHERE area = ? AND is_active = 1 LIMIT 1");
        $stmt->execute([$area]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $result['template_key'] : 'default';
    } catch (Exception $e) {
        error_log("Error getting active branding template for area '{$area}': " . $e->getMessage());
        return 'default';
    }
}

/**
 * Set the active branding template for a specific area
 * @param string $templateValue The template to activate
 * @param string $area The area to set template for (public, admin, client_portal)
 * @return bool Success status
 */
function setActiveBrandingTemplate($templateValue, $area = 'public') {
    global $pdo;
    
    // Simple validation
    $valid_templates = ['default', 'subtle', 'bold', 'casual', 'high_contrast'];
    if (!in_array($templateValue, $valid_templates)) {
        return false;
    }
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Deactivate all templates for this area
        $stmt1 = $pdo->prepare("UPDATE setting_branding_templates SET is_active = 0 WHERE area = ?");
        $stmt1->execute([$area]);
        
        // Activate the selected template
        $stmt2 = $pdo->prepare("UPDATE setting_branding_templates SET is_active = 1 WHERE area = ? AND template_key = ?");
        $stmt2->execute([$area, $templateValue]);
        
        // Check if any row was affected (template exists and was updated)
        if ($stmt2->rowCount() > 0) {
            $pdo->commit();
            return true;
        } else {
            // Template doesn't exist, rollback
            $pdo->rollBack();
            return false;
        }
        
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

/**
 * Get all available branding templates for a specific area
 * @param string $area The area to get templates for
 * @return array Array of template configurations
 */
function getAllBrandingTemplates($area = 'public') {
    return [
        'default' => [
            'name' => 'Professional Default',
            'description' => 'Clean, professional design with balanced colors',
            'css_file' => "{$area}-branding-default.css"
        ],
        'subtle' => [
            'name' => 'Subtle Elegance',
            'description' => 'Refined design with muted tones',
            'css_file' => "{$area}-branding-subtle.css"
        ],
        'bold' => [
            'name' => 'Bold Impact',
            'description' => 'High-contrast design with vibrant colors',
            'css_file' => "{$area}-branding-bold.css"
        ],
        'casual' => [
            'name' => 'Casual Modern',
            'description' => 'Relaxed, approachable design',
            'css_file' => "{$area}-branding-casual.css"
        ],
        'high_contrast' => [
            'name' => 'High Contrast',
            'description' => 'Maximum accessibility with high contrast',
            'css_file' => "{$area}-branding-high-contrast.css"
        ]
    ];
}

/**
 * Get the CSS file path for the active branding template
 * @param string $area The area to get CSS for
 * @return string CSS file path
 */
function getActiveBrandingCSSFile($area = 'public') {
    $active_template = getActiveBrandingTemplate($area);
    $templates = getAllBrandingTemplates($area);
    
    return $templates[$active_template]['css_file'] ?? "{$area}-branding-default.css";
}

/**
 * Get brand colors from database with fallbacks
 * @return array Brand colors and business information
 */
function getBrandingColors() {
    global $pdo;
    
    // Default colors as fallback - Updated to use correct brand colors
    $default_colors = [
        'primary' => '#669999',  // Use database brand_primary_color
        'secondary' => '#b3ced1',  // Use database brand_secondary_color 
        'tertiary' => '#e7b09e',
        'quaternary' => '#2e8b57',
        'accent' => '#ddaa50',
        'warning' => '#ffc107',
        'danger' => '#dc3545',
        'info' => '#17a2b8',
        'success' => '#28a745',
        'error' => '#dc3545',
        'background' => '#ffffff',
        'text' => '#333333',
        'text_light' => '#666666',
        'text_muted' => '#999999',
        'business_name' => '',
        'business_tagline' => '',
        'business_phone' => '',
        'business_email' => '',
        'business_website' => '',
        'business_address' => ''
    ];
    
    try {
        $stmt = $pdo->query("SELECT * FROM setting_branding_colors WHERE id = 1 ORDER BY id LIMIT 1");
        $brand_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Debug logging
        error_log("getBrandingColors() raw database data: " . json_encode($brand_data));
        
        if ($brand_data) {
            // Map database column names to form field names
            $mapped_colors = $default_colors;
            
            if (isset($brand_data['brand_primary_color'])) $mapped_colors['primary'] = $brand_data['brand_primary_color'];
            if (isset($brand_data['brand_secondary_color'])) $mapped_colors['secondary'] = $brand_data['brand_secondary_color'];
            if (isset($brand_data['brand_accent_color'])) $mapped_colors['accent'] = $brand_data['brand_accent_color'];
            if (isset($brand_data['brand_tertiary_color'])) $mapped_colors['tertiary'] = $brand_data['brand_tertiary_color'];
            if (isset($brand_data['brand_quaternary_color'])) $mapped_colors['quaternary'] = $brand_data['brand_quaternary_color'];
            if (isset($brand_data['brand_success_color'])) $mapped_colors['success'] = $brand_data['brand_success_color'];
            if (isset($brand_data['brand_warning_color'])) $mapped_colors['warning'] = $brand_data['brand_warning_color'];
            
            // Debug the mapping
            error_log("getBrandingColors() mapped data: " . json_encode($mapped_colors));
            
            return $mapped_colors;
        }
    } catch (Exception $e) {
        error_log("Error loading brand colors: " . $e->getMessage());
    }
    
    return $default_colors;
}

/**
 * Generate CSS variables for brand colors
 * @param string $area The area to generate variables for
 * @return string CSS variables string
 */
function generateBrandCSSVariables($area = 'public') {
    $colors = getBrandingColors();
    $css_vars = [];
    
    // Generate CSS custom properties
    foreach ($colors as $key => $value) {
        if (!empty($value) && $key !== 'id') {
            $css_key = str_replace('_', '-', $key);
            $css_vars[] = "--brand-{$css_key}: {$value}";
        }
    }
    
    return implode('; ', $css_vars);
}

/**
 * Get business identity information
 * @return array Business identity data
 */
function getBusinessIdentity() {
    global $pdo;
    
    $default_identity = [
        'business_name_short' => 'GWS',
        'business_name_medium' => 'Glitch Wizard Solutions',
        'business_name_full' => 'Glitch Wizard Solutions LLC',
        'business_tagline' => 'Professional Digital Solutions'
    ];
    
    try {
        $stmt = $pdo->query("SELECT * FROM setting_business_identity WHERE id = 1 LIMIT 1");
        $identity_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($identity_data) {
            return array_merge($default_identity, array_filter($identity_data, function($value) {
                return $value !== null && $value !== '';
            }));
        }
    } catch (Exception $e) {
        error_log("Error loading business identity: " . $e->getMessage());
    }
    
    return $default_identity;
}

/**
 * Get comprehensive branding information for an area
 * @param string $area The area to get branding for
 * @return array Complete branding configuration
 */
function getBrandingInfo($area = 'public') {
    return [
        'area' => $area,
        'active_template' => getActiveBrandingTemplate($area),
        'css_file' => getActiveBrandingCSSFile($area),
        'available_templates' => getAllBrandingTemplates($area),
        'colors' => getBrandingColors(),
        'business_identity' => getBusinessIdentity(),
        'css_variables' => generateBrandCSSVariables($area)
    ];
}

/**
 * Apply branding template by generating CSS file
 * @param string $template_name Template to apply
 * @param string $area Area to apply to
 * @return bool Success status
 */
function applyBrandingTemplate($template_name, $area = 'public') {
    $css_file = getActiveBrandingCSSFile($area);
    $css_path = dirname(__DIR__, 2) . "/assets/css/{$css_file}";
    
    // Generate CSS content based on template and colors
    $colors = getBrandingColors();
    $css_content = generateTemplateCSS($template_name, $colors, $area);
    
    // Write CSS file
    $result = file_put_contents($css_path, $css_content);
    
    if ($result !== false) {
        return setActiveBrandingTemplate($template_name, $area);
    }
    
    return false;
}

/**
 * Generate CSS content for a specific template
 * @param string $template Template name
 * @param array $colors Brand colors
 * @param string $area Target area
 * @return string Generated CSS content
 */
function generateTemplateCSS($template, $colors, $area) {
    $css_variables = generateBrandCSSVariables($area);
    
    $css = "/* {$area} Branding CSS - {$template} Template */\n";
    $css .= ":root {\n";
    $css .= "    {$css_variables};\n";
    $css .= "}\n\n";
    
    // Add template-specific styles based on template type
    switch ($template) {
        case 'high_contrast':
            $css .= "/* High Contrast Accessibility Theme */\n";
            $css .= "body { background: #000000; color: #ffffff; }\n";
            $css .= ".btn-primary { background: #ffffff; color: #000000; border: 2px solid #ffffff; }\n";
            $css .= "a { color: #ffff00; }\n";
            break;
            
        case 'bold':
            $css .= "/* Bold Impact Theme */\n";
            $css .= "body { font-weight: 600; }\n";
            $css .= ".btn-primary { font-weight: bold; text-transform: uppercase; }\n";
            break;
            
        case 'subtle':
            $css .= "/* Subtle Elegance Theme */\n";
            $css .= "body { color: #555555; }\n";
            $css .= ".btn-primary { opacity: 0.9; }\n";
            break;
            
        case 'casual':
            $css .= "/* Casual Modern Theme */\n";
            $css .= "body { font-family: 'Segoe UI', sans-serif; }\n";
            $css .= ".btn-primary { border-radius: 25px; }\n";
            break;
            
        default:
            $css .= "/* Professional Default Theme */\n";
            $css .= "/* Standard professional styling */\n";
    }
    
    return $css;
}

// Legacy wrapper functions for backward compatibility with existing code
if (!function_exists('getActiveBrandingTemplate_Simple')) {
    function getActiveBrandingTemplate_Simple() {
        return getActiveBrandingTemplate('public');
    }
}

if (!function_exists('setActiveBrandingTemplate_Simple')) {
    function setActiveBrandingTemplate_Simple($template_key) {
        return setActiveBrandingTemplate($template_key, 'public');
    }
}

if (!function_exists('getAllBrandingTemplates_Simple')) {
    function getAllBrandingTemplates_Simple() {
        return getAllBrandingTemplates('public');
    }
}

?>
