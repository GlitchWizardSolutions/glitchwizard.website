<?php
/**
 * Page Settings Configuration
 * Defines which settings each page type should use
 * This allows for modular, maintainable settings that won't break if pages don't exist
 */

return [
    // Homepage settings
    'index.php' => [
        'title' => 'Homepage Settings',
        'description' => 'Configure homepage content, hero section, and featured elements',
        'sections' => [
            'site' => [
                'title' => 'Site Information',
                'fields' => ['name', 'tagline', 'description', 'logo', 'favicon']
            ],
            'homepage' => [
                'title' => 'Homepage Content',
                'fields' => ['welcome_message', 'featured_content', 'call_to_action']
            ],
            'hero' => [
                'title' => 'Hero Section',
                'fields' => ['title', 'subtitle', 'background_image', 'button_text', 'button_link']
            ],
            'features' => [
                'title' => 'Features Section',
                'fields' => ['feature_1', 'feature_2', 'feature_3', 'features_title']
            ]
        ],
        'priority' => 1 // High priority page
    ],

    // Contact page settings
    'contact.php' => [
        'title' => 'Contact Page Settings',
        'description' => 'Configure contact information, forms, and business details',
        'sections' => [
            'site' => [
                'title' => 'Site Information',
                'fields' => ['name', 'email', 'phone']
            ],
            'contact' => [
                'title' => 'Contact Information',
                'fields' => ['address', 'phone', 'email', 'hours', 'map_embed', 'form_title']
            ]
        ],
        'priority' => 2
    ],

    // Blog settings
    'blog.php' => [
        'title' => 'Blog Listing Page',
        'description' => 'Configure blog listing page and blog-wide settings',
        'sections' => [
            'site' => [
                'title' => 'Site Information',
                'fields' => ['name']
            ],
            'blog' => [
                'title' => 'Blog Settings',
                'fields' => ['title', 'description', 'posts_per_page', 'featured_image']
            ]
        ],
        'priority' => 3
    ],

    'post.php' => [
        'title' => 'Individual Blog Posts',
        'description' => 'Configure individual blog post display and features',
        'sections' => [
            'site' => [
                'title' => 'Site Information',
                'fields' => ['name']
            ],
            'blog' => [
                'title' => 'Blog Settings',
                'fields' => ['show_author', 'show_date', 'show_tags', 'related_posts']
            ]
        ],
        'priority' => 4
    ],

    // Shop settings
    'shop/index.php' => [
        'title' => 'Shop Homepage',
        'description' => 'Configure shop homepage and product listings',
        'sections' => [
            'site' => [
                'title' => 'Site Information',
                'fields' => ['name']
            ],
            'shop' => [
                'title' => 'Shop Settings',
                'fields' => ['title', 'description', 'featured_products', 'categories_display']
            ]
        ],
        'priority' => 2
    ],

    // Gallery settings
    'gallery.php' => [
        'title' => 'Image Gallery',
        'description' => 'Configure image gallery display and organization',
        'sections' => [
            'site' => [
                'title' => 'Site Information',
                'fields' => ['name']
            ],
            'gallery' => [
                'title' => 'Gallery Settings',
                'fields' => ['title', 'description', 'images_per_page', 'thumbnail_size']
            ]
        ],
        'priority' => 5
    ],

    // Legal pages
    'policy-privacy.php' => [
        'title' => 'Privacy Policy',
        'description' => 'Configure privacy policy content and legal information',
        'sections' => [
            'site' => [
                'title' => 'Site Information',
                'fields' => ['name', 'email']
            ],
            'legal' => [
                'title' => 'Legal Information',
                'fields' => ['privacy_policy', 'data_collection', 'contact_info']
            ]
        ],
        'priority' => 6
    ],

    'policy-terms.php' => [
        'title' => 'Terms of Service',
        'description' => 'Configure terms of service and user agreements',
        'sections' => [
            'site' => [
                'title' => 'Site Information',
                'fields' => ['name', 'email']
            ],
            'legal' => [
                'title' => 'Legal Information',
                'fields' => ['terms_of_service', 'user_obligations', 'limitation_liability']
            ]
        ],
        'priority' => 7
    ],

    'policy-accessibility.php' => [
        'title' => 'Accessibility Policy',
        'description' => 'Configure accessibility statement and compliance information',
        'sections' => [
            'site' => [
                'title' => 'Site Information',
                'fields' => ['name', 'email']
            ],
            'legal' => [
                'title' => 'Legal Information',
                'fields' => ['accessibility_statement', 'compliance_level', 'contact_info']
            ]
        ],
        'priority' => 8
    ],

    // Additional common pages that might exist
    'about.php' => [
        'title' => 'About Page Settings',
        'description' => 'Configure about page content and information',
        'sections' => [
            'about_content' => [
                'title' => 'About Content',
                'fields' => ['title', 'text', 'side_text']
            ],
            'about_image' => [
                'title' => 'About Image',
                'fields' => ['path', 'alt']
            ]
        ],
        'priority' => 3
    ],

    'services.php' => [
        'title' => 'Services Page',
        'description' => 'Configure services offered and pricing information',
        'sections' => [
            'site' => [
                'title' => 'Site Information',
                'fields' => ['name']
            ],
            'services' => [
                'title' => 'Services Content',
                'fields' => ['services_list', 'pricing', 'service_areas']
            ]
        ],
        'priority' => 3
    ]
];
?>
