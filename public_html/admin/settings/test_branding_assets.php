<?php
/**
 * Branding Assets Test Script
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: test_branding_assets.php
 * LOCATION: /public_html/admin/settings/
 * PURPOSE: Test the branding assets system functionality
 * 
 * CREATED: 2025-08-17
 * VERSION: 1.0
 */

require_once '../../../private/gws-universal-config.php';
require_once 'branding_assets_manager.php';

try {
    // Test database connection
    $pdo = new PDO($dsn, $username, $password, $pdo_options);
    echo "✓ Database connection successful\n";
    
    // Test BrandingAssetsManager initialization
    $assets_manager = new BrandingAssetsManager($pdo);
    echo "✓ BrandingAssetsManager initialized\n";
    
    // Test getting current assets
    $current_assets = $assets_manager->getCurrentAssets();
    echo "✓ Current assets retrieved: " . count($current_assets) . " records\n";
    
    // Test getting existing logos
    $existing_logos = $assets_manager->getExistingLogos();
    echo "✓ Existing logos retrieved: " . count($existing_logos) . " files\n";
    
    // Check if assets directory exists and is writable
    $assets_dir = $_SERVER['DOCUMENT_ROOT'] . '/gws-universal-hybrid-app/public_html/assets/branding/';
    if (is_dir($assets_dir)) {
        echo "✓ Assets directory exists\n";
        if (is_writable($assets_dir)) {
            echo "✓ Assets directory is writable\n";
        } else {
            echo "⚠ Assets directory is not writable\n";
        }
    } else {
        echo "✗ Assets directory does not exist\n";
    }
    
    // Check database table structure
    $stmt = $pdo->query("DESCRIBE setting_branding_assets");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "✓ Database table has " . count($columns) . " columns\n";
    
    $required_columns = [
        'business_logo_main', 'business_logo_horizontal', 'business_logo_vertical',
        'business_logo_square', 'business_logo_white', 'business_logo_small',
        'favicon_main', 'social_share_default'
    ];
    
    $missing_columns = array_diff($required_columns, $columns);
    if (empty($missing_columns)) {
        echo "✓ All required database columns exist\n";
    } else {
        echo "⚠ Missing columns: " . implode(', ', $missing_columns) . "\n";
    }
    
    echo "\n🎉 Branding Assets System Test Complete!\n";
    echo "You can now access the logo management system in the Database Settings > Branding tab.\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
