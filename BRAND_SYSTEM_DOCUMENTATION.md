# Dynamic Brand Color & Font System

## Overview

This workspace now has a fully dynamic, database-driven brand color and font system. When you create a new company website, you only need to change the values in the database, and it will update all CSS colors and fonts used throughout the website.

## How It Works

### 1. Database Storage
All brand colors and fonts are stored in the `setting_branding_colors` table with the following structure:

**Color Fields:**
- `brand_primary_color` - Main brand color for buttons, links, primary elements
- `brand_secondary_color` - Secondary brand color for headings and accents
- `brand_tertiary_color` - Third brand color for additional variety
- `brand_quaternary_color` - Fourth brand color for extended palette
- `brand_accent_color` - Accent color for special highlights
- `brand_warning_color` - Warning/alert color
- `brand_danger_color` - Error/danger color
- `brand_info_color` - Information color
- `brand_success_color` - Success/confirmation color
- `brand_error_color` - Error text color
- `brand_background_color` - Main background color
- `brand_text_color` - Primary text color
- `brand_text_light` - Light text color
- `brand_text_muted` - Muted/subtle text color
- `custom_color_1`, `custom_color_2`, `custom_color_3` - Additional custom colors

**Font Fields:**
- `brand_font_primary` - Primary brand font
- `brand_font_secondary` - Secondary brand font
- `brand_font_heading` - Font for headings (h1, h2, etc.)
- `brand_font_body` - Font for body text and paragraphs
- `brand_font_monospace` - Font for code and monospace text

### 2. System Components

#### A. Brand Loader (`public_html/assets/includes/brand_loader.php`)
- Loads brand colors and fonts from the database
- Provides fallback defaults if database is unavailable
- Creates PHP variables for backward compatibility
- Includes `outputBrandCSS()` function to generate CSS custom properties
- Includes helper functions like `getBrandColorRGB()` and `isBrandColorLight()`

#### B. Dynamic CSS Integration (`public_html/assets/includes/doctype.php`)
- Automatically includes the brand loader after database settings
- Calls `outputBrandCSS()` in the `<head>` section
- Injects all brand colors and fonts as CSS custom properties (variables)

#### C. CSS Override System (`public_html/assets/css/brand-dynamic.css`)
- Replaces hardcoded colors in main.css with CSS custom properties
- Overrides Bootstrap color utilities with brand colors
- Provides additional brand-specific CSS classes
- Ensures all UI elements use dynamic colors

### 3. CSS Custom Properties Generated

The system automatically generates CSS custom properties like:

```css
:root {
  /* Brand Colors */
  --brand-primary: #6c2eb6;
  --brand-secondary: #bf5512;
  --brand-tertiary: #8B4513;
  --brand-quaternary: #2E8B57;
  --brand-accent: #28a745;
  --brand-success: #28a745;
  --brand-danger: #dc3545;
  --brand-warning: #ffc107;
  --brand-info: #17a2b8;
  --brand-background: #ffffff;
  --brand-text: #333333;
  --brand-text-light: #666666;
  --brand-text-muted: #999999;
  
  /* Brand Fonts */
  --brand-font-primary: 'Inter, system-ui, sans-serif';
  --brand-font-secondary: 'Roboto, Arial, sans-serif';
  --brand-font-heading: 'Inter, system-ui, sans-serif';
  --brand-font-body: 'Roboto, Arial, sans-serif';
  --brand-font-monospace: 'SF Mono, Monaco, Consolas, monospace';
  
  /* Compatibility Aliases */
  --accent-color: var(--brand-primary);
  --heading-color: var(--brand-secondary);
  --primary-color: var(--brand-primary);
  --secondary-color: var(--brand-secondary);
}
```

## Usage

### For New Company Websites

1. **Update Database Values:**
   - Use the admin interface at `admin/settings/branding_colors_form.php`
   - Or directly update the `setting_branding_colors` table in the database

