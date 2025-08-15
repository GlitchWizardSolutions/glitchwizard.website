<?php
// Content Settings - home
// Last updated: 2025-08-07 09:44:53
// Migrated from content-vars.php and content-placement.php

$content_settings = array (
    'hero' => array(
        'heading' => array(
            'value' => isset($site_topic) ? $site_topic : 'Welcome to Our Site',
            'description' => 'The main heading in the hero section',
            'type' => 'text',
            'required' => true
        ),
        'background_image' => array(
            'value' => isset($hero_bg_image) ? $hero_bg_image : 'assets/img/hero-bg-abstract.jpg',
            'description' => 'Background image for the hero section (optimal: 1920x480px)',
            'type' => 'image',
            'required' => true
        )
    ),
    'cta' => array(
        'button_text' => array(
            'value' => isset($cta_button_text) ? $cta_button_text : 'Get Started',
            'description' => 'Text for the main call-to-action button',
            'type' => 'text',
            'required' => true
        )
    )
);
