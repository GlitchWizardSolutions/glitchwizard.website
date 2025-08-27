<?php
/*
 * Branding Settings Management Interface - Tabbed Version
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: branding_settings_tabbed.php
 * LOCATION: /public_html/admin/settings/
 * PURPOSE: Comprehensive branding configuration management with tabbed interface
 * 
 * DESCRIPTION:
 * This interface manages all branding elements in an organized tabbed layout:
 * - Business Information (names, contact details)
 * - Brand Colors (6 brand colors with real-time preview)
 * - Visual Themes (5 professional themes for all areas)
 * - Fonts & Typography (6 font options)
 * - Logos & Images (6 logo variations, favicons, social images)
 * - Advanced Settings (footer branding, file uploads)
 * 
 * FEATURES:
 * - Tabbed interface for better organization
 * - Real-time color preview
 * - Theme selection with live admin refresh
 * - File upload handling for all brand assets
 * - Database-driven with instant updates
 * 
 * VERSION: 3.0 (Tabbed Interface)
 * UPDATED: 2025-08-26
 */

// Prevent caching to ensure fresh data from database
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

include_once '../assets/includes/main.php';

// Security check for admin access
if (!isset($_SESSION['admin_role']) || !in_array($_SESSION['admin_role'], ['Admin', 'Developer'])) {
    header('Location: ../index.php');
    exit();
}

// Note: Using consolidated branding functions to avoid conflicts

// Load consolidated branding functions
require_once '../../assets/includes/branding-functions.php';
require_once '../../assets/includes/theme-loader.php';

