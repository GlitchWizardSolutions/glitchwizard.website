<?php
/**
 * ACCOUNTS SYSTEM CONFIGURATION
 * Template file for accounts system settings
 * 
 * This file serves as a template for the accounts system configuration.
 * Do not modify this template. Instead, copy it to accounts-config.php
 * and make changes there.
 */

// Prevent direct access to this file
if (!defined('PROJECT_ROOT')) {
    die('Direct access to this file is not allowed');
}

// Accounts System Settings
$accounts_settings = [
    // Registration Settings
    'registration_enabled' => [
        'value' => true,
        'description' => 'Allow new users to register accounts'
    ],
    'email_verification_required' => [
        'value' => true, 
        'description' => 'Users must verify their email before accessing their account'
    ],
    'min_password_length' => [
        'value' => 8,
        'description' => 'Minimum number of characters for passwords'
    ],
    'password_complexity_required' => [
        'value' => true,
        'description' => 'Require complex passwords with special chars, numbers, etc'
    ],
    
    // Login Security 
    'max_login_attempts' => [
        'value' => 5,
        'description' => 'Number of failed logins before account lockout'
    ],
    'login_lockout_duration' => [
        'value' => 900,
        'description' => 'Lockout duration in seconds after failed attempts'
    ],
    'session_timeout' => [
        'value' => 7200,
        'description' => 'Session timeout in seconds'  
    ],
    'rememberme_duration' => [
        'value' => 2592000,
        'description' => 'Remember me cookie duration in seconds'
    ],
    'password_reset_token_expiry' => [
        'value' => 3600,
        'description' => 'Password reset token validity in seconds'
    ],
    
    // Profile Settings
    'default_role' => [
        'value' => 'Member',
        'description' => 'Default role for new users'
    ],
    'avatar_upload_enabled' => [
        'value' => true,
        'description' => 'Allow users to upload profile pictures'
    ],
    'avatar_max_size' => [
        'value' => 2097152,
        'description' => 'Maximum avatar file size in bytes'
    ],
    'profile_fields_required' => [
        'value' => 'username,email',
        'description' => 'Required profile fields'
    ],
    'profile_picture_types' => [
        'value' => 'jpg,jpeg,png,gif',
        'description' => 'Allowed image file types'
    ],

    // Email Notifications
    'notification_email_welcome' => [
        'value' => true,
        'description' => 'Send welcome email to new users'
    ],
    'notification_email_password_reset' => [
        'value' => true,
        'description' => 'Send password reset notification emails'
    ],
    'notification_email_login_alerts' => [
        'value' => false,
        'description' => 'Send email alerts for new logins'
    ],

    // Advanced Features
    'two_factor_auth_enabled' => [
        'value' => false,
        'description' => 'Enable two-factor authentication'
    ],
    'social_login_enabled' => [
        'value' => false,
        'description' => 'Allow social media login'
    ],
    'username_min_length' => [
        'value' => 3,
        'description' => 'Minimum username length'
    ],
    'username_max_length' => [
        'value' => 50,
        'description' => 'Maximum username length'
    ],
    'auto_cleanup_inactive_days' => [
        'value' => 365,
        'description' => 'Days before cleaning up inactive accounts'
    ],

    // Compliance and Security
    'gdpr_compliance_enabled' => [
        'value' => true,
        'description' => 'Enable GDPR compliance features'
    ],
    'audit_log_enabled' => [
        'value' => true,
        'description' => 'Enable audit logging'
    ],
    'api_access_enabled' => [
        'value' => false,
        'description' => 'Enable API access'
    ]
];

// Return the configuration array
return $accounts_settings;
