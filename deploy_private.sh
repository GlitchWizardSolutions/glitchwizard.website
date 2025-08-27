#!/bin/bash
# Deploy script for private files
# Use this script on your cPanel server

echo "Deploying private files..."

# Navigate to the private repository clone
cd /home/gw/private_repo/

# Pull latest changes
git pull origin main

# Copy private files to actual private directory
echo "Copying files to private..."
mkdir -p /home/gw/private/
cp -r private/* /home/gw/private/

# Set up config file if it doesn't exist
if [ ! -f "/home/gw/private/gws-universal-config.php" ]; then
    echo "Creating config file from template..."
    cp /home/gw/private/gws-universal-config-template.php /home/gw/private/gws-universal-config.php
    echo "IMPORTANT: Edit /home/gw/private/gws-universal-config.php with your database credentials!"
fi

# Set proper permissions
echo "Setting permissions..."
find /home/gw/private/ -type d -exec chmod 755 {} \;
find /home/gw/private/ -type f -exec chmod 644 {} \;

# Make error log directory writable
chmod 755 /home/gw/private/errors_and_observations/

echo "Private files deployment complete!"
echo "Don't forget to update your database credentials in gws-universal-config.php"
