<?php
/* 
 * Avatar Function Testing Script
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: test_avatar.php
 * LOCATION: /public_html/admin/accounts/
 * PURPOSE: Development tool for testing avatar URL generation and configuration
 * 
 * FILE RELATIONSHIP:
 * This file integrates with:
 * - gws-universal-config.php: System configuration
 * - gws-universal-functions.php: Core functions including getUserAvatar()
 * 
 * HOW IT WORKS:
 * 1. Loads necessary configuration and functions
 * 2. Creates a test account with admin role
 * 3. Tests avatar URL generation
 * 4. Verifies configuration constants
 * 5. Displays results for debugging
 * 
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: NO
 * 
 * FEATURES:
 * - Avatar URL generation testing
 * - Configuration verification
 * - Environment detection
 * - Debug output
 */

include '../../../private/gws-universal-config.php';
include '../../../private/gws-universal-functions.php';

$test_account = ['avatar' => '', 'role' => 'admin'];
echo 'Avatar URL: ' . getUserAvatar($test_account) . "\n";
if (defined('ACCOUNTS_AVATARS_URL')) {
    echo 'ACCOUNTS_AVATARS_URL: ' . ACCOUNTS_AVATARS_URL . "\n";
} else {
    echo 'ACCOUNTS_AVATARS_URL: NOT DEFINED' . "\n";
}
echo 'WEB_ROOT_URL: ' . WEB_ROOT_URL . "\n";
echo 'ENVIRONMENT: ' . ENVIRONMENT . "\n";
?>
