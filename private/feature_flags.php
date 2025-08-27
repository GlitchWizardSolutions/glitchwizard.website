<?php
/**
 * Feature Flags / Module Availability
 * PURPOSE: Quickly enable/disable modules for production minimal build without deleting code.
 * Consume in navigation, dashboards, and conditional includes.
 */
return [
    // Core (always on for this release)
    'accounts_system' => true,
    'blog_system' => true,
    'gallery_system' => true,
    'settings_system' => true,
    // Included public/admin base
    'public_site' => true,

    // Deferred Modules (hidden in this production release)
    'shop_system' => false,
    'landing_pages' => false,
    'invoice_system' => false,
    'documents_system' => false,
    'chat_system' => false,
    'review_system' => false,
];
