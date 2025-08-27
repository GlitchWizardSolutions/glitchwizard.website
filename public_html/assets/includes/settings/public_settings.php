<?php
/*
Page: public_settings.php
Location: public_html/assets/includes/settings/public_settings.php
Purpose: Centralized public-facing settings for business branding, content, and SEO. Edit this file to customize the entire public website for a new business/client.
*/
 
// Contact Section
$contact_email = 'barbara@glitchwizardsolutions.com';
$contact_phone = '+1 555-123-4567';
$contact_address = '123 Main Street';
$contact_city = 'Crawfordville,';
$contact_state = 'FL';
$contact_zipcode = '32327';

// Hero Section
$hero_content = [
    'headline' => 'Welcome to GWS Universal Hybrid Application',
    'subheadline' => 'A modern, flexible template for any business.',
    'bg_image' => ''
];

// Section Titles and Content
$services_title = 'Services';
$services_paragraph = 'Explore our creative and practical services designed to help your business thrive.';
$hero_button_text = 'Get Started';
$hero_button_link = 'about.php';
$contact_title = 'Contact';
$contact_paragraph = 'We look forward to serving you!';
$portfolio_title = 'Portfolio';
$portfolio_paragraph = 'Explore some of our recent projects and creative work. Each item showcases our commitment to quality and innovation.';
$pricing_title = 'Pricing';
$pricing_paragraph = 'Choose the plan that fits your needs. All plans are fully customizable.';
$team_title = 'Team';
$team_paragraph = 'Meet our talented team of professionals dedicated to your success.';
$testimonials_title = 'Testimonials';
$testimonials_paragraph = 'Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit';

// Contact Form Labels
$contact_form_labels = [
    'name' => 'Your Name',
    'email' => 'Your Email',
    'subject' => 'Subject',
    'message' => 'Message',
    'send_button' => 'Send Message',
    'loading' => 'Loading',
    'success' => 'Your message has been sent. Thank you!'
];

// Portfolio Filters
$portfolio_filters = ['All', 'Apps', 'Products', 'Websites'];

// About Alt Section (customizable content)
$about_alt_paragraph = 'Let us help you build a strong online presence and achieve your business objectives. Contact us today to get started!';
$about_alt_heading = 'Discover Our Unique Approach';
$about_alt_italic = 'We combine creativity, technology, and strategy to deliver outstanding results for your business.';
$about_alt_list = [
    'Custom solutions tailored to your brand and goals.',
    'Expert team with years of experience in web development and design.',
    'Ongoing support and collaboration to ensure your success.'
];


// About Section
$about_content = [
    'title' => 'About Us',
    'text' => 'We are a passionate team dedicated to building modern, accessible, and high-performing websites that help businesses of all sizes succeed online.',
    'side_text' => 'Our team brings together years of experience in web development, design, and digital strategy. We focus on accessibility, performance, and user experience to help your business thrive online.'
];

// Footer Links
$footer_links = [
    'accessibility_policy' => '',
    'terms_of_service' => '',
    'privacy_policy' => '',
    'faq' => '',
    'webmaster_email' => 'sidewaysy.onlineorders@gmail.com'
];

// Social Links
$social_links = [
    'facebook' => 'https://www.facebook.com/GlitchwizardSolutions',
    'twitter' => '#',
    'linkedin' => 'https://www.linkedin.com/in/glitchwizard',
    'instagram' => '#'
];

// FAQs
$faqs = [
    [
        'question' => 'What is a hybrid app?',
        'answer' => 'A hybrid app combines the best of web and native technologies.'
    ],
    [
        'question' => 'How do I get started?',
        'answer' => 'Contact us for a free consultation.'
    ],
    [
        'question' => 'What services do you offer?',
        'answer' => 'We offer a wide range of web development, design, and digital marketing services.'
    ],
    [
        'question' => 'How can I contact your support team?',
        'answer' => 'You can reach our support team via email, phone, or the contact form on our website.'
    ],
    [
        'question' => 'Do you provide custom website solutions?',
        'answer' => 'Yes, we specialize in custom website solutions tailored to your business needs.'
    ],
    [
        'question' => 'What is your typical project turnaround time?',
        'answer' => 'Project timelines vary, but most websites are completed within 2-6 weeks.'
    ],
    [
        'question' => 'Can you help with SEO and online marketing?',
        'answer' => 'Absolutely! We offer SEO optimization and digital marketing services.'
    ],
    [
        'question' => 'Do you offer website maintenance?',
        'answer' => 'Yes, we provide ongoing website maintenance and support packages.'
    ],
    [
        'question' => 'Is my website mobile-friendly?',
        'answer' => 'All our websites are designed to be fully responsive and mobile-friendly.'
    ],
    [
        'question' => 'Can I update my website content myself?',
        'answer' => 'We build sites with easy-to-use admin panels so you can update content anytime.'
    ],
    [
        'question' => 'Do you offer e-commerce solutions?',
        'answer' => 'Yes, we can build secure and scalable e-commerce platforms for your business.'
    ],
    [
        'question' => 'How do I get started with a new project?',
        'answer' => 'Contact us for a free consultation and we’ll guide you through the next steps.'
    ]
];

// Services
$services = [
    [
        'title' => 'Web Development',
        'desc' => 'Custom websites and web applications built for your business.',
        'url' => ''
    ],
    [
        'title' => 'UI/UX Design',
        'desc' => 'Modern, accessible, and beautiful user experiences.',
        'url' => ''
    ],
    [
        'title' => 'SEO & Marketing',
        'desc' => 'Grow your audience and reach with proven strategies.',
        'url' => ''
    ],
    [
        'title' => 'Hosting & Security',
        'desc' => 'Reliable hosting and robust security solutions for peace of mind.',
        'url' => ''
    ]
];

