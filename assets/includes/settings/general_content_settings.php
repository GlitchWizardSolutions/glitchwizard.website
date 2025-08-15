<?php
// Content Settings - general
// Last updated: 2025-08-07 09:44:53
// Migrated from content-vars.php and content-placement.php

$content_settings = array (
    'site' => array(
        'name' => array(
            'value' => isset($sitename) ? $sitename : 'Your Site Name',
            'description' => 'The name of your site that appears in the header and browser title',
            'type' => 'text',
            'required' => true
        )
    ),
    'branding' => array(
        'tagline' => array(
            'value' => isset($tagline) ? $tagline : 'Your Site Tagline',
            'description' => 'The main tagline that appears below the hero heading',
            'type' => 'text',
            'required' => true
        )
    )
);
