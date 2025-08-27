<?php
/*
 * Branding Settings Management Interface (Cleaned)
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: branding_settings.php
 * LOCATION: /public_html/admin/settings/
 * PURPOSE: Basic branding configuration management (business info, colors, fonts, logos)
 * 
 * DESCRIPTION:
 * This interface manages core branding elements: business names, colors, fonts, 
 * logos, favicons, and social share images. For advanced visual template 
 * management, use the enhanced template system.
 * 
 * FEATURES:

// Prevent caching to ensure fresh data from database
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
 * - Business identity management (names, taglines)
 * - Color palette configuration (6 brand colors)
 * - Font family management (6 font options)
 * - Logo management (6 logo variations)
 * - Favicon configuration (3 favicon options)
 * - Social share image management (6 social platforms)
 * - Footer branding settings
 * - File upload handling
 * 
 * VISUAL TEMPLATES:
 * For visual styling and multi-area template management, use:
 * /admin/settings/branding-templates-enhanced.php
 * 
 * CLEANED: 2025-08-16 (Removed legacy file-based template system)
 * VERSION: 2.0 (Database-focused)
 */

include_once '../assets/includes/main.php';

// Security check for admin access
if (!isset($_SESSION['admin_role']) || !in_array($_SESSION['admin_role'], ['Admin', 'Developer'])) {
    header('Location: ../index.php');
    exit();
}

// Load enhanced branding functions (database-driven system) - for getBrandingColors()
require_once '../../assets/includes/branding-functions.php';

// Define getAvailableFonts function locally to avoid conflicts
if (!function_exists('getAvailableFonts')) {
    function getAvailableFonts($category = null) {
        return [
            ['family' => 'Arial', 'category' => 'sans-serif', 'weight' => '400'],
            ['family' => 'Helvetica', 'category' => 'sans-serif', 'weight' => '400'],
            ['family' => 'Times New Roman', 'category' => 'serif', 'weight' => '400'],
            ['family' => 'Georgia', 'category' => 'serif', 'weight' => '400'],
            ['family' => 'Courier New', 'category' => 'monospace', 'weight' => '400'],
            ['family' => 'Verdana', 'category' => 'sans-serif', 'weight' => '400'],
            ['family' => 'Trebuchet MS', 'category' => 'sans-serif', 'weight' => '400'],
            ['family' => 'Palatino', 'category' => 'serif', 'weight' => '400'],
            ['family' => 'Lucida Sans', 'category' => 'sans-serif', 'weight' => '400'],
            ['family' => 'Impact', 'category' => 'sans-serif', 'weight' => '700']
        ];
    }
}

