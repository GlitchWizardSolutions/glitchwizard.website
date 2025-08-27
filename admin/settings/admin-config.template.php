<?php
/**
 * ADMIN CENTER CONFIGURATION TEMPLATE
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: admin-config.template.php
 * PURPOSE: Base configuration template for the admin center
 * 
 * HOW THIS FILE WORKS:
 * 1. This template defines the default structure and values for admin center settings
 * 2. It serves as a reference for the config validator to ensure proper configuration
 * 3. When setting up the admin center, copy this file to admin-config.php
 * 4. Make all customizations in admin-config.php, never modify this template
 * 
 * TEMPLATE USAGE:
 * - Used by config-validator.php to validate admin settings structure
 * - Provides documentation of all available settings
 * - Ensures consistency when creating new admin configurations
 * - Serves as a backup reference for default values
 * 
 * IMPORTANT:
 * - DO NOT modify this template file
 * - DO NOT delete this file as it's needed for validation
 * - Always make changes in admin-config.php instead
 * 
 * Last Updated: 2025-08-07
 */

// Prevent direct access to this file
if (!defined('PROJECT_ROOT')) {
    die('Direct access to this file is not allowed');
}

// Admin Center Settings
$admin_settings = [
    // Access Settings
    'admin_roles' => [
        'super_admin' => ['all'],
        'content_admin' => ['blog', 'documents', 'media'],
        'user_admin' => ['accounts', 'clients'],
        'editor' => ['blog', 'documents']
    ],
    
    // Security Settings
    'require_2fa_for_admin' => true,
    'session_timeout' => 1800, // 30 minutes
    'max_failed_logins' => 3,
    
    // UI Settings
    'items_per_page' => 25,
    'enable_quick_actions' => true,
    'enable_dark_mode' => true,
    'show_help_tooltips' => true,
    
    // Feature Access
    'enabled_modules' => [
        'blog' => true,
        'accounts' => true,
        'documents' => true,
        'client_portal' => true,
        'analytics' => true
    ],
    
    // Logging Settings
    'log_admin_actions' => true,
    'log_user_actions' => true,
    'log_retention_days' => 30,
    
    // Notification Settings
    'notify_on_error' => true,
    'notify_on_user_signup' => true,
    'notification_email' => '',
    
    // Backup Settings
    'enable_auto_backup' => true,
    'backup_frequency' => 'daily', // daily, weekly, monthly
    'backup_retention' => 7 // number of backups to keep
];

// Admin Center Paths
$admin_paths = [
    'logs' => PROJECT_ROOT . '/private/logs/admin',
    'backups' => PROJECT_ROOT . '/private/backups',
    'templates' => PROJECT_ROOT . '/public_html/admin/templates',
    'uploads' => PROJECT_ROOT . '/public_html/admin/uploads'
];

// Return the configuration
return [
    'settings' => $admin_settings,
    'paths' => $admin_paths
];