2. **Colors Apply Immediately:**
   - All CSS using `var(--brand-*)` will update instantly
   - Bootstrap components (buttons, alerts, etc.) use brand colors
   - Custom elements can use brand color classes

### CSS Classes Available

#### Color Classes:
```css
.text-primary          /* Uses --brand-primary */
.text-secondary        /* Uses --brand-secondary */
.bg-primary           /* Uses --brand-primary background */
.bg-secondary         /* Uses --brand-secondary background */
.border-primary       /* Uses --brand-primary border */

/* Brand-specific classes */
.brand-primary        /* --brand-primary text */
.brand-secondary      /* --brand-secondary text */
.bg-brand-primary     /* --brand-primary background */
.bg-brand-secondary   /* --brand-secondary background */
```

#### Font Classes:
```css
.font-primary         /* Uses --brand-font-primary */
.font-secondary       /* Uses --brand-font-secondary */
.font-monospace       /* Uses --brand-font-monospace */
```

### In Your CSS/HTML

**Using CSS Custom Properties:**
```css
.my-element {
    background-color: var(--brand-primary);
    color: var(--brand-background);
    font-family: var(--brand-font-heading);
}
```

**Using Brand Classes:**
```html
<div class="bg-brand-primary text-white font-primary">
    This uses brand colors and fonts
</div>
```

**In PHP Templates:**
```php
<div style="background-color: <?= $brand_primary_color ?>; font-family: <?= $brand_font_heading ?>">
    Content with dynamic brand styling
</div>
```

## Management Interface

### Admin Panel (`admin/settings/branding_colors_form.php`)

Features:
- **Visual Color Pickers:** Easy color selection with live previews
- **Font Management:** Text inputs for font stack definitions
- **Live Preview:** See changes immediately in the interface
- **Color Validation:** Ensures valid hex color formats
- **Font Previews:** See how fonts look with sample text

### Database Management

Direct database updates:
```sql
UPDATE setting_branding_colors 
SET brand_primary_color = '#new-color',
    brand_font_primary = 'New Font, fallback, sans-serif'
WHERE id = 1;
```

## File Locations

### Core Files:
- `public_html/assets/includes/brand_loader.php` - Main brand loading system
- `public_html/assets/includes/doctype.php` - Integration point (includes brand_loader)
- `public_html/assets/css/brand-dynamic.css` - CSS overrides and brand classes

### Management:
- `admin/settings/branding_colors_form.php` - Admin interface for brand management

### Testing:
- `public_html/brand_test.php` - Test page showing all brand colors and fonts in action

## Migration from Hardcoded Colors

The system maintains backward compatibility:

1. **Existing CSS:** Will work as before with fallback values
2. **PHP Variables:** All `$brand_*_color` variables are still available
3. **Gradual Migration:** You can migrate existing hardcoded colors to use CSS custom properties over time

## Benefits

1. **Instant Updates:** Change database values, see immediate results across the entire site
2. **Consistency:** All brand colors come from a single source of truth
3. **Easy Client Management:** Clients can change their brand colors through admin interface
4. **Developer Friendly:** CSS custom properties work with all modern browsers
5. **Flexible:** Supports unlimited custom colors and complete font customization
6. **Maintainable:** No need to hunt through CSS files to update brand colors

## Example Workflow

### Setting Up a New Client:

1. **Access Admin Panel:**
   ```
   Navigate to: admin/settings/branding_colors_form.php
   ```

2. **Update Brand Colors:**
   - Set primary color to client's main brand color
   - Set secondary color to complementary color
   - Configure functional colors (success, error, warning, info)
   - Set background and text colors

3. **Update Brand Fonts:**
   - Set heading font to client's preferred heading font
   - Set body font for content text
   - Configure primary and secondary fonts for special uses

4. **Save Changes:**
   - Click "Save Brand Settings"
   - Changes apply immediately across the entire website

5. **Verify Results:**
   - Visit `brand_test.php` to see all colors and fonts in action
   - Check main website pages to confirm styling

The entire website now reflects the client's brand identity without touching any CSS files!