// Helper function to get template data in array format
function getActiveBrandingTemplateArray() {
    global $pdo;
    
    // Use the existing function to get the template key
    $template_key = getActiveBrandingTemplate_Simple();
    
    // If it's already an array, return it
    if (is_array($template_key)) {
        return $template_key;
    }
    
    // If it's a string, fetch the full template data
    try {
        $stmt = $pdo->prepare("SELECT template_key, template_name, template_description FROM setting_branding_templates WHERE template_key = ? AND is_active = 1");
        $stmt->execute([$template_key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return $result;
        } else {
            // Return a basic structure if not found
            return [
                'template_key' => $template_key,
                'template_name' => ucfirst($template_key),
                'template_description' => 'Default template'
            ];
        }
    } catch (Exception $e) {
        error_log("ERROR: Failed to fetch template details: " . $e->getMessage());
        return [
            'template_key' => $template_key,
            'template_name' => ucfirst($template_key),
            'template_description' => 'Default template'
        ];
    }
}

// Handle theme selection
if (isset($_POST['action']) && $_POST['action'] === 'select_theme' && isset($_POST['template_key'])) {
    // Use a more reliable sanitization method
    $template_key = isset($_POST['template_key']) ? trim(strip_tags($_POST['template_key'])) : '';
    
    // Validate against allowed template keys for security
    $allowed_keys = ['default', 'subtle', 'bold', 'casual', 'high_contrast', 'template_1', 'template_2', 'template_3'];
    
    if (in_array($template_key, $allowed_keys) && setActiveBrandingTemplate_Simple($template_key)) {
        $success_message = "Theme '{$template_key}' has been activated successfully!";
    } else {
        $error_message = "Failed to activate theme '{$template_key}'. Please try again.";
    }
}

// Get current active template
$active_template = getActiveBrandingTemplateArray();

// DEBUG: Log what we got
error_log("DEBUG: Active template result: " . print_r($active_template, true));
error_log("DEBUG: Active template type: " . gettype($active_template));

// If no active template exists or it's not an array, try to initialize
if (!$active_template || !is_array($active_template)) {
    try {
        // Check if any templates exist in database
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM setting_branding_templates");
        $stmt->execute();
        $template_count = $stmt->fetchColumn();
        
        error_log("DEBUG: Template count in database: " . $template_count);
        
        if ($template_count == 0) {
            // Initialize default templates if none exist
            error_log("DEBUG: No templates found, attempting to initialize defaults");
            
            // Insert default templates
            $default_templates = [
                ['default', 'Classic Professional', 'Clean, traditional layout perfect for professional businesses', 1],
                ['subtle', 'Subtle Elegance', 'Minimal, understated design for sophisticated brands', 0],
                ['bold', 'Bold Impact', 'Strong, vibrant design for maximum visual impact', 0],
                ['casual', 'Friendly Casual', 'Approachable, relaxed design for friendly businesses', 0],
                ['high_contrast', 'High Contrast', 'Accessibility-focused design with strong contrast ratios', 0]
            ];
            
            foreach ($default_templates as $template) {
                $stmt = $pdo->prepare("INSERT INTO setting_branding_templates (template_key, template_name, template_description, is_active) VALUES (?, ?, ?, ?)");
                $stmt->execute($template);
            }
            
            // Try to get the active template again
            $active_template = getActiveBrandingTemplateArray();
            error_log("DEBUG: After initialization, active template: " . print_r($active_template, true));
            
        } else {
            // Make sure at least one template is active
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM setting_branding_templates WHERE is_active = 1");
            $stmt->execute();
            $active_count = $stmt->fetchColumn();
            
            if ($active_count == 0) {
                // Set the first template as active
                $stmt = $pdo->prepare("UPDATE setting_branding_templates SET is_active = 1 LIMIT 1");
                $stmt->execute();
                $active_template = getActiveBrandingTemplateArray();
                error_log("DEBUG: Set first template as active: " . print_r($active_template, true));
            }
        }
    } catch (Exception $e) {
        error_log("ERROR: Failed to initialize branding template: " . $e->getMessage());
        // Set a fallback
        $active_template = [
            'template_key' => 'default',
            'template_name' => 'Classic Professional',
            'template_description' => 'Clean, traditional layout perfect for professional businesses'
        ];
    }
}

// Load current branding settings from DATABASE ONLY (no PHP files!)
$brand_colors = getBrandingColors();

// DEBUG: Show what we got from database
error_log("DEBUG: Colors from database: " . print_r($brand_colors, true));

// Extract colors from database or set defaults
$brand_primary_color = $brand_colors['brand_primary_color'] ?? '#3b89b0';
$brand_secondary_color = $brand_colors['brand_secondary_color'] ?? '#bf5512';
$brand_tertiary_color = $brand_colors['brand_tertiary_color'] ?? '#8B4513';
$brand_quaternary_color = $brand_colors['brand_quaternary_color'] ?? '#2E8B57';
$brand_accent_color = $brand_colors['brand_accent_color'] ?? '#28a745';
$brand_warning_color = $brand_colors['brand_warning_color'] ?? '#ffc107';
$brand_danger_color = $brand_colors['brand_danger_color'] ?? '#dc3545';
$brand_info_color = $brand_colors['brand_info_color'] ?? '#17a2b8';
$brand_background_color = $brand_colors['brand_background_color'] ?? '#ffffff';
$brand_text_color = $brand_colors['brand_text_color'] ?? '#333333';
$brand_text_light = $brand_colors['brand_text_light'] ?? '#666666';
$brand_text_muted = $brand_colors['brand_text_muted'] ?? '#999999';
// Spinner style selection (new) default
$brand_spinner_style = $brand_colors['brand_spinner_style'] ?? 'rainbow_ring';

// DEBUG: Show final values being used
error_log("DEBUG: Final primary color: $brand_primary_color");
error_log("DEBUG: Final secondary color: $brand_secondary_color");
error_log("DEBUG: Final tertiary color: $brand_tertiary_color");
error_log("DEBUG: Final quaternary color: $brand_quaternary_color");

// Business identity (these could also be moved to database later)
$business_name_short = 'Burden2Blessings';
$business_name_medium = 'Burden to Blessings';
$business_name_long = 'Burden to Blessings LLC';
$business_tagline_short = 'We look forward to serving you!';
$business_tagline_medium = 'It is our goal to give you the best possible service.';
$business_tagline_long = 'We are looking forward to helping you have the best experience possible during this time of change.';

// Other branding assets (fonts, logos, etc.)
$brand_font_family = 'Roboto, Poppins, Raleway, Arial, sans-serif';
$brand_font_headings = 'Poppins, Arial, sans-serif';
$brand_font_body = 'Roboto, Arial, sans-serif';
$brand_font_accent = 'Raleway, Arial, sans-serif';
$brand_font_monospace = 'Consolas, Monaco, "Courier New", monospace';
$brand_font_display = 'Georgia, "Times New Roman", serif';

// Custom font files (empty by default)
$brand_font_file_1 = '';
$brand_font_file_2 = '';
$brand_font_file_3 = '';
$brand_font_file_4 = '';
$brand_font_file_5 = '';

$business_logo = 'assets/img/logo.png';
$business_logo_horizontal = 'assets/branding/logo_horizontal.png';
$business_logo_vertical = 'assets/branding/logo_vertical.png';
$business_logo_square = 'assets/branding/logo_square.png';
$business_logo_white = 'assets/branding/logo_white.png';
$business_logo_small = 'assets/branding/logo_small.png';
$favicon = 'assets/img/favicon.png';
$apple_touch_icon = 'assets/img/apple-touch-icon.png';
$favicon_blog = 'assets/branding/favicon_blog.ico';
$favicon_portal = 'assets/branding/favicon_portal.ico';
$social_share_default = 'assets/branding/social_default.jpg';
$social_share_facebook = 'assets/branding/social_facebook.jpg';
$social_share_twitter = 'assets/branding/social_twitter.jpg';
$social_share_linkedin = 'assets/branding/social_linkedin.jpg';
$social_share_instagram = 'assets/branding/social_instagram.jpg';
$social_share_blog = 'assets/branding/social_blog.jpg';
$author = 'GWS';
$footer_business_name_type = 'medium';
$footer_logo_enabled = true;
$footer_logo_position = 'left';
$footer_logo_file = 'business_logo';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_branding') {
    
    // Start building the new settings file content
    $settings_content = "<?php\n";
    $settings_content .= "/*\n";
    $settings_content .= " * Database-Driven Branding Settings\n";
    $settings_content .= " * \n";
    $settings_content .= " * SYSTEM: GWS Universal Hybrid App\n";
    $settings_content .= " * FILE: branding_settings.php\n";
    $settings_content .= " * LOCATION: /public_html/assets/includes/settings/\n";
    $settings_content .= " * PURPOSE: Current branding values from database/admin interface\n";
    $settings_content .= " * \n";
    $settings_content .= " * NOTE: For visual template management, use enhanced system at:\n";
    $settings_content .= " * /admin/settings/branding-templates-enhanced.php\n";
    $settings_content .= " * \n";
    $settings_content .= " * LAST UPDATED: " . date('Y-m-d H:i:s') . "\n";
    $settings_content .= " * VERSION: 2.0 (Database-driven)\n";
    $settings_content .= " */\n\n";
    
    // Business Identity
    $settings_content .= "// =========================================================================\n";
    $settings_content .= "// BUSINESS IDENTITY (Multiple lengths for different use cases)\n";
    $settings_content .= "// =========================================================================\n\n";
    
    $business_name_short = sanitizeInput($_POST['business_name_short']);
    $business_name_medium = sanitizeInput($_POST['business_name_medium']);
    $business_name_long = sanitizeInput($_POST['business_name_long']);
    
    $settings_content .= "\$business_name_short = '" . addslashes($business_name_short) . "';\n";
    $settings_content .= "\$business_name_medium = '" . addslashes($business_name_medium) . "';\n";
    $settings_content .= "\$business_name_long = '" . addslashes($business_name_long) . "';\n\n";
    
    $business_tagline_short = sanitizeInput($_POST['business_tagline_short']);
    $business_tagline_medium = sanitizeInput($_POST['business_tagline_medium']);
    $business_tagline_long = sanitizeInput($_POST['business_tagline_long']);
    
    $settings_content .= "\$business_tagline_short = '" . addslashes($business_tagline_short) . "';\n";
    $settings_content .= "\$business_tagline_medium = '" . addslashes($business_tagline_medium) . "';\n";
    $settings_content .= "\$business_tagline_long = '" . addslashes($business_tagline_long) . "';\n\n";
    
    $settings_content .= "// BACKWARD COMPATIBILITY: Maintain existing business_name variable\n";
    $settings_content .= "\$business_name = \$business_name_long;\n\n";
    
    // Brand Colors
    $settings_content .= "// =========================================================================\n";
    $settings_content .= "// BRAND COLORS (Up to 8 brand colors)\n";
    $settings_content .= "// =========================================================================\n\n";
    
    $brand_primary_color = sanitizeColor($_POST['brand_primary_color']);
    $brand_secondary_color = sanitizeColor($_POST['brand_secondary_color']);
    $brand_tertiary_color = sanitizeColor($_POST['brand_tertiary_color']);
    $brand_quaternary_color = sanitizeColor($_POST['brand_quaternary_color']);
    $brand_accent_color = sanitizeColor($_POST['brand_accent_color']);
    $brand_warning_color = sanitizeColor($_POST['brand_warning_color']);
    $brand_danger_color = sanitizeColor($_POST['brand_danger_color']);
    $brand_info_color = sanitizeColor($_POST['brand_info_color']);
    
    $settings_content .= "// Primary brand colors (EXISTING - DO NOT CHANGE NAMES)\n";
    $settings_content .= "\$brand_primary_color = '" . $brand_primary_color . "';\n";
    $settings_content .= "\$brand_secondary_color = '" . $brand_secondary_color . "';\n\n";
    
    $settings_content .= "// Extended brand colors (NEW)\n";
    $settings_content .= "\$brand_tertiary_color = '" . $brand_tertiary_color . "';\n";
    $settings_content .= "\$brand_quaternary_color = '" . $brand_quaternary_color . "';\n";
    $settings_content .= "\$brand_accent_color = '" . $brand_accent_color . "';\n";
    $settings_content .= "\$brand_warning_color = '" . $brand_warning_color . "';\n";
    $settings_content .= "\$brand_danger_color = '" . $brand_danger_color . "';\n";
    $settings_content .= "\$brand_info_color = '" . $brand_info_color . "';\n";
    $settings_content .= "\$brand_color_4 = '" . $brand_color_4 . "';\n";
    $settings_content .= "\$brand_color_5 = '" . $brand_color_5 . "';\n";
    // Spinner style choice
    $submitted_spinner_style = isset($_POST['brand_spinner_style']) ? preg_replace('/[^a-z0-9_\-]/i','', $_POST['brand_spinner_style']) : $brand_spinner_style;
    $settings_content .= "// Spinner style selection (persisted)\n";
    $settings_content .= "\$brand_spinner_style = '" . addslashes($submitted_spinner_style) . "';\n\n";
    
    // Background and text colors
    $brand_background_color = sanitizeColor($_POST['brand_background_color']);
    $brand_text_color = sanitizeColor($_POST['brand_text_color']);
    $brand_text_light = sanitizeColor($_POST['brand_text_light']);
    $brand_text_muted = sanitizeColor($_POST['brand_text_muted']);
    
    $settings_content .= "// Background and text colors\n";
    $settings_content .= "\$brand_background_color = '" . $brand_background_color . "';\n";
    $settings_content .= "\$brand_text_color = '" . $brand_text_color . "';\n";
    $settings_content .= "\$brand_text_light = '" . $brand_text_light . "';\n";
    $settings_content .= "\$brand_text_muted = '" . $brand_text_muted . "';\n\n";
    
    // Brand Fonts
    $settings_content .= "// =========================================================================\n";
    $settings_content .= "// BRAND FONTS (Up to 6 font families + custom uploads)\n";
    $settings_content .= "// =========================================================================\n\n";
    
    // Handle font file uploads first
    $font_upload_dir = '../../assets/branding/fonts/';
    if (!is_dir($font_upload_dir)) {
        mkdir($font_upload_dir, 0755, true);
    }
    
    // Process font uploads
    for ($i = 1; $i <= 5; $i++) {
        $upload_key = "font_upload_$i";
        if (isset($_FILES[$upload_key]) && $_FILES[$upload_key]['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES[$upload_key];
            $file_info = pathinfo($file['name']);
            $extension = strtolower($file_info['extension']);
            
            // Validate file type
            $allowed_types = ['woff2', 'woff', 'ttf', 'otf'];
            if (in_array($extension, $allowed_types)) {
                // Generate safe filename
                $safe_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $file_info['filename']);
                $new_filename = $safe_name . '_' . time() . '.' . $extension;
                $upload_path = $font_upload_dir . $new_filename;
                
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    // Update the form data with the new file path
                    $_POST["brand_font_file_$i"] = 'assets/branding/fonts/' . $new_filename;
                    $success_message = "Font file uploaded successfully: " . $file['name'];
                } else {
                    $error_message = "Failed to upload font file: " . $file['name'];
                }
            } else {
                $error_message = "Invalid font file type. Allowed: WOFF2, WOFF, TTF, OTF";
            }
        }
    }
    
    $brand_font_family = sanitizeInput($_POST['brand_font_family']);
    $brand_font_headings = sanitizeInput($_POST['brand_font_headings']);
    $brand_font_body = sanitizeInput($_POST['brand_font_body']);
    $brand_font_accent = sanitizeInput($_POST['brand_font_accent']);
    $brand_font_monospace = sanitizeInput($_POST['brand_font_monospace']);
    $brand_font_display = sanitizeInput($_POST['brand_font_display']);
    
    $settings_content .= "// BACKWARD COMPATIBILITY: Maintain existing font variable\n";
    $settings_content .= "\$brand_font_family = '" . addslashes($brand_font_family) . "';\n\n";
    
    $settings_content .= "// Additional font options (override Google Fonts defaults)\n";
    $settings_content .= "\$brand_font_primary = \$brand_font_family;\n";
    $settings_content .= "\$brand_font_headings = '" . addslashes($brand_font_headings) . "';\n";
    $settings_content .= "\$brand_font_body = '" . addslashes($brand_font_body) . "';\n";
    $settings_content .= "\$brand_font_accent = '" . addslashes($brand_font_accent) . "';\n";
    $settings_content .= "\$brand_font_monospace = '" . addslashes($brand_font_monospace) . "';\n";
    $settings_content .= "\$brand_font_display = '" . addslashes($brand_font_display) . "';\n\n";
    
    // Font files (custom uploaded fonts)
    $settings_content .= "// Custom uploaded font files (takes precedence over Google Fonts)\n";
    for ($i = 1; $i <= 5; $i++) {
        $font_file = isset($_POST["brand_font_file_$i"]) ? sanitizeInput($_POST["brand_font_file_$i"]) : '';
        $settings_content .= "\$brand_font_file_$i = '" . addslashes($font_file) . "';\n";
    }
    $settings_content .= "\n";
    
    // Logos
    $settings_content .= "// =========================================================================\n";
    $settings_content .= "// LOGOS (Up to 6 logo variations)\n";
    $settings_content .= "// =========================================================================\n\n";
    
    $business_logo = sanitizeInput($_POST['business_logo']);
    $business_logo_horizontal = sanitizeInput($_POST['business_logo_horizontal']);
    $business_logo_vertical = sanitizeInput($_POST['business_logo_vertical']);
    $business_logo_square = sanitizeInput($_POST['business_logo_square']);
    $business_logo_white = sanitizeInput($_POST['business_logo_white']);
    $business_logo_small = sanitizeInput($_POST['business_logo_small']);
    
    $settings_content .= "// BACKWARD COMPATIBILITY: Maintain existing logo variables\n";
    $settings_content .= "\$business_logo = '" . addslashes($business_logo) . "';\n\n";
    
    $settings_content .= "// Additional logo variations (NEW)\n";
    $settings_content .= "\$business_logo_main = \$business_logo;\n";
    $settings_content .= "\$business_logo_horizontal = '" . addslashes($business_logo_horizontal) . "';\n";
    $settings_content .= "\$business_logo_vertical = '" . addslashes($business_logo_vertical) . "';\n";
    $settings_content .= "\$business_logo_square = '" . addslashes($business_logo_square) . "';\n";
    $settings_content .= "\$business_logo_white = '" . addslashes($business_logo_white) . "';\n";
    $settings_content .= "\$business_logo_small = '" . addslashes($business_logo_small) . "';\n\n";
    
    // Favicons
    $settings_content .= "// =========================================================================\n";
    $settings_content .= "// FAVICONS (Up to 3 favicon variations)\n";
    $settings_content .= "// =========================================================================\n\n";
    
    $favicon = sanitizeInput($_POST['favicon']);
    $apple_touch_icon = sanitizeInput($_POST['apple_touch_icon']);
    $favicon_blog = sanitizeInput($_POST['favicon_blog']);
    $favicon_portal = sanitizeInput($_POST['favicon_portal']);
    
    $settings_content .= "// BACKWARD COMPATIBILITY: Maintain existing favicon variables\n";
    $settings_content .= "\$favicon = '" . addslashes($favicon) . "';\n";
    $settings_content .= "\$apple_touch_icon = '" . addslashes($apple_touch_icon) . "';\n\n";
    
    $settings_content .= "// Additional favicon variations (NEW)\n";
    $settings_content .= "\$favicon_main = \$favicon;\n";
    $settings_content .= "\$favicon_blog = '" . addslashes($favicon_blog) . "';\n";
    $settings_content .= "\$favicon_portal = '" . addslashes($favicon_portal) . "';\n\n";
    
    // Social Share Images
    $settings_content .= "// =========================================================================\n";
    $settings_content .= "// SOCIAL SHARE IMAGES (Up to 6 variations)\n";
    $settings_content .= "// =========================================================================\n\n";
    
    $social_share_default = sanitizeInput($_POST['social_share_default']);
    $social_share_facebook = sanitizeInput($_POST['social_share_facebook']);
    $social_share_twitter = sanitizeInput($_POST['social_share_twitter']);
    $social_share_linkedin = sanitizeInput($_POST['social_share_linkedin']);
    $social_share_instagram = sanitizeInput($_POST['social_share_instagram']);
    $social_share_blog = sanitizeInput($_POST['social_share_blog']);
    
    $settings_content .= "\$social_share_default = '" . addslashes($social_share_default) . "';\n";
    $settings_content .= "\$social_share_facebook = '" . addslashes($social_share_facebook) . "';\n";
    $settings_content .= "\$social_share_twitter = '" . addslashes($social_share_twitter) . "';\n";
    $settings_content .= "\$social_share_linkedin = '" . addslashes($social_share_linkedin) . "';\n";
    $settings_content .= "\$social_share_instagram = '" . addslashes($social_share_instagram) . "';\n";
    $settings_content .= "\$social_share_blog = '" . addslashes($social_share_blog) . "';\n\n";
    
    // Author (existing)
    $settings_content .= "// =========================================================================\n";
    $settings_content .= "// AUTHOR AND META INFORMATION (EXISTING)\n";
    $settings_content .= "// =========================================================================\n\n";
    
    $author = sanitizeInput($_POST['author']);
    $settings_content .= "\$author = '" . addslashes($author) . "';\n\n";
    
    // Footer Branding Settings
    $settings_content .= "// =========================================================================\n";
    $settings_content .= "// FOOTER BRANDING SETTINGS\n";
    $settings_content .= "// =========================================================================\n\n";
    
    $footer_business_name_type = sanitizeInput($_POST['footer_business_name_type']);
    $footer_logo_enabled = isset($_POST['footer_logo_enabled']) ? 'true' : 'false';
    $footer_logo_position = sanitizeInput($_POST['footer_logo_position']);
    $footer_logo_file = sanitizeInput($_POST['footer_logo_file']);
    
    $settings_content .= "\$footer_business_name_type = '" . addslashes($footer_business_name_type) . "';\n";
    $settings_content .= "\$footer_logo_enabled = " . $footer_logo_enabled . ";\n";
    $settings_content .= "\$footer_logo_position = '" . addslashes($footer_logo_position) . "';\n";
    $settings_content .= "\$footer_logo_file = '" . addslashes($footer_logo_file) . "';\n\n";
    
    // System notes
    $settings_content .= "// =========================================================================\n";
    $settings_content .= "// SYSTEM NOTES\n";
    $settings_content .= "// =========================================================================\n\n";
    $settings_content .= "/*\n";
    $settings_content .= " * TEMPLATE MANAGEMENT:\n";
    $settings_content .= " * Visual templates are now managed through the enhanced database-driven\n";
    $settings_content .= " * system. Access at: /admin/settings/branding-templates-enhanced.php\n";
    $settings_content .= " * \n";
    $settings_content .= " * LEGACY ARCHIVE:\n";
    $settings_content .= " * Old file-based template system has been archived to:\n";
    $settings_content .= " * branding_settings_legacy_archive.php for reference\n";
    $settings_content .= " */\n";
    
    // SAVE TO DATABASE FIRST (Primary storage)
    try {
        $stmt = $pdo->prepare("
            UPDATE setting_branding_colors 
            SET 
                brand_primary_color = ?,
                brand_secondary_color = ?,
                brand_tertiary_color = ?,
                brand_quaternary_color = ?,
                brand_accent_color = ?,
                brand_warning_color = ?,
                brand_danger_color = ?,
                brand_info_color = ?,
                brand_background_color = ?,
                brand_text_color = ?,
                brand_text_light = ?,
                brand_text_muted = ?,
                brand_spinner_style = ?
            WHERE id = 1
        ");
        
        $database_save_success = $stmt->execute([
            $brand_primary_color,
            $brand_secondary_color,
            $brand_tertiary_color,
            $brand_quaternary_color,
            $brand_accent_color,
            $brand_warning_color,
            $brand_danger_color,
            $brand_info_color,
            $brand_background_color,
            $brand_text_color,
            $brand_text_light,
            $brand_text_muted,
            $submitted_spinner_style
        ]);
        
        if (!$database_save_success) {
            error_log("Failed to save brand colors to database");
        }
    } catch (Exception $e) {
        error_log("Database error saving brand colors: " . $e->getMessage());
        $database_save_success = false;
    }
    
    // Write the file (Backup/compatibility storage)
    $settings_file = '../../assets/includes/settings/branding_settings.php';
    if (file_put_contents($settings_file, $settings_content)) {
        $file_save_success = true;
        
        // Force reload the settings by clearing the include cache and re-including
        $included_files = get_included_files();
        $settings_file_full_path = realpath($settings_file);
        
        // Re-include the updated settings file (bypassing include_once cache)
        include $settings_file;
        
        // Optional: Log the successful save
        error_log("Branding settings updated successfully at " . date('Y-m-d H:i:s'));
    } else {
        $file_save_success = false;
    }
    
    // Set success/error message based on save results
    if ($database_save_success && $file_save_success) {
        $success_message = "Branding settings saved successfully to both database and file!";
    } elseif ($database_save_success) {
        $success_message = "Branding settings saved to database (file save failed - check permissions)";
    } elseif ($file_save_success) {
        $success_message = "Branding settings saved to file (database save failed - check connection)";
    } else {
        $error_message = "Error saving branding settings to both database and file. Please check permissions and database connection.";
    }
}