// Define getAvailableFonts function locally to avoid conflicts
if (!function_exists('getAvailableFonts')) {
    function getAvailableFonts($category = null) {
        global $pdo;
        
        // Start with system fonts
        $fonts = [
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
        
        // Add uploaded custom fonts
        try {
            $stmt = $pdo->query("SELECT font_upload_1, font_upload_2, font_upload_3, font_upload_4, font_upload_5 FROM setting_business_identity WHERE id = 1");
            $custom_fonts = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($custom_fonts) {
                $font_purposes = [
                    'font_upload_1' => 'Regular Body Text',
                    'font_upload_2' => 'Headings & Titles', 
                    'font_upload_3' => 'Formal/Professional',
                    'font_upload_4' => 'Decorative/Fancy',
                    'font_upload_5' => 'Italic/Emphasis'
                ];
                
                foreach ($custom_fonts as $slot => $font_path) {
                    if (!empty($font_path)) {
                        // Check multiple possible paths for the font file
                        $possible_paths = [
                            $font_path,  // Original path
                            '../assets/fonts/custom/' . basename($font_path),  // From admin/settings/ to public_html/assets
                            'assets/fonts/custom/' . basename($font_path),    // From public_html/
                            './assets/fonts/custom/' . basename($font_path)        // From current directory
                        ];
                        
                        $font_exists = false;
                        $working_path = $font_path;
                        
                        foreach ($possible_paths as $test_path) {
                            if (file_exists($test_path)) {
                                $font_exists = true;
                                $working_path = $test_path;
                                break;
                            }
                        }
                        
                        if ($font_exists) {
                            $filename = basename($working_path);
                            $font_name_without_ext = pathinfo($filename, PATHINFO_FILENAME);
                            
                            // Create a readable font family name
                            $display_name = $font_purposes[$slot] . ' (Custom)';
                            
                            $fonts[] = [
                                'family' => $display_name,
                                'category' => 'custom',
                                'weight' => '400',
                                'file_path' => $working_path,
                                'purpose' => $font_purposes[$slot]
                            ];
                        }
                    }
                }
            }
        } catch (Exception $e) {
            // If there's an error loading custom fonts, just continue with system fonts
            error_log("Error loading custom fonts: " . $e->getMessage());
        }
        
        return $fonts;
    }
}

if (!function_exists('generateCustomFontsCSS')) {
    function generateCustomFontsCSS() {
        global $pdo;
        
        try {
            // Get custom font uploads
            $stmt = $pdo->query("SELECT font_upload_1, font_upload_2, font_upload_3, font_upload_4, font_upload_5 FROM setting_business_identity WHERE id = 1");
            $custom_fonts = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$custom_fonts) {
                return false;
            }
            
            $font_purposes = [
                'font_upload_1' => 'Regular Body Text Custom',
                'font_upload_2' => 'Headings Titles Custom', 
                'font_upload_3' => 'Formal Professional Custom',
                'font_upload_4' => 'Decorative Fancy Custom',
                'font_upload_5' => 'Italic Emphasis Custom'
            ];
            
            $css_content = "/* Custom Fonts CSS - Auto-generated by Branding System */\n";
            $css_content .= "/* This file is automatically updated when custom fonts are uploaded */\n\n";
            
            foreach ($custom_fonts as $slot => $font_path) {
                if (!empty($font_path) && file_exists($font_path)) {
                    $filename = basename($font_path);
                    $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    $font_name = $font_purposes[$slot];
                    
                    // Create relative path from CSS file location to the font file
                    $relative_path = '../fonts/custom/' . $filename;
                    
                    $css_content .= "/* {$font_purposes[$slot]} */\n";
                    $css_content .= "@font-face {\n";
                    $css_content .= "    font-family: '{$font_name}';\n";
                    $css_content .= "    src: url('{$relative_path}')";
                    
                    // Add format based on file extension
                    switch ($file_ext) {
                        case 'woff2':
                            $css_content .= " format('woff2')";
                            break;
                        case 'woff':
                            $css_content .= " format('woff')";
                            break;
                        case 'ttf':
                            $css_content .= " format('truetype')";
                            break;
                        case 'otf':
                            $css_content .= " format('opentype')";
                            break;
                    }
                    
                    $css_content .= ";\n";
                    $css_content .= "    font-weight: 400;\n";
                    $css_content .= "    font-style: normal;\n";
                    $css_content .= "    font-display: swap;\n";
                    $css_content .= "}\n\n";
                }
            }
            
            $css_content .= "/* End of auto-generated custom fonts */\n";
            
            // Write to CSS file
            $css_file_path = __DIR__ . '/../../assets/css/custom-fonts.css';
            $css_dir = dirname($css_file_path);
            
            // Create directory if it doesn't exist
            if (!is_dir($css_dir)) {
                mkdir($css_dir, 0755, true);
            }
            
            return file_put_contents($css_file_path, $css_content) !== false;
            
        } catch (Exception $e) {
            error_log("Error generating custom fonts CSS: " . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('redirectWithMessage')) {
    function redirectWithMessage($success_msg = '', $error_msg = '', $tab = '') {
        if (!empty($success_msg)) {
            $_SESSION['branding_success'] = $success_msg;
        }
        if (!empty($error_msg)) {
            $_SESSION['branding_error'] = $error_msg;
        }
        if (!empty($tab)) {
            $_SESSION['branding_tab'] = $tab;
        }
        
        // Build redirect URL with hash for tab
        $redirect_url = $_SERVER['PHP_SELF'];
        if (!empty($tab)) {
            $tab_hash = str_replace('-tab', '', $tab);
            $redirect_url .= '#' . $tab_hash;
        }
        
        header("Location: $redirect_url");
        exit;
    }
}

// Initialize messages
$success_message = '';
$error_message = '';

// Check for redirect messages from session
if (isset($_SESSION['branding_success'])) {
    $success_message = $_SESSION['branding_success'];
    unset($_SESSION['branding_success']);
}
if (isset($_SESSION['branding_error'])) {
    $error_message = $_SESSION['branding_error'];
    unset($_SESSION['branding_error']);
}
if (isset($_SESSION['branding_tab'])) {
    $active_tab = $_SESSION['branding_tab'];
    unset($_SESSION['branding_tab']);
}

// Handle theme selection
if (isset($_POST['action']) && $_POST['action'] === 'select_theme' && isset($_POST['template_key'])) {
    // Use a more reliable sanitization method
    $template_key = isset($_POST['template_key']) ? trim(strip_tags($_POST['template_key'])) : '';
    
    // Validate against allowed template keys for security
    $allowed_keys = ['default', 'subtle', 'bold', 'casual', 'high-contrast'];
    
    if (in_array($template_key, $allowed_keys)) {
        // Direct database activation without transaction to avoid trigger conflicts
        try {
            global $pdo;
            
            // First, deactivate all admin templates
            $stmt1 = $pdo->prepare("UPDATE setting_branding_templates SET is_active = 0 WHERE area = 'admin'");
            $deactivate_result = $stmt1->execute();
            
            if ($deactivate_result) {
                // Then activate the selected template
                $stmt2 = $pdo->prepare("UPDATE setting_branding_templates SET is_active = 1 WHERE area = 'admin' AND template_key = ?");
                $activate_result = $stmt2->execute([$template_key]);
                
                if ($activate_result && $stmt2->rowCount() > 0) {
                    redirectWithMessage("Theme '{$template_key}' has been activated successfully!", '', 'themes-tab');
                } else {
                    redirectWithMessage('', "Theme '{$template_key}' not found in database.", 'themes-tab');
                }
            } else {
                redirectWithMessage('', "Failed to deactivate existing themes.", 'themes-tab');
            }
            
        } catch (Exception $e) {
            redirectWithMessage('', "Database error: " . $e->getMessage(), 'themes-tab');
        }
    } else {
        redirectWithMessage('', "Invalid theme '{$template_key}' selected.", 'themes-tab');
    }
}

// Get current active template and all available templates
$active_template = getActiveTheme('admin');
$all_templates = getAllBrandingTemplates();

// Determine which tab should be active after form submission
$active_tab = 'business-tab'; // default

// Check URL parameter for tab
if (isset($_GET['tab'])) {
    switch ($_GET['tab']) {
        case 'business-info':
            $active_tab = 'business-tab';
            break;
        case 'brand-colors':
            $active_tab = 'colors-tab';
            break;
        case 'visual-themes':
            $active_tab = 'themes-tab';
            break;
        case 'fonts':
            $active_tab = 'fonts-tab';
            break;
        case 'advanced':
            $active_tab = 'advanced-tab';
            break;
    }
}

// Override with form submission if present
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'update_business_info':
            $active_tab = 'business-tab';
            break;
        case 'update_brand_colors':
            $active_tab = 'colors-tab';
            break;
        case 'update_visual_themes':
            $active_tab = 'themes-tab';
            break;
        case 'update_fonts':
            $active_tab = 'fonts-tab';
            break;
        case 'update_advanced':
            $active_tab = 'advanced-tab';
            break;
    }
}

// Load current branding settings from DATABASE ONLY
$brand_colors = getBrandingColors();
$business_identity = getBusinessIdentity();

// Generate CSS for custom fonts on page load
generateCustomFontsCSS();

// Load additional business contact information
try {
    $stmt = $pdo->query("
        SELECT 
            bi.*,
            bc.primary_email,
            bc.primary_phone,
            bc.primary_address,
            bc.street_address,
            bc.city,
            bc.state,
            bc.zipcode,
            bc.country,
            bc.website_url
        FROM setting_business_identity bi 
        LEFT JOIN setting_business_contact bc ON bi.id = bc.business_identity_id 
        WHERE bi.id = 1 
        LIMIT 1
    ");
    $business_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($business_data) {
        $business_identity = array_merge($business_identity, $business_data);
    }
} catch (Exception $e) {
    // Use existing business_identity data as fallback
}

// Debug output (remove in production)
// echo "<!-- DEBUG: Business Identity: " . json_encode($business_identity) . " -->";
// echo "<!-- DEBUG: Brand Colors: " . json_encode($brand_colors) . " -->";

// Handle other form submissions (colors, fonts, logos, etc.)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        global $pdo;
        
        switch ($_POST['action']) {
            case 'update_business_info':
                // Update business identity information
                $stmt = $pdo->prepare("
                    UPDATE setting_business_identity SET 
                        business_name_short = ?,
                        business_name_medium = ?,
                        business_name_long = ?,
                        legal_business_name = ?,
                        business_tagline_short = ?,
                        business_tagline_medium = ?,
                        business_tagline_long = ?,
                        show_full_address = ?
                    WHERE id = 1
                ");
                $stmt->execute([
                    $_POST['business_name_short'] ?? '',
                    $_POST['business_name_medium'] ?? '',
                    $_POST['business_name_long'] ?? '',
                    $_POST['legal_business_name'] ?? '',
                    $_POST['business_tagline_short'] ?? '',
                    $_POST['business_tagline_medium'] ?? '',
                    $_POST['business_tagline_long'] ?? '',
                    isset($_POST['show_full_address']) ? 1 : 0
                ]);
                
                // Update contact information
                $stmt = $pdo->prepare("
                    UPDATE setting_business_contact SET 
                        primary_phone = ?,
                        primary_email = ?,
                        website_url = ?,
                        street_address = ?,
                        city = ?,
                        state = ?,
                        zipcode = ?,
                        country = ?,
                        primary_address = ?
                    WHERE business_identity_id = 1
                ");
                
                // Build combined address for backwards compatibility
                $address_parts = array_filter([
                    $_POST['street_address'] ?? '',
                    trim(($_POST['city'] ?? '') . ', ' . ($_POST['state'] ?? '') . ' ' . ($_POST['zipcode'] ?? ''))
                ]);
                $combined_address = implode('\n', $address_parts);
                
                $stmt->execute([
                    $_POST['business_phone'] ?? '',
                    $_POST['business_email'] ?? '',
                    $_POST['business_website'] ?? '',
                    $_POST['street_address'] ?? '',
                    $_POST['city'] ?? '',
                    strtoupper($_POST['state'] ?? ''),
                    $_POST['zipcode'] ?? '',
                    $_POST['country'] ?? 'United States',
                    $combined_address
                ]);
                
                redirectWithMessage("Business information updated successfully!", '', 'business-tab');
                break;
                
            case 'update_brand_colors':
                // Debug: Log all POST data for colors
                $color_debug = [];
                foreach ($_POST as $key => $value) {
                    if (strpos($key, 'color_') === 0) {
                        $color_debug[$key] = $value;
                    }
                }
                error_log("Color form POST data: " . json_encode($color_debug));
                
                // Update brand colors - using INSERT ON DUPLICATE KEY to handle missing rows
                $stmt = $pdo->prepare("
                    INSERT INTO setting_branding_colors (
                        id, brand_primary_color, brand_secondary_color, brand_accent_color, 
                        brand_tertiary_color, brand_quaternary_color, brand_success_color, brand_warning_color
                    ) VALUES (1, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                        brand_primary_color = VALUES(brand_primary_color),
                        brand_secondary_color = VALUES(brand_secondary_color),
                        brand_accent_color = VALUES(brand_accent_color),
                        brand_tertiary_color = VALUES(brand_tertiary_color),
                        brand_quaternary_color = VALUES(brand_quaternary_color),
                        brand_success_color = VALUES(brand_success_color),
                        brand_warning_color = VALUES(brand_warning_color)
                ");
                
                $colors_to_save = [
                    $_POST['color_primary'] ?? '#6c2eb6',
                    $_POST['color_secondary'] ?? '#bf5512', 
                    $_POST['color_accent'] ?? '#28a745',
                    $_POST['color_tertiary'] ?? '#8B4513',
                    $_POST['color_quaternary'] ?? '#dc3545',
                    $_POST['color_success'] ?? '#28a745',
                    $_POST['color_warning'] ?? '#ffc107'
                ];
                
                // Debug: log what we're about to save
                error_log("About to save colors: " . json_encode(array_combine(
                    ['brand_primary_color', 'brand_secondary_color', 'brand_accent_color', 'brand_tertiary_color', 'brand_quaternary_color', 'brand_success_color', 'brand_warning_color'],
                    $colors_to_save
                )));
                
                $stmt->execute($colors_to_save);
                
                // Debug: verify what was actually saved (make sure we check the right row)
                $verify_stmt = $pdo->query("SELECT brand_primary_color, brand_secondary_color, brand_accent_color, brand_tertiary_color, brand_quaternary_color, brand_success_color, brand_warning_color FROM setting_branding_colors WHERE id = 1 ORDER BY id LIMIT 1");
                $saved_colors = $verify_stmt->fetch(PDO::FETCH_ASSOC);
                error_log("Verified saved colors (ID=1): " . json_encode($saved_colors));
                
                // Force reload of branding colors to clear any caching
                $brand_colors = getBrandingColors();
                
                redirectWithMessage("Brand colors updated successfully!", '', 'colors-tab');
                break;
                
            case 'update_fonts':
                // Update font selections
                $fonts = [
                    'primary_font' => $_POST['primary_font'] ?? 'Arial',
                    'heading_font' => $_POST['heading_font'] ?? 'Arial',
                    'body_font' => $_POST['body_font'] ?? 'Arial'
                ];
                
                foreach ($fonts as $font_type => $font_family) {
                    $stmt = $pdo->prepare("
                        INSERT INTO setting_branding_fonts (font_type, font_family, area) 
                        VALUES (?, ?, 'public') 
                        ON DUPLICATE KEY UPDATE font_family = VALUES(font_family)
                    ");
                    $stmt->execute([$font_type, $font_family]);
                }
                
                redirectWithMessage("Typography settings updated successfully!", '', 'fonts-tab');
                break;
                
            case 'upload_font':
                // Handle font file uploads
                $upload_dir = '../assets/fonts/custom/';
                
                // Create upload directory if it doesn't exist
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $uploaded_count = 0;
                $errors = [];
                
                for ($i = 1; $i <= 5; $i++) {
                    if (isset($_FILES["font_upload_$i"]) && $_FILES["font_upload_$i"]['error'] === UPLOAD_ERR_OK) {
                        $file = $_FILES["font_upload_$i"];
                        $file_name = $file['name'];
                        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                        
                        // Validate file type
                        $allowed_types = ['woff2', 'woff', 'ttf', 'otf'];
                        if (!in_array($file_ext, $allowed_types)) {
                            $errors[] = "Font slot $i: Invalid file type. Only .woff2, .woff, .ttf, .otf files are allowed.";
                            continue;
                        }
                        
                        // Validate file size (2MB max)
                        if ($file['size'] > 2 * 1024 * 1024) {
                            $errors[] = "Font slot $i: File too large. Maximum size is 2MB.";
                            continue;
                        }
                        
                        // Generate safe filename
                        $safe_name = 'custom_font_' . $i . '_' . time() . '.' . $file_ext;
                        $upload_path = $upload_dir . $safe_name;
                        
                        // Store relative path for database (from public_html root)
                        $db_path = 'assets/fonts/custom/' . $safe_name;
                        
                        // Remove old font file if exists
                        $old_font = $business_identity["font_upload_$i"] ?? '';
                        if (!empty($old_font) && file_exists($old_font)) {
                            unlink($old_font);
                        }
                        
                        // Move uploaded file
                        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                            // Update database with relative file path
                            $column = "font_upload_$i";
                            $stmt = $pdo->prepare("
                                INSERT INTO setting_business_identity (id, $column) 
                                VALUES (1, ?)
                                ON DUPLICATE KEY UPDATE $column = ?
                            ");
                            $stmt->execute([$db_path, $db_path]);
                            
                            $uploaded_count++;
                        } else {
                            $errors[] = "Font slot $i: Failed to upload file.";
                        }
                    }
                }
                
                if ($uploaded_count > 0) {
                    // Generate CSS for the new fonts
                    if (generateCustomFontsCSS()) {
                        redirectWithMessage("$uploaded_count font(s) uploaded successfully and CSS updated!", '', 'fonts-tab');
                    } else {
                        redirectWithMessage("$uploaded_count font(s) uploaded successfully! (CSS update failed - check manually)", '', 'fonts-tab');
                    }
                } else if (!empty($errors)) {
                    redirectWithMessage('', implode(' ', $errors), 'fonts-tab');
                } else {
                    redirectWithMessage('', "No fonts were selected for upload.", 'fonts-tab');
                }
                break;
                
            case 'remove_font':
                // Handle font removal
                $font_slot = intval($_POST['font_slot'] ?? 0);
                
                if ($font_slot >= 1 && $font_slot <= 5) {
                    // Get current font path
                    $stmt = $pdo->prepare("SELECT font_upload_$font_slot FROM setting_business_identity WHERE id = 1");
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($result) {
                        $font_path = $result["font_upload_$font_slot"];
                        
                        // Remove file if exists
                        if (!empty($font_path) && file_exists($font_path)) {
                            unlink($font_path);
                        }
                        
                        // Clear database entry
                        $stmt = $pdo->prepare("UPDATE setting_business_identity SET font_upload_$font_slot = '' WHERE id = 1");
                        $stmt->execute();
                        
                        // Regenerate CSS after font removal
                        generateCustomFontsCSS();
                        
                        redirectWithMessage("Font removed successfully!", '', 'fonts-tab');
                    } else {
                        redirectWithMessage('', "Font not found.", 'fonts-tab');
                    }
                } else {
                    redirectWithMessage('', "Invalid font slot.", 'fonts-tab');
                }
                break;
                
            case 'update_footer_branding':
                // Update footer branding settings
                $stmt = $pdo->prepare("
                    UPDATE setting_business_identity SET 
                        footer_logo_enabled = ?,
                        footer_logo_position = ?,
                        footer_business_name_type = ?
                    WHERE id = 1
                ");
                $stmt->execute([
                    isset($_POST['footer_logo_enabled']) ? 1 : 0,
                    $_POST['footer_logo_position'] ?? 'left',
                    $_POST['footer_business_name_type'] ?? 'medium'
                ]);
                
                redirectWithMessage("Footer branding settings updated successfully!", '', 'advanced-tab');
                break;
        }
    } catch (Exception $e) {
        redirectWithMessage('', "Error updating settings: " . $e->getMessage());
    }
}

?>

<?= template_admin_header('Brand Settings', 'settings', 'branding-tabbed') ?>

<!-- Load Active Theme CSS -->
<?php loadActiveThemeCSS('admin'); ?>

<!-- Load Custom Fonts CSS -->
<?php
$custom_fonts_css = __DIR__ . '/../../assets/css/custom-fonts.css';
if (file_exists($custom_fonts_css)) {
    echo '<link rel="stylesheet" href="../../assets/css/custom-fonts.css?v=' . filemtime($custom_fonts_css) . '">';
}
?>

<!-- Success/Error Messages -->
<?php if ($success_message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success_message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        
        <?php if ($_POST['action'] ?? '' === 'update_brand_colors'): ?>
            <!-- Debug info removed for production -->
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ($error_message): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error_message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header py-4 px-4 branding-settings-header">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold">
                <span class="header-icon"><i class="bi bi-palette" aria-hidden="true"></i></span>
                Brand Settings
            </h6>
            <span class="text-white" style="font-size: 0.875rem;">Complete brand management & visual themes</span>
        </div>
    </div>

    <div class="card-body">
        <!-- Tab Navigation -->
        <div class="tab-nav" role="tablist" aria-label="Brand settings tabs">
            <button type="button" class="tab-btn <?= $active_tab === 'business-tab' ? 'active' : '' ?>" role="tab" aria-selected="<?= $active_tab === 'business-tab' ? 'true' : 'false' ?>" aria-controls="business-tab" id="business-tab-btn" onclick="openTab(event,'business-tab')">Business Info</button>
            <button type="button" class="tab-btn <?= $active_tab === 'colors-tab' ? 'active' : '' ?>" role="tab" aria-selected="<?= $active_tab === 'colors-tab' ? 'true' : 'false' ?>" aria-controls="colors-tab" id="colors-tab-btn" onclick="openTab(event,'colors-tab')">Brand Colors</button>
            <button type="button" class="tab-btn <?= $active_tab === 'themes-tab' ? 'active' : '' ?>" role="tab" aria-selected="<?= $active_tab === 'themes-tab' ? 'true' : 'false' ?>" aria-controls="themes-tab" id="themes-tab-btn" onclick="openTab(event,'themes-tab')">Visual Themes</button>
            <button type="button" class="tab-btn <?= $active_tab === 'fonts-tab' ? 'active' : '' ?>" role="tab" aria-selected="<?= $active_tab === 'fonts-tab' ? 'true' : 'false' ?>" aria-controls="fonts-tab" id="fonts-tab-btn" onclick="openTab(event,'fonts-tab')">Typography</button>
            <button type="button" class="tab-btn <?= $active_tab === 'logos-tab' ? 'active' : '' ?>" role="tab" aria-selected="<?= $active_tab === 'logos-tab' ? 'true' : 'false' ?>" aria-controls="logos-tab" id="logos-tab-btn" onclick="openTab(event,'logos-tab')">Logos & Images</button>
            <button type="button" class="tab-btn <?= $active_tab === 'advanced-tab' ? 'active' : '' ?>" role="tab" aria-selected="<?= $active_tab === 'advanced-tab' ? 'true' : 'false' ?>" aria-controls="advanced-tab" id="advanced-tab-btn" onclick="openTab(event,'advanced-tab')">Advanced</button>
        </div>

        <!-- Business Information Tab -->
        <div id="business-tab" class="tab-content <?= $active_tab === 'business-tab' ? 'active' : '' ?>" <?= $active_tab !== 'business-tab' ? 'hidden' : '' ?> role="tabpanel" aria-labelledby="business-tab-btn">
            <h3 class="mb-4">Business Information</h3>
            <p class="text-muted mb-4">Configure your business details with multiple variants for different display contexts. Having short, medium, and long versions allows the system to choose the most appropriate display based on available space.</p>
            
            <!-- Usage Guide -->
            <div class="alert alert-info mb-4">
                <h6><i class="bi bi-info-circle me-2"></i>How These Variants Are Used:</h6>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Business Names:</strong>
                        <ul class="mb-0 mt-1">
                            <li><strong>Short:</strong> Mobile headers, favicons, compact spaces</li>
                            <li><strong>Medium:</strong> Main headers, navigation, general use</li>
                            <li><strong>Long:</strong> Hero sections, about pages, full descriptions</li>
                            <li><strong>Legal:</strong> Terms, privacy policies, formal documents</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <strong>Taglines:</strong>
                        <ul class="mb-0 mt-1">
                            <li><strong>Short:</strong> Buttons, tooltips, limited space</li>
                            <li><strong>Medium:</strong> Subheadings, hero sections, cards</li>
                            <li><strong>Long:</strong> About sections, detailed descriptions</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <form method="POST" id="businessInfoForm">
                <input type="hidden" name="action" value="update_business_info">
                
                <!-- Business Names Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-building me-2"></i>Business Names</h5>
                        <small class="text-muted">Different name variations for different contexts</small>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="business_name_short" class="form-label fw-bold">Short Business Name</label>
                                <input type="text" name="business_name_short" id="business_name_short" class="form-control" value="<?= htmlspecialchars($business_identity['business_name_short'] ?? '') ?>" placeholder="e.g., GWS">
                                <div class="form-text">Brief name for headers, logos, mobile displays</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="business_name_medium" class="form-label fw-bold">Medium Business Name</label>
                                <input type="text" name="business_name_medium" id="business_name_medium" class="form-control" value="<?= htmlspecialchars($business_identity['business_name_medium'] ?? '') ?>" placeholder="e.g., GWS Universal">
                                <div class="form-text">Standard name for most pages and content</div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="business_name_long" class="form-label fw-bold">Long Business Name</label>
                                <input type="text" name="business_name_long" id="business_name_long" class="form-control" value="<?= htmlspecialchars($business_identity['business_name_long'] ?? '') ?>" placeholder="e.g., GWS Universal Hybrid Application Platform">
                                <div class="form-text">Full descriptive name for formal contexts</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="legal_business_name" class="form-label fw-bold">Legal Business Name</label>
                                <input type="text" name="legal_business_name" id="legal_business_name" class="form-control" value="<?= htmlspecialchars($business_identity['legal_business_name'] ?? '') ?>" placeholder="e.g., GlitchWizard Solutions LLC">
                                <div class="form-text">Official registered business name for legal documents</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Business Taglines Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-chat-quote me-2"></i>Business Taglines</h5>
                        <small class="text-muted">Different tagline lengths for various display contexts</small>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="business_tagline_short" class="form-label fw-bold">Short Tagline</label>
                                <input type="text" name="business_tagline_short" id="business_tagline_short" class="form-control" value="<?= htmlspecialchars($business_identity['business_tagline_short'] ?? '') ?>" placeholder="e.g., Innovation Simplified">
                                <div class="form-text">Brief tagline for buttons, small spaces (max ~100 chars)</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="business_tagline_medium" class="form-label fw-bold">Medium Tagline</label>
                                <input type="text" name="business_tagline_medium" id="business_tagline_medium" class="form-control" value="<?= htmlspecialchars($business_identity['business_tagline_medium'] ?? '') ?>" placeholder="e.g., Your complete business solution platform">
                                <div class="form-text">Standard tagline for headers, hero sections (max ~200 chars)</div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 mb-3">
                                <label for="business_tagline_long" class="form-label fw-bold">Long Tagline</label>
                                <textarea name="business_tagline_long" id="business_tagline_long" class="form-control" rows="3" placeholder="e.g., Comprehensive hybrid application platform designed to streamline your business operations and enhance productivity with cutting-edge technology solutions"><?= htmlspecialchars($business_identity['business_tagline_long'] ?? '') ?></textarea>
                                <div class="form-text">Detailed tagline for about pages, marketing descriptions, and comprehensive explanations</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information Section -->

                <!-- Contact Information Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-telephone me-2"></i>Contact Information</h5>
                        <small class="text-muted">Business contact details for website and communications</small>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4 mb-3">
                                <label for="business_phone" class="form-label fw-bold">Phone Number</label>
                                <input type="tel" name="business_phone" id="business_phone" class="form-control" value="<?= htmlspecialchars($business_identity['primary_phone'] ?? '') ?>" placeholder="(555) 123-4567">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="business_email" class="form-label fw-bold">Email Address</label>
                                <input type="email" name="business_email" id="business_email" class="form-control" value="<?= htmlspecialchars($business_identity['primary_email'] ?? '') ?>" placeholder="info@yourbusiness.com">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="business_website" class="form-label fw-bold">Website URL</label>
                                <input type="url" name="business_website" id="business_website" class="form-control" value="<?= htmlspecialchars($business_identity['website_url'] ?? '') ?>" placeholder="https://yourbusiness.com">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold"><i class="bi bi-geo-alt me-2"></i>Business Address</label>
                                <div class="form-text mb-2">Enter your business location details (service businesses can choose which fields to display publicly)</div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12 mb-3">
                                <label for="street_address" class="form-label fw-bold">Street Address</label>
                                <input type="text" name="street_address" id="street_address" class="form-control" value="<?= htmlspecialchars($business_identity['street_address'] ?? '') ?>" placeholder="123 Main Street">
                                <div class="form-text">Street number and name</div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label fw-bold">City</label>
                                <input type="text" name="city" id="city" class="form-control" value="<?= htmlspecialchars($business_identity['city'] ?? '') ?>" placeholder="Your City">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="state" class="form-label fw-bold">State</label>
                                <input type="text" name="state" id="state" class="form-control" value="<?= htmlspecialchars($business_identity['state'] ?? '') ?>" placeholder="ST" maxlength="2" style="text-transform: uppercase;">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="zipcode" class="form-label fw-bold">ZIP Code</label>
                                <input type="text" name="zipcode" id="zipcode" class="form-control" value="<?= htmlspecialchars($business_identity['zipcode'] ?? '') ?>" placeholder="12345" maxlength="10">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="country" class="form-label fw-bold">Country</label>
                                <input type="text" name="country" id="country" class="form-control" value="<?= htmlspecialchars($business_identity['country'] ?? 'United States') ?>" placeholder="United States">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Address Display Options</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="show_full_address" id="show_full_address" <?= ($business_identity['show_full_address'] ?? '1') == '1' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="show_full_address">
                                        Show full street address publicly
                                    </label>
                                </div>
                                <div class="form-text">Uncheck if you're a service business and prefer to show only city, state, zip</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-start align-items-center gap-3 mt-4 pt-3" style="border-top: 1px solid #dee2e6;">
                    <button type="button" class="btn btn-outline-secondary" onclick="resetBusinessForm()">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                    </button>
                    <button type="submit" class="btn btn-success" style="background-color: #28a745 !important; border-color: #28a745 !important; color: white !important;">
                        <i class="bi bi-save me-1"></i>Save Business Information
                    </button>
                    <span class="business-save-status small text-muted ms-2" aria-live="polite"></span>
                </div>
            </form>
        </div>

        <!-- Brand Colors Tab -->
        <div id="colors-tab" class="tab-content <?= $active_tab === 'colors-tab' ? 'active' : '' ?>" <?= $active_tab !== 'colors-tab' ? 'hidden' : '' ?> role="tabpanel" aria-labelledby="colors-tab-btn">
            <h3 class="mb-4">Brand Colors</h3>
            <p class="text-muted mb-4">Define your brand colors that will be used throughout your website. Changes are applied immediately across all areas.</p>
            
            <form method="POST" id="colorsForm">
                <input type="hidden" name="action" value="update_brand_colors">
                
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="color_primary" class="form-label fw-bold">Primary Color</label>
                        <div class="input-group">
                            <input type="color" name="color_primary" id="color_primary" class="form-control form-control-color" value="<?= htmlspecialchars($brand_colors['primary'] ?? '#6c2eb6') ?>" onchange="updateColorText('primary')" oninput="updateColorText('primary')">
                            <input type="text" id="color_primary_text" class="form-control" value="<?= htmlspecialchars($brand_colors['primary'] ?? '#6c2eb6') ?>" placeholder="#FFFFFF" pattern="^#[A-Fa-f0-9]{6}$" onchange="updateColorPicker('primary')" onpaste="setTimeout(() => updateColorPicker('primary'), 10)">
                        </div>
                        <div class="form-text">Main brand color used for headers, buttons, and accents (you can paste hex codes)</div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="color_secondary" class="form-label fw-bold">Secondary Color</label>
                        <div class="input-group">
                            <input type="color" name="color_secondary" id="color_secondary" class="form-control form-control-color" value="<?= htmlspecialchars($brand_colors['secondary'] ?? '#bf5512') ?>" onchange="updateColorText('secondary')" oninput="updateColorText('secondary')">
                            <input type="text" id="color_secondary_text" class="form-control" value="<?= htmlspecialchars($brand_colors['secondary'] ?? '#bf5512') ?>" placeholder="#FFFFFF" pattern="^#[A-Fa-f0-9]{6}$" onchange="updateColorPicker('secondary')" onpaste="setTimeout(() => updateColorPicker('secondary'), 10)">
                        </div>
                        <div class="form-text">Secondary brand color for complementary elements (you can paste hex codes)</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="color_accent" class="form-label fw-bold">Accent Color</label>
                        <div class="input-group">
                            <input type="color" name="color_accent" id="color_accent" class="form-control form-control-color" value="<?= htmlspecialchars($brand_colors['accent'] ?? '#28a745') ?>" onchange="updateColorText('accent')" oninput="updateColorText('accent')">
                            <input type="text" id="color_accent_text" class="form-control" value="<?= htmlspecialchars($brand_colors['accent'] ?? '#28a745') ?>" placeholder="#FFFFFF" pattern="^#[A-Fa-f0-9]{6}$" onchange="updateColorPicker('accent')" onpaste="setTimeout(() => updateColorPicker('accent'), 10)">
                        </div>
                        <div class="form-text">Accent color for highlights and call-to-action elements (you can paste hex codes)</div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="color_tertiary" class="form-label fw-bold">Tertiary Color</label>
                        <div class="input-group">
                            <input type="color" name="color_tertiary" id="color_tertiary" class="form-control form-control-color" value="<?= htmlspecialchars($brand_colors['tertiary'] ?? '#8B4513') ?>" onchange="updateColorText('tertiary')" oninput="updateColorText('tertiary')">
                            <input type="text" id="color_tertiary_text" class="form-control" value="<?= htmlspecialchars($brand_colors['tertiary'] ?? '#8B4513') ?>" placeholder="#FFFFFF" pattern="^#[A-Fa-f0-9]{6}$" onchange="updateColorPicker('tertiary')" onpaste="setTimeout(() => updateColorPicker('tertiary'), 10)">
                        </div>
                        <div class="form-text">Third brand color for additional variety (you can paste hex codes)</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="color_quaternary" class="form-label fw-bold">Quaternary Color</label>
                        <div class="input-group">
                            <input type="color" name="color_quaternary" id="color_quaternary" class="form-control form-control-color" value="<?= htmlspecialchars($brand_colors['quaternary'] ?? '#dc3545') ?>" onchange="updateColorText('quaternary')" oninput="updateColorText('quaternary')">
                            <input type="text" id="color_quaternary_text" class="form-control" value="<?= htmlspecialchars($brand_colors['quaternary'] ?? '#dc3545') ?>" placeholder="#FFFFFF" pattern="^#[A-Fa-f0-9]{6}$" onchange="updateColorPicker('quaternary')" onpaste="setTimeout(() => updateColorPicker('quaternary'), 10)">
                        </div>
                        <div class="form-text">Fourth brand color for maximum design flexibility (you can paste hex codes)</div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="color_success" class="form-label fw-bold">Success Color</label>
                        <div class="input-group">
                            <input type="color" name="color_success" id="color_success" class="form-control form-control-color" value="<?= htmlspecialchars($brand_colors['success'] ?? '#28a745') ?>" onchange="updateColorText('success')" oninput="updateColorText('success')">
                            <input type="text" id="color_success_text" class="form-control" value="<?= htmlspecialchars($brand_colors['success'] ?? '#28a745') ?>" placeholder="#FFFFFF" pattern="^#[A-Fa-f0-9]{6}$" onchange="updateColorPicker('success')" onpaste="setTimeout(() => updateColorPicker('success'), 10)">
                        </div>
                        <div class="form-text">Color for success messages and positive actions (you can paste hex codes)</div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="color_warning" class="form-label fw-bold">Warning Color</label>
                        <div class="input-group">
                            <input type="color" name="color_warning" id="color_warning" class="form-control form-control-color" value="<?= htmlspecialchars($brand_colors['warning'] ?? '#ffc107') ?>" onchange="updateColorText('warning')" oninput="updateColorText('warning')">
                            <input type="text" id="color_warning_text" class="form-control" value="<?= htmlspecialchars($brand_colors['warning'] ?? '#ffc107') ?>" placeholder="#FFFFFF" pattern="^#[A-Fa-f0-9]{6}$" onchange="updateColorPicker('warning')" onpaste="setTimeout(() => updateColorPicker('warning'), 10)">
                        </div>
                        <div class="form-text">Color for warning messages and caution elements (you can paste hex codes)</div>
                    </div>
                </div>

                <div class="d-flex justify-content-start align-items-center gap-3 mt-4 pt-3" style="border-top: 1px solid #dee2e6;">
                    <button type="button" class="btn btn-outline-secondary" onclick="resetColorsForm()">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                    </button>
                    <button type="submit" class="btn btn-success" style="background-color: #28a745 !important; border-color: #28a745 !important; color: white !important; display: inline-block !important;">
                        <i class="bi bi-save me-1"></i>Save Brand Colors
                    </button>
                    <span class="colors-save-status small text-muted ms-2" aria-live="polite"></span>
                </div>
            </form>
        </div>

        <!-- Visual Themes Tab -->
        <div id="themes-tab" class="tab-content <?= $active_tab === 'themes-tab' ? 'active' : '' ?>" <?= $active_tab !== 'themes-tab' ? 'hidden' : '' ?> role="tabpanel" aria-labelledby="themes-tab-btn">
            <h3 class="mb-4">Visual Themes</h3>
            <p class="text-muted mb-4">Choose a visual theme that affects the styling across all three areas: <strong>Public Website</strong>, <strong>Admin Panel</strong>, and <strong>Client Portal</strong>. The page will refresh to show the new admin theme immediately.</p>
            
            <div class="alert alert-info mb-4">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Multi-Area Impact:</strong> Selecting a theme applies consistent styling to your public website, admin panel, and client portal simultaneously for a unified brand experience.
            </div>

            <?php
            // Define theme details for presentation
            $theme_details = [
                'default' => [
                    'name' => 'Classic Professional',
                    'description' => 'Clean, traditional layout perfect for professional businesses',
                    'icon' => 'bi-buildings',
                    'color' => '#007bff'
                ],
                'subtle' => [
                    'name' => 'Subtle Elegance',
                    'description' => 'Minimal, understated design for sophisticated brands',
                    'icon' => 'bi-gem',
                    'color' => '#6c757d'
                ],
                'bold' => [
                    'name' => 'Bold Impact',
                    'description' => 'Strong, vibrant design for maximum visual impact',
                    'icon' => 'bi-lightning-charge',
                    'color' => '#dc3545'
                ],
                'casual' => [
                    'name' => 'Friendly Casual',
                    'description' => 'Approachable, relaxed design for friendly businesses',
                    'icon' => 'bi-heart',
                    'color' => '#28a745'
                ],
                'high-contrast' => [
                    'name' => 'High Contrast',
                    'description' => 'Accessible design with strong contrast ratios',
                    'icon' => 'bi-universal-access',
                    'color' => '#000000'
                ]
            ];

            $available_themes = array_keys($theme_details);
            ?>

            <div class="row">
                <?php foreach ($available_themes as $theme_key): 
                    $theme_info = $theme_details[$theme_key];
                    $is_active = ($active_template === $theme_key);
                ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card theme-card <?= $is_active ? 'border-primary active-theme' : '' ?>" style="<?= $is_active ? 'border-color: ' . $theme_info['color'] . ' !important;' : '' ?>">
                            <div class="card-header d-flex align-items-center justify-content-between" style="background-color: <?= $theme_info['color'] ?>; color: white;">
                                <div class="d-flex align-items-center">
                                    <i class="<?= $theme_info['icon'] ?> me-2"></i>
                                    <strong><?= htmlspecialchars($theme_info['name']) ?></strong>
                                </div>
                                <?php if ($is_active): ?>
                                    <span class="badge bg-light text-dark">
                                        <i class="bi bi-check-circle me-1"></i>Active
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <p class="card-text text-muted mb-3"><?= htmlspecialchars($theme_info['description']) ?></p>
                                
                                <div class="theme-preview mb-3">
                                    <div class="d-flex gap-2 mb-2">
                                        <div class="color-swatch" style="background-color: <?= $theme_info['color'] ?>;" title="Primary"></div>
                                        <div class="color-swatch" style="background-color: var(--brand-secondary, #bf5512);" title="Secondary"></div>
                                        <div class="color-swatch" style="background-color: #ffffff; border: 1px solid #ddd;" title="Background"></div>
                                    </div>
                                    <small class="text-muted">Uses your brand colors with theme-specific styling</small>
                                </div>

                                <?php if (!$is_active): ?>
                                    <form method="POST" class="d-inline-block w-100">
                                        <input type="hidden" name="action" value="select_theme">
                                        <input type="hidden" name="template_key" value="<?= htmlspecialchars($theme_key) ?>">
                                        <button type="submit" class="btn btn-outline-primary w-100">
                                            <i class="bi bi-palette me-1"></i>Activate Theme
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button type="button" class="btn btn-primary w-100" disabled>
                                        <i class="bi bi-check-circle me-1"></i>Currently Active
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="alert alert-warning mt-4">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Note:</strong> Theme changes are applied instantly. Visit your public website to see the theme in action across all areas.
            </div>
        </div>

        <!-- Fonts & Typography Tab -->
        <div id="fonts-tab" class="tab-content" hidden role="tabpanel" aria-labelledby="fonts-tab-btn">
            <h3 class="mb-4">Typography Settings</h3>
            <p class="text-muted mb-4">Configure fonts and typography settings for your website.</p>
            
            <!-- Font Upload Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Custom Font Uploads</h5>
                    <p class="mb-0 text-muted small">Upload up to 5 custom font files (.woff2, .woff, .ttf, .otf). Ensure fonts are properly licensed for web use.</p>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" id="fontUploadForm">
                        <input type="hidden" name="action" value="upload_font">
                        
                        <?php
                        // Get current font uploads
                        $font_uploads = [];
                        for ($i = 1; $i <= 5; $i++) {
                            $font_file = $business_identity["font_upload_$i"] ?? '';
                            $font_uploads[$i] = $font_file;
                        }
                        ?>
                        
                        <?php 
                        // Define descriptive font slot purposes
                        $font_slot_purposes = [
                            1 => [
                                'name' => 'Regular Body Text',
                                'description' => 'Main font for paragraphs, articles, and general content',
                                'icon' => 'bi-text-paragraph',
                                'examples' => 'Articles, descriptions, body content'
                            ],
                            2 => [
                                'name' => 'Headings & Titles',
                                'description' => 'Bold, attention-grabbing font for headings and titles',
                                'icon' => 'bi-type-h1',
                                'examples' => 'Page titles, section headers, hero text'
                            ],
                            3 => [
                                'name' => 'Formal/Professional',
                                'description' => 'Professional font for business documents and formal content',
                                'icon' => 'bi-briefcase',
                                'examples' => 'Legal text, certificates, professional documents'
                            ],
                            4 => [
                                'name' => 'Decorative/Fancy',
                                'description' => 'Stylized font for special occasions and decorative elements',
                                'icon' => 'bi-stars',
                                'examples' => 'Special announcements, invitations, decorative headers'
                            ],
                            5 => [
                                'name' => 'Italic/Emphasis',
                                'description' => 'Italic or script font for quotes, captions, and emphasis',
                                'icon' => 'bi-type-italic',
                                'examples' => 'Quotes, captions, testimonials, emphasis text'
                            ]
                        ];
                        ?>
                        
                        <?php for ($i = 1; $i <= 5; $i++): 
                            $purpose = $font_slot_purposes[$i];
                        ?>
                        <div class="row mb-4 p-3 border rounded bg-light">
                            <div class="col-12 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="<?php echo $purpose['icon']; ?> me-2 text-primary fs-5"></i>
                                    <label for="font_upload_<?php echo $i; ?>" class="form-label fw-bold mb-0 text-primary">
                                        <?php echo htmlspecialchars($purpose['name']); ?>
                                    </label>
                                </div>
                                <p class="text-muted small mb-2 ms-4"><?php echo htmlspecialchars($purpose['description']); ?></p>
                                <p class="text-info small mb-2 ms-4"><strong>Best for:</strong> <?php echo htmlspecialchars($purpose['examples']); ?></p>
                            </div>
                            <div class="col-md-8">
                                <input type="file" name="font_upload_<?php echo $i; ?>" id="font_upload_<?php echo $i; ?>" 
                                       class="form-control" accept=".woff2,.woff,.ttf,.otf">
                                <div class="form-text">Accepted formats: .woff2, .woff, .ttf, .otf (Max: 2MB)</div>
                            </div>
                            <div class="col-md-4">
                                <?php if (!empty($font_uploads[$i])): ?>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-success me-2">
                                            <i class="bi bi-check-circle me-1"></i>Uploaded
                                        </span>
                                    </div>
                                    <div class="mt-1">
                                        <small class="text-muted d-block">Current: <?php echo basename($font_uploads[$i]); ?></small>
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-1" 
                                                onclick="removeFont(<?php echo $i; ?>)">
                                            <i class="bi bi-trash me-1"></i>Remove
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small">No font uploaded</span>
                                    <div class="mt-1">
                                        <small class="text-success"><i class="bi bi-arrow-left me-1"></i>Upload your <?php echo strtolower($purpose['name']); ?> font here</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endfor; ?>
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-upload me-1"></i>Upload Fonts
                                </button>
                                <span class="font-upload-status small text-muted ms-2" aria-live="polite"></span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Font Application Guide -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">How Your Uploaded Fonts Will Be Used</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary">Font Usage by Category</h6>
                            <div class="small">
                                <div class="mb-2">
                                    <i class="bi bi-text-paragraph text-primary me-2"></i>
                                    <strong>Regular Body Text:</strong> Main content, paragraphs, article text
                                </div>
                                <div class="mb-2">
                                    <i class="bi bi-type-h1 text-primary me-2"></i>
                                    <strong>Headings & Titles:</strong> Page titles, section headers, hero text
                                </div>
                                <div class="mb-2">
                                    <i class="bi bi-briefcase text-primary me-2"></i>
                                    <strong>Formal/Professional:</strong> Legal documents, certificates, business content
                                </div>
                                <div class="mb-2">
                                    <i class="bi bi-stars text-primary me-2"></i>
                                    <strong>Decorative/Fancy:</strong> Special announcements, invitations, callouts
                                </div>
                                <div class="mb-2">
                                    <i class="bi bi-type-italic text-primary me-2"></i>
                                    <strong>Italic/Emphasis:</strong> Quotes, captions, testimonials, emphasis
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-success">Upload Recommendations</h6>
                            <div class="small">
                                <div class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    <strong>Your Regular Font:</strong> Upload to "Regular Body Text"
                                </div>
                                <div class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    <strong>Your Formal Font:</strong> Upload to "Formal/Professional"
                                </div>
                                <div class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    <strong>Your Fancy Font:</strong> Upload to "Decorative/Fancy"
                                </div>
                                <div class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    <strong>Your Italic Font:</strong> Upload to "Italic/Emphasis"
                                </div>
                                <div class="mt-3 p-2 bg-light rounded">
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle me-1"></i>
                                        You can leave "Headings & Titles" empty if you want to use one of your other fonts for headers.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Font Selection Form -->
            <form method="POST" id="fontsForm">
                <input type="hidden" name="action" value="update_fonts">
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label for="primary_font" class="form-label fw-bold">Primary Font</label>
                        <select name="primary_font" id="primary_font" class="form-select">
                            <?php
                            $available_fonts = getAvailableFonts();
                            $current_primary = $business_identity['primary_font'] ?? 'Arial';
                            foreach ($available_fonts as $font): ?>
                                <option value="<?= htmlspecialchars($font['family']) ?>" <?= $font['family'] === $current_primary ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($font['family']) ?> (<?= htmlspecialchars($font['category']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Default font used across the entire website unless overridden</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="heading_font" class="form-label fw-bold">Heading Font</label>
                        <select name="heading_font" id="heading_font" class="form-select">
                            <?php
                            $current_heading = $business_identity['heading_font'] ?? 'Arial';
                            foreach ($available_fonts as $font): ?>
                                <option value="<?= htmlspecialchars($font['family']) ?>" <?= $font['family'] === $current_heading ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($font['family']) ?> (<?= htmlspecialchars($font['category']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Font specifically for H1, H2, H3 headings and page titles</div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label for="body_font" class="form-label fw-bold">Body Font</label>
                        <select name="body_font" id="body_font" class="form-select">
                            <?php
                            $current_body = $business_identity['body_font'] ?? 'Arial';
                            foreach ($available_fonts as $font): ?>
                                <option value="<?= htmlspecialchars($font['family']) ?>" <?= $font['family'] === $current_body ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($font['family']) ?> (<?= htmlspecialchars($font['category']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Font specifically for article text, descriptions, and long-form content</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Font Preview</label>
                        <div class="font-preview-area p-3 border rounded">
                            <h4 style="font-family: var(--heading-font, Arial);">Heading Sample</h4>
                            <p style="font-family: var(--body-font, Arial);">This is a sample of body text using your selected font family. You can see how it will appear on your website.</p>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-start align-items-center gap-3 mt-4 pt-3" style="border-top: 1px solid #dee2e6;">
                    <button type="button" class="btn btn-outline-secondary" onclick="resetFontsForm()">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save me-1"></i>Save Typography Settings
                    </button>
                    <span class="fonts-save-status small text-muted ms-2" aria-live="polite"></span>
                </div>
            </form>
        </div>

        <!-- Logos & Images Tab -->
        <div id="logos-tab" class="tab-content" hidden role="tabpanel" aria-labelledby="logos-tab-btn">
            <h3 class="mb-4">Logos & Images</h3>
            <p class="text-muted mb-4">Upload and manage your brand logos, favicons, and social media images.</p>
            
            <div class="alert alert-info mb-4">
                <i class="bi bi-info-circle me-2"></i>Coming soon: Logo upload system, favicon management, and social media image optimization.
            </div>
        </div>

        <!-- Advanced Settings Tab -->
        <div id="advanced-tab" class="tab-content" hidden role="tabpanel" aria-labelledby="advanced-tab-btn">
            <h3 class="mb-4">Advanced Settings</h3>
            <p class="text-muted mb-4">Advanced branding options and footer settings.</p>
            
            <form method="POST" id="advancedForm">
                <input type="hidden" name="action" value="update_footer_branding">
                
                <!-- Footer Branding Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-layout-text-window-reverse me-2"></i>Footer Branding</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="footer_business_name_type" class="form-label fw-bold">Business Name Display</label>
                                <select name="footer_business_name_type" id="footer_business_name_type" class="form-select">
                                    <?php $current_name_type = $business_identity['footer_business_name_type'] ?? 'medium'; ?>
                                    <option value="short" <?= $current_name_type === 'short' ? 'selected' : '' ?>>Short Name</option>
                                    <option value="medium" <?= $current_name_type === 'medium' ? 'selected' : '' ?>>Medium Name</option>
                                    <option value="long" <?= $current_name_type === 'long' ? 'selected' : '' ?>>Long Name</option>
                                </select>
                                <div class="form-text">Choose how your business name appears in the footer</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="footer_logo_position" class="form-label fw-bold">Logo Position</label>
                                <select name="footer_logo_position" id="footer_logo_position" class="form-select">
                                    <?php $current_position = $business_identity['footer_logo_position'] ?? 'left'; ?>
                                    <option value="left" <?= $current_position === 'left' ? 'selected' : '' ?>>Left of Business Name</option>
                                    <option value="top" <?= $current_position === 'top' ? 'selected' : '' ?>>Above Business Name</option>
                                </select>
                                <div class="form-text">Where the logo appears relative to the business name</div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="footer_logo_enabled" id="footer_logo_enabled" value="1" <?= ($business_identity['footer_logo_enabled'] ?? false) ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-bold" for="footer_logo_enabled">
                                        Enable Footer Logo
                                    </label>
                                    <div class="form-text">Show your logo in the footer area</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Note:</strong> Logo files are managed in the "Logos & Images" tab. You can upload your footer logo there and it will automatically appear when enabled here.
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-start align-items-center gap-3 mt-4 pt-3" style="border-top: 1px solid #dee2e6;">
                    <button type="button" class="btn btn-outline-secondary" onclick="resetAdvancedForm()">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save me-1"></i>Save Advanced Settings
                    </button>
                    <span class="advanced-save-status small text-muted ms-2" aria-live="polite"></span>
                </div>
            </form>
        </div>

    </div>
</div>

<style>
/* Branding Card Header Styling */
.branding-settings-header {
    background: var(--brand-primary, #6c2eb6) !important;
    color: white !important;
    border-bottom: 1px solid var(--brand-primary, #6c2eb6) !important;
}

.branding-settings-header h6 {
    color: white !important;
    margin: 0 !important;
    font-weight: 600 !important;
    font-size: 1rem !important;
}

.header-icon {
    margin-right: 8px;
    opacity: 0.9;
}

.header-icon i {
    font-size: 1.1rem;
}

/* Tab Navigation */
.tab-nav {
    display: flex;
    border-bottom: 2px solid #dee2e6;
    margin-bottom: 0;
    position: relative;
    background-color: transparent;
    padding: 1rem 0 0 0;
    flex-wrap: wrap;
    gap: 4px;
}

.tab-btn {
    background: var(--brand-primary, #6c2eb6) !important;
    border: 2px solid var(--brand-primary, #6c2eb6) !important;
    border-bottom: 2px solid #dee2e6;
    padding: 12px 20px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    color: white !important;
    transition: all .3s ease;
    border-radius: 8px 8px 0 0;
    margin-right: 4px;
    position: relative;
    outline: none;
    text-decoration: none;
}

.tab-btn:hover {
    background: var(--brand-primary, #6c2eb6) !important;
    border-color: var(--brand-primary, #6c2eb6) !important;
    opacity: 0.9;
    color: white !important;
}

.tab-btn.active,
.tab-btn[aria-selected="true"] {
    color: #495057 !important;
    background: #fff !important;
    border-color: #dee2e6 #dee2e6 transparent !important;
    font-weight: 600;
    z-index: 2;
    border-bottom: 2px solid #fff !important;
    margin-bottom: -2px;
}

.tab-content {
    padding: 30px;
    background: #fff;
    border: 2px solid #dee2e6;
    border-top: none;
    border-radius: 0 8px 8px 8px;
    margin-top: 0;
    margin-left: 0;
}

.tab-content[hidden] {
    display: none !important;
}

.tab-content.active {
    display: block !important;
}

/* Theme Cards */
.theme-card {
    transition: all 0.3s ease;
    height: 100%;
}

.theme-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.theme-card.active-theme {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.color-swatch {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Form Controls */
.form-control-color {
    width: 60px;
    padding: 0.375rem 0.5rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .tab-nav {
        flex-direction: column;
        gap: 2px;
    }
    
    .tab-btn {
        margin-right: 0;
        border-radius: 8px;
        margin-bottom: 2px;
    }
    
    .tab-btn.active,
    .tab-btn[aria-selected="true"] {
        border-radius: 8px;
        margin-bottom: 2px;
    }
    
    .tab-content {
        border-radius: 8px;
        margin-top: 10px;
    }
}
</style>

<script>
// Tab functionality
function openTab(evt, tabName) {
    if (evt) {
        evt.preventDefault();
    }
    
    // Hide all tab contents
    var tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(function(content) {
        content.classList.remove('active');
        content.setAttribute('hidden', 'hidden');
    });
    
    // Remove active class from all tab buttons
    var tabButtons = document.querySelectorAll('.tab-btn');
    tabButtons.forEach(function(button) {
        button.classList.remove('active');
        button.setAttribute('aria-selected', 'false');
    });
    
    // Show the selected tab content
    var targetTab = document.getElementById(tabName);
    if (targetTab) {
        targetTab.classList.add('active');
        targetTab.removeAttribute('hidden');
    }
    
    // Set the clicked button as active
    if (evt && evt.currentTarget) {
        evt.currentTarget.classList.add('active');
        evt.currentTarget.setAttribute('aria-selected', 'true');
    }
    
    // Update URL hash
    if (history.replaceState && targetTab) {
        history.replaceState(null, '', '#' + tabName.replace(/-tab$/, ''));
    }
}

// Initialize tabs on page load
document.addEventListener('DOMContentLoaded', function() {
    // Attach click handlers to tab buttons
    document.querySelectorAll('.tab-nav .tab-btn').forEach(function(btn) {
        if (btn.dataset.bound) return;
        btn.dataset.bound = '1';
        
        btn.addEventListener('click', function(e) {
            var tabId = this.getAttribute('aria-controls') || '';
            openTab(e, tabId.replace(/^(#?)/, ''));
        });
        
        btn.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                var tabId = this.getAttribute('aria-controls') || '';
                openTab(e, tabId.replace(/^(#?)/, ''));
            }
        });
    });
    
    // Handle URL hash on page load
    var hash = location.hash.replace('#', '');
    if (hash && document.getElementById(hash + '-tab')) {
        openTab(null, hash + '-tab');
    }
    
    // Color picker updates
    document.querySelectorAll('input[type="color"]').forEach(function(colorInput) {
        colorInput.addEventListener('change', function() {
            var textInput = this.parentNode.querySelector('input[type="text"]');
            if (textInput) {
                textInput.value = this.value;
            }
        });
    });
});

// Form reset functions
function resetBusinessForm() {
    if (confirm('Reset all business information to original values?')) {
        document.getElementById('businessInfoForm').reset();
    }
}

function resetColorsForm() {
    if (confirm('Reset all colors to original values?')) {
        document.getElementById('colorsForm').reset();
        // Update color picker displays
        document.querySelectorAll('input[type="color"]').forEach(function(colorInput) {
            var textInput = colorInput.parentNode.querySelector('input[type="text"]');
            if (textInput) {
                textInput.value = colorInput.value;
            }
        });
    }
}

function resetFontsForm() {
    if (confirm('Reset all typography settings to original values?')) {
        document.getElementById('fontsForm').reset();
    }
}

function resetAdvancedForm() {
    if (confirm('Reset all advanced settings to original values?')) {
        document.getElementById('advancedForm').reset();
    }
}

// Auto-dismiss success messages
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert-success');
    alerts.forEach(alert => {
        if (alert.querySelector('.btn-close')) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    });
}, 5000);

// Add form submission feedback
document.querySelectorAll('form').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        const statusSpan = this.querySelector('[class$="-save-status"]');
        
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Saving...';
        }
        
        if (statusSpan) {
            statusSpan.innerHTML = 'Saving...';
            statusSpan.className = statusSpan.className.replace('text-muted', 'text-info');
        }
    });
});

// Font preview updates
document.querySelectorAll('select[name$="_font"]').forEach(function(fontSelect) {
    fontSelect.addEventListener('change', function() {
        const previewArea = document.querySelector('.font-preview-area');
        if (previewArea) {
            const selectedFont = this.value;
            if (this.name === 'heading_font') {
                previewArea.querySelector('h4').style.fontFamily = selectedFont;
            } else if (this.name === 'body_font') {
                previewArea.querySelector('p').style.fontFamily = selectedFont;
            }
        }
    });
});

// Color picker and text input synchronization functions
function updateColorText(colorName) {
    const colorPicker = document.getElementById('color_' + colorName);
    const textInput = document.getElementById('color_' + colorName + '_text');
    
    if (colorPicker && textInput) {
        // Update text input with the color picker value
        textInput.value = colorPicker.value.toUpperCase();
        
        // Remove any error styling since we have a valid color
        textInput.style.borderColor = '';
        textInput.style.backgroundColor = '';
        
        // Debug logging (remove in production)
        console.log('Updated ' + colorName + ' text to: ' + colorPicker.value.toUpperCase());
    } else {
        console.error('Could not find elements for color: ' + colorName);
    }
}

function updateColorPicker(colorName) {
    const colorPicker = document.getElementById('color_' + colorName);
    const textInput = document.getElementById('color_' + colorName + '_text');
    if (colorPicker && textInput) {
        let hexValue = textInput.value.trim();
        
        // Add # if missing
        if (hexValue && !hexValue.startsWith('#')) {
            hexValue = '#' + hexValue;
        }
        
        // Validate hex format
        if (hexValue.match(/^#[A-Fa-f0-9]{6}$/)) {
            colorPicker.value = hexValue.toLowerCase();
            textInput.value = hexValue.toUpperCase();
            // Remove any error styling
            textInput.style.borderColor = '';
            textInput.style.backgroundColor = '';
        } else if (hexValue.length > 1) {
            // Show error for invalid format
            textInput.style.borderColor = '#dc3545';
            textInput.style.backgroundColor = '#fff5f5';
        }
    }
}

// Initialize color text inputs when page loads
document.addEventListener('DOMContentLoaded', function() {
    const colorNames = ['primary', 'secondary', 'accent', 'tertiary', 'quaternary', 'success', 'warning'];
    colorNames.forEach(function(colorName) {
        updateColorText(colorName);
    });
});

// Font upload management
function removeFont(fontSlot) {
    if (confirm('Are you sure you want to remove this font? This action cannot be undone.')) {
        // Create a form to remove the font
        const form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'remove_font';
        
        const slotInput = document.createElement('input');
        slotInput.type = 'hidden';
        slotInput.name = 'font_slot';
        slotInput.value = fontSlot;
        
        form.appendChild(actionInput);
        form.appendChild(slotInput);
        document.body.appendChild(form);
        
        // Show loading state
        const statusSpan = document.querySelector('.font-upload-status');
        if (statusSpan) {
            statusSpan.innerHTML = 'Removing font...';
            statusSpan.className = statusSpan.className.replace('text-muted', 'text-warning');
        }
        
        form.submit();
    }
}
</script>

<?= template_admin_footer() ?>
