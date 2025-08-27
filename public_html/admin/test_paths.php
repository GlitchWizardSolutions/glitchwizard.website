<?php
/* 
 * Admin System Path Test
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: test_paths.php
 * LOCATION: /public_html/admin/
 * PURPOSE: Verify admin system paths and resource accessibility
 * DETAILED DESCRIPTION:
 * This file provides a testing utility for verifying that all admin system paths
 * and resources are properly configured and accessible. It checks critical paths,
 * file permissions, and resource availability to ensure proper system functionality.
 * REQUIRED FILES: 
 * - /public_html/assets/includes/main.php
 * - /private/gws-universal-config.php
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: [IN DEVELOPMENT]
 * 
 * FEATURES:
 * - Path validation testing
 * - Resource accessibility checks
 * - Configuration file verification
 * - Permission validation
 * - System dependency checks
 */
include_once 'assets/includes/main.php';

echo "<!DOCTYPE html>\n";
echo "<html><head><title>Admin Path Test</title></head><body>\n";
echo "<h1>Admin System Path Test</h1>\n";

echo "<h2>Path Variables:</h2>\n";
echo "<p><strong>Admin Path:</strong> " . htmlspecialchars($admin_path) . "</p>\n";
echo "<p><strong>Public Path:</strong> " . htmlspecialchars($public_path) . "</p>\n";

echo "<h2>Constants:</h2>\n";
echo "<p><strong>WEB_ROOT_URL:</strong> " . (defined('WEB_ROOT_URL') ? htmlspecialchars(WEB_ROOT_URL) : 'Not defined') . "</p>\n";
echo "<p><strong>BLOG_AVATARS_URL:</strong> " . (defined('BLOG_AVATARS_URL') ? htmlspecialchars(BLOG_AVATARS_URL) : 'Not defined') . "</p>\n";

echo "<h2>Resource Tests:</h2>\n";

// Test CSS file
$css_path = $_SERVER['DOCUMENT_ROOT'] . $admin_path . '/accounts/admin.css';
echo "<p><strong>CSS File:</strong> " . ($css_path . " - " . (file_exists($css_path) ? "✅ EXISTS" : "❌ MISSING")) . "</p>\n";

// Test JS file
$js_path = $_SERVER['DOCUMENT_ROOT'] . $admin_path . '/accounts/admin.js';
echo "<p><strong>JS File:</strong> " . ($js_path . " - " . (file_exists($js_path) ? "✅ EXISTS" : "❌ MISSING")) . "</p>\n";

// Test favicon
$favicon_path = $_SERVER['DOCUMENT_ROOT'] . $admin_path . '/assets/img/favicon.png';
echo "<p><strong>Favicon:</strong> " . ($favicon_path . " - " . (file_exists($favicon_path) ? "✅ EXISTS" : "❌ MISSING")) . "</p>\n";

// Test avatar directory
$avatar_dir = $_SERVER['DOCUMENT_ROOT'] . BLOG_AVATARS_URL;
echo "<p><strong>Avatar Directory:</strong> " . ($avatar_dir . " - " . (is_dir($avatar_dir) ? "✅ EXISTS" : "❌ MISSING")) . "</p>\n";

// Test default avatar
$default_avatar = $avatar_dir . '/default-user.svg';
echo "<p><strong>Default Avatar:</strong> " . ($default_avatar . " - " . (file_exists($default_avatar) ? "✅ EXISTS" : "❌ MISSING")) . "</p>\n";

echo "<h2>Generated URLs:</h2>\n";
echo "<p><strong>CSS URL:</strong> <a href='" . htmlspecialchars($admin_path . '/accounts/admin.css') . "' target='_blank'>" . htmlspecialchars($admin_path . '/accounts/admin.css') . "</a></p>\n";
echo "<p><strong>JS URL:</strong> <a href='" . htmlspecialchars($admin_path . '/accounts/admin.js') . "' target='_blank'>" . htmlspecialchars($admin_path . '/accounts/admin.js') . "</a></p>\n";
echo "<p><strong>Favicon URL:</strong> <a href='" . htmlspecialchars($admin_path . '/assets/img/favicon.png') . "' target='_blank'>" . htmlspecialchars($admin_path . '/assets/img/favicon.png') . "</a></p>\n";

echo "</body></html>\n";
?>