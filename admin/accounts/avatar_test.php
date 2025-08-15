<?php
/* 
 * Avatar URL Generation Test Script
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: avatar_test.php
 * LOCATION: /public_html/admin/accounts/
 * PURPOSE: Development tool for testing avatar URL generation in different environments
 * 
 * FILE RELATIONSHIP:
 * This file is a standalone test script that:
 * - Mocks configuration constants
 * - Tests getUserAvatar() function
 * - Verifies URL generation logic
 * 
 * HOW IT WORKS:
 * 1. Sets up test environment
 * 2. Mocks necessary constants
 * 3. Implements test version of getUserAvatar()
 * 4. Tests URL generation for different scenarios
 * 5. Validates environment-specific paths
 * 
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: NO
 * 
 * FEATURES:
 * - Development environment testing
 * - Port-aware URL generation
 * - Multiple server configuration testing
 * - Path validation
 */

// Set the environment to dev to test
define('ENVIRONMENT', 'dev');

// Mock the constants that would be defined in config
define('public_path', 'c:\xampp\htdocs\gws-universal-hybrid-app\public_html');

// Include the function
function getUserAvatar($account)
{
    // Environment-aware base URL construction
    if (ENVIRONMENT === 'dev')
    {
        // Check if we're running on port 3000 (dev server) or default XAMPP
        $current_host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        if (strpos($current_host, ':3000') !== false)
        {
            // Dev server on port 3000 - DOES include /public_html prefix
            $base_url = 'http://localhost:3000';
            $avatar_url_path = $base_url . '/public_html/accounts_system/assets/uploads/avatars';
        } else
        {
            // Local XAMPP - don't include /public_html prefix for localhost
            $base_url = 'http://localhost';
            $avatar_url_path = '/gws-universal-hybrid-app/public_html/accounts_system/assets/uploads/avatars';
        }
    } else
    {
        // Production - use ACCOUNTS_AVATARS_URL
        $avatar_url_path = 'ACCOUNTS_AVATARS_URL_PLACEHOLDER';
    }

    // Check if user has a custom avatar
    if (!empty($account['avatar']))
    {
        $avatar_file_path = public_path . '/accounts_system/assets/uploads/avatars/' . $account['avatar'];
        if (file_exists($avatar_file_path))
        {
            // For dev environment, use constructed URL; for production, use the URL path directly
            if (ENVIRONMENT === 'dev')
            {
                return $avatar_url_path . '/' . $account['avatar'];
            } else
            {
                return 'ACCOUNTS_AVATARS_URL' . '/' . $account['avatar'];
            }
        }
    }

    // Role-based default avatars
    $default_avatar = '';
    switch (strtolower($account['role']))
    {
        case 'developer':
            $default_avatar = 'default-developer.svg';
            break;
        case 'admin':
            $default_avatar = 'default-admin.svg';
            break;
        case 'editor':
            $default_avatar = 'default-editor.svg';
            break;
        case 'blog_only':
        case 'blog user':
            $default_avatar = 'default-blog.svg';
            break;
        case 'member':
            $default_avatar = 'default-member.svg';
            break;
        case 'guest':
            $default_avatar = 'default-guest.svg';
            break;
        case 'demo':
            $default_avatar = 'default-demo.svg';
            break;
        default:
            $default_avatar = 'default-user.svg';
    }

    // Return default avatar with proper URL construction
    if (ENVIRONMENT === 'dev')
    {
        return $avatar_url_path . '/' . $default_avatar;
    } else
    {
        return 'ACCOUNTS_AVATARS_URL' . '/' . $default_avatar;
    }
}

// Test the function
$test_account = ['avatar' => '', 'role' => 'admin'];
echo 'Generated Avatar URL: ' . getUserAvatar($test_account) . "\n";

// Test with custom avatar
$test_account_custom = ['avatar' => 'avatar.png', 'role' => 'admin'];
echo 'Custom Avatar URL: ' . getUserAvatar($test_account_custom) . "\n";

echo 'File exists check for avatar.png: ';
$file_path = 'c:\xampp\htdocs\gws-universal-hybrid-app\public_html\accounts_system\assets\uploads\avatars\avatar.png';
echo file_exists($file_path) ? 'YES' : 'NO';
echo "\n";
?>
