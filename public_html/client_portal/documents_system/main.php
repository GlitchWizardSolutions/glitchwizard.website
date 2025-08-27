<?php
/**
 * SYSTEM: GWS Universal Hybrid Application - Documents System
 * LOCATION: public_html/documents_system/main.php
 * LOG: Main configuration and template functions for documents system
 * PRODUCTION: [To be updated on deployment]
 */

/**
 * File: main.php
 * Description: Main configuration file for documents system
 * Functions:
 *   - Include unified template system
 *   - Template header/footer functions for documents system
 *   - Authentication and session management
 * Expected Outputs:
 *   - Template functions available globally
 * Related Files:
 *   - ../shared/templates/unified-template.php
 *   - ../shared/templates/template-config.php
 *   - ../../private/gws-universal-config.php
 */

// Include main configuration
require_once '../../private/gws-universal-config.php';

// Include unified template system
require_once '../shared/templates/unified-template.php';
require_once '../shared/templates/template-config.php';

// Template header function - uses unified template system
function template_header($title = 'Document Management')
{
    // Get the current file name for navigation highlighting
    $current_file_name = basename($_SERVER['SCRIPT_NAME']);
    $current_page = '';

    // Map file names to navigation keys
    $page_mapping = [
        'dashboard.php' => 'dashboard',
        'client-documents.php' => 'documents',
        'sign-documents.php' => 'sign',
        'client-sign-documents.php' => 'sign',
        'profile.php' => 'profile'
    ];

    $current_page = isset($page_mapping[$current_file_name]) ? $page_mapping[$current_file_name] : 'dashboard';

    // Get template configuration for documents system
    $config = get_template_config('documents_system');

    // Get user data from session
    $user_data = get_user_data_for_template();

    // Add admin panel link if user is admin
    $navigation = $config['navigation'];
    if (isset($_SESSION['role']) && $_SESSION['role'] == 'Admin')
    {
        $navigation['admin'] = ['url' => '../admin', 'icon' => 'bi-shield-check', 'label' => 'Admin Panel'];
    }

    // Call unified template header
    unified_template_header($title, $current_page, $user_data, $navigation, $config['assets_path']);
}

// Template footer function - uses unified template system
function template_footer()
{
    // Get template configuration for documents system
    $config = get_template_config('documents_system');

    // Call unified template footer
    unified_template_footer($config['assets_path']);
}

// Legacy headerBlock function for backward compatibility
function headerBlock($title = "Document Management System")
{
    template_header($title);
}

// Legacy footerBlock function for backward compatibility
function footerBlock()
{
    template_footer();
}

// Authentication check function
function check_loggedin($redirect_file = 'index.php')
{
    // Check if user is logged in (compatible with both account and legacy session systems)
    if (!isset($_SESSION['loggedin']) && !isset($_SESSION['loggedin']) && !isset($_SESSION['client_id']))
    {
        header('Location: ' . $redirect_file);
        exit;
    }
}

// Get current user ID (compatible with multiple session systems)
function get_current_user_id()
{
    if (isset($_SESSION['id']))
    {
        return $_SESSION['id'];
    } elseif (isset($_SESSION['id']))
    {
        return $_SESSION['id'];
    } elseif (isset($_SESSION['client_id']))
    {
        return $_SESSION['client_id'];
    }
    return 0;
}

// Get current user name (compatible with multiple session systems)
function get_current_user_name()
{
    if (isset($_SESSION['name']))
    {
        return $_SESSION['name'];
    } elseif (isset($_SESSION['name']))
    {
        return $_SESSION['name'];
    } elseif (isset($_SESSION['client_name']))
    {
        return $_SESSION['client_name'];
    }
    return 'User';
}
?>