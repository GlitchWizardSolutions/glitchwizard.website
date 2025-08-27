<?php
/**
 * Role Definitions and Hierarchy
 * Defines all available roles and their relationships
 */

// Prevent direct access
if (!defined('PROJECT_ROOT')) {
    die('Direct access to this file is not allowed');
}

// Role hierarchy (higher index = more privileges)
return [
    'guest' => [
        'level' => 0,
        'title' => 'Guest',
        'description' => 'Can only view public pages',
        'capabilities' => [
            'view_public_pages' => true,
        ]
    ],
    'subscriber' => [
        'level' => 0,
        'title' => 'Subscriber',
        'description' => 'Same as guest, prepared for future features',
        'capabilities' => [
            'view_public_pages' => true,
        ]
    ],
    'blog_user' => [
        'level' => 1,
        'title' => 'Blog User',
        'description' => 'Can comment on blog posts',
        'capabilities' => [
            'view_public_pages' => true,
            'comment_on_posts' => true,
            'manage_own_comments' => true
        ]
    ],
    'member' => [
        'level' => 2,
        'title' => 'Member',
        'description' => 'Has access to client portal and blog features',
        'capabilities' => [
            'view_public_pages' => true,
            'access_client_portal' => true,
            'comment_on_posts' => true,
            'manage_own_comments' => true,
            'view_member_content' => true
        ]
    ],
    'editor' => [
        'level' => 3,
        'title' => 'Editor',
        'description' => 'Can manage all blog content except settings',
        'capabilities' => [
            'view_public_pages' => true,
            'access_client_portal' => true,
            'comment_on_posts' => true,
            'manage_own_comments' => true,
            'manage_all_comments' => true,
            'manage_posts' => true,
            'manage_categories' => true,
            'manage_tags' => true,
            'view_member_content' => true
        ]
    ],
    'admin' => [
        'level' => 4,
        'title' => 'Administrator',
        'description' => 'Full access except system settings',
        'capabilities' => [
            'view_public_pages' => true,
            'access_client_portal' => true,
            'comment_on_posts' => true,
            'manage_own_comments' => true,
            'manage_all_comments' => true,
            'manage_posts' => true,
            'manage_categories' => true,
            'manage_tags' => true,
            'manage_users' => true,
            'manage_roles' => true,
            'view_member_content' => true,
            'manage_site_content' => true,
            'access_admin_area' => true,
            'manage_blog_settings' => true
        ]
    ],
    'developer' => [
        'level' => 5,
        'title' => 'Developer',
        'description' => 'Complete system access',
        'capabilities' => [
            'view_public_pages' => true,
            'access_client_portal' => true,
            'comment_on_posts' => true,
            'manage_own_comments' => true,
            'manage_all_comments' => true,
            'manage_posts' => true,
            'manage_categories' => true,
            'manage_tags' => true,
            'manage_users' => true,
            'manage_roles' => true,
            'view_member_content' => true,
            'manage_site_content' => true,
            'access_admin_area' => true,
            'manage_blog_settings' => true,
            'manage_system_settings' => true,
            'manage_server_settings' => true,
            'view_system_info' => true,
            'manage_security_settings' => true
        ]
    ]
];
