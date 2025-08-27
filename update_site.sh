#!/bin/bash
# update_site.sh - Simple deployment script for cPanel
# Place this file in /home/gw/ and run when you need to update

echo "Updating GWS Universal Hybrid App..."

# Navigate to repository
cd /home/gw/repo/

# Pull latest changes
git pull origin main

# Copy public_html files
echo "Updating public_html..."
cp -r public_html/* /home/gw/public_html/

# Copy private files (but don't overwrite config)
echo "Updating private files..."
mkdir -p /home/gw/private/

# Copy all private files except config.php
find private/ -name "*.php" ! -name "gws-universal-config.php" -exec cp {} /home/gw/private/ \;
find private/ -name "*.md" -exec cp {} /home/gw/private/ \;
find private/ -name "*.css" -exec cp {} /home/gw/private/ \;
find private/ -name "*.zip" -exec cp {} /home/gw/private/ \;

# Copy subdirectories
cp -r private/admin_safe/ /home/gw/private/ 2>/dev/null || true
cp -r private/ai_content/ /home/gw/private/ 2>/dev/null || true
cp -r private/archived_blog_system/ /home/gw/private/ 2>/dev/null || true
cp -r private/classes/ /home/gw/private/ 2>/dev/null || true
cp -r private/developer/ /home/gw/private/ 2>/dev/null || true
cp -r private/errors_and_observations/ /home/gw/private/ 2>/dev/null || true

# Set up config file if it doesn't exist
if [ ! -f "/home/gw/private/gws-universal-config.php" ]; then
    echo "Creating config file from template..."
    cp /home/gw/private/gws-universal-config-template.php /home/gw/private/gws-universal-config.php
    echo "IMPORTANT: Edit gws-universal-config.php with your database credentials!"
fi

# Set permissions
chmod 755 /home/gw/public_html/assets/fonts/custom/
chmod 755 /home/gw/public_html/assets/img/
chmod 755 /home/gw/public_html/assets/branding/

echo "Update complete!"
echo "Database SQL file: /home/gw/public_html/FULL DATABASE SQL/database.sql"
