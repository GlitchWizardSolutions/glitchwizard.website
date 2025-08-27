# GWS Universal Hybrid App

A comprehensive web application with advanced branding, theming, and content management capabilities.

## Quick Setup

### 1. Database Configuration

1. Copy the config template:
   ```bash
   cp private/gws-universal-config-template.php private/gws-universal-config.php
   ```

2. Edit `private/gws-universal-config.php` and update:
   - Database credentials (host, username, password, database name)
   - Site URL for your environment
   - Admin email address

### 2. Environment Setup

- **Development**: Set `ENVIRONMENT` to `'development'` in the config file
- **Production**: Set `ENVIRONMENT` to `'production'` in the config file

### 3. Security Notes

- The `private/` folder contains sensitive configuration files
- Never commit `private/gws-universal-config.php` to version control
- Only the template file is tracked by Git
- Update database credentials for your environment

## Features

- **Visual Themes System**: 5 professional themes with database-driven activation
- **Typography Management**: Custom font upload system with 5 categorized slots
- **Brand Colors**: 6-color branding system with real-time preview
- **Content Management**: Comprehensive CMS with blog, portfolio, and shop systems
- **Admin Panel**: Full-featured administration interface
- **Responsive Design**: Bootstrap 5.3.3 based responsive layouts

## Directory Structure

```
gws-universal-hybrid-app/
├── private/                    # Private configuration & functions (not web accessible)
│   ├── gws-universal-config-template.php  # Configuration template
│   └── gws-universal-config.php          # Your actual config (create from template)
├── public_html/               # Web accessible files
│   ├── admin/                # Administration interface
│   ├── assets/               # CSS, JS, images, fonts
│   ├── index.php            # Main website entry point
│   └── ...
└── README.md                # This file
```

## Font System

Custom fonts are stored in `public_html/assets/fonts/custom/` and are production-ready for hosting environments.

## Development vs Production

The application automatically adjusts error reporting and other settings based on the `ENVIRONMENT` constant in your config file.

---

**Note**: This application is designed for professional hosting environments where only the `public_html` folder is web-accessible.
