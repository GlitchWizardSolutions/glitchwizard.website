<?php
/* 
 * Admin Access Protection
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: protection.php
 * LOCATION: /public_html/admin/
 * PURPOSE: Central security check for admin panel access
 * DETAILED DESCRIPTION:
 * This file serves as a security gateway for the admin panel, ensuring that
 * only authorized users with appropriate permissions can access administrative
 * functions. It provides centralized access control and security validation
 * for all admin pages.
 * REQUIRED FILES: 
 * - /private/access-control.php
 * - /private/role-definitions.php
 * - /private/role-functions.php
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: [IN DEVELOPMENT]
 * 
 * FEATURES:
 * - Admin access verification
 * - Role-based access control
 * - Security validation
 * - Session protection
 * - Access logging
 */

require_once __DIR__ . '/../../private/access-control.php';

// Check admin access before allowing any admin page to load
check_admin_access();
?>
