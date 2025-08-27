#!/bin/bash
# Deploy script for public_html files
# Use this script on your cPanel server

echo "Deploying public_html files..."

# Navigate to the public_html repository clone
cd /home/gw/public_html_repo/

# Pull latest changes
git pull origin main

# Copy public_html contents to actual public_html directory
echo "Copying files to public_html..."
cp -r public_html/* /home/gw/public_html/

# Set proper permissions
echo "Setting permissions..."
find /home/gw/public_html/ -type d -exec chmod 755 {} \;
find /home/gw/public_html/ -type f -exec chmod 644 {} \;

# Make upload directories writable
chmod 755 /home/gw/public_html/assets/fonts/custom/
chmod 755 /home/gw/public_html/assets/img/
chmod 755 /home/gw/public_html/assets/branding/

echo "Public HTML deployment complete!"