// Team Members
$team_members = [
    [
        'name' => 'Jane Smith',
        'role' => 'CEO',
        'social' => [
            'twitter' => '#',
            'facebook' => '#',
            'instagram' => '#',
            'linkedin' => '#'
        ]
    ],
    [
        'name' => 'John Doe',
        'role' => 'Lead Developer',
        'social' => [
            'twitter' => '#',
            'facebook' => '#',
            'instagram' => '#',
            'linkedin' => '#'
        ]
    ],
    [
        'name' => 'Emily Chen',
        'role' => 'Marketing Director',
        'social' => [
            'twitter' => '#',
            'facebook' => '#',
            'instagram' => '#',
            'linkedin' => '#'
        ]
    ],
    [
        'name' => 'Carlos Ruiz',
        'role' => 'Designer',
        'social' => [
            'twitter' => '#',
            'facebook' => '#',
            'instagram' => '#',
            'linkedin' => '#'
        ]
    ]
];

// Portfolio Items
$portfolio_items = [
    [
        'title' => 'Mobile App Design',
        'desc' => 'Modern, user-friendly mobile application for business productivity.'
    ],
    [
        'title' => 'Product Launch Campaign',
        'desc' => 'Comprehensive marketing campaign for a new product release.'
    ],
    [
        'title' => 'E-commerce Platform',
        'desc' => 'Full-featured online store with payment integration.'
    ],
    [
        'title' => 'Brand Identity Design',
        'desc' => 'Complete brand identity package including logo and guidelines.'
    ],
    [
        'title' => 'Web Development',
        'desc' => 'Custom website development with modern frameworks.'
    ],
    [
        'title' => 'Digital Marketing',
        'desc' => 'Strategic digital marketing campaigns for growth.'
    ]
];

// Testimonials
$testimonials = [
    [
        'name' => 'Jane Smith',
        'role' => 'CEO, ExampleCorp',
        'text' => 'This platform transformed our business. Highly recommended!'
    ],
    [
        'name' => 'John Doe',
        'role' => 'Lead Developer, DevWorks',
        'text' => 'The flexibility and customization options are unmatched.'
    ],
    [
        'name' => 'Emily Chen',
        'role' => 'Marketing Director, MarketPro',
        'text' => 'Our team loves the intuitive admin panel and robust features.'
    ],
    [
        'name' => 'Carlos Ruiz',
        'role' => 'Designer, Creativa',
        'text' => 'Beautiful design and easy to update content. Perfect for agencies.'
    ],
    [
        'name' => 'Priya Patel',
        'role' => 'Operations Manager, BizOps',
        'text' => 'Reliable, secure, and accessible. Our clients are impressed.'
    ],
    [
        'name' => 'Michael Lee',
        'role' => 'Consultant, Lee Consulting',
        'text' => 'Setup was a breeze and support is excellent. Five stars!'
    ]
];

// Stats
$stats = [
    [
        'label' => 'Projects',
        'value' => '120'
    ],
    [
        'label' => 'Clients',
        'value' => '85'
    ],
    [
        'label' => 'Awards',
        'value' => '15'
    ],
    [
        'label' => 'Team Members',
        'value' => '10'
    ]
];

// Pricing Plans
$pricing_plans = [
    [
        'name' => 'Basic',
        'price' => '$99',
        'button_text' => 'Buy Now',
        'button_link' => '#',
        'features' => [
            '1 Website',
            'Basic Support',
            'SSL Certificate',
        ],
        'feature_status' => [
            'check',
            'check',
            'check',
        ]
    ],
    [
        'name' => 'Pro',
        'price' => '$199',
        'button_text' => 'Buy Now',
        'button_link' => '#',
        'features' => [
            '3 Websites',
            'Priority Support',
            'SSL Certificate',
            'SEO Optimization',
        ],
        'feature_status' => [
            'check',
            'check',
            'check',
            'check',
        ]
    ],
    [
        'name' => 'Enterprise',
        'price' => 'Contact Us',
        'button_text' => 'Contact Sales',
        'button_link' => '#',
        'features' => [
            'Unlimited Websites',
            'Dedicated Support',
            'SSL Certificate',
            'SEO Optimization',
            'Custom Integrations',
        ],
        'feature_status' => [
            'check',
            'check',
            'check',
            'check',
            'check',
        ]
    ]
];

// Call To Action Section
$cta_heading = 'Ready to Get Started?';
$cta_text = 'Take the next step with our professional services and solutions.';
$cta_button_text = 'Contact Us';
$cta_button_link = 'contact.php';
 
// Footer Special Links
$footer_special_links = [
    'rss' => 'rss',
    'sitemap' => 'sitemap',
    'accessibility_policy' => 'policy-accessibility.php',
    'terms_of_service' => 'policy-terms.php',
    'privacy_policy' => 'policy-privacy.php',
    'faq' => 'faqs.php'
];

// Header Menu Links
$header_menu = [
    'Home' => 'index.php',
    'About' => 'about.php',
    'Services' => 'index.php#services',
    'Contact' => 'index.php#contact',
];

// Business Identity Settings
$business_name = 'GWS Universal Hybrid Application';
$business_name_short = 'GWS';
$business_name_medium = 'GWS Universal';
$business_name_long = 'GWS Universal Hybrid Application';

// Footer Branding Settings
$footer_business_name_type = 'medium'; // Options: 'short', 'medium', 'long'
$footer_logo_enabled = true;
$footer_logo_position = 'left'; // Options: 'left', 'top'
$footer_logo_file = 'admin_logo.svg'; // Options: 'main_logo.png', 'admin_logo.svg', 'favicon.ico', 'secondary_logo.png'

$author = 'GWS';