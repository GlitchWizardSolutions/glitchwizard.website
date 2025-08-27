<?php
/**
 * Role Management Functions
 * Provides comprehensive role checking and capability verification
 */

// Global role cache
global $ROLE_CACHE;

/**
 * Get all defined roles
 * @return array Role definitions
 */
function getRoles() {
    global $ROLE_CACHE;
    if ($ROLE_CACHE === null) {
        $ROLE_CACHE = include PROJECT_ROOT . '/private/role-definitions.php';
    }
    return $ROLE_CACHE;
}

/**
 * Get a specific role's definition
 * @param string $role Role name
 * @return array|null Role definition or null if not found
 */
function getRole($role) {
    $roles = getRoles();
    return $roles[strtolower($role)] ?? null;
}

/**
 * Get role level
 * @param string $role Role name
 * @return int Role level (higher = more privileges)
 */
function getRoleLevel($role) {
    $roleData = getRole($role);
    return $roleData ? $roleData['level'] : -1;
}

/**
 * Check if a role has a specific capability
 * @param string $role Role name
 * @param string $capability Capability to check
 * @return bool True if role has capability
 */
function roleHasCapability($role, $capability) {
    $roleData = getRole($role);
    return $roleData ? ($roleData['capabilities'][$capability] ?? false) : false;
}

/**
 * Check if current user has a specific capability
 * @param string $capability Capability to check
 * @return bool True if user has capability
 */
function current_user_can($capability) {
    $userRole = get_user_role();
    return roleHasCapability($userRole, $capability);
}

/**
 * Check if one role is higher than another
 * @param string $role1 First role
 * @param string $role2 Second role
 * @return bool True if role1 is higher than role2
 */
function isRoleHigher($role1, $role2) {
    return getRoleLevel($role1) > getRoleLevel($role2);
}

/**
 * Check if current user has equal or higher role than specified
 * @param string $requiredRole Required role level
 * @return bool True if user's role is equal or higher
 */
function currentUserHasMinimumRole($requiredRole) {
    $userRole = get_user_role();
    return getRoleLevel($userRole) >= getRoleLevel($requiredRole);
}

/**
 * Get list of all capabilities for a role
 * @param string $role Role name
 * @return array List of capabilities
 */
function getRoleCapabilities($role) {
    $roleData = getRole($role);
    return $roleData ? $roleData['capabilities'] : [];
}

/**
 * Check if a role meets or exceeds another role's level
 * @param string $userRole The role to check
 * @param string $requiredRole The minimum required role level
 * @return bool True if userRole meets or exceeds requiredRole's level
 */
function hasMinimumRole($userRole, $requiredRole) {
    return getRoleLevel($userRole) >= getRoleLevel($requiredRole);
}

/**
 * Verify if user can access a specific admin page
 * @param string $page Admin page identifier
 * @return bool True if access is allowed
 */
function canAccessAdminPage($page) {
    $userRole = get_user_role();
    
    // Define page access requirements
    $pageAccess = [
        'system_settings' => ['developer'],
        'security_settings' => ['developer'],
        'blog_settings' => ['developer', 'admin'],
        'user_management' => ['developer', 'admin'],
        'content_management' => ['developer', 'admin', 'editor'],
        'blog_management' => ['developer', 'admin', 'editor']
    ];
    
    // Get required roles for the page
    $requiredRoles = $pageAccess[$page] ?? ['developer']; // Default to developer only
    
    // Check if user's role is in the allowed roles
    foreach ($requiredRoles as $allowedRole) {
        if (currentUserHasMinimumRole($allowedRole)) {
            return true;
        }
    }
    
    return false;
}
