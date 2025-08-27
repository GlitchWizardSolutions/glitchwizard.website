#!/bin/bash
# Complete deployment script
# Run this after both repositories are cloned

echo "=== GWS Universal Hybrid App Deployment ==="
echo "Running complete deployment..."

# Deploy public files
echo "Step 1: Deploying public_html files..."
/home/gw/deploy_public.sh

echo ""

# Deploy private files  
echo "Step 2: Deploying private files..."
/home/gw/deploy_private.sh

echo ""
echo "=== Deployment Complete! ==="
echo ""
echo "Next Steps:"
echo "1. Edit /home/gw/private/gws-universal-config.php"
echo "2. Update database credentials"
echo "3. Set SITE_URL to your domain"
echo "4. Import database.sql to your MySQL database"
echo "5. Test your site!"
echo ""
echo "Database file location: /home/gw/public_html/FULL DATABASE SQL/database.sql"
