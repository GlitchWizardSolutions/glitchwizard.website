<?php
/**
 * Enhanced Branding Functions - Multi-Area Database-driven template system
 * Supports individual and unified template selection across public, admin, and client areas
 */

require_once __DIR__ . '/../../private/gws-universal-config.php';

/**
 * Get the currently active branding template for a specific area
 * @param string $area Area to get template for (public, admin, client_portal)
 * @return string Active template variation
 */
function getActiveBrandingTemplate($area = 'public') {
    global $pdo;
    
    try {
        // Check if unified mode is active
        if (isBrandingSyncModeUnified()) {
            // In unified mode, get the public template for all areas
            $stmt = $pdo->prepare("
                SELECT setting_value 
                FROM setting_branding_templates 
                WHERE setting_name = 'template_variation' 
                AND area = 'public'
                AND is_active = TRUE 
                LIMIT 1
            ");
        } else {
            // In individual mode, get the area-specific template
            $stmt = $pdo->prepare("
                SELECT setting_value 
                FROM setting_branding_templates 
                WHERE setting_name = 'template_variation' 
                AND area = ?
                AND is_active = TRUE 
                LIMIT 1
            ");
        }
        
        $stmt->execute(isBrandingSyncModeUnified() ? [] : [$area]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $result['setting_value'] : 'default';
    } catch (Exception $e) {
        error_log("Error fetching active branding template for area $area: " . $e->getMessage());
        return 'default';
    }
}

/**
 * Set the active branding template for a specific area
 * @param string $templateValue Template variation to activate
 * @param string $area Area to set template for (public, admin, client_portal, or 'all' for unified)
 * @return bool Success status
 */
function setActiveBrandingTemplate($templateValue, $area = 'public') {
    global $pdo;
    
    try {
        if ($area === 'all') {
            // Set unified mode and activate template for all areas
            setBrandingSyncMode('unified');
            
            $areas = ['public', 'admin', 'client_portal'];
            foreach ($areas as $currentArea) {
                // Deactivate all templates for this area
                $stmt = $pdo->prepare("
                    UPDATE setting_branding_templates 
                    SET is_active = FALSE 
                    WHERE setting_name = 'template_variation' AND area = ?
                ");
                $stmt->execute([$currentArea]);
                
                // Activate the selected template for this area
                $stmt = $pdo->prepare("
                    UPDATE setting_branding_templates 
                    SET is_active = TRUE 
                    WHERE setting_name = 'template_variation' 
                    AND area = ?
                    AND setting_value = ?
                ");
                $stmt->execute([$currentArea, $templateValue]);
            }
            return true;
        } else {
            // Set individual mode and activate template for specific area
            setBrandingSyncMode('individual');
            
            // First, deactivate all templates for this area
            $stmt = $pdo->prepare("
                UPDATE setting_branding_templates 
                SET is_active = FALSE 
                WHERE setting_name = 'template_variation' AND area = ?
            ");
            $stmt->execute([$area]);
            
            // Then activate the selected template for this area
            $stmt = $pdo->prepare("
                UPDATE setting_branding_templates 
                SET is_active = TRUE 
                WHERE setting_name = 'template_variation' 
                AND area = ?
                AND setting_value = ?
            ");
            return $stmt->execute([$area, $templateValue]);
        }
    } catch (Exception $e) {
        error_log("Error setting active branding template for area $area: " . $e->getMessage());
        return false;
    }
}

/**
 * Get all available branding templates for a specific area
 * @param string $area Area to get templates for
 * @return array List of available templates
 */
function getAllBrandingTemplates($area = 'public') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT setting_value, template_label, template_description, css_file, is_active
            FROM setting_branding_templates 
            WHERE setting_name = 'template_variation' AND area = ?
            ORDER BY setting_value
        ");
        $stmt->execute([$area]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error fetching branding templates for area $area: " . $e->getMessage());
        return [];
    }
}

/**
 * Check if branding sync mode is unified
 * @return bool True if unified mode is active
 */
function isBrandingSyncModeUnified() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT setting_value 
            FROM setting_branding_templates 
            WHERE setting_name = 'sync_mode' 
            AND area = 'global'
            AND is_active = TRUE 
            LIMIT 1
        ");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result && $result['setting_value'] === 'unified';
    } catch (Exception $e) {
        error_log("Error checking branding sync mode: " . $e->getMessage());
        return false; // Default to individual mode
    }
}

/**
 * Set branding synchronization mode
 * @param string $mode 'unified' or 'individual'
 * @return bool Success status
 */
function setBrandingSyncMode($mode) {
    global $pdo;
    
    try {
        // Deactivate all sync modes
        $stmt = $pdo->prepare("
            UPDATE setting_branding_templates 
            SET is_active = FALSE 
            WHERE setting_name = 'sync_mode' AND area = 'global'
        ");
        $stmt->execute();
        
        // Activate the selected mode
        $stmt = $pdo->prepare("
            UPDATE setting_branding_templates 
            SET is_active = TRUE 
            WHERE setting_name = 'sync_mode' 
            AND area = 'global'
            AND setting_value = ?
        ");
        return $stmt->execute([$mode]);
    } catch (Exception $e) {
        error_log("Error setting branding sync mode: " . $e->getMessage());
        return false;
    }
}

/**
 * Get current branding sync mode
 * @return string Current sync mode ('unified' or 'individual')
 */
function getBrandingSyncMode() {
    return isBrandingSyncModeUnified() ? 'unified' : 'individual';
}

/**
 * Generate CSS variables for the current template
 * This replaces the old getBrandCSSVariables() function
 * @param string $area Area to get variables for
 * @return array CSS variables for the current template
 */
function generateBrandCSSVariables($area = 'public') {
    // Get base brand variables from config
    $brandPrimary = defined('BRAND_PRIMARY') ? BRAND_PRIMARY : '#007bff';
    $brandSecondary = defined('BRAND_SECONDARY') ? BRAND_SECONDARY : '#6c757d';
    $brandAccent = defined('BRAND_ACCENT') ? BRAND_ACCENT : '#28a745';
    
    $brandVariables = [
        '--brand-primary' => $brandPrimary,
        '--brand-secondary' => $brandSecondary, 
        '--brand-accent' => $brandAccent,
        '--brand-primary-rgb' => hexToRgb($brandPrimary),
        '--brand-secondary-rgb' => hexToRgb($brandSecondary),
        '--brand-accent-rgb' => hexToRgb($brandAccent)
    ];
    
    // Template-specific modifications could be added here
    $activeTemplate = getActiveBrandingTemplate($area);
    
    switch ($activeTemplate) {
        case 'high-contrast':
            // High contrast mode modifications
            break;
        case 'subtle':
            // Subtle mode modifications
            break;
        case 'bold':
            // Bold mode modifications
            break;
        case 'casual':
            // Casual mode modifications
            break;
        default:
            // Default template - no modifications needed
            break;
    }
    
    return $brandVariables;
}

/**
 * Convert hex color to RGB values
 * @param string $hex Hex color code
 * @return string RGB values (comma-separated)
 */
function hexToRgb($hex) {
    $hex = ltrim($hex, '#');
    
    if (strlen($hex) == 3) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    return "$r, $g, $b";
}

/**
 * Get CSS file path for active template in specific area
 * @param string $area Area to get CSS file for
 * @return string Path to active template CSS file
 */
function getActiveBrandingCSSFile($area = 'public') {
    global $pdo;
    
    try {
        // Check if unified mode is active
        if (isBrandingSyncModeUnified()) {
            // In unified mode, use public template for all areas but with area-specific CSS files
            $stmt = $pdo->prepare("
                SELECT setting_value 
                FROM setting_branding_templates 
                WHERE setting_name = 'template_variation' 
                AND area = 'public'
                AND is_active = TRUE 
                LIMIT 1
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $templateValue = $result ? $result['setting_value'] : 'default';
            
            // Generate area-specific CSS file name
            return getAreaCSSFileName($area, $templateValue);
        } else {
            // In individual mode, get area-specific CSS file
            $stmt = $pdo->prepare("
                SELECT css_file 
                FROM setting_branding_templates 
                WHERE setting_name = 'template_variation' 
                AND area = ?
                AND is_active = TRUE 
                LIMIT 1
            ");
            $stmt->execute([$area]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ? $result['css_file'] : getAreaCSSFileName($area, 'default');
        }
    } catch (Exception $e) {
        error_log("Error fetching active branding CSS file for area $area: " . $e->getMessage());
        return getAreaCSSFileName($area, 'default');
    }
}

/**
 * Generate area-specific CSS file name
 * @param string $area Area name
 * @param string $template Template variation
 * @return string CSS file name
 */
function getAreaCSSFileName($area, $template) {
    $prefix = match($area) {
        'admin' => 'admin-branding',
        'client_portal' => 'client-branding',
        default => 'public-branding'
    };
    
    // For 'default' template, use just the prefix without suffix
    if ($template === 'default') {
        return "{$prefix}.css";
    }
    
    return "{$prefix}-{$template}.css";
}

/**
 * Get template status overview for all areas
 * @return array Status information for all areas
 */
function getBrandingTemplateOverview() {
    $overview = [
        'sync_mode' => getBrandingSyncMode(),
        'areas' => []
    ];
    
    $areas = ['public', 'admin', 'client_portal'];
    
    foreach ($areas as $area) {
        $overview['areas'][$area] = [
            'active_template' => getActiveBrandingTemplate($area),
            'css_file' => getActiveBrandingCSSFile($area),
            'available_templates' => getAllBrandingTemplates($area)
        ];
    }
    
    return $overview;
}

/**
 * Backward compatibility functions for existing header.php
 * These functions maintain compatibility with the existing system
 */

/**
 * Get business identity information
 * @return array Business identity data
 */
function getBusinessIdentity() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM setting_business_identity LIMIT 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return $result;
        }
    } catch (Exception $e) {
        error_log("Error fetching business identity: " . $e->getMessage());
    }
    
    // Fallback to default values
    return [
        'business_name_long' => 'Your Business Name',
        'business_name_short' => 'YBN',
        'business_name_medium' => 'Your Business'
    ];
}

/**
 * Get branding colors from database
 * @return array Branding color data
 */
function getBrandingColors() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM setting_branding_colors LIMIT 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return $result;
        }
    } catch (Exception $e) {
        error_log("Error fetching branding colors: " . $e->getMessage());
    }
    
    // Fallback to default brand colors with ALL fields
    return [
        'brand_primary_color' => '#3b89b0',
        'brand_secondary_color' => '#bf5512',
        'brand_tertiary_color' => '#8B4513',
        'brand_quaternary_color' => '#2E8B57',
        'brand_accent_color' => '#28a745',
        'brand_warning_color' => '#ffc107',
        'brand_danger_color' => '#dc3545',
        'brand_info_color' => '#17a2b8',
        'brand_background_color' => '#ffffff',
        'brand_text_color' => '#333333',
        'brand_text_light' => '#666666',
        'brand_text_muted' => '#999999'
    ];
}

/**
 * Get available fonts for dropdown selection
 * @param string $category Optional font category filter
 * @return array Font options for dropdowns
 */
function getAvailableFonts($category = null) {
    global $pdo;
    
    // Start with popular Google Fonts and system fonts
    $default_fonts = [
        // Sans-serif fonts
        ['name' => 'Inter (Google)', 'family' => 'Inter, system-ui, sans-serif', 'type' => 'google', 'category' => 'sans-serif'],
        ['name' => 'Roboto (Google)', 'family' => 'Roboto, Arial, sans-serif', 'type' => 'google', 'category' => 'sans-serif'],
        ['name' => 'Open Sans (Google)', 'family' => 'Open Sans, Arial, sans-serif', 'type' => 'google', 'category' => 'sans-serif'],
        ['name' => 'Poppins (Google)', 'family' => 'Poppins, Arial, sans-serif', 'type' => 'google', 'category' => 'sans-serif'],
        ['name' => 'Lato (Google)', 'family' => 'Lato, Arial, sans-serif', 'type' => 'google', 'category' => 'sans-serif'],
        ['name' => 'Montserrat (Google)', 'family' => 'Montserrat, Arial, sans-serif', 'type' => 'google', 'category' => 'sans-serif'],
        ['name' => 'Source Sans Pro (Google)', 'family' => 'Source Sans Pro, Arial, sans-serif', 'type' => 'google', 'category' => 'sans-serif'],
        ['name' => 'Arial (System)', 'family' => 'Arial, sans-serif', 'type' => 'system', 'category' => 'sans-serif'],
        ['name' => 'Helvetica (System)', 'family' => 'Helvetica, Arial, sans-serif', 'type' => 'system', 'category' => 'sans-serif'],
        
        // Serif fonts
        ['name' => 'Playfair Display (Google)', 'family' => 'Playfair Display, Georgia, serif', 'type' => 'google', 'category' => 'serif'],
        ['name' => 'Merriweather (Google)', 'family' => 'Merriweather, Georgia, serif', 'type' => 'google', 'category' => 'serif'],
        ['name' => 'Crimson Text (Google)', 'family' => 'Crimson Text, Georgia, serif', 'type' => 'google', 'category' => 'serif'],
        ['name' => 'Lora (Google)', 'family' => 'Lora, Georgia, serif', 'type' => 'google', 'category' => 'serif'],
        ['name' => 'Georgia (System)', 'family' => 'Georgia, serif', 'type' => 'system', 'category' => 'serif'],
        ['name' => 'Times New Roman (System)', 'family' => 'Times New Roman, serif', 'type' => 'system', 'category' => 'serif'],
        
        // Monospace fonts
        ['name' => 'JetBrains Mono (Google)', 'family' => 'JetBrains Mono, Consolas, monospace', 'type' => 'google', 'category' => 'monospace'],
        ['name' => 'Fira Code (Google)', 'family' => 'Fira Code, Consolas, monospace', 'type' => 'google', 'category' => 'monospace'],
        ['name' => 'SF Mono (System)', 'family' => 'SF Mono, Monaco, Consolas, monospace', 'type' => 'system', 'category' => 'monospace'],
        ['name' => 'Consolas (System)', 'family' => 'Consolas, Monaco, monospace', 'type' => 'system', 'category' => 'monospace'],
        
        // Display fonts
        ['name' => 'Oswald (Google)', 'family' => 'Oswald, Arial Black, sans-serif', 'type' => 'google', 'category' => 'display'],
        ['name' => 'Bebas Neue (Google)', 'family' => 'Bebas Neue, Arial Black, sans-serif', 'type' => 'google', 'category' => 'display'],
    ];
    
    try {
        // Get custom uploaded fonts from database
        $custom_fonts = [];
        $stmt = $pdo->query("SELECT font_name, font_family FROM custom_fonts WHERE is_active = 1 ORDER BY font_name");
        $custom_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($custom_result as $font) {
            $custom_fonts[] = [
                'name' => $font['font_name'] . ' (Custom)',
                'family' => $font['font_family'],
                'type' => 'custom',
                'category' => 'custom'
            ];
        }
        
        // Combine default and custom fonts
        $all_fonts = array_merge($default_fonts, $custom_fonts);
        
    } catch (Exception $e) {
        error_log("Error fetching custom fonts: " . $e->getMessage());
        $all_fonts = $default_fonts;
    }
    
    // Filter by category if specified
    if ($category) {
        $all_fonts = array_filter($all_fonts, function($font) use ($category) {
            return $font['category'] === $category;
        });
    }
    
    return $all_fonts;
}

/**
 * Get current font selections
 * @return array Current font selections by role
 */
function getCurrentFontSelections() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("
            SELECT bf.font_role, bf.selected_font_id, cf.font_name, cf.font_family, cf.font_type
            FROM setting_brand_fonts bf
            JOIN setting_custom_fonts cf ON bf.selected_font_id = cf.id
            WHERE cf.is_active = TRUE
        ");
        
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Convert to role-indexed array
        $fonts = [];
        foreach ($result as $row) {
            $fonts[$row['font_role']] = $row;
        }
        
        return $fonts;
    } catch (Exception $e) {
        error_log("Error fetching current font selections: " . $e->getMessage());
        return [];
    }
}

/**
 * Get header menu configuration
 * @return array Header menu data
 */
function getHeaderMenu() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM setting_header_menu ORDER BY menu_order ASC");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($result) {
            return $result;
        }
    } catch (Exception $e) {
        error_log("Error fetching header menu: " . $e->getMessage());
    }
    
    // Fallback to default menu
    return [
        ['menu_title' => 'Home', 'menu_url' => '/', 'menu_order' => 1],
        ['menu_title' => 'About', 'menu_url' => '/about', 'menu_order' => 2],
        ['menu_title' => 'Services', 'menu_url' => '/services', 'menu_order' => 3],
        ['menu_title' => 'Contact', 'menu_url' => '/contact', 'menu_order' => 4]
    ];
}

?>
