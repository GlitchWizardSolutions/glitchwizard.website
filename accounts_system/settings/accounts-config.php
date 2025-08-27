<?php

// Prevent direct access
if (!defined('PROJECT_ROOT')) {
    die('Direct access to this file is not allowed');
}

// accounts system settings
return array (
  'registration_enabled' => 
  array (
    'value' => true,
    'description' => 'Allow new users to register accounts',
  ),
  'email_verification_required' => 
  array (
    'value' => true,
    'description' => 'Users must verify their email before accessing their account',
  ),
  'min_password_length' => 
  array (
    'value' => 8,
    'description' => 'Minimum number of characters for passwords',
  ),
  'password_complexity_required' => 
  array (
    'value' => true,
    'description' => 'Require complex passwords with special chars, numbers, etc',
  ),
  'max_login_attempts' => 
  array (
    'value' => 5,
    'description' => 'Number of failed logins before account lockout',
  ),
  'login_lockout_duration' => 
  array (
    'value' => 900,
    'description' => 'Lockout duration in seconds after failed attempts',
  ),
  'session_timeout' => 
  array (
    'value' => 7200,
    'description' => 'Session timeout in seconds',
  ),
  'rememberme_duration' => 
  array (
    'value' => 2592000,
    'description' => 'Remember me cookie duration in seconds',
  ),
  'password_reset_token_expiry' => 
  array (
    'value' => 3600,
    'description' => 'Password reset token validity in seconds',
  ),
  'default_role' => 
  array (
    'value' => 'Member',
    'description' => 'Default role for new users',
  ),
  'avatar_upload_enabled' => 
  array (
    'value' => true,
    'description' => 'Allow users to upload profile pictures',
  ),
  'avatar_max_size' => 
  array (
    'value' => 2097152,
    'description' => 'Maximum avatar file size in bytes',
  ),
  'profile_fields_required' => 
  array (
    'value' => 'username,email',
    'description' => 'Required profile fields',
  ),
  'profile_picture_types' => 
  array (
    'value' => 'jpg,jpeg,png,gif',
    'description' => 'Allowed image file types',
  ),
  'notification_email_welcome' => 
  array (
    'value' => true,
    'description' => 'Send welcome email to new users',
  ),
  'notification_email_password_reset' => 
  array (
    'value' => true,
    'description' => 'Send password reset notification emails',
  ),
  'notification_email_login_alerts' => 
  array (
    'value' => false,
    'description' => 'Send email alerts for new logins',
  ),
  'two_factor_auth_enabled' => 
  array (
    'value' => false,
    'description' => 'Enable two-factor authentication',
  ),
  'social_login_enabled' => 
  array (
    'value' => false,
    'description' => 'Allow social media login',
  ),
  'username_min_length' => 
  array (
    'value' => 3,
    'description' => 'Minimum username length',
  ),
  'username_max_length' => 
  array (
    'value' => 50,
    'description' => 'Maximum username length',
  ),
  'auto_cleanup_inactive_days' => 
  array (
    'value' => 365,
    'description' => 'Days before cleaning up inactive accounts',
  ),
  'gdpr_compliance_enabled' => 
  array (
    'value' => true,
    'description' => 'Enable GDPR compliance features',
  ),
  'audit_log_enabled' => 
  array (
    'value' => true,
    'description' => 'Enable audit logging',
  ),
  'api_access_enabled' => 
  array (
    'value' => false,
    'description' => 'Enable API access',
  ),
);
