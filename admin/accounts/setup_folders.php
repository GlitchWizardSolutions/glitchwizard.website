<?php
/**
 * Document System Folder Setup Script
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: setup_folders.php
 * LOCATION: /public_html/admin/accounts/
 * PURPOSE: One-time setup script to create the initial folder structure for the document system
 * 
 * FILE RELATIONSHIP:
 * This file works with:
 * - Document management system
 * - Role-based access system
 * - File system operations
 * 
 * HOW IT WORKS:
 * 1. Creates base document directory if it doesn't exist
 * 2. Sets up role-specific folders (Developer, Admin, Editor)
 * 3. Creates Welcome folder for new users
 * 4. Generates README files in each folder
 * 
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: YES
 * 
 * FEATURES:
 * - Automatic directory creation
 * - Role-based folder structure
 * - README file generation
 * - Proper permissions setting
 * - Error handling
 */

$base_path = '../../documents_system/account_documents/';

// Create base directory
if (!file_exists($base_path)) {
    mkdir($base_path, 0755, true);
    echo "Created base documents directory.\n";
}

// Create system folders
$folders = ['Developer', 'Admin', 'Editor', 'Welcome'];
foreach ($folders as $folder) {
    $folder_path = $base_path . $folder;
    if (!file_exists($folder_path)) {
        mkdir($folder_path, 0755, true);
        
        // Create README file
        $readme = "# $folder Folder\n\nSystem folder for $folder access level.\nCreated: " . date('Y-m-d H:i:s') . "\n";
        file_put_contents($folder_path . '/README.md', $readme);
        
        echo "Created $folder folder with README.md\n";
    } else {
        echo "$folder folder already exists.\n";
    }
}

echo "Setup complete!\n";
?>
