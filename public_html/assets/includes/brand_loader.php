<?php
/**
 * Brand Color and Font Loader - Database Driven
 * 
 * This file loads all brand colors and fonts from the setting_branding_colors table
 * and makes them available as PHP variables and CSS custom properties.
 * 
 * Usage: Include this file after database connection is established
 * Result: Brand colors/fonts available as variables and CSS custom properties
 */

// Ensure database connection is available
if (!isset($pdo)) {
    die("Database connection not available. Include gws-universal-config.php first.");
}

// Initialize default brand values (fallbacks)
$brand_colors = [
    'primary' => '#6c2eb6',
    'secondary' => '#bf5512',
    'tertiary' => '#8B4513',
    'quaternary' => '#2E8B57',
    'accent' => '#28a745',
    'warning' => '#ffc107',
    'danger' => '#dc3545',
    'info' => '#17a2b8',
    'success' => '#28a745',
    'error' => '#dc3545',
    'background' => '#ffffff',
    'text' => '#333333',
    'text_light' => '#666666',
    'text_muted' => '#999999',
    'custom_1' => '#cccccc',
    'custom_2' => '#dddddd',
    'custom_3' => '#eeeeee'
];

$brand_fonts = [
    'primary' => 'Inter, system-ui, sans-serif',
    'secondary' => 'Roboto, Arial, sans-serif',
    'heading' => 'Inter, system-ui, sans-serif',
    'body' => 'Roboto, Arial, sans-serif',
    'monospace' => 'SF Mono, Monaco, Consolas, monospace'
];

// Load brand colors and fonts from database
try {
    $stmt = $pdo->query("SELECT * FROM setting_branding_colors WHERE id = 1 LIMIT 1");
    $brand_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($brand_data) {
        // Map database fields to brand_colors array - Database values override defaults
        $brand_colors = [
            'primary' => $brand_data['brand_primary_color'] ?: $brand_colors['primary'],
            'secondary' => $brand_data['brand_secondary_color'] ?: $brand_colors['secondary'],
            'tertiary' => $brand_data['brand_tertiary_color'] ?: $brand_colors['tertiary'],
            'quaternary' => $brand_data['brand_quaternary_color'] ?: $brand_colors['quaternary'],
            'accent' => $brand_data['brand_accent_color'] ?: $brand_colors['accent'],
            'warning' => $brand_data['brand_warning_color'] ?: $brand_colors['warning'],
            'danger' => $brand_data['brand_danger_color'] ?: $brand_colors['danger'],
            'info' => $brand_data['brand_info_color'] ?: $brand_colors['info'],
            'success' => $brand_data['brand_success_color'] ?: $brand_colors['success'],
            'error' => $brand_data['brand_error_color'] ?: $brand_colors['error'],
            'background' => $brand_data['brand_background_color'] ?: $brand_colors['background'],
            'text' => $brand_data['brand_text_color'] ?: $brand_colors['text'],
            'text_light' => $brand_data['brand_text_light'] ?: $brand_colors['text_light'],
            'text_muted' => $brand_data['brand_text_muted'] ?: $brand_colors['text_muted'],
            'custom_1' => $brand_data['custom_color_1'] ?: $brand_colors['custom_1'],
            'custom_2' => $brand_data['custom_color_2'] ?: $brand_colors['custom_2'],
            'custom_3' => $brand_data['custom_color_3'] ?: $brand_colors['custom_3']
        ];
        
        // Map database fields to brand_fonts array
        $brand_fonts = [
            'primary' => $brand_data['brand_font_primary'] ?? $brand_fonts['primary'],
            'secondary' => $brand_data['brand_font_secondary'] ?? $brand_fonts['secondary'],
            'heading' => $brand_data['brand_font_heading'] ?? $brand_fonts['heading'],
            'body' => $brand_data['brand_font_body'] ?? $brand_fonts['body'],
            'monospace' => $brand_data['brand_font_monospace'] ?? $brand_fonts['monospace']
        ];
    }
} catch (PDOException $e) {
    // Log error but continue with defaults
    error_log("Brand loader error: " . $e->getMessage());
}

