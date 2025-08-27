<?php
/**
 * CLIENT PORTAL CONFIGURATION
 * Template file for client portal settings
 * 
 * This file serves as a template for the client portal configuration.
 * Do not modify this template. Instead, copy it to client-config.php
 * and make changes there.
 */

// Prevent direct access to this file
if (!defined('PROJECT_ROOT')) {
    die('Direct access to this file is not allowed');
}

// Client Portal Settings
$portal_settings = [
    // Access Settings
    'require_login' => true,
    'allow_client_registration' => false,
    'allow_document_upload' => true,
    'allow_document_download' => true,
    
    // Document Settings
    'max_upload_size' => '10MB',
    'allowed_file_types' => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
    'max_files_per_upload' => 5,
    
    // Notification Settings
    'notify_on_upload' => true,
    'notify_on_comment' => true,
    'notification_email' => '',
    
    // Display Settings
    'items_per_page' => 20,
    'show_file_size' => true,
    'show_upload_date' => true,
    'show_download_count' => true,
    
    // Feature Toggles
    'enable_comments' => true,
    'enable_sharing' => false,
    'enable_versioning' => true,
    
    // Integration Settings
    'enable_documents_system' => true,
    'enable_invoicing' => true,
    'enable_messaging' => true
];

// Portal Paths Configuration
$portal_paths = [
    'uploads' => PROJECT_ROOT . '/public_html/client_portal/assets/uploads',
    'templates' => PROJECT_ROOT . '/public_html/client_portal/templates',
    'temp' => PROJECT_ROOT . '/public_html/client_portal/temp'
];

// Return the configuration
return [
    'settings' => $portal_settings,
    'paths' => $portal_paths
];
