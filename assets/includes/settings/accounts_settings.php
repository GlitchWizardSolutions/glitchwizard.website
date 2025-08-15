<?php
/**
 * General Account Settings Storage
 * 
 * SYSTEM: GWS Universal Hybrid App
 * FILE: accounts_settings.php
 * LOCATION: /public_html/assets/includes/settings/
 * PURPOSE: Stores general account settings data
 * 
 * FILE RELATIONSHIP:
 * This file stores the actual configuration values that are managed through
 * the main account_settings.php interface. While account_settings.php provides
 * the admin interface, this file (accounts_settings.php) is the persistent
 * storage for general account settings like SMTP and notification configurations.
 * 
 * HOW IT WORKS:
 * 1. This file is loaded by account_settings.php when displaying settings
 * 2. Contains only the data structure ($account_settings array)
 * 3. Is automatically updated when settings are saved through the admin interface
 * 4. Should not be edited directly - use the admin interface instead
 * 
 * Last updated: 2025-08-07 16:02:15
 */

$account_settings = array (
  'notification_email_password_reset' => true,
  'smtp_host' => '',
  'smtp_port' => 0,
  'smtp_user' => '',
  'smtp_pass' => '',
  'smtp_secure' => 'none',
  'mail_from_name' => '',
  'mail_from_email' => '',
);
