<?php
/*
 * Database-Driven Branding Settings
 * 
 * SYSTEM: GWS Universal Hybrid App
 * FILE: branding_settings.php
 * LOCATION: /public_html/assets/includes/settings/
 * PURPOSE: Current branding values from database/admin interface
 * 
 * NOTE: For visual template management, use enhanced system at:
 * /admin/settings/branding-templates-enhanced.php
 * 
 * LAST UPDATED: 2025-08-16 13:50:34
 * VERSION: 2.0 (Database-driven)
 */

// =========================================================================
// BUSINESS IDENTITY (Multiple lengths for different use cases)
// =========================================================================

$business_name_short = 'Burden2Blessings';
$business_name_medium = 'Burden to Blessings';
$business_name_long = 'Burden to Blessings LLC';

$business_tagline_short = 'We look forward to serving you!';
$business_tagline_medium = 'It is our goal to give you the best possible service.';
$business_tagline_long = 'We are looking forward to helping you have the best experience possible during this time of change.';

// BACKWARD COMPATIBILITY: Maintain existing business_name variable
$business_name = $business_name_long;

// =========================================================================
// BRAND COLORS (Up to 6 brand colors)
// =========================================================================

// Primary brand colors (EXISTING - DO NOT CHANGE NAMES)
$brand_primary_color = '#3b89b0';
$brand_secondary_color = '#bf5512';

// Additional brand colors (NEW)
$brand_accent_color = '#28a745';
$brand_warning_color = '#ffc107';
$brand_danger_color = '#dc3545';
$brand_info_color = '#17a2b8';

// Background and text colors
$brand_background_color = '#ffffff';
$brand_text_color = '#333333';
$brand_text_light = '#666666';
$brand_text_muted = '#999999';

// =========================================================================
// BRAND FONTS (Up to 6 font families)
// =========================================================================

// BACKWARD COMPATIBILITY: Maintain existing font variable
$brand_font_family = 'Roboto, Poppins, Raleway, Arial, sans-serif';

// Additional font options
$brand_font_primary = $brand_font_family;
$brand_font_headings = 'Poppins, Arial, sans-serif';
$brand_font_body = 'Roboto, Arial, sans-serif';
$brand_font_accent = 'Raleway, Arial, sans-serif';
$brand_font_monospace = 'Consolas, Monaco, &quot;Courier New&quot;, monospace';
$brand_font_display = 'Georgia, &quot;Times New Roman&quot;, serif';

// Font file locations (if uploaded custom fonts)
$brand_font_file_1 = '';
$brand_font_file_2 = '';
$brand_font_file_3 = '';
$brand_font_file_4 = '';
$brand_font_file_5 = '';
$brand_font_file_6 = '';

// =========================================================================
// LOGOS (Up to 6 logo variations)
// =========================================================================

// BACKWARD COMPATIBILITY: Maintain existing logo variables
$business_logo = 'assets/img/logo.png';

// Additional logo variations (NEW)
$business_logo_main = $business_logo;
$business_logo_horizontal = 'assets/branding/logo_horizontal.png';
$business_logo_vertical = 'assets/branding/logo_vertical.png';
$business_logo_square = 'assets/branding/logo_square.png';
$business_logo_white = 'assets/branding/logo_white.png';
$business_logo_small = 'assets/branding/logo_small.png';

// =========================================================================
// FAVICONS (Up to 3 favicon variations)
// =========================================================================

// BACKWARD COMPATIBILITY: Maintain existing favicon variables
$favicon = 'assets/img/favicon.png';
$apple_touch_icon = 'assets/img/apple-touch-icon.png';

// Additional favicon variations (NEW)
$favicon_main = $favicon;
$favicon_blog = 'assets/branding/favicon_blog.ico';
$favicon_portal = 'assets/branding/favicon_portal.ico';

// =========================================================================
// SOCIAL SHARE IMAGES (Up to 6 variations)
// =========================================================================

$social_share_default = 'assets/branding/social_default.jpg';
$social_share_facebook = 'assets/branding/social_facebook.jpg';
$social_share_twitter = 'assets/branding/social_twitter.jpg';
$social_share_linkedin = 'assets/branding/social_linkedin.jpg';
$social_share_instagram = 'assets/branding/social_instagram.jpg';
$social_share_blog = 'assets/branding/social_blog.jpg';

// =========================================================================
// AUTHOR AND META INFORMATION (EXISTING)
// =========================================================================

$author = 'GWS';

// =========================================================================
// FOOTER BRANDING SETTINGS
// =========================================================================

$footer_business_name_type = 'medium';
$footer_logo_enabled = true;
$footer_logo_position = 'left';
$footer_logo_file = 'business_logo';

// =========================================================================
// SYSTEM NOTES
// =========================================================================

/*
 * TEMPLATE MANAGEMENT:
 * Visual templates are now managed through the enhanced database-driven
 * system. Access at: /admin/settings/branding-templates-enhanced.php
 * 
 * LEGACY ARCHIVE:
 * Old file-based template system has been archived to:
 * branding_settings_legacy_archive.php for reference
 */
