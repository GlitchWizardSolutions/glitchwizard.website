# Brand Theme System Documentation

## Overview

The GWS Universal Hybrid App now features a complete **database-driven brand theme system** that allows administrators to select from multiple professional, attractive CSS themes for all three areas of the website:

- **Public Website** (`public_html/`)
- **Admin Panel** (`public_html/admin/`)
- **Client Portal** (`public_html/client_portal/`)

## Key Features

✅ **Database-Driven Colors**: All brand colors are loaded from `setting_branding_colors` table  
✅ **Dynamic Theme Selection**: Admins can switch themes via database settings  
✅ **Professional Appearance**: All themes designed to be "attractive and professional, not obnoxious"  
✅ **Multi-Area Support**: Separate branded CSS for public, admin, and client portal areas  
✅ **Responsive Design**: All themes work perfectly on desktop, tablet, and mobile  
✅ **CSS Custom Properties**: Modern CSS variables for dynamic color application  

## Available Themes

### Public Website Themes
1. **Classic Professional** (`public-branding.css`) - Clean, traditional layout
2. **Subtle Elegance** (`public-branding-subtle.css`) - Minimal, understated design
3. **Bold Impact** (`public-branding-bold.css`) - Strong, vibrant design for maximum impact
4. **Friendly Casual** (`public-branding-casual.css`) - Approachable, relaxed design
5. **High Contrast** (`public-branding-high-contrast.css`) - Accessibility-focused design

### Admin Panel Themes
- **Professional Admin** (`admin-branding.css`) - Sophisticated business interface

### Client Portal Themes
- **Welcoming Client** (`client-branding.css`) - Friendly, approachable client interface

## System Architecture

### Core Files

#### 1. Brand Loader (`public_html/assets/includes/brand_loader.php`)
- Loads brand colors and fonts from database
- Provides `outputBrandCSS()` function for dynamic CSS generation
- Includes helper functions for color manipulation
- Fallback to default values if database unavailable

#### 2. Branding Functions (`public_html/assets/includes/branding-functions.php`)
- `getActiveBrandingTemplate()` - Get current active theme
- `getAllBrandingTemplates()` - Get all available themes
- `setActiveBrandingTemplate($key)` - Change active theme
- `getActiveBrandingCSSFile()` - Get CSS file for active theme

#### 3. Dynamic CSS Loading (`public_html/assets/includes/doctype.php`)
- Updated to load CSS dynamically based on active theme
- No more hardcoded CSS file references
- Automatic theme switching when database changes

### Database Tables

#### `setting_branding_colors`
Stores all brand colors with fallback defaults:
- `brand_primary_color`, `brand_secondary_color`
- `brand_tertiary_color`, `brand_quaternary_color`
- `brand_accent_color`, `brand_warning_color`, etc.
- Font settings: `brand_font_primary`, `brand_font_heading`, etc.

#### `setting_branding_templates`
Manages theme selection and configuration:
- `template_key` - Unique identifier
- `template_name` - Display name
- `template_description` - Theme description
- `css_class` - CSS class for theme
- `is_active` - Boolean for active theme
- `template_config` - JSON configuration

## How to Use

### For Administrators

#### 1. Change Themes
Visit: `public_html/admin/theme-selection.php`
- Preview all available themes
- Click "Select Theme" to activate
- Changes apply instantly across the website

#### 2. Test Themes
Visit: `public_html/brand-theme-test.php`
- See how active theme looks across components
- Test buttons, forms, cards, alerts, etc.
- Verify professional appearance

#### 3. Customize Colors
Update the `setting_branding_colors` table:
```sql
UPDATE setting_branding_colors 
SET brand_primary_color = '#your-color',
    brand_secondary_color = '#your-secondary-color'
WHERE id = 1;
```

### For Developers

#### 1. Using Brand Colors in CSS
```css
/* CSS files automatically have access to these variables */
.my-element {
    background-color: var(--brand-primary);
    color: var(--brand-text);
    border: 1px solid var(--brand-secondary);
}

/* RGB values for transparency */
.my-overlay {
    background-color: rgba(var(--brand-primary-rgb), 0.5);
}
```

#### 2. Using Brand Colors in PHP
```php
// Include brand loader
require_once 'assets/includes/brand_loader.php';

// Colors available as PHP variables
echo $brand_primary_color;      // #6c2eb6 (example)
echo $brand_secondary_color;    // #bf5512 (example)

// Generate CSS in PHP templates
outputBrandCSS();  // Outputs <style> with CSS variables
```

