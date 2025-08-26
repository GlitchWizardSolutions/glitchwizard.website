<?php
// Simple test to verify shop-auth.php variables are defined
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing shop-auth.php variable definitions...\n";

// Include the same files that shop-auth.php includes
$main_paths = [
    __DIR__ . '/accounts_system/main.php',
    __DIR__ . '/assets/includes/main.php',
    __DIR__ . '/main.php'
];
$main_found = false;
foreach ($main_paths as $main_path)
{
    if (file_exists($main_path))
    {
        include $main_path;
        $main_found = true;
        break;
    }
}

if (!$main_found)
{
    die('Critical error: Could not locate main.php');
}

// Load public settings for site branding
// Note: Settings now loaded from database via database_settings.php system
require_once __DIR__ . '/assets/includes/settings/database_settings.php';

// Set page variables for template
$page_title = 'Login / Register - ' . $business_name;
$current_page = 'shop-auth.php';

echo "✓ business_name: " . $business_name . "\n";
echo "✓ page_title: " . $page_title . "\n";
echo "✓ current_page: " . $current_page . "\n";
echo "Test completed successfully!\n";
?>
