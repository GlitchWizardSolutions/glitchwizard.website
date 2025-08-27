# Business Branding Assets

This directory contains customizable branding assets for your business installation.

## Required Files:

- `admin_logo.png` - Logo for admin interface (recommended: 40x40px, transparent background)
- `main_logo.png` - Main logo for public pages (flexible size)
- `favicon.ico` - Website favicon (16x16px)
- `secondary_logo.png` - Smaller/alternate logo for specific uses

## Setup Instructions:

1. Upload your business logos to this directory
2. Rename them to match the expected filenames above
3. Update the branding constants in `/private/gws-universal-config.php`:
   - `BUSINESS_NAME` - Your business name
   - `BUSINESS_SHORT_NAME` - Short version/acronym
   - `BRAND_PRIMARY_COLOR` - Main brand color (hex)
   - `BRAND_SECONDARY_COLOR` - Secondary brand color (hex)
   - `BRAND_ACCENT_COLOR` - Accent color for highlights (hex)

## Brand Colors:

The system uses CSS custom properties that can be overridden:

- `--brand-primary`
- `--brand-secondary`
- `--brand-accent`
- `--brand-gradient`

## File Formats:

- Logos: PNG (recommended for transparency)
- Favicon: ICO or PNG
- All images should be optimized for web use

## Fallbacks:

If custom assets are not found, the system will use default placeholders until they are uploaded.
