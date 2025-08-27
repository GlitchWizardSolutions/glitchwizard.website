<?php
/**
 * Page Settings Mapping
 * Maps each page to its corresponding settings files and structure
 */

return [
    'about.php' => [
        'settings_files' => [
            'public_settings.php' => [
                'path' => '../../assets/includes/settings/public_settings.php',
                'type' => 'php_variable',
                'variable' => 'about_content',
                'structure' => [
                    'title' => ['type' => 'text', 'label' => 'Page Title'],
                    'text' => ['type' => 'textarea', 'label' => 'Main Content'],
                    'side_text' => ['type' => 'summernote', 'label' => 'Side Text']
                ]
            ]
        ],
        'image_settings' => [
            'about' => [
                'fields' => [
                    'path' => ['type' => 'text', 'label' => 'Image Path'],
                    'alt' => ['type' => 'text', 'label' => 'Alt Text']
                ]
            ]
        ],
        'display_name' => 'About Page'
    ],
    
    'contact.php' => [
        'settings_files' => [
            'contact_settings.php' => [
                'path' => '../../assets/includes/settings/contact_settings.php',
                'type' => 'php_variables',
                'variables' => [
                    'contact_content' => [
                        'title' => ['type' => 'text', 'label' => 'Page Title'],
                        'subtitle' => ['type' => 'text', 'label' => 'Subtitle'],
                        'description' => ['type' => 'textarea', 'label' => 'Page Description']
                    ],
                    'contact_info' => [
                        'address' => ['type' => 'textarea', 'label' => 'Address'],
                        'phone' => ['type' => 'text', 'label' => 'Phone Number'],
                        'email' => ['type' => 'email', 'label' => 'Email Address'],
                        'hours' => ['type' => 'textarea', 'label' => 'Business Hours']
                    ]
                ]
            ]
        ],
        'display_name' => 'Contact Page'
    ],
    
    'index.php' => [
        'settings_files' => [
            'home_content_settings.php' => [
                'path' => '../../assets/includes/settings/home_content_settings.php',
                'type' => 'php_variables',
                'variables' => [
                    'home_content' => [
                        'title' => ['type' => 'text', 'label' => 'Homepage Title'],
                        'subtitle' => ['type' => 'text', 'label' => 'Subtitle'],
                        'welcome_message' => ['type' => 'summernote', 'label' => 'Welcome Message'],
                        'featured_content' => ['type' => 'summernote', 'label' => 'Featured Content']
                    ]
                ]
            ]
        ],
        'display_name' => 'Homepage'
    ],
    
    'blog.php' => [
        'settings_files' => [
            'blog_settings.php' => [
                'path' => '../../assets/includes/settings/blog_settings.php',
                'type' => 'php_variables',
                'variables' => [
                    'blog_content' => [
                        'title' => ['type' => 'text', 'label' => 'Blog Page Title'],
                        'description' => ['type' => 'textarea', 'label' => 'Blog Description'],
                        'posts_per_page' => ['type' => 'number', 'label' => 'Posts Per Page']
                    ]
                ]
            ]
        ],
        'display_name' => 'Blog Page'
    ],
    
    'services.php' => [
        // NOTE: Previously used general_content_settings.php (now deprecated/removed)
        // Placeholder mapping retained for future services-specific settings implementation.
        'settings_files' => [
        ],
        'display_name' => 'Services Page'
    ],
    
    'gallery.php' => [
        'settings_files' => [
            'media_content_settings.php' => [
                'path' => '../../assets/includes/settings/media_content_settings.php',
                'type' => 'php_variables',
                'variables' => [
                    'gallery_content' => [
                        'title' => ['type' => 'text', 'label' => 'Gallery Title'],
                        'description' => ['type' => 'textarea', 'label' => 'Gallery Description']
                    ]
                ]
            ]
        ],
        'display_name' => 'Gallery Page'
    ]
];
