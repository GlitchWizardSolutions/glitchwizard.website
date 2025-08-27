# Custom Fonts Directory

This directory is for storing custom font files that can be used in the branding system.

## Supported Font Formats:
- **WOFF2** (Web Open Font Format 2) - Recommended, best compression
- **WOFF** (Web Open Font Format) - Good fallback
- **TTF** (TrueType Font) - Widely supported
- **OTF** (OpenType Font) - Good for complex fonts

## How to Add Custom Fonts:

### 1. Upload Font Files
Place your font files in this directory:
```
/public_html/admin/assets/fonts/custom/
```

### 2. Recommended File Naming:
- Use lowercase and hyphens
- Include weight/style in name
- Examples:
  - `my-brand-font-regular.woff2`
  - `my-brand-font-bold.woff2`
  - `my-brand-font-italic.woff2`

### 3. Add to Database
After uploading files, add entries to the `custom_fonts` table:

```sql
INSERT INTO custom_fonts 
(font_name, font_family, font_file_path, font_format, file_size, is_active) 
VALUES 
('My Brand Font Regular', 'My Brand Font', '/admin/assets/fonts/custom/my-brand-font-regular.woff2', 'woff2', 150000, 1);
```

### 4. Font Family Names
- Use consistent `font_family` names for all weights of the same font
- The `font_name` should be descriptive (include weight/style)
- Example:
  - Font Family: "My Brand Font"
  - Font Names: "My Brand Font Regular", "My Brand Font Bold", "My Brand Font Italic"

## File Size Recommendations:
- Keep font files under 200KB when possible
- WOFF2 format provides the best compression
- Consider loading only the weights/styles you actually use

## Font Loading:
Once added to the database, your custom fonts will appear in the font dropdowns in the branding settings and can be selected like any other font option.
