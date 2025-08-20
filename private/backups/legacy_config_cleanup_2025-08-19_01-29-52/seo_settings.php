 <?php
// seo_settings.php
// Centralized SEO settings for each page in the client portal
// This file is intended to be included in doctype.php and edited via admin tools later.

// Example structure: array indexed by page name (without ".php")
$seo_settings = [
    // Dashboard page
    'dashboard' => [
        'title' => 'Dashboard - NiceAdmin Bootstrap Template',
        'description' => 'Your personalized dashboard overview and quick access to all features.',
        'keywords' => 'dashboard, overview, admin, portal',
    ],
    // Profile page
    'profile' => [
        'title' => 'Profile - NiceAdmin',
        'description' => 'View and edit your user profile, account settings, and preferences.',
        'keywords' => 'profile, user, account, settings',
    ],
    // FAQ page
    'faq' => [
        'title' => 'Frequently Asked Questions - NiceAdmin',
        'description' => 'Answers to common questions about the client portal and its features.',
        'keywords' => 'faq, questions, help, support',
    ],
    // Contact page
    'contact' => [
        'title' => 'Contact Us - NiceAdmin',
        'description' => 'Get in touch with our support team for assistance or inquiries.',
        'keywords' => 'contact, support, help, inquiry',
    ],
    // Default/fallback
    'default' => [
        'title' => 'Client Portal - NiceAdmin',
        'description' => 'Welcome to the client portal. Access your dashboard, profile, and more.',
        'keywords' => 'client portal, dashboard, admin, user',
    ],
];