// Set individual variables for backward compatibility (used in footer.php and other templates)
$brand_primary_color = $brand_colors['primary'];
$brand_secondary_color = $brand_colors['secondary'];
$brand_tertiary_color = $brand_colors['tertiary'];
$brand_quaternary_color = $brand_colors['quaternary'];
$brand_accent_color = $brand_colors['accent'];
$brand_warning_color = $brand_colors['warning'];
$brand_danger_color = $brand_colors['danger'];
$brand_info_color = $brand_colors['info'];
$brand_success_color = $brand_colors['success'];
$brand_error_color = $brand_colors['error'];
$brand_background_color = $brand_colors['background'];
$brand_text_color = $brand_colors['text'];
$brand_text_light = $brand_colors['text_light'];
$brand_text_muted = $brand_colors['text_muted'];

$brand_font_primary = $brand_fonts['primary'];
$brand_font_secondary = $brand_fonts['secondary'];
$brand_font_heading = $brand_fonts['heading'];
$brand_font_body = $brand_fonts['body'];
$brand_font_monospace = $brand_fonts['monospace'];

/**
 * Function to output CSS custom properties for brand colors and fonts
 * This can be called in the <head> section or anywhere CSS is needed
 */
function outputBrandCSS() {
    global $brand_colors, $brand_fonts;
    
    echo "<style id=\"dynamic-brand-css\">\n";
    echo ":root {\n";
    
    // Brand Colors as CSS Custom Properties
    echo "  /* Brand Colors - Database Driven */\n";
    foreach ($brand_colors as $key => $value) {
        $css_var_name = str_replace('_', '-', $key);
        echo "  --brand-{$css_var_name}: {$value};\n";
    }
    
    // Additional CSS property aliases for compatibility
    echo "\n  /* Compatibility Aliases */\n";
    echo "  --accent-color: var(--brand-primary);\n";
    echo "  --heading-color: var(--brand-secondary);\n";
    echo "  --primary-color: var(--brand-primary);\n";
    echo "  --secondary-color: var(--brand-secondary);\n";
    echo "  --success-color: var(--brand-success);\n";
    echo "  --danger-color: var(--brand-danger);\n";
    echo "  --warning-color: var(--brand-warning);\n";
    echo "  --info-color: var(--brand-info);\n";
    
    // Brand Fonts as CSS Custom Properties
    echo "\n  /* Brand Fonts - Database Driven */\n";
    foreach ($brand_fonts as $key => $value) {
        $css_var_name = str_replace('_', '-', $key);
        echo "  --brand-font-{$css_var_name}: '{$value}';\n";
    }
    
    // Font aliases for compatibility
    echo "\n  /* Font Compatibility Aliases */\n";
    echo "  --font-primary: var(--brand-font-primary);\n";
    echo "  --font-secondary: var(--brand-font-secondary);\n";
    echo "  --font-heading: var(--brand-font-heading);\n";
    echo "  --font-body: var(--brand-font-body);\n";
    
    echo "}\n";
    
    // Add some base typography rules using the brand fonts
    echo "\n/* Base Typography - Brand Fonts Applied */\n";
    echo "body {\n";
    echo "  font-family: var(--brand-font-body);\n";
    echo "  color: var(--brand-text);\n";
    echo "}\n";
    
    echo "/* Exclude hero sections from body text color inheritance */\n";
    echo ".hero, section.hero, #hero {\n";
    echo "  color: white !important;\n";
    echo "}\n";
    
    echo ".hero *, section.hero *, #hero * {\n";
    echo "  color: inherit !important;\n";
    echo "}\n";
    
    echo ".hero h1, section.hero h1, #hero h1,\n";
    echo ".hero h2, section.hero h2, #hero h2,\n";
    echo ".hero h3, section.hero h3, #hero h3,\n";
    echo ".hero h4, section.hero h4, #hero h4,\n";
    echo ".hero h5, section.hero h5, #hero h5,\n";
    echo ".hero h6, section.hero h6, #hero h6 {\n";
    echo "  color: white !important;\n";
    echo "  font-weight: 800 !important;\n";
    echo "  font-size: 72px !important;\n";
    echo "  line-height: 84px !important;\n";
    echo "  text-shadow: \n";
    echo "    -2px -2px 0 rgba(0, 0, 0, 0.8),\n";
    echo "    2px -2px 0 rgba(0, 0, 0, 0.8),\n";
    echo "    -2px 2px 0 rgba(0, 0, 0, 0.8),\n";
    echo "    2px 2px 0 rgba(0, 0, 0, 0.8),\n";
    echo "    0 4px 8px rgba(0, 0, 0, 0.6) !important;\n";
    echo "  background: rgba(0, 0, 0, 0.1) !important;\n";
    echo "  backdrop-filter: blur(1px) !important;\n";
    echo "  padding: 10px 20px !important;\n";
    echo "  border-radius: 6px !important;\n";
    echo "  margin: 0 auto 15px auto !important;\n";
    echo "  display: inline-block !important;\n";
    echo "}\n";
    
    echo ".hero p, section.hero p, #hero p {\n";
    echo "  color: rgba(255, 255, 255, 0.98) !important;\n";
    echo "  font-size: 20px !important;\n";
    echo "  line-height: 28px !important;\n";
    echo "  font-weight: 500 !important;\n";
    echo "  text-shadow: \n";
    echo "    -1px -1px 0 rgba(0, 0, 0, 0.7),\n";
    echo "    1px -1px 0 rgba(0, 0, 0, 0.7),\n";
    echo "    -1px 1px 0 rgba(0, 0, 0, 0.7),\n";
    echo "    1px 1px 0 rgba(0, 0, 0, 0.7),\n";
    echo "    0 2px 4px rgba(0, 0, 0, 0.5) !important;\n";
    echo "  background: rgba(0, 0, 0, 0.08) !important;\n";
    echo "  backdrop-filter: blur(1px) !important;\n";
    echo "  padding: 8px 16px !important;\n";
    echo "  border-radius: 4px !important;\n";
    echo "  margin: 0 auto !important;\n";
    echo "  display: inline-block !important;\n";
    echo "  max-width: 800px !important;\n";
    echo "}\n";
    
    echo "/* Fix hero form font sizes and layout */\n";
    echo ".hero-inline-form, .hero-form-header h3, .hero-form-subtitle p {\n";
    echo "  font-size: inherit !important;\n";
    echo "}\n";
    
    echo ".hero-form-header h3 {\n";
    echo "  font-size: 0.9rem !important;\n";
    echo "  background: none !important;\n";
    echo "  padding: 0 !important;\n";
    echo "  margin: 0 !important;\n";
    echo "  font-weight: 600 !important;\n";
    echo "}\n";
    
    echo ".hero-form-subtitle p {\n";
    echo "  font-size: 0.85rem !important;\n";
    echo "}\n";
    
    echo "/* Make form horizontal and compact */\n";
    echo ".hero-inline-form {\n";
    echo "  display: flex !important;\n";
    echo "  flex-direction: column !important;\n";
    echo "  align-items: center !important;\n";
    echo "  max-width: 100% !important;\n";
    echo "  padding: 0.8rem 1.2rem !important;\n";
    echo "}\n";
    
    echo ".hero-inline-form form {\n";
    echo "  display: flex !important;\n";
    echo "  flex-wrap: wrap !important;\n";
    echo "  align-items: center !important;\n";
    echo "  justify-content: center !important;\n";
    echo "  gap: 0.75rem !important;\n";
    echo "  width: 100% !important;\n";
    echo "}\n";
    
    echo ".hero-form-inputs {\n";
    echo "  display: flex !important;\n";
    echo "  flex-wrap: wrap !important;\n";
    echo "  gap: 0.5rem !important;\n";
    echo "  flex: 1 !important;\n";
    echo "  justify-content: center !important;\n";
    echo "}\n";
    
    echo "/* Responsive hero text sizing */\n";
    echo "@media (max-width: 1200px) {\n";
    echo "  .hero h1, section.hero h1, #hero h1 {\n";
    echo "    font-size: 64px !important;\n";
    echo "    line-height: 72px !important;\n";
    echo "    padding: 12px 20px !important;\n";
    echo "  }\n";
    echo "  .hero p, section.hero p, #hero p {\n";
    echo "    font-size: 24px !important;\n";
    echo "    line-height: 32px !important;\n";
    echo "  }\n";
    echo "}\n";
    
    echo "@media (max-width: 992px) {\n";
    echo "  .hero h1, section.hero h1, #hero h1 {\n";
    echo "    font-size: 48px !important;\n";
    echo "    line-height: 56px !important;\n";
    echo "    padding: 10px 18px !important;\n";
    echo "  }\n";
    echo "  .hero p, section.hero p, #hero p {\n";
    echo "    font-size: 22px !important;\n";
    echo "    line-height: 30px !important;\n";
    echo "    padding: 10px 16px !important;\n";
    echo "  }\n";
    echo "}\n";
    
    echo "@media (max-width: 768px) {\n";
    echo "  .hero h1, section.hero h1, #hero h1 {\n";
    echo "    font-size: 38px !important;\n";
    echo "    line-height: 44px !important;\n";
    echo "    padding: 8px 15px !important;\n";
    echo "  }\n";
    echo "  .hero p, section.hero p, #hero p {\n";
    echo "    font-size: 20px !important;\n";
    echo "    line-height: 28px !important;\n";
    echo "    padding: 8px 14px !important;\n";
    echo "  }\n";
    echo "}\n";
    
    echo "@media (max-width: 576px) {\n";
    echo "  .hero h1, section.hero h1, #hero h1 {\n";
    echo "    font-size: 32px !important;\n";
    echo "    line-height: 38px !important;\n";
    echo "    padding: 6px 12px !important;\n";
    echo "  }\n";
    echo "  .hero p, section.hero p, #hero p {\n";
    echo "    font-size: 18px !important;\n";
    echo "    line-height: 26px !important;\n";
    echo "    padding: 6px 12px !important;\n";
    echo "  }\n";
    echo "}\n";
    
    echo "h1, h2, h3, h4, h5, h6 {\n";
    echo "  font-family: var(--brand-font-heading);\n";
    echo "}\n";
    
    echo ".font-primary {\n";
    echo "  font-family: var(--brand-font-primary);\n";
    echo "}\n";
    
    echo ".font-secondary {\n";
    echo "  font-family: var(--brand-font-secondary);\n";
    echo "}\n";
    
    echo ".font-monospace {\n";
    echo "  font-family: var(--brand-font-monospace);\n";
    echo "}\n";
    
    // Hero Form Accessibility and Positioning Rules
    echo "\n/* Hero Form Accessibility and Positioning */\n";
    echo ".hero-inline-form {\n";
    echo "  position: relative !important;\n";
    echo "  height: auto !important;\n";
    echo "  min-height: 80px !important;\n";
    echo "  max-height: 120px !important;\n";
    echo "}\n";
    
    echo ".hero-inline-form form {\n";
    echo "  min-height: 60px !important;\n";
    echo "  max-height: 80px !important;\n";
    echo "}\n";
    
    echo ".hero-form-header {\n";
    echo "  flex: 0 0 140px !important;\n";
    echo "  display: flex !important;\n";
    echo "  align-items: center !important;\n";
    echo "  justify-content: center !important;\n";
    echo "}\n";
    
    echo ".hero-form-button-container {\n";
    echo "  flex: 0 0 160px !important;\n";
    echo "  display: flex !important;\n";
    echo "  align-items: center !important;\n";
    echo "  justify-content: center !important;\n";
    echo "}\n";
    
    echo ".hero-form-inputs {\n";
    echo "  flex: 1 !important;\n";
    echo "  max-width: calc(100% - 320px) !important;\n";
    echo "  min-width: 320px !important;\n";
    echo "}\n";
    
    echo ".hero-get-started {\n";
    echo "  font-size: clamp(1rem, 2vw, 1.2rem) !important;\n";
    echo "  font-weight: 700 !important;\n";
    echo "  text-shadow: 2px 2px 4px rgba(0,0,0,0.8), -1px -1px 2px rgba(0,0,0,0.6), 1px -1px 2px rgba(0,0,0,0.6), -1px 1px 2px rgba(0,0,0,0.6) !important;\n";
    echo "}\n";
    
    echo ".hero-form-button {\n";
    echo "  background: #FFD700 !important;\n";
    echo "  border: 2px solid #000000 !important;\n";
    echo "  color: #2c3e50 !important;\n";
    echo "  text-shadow: 1px 1px 0px rgba(0,0,0,0.8), -1px -1px 0px rgba(0,0,0,0.8), 1px -1px 0px rgba(0,0,0,0.8), -1px 1px 0px rgba(0,0,0,0.8), 0px 1px 0px rgba(0,0,0,0.8), 0px -1px 0px rgba(0,0,0,0.8), 1px 0px 0px rgba(0,0,0,0.8), -1px 0px 0px rgba(0,0,0,0.8) !important;\n";
    echo "}\n";
    
    echo ".hero-form-button:hover {\n";
    echo "  background: #FFA500 !important;\n";
    echo "  border: 2px solid #000000 !important;\n";
    echo "}\n";
    
    echo ".hero-form-input {\n";
    echo "  background: white !important;\n";
    echo "  color: #2c3e50 !important;\n";
    echo "}\n";
    
    echo "\n/* Section Text Accessibility Rules */\n";
    echo "/* Always use readable text colors regardless of brand colors */\n";
    
    echo "/* Main Content Text - Use brand text color for readability */\n";
    echo "section:not(.hero) h1, section:not(.hero) h2, section:not(.hero) h3,\n";
    echo "section:not(.hero) h4, section:not(.hero) h5, section:not(.hero) h6,\n";
    echo ".section-heading, .content-heading {\n";
    echo "  color: var(--brand-text) !important;\n";
    echo "}\n";
    
    echo "section:not(.hero) p, section:not(.hero) .text-content,\n";
    echo ".section-text, .content-text, main p, article p {\n";
    echo "  color: var(--brand-text) !important;\n";
    echo "}\n";
    
    echo "section:not(.hero) .text-muted, .secondary-text {\n";
    echo "  color: var(--brand-text-muted) !important;\n";
    echo "}\n";
    
    echo "/* Footer text should use readable colors */\n";
    echo "#footer .link-list-item a, #footer .service-list-item a, #footer .contact-item {\n";
    echo "  color: var(--brand-text) !important;\n";
    echo "}\n";
    
    echo "/* Specific contact information should use text color */\n";
    echo "#footer .contact-item span, #footer .contact-item a[href^='tel:'], #footer .contact-item a[href^='mailto:'] {\n";
    echo "  color: var(--brand-text) !important;\n";
    echo "}\n";
    
    echo "/* Footer section headings use brand colors */\n";
    echo "#footer h4 {\n";
    echo "  color: var(--brand-primary) !important;\n";
    echo "}\n";
    
    echo "/* Footer icons use brand colors */\n";
    echo "#footer .link-list-item i, #footer .service-list-item i, #footer .contact-item i {\n";
    echo "  color: var(--brand-primary) !important;\n";
    echo "}\n";
    
    echo "/* Social media icons use brand colors */\n";
    echo "#footer .social-media-section a, #footer .social-icon {\n";
    echo "  color: var(--brand-primary) !important;\n";
    echo "}\n";
    
    echo "/* Special footer links (accent links) use brand colors and enhanced visibility */\n";
    echo "#footer .brand-link, #footer .accent-link {\n";
    echo "  color: var(--brand-primary) !important;\n";
    echo "  font-weight: 600 !important;\n";
    echo "  font-size: 1.05em !important;\n";
    echo "}\n";
    
    echo "#footer .brand-link:hover, #footer .accent-link:hover {\n";
    echo "  color: var(--brand-secondary) !important;\n";
    echo "}\n";
    
    echo "/* Copyright section stays white - brand independent */\n";
    echo "#footer .footer-copyright, #footer .footer-copyright p, #footer .footer-design-link {\n";
    echo "  color: #fff !important;\n";
    echo "}\n";
    
    echo "/* Navigation text should be readable */\n";
    echo ".navbar .nav-link, .navbar .navbar-brand {\n";
    echo "  color: var(--brand-text) !important;\n";
    echo "}\n";
    
    echo "/* Brand colors should only be used for accents, buttons, and highlights */\n";
    echo ".brand-accent, .accent-text { color: var(--brand-primary) !important; }\n";
    echo ".brand-link, .highlight-link { color: var(--brand-primary) !important; }\n";
    echo ".brand-link:hover, .highlight-link:hover { color: var(--brand-secondary) !important; }\n";
    echo ".brand-bg, .accent-bg { background-color: var(--brand-primary) !important; }\n";
    echo ".brand-border, .accent-border { border-color: var(--brand-primary) !important; }\n";
    
    echo "\n/* Clean Professional Services Section - Footer-Inspired */\n";
    echo ".services .service-item {\n";
    echo "  background: white;\n";
    echo "  border-radius: 16px;\n";
    echo "  padding: 2.5rem 1.5rem;\n";
    echo "  text-align: center;\n";
    echo "  box-shadow: 0 4px 20px rgba(0,0,0,0.08);\n";
    echo "  transition: all 0.3s ease;\n";
    echo "  border: 1px solid rgba(0,0,0,0.05);\n";
    echo "  position: relative;\n";
    echo "  height: 100%;\n";
    echo "}\n";
    
    echo ".services .service-item:hover {\n";
    echo "  transform: translateY(-8px);\n";
    echo "  box-shadow: 0 12px 40px rgba(0,0,0,0.15);\n";
    echo "  border-color: var(--brand-primary);\n";
    echo "}\n";
    
    echo ".services .service-item .icon {\n";
    echo "  margin: 0 auto 1.5rem;\n";
    echo "  display: flex;\n";
    echo "  align-items: center;\n";
    echo "  justify-content: center;\n";
    echo "  height: 80px;\n";
    echo "}\n";
    
    echo ".services .service-item .icon i {\n";
    echo "  font-size: 3.5rem;\n";
    echo "  color: var(--brand-primary);\n";
    echo "  transition: all 0.3s ease;\n";
    echo "  opacity: 1;\n";
    echo "  visibility: visible;\n";
    echo "}\n";
    
    echo ".services .service-item:hover .icon i {\n";
    echo "  color: var(--brand-secondary);\n";
    echo "  transform: scale(1.1);\n";
    echo "  opacity: 1;\n";
    echo "  visibility: visible;\n";
    echo "}\n";
    
    echo ".services .service-item h3 {\n";
    echo "  color: #2c3e50;\n";
    echo "  font-size: 1.4rem;\n";
    echo "  font-weight: 600;\n";
    echo "  margin-bottom: 1rem;\n";
    echo "  transition: color 0.3s ease;\n";
    echo "  height: 3.5rem;\n";
    echo "  display: flex;\n";
    echo "  align-items: center;\n";
    echo "  justify-content: center;\n";
    echo "  line-height: 1.2;\n";
    echo "}\n";
    
    echo ".services .service-item:hover h3 {\n";
    echo "  color: var(--brand-primary);\n";
    echo "}\n";
    
    echo ".services .service-item p {\n";
    echo "  color: #666666;\n";
    echo "  font-size: 0.95rem;\n";
    echo "  line-height: 1.6;\n";
    echo "  margin-bottom: 0;\n";
    echo "  margin-top: auto;\n";
    echo "}\n";
    
    echo "/* Hide old blob SVG completely and fix icon visibility */\n";
    echo ".services .service-item svg {\n";
    echo "  display: none !important;\n";
    echo "}\n";
    
    echo "/* Force icon visibility and override any hidden styles */\n";
    echo ".services .service-item .icon i,\n";
    echo ".services .service-item:hover .icon i {\n";
    echo "  display: inline-block !important;\n";
    echo "  opacity: 1 !important;\n";
    echo "  visibility: visible !important;\n";
    echo "}\n";
    
    echo "/* Clean override for all color variants */\n";
    echo ".services .service-item.item-cyan,\n";
    echo ".services .service-item.item-orange,\n";
    echo ".services .service-item.item-teal,\n";
    echo ".services .service-item.item-red,\n";
    echo ".services .service-item.item-indigo,\n";
    echo ".services .service-item.item-pink {\n";
    echo "  /* All use same clean professional styling */\n";
    echo "}\n";
    
    echo "/* Override any existing color-specific icon rules */\n";
    echo ".services .service-item.item-cyan i,\n";
    echo ".services .service-item.item-orange i,\n";
    echo ".services .service-item.item-teal i,\n";
    echo ".services .service-item.item-red i,\n";
    echo ".services .service-item.item-indigo i,\n";
    echo ".services .service-item.item-pink i {\n";
    echo "  color: var(--brand-primary) !important;\n";
    echo "  opacity: 1 !important;\n";
    echo "  visibility: visible !important;\n";
    echo "}\n";
    
    echo ".services .service-item.item-cyan:hover i,\n";
    echo ".services .service-item.item-orange:hover i,\n";
    echo ".services .service-item.item-teal:hover i,\n";
    echo ".services .service-item.item-red:hover i,\n";
    echo ".services .service-item.item-indigo:hover i,\n";
    echo ".services .service-item.item-pink:hover i {\n";
    echo "  color: var(--brand-secondary) !important;\n";
    echo "  opacity: 1 !important;\n";
    echo "  visibility: visible !important;\n";
    echo "}\n";
    
    echo "\n/* Service CTA Button Hover Effect */\n";
    echo "#service-cta .btn:hover {\n";
    echo "  background-color: var(--brand-secondary) !important;\n";
    echo "  border-color: var(--brand-secondary) !important;\n";
    echo "  transform: translateY(-2px);\n";
    echo "  box-shadow: 0 6px 20px rgba(var(--accent-color-rgb), 0.3);\n";
    echo "}\n";
    
    echo "\n/* Override scroll-to-top button with brand colors */\n";
    echo ".scroll-top {\n";
    echo "  background-color: var(--brand-primary) !important;\n";
    if (isset($brand_colors['primary'])) {
        echo "  background-color: {$brand_colors['primary']} !important;\n";
    }
    echo "}\n";
    
    echo ".scroll-top:hover {\n";
    echo "  background-color: var(--brand-secondary) !important;\n";
    if (isset($brand_colors['secondary'])) {
        echo "  background-color: {$brand_colors['secondary']} !important;\n";
    }
    echo "}\n";
    
    echo "\n/* Override footer links with brand colors */\n";
    echo ".footer a {\n";
    echo "  color: var(--brand-primary) !important;\n";
    if (isset($brand_colors['primary'])) {
        echo "  color: {$brand_colors['primary']} !important;\n";
    }
    echo "}\n";
    
    echo ".footer a:hover {\n";
    echo "  color: var(--brand-secondary) !important;\n";
    if (isset($brand_colors['secondary'])) {
        echo "  color: {$brand_colors['secondary']} !important;\n";
    }
    echo "}\n";
    
    echo "\n/* Main Call-to-Action Section Styling */\n";
    echo "section.call-to-action.section.accent-background,\n";
    echo "#call-to-action.call-to-action.section.accent-background {\n";
    echo "  background-color: var(--brand-primary) !important;\n";
    if (isset($brand_colors['primary'])) {
        echo "  background-color: {$brand_colors['primary']} !important;\n";
    }
    echo "  color: white !important;\n";
    echo "}\n";
    
    // Also output the actual brand color value for maximum compatibility
    if (isset($brand_colors['primary'])) {
        echo "section.call-to-action.section.accent-background,\n";
        echo "#call-to-action.call-to-action.section.accent-background {\n";
        echo "  background-color: {$brand_colors['primary']} !important;\n";
        echo "  background: {$brand_colors['primary']} !important;\n";
        echo "}\n";
    }
    
    echo ".call-to-action.section.accent-background h3 {\n";
    echo "  color: white !important;\n";
    echo "  font-size: 2.5rem !important;\n";
    echo "  font-weight: 600 !important;\n";
    echo "  margin-bottom: 1.5rem !important;\n";
    echo "  line-height: 1.2 !important;\n";
    echo "}\n";
    
    echo ".call-to-action.section.accent-background p {\n";
    echo "  color: white !important;\n";
    echo "  font-size: 1.125rem !important;\n";
    echo "  line-height: 1.6 !important;\n";
    echo "  margin-bottom: 2rem !important;\n";
    echo "  opacity: 0.95 !important;\n";
    echo "}\n";
    
    echo ".call-to-action.section.accent-background .cta-btn {\n";
    echo "  background-color: white !important;\n";
    echo "  color: var(--brand-primary) !important;\n";
    if (isset($brand_colors['primary'])) {
        echo "  color: {$brand_colors['primary']} !important;\n";
    }
    echo "  border: 2px solid white !important;\n";
    echo "  padding: 14px 32px !important;\n";
    echo "  font-size: 1rem !important;\n";
    echo "  font-weight: 600 !important;\n";
    echo "  border-radius: 6px !important;\n";
    echo "  text-decoration: none !important;\n";
    echo "  display: inline-block !important;\n";
    echo "  transition: all 0.3s ease !important;\n";
    echo "}\n";
    
    echo ".call-to-action.section.accent-background .cta-btn:hover {\n";
    echo "  background-color: var(--brand-secondary) !important;\n";
    if (isset($brand_colors['secondary'])) {
        echo "  background-color: {$brand_colors['secondary']} !important;\n";
    }
    echo "  color: white !important;\n";
    echo "  border-color: var(--brand-secondary) !important;\n";
    if (isset($brand_colors['secondary'])) {
        echo "  border-color: {$brand_colors['secondary']} !important;\n";
    }
    echo "  transform: translateY(-2px) !important;\n";
    echo "  box-shadow: 0 6px 20px rgba(0,0,0,0.2) !important;\n";
    echo "}\n";
    
    echo "\n/* Footer Copyright Section Styling */\n";
    echo ".footer .copyright {\n";
    echo "  font-size: 1.25rem !important;\n";
    echo "  line-height: 1.6 !important;\n";
    echo "}\n";
    
    echo ".footer .copyright a {\n";
    echo "  color: white !important;\n";
    echo "  text-decoration: none !important;\n";
    echo "  transition: all 0.3s ease !important;\n";
    echo "  font-size: 1.25rem !important;\n";
    echo "}\n";
    
    echo ".footer .copyright a:hover {\n";
    echo "  color: white !important;\n";
    echo "  font-weight: bold !important;\n";
    echo "  text-decoration: none !important;\n";
    echo "}\n";
    
    echo "\n/* Override any default link colors with brand colors */\n";
    echo "a, .link {\n";
    echo "  color: var(--brand-primary) !important;\n";
    if (isset($brand_colors['primary'])) {
        echo "  color: {$brand_colors['primary']} !important;\n";
    }
    echo "}\n";
    
    echo "a:hover, .link:hover {\n";
    echo "  color: var(--brand-secondary) !important;\n";
    if (isset($brand_colors['secondary'])) {
        echo "  color: {$brand_colors['secondary']} !important;\n";
    }
    echo "}\n";
    
    echo "</style>\n";
}

/**
 * Function to get brand color RGB values for CSS rgba() functions
 */
function getBrandColorRGB($color_key) {
    global $brand_colors;
    
    $hex = $brand_colors[$color_key] ?? '#000000';
    $hex = ltrim($hex, '#');
    
    if (strlen($hex) === 3) {
        $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    }
    
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    return "$r, $g, $b";
}

/**
 * Function to check if a color is light or dark (for automatic text color selection)
 */
function isBrandColorLight($color_key) {
    global $brand_colors;
    
    $hex = $brand_colors[$color_key] ?? '#000000';
    $hex = ltrim($hex, '#');
    
    if (strlen($hex) === 3) {
        $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    }
    
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    // Calculate luminance
    $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
    
    return $luminance > 0.5;
}
