<?php
/**
 * Access control check for protected directories
 */

// Include core files
require_once __DIR__ . '/gws-universal-config.php';
require_once __DIR__ . '/gws-universal-functions.php';
require_once __DIR__ . '/role-functions.php';

/**
 * Check admin directory access
 * Only admin and developer roles allowed
 */
function check_admin_access() {
    // Ensure user is logged in and has admin/developer role
    if (!isset($_SESSION['loggedin']) || !has_role(['admin', 'developer'])) {
        header('Location: ' . WEB_ROOT_URL . '/auth.php?error=' . urlencode('You must be an administrator to access this area.'));
        exit;
    }
}

/**
 * Check client portal access
 * Member and above roles allowed
 */
function check_client_portal_access() {
    // Ensure user is logged in and has member or higher role
    if (!isset($_SESSION['loggedin']) || !has_role('member')) {
        header('Location: ' . WEB_ROOT_URL . '/auth.php?error=' . urlencode('You must be a member to access this area.'));
        exit;
    }
}
