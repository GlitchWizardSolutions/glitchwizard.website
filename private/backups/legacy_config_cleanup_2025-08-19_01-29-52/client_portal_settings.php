<?php
// ================= CLIENT PORTAL SETTINGS =================
// Edit this file to customize the client portal for each workspace.
// All unique, environment-specific, or branding values should be set here.

$client_portal_settings = array(
    // --- Branding & Identity ---
    'site_name' => 'Glitch Wizard Client Portal',
    'company_name' => 'GlitchWizard Solutions',
    'logo_path' => 'assets/img/logo.png',
    'favicon_path' => 'assets/img/favicon.png',
    'tagline' => 'Your Digital Partner',

    // --- Contact Info ---
    'support_email' => 'support@example.com',
    'support_phone' => '+1-555-123-4567',
    'company_address' => '123 Main St, City, Country',
    'social_links' => [
        'twitter' => 'https://twitter.com/yourcompany',
        'facebook' => 'https://facebook.com/yourcompany',
        'linkedin' => 'https://linkedin.com/company/yourcompany'
    ],

    // --- UI/UX & Localization ---
    'theme_color' => '#4154f1',
    'default_language' => 'en',
    'timezone' => 'America/New_York',
    'date_format' => 'Y-m-d',
    'currency' => 'USD',

    // --- Feature Toggles ---
    'enable_blog' => true,
    'enable_chat' => false,
    'maintenance_mode' => false,

    // --- Paths & Uploads ---
    'upload_dir' => '/uploads/',
    'max_upload_size' => 10485760, // 10 MB

    // --- Security ---
    'session_timeout' => 7200, // seconds
    'rememberme_duration' => 2592000, // seconds
    'password_policy' => [
        'min_length' => 8,
        'require_special' => true
    ],

    // --- Integrations ---
    'google_analytics_id' => '',
    'recaptcha_site_key' => '',

    // --- Email ---
    'mail_from' => 'no_reply@example.com',
    'mail_enabled' => true,
    'mail_name' => 'Glitch Wizard Digital Solutions',
    'notify_admin_email' => 'webmaster@example.com',
    'SMTP' => true,
    'smtp_host' => 'smtp.example.com',
    'smtp_user' => 'no_reply@example.com',
    'smtp_pass' => '',
    'smtp_secure' => 'ssl',
    'smtp_port' => 587,
    'email_verification_required' => true,

    // --- Legal ---
    'privacy_policy_url' => '/privacy-policy.php',
    'terms_url' => '/terms.php',
);
?>