// Helper functions
function sanitizeInput($input) {
    return trim(stripslashes(htmlspecialchars($input)));
}

function sanitizeColor($color) {
    // Basic color validation - should be hex color
    if (preg_match('/^#[a-fA-F0-9]{6}$/', $color)) {
        return $color;
    }
    return '#000000'; // Default fallback
}

// Call the admin header template
// Call the admin header template
echo template_admin_header('Branding Settings', 'settings', 'branding');
?>

<style>
.color-preview {
    width: 30px;
    height: 30px;
    border: 2px solid #ddd;
    border-radius: 4px;
    display: inline-block;
    margin-left: 10px;
    vertical-align: middle;
}

.font-preview {
    padding: 12px;
    border: 2px solid #ddd;
    border-radius: 6px;
    margin-top: 6px;
    background: #f8f9fb;
    font-family: inherit;
    font-size: 14px;
    line-height: 1.4;
}

.section-header {
    background: var(--brand-primary-color, #3b89b0) !important;
    color: white !important;
    padding: 14px 20px !important;
    border-radius: 6px !important;
    margin: 28px 0 16px 0 !important;
    border: 1px solid var(--brand-primary-color, #3b89b0) !important;
}

.form-section {
    background: #f8f9fb !important;
    padding: 24px !important;
    border-radius: 6px !important;
    margin-bottom: 20px !important;
    border: 1px solid #e3e6f0 !important;
    border-left: 4px solid var(--brand-primary-color, #3b89b0) !important;
}

/* Theme Card Styling */
.theme-card {
    border: 2px solid #e3e6f0 !important;
    border-radius: 12px !important;
    overflow: hidden;
    position: relative;
}

.theme-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    border-color: var(--brand-primary-color, #3b89b0) !important;
}

.theme-card.border-primary {
    border-color: var(--brand-primary-color, #3b89b0) !important;
    box-shadow: 0 4px 15px rgba(59, 137, 176, 0.2) !important;
}

.theme-card .card-body {
    padding: 1.5rem !important;
}

.theme-card .card-footer {
    padding: 1rem 1.5rem !important;
    border-top: 1px solid #e3e6f0 !important;
}
    border: 1px solid #e3e6f0 !important;
    border-left: 4px solid var(--brand-primary-color, #3b89b0) !important;
}

/* Ensure proper font rendering and accessibility */
body, .form-control, .form-select, .btn {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
}

/* High contrast mode improvements */
@media (prefers-contrast: high) {
    .section-header,
    .btn-primary {
        border: 3px solid white !important;
    }
    
    .form-control,
    .color-input-group {
        border-width: 3px !important;
    }
}
</style>

<!-- Enhanced Admin CSS for Professional Styling -->
<link href="../assets/css/branding-settings-enhanced.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css">

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4 branding-settings-container">
                <div class="card-header py-3 branding-settings-header">
                    <h6 class="m-0 font-weight-bold">
                        <span class="header-icon"><i class="bi bi-palette-fill" aria-hidden="true"></i></span>
                        Professional Branding Settings
                    </h6>
                </div>
                
                <div class="card-body">
                    
                    <?php if (isset($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill" aria-hidden="true"></i> <?php echo $success_message; ?>
                        <button type="button" class="close" data-dismiss="alert">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    
                    <!-- Debug: Show current loaded values -->
                    <div class="debug-panel">
                        <strong>Debug - Currently Loaded Values:</strong><br>
                        Primary: <span class="color-swatch" style="background: <?php echo $brand_primary_color; ?>;"><?php echo $brand_primary_color; ?></span><br>
                        Secondary: <span class="color-swatch" style="background: <?php echo $brand_secondary_color; ?>;"><?php echo $brand_secondary_color; ?></span><br>
                        Tertiary: <span class="color-swatch" style="background: <?php echo $brand_tertiary_color; ?>;"><?php echo $brand_tertiary_color; ?></span><br>
                        Quaternary: <span class="color-swatch" style="background: <?php echo $brand_quaternary_color; ?>;"><?php echo $brand_quaternary_color; ?></span><br>
                        Accent: <span class="color-swatch" style="background: <?php echo $brand_accent_color; ?>;"><?php echo $brand_accent_color; ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i> <?php echo $error_message; ?>
                        <button type="button" class="close" data-dismiss="alert">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php endif; ?>
                    
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="save_branding">
                        <input type="hidden" name="brand_spinner_style_current" value="<?php echo htmlspecialchars($brand_spinner_style); ?>">
                        
                        <!-- Business Identity Section -->
                        <div class="section-header">
                            <h4>
                                <i class="bi bi-building" aria-hidden="true"></i>
                                Business Identity
                            </h4>
                            <small>Configure business names and taglines for different contexts</small>
                        </div>
                        
                        <div class="form-section">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Business Name - Short</label>
                                    <input type="text" name="business_name_short" class="form-control" 
                                           value="<?php echo htmlspecialchars($business_name_short ?? ''); ?>"
                                           placeholder="e.g., GWS">
                                    <small class="text-muted">For mobile nav, tight spaces</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Business Name - Medium</label>
                                    <input type="text" name="business_name_medium" class="form-control" 
                                           value="<?php echo htmlspecialchars($business_name_medium ?? ''); ?>"
                                           placeholder="e.g., GWS Universal">
                                    <small class="text-muted">For headers, cards</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Business Name - Long</label>
                                    <input type="text" name="business_name_long" class="form-control" 
                                           value="<?php echo htmlspecialchars($business_name_long ?? ''); ?>"
                                           placeholder="e.g., GWS Universal Hybrid Application">
                                    <small class="text-muted">For full display, about pages</small>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <label class="form-label">Global Spinner Style</label>
                                    <select name="brand_spinner_style" class="form-select">
                                        <?php
                                        $spinner_options = [
                                            'rainbow_ring' => 'Rainbow Ring (Recommended)',
                                            'border' => 'Border (Single Color)',
                                            'gradient' => 'Conic Gradient',
                                            'logo_ring' => 'Logo Ring',
                                            'pulse_orb' => 'Pulse Orb',
                                            'dots' => 'Bouncing Dots'
                                        ];
                                        foreach ($spinner_options as $val => $label) {
                                            $sel = ($brand_spinner_style === $val) ? ' selected' : '';
                                            echo '<option value="' . htmlspecialchars($val, ENT_QUOTES) . '"' . $sel . '>' . htmlspecialchars($label) . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <small class="text-muted">Select the loading animation style used across the application.</small>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label class="form-label">Tagline - Short</label>
                                    <input type="text" name="business_tagline_short" class="form-control" 
                                           value="<?php echo htmlspecialchars($business_tagline_short ?? ''); ?>"
                                           placeholder="e.g., Innovation Simplified">
                                    <small class="text-muted">For headers, cards</small>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Tagline - Medium</label>
                                    <input type="text" name="business_tagline_medium" class="form-control" 
                                           value="<?php echo htmlspecialchars($business_tagline_medium ?? ''); ?>"
                                           placeholder="e.g., Your complete business solution">
                                    <small class="text-muted">For hero sections</small>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <label class="form-label">Tagline - Long</label>
                                    <textarea name="business_tagline_long" class="form-control" rows="3"
                                              placeholder="e.g., Comprehensive platform designed to streamline operations and deliver exceptional customer experiences"><?php echo htmlspecialchars($business_tagline_long ?? ''); ?></textarea>
                                    <small class="text-muted">For about pages, detailed descriptions</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Footer Branding Section -->
                        <div class="section-header">
                            <h4>
                                <i class="bi bi-window" aria-hidden="true"></i>
                                Footer Branding
                            </h4>
                            <small>Configure how business name and logo appear in the footer</small>
                        </div>
                        
                        <div class="form-section">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Footer Business Name Type</label>
                                    <select name="footer_business_name_type" class="form-select">
                                        <option value="short" <?php echo ($footer_business_name_type ?? 'medium') === 'short' ? 'selected' : ''; ?>>Short</option>
                                        <option value="medium" <?php echo ($footer_business_name_type ?? 'medium') === 'medium' ? 'selected' : ''; ?>>Medium</option>
                                        <option value="long" <?php echo ($footer_business_name_type ?? 'medium') === 'long' ? 'selected' : ''; ?>>Long</option>
                                    </select>
                                    <small class="text-muted">Which business name length to show</small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Footer Logo Position</label>
                                    <select name="footer_logo_position" class="form-select">
                                        <option value="left" <?php echo ($footer_logo_position ?? 'left') === 'left' ? 'selected' : ''; ?>>Left of Name</option>
                                        <option value="top" <?php echo ($footer_logo_position ?? 'left') === 'top' ? 'selected' : ''; ?>>Above Name</option>
                                    </select>
                                    <small class="text-muted">Logo placement relative to name</small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Footer Logo File</label>
                                    <select name="footer_logo_file" class="form-select">
                                        <option value="business_logo" <?php echo ($footer_logo_file ?? 'business_logo') === 'business_logo' ? 'selected' : ''; ?>>Main Logo</option>
                                        <option value="business_logo_small" <?php echo ($footer_logo_file ?? 'business_logo') === 'business_logo_small' ? 'selected' : ''; ?>>Small Logo</option>
                                        <option value="business_logo_square" <?php echo ($footer_logo_file ?? 'business_logo') === 'business_logo_square' ? 'selected' : ''; ?>>Square Logo</option>
                                        <option value="business_logo_white" <?php echo ($footer_logo_file ?? 'business_logo') === 'business_logo_white' ? 'selected' : ''; ?>>White Logo</option>
                                        <option value="admin_logo.svg" <?php echo ($footer_logo_file ?? 'business_logo') === 'admin_logo.svg' ? 'selected' : ''; ?>>Admin Logo (SVG)</option>
                                        <option value="favicon" <?php echo ($footer_logo_file ?? 'business_logo') === 'favicon' ? 'selected' : ''; ?>>Favicon</option>
                                    </select>
                                    <small class="text-muted">Which logo variation to use</small>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check mt-4">
                                        <input type="checkbox" name="footer_logo_enabled" class="form-check-input" id="footer_logo_enabled"
                                            <?php echo ($footer_logo_enabled ?? true) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="footer_logo_enabled">
                                            Enable Footer Logo
                                        </label>
                                        <small class="text-muted d-block">Show/hide logo in footer</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Brand Colors Section -->
                        <div class="section-header">
                            <h4>
                                <i class="bi bi-palette-fill" aria-hidden="true"></i>
                                Brand Colors
                            </h4>
                            <small>Configure your brand color palette (maintains compatibility with existing colors)</small>
                        </div>
                        
                        <div class="form-section">
                            <div class="row color-row">
                                <div class="col-md-3">
                                    <label class="form-label">Primary Color <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="color" name="brand_primary_color" class="form-control color-picker" 
                                               value="<?php echo htmlspecialchars($brand_primary_color); ?>"
                                               id="brand_primary_color_picker">
                                        <input type="text" class="form-control color-hex" 
                                               value="<?php echo htmlspecialchars($brand_primary_color); ?>"
                                               id="brand_primary_color_hex" 
                                               placeholder="#000000" 
                                               pattern="^#[0-9A-Fa-f]{6}$"
                                               title="Enter a valid hex color (e.g., #FF5733)">
                                    </div>
                                    <small class="text-muted">Main brand color</small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Secondary Color <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="color" name="brand_secondary_color" class="form-control color-picker" 
                                               value="<?php echo htmlspecialchars($brand_secondary_color); ?>"
                                               id="brand_secondary_color_picker">
                                        <input type="text" class="form-control color-hex" 
                                               value="<?php echo htmlspecialchars($brand_secondary_color); ?>"
                                               id="brand_secondary_color_hex"
                                               placeholder="#000000" 
                                               pattern="^#[0-9A-Fa-f]{6}$"
                                               title="Enter a valid hex color (e.g., #FF5733)">
                                    </div>
                                    <small class="text-muted">Secondary brand color</small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tertiary Color</label>
                                    <div class="input-group">
                                        <input type="color" name="brand_tertiary_color" class="form-control color-picker" 
                                               value="<?php echo htmlspecialchars($brand_tertiary_color ?? '#8B4513'); ?>"
                                               id="brand_tertiary_color_picker">
                                        <input type="text" class="form-control color-hex" 
                                               value="<?php echo htmlspecialchars($brand_tertiary_color ?? '#8B4513'); ?>"
                                               id="brand_tertiary_color_hex"
                                               placeholder="#000000" 
                                               pattern="^#[0-9A-Fa-f]{6}$"
                                               title="Enter a valid hex color (e.g., #FF5733)">
                                    </div>
                                    <small class="text-muted">Third brand color</small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Quaternary Color</label>
                                    <div class="input-group">
                                        <input type="color" name="brand_quaternary_color" class="form-control color-picker" 
                                               value="<?php echo htmlspecialchars($brand_quaternary_color ?? '#2E8B57'); ?>"
                                               id="brand_quaternary_color_picker">
                                        <input type="text" class="form-control color-hex" 
                                               value="<?php echo htmlspecialchars($brand_quaternary_color ?? '#2E8B57'); ?>"
                                               id="brand_quaternary_color_hex"
                                               placeholder="#000000" 
                                               pattern="^#[0-9A-Fa-f]{6}$"
                                               title="Enter a valid hex color (e.g., #FF5733)">
                                    </div>
                                    <small class="text-muted">Fourth brand color</small>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <label class="form-label">Accent Color</label>
                                    <div class="input-group">
                                        <input type="color" name="brand_accent_color" class="form-control color-picker" 
                                               value="<?php echo htmlspecialchars($brand_accent_color ?? '#28a745'); ?>"
                                               id="brand_accent_color_picker">
                                        <input type="text" class="form-control color-hex" 
                                               value="<?php echo htmlspecialchars($brand_accent_color ?? '#28a745'); ?>"
                                               id="brand_accent_color_hex"
                                               placeholder="#000000" 
                                               pattern="^#[0-9A-Fa-f]{6}$"
                                               title="Enter a valid hex color (e.g., #FF5733)">
                                    </div>
                                    <small class="text-muted">Success/positive actions</small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Warning Color</label>
                                    <div class="input-group">
                                        <input type="color" name="brand_warning_color" class="form-control color-picker" 
                                               value="<?php echo htmlspecialchars($brand_warning_color ?? '#ffc107'); ?>"
                                               id="brand_warning_color_picker">
                                        <input type="text" class="form-control color-hex" 
                                               value="<?php echo htmlspecialchars($brand_warning_color ?? '#ffc107'); ?>"
                                               id="brand_warning_color_hex"
                                               placeholder="#000000" 
                                               pattern="^#[0-9A-Fa-f]{6}$"
                                               title="Enter a valid hex color (e.g., #FF5733)">
                                    </div>
                                    <small class="text-muted">Warnings/caution</small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Danger Color</label>
                                    <div class="input-group">
                                        <input type="color" name="brand_danger_color" class="form-control color-picker" 
                                               value="<?php echo htmlspecialchars($brand_danger_color ?? '#dc3545'); ?>"
                                               id="brand_danger_color_picker">
                                        <input type="text" class="form-control color-hex" 
                                               value="<?php echo htmlspecialchars($brand_danger_color ?? '#dc3545'); ?>"
                                               id="brand_danger_color_hex"
                                               placeholder="#000000" 
                                               pattern="^#[0-9A-Fa-f]{6}$"
                                               title="Enter a valid hex color (e.g., #FF5733)">
                                    </div>
                                    <small class="text-muted">Errors/negative actions</small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Info Color</label>
                                    <div class="input-group">
                                        <input type="color" name="brand_info_color" class="form-control color-picker" 
                                               value="<?php echo htmlspecialchars($brand_info_color ?? '#17a2b8'); ?>"
                                               id="brand_info_color_picker">
                                        <input type="text" class="form-control color-hex" 
                                               value="<?php echo htmlspecialchars($brand_info_color ?? '#17a2b8'); ?>"
                                               id="brand_info_color_hex"
                                               placeholder="#000000" 
                                               pattern="^#[0-9A-Fa-f]{6}$"
                                               title="Enter a valid hex color (e.g., #FF5733)">
                                    </div>
                                    <small class="text-muted">Information/neutral</small>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <label class="form-label">Background Color</label>
                                    <div class="input-group">
                                        <input type="color" name="brand_background_color" class="form-control color-picker" 
                                               value="<?php echo htmlspecialchars($brand_background_color ?? '#ffffff'); ?>"
                                               id="brand_background_color_picker">
                                        <input type="text" class="form-control color-hex" 
                                               value="<?php echo htmlspecialchars($brand_background_color ?? '#ffffff'); ?>"
                                               id="brand_background_color_hex"
                                               placeholder="#000000" 
                                               pattern="^#[0-9A-Fa-f]{6}$"
                                               title="Enter a valid hex color (e.g., #FF5733)">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Text Color</label>
                                    <div class="input-group">
                                        <input type="color" name="brand_text_color" class="form-control color-picker" 
                                               value="<?php echo htmlspecialchars($brand_text_color ?? '#333333'); ?>"
                                               id="brand_text_color_picker">
                                        <input type="text" class="form-control color-hex" 
                                               value="<?php echo htmlspecialchars($brand_text_color ?? '#333333'); ?>"
                                               id="brand_text_color_hex"
                                               placeholder="#000000" 
                                               pattern="^#[0-9A-Fa-f]{6}$"
                                               title="Enter a valid hex color (e.g., #FF5733)">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Light Text</label>
                                    <div class="input-group">
                                        <input type="color" name="brand_text_light" class="form-control color-picker" 
                                               value="<?php echo htmlspecialchars($brand_text_light ?? '#666666'); ?>"
                                               id="brand_text_light_picker">
                                        <input type="text" class="form-control color-hex" 
                                               value="<?php echo htmlspecialchars($brand_text_light ?? '#666666'); ?>"
                                               id="brand_text_light_hex"
                                               placeholder="#000000" 
                                               pattern="^#[0-9A-Fa-f]{6}$"
                                               title="Enter a valid hex color (e.g., #FF5733)">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Muted Text</label>
                                    <div class="input-group">
                                        <input type="color" name="brand_text_muted" class="form-control color-picker" 
                                               value="<?php echo htmlspecialchars($brand_text_muted ?? '#999999'); ?>"
                                               id="brand_text_muted_picker">
                                        <input type="text" class="form-control color-hex" 
                                               value="<?php echo htmlspecialchars($brand_text_muted ?? '#999999'); ?>"
                                               id="brand_text_muted_hex"
                                               placeholder="#000000" 
                                               pattern="^#[0-9A-Fa-f]{6}$"
                                               title="Enter a valid hex color (e.g., #FF5733)">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Theme Selection Section -->
                        <div class="section-header">
                            <h4>
                                <i class="bi bi-brush" aria-hidden="true"></i>
                                Website Theme Selection
                            </h4>
                            <small>Choose how your brand colors are applied across your website - professional themes that are attractive but not obnoxious</small>
                        </div>
                        
                        <div class="form-section">
                            <?php if ($active_template && is_array($active_template)): ?>
                            <div class="alert alert-info mb-4">
                                <strong><i class="bi bi-info-circle me-2"></i>Currently Active Theme:</strong> 
                                <strong><?= htmlspecialchars($active_template['template_name'] ?? 'Unknown') ?></strong>
                                <br>
                                <small><?= htmlspecialchars($active_template['template_description'] ?? 'No description available') ?></small>
                            </div>
                            <?php elseif ($active_template): ?>
                            <div class="alert alert-warning mb-4">
                                <strong><i class="bi bi-exclamation-triangle me-2"></i>Template Issue:</strong> 
                                Active template data format is incorrect. Current value: <?= htmlspecialchars(is_string($active_template) ? $active_template : gettype($active_template)) ?>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-warning mb-4">
                                <strong><i class="bi bi-exclamation-triangle me-2"></i>No Active Theme:</strong> 
                                No theme is currently active. Please select a theme below.
                            </div>
                            <?php endif; ?>
                            
                            <div class="row">
                                <?php
                                // Define theme details for presentation
                                $theme_details = [
                                    'default' => [
                                        'name' => 'Classic Professional',
                                        'description' => 'Clean, traditional layout perfect for professional businesses',
                                        'icon' => 'building',
                                        'style_preview' => 'Traditional • Professional • Conservative'
                                    ],
                                    'subtle' => [
                                        'name' => 'Subtle Elegance',
                                        'description' => 'Minimal, understated design for sophisticated brands',
                                        'icon' => 'gem',
                                        'style_preview' => 'Minimal • Sophisticated • Understated'
                                    ],
                                    'bold' => [
                                        'name' => 'Bold Impact',
                                        'description' => 'Strong, vibrant design for maximum visual impact',
                                        'icon' => 'lightning',
                                        'style_preview' => 'Vibrant • Dramatic • High Impact'
                                    ],
                                    'casual' => [
                                        'name' => 'Friendly Casual',
                                        'description' => 'Approachable, relaxed design for friendly businesses',
                                        'icon' => 'heart',
                                        'style_preview' => 'Friendly • Approachable • Relaxed'
                                    ],
                                    'high_contrast' => [
                                        'name' => 'High Contrast',
                                        'description' => 'Accessibility-focused design with strong contrast ratios',
                                        'icon' => 'eye',
                                        'style_preview' => 'Accessible • High Contrast • Clear'
                                    ]
                                ];
                                
                                foreach ($theme_details as $theme_key => $theme_info):
                                    $is_active = $active_template && is_array($active_template) && 
                                        ((isset($active_template['template_key']) && $active_template['template_key'] === $theme_key) || 
                                         (isset($active_template['template_name']) && strpos(strtolower($active_template['template_name']), strtolower($theme_info['name'])) !== false));
                                ?>
                                    <div class="col-lg-4 col-md-6 mb-4">
                                        <div class="card h-100 theme-card <?= $is_active ? 'border-primary' : '' ?>" style="transition: all 0.3s ease;">
                                            <?php if ($is_active): ?>
                                                <div style="position: absolute; top: 10px; right: 10px; z-index: 10;">
                                                    <span class="badge bg-primary">
                                                        <i class="bi bi-check me-1"></i>Active
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="card-body">
                                                <div class="text-center mb-3">
                                                    <i class="bi bi-<?= $theme_info['icon'] ?> text-primary" style="font-size: 2.5rem;"></i>
                                                </div>
                                                
                                                <h6 class="card-title text-center"><?= htmlspecialchars($theme_info['name']) ?></h6>
                                                <p class="card-text text-muted text-center" style="font-size: 0.9em;">
                                                    <?= htmlspecialchars($theme_info['description']) ?>
                                                </p>
                                                
                                                <div class="text-center mb-3">
                                                    <small class="text-primary fw-bold"><?= $theme_info['style_preview'] ?></small>
                                                </div>
                                                
                                                <!-- Theme Preview Colors -->
                                                <div class="text-center mb-3">
                                                    <div class="d-inline-flex gap-1">
                                                        <div style="width: 20px; height: 20px; background: var(--brand-primary-color, #669999); border-radius: 50%; border: 2px solid #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.2);"></div>
                                                        <div style="width: 20px; height: 20px; background: var(--brand-secondary-color, #b3ced1); border-radius: 50%; border: 2px solid #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.2);"></div>
                                                        <div style="width: 20px; height: 20px; background: #ffffff; border-radius: 50%; border: 2px solid #ddd; box-shadow: 0 1px 3px rgba(0,0,0,0.2);"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="card-footer bg-transparent">
                                                <?php if (!$is_active): ?>
                                                    <form method="POST" class="d-inline-block w-100">
                                                        <input type="hidden" name="action" value="select_theme">
                                                        <input type="hidden" name="template_key" value="<?= htmlspecialchars($theme_key) ?>">
                                                        <button type="submit" class="btn btn-outline-primary w-100">
                                                            <i class="bi bi-check2 me-2"></i>Select Theme
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <button type="button" class="btn btn-primary w-100" disabled>
                                                        <i class="bi bi-star me-2"></i>Currently Active
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="alert alert-light">
                                        <h6><i class="bi bi-lightbulb me-2"></i>Theme Information</h6>
                                        <ul class="mb-0 small">
                                            <li><strong>Professional Design:</strong> All themes are designed to be attractive and professional, never obnoxious</li>
                                            <li><strong>Your Brand Colors:</strong> Themes automatically use the brand colors you've set above</li>
                                            <li><strong>Responsive:</strong> All themes work perfectly on desktop, tablet, and mobile devices</li>
                                            <li><strong>Easy Switching:</strong> You can change themes anytime - the switch is instant</li>
                                            <li><strong>Preview:</strong> <a href="../brand-theme-test.php" target="_blank" class="text-primary">Test your active theme <i class="bi bi-box-arrow-up-right"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Brand Fonts Section -->
                        <div class="section-header">
                            <h4>
                                <i class="bi bi-type" aria-hidden="true"></i>
                                Brand Fonts
                            </h4>
                            <small>Configure typography - supports Google Fonts, system fonts, and custom uploaded fonts</small>
                        </div>
                        
                        <div class="form-section">
                            <!-- Font Instructions -->
                            <div class="alert alert-info mb-4">
                                <strong>Font Configuration Options:</strong><br>
                                • <strong>Google Fonts:</strong> Use font names like "Roboto", "Poppins", "Open Sans"<br>
                                • <strong>System Fonts:</strong> Use standard fonts like "Arial", "Helvetica", "Times New Roman"<br>
                                • <strong>Custom Fonts:</strong> Upload font files below and reference them by name<br>
                                • <strong>Font Stacks:</strong> Always include fallbacks: "CustomFont, Arial, sans-serif"
                            </div>
                            
                            <!-- Primary Font Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Primary Font Family <span class="text-danger">*</span></label>
                                    <select name="brand_font_family" class="form-select font-select" data-font-preview="primary">
                                        <?php
                                        $available_fonts = getAvailableFonts();
                                        $current_font = $brand_font_family;
                                        
                                        // Add current font as first option if it's not in the list
                                        $found_current = false;
                                        foreach ($available_fonts as $font) {
                                            if ($font['family'] === $current_font) {
                                                $found_current = true;
                                                break;
                                            }
                                        }
                                        
                                        if (!$found_current && !empty($current_font)) {
                                            echo '<option value="' . htmlspecialchars($current_font) . '" selected>Current: ' . htmlspecialchars($current_font) . '</option>';
                                        }
                                        
                                        // Group fonts by type
                                        $google_fonts = array_filter($available_fonts, function($f) { return $f['type'] === 'google'; });
                                        $system_fonts = array_filter($available_fonts, function($f) { return $f['type'] === 'system'; });
                                        $custom_fonts = array_filter($available_fonts, function($f) { return $f['type'] === 'custom'; });
                                        
                                        if (!empty($google_fonts)) {
                                            echo '<optgroup label="Google Fonts">';
                                            foreach ($google_fonts as $font) {
                                                $selected = ($font['family'] === $current_font) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($font['family']) . '" ' . $selected . '>' . htmlspecialchars($font['name']) . '</option>';
                                            }
                                            echo '</optgroup>';
                                        }
                                        
                                        if (!empty($system_fonts)) {
                                            echo '<optgroup label="System Fonts">';
                                            foreach ($system_fonts as $font) {
                                                $selected = ($font['family'] === $current_font) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($font['family']) . '" ' . $selected . '>' . htmlspecialchars($font['name']) . '</option>';
                                            }
                                            echo '</optgroup>';
                                        }
                                        
                                        if (!empty($custom_fonts)) {
                                            echo '<optgroup label="Custom Uploaded Fonts">';
                                            foreach ($custom_fonts as $font) {
                                                $selected = ($font['family'] === $current_font) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($font['family']) . '" ' . $selected . '>' . htmlspecialchars($font['name']) . '</option>';
                                            }
                                            echo '</optgroup>';
                                        }
                                        ?>
                                    </select>
                                    <small class="text-muted">Main font used throughout site (overrides any default fonts)</small>
                                    <div class="font-preview mt-2" id="primary-preview" style="font-family: <?php echo htmlspecialchars($brand_font_family); ?>;">
                                        The quick brown fox jumps over the lazy dog. 1234567890
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Headings Font</label>
                                    <select name="brand_font_headings" class="form-select font-select" data-font-preview="headings">
                                        <?php
                                        $current_headings = $brand_font_headings ?? '';
                                        
                                        echo '<option value="">Use Primary Font</option>';
                                        
                                        // Add current font as option if it's not in the list
                                        $found_current_headings = false;
                                        foreach ($available_fonts as $font) {
                                            if ($font['family'] === $current_headings) {
                                                $found_current_headings = true;
                                                break;
                                            }
                                        }
                                        
                                        if (!$found_current_headings && !empty($current_headings)) {
                                            echo '<option value="' . htmlspecialchars($current_headings) . '" selected>Current: ' . htmlspecialchars($current_headings) . '</option>';
                                        }
                                        
                                        if (!empty($google_fonts)) {
                                            echo '<optgroup label="Google Fonts">';
                                            foreach ($google_fonts as $font) {
                                                $selected = ($font['family'] === $current_headings) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($font['family']) . '" ' . $selected . '>' . htmlspecialchars($font['name']) . '</option>';
                                            }
                                            echo '</optgroup>';
                                        }
                                        
                                        if (!empty($system_fonts)) {
                                            echo '<optgroup label="System Fonts">';
                                            foreach ($system_fonts as $font) {
                                                $selected = ($font['family'] === $current_headings) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($font['family']) . '" ' . $selected . '>' . htmlspecialchars($font['name']) . '</option>';
                                            }
                                            echo '</optgroup>';
                                        }
                                        
                                        if (!empty($custom_fonts)) {
                                            echo '<optgroup label="Custom Uploaded Fonts">';
                                            foreach ($custom_fonts as $font) {
                                                $selected = ($font['family'] === $current_headings) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($font['family']) . '" ' . $selected . '>' . htmlspecialchars($font['name']) . '</option>';
                                            }
                                            echo '</optgroup>';
                                        }
                                        ?>
                                    </select>
                                    <small class="text-muted">Font for headings (h1, h2, etc.)</small>
                                    <div class="font-preview mt-2" id="headings-preview" style="font-family: <?php echo htmlspecialchars($brand_font_headings ?? 'inherit'); ?>; font-weight: bold; font-size: 18px;">
                                        Sample Heading Text
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Secondary Font Row -->
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label class="form-label">Body Text Font</label>
                                    <select name="brand_font_body" class="form-select font-select" data-font-preview="body">
                                        <?php
                                        $current_body = $brand_font_body ?? '';
                                        
                                        echo '<option value="">Use Primary Font</option>';
                                        
                                        // Add current font as option if it's not in the list
                                        $found_current_body = false;
                                        foreach ($available_fonts as $font) {
                                            if ($font['family'] === $current_body) {
                                                $found_current_body = true;
                                                break;
                                            }
                                        }
                                        
                                        if (!$found_current_body && !empty($current_body)) {
                                            echo '<option value="' . htmlspecialchars($current_body) . '" selected>Current: ' . htmlspecialchars($current_body) . '</option>';
                                        }
                                        
                                        if (!empty($google_fonts)) {
                                            echo '<optgroup label="Google Fonts">';
                                            foreach ($google_fonts as $font) {
                                                $selected = ($font['family'] === $current_body) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($font['family']) . '" ' . $selected . '>' . htmlspecialchars($font['name']) . '</option>';
                                            }
                                            echo '</optgroup>';
                                        }
                                        
                                        if (!empty($system_fonts)) {
                                            echo '<optgroup label="System Fonts">';
                                            foreach ($system_fonts as $font) {
                                                $selected = ($font['family'] === $current_body) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($font['family']) . '" ' . $selected . '>' . htmlspecialchars($font['name']) . '</option>';
                                            }
                                            echo '</optgroup>';
                                        }
                                        
                                        if (!empty($custom_fonts)) {
                                            echo '<optgroup label="Custom Uploaded Fonts">';
                                            foreach ($custom_fonts as $font) {
                                                $selected = ($font['family'] === $current_body) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($font['family']) . '" ' . $selected . '>' . htmlspecialchars($font['name']) . '</option>';
                                            }
                                            echo '</optgroup>';
                                        }
                                        ?>
                                    </select>
                                    <small class="text-muted">Font for paragraphs and content</small>
                                    <div class="font-preview mt-2" id="body-preview" style="font-family: <?php echo htmlspecialchars($brand_font_body ?? 'inherit'); ?>;">
                                        Body text sample for readability testing.
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Accent Font</label>
                                    <select name="brand_font_accent" class="form-select font-select" data-font-preview="accent">
                                        <?php
                                        $current_accent = $brand_font_accent ?? '';
                                        
                                        echo '<option value="">Use Primary Font</option>';
                                        
                                        // Add current font as option if it's not in the list
                                        $found_current_accent = false;
                                        foreach ($available_fonts as $font) {
                                            if ($font['family'] === $current_accent) {
                                                $found_current_accent = true;
                                                break;
                                            }
                                        }
                                        
                                        if (!$found_current_accent && !empty($current_accent)) {
                                            echo '<option value="' . htmlspecialchars($current_accent) . '" selected>Current: ' . htmlspecialchars($current_accent) . '</option>';
                                        }
                                        
                                        if (!empty($google_fonts)) {
                                            echo '<optgroup label="Google Fonts">';
                                            foreach ($google_fonts as $font) {
                                                $selected = ($font['family'] === $current_accent) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($font['family']) . '" ' . $selected . '>' . htmlspecialchars($font['name']) . '</option>';
                                            }
                                            echo '</optgroup>';
                                        }
                                        
                                        if (!empty($system_fonts)) {
                                            echo '<optgroup label="System Fonts">';
                                            foreach ($system_fonts as $font) {
                                                $selected = ($font['family'] === $current_accent) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($font['family']) . '" ' . $selected . '>' . htmlspecialchars($font['name']) . '</option>';
                                            }
                                            echo '</optgroup>';
                                        }
                                        
                                        if (!empty($custom_fonts)) {
                                            echo '<optgroup label="Custom Uploaded Fonts">';
                                            foreach ($custom_fonts as $font) {
                                                $selected = ($font['family'] === $current_accent) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($font['family']) . '" ' . $selected . '>' . htmlspecialchars($font['name']) . '</option>';
                                            }
                                            echo '</optgroup>';
                                        }
                                        ?>
                                    </select>
                                    <small class="text-muted">Font for special elements and highlights</small>
                                    <div class="font-preview mt-2" id="accent-preview" style="font-family: <?php echo htmlspecialchars($brand_font_accent ?? 'inherit'); ?>; font-style: italic;">
                                        Accent text sample
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Monospace Font</label>
                                    <select name="brand_font_monospace" class="form-select font-select" data-font-preview="monospace">
                                        <?php
                                        $current_monospace = $brand_font_monospace ?? '';
                                        
                                        echo '<option value="">Use System Monospace</option>';
                                        
                                        // Add current font as option if it's not in the list
                                        $found_current_monospace = false;
                                        foreach ($available_fonts as $font) {
                                            if ($font['family'] === $current_monospace) {
                                                $found_current_monospace = true;
                                                break;
                                            }
                                        }
                                        
                                        if (!$found_current_monospace && !empty($current_monospace)) {
                                            echo '<option value="' . htmlspecialchars($current_monospace) . '" selected>Current: ' . htmlspecialchars($current_monospace) . '</option>';
                                        }
                                        
                                        // Filter monospace fonts
                                        $monospace_fonts = array_filter($available_fonts, function($f) { return $f['category'] === 'monospace'; });
                                        
                                        if (!empty($monospace_fonts)) {
                                            echo '<optgroup label="Monospace Fonts">';
                                            foreach ($monospace_fonts as $font) {
                                                $selected = ($font['family'] === $current_monospace) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($font['family']) . '" ' . $selected . '>' . htmlspecialchars($font['name']) . '</option>';
                                            }
                                            echo '</optgroup>';
                                        }
                                        
                                        if (!empty($custom_fonts)) {
                                            echo '<optgroup label="Custom Uploaded Fonts">';
                                            foreach ($custom_fonts as $font) {
                                                $selected = ($font['family'] === $current_monospace) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($font['family']) . '" ' . $selected . '>' . htmlspecialchars($font['name']) . '</option>';
                                            }
                                            echo '</optgroup>';
                                        }
                                        ?>
                                    </select>
                                    <small class="text-muted">Font for code and technical content</small>
                                    <div class="font-preview mt-2" id="monospace-preview" style="font-family: <?php echo htmlspecialchars($brand_font_monospace ?? 'monospace'); ?>; background: #374151; padding: 8px; border-radius: 4px;">
                                        code { font-family: monospace; }
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Display Font and Author Row -->
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label class="form-label">Display Font</label>
                                    <select name="brand_font_display" class="form-select font-select" data-font-preview="display">
                                        <?php
                                        $current_display = $brand_font_display ?? '';
                                        
                                        echo '<option value="">Use Primary Font</option>';
                                        
                                        // Add current font as option if it's not in the list
                                        $found_current_display = false;
                                        foreach ($available_fonts as $font) {
                                            if ($font['family'] === $current_display) {
                                                $found_current_display = true;
                                                break;
                                            }
                                        }
                                        
                                        if (!$found_current_display && !empty($current_display)) {
                                            echo '<option value="' . htmlspecialchars($current_display) . '" selected>Current: ' . htmlspecialchars($current_display) . '</option>';
                                        }
                                        
                                        // Filter display fonts (serif and display category)
                                        $display_fonts = array_filter($available_fonts, function($f) { 
                                            return $f['category'] === 'serif' || $f['category'] === 'display'; 
                                        });
                                        
                                        if (!empty($display_fonts)) {
                                            echo '<optgroup label="Display & Serif Fonts">';
                                            foreach ($display_fonts as $font) {
                                                $selected = ($font['family'] === $current_display) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($font['family']) . '" ' . $selected . '>' . htmlspecialchars($font['name']) . '</option>';
                                            }
                                            echo '</optgroup>';
                                        }
                                        
                                        // Also include some sans-serif options for display
                                        $sans_serif_fonts = array_filter($available_fonts, function($f) { 
                                            return $f['category'] === 'sans-serif'; 
                                        });
                                        
                                        if (!empty($sans_serif_fonts)) {
                                            echo '<optgroup label="Sans-Serif Fonts">';
                                            foreach (array_slice($sans_serif_fonts, 0, 5) as $font) { // Limit to first 5
                                                $selected = ($font['family'] === $current_display) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($font['family']) . '" ' . $selected . '>' . htmlspecialchars($font['name']) . '</option>';
                                            }
                                            echo '</optgroup>';
                                        }
                                        
                                        if (!empty($custom_fonts)) {
                                            echo '<optgroup label="Custom Uploaded Fonts">';
                                            foreach ($custom_fonts as $font) {
                                                $selected = ($font['family'] === $current_display) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($font['family']) . '" ' . $selected . '>' . htmlspecialchars($font['name']) . '</option>';
                                            }
                                            echo '</optgroup>';
                                        }
                                        ?>
                                    </select>
                                    <small class="text-muted">Font for large display text and decorative elements</small>
                                    <div class="font-preview mt-2" id="display-preview" style="font-family: <?php echo htmlspecialchars($brand_font_display ?? 'serif'); ?>; font-size: 24px; font-weight: bold;">
                                        Display Typography
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Author</label>
                                    <input type="text" name="author" class="form-control" 
                                           value="<?php echo htmlspecialchars($author); ?>"
                                           placeholder="Website Author Name">
                                    <small class="text-muted">Used in meta tags and copyright</small>
                                </div>
                            </div>
                            
                            <!-- Custom Font Upload Section -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5 class="mb-3">Custom Font Upload</h5>
                                    <p class="text-muted mb-3">Upload your own font files to use custom typography. Supported formats: WOFF2, WOFF, TTF</p>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Font File 1</label>
                                    <div class="input-group">
                                        <input type="file" name="font_upload_1" class="form-control" accept=".woff2,.woff,.ttf,.otf">
                                        <input type="text" name="brand_font_file_1" class="form-control" 
                                               value="<?php echo htmlspecialchars($brand_font_file_1 ?? ''); ?>"
                                               placeholder="Current: filename.woff2" readonly>
                                    </div>
                                    <small class="text-muted">Upload primary custom font file</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Font File 2</label>
                                    <div class="input-group">
                                        <input type="file" name="font_upload_2" class="form-control" accept=".woff2,.woff,.ttf,.otf">
                                        <input type="text" name="brand_font_file_2" class="form-control" 
                                               value="<?php echo htmlspecialchars($brand_font_file_2 ?? ''); ?>"
                                               placeholder="Current: filename.woff2" readonly>
                                    </div>
                                    <small class="text-muted">Upload secondary custom font file</small>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label class="form-label">Font File 3</label>
                                    <div class="input-group">
                                        <input type="file" name="font_upload_3" class="form-control" accept=".woff2,.woff,.ttf,.otf">
                                        <input type="text" name="brand_font_file_3" class="form-control" 
                                               value="<?php echo htmlspecialchars($brand_font_file_3 ?? ''); ?>"
                                               placeholder="Current: filename.woff2" readonly>
                                    </div>
                                    <small class="text-muted">Additional font file</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Font File 4</label>
                                    <div class="input-group">
                                        <input type="file" name="font_upload_4" class="form-control" accept=".woff2,.woff,.ttf,.otf">
                                        <input type="text" name="brand_font_file_4" class="form-control" 
                                               value="<?php echo htmlspecialchars($brand_font_file_4 ?? ''); ?>"
                                               placeholder="Current: filename.woff2" readonly>
                                    </div>
                                    <small class="text-muted">Additional font file</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Font File 5</label>
                                    <div class="input-group">
                                        <input type="file" name="font_upload_5" class="form-control" accept=".woff2,.woff,.ttf,.otf">
                                        <input type="text" name="brand_font_file_5" class="form-control" 
                                               value="<?php echo htmlspecialchars($brand_font_file_5 ?? ''); ?>"
                                               placeholder="Current: filename.woff2" readonly>
                                    </div>
                                    <small class="text-muted">Additional font file</small>
                                </div>
                            </div>
                            
                            <!-- Google Fonts Quick Selection -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5 class="mb-3">Quick Google Fonts Selection</h5>
                                    <p class="text-muted mb-3">Click to quickly apply popular Google Fonts combinations</p>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-outline-secondary w-100 font-preset-btn" 
                                            data-preset="modern">
                                        <strong style="font-family: 'Inter', sans-serif;">Modern</strong><br>
                                        <small>Inter + Roboto</small>
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-outline-secondary w-100 font-preset-btn" 
                                            data-preset="classic">
                                        <strong style="font-family: 'Playfair Display', serif;">Classic</strong><br>
                                        <small>Playfair + Source Sans</small>
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-outline-secondary w-100 font-preset-btn" 
                                            data-preset="minimal">
                                        <strong style="font-family: 'Poppins', sans-serif;">Minimal</strong><br>
                                        <small>Poppins + Open Sans</small>
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-outline-secondary w-100 font-preset-btn" 
                                            data-preset="tech">
                                        <strong style="font-family: 'JetBrains Mono', monospace;">Tech</strong><br>
                                        <small>Fira Code + System</small>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Logos Section -->
                        <div class="section-header">
                            <h4><i class="bi bi-image" aria-hidden="true"></i> Logo Management</h4>
                            <small>Configure logo variations for different uses</small>
                        </div>
                        
                        <div class="form-section">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Main Logo <span class="text-danger">*</span></label>
                                    <input type="text" name="business_logo" class="form-control" 
                                           value="<?php echo htmlspecialchars($business_logo); ?>"
                                           placeholder="assets/img/logo.png">
                                    <small class="text-muted">Primary logo used throughout site</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Horizontal Logo</label>
                                    <input type="text" name="business_logo_horizontal" class="form-control" 
                                           value="<?php echo htmlspecialchars($business_logo_horizontal ?? ''); ?>"
                                           placeholder="assets/branding/logo_horizontal.png">
                                    <small class="text-muted">Wide format logo for headers</small>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label class="form-label">Vertical Logo</label>
                                    <input type="text" name="business_logo_vertical" class="form-control" 
                                           value="<?php echo htmlspecialchars($business_logo_vertical ?? ''); ?>"
                                           placeholder="assets/branding/logo_vertical.png">
                                    <small class="text-muted">Tall format logo</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Square Logo</label>
                                    <input type="text" name="business_logo_square" class="form-control" 
                                           value="<?php echo htmlspecialchars($business_logo_square ?? ''); ?>"
                                           placeholder="assets/branding/logo_square.png">
                                    <small class="text-muted">Square format logo</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">White Logo</label>
                                    <input type="text" name="business_logo_white" class="form-control" 
                                           value="<?php echo htmlspecialchars($business_logo_white ?? ''); ?>"
                                           placeholder="assets/branding/logo_white.png">
                                    <small class="text-muted">White version for dark backgrounds</small>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label class="form-label">Small Logo</label>
                                    <input type="text" name="business_logo_small" class="form-control" 
                                           value="<?php echo htmlspecialchars($business_logo_small ?? ''); ?>"
                                           placeholder="assets/branding/logo_small.png">
                                    <small class="text-muted">Small version for tight spaces</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Favicons Section -->
                        <div class="section-header">
                            <h4><i class="bi bi-star-fill" aria-hidden="true"></i> Favicons</h4>
                            <small>Configure favicon variations</small>
                        </div>
                        
                        <div class="form-section">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Main Favicon <span class="text-danger">*</span></label>
                                    <input type="text" name="favicon" class="form-control" 
                                           value="<?php echo htmlspecialchars($favicon); ?>"
                                           placeholder="assets/img/favicon.png">
                                    <small class="text-muted">Main site favicon</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Apple Touch Icon <span class="text-danger">*</span></label>
                                    <input type="text" name="apple_touch_icon" class="form-control" 
                                           value="<?php echo htmlspecialchars($apple_touch_icon); ?>"
                                           placeholder="assets/img/apple-touch-icon.png">
                                    <small class="text-muted">Apple device icon</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Blog Favicon</label>
                                    <input type="text" name="favicon_blog" class="form-control" 
                                           value="<?php echo htmlspecialchars($favicon_blog ?? ''); ?>"
                                           placeholder="assets/branding/favicon_blog.ico">
                                    <small class="text-muted">Special favicon for blog</small>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label class="form-label">Portal Favicon</label>
                                    <input type="text" name="favicon_portal" class="form-control" 
                                           value="<?php echo htmlspecialchars($favicon_portal ?? ''); ?>"
                                           placeholder="assets/branding/favicon_portal.ico">
                                    <small class="text-muted">Special favicon for client portal</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Social Share Images Section -->
                        <div class="section-header">
                            <h4><i class="bi bi-share" aria-hidden="true"></i> Social Share Images</h4>
                            <small>Configure social media share images</small>
                        </div>
                        
                        <div class="form-section">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Default Social Share</label>
                                    <input type="text" name="social_share_default" class="form-control" 
                                           value="<?php echo htmlspecialchars($social_share_default ?? ''); ?>"
                                           placeholder="assets/branding/social_default.jpg">
                                    <small class="text-muted">Default social share image</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Facebook Share</label>
                                    <input type="text" name="social_share_facebook" class="form-control" 
                                           value="<?php echo htmlspecialchars($social_share_facebook ?? ''); ?>"
                                           placeholder="assets/branding/social_facebook.jpg">
                                    <small class="text-muted">Facebook optimized (1200x630)</small>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label class="form-label">Twitter Share</label>
                                    <input type="text" name="social_share_twitter" class="form-control" 
                                           value="<?php echo htmlspecialchars($social_share_twitter ?? ''); ?>"
                                           placeholder="assets/branding/social_twitter.jpg">
                                    <small class="text-muted">Twitter optimized (1024x512)</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">LinkedIn Share</label>
                                    <input type="text" name="social_share_linkedin" class="form-control" 
                                           value="<?php echo htmlspecialchars($social_share_linkedin ?? ''); ?>"
                                           placeholder="assets/branding/social_linkedin.jpg">
                                    <small class="text-muted">LinkedIn optimized (1200x627)</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Instagram Share</label>
                                    <input type="text" name="social_share_instagram" class="form-control" 
                                           value="<?php echo htmlspecialchars($social_share_instagram ?? ''); ?>"
                                           placeholder="assets/branding/social_instagram.jpg">
                                    <small class="text-muted">Instagram optimized (1080x1080)</small>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label class="form-label">Blog Share</label>
                                    <input type="text" name="social_share_blog" class="form-control" 
                                           value="<?php echo htmlspecialchars($social_share_blog ?? ''); ?>"
                                           placeholder="assets/branding/social_blog.jpg">
                                    <small class="text-muted">Blog posts social share</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Enhanced Template System Note -->
                        <div class="section-header">
                            <h4><i class="bi bi-grid-3x3-gap" aria-hidden="true"></i> Visual Template Management</h4>
                            <small>For advanced visual styling and template management</small>
                        </div>
                        
                        <div class="form-section">
                            <div class="alert alert-info">
                                <h5><i class="bi bi-info-circle" aria-hidden="true"></i> Enhanced Template System</h5>
                                <p class="mb-2">Visual templates are now managed through the enhanced database-driven system which provides:</p>
                                <ul class="mb-3">
                                    <li>Multi-area template support (Public, Admin, Client Portal)</li>
                                    <li>Database-driven template storage</li>
                                    <li>Live preview capabilities</li>
                                    <li>Professional styling options (Default, High-contrast, Subtle, Bold, Casual)</li>
                                </ul>
                                <a href="branding-templates-enhanced.php" class="btn btn-info">
                                    <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> Access Enhanced Template Manager
                                </a>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="button-group">
                            <a href="settings_dash.php" class="btn btn-secondary btn-lg">
                                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                                &nbsp;&nbsp;Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save" aria-hidden="true"></i>
                                &nbsp;&nbsp;Save Branding Settings
                            </button>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * Enhanced Color Input Management
 * Provides bidirectional synchronization between color picker and hex input
 */

// Validate hex color format
function isValidHex(hex) {
    return /^#[0-9A-Fa-f]{6}$/.test(hex);
}

// Normalize hex color (add # if missing, convert to uppercase, strip unwanted characters)
function normalizeHex(hex) {
    // Strip common unwanted characters that get copied from CSS/design tools
    hex = hex.trim()
             .replace(/[;,\s]/g, '') // Remove semicolons, commas, spaces
             .replace(/^(color:|background-color:|#)/i, '') // Remove CSS property prefixes
             .replace(/#/g, ''); // Remove any # symbols
    
    // Add the # prefix
    hex = '#' + hex;
    
    return hex.toLowerCase();
}

// Update color picker when hex input changes
function updateColorPicker(hexInput) {
    // First normalize/cleanup the input
    const cleanedHex = normalizeHex(hexInput.value);
    
    // Update the input field with the cleaned value
    hexInput.value = cleanedHex;
    
    // Then validate the cleaned hex
    if (isValidHex(cleanedHex)) {
        const colorPicker = hexInput.previousElementSibling;
        colorPicker.value = cleanedHex;
        hexInput.style.borderColor = '';
        hexInput.title = 'Valid hex color';
    } else {
        hexInput.style.borderColor = '#dc3545';
        hexInput.title = 'Invalid hex color format. Use #RRGGBB (e.g., #FF5733)';
    }
}

// Update hex input when color picker changes
function updateHexInput(colorPicker) {
    const hexInput = colorPicker.nextElementSibling;
    hexInput.value = colorPicker.value.toUpperCase();
    hexInput.style.borderColor = '';
}

// Legacy function for backward compatibility
function updateColorPreview(input) {
    if (input.type === 'color') {
        updateHexInput(input);
    } else {
        updateColorPicker(input);
    }
}

// Initialize color input synchronization
document.addEventListener('DOMContentLoaded', function() {
    // Set up color picker listeners
    document.querySelectorAll('.color-picker').forEach(picker => {
        picker.addEventListener('change', function() {
            updateHexInput(this);
        });
        
        picker.addEventListener('input', function() {
            updateHexInput(this);
        });
    });

    // Set up hex input listeners
    document.querySelectorAll('.color-hex').forEach(hexInput => {
        // Live validation and cleanup as user types
        hexInput.addEventListener('input', function() {
            // Clean up immediately on any input
            updateColorPicker(this);
        });
        
        // Additional cleanup on paste events
        hexInput.addEventListener('paste', function(e) {
            // Small delay to let paste complete, then clean up
            setTimeout(() => {
                updateColorPicker(this);
            }, 10);
        });
        
        // Final validation on blur
        hexInput.addEventListener('blur', function() {
            updateColorPicker(this);
        });
        
        // Handle Enter key
        hexInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                updateColorPicker(this);
                this.blur();
            }
        });
    });

    // Form submission validation
    document.querySelector('form').addEventListener('submit', function(e) {
        let hasErrors = false;
        
        document.querySelectorAll('.color-hex').forEach(hexInput => {
            const hex = normalizeHex(hexInput.value);
            if (!isValidHex(hex)) {
                hexInput.style.borderColor = '#dc3545';
                hasErrors = true;
            }
        });
        
        if (hasErrors) {
            e.preventDefault();
            alert('Please fix invalid hex color values before submitting.');
            return false;
        }
    });
    
    // Font Preview and Management System
    function updateFontPreview(input) {
        const previewType = input.dataset.fontPreview;
        const previewElement = document.getElementById(previewType + '-preview');
        const fontFamily = input.value || 'inherit';
        
        if (previewElement) {
            previewElement.style.fontFamily = fontFamily;
            
            // Load Google Fonts dynamically for preview
            if (fontFamily && !fontFamily.includes('Arial') && !fontFamily.includes('Helvetica') && !fontFamily.includes('sans-serif')) {
                const fontName = fontFamily.split(',')[0].replace(/['"]/g, '').trim();
                if (fontName && fontName !== 'inherit') {
                    loadGoogleFont(fontName);
                }
            }
        }
    }
    
    function loadGoogleFont(fontName) {
        // Check if font is already loaded
        const existingLink = document.querySelector(`link[href*="${fontName.replace(' ', '+')}"]`);
        if (existingLink) return;
        
        // Create link element for Google Fonts
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = `https://fonts.googleapis.com/css2?family=${fontName.replace(' ', '+')}:wght@300;400;500;600;700&display=swap`;
        document.head.appendChild(link);
    }
    
    // Font input listeners
    document.querySelectorAll('.font-input').forEach(input => {
        input.addEventListener('input', function() {
            updateFontPreview(this);
        });
        
        input.addEventListener('blur', function() {
            updateFontPreview(this);
        });
    });
    
    // Google Fonts Presets
    const fontPresets = {
        modern: {
            primary: 'Inter, system-ui, -apple-system, sans-serif',
            headings: 'Inter, system-ui, -apple-system, sans-serif',
            body: 'Inter, system-ui, -apple-system, sans-serif',
            accent: 'Inter, system-ui, -apple-system, sans-serif',
            monospace: 'JetBrains Mono, Fira Code, Consolas, monospace',
            display: 'Inter, system-ui, -apple-system, sans-serif'
        },
        classic: {
            primary: 'Source Sans Pro, Arial, sans-serif',
            headings: 'Playfair Display, Georgia, serif',
            body: 'Source Sans Pro, Arial, sans-serif',
            accent: 'Playfair Display, Georgia, serif',
            monospace: 'Source Code Pro, Consolas, monospace',
            display: 'Playfair Display, Georgia, serif'
        },
        minimal: {
            primary: 'Open Sans, Arial, sans-serif',
            headings: 'Poppins, Arial, sans-serif',
            body: 'Open Sans, Arial, sans-serif',
            accent: 'Poppins, Arial, sans-serif',
            monospace: 'Roboto Mono, Consolas, monospace',
            display: 'Poppins, Arial, sans-serif'
        },
        tech: {
            primary: 'System UI, -apple-system, BlinkMacSystemFont, sans-serif',
            headings: 'System UI, -apple-system, BlinkMacSystemFont, sans-serif',
            body: 'System UI, -apple-system, BlinkMacSystemFont, sans-serif',
            accent: 'System UI, -apple-system, BlinkMacSystemFont, sans-serif',
            monospace: 'Fira Code, JetBrains Mono, Consolas, monospace',
            display: 'System UI, -apple-system, BlinkMacSystemFont, sans-serif'
        }
    };
    
    // Font preset buttons
    document.querySelectorAll('.font-preset-btn').forEach(button => {
        button.addEventListener('click', function() {
            const preset = this.dataset.preset;
            const fonts = fontPresets[preset];
            
            if (fonts) {
                // Apply preset fonts to form selects (now dropdowns)
                const primarySelect = document.querySelector('select[name="brand_font_family"]');
                const headingsSelect = document.querySelector('select[name="brand_font_headings"]');
                const bodySelect = document.querySelector('select[name="brand_font_body"]');
                const accentSelect = document.querySelector('select[name="brand_font_accent"]');
                const monospaceSelect = document.querySelector('select[name="brand_font_monospace"]');
                const displaySelect = document.querySelector('select[name="brand_font_display"]');
                
                // Set values and trigger change events
                if (primarySelect) {
                    primarySelect.value = fonts.primary;
                    primarySelect.dispatchEvent(new Event('change'));
                }
                if (headingsSelect) {
                    headingsSelect.value = fonts.headings;
                    headingsSelect.dispatchEvent(new Event('change'));
                }
                if (bodySelect) {
                    bodySelect.value = fonts.body;
                    bodySelect.dispatchEvent(new Event('change'));
                }
                if (accentSelect) {
                    accentSelect.value = fonts.accent;
                    accentSelect.dispatchEvent(new Event('change'));
                }
                if (monospaceSelect) {
                    monospaceSelect.value = fonts.monospace;
                    monospaceSelect.dispatchEvent(new Event('change'));
                }
                if (displaySelect) {
                    displaySelect.value = fonts.display;
                    displaySelect.dispatchEvent(new Event('change'));
                }
                
                // Visual feedback
                this.classList.add('btn-success');
                setTimeout(() => {
                    this.classList.remove('btn-success');
                    this.classList.add('btn-outline-secondary');
                }, 1000);
            }
        });
    });
    
    // Load Google Fonts for presets on page load
    const commonGoogleFonts = ['Inter', 'Playfair Display', 'Source Sans Pro', 'Source Code Pro', 'Open Sans', 'Poppins', 'Roboto Mono', 'JetBrains Mono', 'Fira Code'];
    commonGoogleFonts.forEach(font => {
        loadGoogleFont(font);
    });
    
    // Handle font dropdown changes and update previews
    document.querySelectorAll('.font-select').forEach(select => {
        console.log('Setting up font select:', select.name, 'with preview:', select.dataset.fontPreview);
        
        select.addEventListener('change', function() {
            console.log('Font changed:', this.name, 'to:', this.value);
            const previewId = this.dataset.fontPreview + '-preview';
            const preview = document.getElementById(previewId);
            
            console.log('Looking for preview element:', previewId, 'Found:', !!preview);
            
            if (preview) {
                // Update font family
                const selectedValue = this.value || 'inherit';
                preview.style.fontFamily = selectedValue;
                console.log('Updated font family to:', selectedValue);
                
                // Visual feedback for debugging
                preview.style.border = '3px solid #28a745';
                setTimeout(() => {
                    preview.style.border = '2px solid #4b5563';
                }, 500);
                
                // Load Google Fonts if needed
                if (selectedValue && selectedValue !== 'inherit' && selectedValue !== '') {
                    const fontName = selectedValue.split(',')[0].trim().replace(/['"]/g, '');
                    
                    // Check if it's a Google Font (not system fonts)
                    const isGoogleFont = !['Arial', 'Helvetica', 'Times New Roman', 'Georgia', 'Verdana', 'Consolas', 'Monaco', 'Courier New'].includes(fontName);
                    const isSystemFont = fontName.includes('system-ui') || fontName.includes('-apple-system') || fontName.includes('BlinkMacSystemFont');
                    
                    if (isGoogleFont && !isSystemFont) {
                        console.log('Loading Google Font:', fontName);
                        loadGoogleFont(fontName);
                    }
                }
            } else {
                console.error('Preview element not found:', previewId);
                console.log('Available preview elements:', Array.from(document.querySelectorAll('[id$="-preview"]')).map(el => el.id));
            }
        });
    });
    
    // Debug: List all font selects and previews
    console.log('Font selects found:', document.querySelectorAll('.font-select').length);
    console.log('Preview elements found:', document.querySelectorAll('.font-preview').length);
    
    // Also trigger change event on page load to update previews
    document.querySelectorAll('.font-select').forEach(select => {
        if (select.value) {
            console.log('Triggering initial change for:', select.name, 'value:', select.value);
            select.dispatchEvent(new Event('change'));
        }
    });
});

// Add styling for font previews and presets
const fontStyle = document.createElement('style');
fontStyle.textContent = `
.font-preview {
    background: #374151 !important;
    color: #ffffff !important;
    border: 2px solid #4b5563 !important;
    border-radius: 8px !important;
    padding: 12px 16px !important;
    font-size: 16px !important;
    min-height: 50px !important;
    display: flex !important;
    align-items: center !important;
    transition: all 0.3s ease !important;
    margin-top: 8px !important;
    font-weight: 400 !important;
    line-height: 1.4 !important;
}

.font-preview:hover {
    border-color: #6b7280 !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15) !important;
}

.font-input:focus + .font-preview {
    border-color: var(--admin-brand-primary, #007bff) !important;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25) !important;
}

.font-preset-btn {
    height: 70px;
    border: 2px solid #dee2e6;
    transition: all 0.3s ease;
    margin-bottom: 10px;
}

.font-preset-btn:hover {
    border-color: var(--admin-brand-primary, #007bff);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.font-preset-btn.btn-success {
    border-color: #28a745;
    background-color: #28a745;
    color: white;
}

.font-upload-section {
    background: #f8f9fb;
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    padding: 20px;
    margin-top: 20px;
}

.font-input {
    font-family: 'SF Mono', Consolas, 'Liberation Mono', Menlo, monospace;
    font-size: 13px;
}

.alert-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    border: 1px solid #b6d7ff;
    color: #0c5460;
}

/* Enhanced button group */
.button-group {
    display: flex;
    gap: 15px;
    justify-content: flex-start;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #e9ecef;
}

.button-group .btn {
    display: flex;
    align-items: center;
    font-weight: 600;
    padding: 12px 24px;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.button-group .btn:hover {
    transform: translateY(-1px);
}

/* Debug font selections */
.font-select {
    border: 2px solid #e9ecef !important;
}

.font-select:focus {
    border-color: #007bff !important;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25) !important;
}
`;
document.head.appendChild(fontStyle);

// Add some helpful styling for invalid inputs
const style = document.createElement('style');
style.textContent = `
.color-hex {
    font-family: 'Courier New', monospace;
    text-transform: uppercase;
}
.color-hex:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
.input-group .color-picker {
    max-width: 60px;
}
.input-group .color-hex {
    flex: 1;
}
`;
document.head.appendChild(style);
</script>

<?php echo template_admin_footer(); ?>
