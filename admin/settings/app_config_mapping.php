<?php
/**
 * Application Configuration Mapping
 * Maps each application to its configuration file and settings structure
 */

return [
    'shop_system' => [
        'display_name' => 'Shop System',
        'description' => 'E-commerce and shopping cart configuration',
        'config_file' => '../shop_system/config.php',
        'backup_file' => '../shop_system/config.php.bak',
        'sections' => [
            'basic' => [
                'title' => 'Basic Settings',
                'icon' => 'bi bi-shop',
                'settings' => [
                    'site_name' => [
                        'type' => 'text',
                        'label' => 'Website Title',
                        'description' => 'The title of your website',
                        'default' => 'Shopping Cart'
                    ],
                    'currency_code' => [
                        'type' => 'select',
                        'label' => 'Currency Symbol',
                        'description' => 'Currency code for your store',
                        'options' => [
                            '&dollar;' => 'USD ($)',
                            '&euro;' => 'EUR (€)',
                            '&pound;' => 'GBP (£)',
                            '&yen;' => 'JPY (¥)'
                        ],
                        'default' => '&dollar;'
                    ],
                    'featured_image' => [
                        'type' => 'text',
                        'label' => 'Featured Image',
                        'description' => 'Default featured image path for homepage',
                        'default' => 'uploads/featured-image.jpg'
                    ],
                    'weight_unit' => [
                        'type' => 'select',
                        'label' => 'Weight Unit',
                        'description' => 'Unit for product weights',
                        'options' => [
                            'lbs' => 'Pounds (lbs)',
                            'kg' => 'Kilograms (kg)',
                            'oz' => 'Ounces (oz)',
                            'g' => 'Grams (g)'
                        ],
                        'default' => 'lbs'
                    ]
                ]
            ],
            'orders' => [
                'title' => 'Order Settings',
                'icon' => 'bi bi-bag',
                'settings' => [
                    'default_payment_status' => [
                        'type' => 'select',
                        'label' => 'Default Payment Status',
                        'description' => 'Default status for new orders',
                        'options' => [
                            'Pending' => 'Pending',
                            'Completed' => 'Completed',
                            'Processing' => 'Processing',
                            'Shipped' => 'Shipped'
                        ],
                        'default' => 'Completed'
                    ],
                    'account_required' => [
                        'type' => 'boolean',
                        'label' => 'Account Required for Checkout',
                        'description' => 'Require customers to create an account before checkout',
                        'default' => false
                    ]
                ]
            ],
            'technical' => [
                'title' => 'Technical Settings',
                'icon' => 'bi bi-gear',
                'settings' => [
                    'rewrite_url' => [
                        'type' => 'boolean',
                        'label' => 'URL Rewriting',
                        'description' => 'Enable URL rewrite feature (requires .htaccess configuration)',
                        'default' => false
                    ],
                    'template_editor' => [
                        'type' => 'select',
                        'label' => 'Template Editor',
                        'description' => 'Editor for product descriptions and email templates',
                        'options' => [
                            'tinymce' => 'TinyMCE',
                            'textarea' => 'Textarea'
                        ],
                        'default' => 'tinymce'
                    ],
                    'secret_key' => [
                        'type' => 'password',
                        'label' => 'Secret Key',
                        'description' => 'Used to generate unique reset codes for forgot password feature',
                        'default' => 'YOUR_SECRET_KEY'
                    ]
                ]
            ],
            'mail' => [
                'title' => 'Email Settings',
                'icon' => 'bi bi-envelope',
                'settings' => [
                    'mail_enabled' => [
                        'type' => 'boolean',
                        'label' => 'Enable Email',
                        'description' => 'Send emails to customers for new orders',
                        'default' => false
                    ],
                    'mail_from' => [
                        'type' => 'email',
                        'label' => 'From Email Address',
                        'description' => 'Email address to send from',
                        'default' => 'noreply@example.com'
                    ],
                    'mail_name' => [
                        'type' => 'text',
                        'label' => 'From Name',
                        'description' => 'Name of your website/business',
                        'default' => 'Your Website/Business Name'
                    ],
                    'notifications_enabled' => [
                        'type' => 'boolean',
                        'label' => 'Enable Notifications',
                        'description' => 'Receive email notifications for new payments',
                        'default' => true
                    ],
                    'notification_email' => [
                        'type' => 'email',
                        'label' => 'Notification Email',
                        'description' => 'Email address to send notifications to',
                        'default' => 'notifications@example.com'
                    ]
                ]
            ],
            'smtp' => [
                'title' => 'SMTP Settings',
                'icon' => 'bi bi-hdd-network',
                'settings' => [
                    'SMTP' => [
                        'type' => 'boolean',
                        'label' => 'Use SMTP Server',
                        'description' => 'Use SMTP server for sending emails',
                        'default' => false
                    ],
                    'smtp_secure' => [
                        'type' => 'select',
                        'label' => 'SMTP Security',
                        'description' => 'SMTP secure connection type',
                        'options' => [
                            'ssl' => 'SSL',
                            'tls' => 'TLS'
                        ],
                        'default' => 'ssl'
                    ],
                    'smtp_host' => [
                        'type' => 'text',
                        'label' => 'SMTP Host',
                        'description' => 'SMTP server hostname',
                        'default' => 'smtp.example.com'
                    ],
                    'smtp_port' => [
                        'type' => 'number',
                        'label' => 'SMTP Port',
                        'description' => 'SMTP server port number',
                        'default' => 465
                    ],
                    'smtp_user' => [
                        'type' => 'email',
                        'label' => 'SMTP Username',
                        'description' => 'SMTP username',
                        'default' => 'user@example.com'
                    ],
                    'smtp_pass' => [
                        'type' => 'password',
                        'label' => 'SMTP Password',
                        'description' => 'SMTP password',
                        'default' => 'secret'
                    ]
                ]
            ],
            'payments' => [
                'title' => 'Payment Methods',
                'icon' => 'bi bi-credit-card',
                'settings' => [
                    'pay_on_delivery_enabled' => [
                        'type' => 'boolean',
                        'label' => 'Pay on Delivery',
                        'description' => 'Accept pay on delivery payments',
                        'default' => true
                    ],
                    'paypal_enabled' => [
                        'type' => 'boolean',
                        'label' => 'PayPal Enabled',
                        'description' => 'Accept payments with PayPal',
                        'default' => true
                    ],
                    'stripe_enabled' => [
                        'type' => 'boolean',
                        'label' => 'Stripe Enabled',
                        'description' => 'Accept payments with Stripe',
                        'default' => true
                    ],
                    'coinbase_enabled' => [
                        'type' => 'boolean',
                        'label' => 'Coinbase Enabled',
                        'description' => 'Accept payments with Coinbase',
                        'default' => false
                    ]
                ]
            ]
        ]
    ],
    
    'blog_system' => [
        'display_name' => 'Blog System',
        'description' => 'Blog and content management configuration',
        'config_file' => '../blog_system/config.php',
        'backup_file' => '../blog_system/config.php.bak',
        'sections' => [
            'basic' => [
                'title' => 'Basic Settings',
                'icon' => 'bi bi-journal-text',
                'settings' => [
                    'blog_title' => [
                        'type' => 'text',
                        'label' => 'Blog Title',
                        'description' => 'The title of your blog',
                        'default' => 'My Blog'
                    ],
                    'posts_per_page' => [
                        'type' => 'number',
                        'label' => 'Posts Per Page',
                        'description' => 'Number of posts to display per page',
                        'default' => 10
                    ],
                    'comments_enabled' => [
                        'type' => 'boolean',
                        'label' => 'Enable Comments',
                        'description' => 'Allow comments on blog posts',
                        'default' => true
                    ]
                ]
            ]
        ]
    ],
    
    'accounts_system' => [
        'display_name' => 'User Accounts System',
        'description' => 'User registration and account management configuration',
        'config_file' => '../accounts_system/config.php',
        'backup_file' => '../accounts_system/config.php.bak',
        'sections' => [
            'registration' => [
                'title' => 'Registration Settings',
                'icon' => 'bi bi-person-plus',
                'settings' => [
                    'registration_enabled' => [
                        'type' => 'boolean',
                        'label' => 'Allow Registration',
                        'description' => 'Allow new users to register',
                        'default' => true
                    ],
                    'email_verification' => [
                        'type' => 'boolean',
                        'label' => 'Email Verification',
                        'description' => 'Require email verification for new accounts',
                        'default' => true
                    ],
                    'admin_approval' => [
                        'type' => 'boolean',
                        'label' => 'Admin Approval',
                        'description' => 'Require admin approval for new accounts',
                        'default' => false
                    ]
                ]
            ]
        ]
    ]
];