#### 3. Creating New Themes
1. Create new CSS file in `public_html/assets/css/`
2. Import dynamic brand variables:
   ```css
   @import url('brand-dynamic.css');
   ```
3. Use CSS custom properties:
   ```css
   .btn-primary {
       background: var(--brand-primary);
       color: var(--brand-text);
   }
   ```
4. Add theme to database:
   ```sql
   INSERT INTO setting_branding_templates 
   (template_key, template_name, template_description, css_class, template_config)
   VALUES ('my_theme', 'My Theme', 'Description', 'my-theme-class', 
   '{"css_file": "public-branding-my-theme.css"}');
   ```

## CSS Variable Reference

### Colors
- `--brand-primary` - Primary brand color
- `--brand-secondary` - Secondary brand color
- `--brand-tertiary` - Tertiary brand color
- `--brand-quaternary` - Quaternary brand color
- `--brand-accent` - Accent color
- `--brand-background` - Background color
- `--brand-text` - Primary text color
- `--brand-text-light` - Light text color
- `--brand-text-muted` - Muted text color

### RGB Values (for transparency)
- `--brand-primary-rgb` - RGB values of primary color
- `--brand-secondary-rgb` - RGB values of secondary color

### Fonts
- `--brand-font-primary` - Primary font family
- `--brand-font-secondary` - Secondary font family
- `--brand-font-heading` - Heading font family
- `--brand-font-body` - Body text font family

## Theme Characteristics

### Classic Professional
- Clean, traditional business appearance
- Moderate contrast and subtle effects
- Perfect for corporate websites
- Conservative color application

### Subtle Elegance  
- Minimal, understated design
- Very low opacity effects
- Sophisticated appearance
- Ideal for luxury brands

### Bold Impact
- Strong gradients and vibrant colors
- High contrast and dramatic effects
- Maximum visual impact
- Great for creative businesses

### Friendly Casual
- Rounded corners and playful elements
- Approachable, relaxed feeling
- Comic Sans font option for casual look
- Perfect for friendly businesses

### High Contrast
- Accessibility-focused design
- Strong contrast ratios
- Bold borders and clear hierarchy
- WCAG compliant for accessibility

## File Structure

```
public_html/
├── assets/
│   ├── css/
│   │   ├── brand-dynamic.css              # Core dynamic brand CSS
│   │   ├── public-branding.css            # Classic theme
│   │   ├── public-branding-subtle.css     # Subtle theme
│   │   ├── public-branding-bold.css       # Bold theme
│   │   ├── public-branding-casual.css     # Casual theme
│   │   └── public-branding-high-contrast.css # High contrast theme
│   └── includes/
│       ├── brand_loader.php               # Core brand loading system
│       ├── branding-functions.php         # Theme management functions
│       └── doctype.php                    # Updated with dynamic CSS loading
├── admin/
│   ├── assets/css/
│   │   └── admin-branding.css             # Admin panel branding
│   └── theme-selection.php               # Theme selection interface
├── client_portal/
│   └── assets/css/
│       └── client-branding.css            # Client portal branding
└── brand-theme-test.php                   # Theme testing page
```

## Troubleshooting

### Theme Not Changing
1. Check database connection in `brand_loader.php`
2. Verify `setting_branding_templates` table exists
3. Ensure CSS file exists in `assets/css/` directory
4. Clear browser cache after theme changes

### Colors Not Updating
1. Verify `setting_branding_colors` table has data
2. Check that `outputBrandCSS()` is called in page head
3. Ensure CSS uses `var(--brand-*)` custom properties

### CSS Not Loading
1. Check file paths in `getActiveBrandingCSSFile()`
2. Verify CSS file permissions
3. Check browser developer tools for 404 errors

## Best Practices

1. **Always Use CSS Variables**: Use `var(--brand-primary)` instead of hardcoded colors
2. **Test All Themes**: Verify new components work with all available themes
3. **Maintain Professional Appearance**: Keep designs clean and not obnoxious
4. **Use Fallbacks**: Always provide fallback values for CSS variables
5. **Document Changes**: Update this documentation when adding new themes

## Support and Updates

- **Theme System Version**: 2.0
- **Last Updated**: January 2025
- **Compatibility**: Bootstrap 5, modern browsers
- **Database**: MySQL/MariaDB with InnoDB engine

For additional support or theme customization requests, please refer to the development team or update the CSS files following the established patterns.
